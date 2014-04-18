<?php

function fix_source( $src ) {
	$src = 'http' == substr( $src, 0, 4 ) ? $src : 'http://www.artsmia.org' . $src;
	str_replace( '_h', '_e', $src );
	str_replace( 'h_images', 'e_images', $src );
	str_replace( 'h.jpg', 'e.jpg', $src );
	return $src;
}

function check_source( $src ) {
	if( ! empty( $src ) && 'http://zoom' != substr( $src, 0, 11 ) ) {
		return true;
	}
	return false;
}

// Main Query
$host = ARTSMIA_DB_HOST;
$dbname = ARTSMIA_TTA_NAME;
$user = ARTSMIA_TTA_USER;
$pass = ARTSMIA_TTA_PASS;
try{
	$dbh = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass) or die("Couldn't connect");
	$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch(PDOException $e) {
	echo "Connection failed: ".$e->getMessage();
	die;
}
$main_query=$dbh->prepare("SELECT * FROM newsletters WHERE volume='185'");
$main_query->execute();
$results = $main_query->fetch(PDO::FETCH_ASSOC);


// Root post
$mainpost = new ModularPost( 'Five Facts: ' . $results['title'], 'tta' );

// Define as root of this group of Five Facts pages
$mainpost->set_subsite_root();

// Facts carousel
$mainpost->add_carousel( 'children' );

// Title
$mainpost->add_static_content( '', '<h1>' . $results['title'] . '</h1>', 'full' );

// Main content
$mainpost->add_static_content( '', $results['introduction'] );

// Subpages
for( $i=1; $i<6; $i++ ) {

	$child = new ModularPost( '<span>FACT #' . $i . ':</span> ' . $results['page' . $i . '_title'], 'tta' );

	// Facts carousel
	$child->add_carousel( 'inherit' );

	// Page title
	$child->add_static_content( '', '<h1>FACT #' . $i . ': ' . $results['page' . $i . '_title'] . '</h1>', 'full' );

	// Main image
	$child_featured = $child->add_image( fix_source( $results['page' . $i . '_pic'] ), $results['page' . $i . '_cap'], null, 'left' );
	// Now that we've created the image module, save as featured image
	$child->set_featured_image( $child_featured );

	// Main content
	$child->add_static_content( '', $results['page' . $i . '_text'], 'right' );

	// Slideshow, IF any of the facts are usable URLs. 
	if( check_source( $results['pic' . $i . '_1'] ) || check_source( $results['pic' . $i . '_2'] ) || check_source( $results['pic' . $i . '_3'] ) ) {
		$child_slideshow = $child->add_slideshow();
		$child_slideshow->set_option( 'height', 450 );
		if( check_source( $results['pic' . $i . '_1'] ) ) {
			$child_slideshow->add_slide(
				str_replace( '_h', '_e', fix_source( $results['pic' . $i . '_1'] ) ),
				null,
				null,
				$results['cap' . $i . '_1']
			);
		}
		if( check_source( $results['pic' . $i . '_2'] ) ) {
			$child_slideshow->add_slide(
				str_replace( '_h', '_e', fix_source( $results['pic' . $i . '_2'] ) ),
				null,
				null,
				$results['cap' . $i . '_2']
			);
		}
		if( check_source( $results['pic' . $i . '_3'] ) ) {
			$child_slideshow->add_slide(
				str_replace( '_h', '_e', fix_source( $results['pic' . $i . '_3'] ) ),
				null,
				null,
				$results['cap' . $i . '_3']
			);
		}
	}

	// Append to root page.
	$mainpost->add_child( $child );

}

// Activities Query

$act_query=$dbh->prepare("SELECT * FROM related_activities WHERE volume='185'");
$act_query->execute();
$activities = $act_query->fetchAll(PDO::FETCH_ASSOC);
$act_content = '';
foreach( $activities as $activity ) {
	$act_content .= '<h6>' . $activity['title'] . '</h6>';
	$act_content .= '<p>' . $activity['description'] . '</p>';
}
$mainpost->add_static_content( 'Related Activities', $act_content, 'center' );

// Publish
$new_id = $mainpost->publish();

echo "<p>Your new post has been created. <a href='/wp-admin/post.php?post=" . $new_id . "&action=edit'>Edit</a> or <a href='" . get_permalink( $new_id ) . "'>view</a> it now.</p>";
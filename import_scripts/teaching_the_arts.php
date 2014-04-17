<?php

// Get data
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
$query=$dbh->prepare("SELECT * FROM newsletters WHERE volume='185'");
$query->execute();
$results = $query->fetch(PDO::FETCH_ASSOC);


// Main post
$mainpost = new ModularPost( $results['title'], 'tta' );
$mainpost->add_carousel( 'children' );
$mainpost->add_static_content( '', $results['introduction'] );

// Subpages
for( $i=1; $i<6; $i++ ) {

	$child = new ModularPost( $results['page' . $i . '_title'], 'tta' );

	$child_featured = $child->add_image( 'http://www.artsmia.org' . $results['page' . $i . '_pic'], $results['page' . $i . '_cap'], null, 'left' );

	$child->set_featured_image( $child_featured );

	$child->add_static_content( $results['page1_title'], $results['page' . $i . '_text'], 'right' );

	$child_slideshow = $child->add_slideshow();
	$child_slideshow->add_slide(
		str_replace( '_h', '_e', 'http://www.artsmia.org' . $results['pic' . $i . '_1'] ),
		null,
		null,
		$results['cap' . $i . '_1']
	);
	$child_slideshow->add_slide(
		str_replace( '_h', '_e', 'http://www.artsmia.org' . $results['pic' . $i . '_2'] ),
		null,
		null,
		$results['cap' . $i . '_2']
	);
	$child_slideshow->add_slide(
		str_replace( '_h', '_e', 'http://www.artsmia.org' . $results['pic' . $i . '_3'] ),
		null,
		null,
		$results['cap' . $i . '_3']
	);

	$child->add_carousel( 'inherit' );

	$mainpost->add_child( $child );

}



// Publish
$new_id = $mainpost->publish();


echo "<p>Your new post has been created. <a href='/wp-admin/post.php?post=" . $new_id . "&action=edit'>Edit</a> or <a href='" . get_permalink( $new_id ) . "'>view</a> it now.</p>";
<?php

set_time_limit( 300 );

/* -------------------------------------------------------------------------- *
 * UTILITIES
 * -------------------------------------------------------------------------- */

function fix_source( $src ) {

	// These URLs are broken - display fallback image as defined in module
	if( 'http://zoom' == substr( $src, 0, 11 ) ){
		return null;
	}

	$src = 'http' == substr( $src, 0, 4 ) ? $src : 'http://www.artsmia.org' . $src;

	str_replace( '_h', '_e', $src );
	str_replace( '-h', '-e', $src );

	return $src;
}


/* -------------------------------------------------------------------------- *
 * GET TAX IDS
 * -------------------------------------------------------------------------- */

$ff_term = term_exists( 'Five Facts', 'tta_format' );
$ff_term_id = $ff_term['term_id'];
$oif_term = term_exists( 'Object in Focus', 'tta_format' );
$oif_term_id = $oif_term['term_id'];


/* -------------------------------------------------------------------------- *
 * MAIN QUERY
 * -------------------------------------------------------------------------- */

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
$main_query=$dbh->prepare("SELECT * FROM newsletters WHERE publish='1'");
$main_query->execute();
$results = $main_query->fetchAll(PDO::FETCH_ASSOC);

foreach( $results as $row ){

	switch( $row['template'] ){


		/* ---------------------------------------------------------------------- *
		 * FIVE FACTS
		 * ---------------------------------------------------------------------- */

		case '5facts':

			$format = 'Five Facts';

			// Root post
			$mainpost = new ModularPost( array(
				'post_title' => 'Teaching the Arts: Five Facts: ' . utf8_encode( $row['title'] ),
				'post_name' => utf8_encode( $row['title'] ),
				'post_type' => 'tta',
				'post_excerpt' => utf8_encode( $row['introduction'] ),
				'menu_order' => $row['volume'],
			) );

			// Define as root of this group of Five Facts pages
			$mainpost->set_subsite_root();

			// Facts carousel
			$mainpost->add_carousel( 'children' );

			// Title
			$mainpost->add_static_content( '', '<h1>' . utf8_encode( $row['title'] ) . '</h1>', 'full' );

			// Main content
			$mainpost->add_static_content( '', utf8_encode( $row['introduction'] ) );

			// Subpages
			for( $i=1; $i<6; $i++ ) {

				$child = new ModularPost( array(
					'post_title' => '<span>FACT #' . $i . ':</span> ' . utf8_encode( $row['page' . $i . '_title'] ),
					'post_name' => utf8_encode( $row['page' . $i . '_title'] ),
					'post_type' => 'tta' 
				) );

				// Facts carousel
				$child->add_carousel( 'inherit' );

				// Page title
				$child->add_static_content( '', '<h1>FACT #' . $i . ': ' . utf8_encode( $row['page' . $i . '_title'] ) . '</h1>', 'full' );

				// Main image
				$child_featured = $child->add_image( fix_source( $row['page' . $i . '_pic'] ), utf8_encode( $row['page' . $i . '_cap'] ), null, 'left' );
				// Now that we've created the image module, save as featured image
				$child->set_featured_image( $child_featured );

				// Main content
				$child->add_static_content( '', utf8_encode( $row['page' . $i . '_text'] ), 'right' );

				// Slideshow, IF any of the facts are usable URLs. 
				if( $row['pic' . $i . '_1'] || $row['pic' . $i . '_2'] || $row['pic' . $i . '_3'] ) {
					$child_slideshow = $child->add_slideshow();
					$child_slideshow->set_option( 'height', 450 );
					if( $row['pic' . $i . '_1'] ) {
						$child_slideshow->add_slide(
							str_replace( '_h', '_e', fix_source( $row['pic' . $i . '_1'] ) ),
							null,
							null,
							utf8_encode( $row['cap' . $i . '_1'] )
						);
					}
					if( $row['pic' . $i . '_2'] ) {
						$child_slideshow->add_slide(
							str_replace( '_h', '_e', fix_source( $row['pic' . $i . '_2'] ) ),
							null,
							null,
							utf8_encode( $row['cap' . $i . '_2'] )
						);
					}
					if( $row['pic' . $i . '_3'] ) {
						$child_slideshow->add_slide(
							str_replace( '_h', '_e', fix_source( $row['pic' . $i . '_3'] ) ),
							null,
							null,
							utf8_encode( $row['cap' . $i . '_3'] )
						);
					}
				}

				// Append to root page.
				$mainpost->add_child( $child );

			}

			// Activities Query

			$act_query=$dbh->prepare("SELECT * FROM related_activities WHERE volume=?");
			$act_query->execute( array( $row['volume'] ) );
			$activities = $act_query->fetchAll( PDO::FETCH_ASSOC );
			$act_content = '';
			foreach( $activities as $activity ) {
				$act_content .= '<h6>' . utf8_encode( $activity['title'] ) . '</h6>';
				$act_content .= '<p>' . utf8_encode( $activity['description'] ) . '</p>';
			}

			$act_page = new ModularPost( array(
				'post_title' => 'Related Activities', 
				'post_type' => 'tta' 
			) );

			$act_page->add_carousel( 'inherit' );

			$act_page->add_static_content( 'Related Activities', $act_content, 'center' );

			$mainpost->add_child( $act_page );

			// Publish
			$new_id = $mainpost->publish();

			// Set terms
			wp_set_post_terms( $new_id, $ff_term_id, 'tta_format' );

			// Add additional metadata
			update_post_meta( $new_id, 'tta_vol_date', $row['voldate'] );
			update_post_meta( $new_id, 'tta_vol_id', $row['volume'] );

			echo "<p>Created a new " . $format . " post from Volume " . $row['volume'] . ". <a href='/wp-admin/post.php?post=" . $new_id . "&action=edit'>Edit</a> or <a href='" . get_permalink( $new_id ) . "'>view</a> it now.</p>";

			break;


		/* ---------------------------------------------------------------------- *
		 * OBJECT IN FOCUS
		 * ---------------------------------------------------------------------- */

		case 'objif':

			$format = 'Object in Focus';

			// Root post
			$mainpost = new ModularPost( array(
				'post_title' => 'Teaching the Arts: Object in Focus: ' . utf8_encode( $row['title'] ),
				'post_name' => utf8_encode( $row['title'] ),
				'post_type' => 'tta',
				'post_excerpt' => utf8_encode( $row['introduction'] ),
				'menu_order' => $row['volume'],
			) );

			// Define as root of this group of Five Facts pages
			$mainpost->set_subsite_root();

			// Facts carousel
			$mainpost->add_carousel( 'children' );

			// Main Image
			$mainpost->add_image( fix_source( $row['object_pic'] ), null, null, 'left' );

			// Main content
			$mainpost->add_static_content( utf8_encode( $row['title'] ), utf8_encode( $row['object_cap'] ), 'right' );

			// Subpages
			for( $i=1; $i<4; $i++ ) {

				$child = new ModularPost( array(
					'post_title' => '<span>KEY IDEA #' . $i . ':</span> ' . utf8_encode( $row['page' . $i . '_title'] ),
					'post_name' => utf8_encode( $row['page' . $i . '_title'] ),
					'post_type' => 'tta',
				) );

				// Facts carousel
				$child->add_carousel( 'inherit' );

				// Main image
				$child->add_image( fix_source( $row['object_pic'] ), null, null, 'left' );

				// Slideshow, IF any of the supporting images are usable URLs. 
				if( $row['pic' . $i . '_1'] || $row['pic' . $i . '_2'] || $row['pic' . $i . '_3'] ) {
					$child_slideshow = $child->add_slideshow( null, null, 'left' );
					$child_slideshow->set_option( 'height', 450 );
					if( $row['pic' . $i . '_1'] ) {
						$child_slideshow->add_slide(
							str_replace( '_h', '_e', fix_source( $row['pic' . $i . '_1'] ) ),
							null,
							null,
							utf8_encode( $row['cap' . $i . '_1'] )
						);
					}
					if( $row['pic' . $i . '_2'] ) {
						$child_slideshow->add_slide(
							str_replace( '_h', '_e', fix_source( $row['pic' . $i . '_2'] ) ),
							null,
							null,
							utf8_encode( $row['cap' . $i . '_2'] )
						);
					}
					if( $row['pic' . $i . '_3'] ) {
						$child_slideshow->add_slide(
							str_replace( '_h', '_e', fix_source( $row['pic' . $i . '_3'] ) ),
							null,
							null,
							utf8_encode( $row['cap' . $i . '_3'] )
						);
					}

					// Use first image in slideshow for featured image, so it's not the 
					// object in focus every time.
					$child->set_featured_image( $child_slideshow, 0 );
				}

				// Main content
				$child->add_static_content( 'KEY IDEA #' . $i . ': ' . utf8_encode( $row['page' . $i . '_title'] ), utf8_encode( $row['page' . $i . '_text'] ), 'right' );

				// Append to root page.
				$mainpost->add_child( $child );

			}

			// Activities Query

			$act_query=$dbh->prepare("SELECT * FROM related_activities WHERE volume=?");
			$act_query->execute( array( $row['volume'] ) );
			$activities = $act_query->fetchAll( PDO::FETCH_ASSOC );
			$act_content = '';
			foreach( $activities as $activity ) {
				$act_content .= '<h6>' . utf8_encode( $activity['title'] ) . '</h6>';
				$act_content .= '<p>' . utf8_encode( $activity['description'] ) . '</p>';
			}

			$act_page = new ModularPost( array(
				'post_title' => 'Related Activities',
				'post_type' => 'tta',
			) );

			$act_page->add_carousel( 'inherit' );

			$act_page->add_static_content( 'Related Activities', $act_content, 'center' );

			$mainpost->add_child( $act_page );

			// Publish
			$new_id = $mainpost->publish();

			// Set terms
			wp_set_post_terms( $new_id, $oif_term_id, 'tta_format' );

			// Add additional metadata
			update_post_meta( $new_id, 'tta_vol_date', $row['voldate'] );
			update_post_meta( $new_id, 'tta_vol_id', $row['volume'] );

			echo "<p>Created a new " . $format . " post from Volume " . $row['volume'] . ". <a href='/wp-admin/post.php?post=" . $new_id . "&action=edit'>Edit</a> or <a href='" . get_permalink( $new_id ) . "'>view</a> it now.</p>";

			break;	
	}
}

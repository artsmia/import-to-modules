<?php

// Instantiate the modular post object with a title

$post = new ModularPost( 'My awesome Modular Post' );

// Add a static content module

$post->add_static_content( 'Welcome to Art in Bloom 2014.', "<p>Enter a world of timeless art and fresh flowers during the 31st annual Art in Bloom. Delight in over 150 floral arrangements designed to interpret pieces of art from the MIA’s permanent collection. Art in Bloom offers four full days of free and ticketed events.</p>", 'left' );

// Modules can also be configured after instantiation ...

$content = $post->add_static_content();
$content->title = 'Supporters';
$content->content = "<p>Generous support provided by Art in Bloom 2014 Honorary Chairs Ben Jaffray and Nivin MacMillan, Gabberts Design Studio & Fine Furnishings, Lakewood Cemetery, Bachman's, Martha Head, Dorsey & Whitney LLP, Renata Winsor, Lucille Amis, RBC Wealth Management, The Todd L. and Barbara K. Bachman Family Fund of The Minneapolis Foundation, Caldrea, The Phillips Family, and Tom and Lynn Schaefer.</p>";
$content->position = 'right';

// Add a highlighted image; images will be downloaded and added to the WordPress media library on publish

$post->add_image( 'http://mia-wp-cdn.s3.amazonaws.com/wp-content/uploads/2014/01/AIB-Poster.jpg', 'The Art in Bloom 2014 poster', 'Art in Bloom 2014', 'center' );

// Slideshows have special helper methods for options and slides.

$slideshow = $post->add_slideshow();

$slideshow->set_option( 'title_card', 'custom' );
$slideshow->set_option( 'title_custom', 'Art in Bloom 2014' );
$slideshow->set_option( 'sizing', 'aspect' );
$slideshow->set_option( 'aspect_w', 16 );
$slideshow->set_option( 'aspect_h', 9 );
$slideshow->set_option( 'min_height', 300 );

$slideshow->add_slide(
	'http://mia-wp-cdn.s3.amazonaws.com/wp-content/uploads/2014/01/AIB_WebTreatment_Header_forweb.jpg',
	'The Art in Bloom 2014 poster',
	'Art in Bloom 2014',
	"<p>Art in Bloom is presented by Friends of the Institute, an organization that has provided benevolent support to the MIA for 92 years.</p>"
);
$slideshow->add_slide(
	'http://mia-wp-cdn.s3.amazonaws.com/wp-content/uploads/2014/01/mia_6009905-crop.jpg',
	'Saito Ippo, Japanese, screen (byobu), Flowers of the Four Seasons (detail), early 19th century, ink and colors on gold leaf, Gift of the Clark Center for Japanese Art & Culture',
	'Flowers of the Four Seasons (detail)',
	"<p>This screen contains some beautiful flowers.</p>"
);

// publish() returns the new post ID.

$new_id = $post->publish();

// Output is displayed to the user after the script runs.

echo "<p>Your new post has been created. <a href='/wp-admin/post.php?post=" . $new_id . "&action=edit'>Edit</a> or <a href='" . get_permalink( $new_id ) . "'>view</a> it now.</p>";
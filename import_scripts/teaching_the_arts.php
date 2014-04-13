<?php

	$post = new ModularPost( 'My slideshow post' );

	$slideshow = $post->add_slideshow();

	$slideshow->add_slide(
		'http://media-cache-ak0.pinimg.com/736x/71/c8/34/71c834d90d2072d267c190157ef59a6e.jpg',
		'Planet (caption)',
		'This is a planet (alt)',
		'Description of this slide.'
	);

	$slideshow->add_slide(
		'http://media-cache-ak0.pinimg.com/736x/c4/29/5c/c4295c42ae3b030b2fcf5056f0258e7d.jpg',
		'Waterfall (caption)',
		'This is a waterfall (alt)',
		'Description of the waterfall.'
	);

	$slideshow->position = 'full';

	$slideshow->set_option( 'sizing', 'aspect' );
	$slideshow->set_option( 'aspect_w', 16 );
	$slideshow->set_option( 'aspect_h', 9 );

	$new_id = $post->publish();

	echo "Your new post has been created. <a href='http://march.loc/wp-admin/post.php?post=" . $new_id . "&action=edit'>Edit</a> or <a href='" . get_permalink( $new_id ) . "'>view</a> it now.";
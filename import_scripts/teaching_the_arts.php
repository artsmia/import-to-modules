<?php

	$post = new ModularPost( 'My classy post' );
	$image = $post->add_image();
	$image->url = 'http://media-cache-ak0.pinimg.com/736x/3a/e9/b8/3ae9b8aa242753ae30dce8a775d5745e.jpg';
	$image->caption ='Image Caption';
	$image->alt = 'Image Alt';

	$new_id = $post->publish();

	echo "Your new post has been created. <a href='http://march.loc/wp-admin/post.php?post=" . $new_id . "&action=edit'>Edit</a> or <a href='" . get_permalink( $new_id ) . "'>view</a> it now.";
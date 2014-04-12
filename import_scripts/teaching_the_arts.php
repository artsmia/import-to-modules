<?php

	$post = new ModularPost( 'My image post' );
	$post->add_image( 
		'http://media-cache-ak0.pinimg.com/736x/3a/e9/b8/3ae9b8aa242753ae30dce8a775d5745e.jpg',
		'Image Caption',
		'Image Alt',
		'full'
	);

	$new_id = $post->publish();

	echo "Your new post has been created. <a href='http://march.loc/wp-admin/post.php?post=" . $new_id . "&action=edit'>Edit</a> or <a href='" . get_permalink( $new_id ) . "'>view</a> it now.";
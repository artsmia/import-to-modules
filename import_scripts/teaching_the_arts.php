<?php

	$post = new ModularPost( 'My new post' );
	$post->add_static_content( 'This is my static content', "<ul><li>Here</li><li>is an unordered list.</li></ul>", 'left' );
	$new_id = $post->publish();

	echo "Your new post has been created. <a href='http://march.loc/wp-admin/post.php?post=" . $new_id . "&action=edit'>Edit</a> or <a href='" . get_permalink( $new_id ) . "'>view</a> it now.";
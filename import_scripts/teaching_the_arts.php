<?php

	$post = new ModularPost( 'My parent post' );
	$post->add_static_content( 'I am a parent post', "<ul><li>Here</li><li>is an unordered list.</li></ul>", 'left' );

	$child_post = new ModularPost( 'My child post' );
	$child_post->add_static_content( 'I am a child post', "<p>Howdy</p>", 'left' );

	$post->add_child( $child_post );

	$new_id = $post->publish();

	echo "Your new post has been created. <a href='http://march.loc/wp-admin/post.php?post=" . $new_id . "&action=edit'>Edit</a> or <a href='" . get_permalink( $new_id ) . "'>view</a> it now.";
<?php

/**
 * ModularPost
 *
 * Represents a single imported post and its associated modules.
 */
class ModularPost { 

	/**
	 * The title of the post to be created.
	 */
	private $post_title;

	/**
	 * The post type of the post to be created.
	 */
	private $post_type;

	/**
	 * The template (if post_type = page) for the page to be created.
	 */
	private $post_template;

	/**
	 * Array holding other ModularPosts that are children of this one.
	 */
	private $children = array();

	/**
	 * Incrementor that keeps track of how many modules are on a page.
	 */
	private $mod_index = 0;

	/**
	 * Array of module layouts, serialized version becomes the value of the
	 * 'modules' postmeta key.
	 */
	private $module_list = array();

	/**
	 * Data corresponding to each module.
	 */
	private $module_data = array();

	/**
	 * Get mod index
	 *
	 * Return current index and increment
	 */
	function get_mod_index() {
		$index = $this->mod_index;
		$this->mod_index++;
		return $index;
	}

	/**
	 * add_static_content
	 *
	 * Add static content to a post.
	 */
	function add_static_content( $title, $content, $layout = 'center' ) { 

		$i = $this->get_mod_index();

		$this->module_list[] = 'static';

		$this->module_data[] = array(
			 'modules_' . $i . '_layout' => $layout,
			'_modules_' . $i . '_layout' => 'field_524b1fdf186a1',
			 'modules_' . $i . '_content' => 1,
			'_modules_' . $i . '_content' => 'field_524b1a80ffd07',
			 'modules_' . $i . '_content_0_title' => $title,
			'_modules_' . $i . '_content_0_title' => 'field_524b1e68d452c',
			 'modules_' . $i . '_content_0_text' => $content,
			'_modules_' . $i . '_content_0_text' => 'field_524b1e4bd452b',
		);

	}

	/**
	 * add_image
	 *
	 * Add a highlighted image to a post.
	 */
	function add_image( $url, $layout = 'center' ){

		$i = $this->get_mod_index();

		$tmp = download_url( $url );
		$file_array = array(
		  'name' => basename( $url ),
		  'tmp_name' => $tmp
		);
		
		// Check for download errors
		if ( is_wp_error( $tmp ) ) {
		  @unlink( $file_array[ 'tmp_name' ] );
		}
		
		$media_id = media_handle_sideload( $file_array, 0 );
		
		// Check for handle sideload errors.
		if ( is_wp_error( $media_id ) ) {
		  @unlink( $file_array['tmp_name'] );
		}

		$this->module_list[] = 'image';

		$this->module_data[] = array(
			 'modules_' . $i . '_layout' => $layout,
			'_modules_' . $i . '_layout' => 'field_524b1adeffd0b',
			 'modules_' . $i . '_content' => 1,
			'_modules_' . $i . '_content' => 'field_524b1abaffd0a',
			 'modules_' . $i . '_content_0_image' => $media_id,
			'_modules_' . $i . '_content_0_image' => 'field_524b39cf34532',
		);

	}

	/**
	 * add_carousel
	 *
	 * Add a nav carousel to a post. As carousels are always full width and the
	 * plugin assumes that the user intends to make a carousel of post children
	 * (rather than create a nav menu), there are no parameters.
	 */
	function add_carousel() {

		$i = $this->get_mod_index();

		$this->module_list[] = 'carousel';

		$this->module_data[] = array(
			 'modules_' . $i . '_layout' => 'full',
			'_modules_' . $i . '_layout' => 'field_52b8b3e1cfd98',
			 'modules_' . $i . '_content' => 1,
			'_modules_' . $i . '_content' => 'field_52b61e566d52d',
			 'modules_' . $i . '_content_0_source' => 'children',
			'_modules_' . $i . '_content_0_source' => 'field_534851493c28e',
		);
	}

	/**
	 * add_child
	 * 
	 * Registers (and stores) another modular post object as a child of this one, 
	 * so that all ModularPosts are published together when the parent is
	 * published.
	 */
	function add_child( $child ) {
		if( $child instanceof ModularPost ) {
			$this->children[] = &$child;
		}
	}

	/**
	 * publish
	 *
	 * Publishes new post and associated modules
	 */
	function publish() {

		global $wpdb;

		$postdata = array(
			'post_title' => $this->post_title,
			'post_type' => $this->post_type,
			'post_status' => 'publish',
		);

		$new_id = wp_insert_post( $postdata );

		if( ! $new_id ) {
			die( 'Failed to insert post.' );
		}

		if( 'page' == $this->post_type && $this->post_template ) {
			update_post_meta( $new_id, '_wp_page_template', $this->post_template );
		}

		update_post_meta( $new_id, 'modules', $this->module_list );
		update_post_meta( $new_id, '_modules', 'field_524b16d70ce72' );

		foreach( $this->module_data as $module ) {

			foreach( $module as $key => $value ) {

				update_post_meta( $new_id, $key, $value );

			}

		}

		return $new_id;

	}

	/**
	 * Construct
	 */
	function __construct( $post_title, $post_type = 'page', $post_template = 'treatment.php' ) {

		$this->post_title = $post_title;
		$this->post_type = $post_type;
		$this->post_template = $post_template;

	}

}
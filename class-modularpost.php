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
	 * The module containing the featured image for the page.
	 */
	private $featured_image;

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
	 * Get mod index
	 *
	 * Return current index and increment
	 */
	function get_next_mod_index() {
		$index = $this->mod_index;
		$this->mod_index++;
		return $index;
	}

	/**
	 * add_static_content
	 *
	 * Add static content to a post. Wraps the I2M_Module__static object.
	 *
	 * External images will be downloaded and stored in the WP media library, and
	 * will be attached to the newly created post.
	 *
	 * @param string $title The title of the static content block.
	 * @param string $content The HTML content of the static content block.
	 * @param string $position One of 'center', 'full', 'left', or 'right'
	 */
	function add_static_content( $title = null, $content = null, $position = null ) { 

		$module = new I2M_Module__static( $title, $content, $position );

		$this->module_list[] = $module->get_acf_layout();
		$this->modules[] = $module;

		return $module;

	}

	/**
	 * add_image
	 *
	 * Add a highlighted image to a post. Wraps the I2M_Module__image object.
	 * 
	 * @param string $url The remote location of the image file.
	 * @param string $caption The caption text, typically displayed beneath the image.
	 * @param string $alt Alt text for the image
	 * @param string $position One of 'center', 'full', 'left', or 'right'
	 */
	function add_image( $url = null, $caption = null, $alt = null, $position = null ) {

		$module = new I2M_Module__image( $url, $caption, $alt, $position );

		$this->module_list[] = $module->get_acf_layout();
		$this->modules[] = $module;

		return $module;

	}

	/**
	 * add_slideshow
	 *
	 * Add a slideshow to a post. Wraps the I2M_Module__slideshow object.
	 *
	 * @param array $slides An array of slide arrays
	 * @param array $options An array of options
	 * @param string $position One of 'center', 'full', 'left', or 'right'
	 */
	function add_slideshow( $slides = null, $options = null, $position = null ) {

		$module = new I2M_Module__slideshow( $slides, $options, $position );

		$this->module_list[] = $module->get_acf_layout();
		$this->modules[] = $module;

		return $module;

	}

	/**
	 * add_carousel
	 *
	 * Add a nav carousel to a post. Wraps the I2M_Module__carousel object.
	 *
	 * Note: position is always 'full'
	 *
	 * @param string $source Source of posts: 'children' or 'inherit'. Navs are not supported at this time.
	 */
	function add_carousel( $source = null ) {

		$module = new I2M_Module__carousel( $source );

		$this->module_list[] = $module->get_acf_layout( $source );
		$this->modules[] = $module;

		return $module;

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
			$this->children[] = $child;
		}
	}

	/**
	 * set_featured_image
	 *
	 * Sets the featured image for the post. 
	 *
	 * @param object $image_module The I2M_Module__image module containing the image
	 */
	function set_featured_image( $image_module = null ) {
		if( $image_module && $image_module instanceof I2M_Module__image ) {
			$this->featured_image = $image_module;
		}
	}
	/**
	 * publish
	 *
	 * Publishes new post and associated modules
	 *
	 * @param int $parent_id Wordpress ID of the post this should be nested under.
	 * @param string $post_status The status of the new post, i.e. 'draft' or 'private' or 'publish'
	 */
	function publish( $parent_id = null, $post_status = 'publish' ) {

		global $wpdb;

		$postdata = array(
			'post_title' => $this->post_title,
			'post_type' => $this->post_type,
			'post_status' => 'publish',
		);

		// Add parent ID if specified
		if( $parent_id ) {
			$postdata[ 'post_parent' ] = $parent_id;
		}

		// Create post
		$new_id = wp_insert_post( $postdata );
		if( ! $new_id ) {
			die( 'Failed to insert post.' );
		}

		// Add template if specified
		if( 'page' == $this->post_type && $this->post_template ) {
			update_post_meta( $new_id, '_wp_page_template', $this->post_template );
		}

		// ACF gives every modular page a 'modules' key that corresponds to a list
		// of included modules types (or in ACF terms, layouts) in the order they
		// appear on the page.
		update_post_meta( $new_id, 'modules', $this->module_list );
		update_post_meta( $new_id, '_modules', 'field_524b16d70ce72' );

		// Loop through the modules, adding postmeta in the same format ACF would.
		foreach( $this->modules as $index=>$module ) {

			// Do any necessary setup (i.e. download images)
			$module->before_publish( $new_id );

			// Fetch postmeta for module
			$postmeta_rows = $module->get_postmeta_rows( $index );

			// Update in database
			foreach( $postmeta_rows as $key => $value ) {
				update_post_meta( $new_id, $key, $value );
			}

		}

		if( ! empty( $this->featured_image ) ) {
			$thumbnail_id = $this->featured_image->get_media_id();
			update_post_meta( $new_id, '_thumbnail_id', $thumbnail_id );
		}

		// Loop through and publish children
		foreach( $this->children as $child ) {
			$child->publish( $new_id );
		}

		return $new_id;

	}

	/**
	 * Set up object
	 */
	function __construct( $post_title = null, $post_type = 'page', $post_template = 'treatment.php' ) {

		$this->post_title = $post_title;
		$this->post_type = $post_type;
		$this->post_template = $post_template;

	}

}
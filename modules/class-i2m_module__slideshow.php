<?php

/**
 * I2M_Module__slideshow
 *
 * Slideshow module
 */
class I2M_Module__slideshow extends I2M_Module {

	public $position;

	public $slides;

	private $options = array(

		'controls' => null, 

		'title_card' => 0, // 'page', 'graphic', 'custom'

		// If 'graphic'
		'title_graphic' => null,

		// If 'custom'
		'title_custom' => null,

		'sizing' => 'absolute', // 'aspect'
		'height' => 300,

		// If 'aspect'
		'aspect_w' => 0,
		'aspect_h' => 0,
		'min_height' => 0,
	);

	/**
	 * Add a slide to the slideshow.
	 */
	public function add_slide( $url = null, $caption = null, $alt = null, $description = null ) {
		$this->slides[] = array(
			'url' => $url,
			'caption' => $caption,
			'alt' => $alt,
			'description' => $description,
		);
	}

	/**
	 * Helper function for hiding arrows, since the option format is obtuse.
	 * (To be fair, it's obtuse so that we can easily add other control option
	 * checkboxes in the future.)
	 *
	 * NOTE: This is saving correctly, but it look slike the actual hide_arrows 
	 * functionality is broken in the front-end slideshow module.
	 */
	public function hide_arrows() {
		$this->options['controls'] = array( 'hide_arrows' );
	}

	/**
	 * Options setter so that options are set one value at a time without the
	 * entire array being overwritten
	 */
	public function set_option( $option, $value ) {
		$this->options[ $option ] = $value;
	}

	/**
	 * before_publish
	 *
	 * Called just before get_postmeta_rows. Here, used to sideload slide images
	 * and store media_id in slide.
	 *
	 * TODO: Import title_graphic as well!
	 *
	 * @param int $new_id The ID of the newly created modular post.
	 */
	public function before_publish( $new_id ) {

		foreach( $this->slides as $slide_index => $slide ) {

			$url = $slide['url'];
			$caption = $slide['caption'];
			$alt = $slide['alt'];

			// Download image
			$tmp = download_url( $url );
			$file_array = array(
			  'name' => basename( $url ),
			  'tmp_name' => $tmp
			);
			
			// Check for download errors
			if ( is_wp_error( $tmp ) ) {
			  @unlink( $file_array['tmp_name'] );
			}

			// Include caption if specified (WP stores this in the post_excerpt field)
			$post_data = array(
				'post_excerpt' => $caption,
			);
			
			// Create media post and attach to $new_id
			$media_id = media_handle_sideload( $file_array, $new_id, null, $post_data );
			
			// Check for handle sideload errors.
			if ( is_wp_error( $media_id ) ) {
			  @unlink( $file_array['tmp_name'] );
			} else {
				$this->slides[ $slide_index ]['media_id'] = $media_id;
			}

			// Add alt text if specified.
			if( $alt ) {
				update_post_meta( $media_id, '_wp_attachment_image_alt', $alt );
			}

		}

	}

	/**
	 * get_postmeta_rows
	 *
	 * Generates rows for the wp_postmeta table. Called on ModularPost publish.
	 *
	 * @param int $index The index of the module on the page, provided by ModularPost.
	 */
	public function get_postmeta_rows( $index ) {

		$rows = array();

		$rows[ 'modules_' . $index . '_layout' ] = $this->position;
		$rows[ '_modules_' . $index . '_layout' ] = 'field_524b1fdf186a1';
		$rows[ 'modules_' . $index . '_content' ] = 1;
		$rows[ '_modules_' . $index . '_content' ] = 'field_524b1f95186a0';

		// Options
		$rows[ 'modules_' . $index . '_content_0_controls' ] = $this->options['controls'];
		$rows[ '_modules_' . $index . '_content_0_controls' ] = 'field_52812267224fe';
		$rows[ 'modules_' . $index . '_content_0_title_card' ] = $this->options['title_card'];
		$rows[ '_modules_' . $index . '_content_0_title_card' ] = 'field_528153508b60e';
		$rows[ 'modules_' . $index . '_content_0_title_graphic' ] = $this->options['title_graphic'];
		$rows[ '_modules_' . $index . '_content_0_title_graphic' ] = 'field_528153768b60f';
		$rows[ 'modules_' . $index . '_content_0_title_custom' ] = $this->options['title_custom'];
		$rows[ '_modules_' . $index . '_content_0_title_custom' ] = 'field_528153978b610';
		$rows[ 'modules_' . $index . '_content_0_sizing' ] = $this->options['sizing'];
		$rows[ '_modules_' . $index . '_content_0_sizing' ] = 'field_52e294ced7ead';
		$rows[ 'modules_' . $index . '_content_0_height' ] = $this->options['height'];
		$rows[ '_modules_' . $index . '_content_0_height' ] = 'field_52e2950bd7eae';
		$rows[ 'modules_' . $index . '_content_0_aspect_w' ] = $this->options['aspect_w'];
		$rows[ '_modules_' . $index . '_content_0_aspect_w' ] = 'field_52e29540d7eaf';
		$rows[ 'modules_' . $index . '_content_0_aspect_h' ] = $this->options['aspect_h'];
		$rows[ '_modules_' . $index . '_content_0_aspect_h' ] = 'field_52e29583d7eb0';
		$rows[ 'modules_' . $index . '_content_0_min_height' ] = $this->options['min_height'];
		$rows[ '_modules_' . $index . '_content_0_min_height' ] = 'field_528153db8b612';
	
		// Slides
		$rows[ 'modules_' . $index . '_content_0_gallery' ] = count( $this->slides );
		$rows[ '_modules_' . $index . '_content_0_gallery' ] = 'field_524cac8f5a4a7';
		foreach( $this->slides as $slide_index => $slide ) {
			$rows[ 'modules_' . $index . '_content_0_gallery_' . $slide_index . '_image' ] = $slide[ 'media_id' ];
			$rows[ '_modules_' . $index . '_content_0_gallery_' . $slide_index . '_image' ] = 'field_524cac9b5a4a8';
			$rows[ 'modules_' . $index . '_content_0_gallery_' . $slide_index . '_description' ] = $slide[ 'description' ];
			$rows[ '_modules_' . $index . '_content_0_gallery_' . $slide_index . '_description' ] = 'field_524e2b49e6084';
		}

		return $rows;

	}

	/**
	 * Set up object.
	 */
	public function __construct( $slides = null, $options = null, $position = null ) {

		$this->acf_layout = 'slideshow';

		$this->position = $position ? $position : 'full';

		$this->slides = $slides ? $slides : array();

		if( ! empty( $options ) ) {
			$this->options = array_merge( $this->options, $options );
		}

	}

}
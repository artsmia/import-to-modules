<?php

/**
 * I2M_Module__image
 *
 * Image module
 */
class I2M_Module__image extends I2M_Module {

	public $position;

	public $url;
	public $caption;
	public $alt;

	private $media_id;

	private $fallback_id = 29824;

	/**
	 * Get media ID
	 */
	public function get_media_id() {
		return isset( $this->media_id ) ? $this->media_id : null;
	}

	/**
	 * before_publish
	 *
	 * Called just before get_postmeta_rows. Here, used to sideload
	 * image and append to correct post.
	 *
	 * @param int $new_id The ID of the newly created modular post.
	 */
	public function before_publish( $new_id ) {

		global $src_hash;

		if( ! $this->url ){
			$this->media_id = $this->fallback_id;
			return;
		}

		if( array_key_exists( $this->url, $src_hash ) ){
			$this->media_id = $src_hash[ $this->url ];
			return;
		}

		// Download image
		$tmp = download_url( $this->url );
		$file_array = array(
		  'name' => basename( $this->url ),
		  'tmp_name' => $tmp
		);
		
		// Check for download errors
		if ( is_wp_error( $tmp ) ) {
		  @unlink( $file_array[ 'tmp_name' ] );
		  $this->media_id = $this->fallback_id;
			$src_hash[ $this->url ] = $this->fallback_id;
			return;
		}

		// Include caption if specified (WP stores this in the post_excerpt field)
		$post_data = array(
			'post_excerpt' => $this->caption,
		);
		
		// Create media post and attach to $new_id
		$media_id = media_handle_sideload( $file_array, $new_id, null, $post_data );
		
		// Check for handle sideload errors.
		if ( is_wp_error( $media_id ) ) {
		  @unlink( $file_array['tmp_name'] );
		  $this->media_id = $this->fallback_id;
			$src_hash[ $this->url ] = $this->fallback_id;
			return;
		} else {
			$this->media_id = $media_id;
			$src_hash[ $this->url ] = $media_id;
		}

		// Add alt text if specified.
		if( $this->alt ) {
			update_post_meta( $media_id, '_wp_attachment_image_alt', $this->alt );
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
		return array(
			 'modules_' . $index . '_layout' => $this->position,
			'_modules_' . $index . '_layout' => 'field_524b1adeffd0b',
			 'modules_' . $index . '_content' => 1,
			'_modules_' . $index . '_content' => 'field_524b1abaffd0a',
			 'modules_' . $index . '_content_0_image' => $this->media_id,
			'_modules_' . $index . '_content_0_image' => 'field_524b39cf34532',
		);
	}

	/**
	 * Set up object.
	 */
	public function __construct( $url = null, $caption = null, $alt = null, $position = 'center' ) {

		$this->acf_layout = 'image';

		$this->position = $position;

		$this->url = $url;
		$this->caption = $caption;
		$this->alt = $alt;

	}
	
}
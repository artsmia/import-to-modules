<?php 
/** 
 * I2M_Module
 *
 * Template for a module object.
 */
abstract class I2M_Module {

	/**
	 * string acf_layout
	 *
	 * The ACF layout of the module.
	 */
	protected $acf_layout;

	/**
	 * get_acf_layout
	 *
	 * Returns ACF layout to ModularPost. Called on instantiation.
	 */
	public function get_acf_layout() {
		return $this->acf_layout;
	}

	/**
	 * before_publish
	 *
	 * Called just before get_postmeta_rows.
	 *
	 * @param int $new_id The ID of the newly created modular post.
	 * @param arr $src_hash A hash of URLs to media IDs, so we don't download
	 * anything more than once.
	 */
	public function before_publish( $new_id ) {}

}
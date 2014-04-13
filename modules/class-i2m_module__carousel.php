<?php

/**
 * I2M_Module__carousel
 *
 * Carousel module
 */
class I2M_Module__carousel extends I2M_Module {

	/**
	 * get_postmeta_rows
	 *
	 * Generates rows for the wp_postmeta table. Called on ModularPost publish.
	 *
	 * @param int $index The index of the module on the page, provided by ModularPost.
	 */
	public function get_postmeta_rows( $index ) {
		return array(
			 'modules_' . $index . '_layout' => 'full',
			'_modules_' . $index . '_layout' => 'field_52b8b3e1cfd98',
			 'modules_' . $index . '_content' => 1,
			'_modules_' . $index . '_content' => 'field_52b61e566d52d',
			 'modules_' . $index . '_content_0_source' => 'children',
			'_modules_' . $index . '_content_0_source' => 'field_534851493c28e',
		);
	}

	/**
	 * Set up the object.
	 */
	public function __construct() {
		$this->acf_layout = 'carousel';
	}

}
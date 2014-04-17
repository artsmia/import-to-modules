<?php

/**
 * I2M_Module__carousel
 *
 * Carousel module
 */
class I2M_Module__carousel extends I2M_Module {

	private $source;

	/**
	 * Update the source of the carousel.
	 */
	public function set_source( $source ) {

		if( ! in_array( $source, array( 'children', 'inherit' ) ) ) {
			echo "Invalid carousel source!";
			return;
		}

		$this->source = $source;
		
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
			 'modules_' . $index . '_layout' => 'full',
			'_modules_' . $index . '_layout' => 'field_52b8b3e1cfd98',
			 'modules_' . $index . '_content' => 1,
			'_modules_' . $index . '_content' => 'field_52b61e566d52d',
			 'modules_' . $index . '_content_0_source' => $this->source,
			'_modules_' . $index . '_content_0_source' => 'field_534851493c28e',
		);
	}

	/**
	 * Set up the object.
	 */
	public function __construct( $source = null ) {

		$this->acf_layout = 'carousel';

		$this->source = $source ? $source : 'children';

	}

}
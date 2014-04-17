<?php

/**
 * I2M_Module__static
 *
 * Static Content module
 */
class I2M_Module__static extends I2M_Module {
	
	public $position;

	public $title;
	public $content;

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
			'_modules_' . $index . '_layout' => 'field_524b1fdf186a1',
			 'modules_' . $index . '_content' => 1,
			'_modules_' . $index . '_content' => 'field_524b1a80ffd07',
			 'modules_' . $index . '_content_0_title' => $this->title,
			'_modules_' . $index . '_content_0_title' => 'field_524b1e68d452c',
			 'modules_' . $index . '_content_0_text' => $this->content,
			'_modules_' . $index . '_content_0_text' => 'field_524b1e4bd452b',
		);
	}

	/**
	 * Set up object.
	 */
	public function __construct( $title = null, $content = null, $position = null ) {

		$this->acf_layout = 'static';

		$this->position = $position ? $position : 'center';

		$this->title = $title;
		$this->content = $content;

	}

}
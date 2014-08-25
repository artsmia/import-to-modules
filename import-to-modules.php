<?php
/**
 * Plugin Name: Import to Modules
 * Description: Provides an API of sorts for importing external data into the website as modules.
 * Version: 0.0.1
 * Author: Minneapolis Institute of Arts
 * Author URI: http://new.artsmia.org
 */

// Main plugin class
include( 'class-importtomodules.php' );

// Modular post creation API
include( 'class-modularpost.php' );

// Individual modules
foreach( glob( plugin_dir_path( __FILE__ ) . "modules/*.php" ) as $path ) {
	include_once( $path );
}

new ImportToModules();
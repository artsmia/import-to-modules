<?php
/**
 * Plugin Name: Import to Modules
 * Description: Provides an API of sorts for importing external data into the website as modules.
 * Version: 0.0.1
 * Author: Minneapolis Institute of Arts
 * Author URI: http://new.artsmia.org
 */

include( 'class-importtomodules.php' );
include( 'class-modularpost.php' );

new ImportToModules();
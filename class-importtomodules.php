<?php

/**
 * ImportToModules
 *
 * The main class for the plugin.
 */
class ImportToModules {

	/**
	 * Register plugin page under tools menu
	 */
	function register_plugin_page() {

    add_submenu_page( 
    	'tools.php', 
    	'Import to Modules', 
    	'Import to Modules', 
    	'administrator', 
    	'import_to_modules', 
    	array( $this, 'render_plugin_page' )
    );

	}

	/**
	 * Render plugin page
	 */
	function render_plugin_page() {
	?>

	<div class="wrap">

	<h2>Import to Modules</h2>

		<p>Choose an import script to run. New scripts can be added directly to the /import_scripts/ plugin subfolder.</p>

		<select id='i2m_script' style='max-width:'>
			<?php foreach( glob( plugin_dir_path( __FILE__ ) . "import_scripts/*.php" ) as $path ) {
				echo "<option value='" . basename( $path ) . "'>" . basename( $path ) . "</option>";
			} ?>
		</select>

		<input type='button' class='button button-primary' id='i2m_submit' value='Go!' />

		<p style='background-color:#fff; color:#555; padding:25px; box-sizing:border-box; display:none;' id='ajax-response'></p>

		<script type='text/javascript'>
		jQuery( document ).ready( function() {
			jQuery( '#i2m_submit' ).on( 'click', function() {
				jQuery.post(
					ajaxurl,
					{ 
						action: 'import_to_modules',
						import_script: jQuery( '#i2m_script option:selected' ).val(),
						_wpnonce: '<?php echo wp_create_nonce( 'import_to_modules' ); ?>'
					},
					function( response ) {
						jQuery( '#ajax-response' ).html( response ).fadeIn( 300 );
					}
				);
			});
		});
		</script>

  </div>

  <?php
	}

	/**
	 * Include and run selected import script
	 */
	function handle_import_to_modules() {

		check_admin_referer( 'import_to_modules' );

		$import_script = $_POST[ 'import_script' ];
		include_once( plugin_dir_path( __FILE__ ) . 'import_scripts/' . $import_script );
		
		die;

	}

	/**
	 * Set up plugin
	 */
	function __construct() {
		add_action( 'admin_menu', array( $this, 'register_plugin_page' ) );
		add_action( 'wp_ajax_import_to_modules', array( $this, 'handle_import_to_modules' ) );
	}
}
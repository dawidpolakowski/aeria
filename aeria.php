<?php
/**
 * Plugin Name: Aeria
 * Description: Modular, Fast, Wordpress Framework for advanced app development.
 * Author: Caffeina
 * Author URI: http://caffeina.com
 * Plugin URI: https://github.com/CaffeinaLab/aeria
 * Version: 1.8.84
 */

// Exit if accessed directly
if( false === defined('ABSPATH') ) exit;

// The Framework version
define('AERIA','1.8.84');

// Store whether or not we're in the admin
if( false === defined('IS_ADMIN') ) define( 'IS_ADMIN',  is_admin() );

define('IS_NOT_ADMIN',  !IS_ADMIN);

// Define paths
define('AERIA_DIR',             rtrim(plugin_dir_path( __FILE__ ),'/').'/');
define('AERIA_URL',             rtrim(plugins_url( 'aeria' ),'/').'/');
define('AERIA_HOME_URL',        home_url('/'));
define('AERIA_RESOURCE_DIR',    AERIA_DIR.'resources/');
define('AERIA_PLUGINS_URL',     AERIA_DIR.'plugins/');
define('AERIA_RESOURCE_URL',    AERIA_URL.'resources/');
define('THEME_DIR',             get_stylesheet_directory().'/');

// Polyfills
include(AERIA_DIR.'lib/legacy.php');

// Autoupdate
include __DIR__.'/lib/plugin-update-checker.php';
PucFactory::buildUpdateChecker(
    'https://raw.githubusercontent.com/CaffeinaLab/aeria/master/metadata.json',
    __FILE__,
    'aeria'
);

// Register autoloader
spl_autoload_register(function($class){
	return is_file($class_file = AERIA_DIR.'classes/' . $class . '.php')?include($class_file):false;
});

// Tools: Icon
function icon($name){
	return strpos($name,'http')===0 ? $name : AERIA_RESOURCE_URL.'icons/'.$name.'.png';
}

// Create ralations table on register
register_activation_hook( __FILE__, function(){
	global $wpdb;
	$table_name = $wpdb->prefix.'aeria_relations';
	$wpdb->query("CREATE TABLE IF NOT EXISTS $table_name (
		`id_1` int(11) unsigned NOT NULL,
		`id_2` int(11) unsigned NOT NULL,
		`type` VARCHAR(50) NOT NULL,
		PRIMARY KEY (id_1,id_2,type)
	) DEFAULT CHARSET=utf8;");

});

// Enqueue Admin Scripts
if(IS_ADMIN){

	// Register filters
	add_filter( 'posts_where', 'AeriaUtils::title_like_posts_where', 10, 2 );

	add_action( 'admin_init', function(){
		// Branding
		add_filter( 'admin_footer_text', function($text){
			$aeria_logo = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAKCAYAAAC9vt6cAAACEUlEQVQoFV1SzWsTQRR/MztJuul2E7PZkrVtWmmJCqEWKljRQ0WEIgiCKLReAt68tJcevBUpgsGDXvwDFEEU2kLwIB56VWqFqkHToDY1bWzSfLGzzSbZD3dWU8SBN8z8vt6DGVSsUh/8Xb1HhOb0k49zalCZUdtmD8bowzkZ3108L2c6mn/1DEMMqNKG5/iATKeXcsltvn9e1OswFukCv8jD5rdfdQk1pzReELoblD68MvTWtm1SqmncYQDrvLC0EU+V+I2w6MeJs7KtCAg1gbNpG6GVtb16UTMDzGDqjcdvEiOznQDMujOi5gkIqtbCl6IEQgKBArVthvfxtnV5NCReHwsBq5OycPvag9dTrCnzEjb6XGpr4l1m7766WwE4E4YKNYBNEOuxWAaSOAybNXADYTiAsmnfwuLy2mfHu4Wcw9DyFyPLlBdOBLnBQQU1dR0uDosgdHvANNwQRrtrp1i3VrImzqa/rj+aiU+StOq7uru/T26MR1qOmTBzn88E07SAau2Oz72zS5fXg+PBA6N1VBq/83x9khzkcnVH6VARKFdUQ+FNiEoB0tCbf0Y+jABoGaaLjch+nC9UIF8taziZmHgpaTur39OfvFSlnEMQy7JcIXY+QqdYjpdwiBU769Xys1f3bq6izM+S8GM7f/rpi9QtRoA/FB6NDdiK0uu+jov9txUKxfapWDR5LNr//jfP/ulncDnamgAAAABJRU5ErkJggg==';
			return $text . ' - Powered by <img src="'.$aeria_logo.'" title="Aeria"> Aeria ' . AERIA . ' by <a href="http://caffeina.com" target="_blank">Caffeina</a>.';
		}, 11 );
	});

	add_filter( 'plugin_action_links', function($actions, $plugin_file, $plugin_data, $context ) {
		if ( isset($actions['edit']) ) unset( $actions['edit'] );
		return $actions;
	}, 10, 4 );

	add_filter('upload_mimes',function($existing_mimes=[]){
		$existing_mimes['svg'] = 'image/svg+xml';
		return $existing_mimes;
	});

	// Tools: Admin edit URL for post ID
	function admin_edit_url_for_id($id){
		return admin_url('post.php')."?post={$id}&action=edit";
	}

	// Add Script Select
	AeriaMetabox::add_script_select();

	// Run Ajax Relations
	add_action( 'wp_ajax_aeria_search', 		 'AeriaUtils::search' );
	add_action( 'wp_ajax_aeria_search_init', 'AeriaUtils::search_init' );

	// Run Aeria-dependent plugins
	do_action( 'aeria_init' );

} // IS_ADMIN



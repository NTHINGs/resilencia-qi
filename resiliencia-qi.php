<?php
/**
 * Plugin Name:       Resiliencia QI
 * Plugin URI:        https://github.com/NTHINGs/resiliencia-qi
 * Description:       Plugin hecho a la medida para manejar el cuestionario de resiliencia del Instituto QI.
 * Version:           1.0.0
 * Author:            Mauricio Martinez, Fernando Alvarez
 * Author URI:        https://github.com/NTHINGs
 * License:           MIT
 * License URI:       https://github.com/NTHINGs/resiliencia-qi/blob/master/LICENSE
 * Text Domain:       resiliencia-qi
 *
 * @link              https://github.com/NTHINGs/resiliencia-qi
 * @package           resiliencia-qi
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define global constants.
 *
 * @since 1.0.0
 */
// Plugin version.
if ( ! defined( 'ABS_VERSION_RESILiENCIA' ) ) {
	define( 'ABS_VERSION_RESILiENCIA', '1.0.0' );
}
if ( ! defined( 'RES_PLUGIN_PATH' ) ) {
	define( 'RES_PLUGIN_PATH',  plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'RES_PLUGIN_URL' ) ) {
	define( 'RES_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
/**
 * Link.
 *
 * @since 1.0.0
 */
if ( file_exists( RES_PLUGIN_PATH . 'shortcodes/formulario-registro-empresa.php' ) ) {
	require_once( RES_PLUGIN_PATH . 'shortcodes/formulario-registro-empresa.php' );
}
if ( file_exists( RES_PLUGIN_PATH . 'shortcodes/cuestionario-resiliencia.php' ) ) {
	require_once( RES_PLUGIN_PATH . 'shortcodes/cuestionario-resiliencia.php' );
}
if ( file_exists( RES_PLUGIN_PATH . 'shortcodes/resultados-cuestionario.php' ) ) {
	require_once( RES_PLUGIN_PATH . 'shortcodes/resultados-cuestionario.php' );
}

add_action('wp_enqueue_scripts','resiliencia_qi_init');

function resiliencia_qi_init() {
	wp_deregister_script('jquery');
	wp_enqueue_script('jquery', '//ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js', array(), null, true);
	wp_register_script( 'popper', '//cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js', array( 'jquery' ), '3.3.1', false );
	wp_enqueue_script( 'bootstrap', '//stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js', array( 'jquery', 'popper' ), '3.3.1', false );
	wp_enqueue_script( 'gijgo', '//cdn.jsdelivr.net/npm/gijgo@1.9.10/js/gijgo.min.js', array( 'jquery' ), '3.3.1', false );
	wp_enqueue_style( 'gijgo', '//cdn.jsdelivr.net/npm/gijgo@1.9.10/css/gijgo.min.css');
	wp_enqueue_script('chart', '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.min.js', array(), null, true);
}

// Crear Tablas en MySql
function resiliencia_qi_create_plugin_database() {
    global $table_prefix, $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$sql = str_replace(array("%TABLE_PREFIX%", "%CHARSET_COLLATE%"), array($table_prefix . "resiliencia_", $charset_collate), file_get_contents( RES_PLUGIN_PATH . "/schema.sql" ));
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta($sql);

	add_role( 'empresa', 'Empresa', array( 'read' => true, 'resiliencia'=> true ) );
}
register_activation_hook( __FILE__, 'resiliencia_qi_create_plugin_database' );


// Creando Página en dashboard
add_action( 'admin_menu', 'resiliencia_qi_admin' );

function resiliencia_qi_admin() {
    add_menu_page(
        'Cuestionario Resiliencia',     // page title
        'Cuestionario Resiliencia',     // menu title
        'resiliencia',   // capability
        'cuestionario-resiliencia',     // menu slug
		'render_resiliencia_qi_admin', // callback function
		'dashicons-universal-access'
    );
}
function render_resiliencia_qi_admin() {
	global $title;
	$current_user = wp_get_current_user();
	$link = '/cuestionario-resiliencia/?org_id=' . get_user_meta($current_user->ID, 'hash', true);
	
	$variables = array(
		"%TITLE%",
		"%LINK%",
	);
	$values = array(
		$title,
		$link
	);
	print str_replace($variables, $values, file_get_contents(  RES_PLUGIN_PATH . "templates/admin.html" ));
}

<?php
/**
 * Plugin Name:       Resiliencia QI
 * Plugin URI:        https://github.com/NTHINGs/resiliencia-qi
 * Description:       Plugin hecho a la medida para manejar el cuestionario de resilencia del Instituto QI.
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
if ( ! defined( 'ABS_VERSION' ) ) {
	define( 'ABS_VERSION', '1.0.0' );
}

if ( ! defined( 'ABS_NAME' ) ) {
	define( 'ABS_NAME', trim( dirname( plugin_basename( __FILE__ ) ), '/' ) );
}

if ( ! defined( 'ABS_DIR' ) ) {
	define( 'ABS_DIR', WP_PLUGIN_DIR . '/' . ABS_NAME );
}

if ( ! defined( 'ABS_URL' ) ) {
	define( 'ABS_URL', WP_PLUGIN_URL . '/' . ABS_NAME );
}

/**
 * Link.
 *
 * @since 1.0.0
 */
// if ( file_exists( ABS_DIR . '/shortcodes/formulario-registro-empresa.php' ) ) {
	require_once( ABS_DIR . '/shortcodes/formulario-registro-empresa.php' );
// }

add_action('wp_enqueue_scripts','resiliencia_qi_init');

function resiliencia_qi_init() {
	wp_deregister_script('jquery');
	wp_enqueue_script('jquery', '//ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js', array(), null, true);
	wp_register_script( 'popper', '//cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js', array( 'jquery' ), '3.3.1', false );
	wp_enqueue_script( 'bootstrap', '//stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js', array( 'jquery', 'popper' ), '3.3.1', false );
	wp_enqueue_script( 'gijgo', '//cdn.jsdelivr.net/npm/gijgo@1.9.10/js/gijgo.min.js', array( 'jquery' ), '3.3.1', false );
	wp_enqueue_style( 'gijgo', '//cdn.jsdelivr.net/npm/gijgo@1.9.10/css/gijgo.min.css');
    wp_register_script( 'jspdf', plugins_url( '/js/jspdf.min.js', __FILE__ ));
	wp_register_script( 'resiliencia_qi', plugins_url( '/js/resiliencia_qi.js', __FILE__ ));
}

// Crear Tablas en MySql
function resilencia_qi_create_plugin_database() {
    global $table_prefix, $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$sql = str_replace(array("%TABLE_PREFIX%", "%CHARSET_COLLATE%"), array($table_prefix . "resiliencia_", $charset_collate), file_get_contents( plugin_dir_path(__FILE__) . "/schema.sql" ));
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	// dbDelta($sql);

	add_role( 'empresa', 'Empresa', array( 'read' => true ) );
}
register_activation_hook( __FILE__, 'resilencia_qi_create_plugin_database' );


// Creando Página en dashboard
add_action( 'admin_menu', 'resilencia_qi_admin' );

function resilencia_qi_admin() {
    add_menu_page(
        'Cuestionario Resiliencia',     // page title
        'Cuestionario Resiliencia',     // menu title
        'manage_options',   // capability
        'cuestionario-resiliencia',     // menu slug
		'render_resilencia_qi_admin', // callback function
		'dashicons-universal-access'
    );
}
function render_resilencia_qi_admin() {
    global $title;

    print '<div class="wrap">';
	print "<h1>$title</h1>";
	print do_shortcode('[formulario-registro-empresa]');

    $file = plugin_dir_path( __FILE__ ) . "templates/admin.html";

    if ( file_exists( $file ) )
        require $file;

    print "<p class='description'>Included from <code>$file</code></p>";

    print '</div>';
}

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
// 	require_once( ABS_DIR . '/shortcodes/formulario-registro-empresa.php' );
// }

add_shortcode( 'formulario-registro-empresa', 'formulario_registro_empresa_shortcode' );
/**
 * formulario-registro-empresa shortcode.
 *
 * @return string
 * @since  1.0.0
 */
function formulario_registro_empresa_shortcode() {
	ob_start();
	guardar_empresa();
	render_html();
	return ob_get_clean();
}

function render_html() {
	// $current_user = wp_get_current_user();
	$variables = array(
		"%REQUEST_URI%",
	);
	$values = array(
		esc_url( $_SERVER['REQUEST_URI'] ),
	);
	echo str_replace($variables, $values, file_get_contents( plugin_dir_path( __DIR__ ) . "/templates/formulario-registro-empresa.html" ));
}

function guardar_empresa() {
	if ( !function_exists( 'wp_handle_upload' ) ){
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
	}

	if ( isset( $_POST['submitted'] ) ) {
		$username = $_POST['username'];
		$password = $_POST['password'];
		$display_name = $_POST['display_name'];
		$email = $_POST['email'];
		$current_time = current_time( 'mysql' );
		$hash = hash('sha256', current_time( 'mysql' ));
		$userdata = array(
			'user_login'         =>   $username,
			'user_pass'          =>   $password,
			'display_name'       =>   $display_name,
			'user_email'         =>   $email,
			'user_registered'    =>   $current_time,
			'user_url'           =>   $hash,
		);
		$user = wp_insert_user( $userdata );
		echo 'Tu organización '. $username .' ha sido registrada correctamente <a href="' . get_site_url() . '/wp-login.php">Click aquí para iniciar sesión</a>.';
	}
}

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

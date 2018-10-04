<?php
/**
 * Mostrar menu en wp-admin
 *
 *
 * @package	 resiliencia-qi
 * @since    1.0.0
 */
if ( file_exists( RES_PLUGIN_PATH . 'util/util.php' ) ) {
	require_once( RES_PLUGIN_PATH . 'util/util.php' );
}
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
	global $title, $wpdb;
	
	if (current_user_can('resiliencia') && !current_user_can('resiliencia_admin')) {
        // Render pagina de organizacion
        $current_user = wp_get_current_user();
        $hash = get_user_meta($current_user->ID, 'hash', true);
        $variables = array(
            "%TITLE%",
            "%SITE_URL%",
            "%HASH%",
        );
        $values = array(
            $title,
            get_site_url(),
            $hash,
        );
        $r = $wpdb->prefix . 'resiliencia_registros';
        $cuestionarios = $wpdb->get_results("SELECT id FROM $r WHERE organizacion = $hash");
        print print_r($cuestionarios);
        $resultados = get_resultados($cuestionario_id)
		print str_replace($variables, $values, file_get_contents(  RES_PLUGIN_PATH . "templates/resultados-organizacion.html" ));
	} elseif (current_user_can('resiliencia_admin')) {
        // Render pagina de todas las organizaciones
        $variables = array(
            "%TITLE%",
        );
        $values = array(
            $title,
        );
		print str_replace($variables, $values, file_get_contents(  RES_PLUGIN_PATH . "templates/resultados-generales.html" ));
	}
	
}
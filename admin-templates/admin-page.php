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
if(!class_exists('Resultados_Resiliencia_Table')){
    require_once( RES_PLUGIN_PATH . 'util/Resultados_Resiliencia_Table.php' );
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
    global $title;
	
	if (current_user_can('resiliencia') && !current_user_can('resiliencia_admin')) {
        // Render pagina de organizacion
        $wp_list_table = new Resultados_Resiliencia_Table();
        $wp_list_table->prepare_items();
        $variables = array(
            "%TITLE%",
            "%SITE_URL%",
            "%HASH%",
            "%TABLE%",
        );
        $values = array(
            $title,
            get_site_url(),
            get_user_hash(),
            $wp_list_table->display(),
        );
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
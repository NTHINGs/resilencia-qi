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
add_filter( 'set-screen-option', 'set_screen', 10, 3 );
add_action( 'admin_menu', 'resiliencia_qi_admin' );

function resiliencia_qi_admin() {
    $hook = add_menu_page(
        'Cuestionario Resiliencia',     // page title
        'Cuestionario Resiliencia',     // menu title
        'resiliencia',   // capability
        'cuestionario-resiliencia',     // menu slug
		'render_resiliencia_qi_admin', // callback function
		'dashicons-universal-access'
    );
    add_action( "load-$hook", 'screen_option' );
}
function render_resiliencia_qi_admin() {
    global $title;
	
	if (current_user_can('resiliencia') && !current_user_can('resiliencia_admin')) {
        // Render pagina de organizacion
        print 'ESTOY AQUI';
        $wp_list_table = new Resultados_Resiliencia_Table(get_user_hash());
        $wp_list_table->prepare_items();
        $variables = array(
            "%TITLE%",
            "%SITE_URL%",
            "%HASH%",
        );
        $values = array(
            $title,
            get_site_url(),
            get_user_hash(),
        );
        print str_replace($variables, $values, file_get_contents(  RES_PLUGIN_PATH . "templates/resultados-organizacion.html" ));
        render_table_resultados($wp_list_table);
        
	} elseif (current_user_can('resiliencia_admin')) {
        // Render pagina de todas las organizaciones
        $wp_list_table = new Resultados_Resiliencia_Table(NULL);
        $wp_list_table->prepare_items();
        $variables = array(
            "%TITLE%",
        );
        $values = array(
            $title,
        );
        print str_replace($variables, $values, file_get_contents(  RES_PLUGIN_PATH . "templates/resultados-generales.html" ));
        render_table_resultados($wp_list_table);
	}
	
}

function render_table_resultados($instance) {
    print '<div id="poststuff">';
    print '<div id="post-body" class="metabox-holder columns-2">';
    print '<div id="post-body-content">';
    print '<div class="meta-box-sortables ui-sortable">';
    print '<form method="post">';
    $instance->display();
    print '</form>';
    print '</div>';
    print '</div>';
    print '</div>';
    print '<br class="clear">';
    print '</div>';
    print '</div>';
}

function set_screen( $status, $option, $value ) {
	return $value;
}

function screen_option() {

	$option = 'per_page';
	$args   = [
		'label'   => 'Resultados',
		'default' => 10,
		'option'  => 'resultados_per_page'
	];

	add_screen_option( $option, $args );
}
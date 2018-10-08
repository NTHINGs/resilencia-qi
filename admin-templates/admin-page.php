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

    add_submenu_page(
        null,
        'Resultados Individuales', //page title
        'Resultados Individuales', //menu title
        'resiliencia', //capability,
        'resultados-individuales',//menu slug
        'render_resiliencia_resultados_individuales' //callback function
    );
}
function render_resiliencia_qi_admin() {
    global $title;
	
	if (current_user_can('resiliencia') && !current_user_can('resiliencia_admin')) {
        // Render pagina de organizacion
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
        // print str_replace($variables, $values, file_get_contents(  RES_PLUGIN_PATH . "templates/resultados-organizacion.html" ));
        print do_shortcode('[resultados-cuestionario org_id="' . get_user_hash() . '"]');
        // print '</div></div>';
        render_table_resultados(false);
        
	} elseif (current_user_can('resiliencia_admin')) {
        // Render pagina de todas las organizaciones
        $variables = array(
            "%TITLE%",
        );
        $values = array(
            $title,
        );
        print str_replace($variables, $values, file_get_contents(  RES_PLUGIN_PATH . "templates/resultados-generales.html" ));
        render_table_resultados(true);
	}
	
}

function render_table_resultados($admin) {
    print '<div id="poststuff">';

    print '<form method="post">';
    $wp_list_table = new Resultados_Resiliencia_Table();
    if( isset($_POST['s']) ){
        $wp_list_table->prepare_items($_POST['s'], $admin);
    } else {
        $wp_list_table->prepare_items(null, $admin);
    }
    $wp_list_table->search_box( 'Buscar', 'search_id' ); 
    $wp_list_table->display();
    print '</form>';
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

function render_resiliencia_resultados_individuales() {
    if(isset($_GET['registro'])){
        echo '<a href="' . admin_url('admin.php?page=cuestionario-resiliencia') . '"><- Volver a la lista </a>';
        echo do_shortcode('[resultados-cuestionario cuestionario_id="' . $_GET['registro'] . '"]');
    } else {
        echo 'ERROR NO SE ESPECIFICO EL CUESTIONARIO PARA VER RESULTADOS';
    }
}
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
if(!class_exists('Orgs_Resiliencia_Table')){
    require_once( RES_PLUGIN_PATH . 'util/Orgs_Resiliencia_Table.php' );
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

    $hook2 = add_submenu_page(
        null,
        'Resultados De Tu Organización', //page title
        'Resultados De Tu Organización', //menu title
        'resiliencia', //capability,
        'resultados-organizacionales',//menu slug
        'render_resiliencia_admin_org' //callback function
    );
    add_action( "load-$hook2", 'screen_option' );

    $hook3 = add_submenu_page(
        null,
        'Resultados Por Área', //page title
        'Resultados Por Área', //menu title
        'resiliencia', //capability,
        'resultados-organizacionales-area',//menu slug
        'render_resiliencia_admin_org_areas' //callback function
    );
    add_action( "load-$hook3", 'screen_option' );
}
function render_resiliencia_qi_admin() {
    global $title;
	
	if (current_user_can('resiliencia') && !current_user_can('resiliencia_admin')) {
        // Render pagina de organizacion
        render_resiliencia_admin_org(get_user_hash());
	} elseif (current_user_can('resiliencia_admin')) {
        // Render pagina de todas las organizaciones
        $variables = array(
            "%TITLE%",
        );
        $values = array(
            $title,
        );
        ob_start();
        print str_replace($variables, $values, file_get_contents(  RES_PLUGIN_PATH . "templates/resultados-generales.html" ));
        render_table_orgs();
        ob_end_flush();
	}
	
}

function render_resiliencia_admin_org($org_id=NULL) {
    if( isset($_GET['org_id']) ){
        $org_id = $_GET['org_id'];
    }
    if($org_id != NULL) {
        $variables = array(
            "%TITLE%",
            "%SITE_URL%",
            "%HASH%",
        );

        $args = array(
            'search'         => '**',
            'search_columns' => array( 'display_name' )
        );
        $query = new WP_User_Query( $args );
        $usuario = null;
        foreach ( $query->get_results() as $user ) {
            $hash = get_user_meta($user->ID, 'hash', true);
            if ($hash == $org_id) {
                $usuario = $user->display_name;
            }
        }
        $values = array(
            $usuario,
            get_site_url(),
            $org_id,
        );
        $area = NULL;
        if( isset($_POST['area']) && $_POST['area'] != 'todas' ){
            $area = $_POST['area'];
        }
        ob_start();
        print str_replace($variables, $values, file_get_contents(  RES_PLUGIN_PATH . "templates/resultados-organizacion.html" ));
        print $area;
        print do_shortcode('[resultados-cuestionario org_id="' . $org_id . '" area="' . $area . '"]');
        print '</div>';
        print '<h2>Resultados</h2>';
        render_table_resultados($org_id, $area);
        ob_end_flush();
    } else {
        print 'ERROR ORG_ID NO ESTA DEFINIDO';
    }
    
}

function render_table_resultados($org_id, $area = NULL) {
    global $wpdb;
    $table_areas = $wpdb->prefix . "areasorgareas";
    $areas = $wpdb->get_results(
        "SELECT * FROM $table_areas WHERE organizacion = '{$org_id}'",
        'ARRAY_A'
    );
    ob_start();
    ?>
    <div id="poststuff">
        <form method="post">
            <label for="area">Filtrar por área</label>                                                                                                                                                                                                                                                     
            <select id="area" name="area">
                <option value="todas">
                    Todas
                </option>
                <?php
                    foreach( $areas as $index => $row ) {
                        ?>
                        <option value="<?php print $row['nombre']; ?>">
                            <?php print $row['nombre']; ?>
                        </option>
                        <?php
                    }
                ?>
            </select>
            <?php
                submit_button('Filtrar');
            ?>
            <button type="button" class="button button-primary" onclick="imprimir()">Imprimir</button>
            <?php
                $wp_list_table = NULL;
                if( isset($_POST['s']) ){
                    $wp_list_table = new Resultados_Resiliencia_Table($_POST['s'], $org_id, $area);
                } else {
                    $wp_list_table = new Resultados_Resiliencia_Table(null, $org_id, $area);
                }
                $wp_list_table->prepare_items();
                $wp_list_table->search_box( 'Buscar', 'search_id' ); 
                $wp_list_table->display();
            ?>
        </form>
    </div>
    <script>
        var area = '<?php print $area; ?>' + '';
        if (area === '') {
            area = 'todas';
        }
        document.getElementById("area").value = area;

        function getDataUri(url) {
            if (url) {
                return new Promise((resolve, reject) => {
                    let extension = url.split('.').pop();
                    let type = 'png';
                    switch (extension) {
                        case 'jpg':
                        case 'jpeg':
                            type = 'jpeg';
                            break;
                    }
                    let image = new Image();

                    image.onload = function () {
                        let canvas = document.createElement('canvas');
                        canvas.width = this.naturalWidth; // or 'width' if you want a special/scaled size
                        canvas.height = this.naturalHeight; // or 'height' if you want a special/scaled size
                        canvas.getContext('2d').drawImage(this, 0, 0);
                        resolve(canvas.toDataURL('image/' + type));
                    };

                    image.src = url;
                });
            }
        }

        function imprimir() {
            const data = new FormData();
            data.append('action', 'resiliencia_imprimir_reporte_areas_general');
            data.append('org_id', '<?php print $org_id; ?>');
            data.append('search', '<?php print $_POST["s"]; ?>');
            data.append('area', '<?php print $area; ?>');
            const Http = new XMLHttpRequest();
            const url="<?php print admin_url( 'admin-ajax.php' ); ?>";
            Http.open("POST", url);
            Http.send(data);
            Http.onreadystatechange = function (e) {
                if (this.readyState == 4 && this.status == 200) {
                    var results = null;
                    try {
                        results = JSON.parse(Http.responseText);
                        (async () => {
                            let doc = new jsPDF({
                                orientation: 'landscape',
                                pageFormat: 'a4'
                            });
                            doc.autoTableSetDefaults({
                                headStyles: {
                                    fillColor: [80, 18, 70],
                                    textColor: 255
                                }
                            });

                            doc.addImage(await getDataUri('/wp-content/plugins/resiliencia-qi/logo.png'), 10, 10, 30, 20);
                            doc.setFontSize(24);
                            doc.text('Resultados Resiliencia', 45, 25);
                            doc.autoTable({
                                startY: 40,
                                columns: [
                                    {header: 'ID', dataKey: 'id'},
                                    {header: 'Nombre', dataKey: 'nombre'},
                                    {header: 'Autoestima', dataKey: 'autoestima'},
                                    {header: 'Empatía', dataKey: 'empatia'},
                                    {header: 'Autonomía', dataKey: 'autonomia'},
                                    {header: 'Humor', dataKey: 'humor'},
                                    {header: 'Creatividad', dataKey: 'creatividad'},
                                    {header: 'Total', dataKey: 'total'},
                                ],
                                body: results,
                                didParseCell: function(data) {
                                    if(data.row.section === 'body' && data.column.dataKey !== 'id' && data.column.dataKey !== 'nombre' && data.column.dataKey !== 'total') {
                                        const obj = data.cell.raw;
                                        data.cell.text = obj.text;
                                        data.cell.styles.textColor = obj.color;
                                    }
                                }
                            });
                            doc.save('resultados_resiliencia.pdf');
                        })();
                    } catch (e) {
                        console.error(e);
                    }
                }
            };
        }
    </script>
    <?php
    ob_end_flush();
}

function render_table_orgs() {
    print '<div id="poststuff">';

    print '<form method="post">';
    $wp_list_table = NULL;
    if( isset($_POST['s']) ){
        $wp_list_table = new Orgs_Resiliencia_Table($_POST['s']);
    } else {
        $wp_list_table = new Orgs_Resiliencia_Table();
    }
    $wp_list_table->prepare_items();
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
        echo '<a href="' . add_query_arg( 'org_id', $_GET['org_id'], admin_url('admin.php?page=resultados-organizacionales')) . '"><- Volver a la lista </a>';
        echo do_shortcode('[resultados-cuestionario cuestionario_id="' . $_GET['registro'] . '"]');
    } else {
        echo 'ERROR NO SE ESPECIFICO EL CUESTIONARIO PARA VER RESULTADOS';
    }
}

function render_resiliencia_admin_org_areas() {

}
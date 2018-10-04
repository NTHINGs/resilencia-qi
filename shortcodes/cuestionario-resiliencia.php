<?php
/**
 * Mostrar formulario para inventario de resiliencia
 *
 * [cuestionario-resiliencia]
 *
 * @package	 resiliencia-qi
 * @since    1.0.0
 */
if ( ! function_exists( 'cuestionario_resiliencia_shortcode' ) ) {
	// Add the action.
	add_action( 'plugins_loaded', function() {
		// Add the shortcode.
		add_shortcode( 'cuestionario-resiliencia', 'cuestionario_resiliencia_shortcode' );
	});

	/**
	 * cuestionario-resiliencia shortcode.
	 *
	 * @return string
	 * @since  1.0.0
	 */
	function cuestionario_resiliencia_shortcode() {
        ob_start();
        guardar_cuestionario();
        if ( !isset( $_POST['submitted'] ) ) {
            render_html_form_cuestionario();
        }
        return ob_get_clean();
    }
    
    function render_html_form_cuestionario() {

        if (!isset($_GET['org_id'])) {
            echo 'LINK DE ORGANIZACION INCORRECTO';
        } else {
            global $wpdb;
            $table_name = $wpdb->prefix . "resiliencia_preguntas";
            $preguntas = json_encode($wpdb->get_results(
            "SELECT * FROM $table_name"
            ));
            $variables = array(
                "%REQUEST_URI%",
                "%PREGUNTAS%",
                "%ORG_ID%",
                "%ORG_NAME%"
            );
            echo $_GET['org_id'];
            $organizacion = get_users(
                array(
                    'role' => 'empresa',
                    'hash' => $_GET['org_id'],
                )
            )[0];
            echo $organizacion;
            $values = array(
                esc_url( $_SERVER['REQUEST_URI'] ),
                $preguntas,
                $_GET['org_id'],
                $organizacion
            );
            echo str_replace($variables, $values, file_get_contents( plugin_dir_path( __DIR__ ) . "/templates/cuestionario-resiliencia.html" ));
        }
    }

    function guardar_cuestionario() {
        if ( isset( $_POST['submitted'] ) ) {
            global $wpdb;

            $table_name = $wpdb->prefix . "resiliencia_registros";
            $values = array(
                'nombre'             => $_POST['nombre'],
                'fechadenacimiento'  => date('Y-m-d', strtotime($_POST['fechadenacimiento'])),
                'edad'               => $_POST['edad'],
                'fechaaplicacion'    => current_time( 'mysql' ),
                'organizacion'       => $_POST['organizacion'],
            );
            $wpdb->insert( $table_name, $values, array(
                '%s',
                '%s',
                '%d',
                '%s',
                '%s',
            ));

            $cuestionario_id = $wpdb->insert_id;

            $resultados_table_name = $wpdb->prefix . "resiliencia_resultados";
            foreach($_POST as $key => $value) {
                if (strpos($key, 'pregunta_')>=0) {
                    $pregunta = array_pop(explode('pregunta_', $key));
                    $respuesta = array(
                        'respuesta'    => $value,
                        'pregunta'     => $pregunta,
                        'cuestionario' => $cuestionario_id,
                    );

                    $wpdb->insert( $resultados_table_name, $respuesta, array(
                        '%s',
                        '%d',
                        '%d',
                    ));
                }
            }
            echo 'Formulario enviado correctamente';
        }
    }
}

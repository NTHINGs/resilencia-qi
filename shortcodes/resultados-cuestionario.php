<?php
/**
 * Mostrar resultados del cuestionario
 *
 * [resultados-cuestionario]
 *
 * @package	 resiliencia-qi
 * @since    1.0.0
 */
if ( ! function_exists( 'resultados_cuestionario_shortcode' ) ) {
	// Add the action.
	add_action( 'plugins_loaded', function() {
		// Add the shortcode.
		add_shortcode( 'resultados-cuestionario', 'resultados_cuestionario_shortcode' );
	});

	/**
	 * resultados-cuestionario shortcode.
	 *
	 * @return string
	 * @since  1.0.0
	 */
	function resultados_cuestionario_shortcode($atts) {
        ob_start();
        render_html_resultados_cuestionario($atts);
        return ob_get_clean();
    }
    
    function render_html_resultados_cuestionario($atts) {
        $_atts = shortcode_atts( array(
			'cuestionario_id'  => NULL,
        ), $atts );
        $cuestionario_id = $_atts['cuestionario_id'];
        if($cuestionario_id == NULL) {
            echo 'ERROR MOSTRANDO LOS RESULTADOS';
        } else {
            $variables = array(
                '%DATA%',
            );

            $resultados = get_resultados($cuestionario_id);
            $values = array(
                $resultados,
            );
            echo $resultados;
            
            echo str_replace($variables, $values, file_get_contents( plugin_dir_path( __DIR__ ) . "/templates/resultados-cuestionario.html" ));
        }
    }

    function get_resultados($cuestionario_id) {
        global $wpdb;
        $resultados = array();
        $rs = $wpdb->prefix . 'resiliencia_resultados RS';
        $p = $wpdb->prefix . 'resiliencia_preguntas P';
        $r = $wpdb->prefix . 'resiliencia_registros R';

        $sql = "SELECT COUNT(RS.respuesta)
            FROM %RS%, %P%, %R%
            WHERE RS.pregunta = P.id 
            AND RS.cuestionario = R.id 
            AND R.id = '%ID%'
            AND P.tipo = '%TIPO%'
            AND P.grupo = '%GRUPO%'
            AND RS.respuesta = '%RESPUESTA%'";
        
        $variables = array(
            '%RS%',
            '%P%',
            '%R%',
            '%ID%',
            '%TIPO%',
            '%GRUPO%',
            '%RESPUESTA%',
        );
        
        $obj = array(
            'Autoestima' => array(
                'P' => 'S',
                'N' => 'N',
            ),
            'Empatía' => array(
                'P' => 'S',
                'N' => 'N',
            ),
            'Autonomía' => array(
                'P' => 'S',
                'N' => 'N',
            ),
            'Humor' => array(
                'P' => 'S',
                'N' => 'N',
            ),
            'Creatividad' => array(
                'P' => 'S',
                'N' => 'N',
            ),
        );

        foreach($obj as $grupo => $array_tipo_res) {
            foreach($array_tipo_res as $tipo => $respuesta) {
                $values = array(
                    $rs,
                    $p,
                    $r,
                    $cuestionario_id,
                    $tipo,
                    $grupo,
                    $respuesta,
                );
                echo 'IM IN THE FOR';
                echo str_replace($variables, $values, $sql);
                $resultado = $wpdb->get_results(str_replace($variables, $values, $sql))[0];
                echo $resultado;
                array_push($resultados, $resultado);
            }
        }

        return json_encode($resultados);
    }
}

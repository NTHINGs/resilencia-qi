<?php
/**
 * Mostrar resultados del cuestionario
 *
 * [resultados-cuestionario]
 *
 * @package	 resiliencia-qi
 * @since    1.0.0
 */
if ( file_exists( RES_PLUGIN_PATH . 'util/util.php' ) ) {
	require_once( RES_PLUGIN_PATH . 'util/util.php' );
}
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
            'org_id' => NULL,
        ), $atts );
        $cuestionario_id = $_atts['cuestionario_id'];
        $org_id = $_atts['org_id'];
        if ($cuestionario_id != NULL) {
            // Resultados individuales
            echo resultados_por_cuestionario_resiliencia($cuestionario_id);
        } elseif ($org_id != NULL) {
            echo resultados_por_organizacion_resiliencia($org_id);
        } else {
            echo 'ERROR: El shortcode tiene parametros incorrectos.';
        }
    }
    
    function resultados_por_cuestionario_resiliencia($cuestionario_id) {
        $variables = array(
            '%DATA%',
            '%RESULTADOS%',
            '%HEIGHT%',
        );
        $data = array();
        $resultados = get_resultados($cuestionario_id);
        array_push($data, calcular_rango('autoestima', (int)$resultados[0]));
        array_push($data, calcular_rango('empatia', (int)$resultados[1]));
        array_push($data, calcular_rango('autonomia', (int)$resultados[2]));
        array_push($data, calcular_rango('humor', (int)$resultados[3]));
        array_push($data, calcular_rango('creatividad', (int)$resultados[4]));
        array_push($data, calcular_total($resultados));
        $values = array(
            json_encode($resultados),
            json_encode($data),
            600,
        );
        
        return str_replace($variables, $values, file_get_contents( plugin_dir_path( __DIR__ ) . "/templates/resultados-cuestionario-individuales.html" ));
    }


    function resultados_por_organizacion_resiliencia($org_id) {
        $variables = array(
            '%DATA%',
            '%RESULTADOS%',
            '%HEIGHT%',
        );
        $data = array();
        $resultados = get_resultados_por_org($org_id);
        array_push($data, calcular_rango('autoestima', (int)$resultados[0]));
        array_push($data, calcular_rango('empatia', (int)$resultados[1]));
        array_push($data, calcular_rango('autonomia', (int)$resultados[2]));
        array_push($data, calcular_rango('humor', (int)$resultados[3]));
        array_push($data, calcular_rango('creatividad', (int)$resultados[4]));
        array_push($data, calcular_total($resultados));
        $values = array(
            json_encode($resultados),
            json_encode($data),
            40,
        );
        
        return str_replace($variables, $values, file_get_contents( plugin_dir_path( __DIR__ ) . "/templates/resultados-cuestionario-individuales.html" ));
    }

}

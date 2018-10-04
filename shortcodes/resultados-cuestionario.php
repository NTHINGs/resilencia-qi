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
        if($_atts['cuestionario_id'] == NULL) {
            echo 'ERROR MOSTRANDO LOS RESULTADOS';
        } else {
            $variables = array();
            $values = array();
            echo str_replace($variables, $values, file_get_contents( plugin_dir_path( __DIR__ ) . "/templates/resultados-cuestionario.html" ));
        }
    }
}

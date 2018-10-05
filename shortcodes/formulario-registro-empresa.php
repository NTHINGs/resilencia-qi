<?php
/**
 * Mostrar formulario para registrar empresa
 *
 * [formulario-registro-empresa]
 *
 * @package	 resiliencia-qi
 * @since    1.0.0
 */
if ( ! function_exists( 'formulario_registro_empresa_shortcode' ) ) {
	// Add the action.
	add_action( 'plugins_loaded', function() {
		// Add the shortcode.
		add_shortcode( 'formulario-registro-empresa', 'formulario_registro_empresa_shortcode' );
	});

	/**
	 * formulario-registro-empresa shortcode.
	 *
	 * @return string
	 * @since  1.0.0
	 */
	function formulario_registro_empresa_shortcode() {
        ob_start();
        guardar_empresa();
        if ( !isset( $_POST['submitted'] ) ) {
            render_html_form();
        }
        return ob_get_clean();
    }
    
    function render_html_form() {
        $variables = array(
            "%REQUEST_URI%",
        );
        $values = array(
            esc_url( $_SERVER['REQUEST_URI'] ),
        );
        echo str_replace($variables, $values, file_get_contents( plugin_dir_path( __DIR__ ) . "/templates/formulario-registro-empresa.html" ));
    }

    function guardar_empresa() {
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
                'role'               =>   'empresa',
            );
            $user_id = wp_insert_user( $userdata );
            echo $user_id;
            if ( ! is_wp_error( $user_id ) ) {
                $user_id = add_user_meta( $user_id, 'hash', $hash, true );
                if ( $user_id !== False ) {
                    echo 'Tu organización '. $username .' ha sido registrada correctamente <a href="' . get_site_url() . '/wp-login.php">Click aquí para iniciar sesión</a>.';
                } else {
                    echo 'Ocurrió un error al dar de alta tu organización';
                }
            } else {
                echo 'Ocurrió un error al dar de alta tu organización';
                echo $user_id;
            }
            
        }
    }
}

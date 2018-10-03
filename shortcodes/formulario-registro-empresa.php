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
        render_html();
        return ob_get_clean();
    }
    
    function render_html() {
        // $current_user = wp_get_current_user();
        $variables = array(
            "%REQUEST_URI%",
        );
        $values = array(
            esc_url( $_SERVER['REQUEST_URI'] ),
        );
        echo str_replace($variables, $values, file_get_contents( plugin_dir_path( __DIR__ ) . "/templates/formulario-registro-empresa.html" ));
    }

    function guardar_empresa() {
        if ( !function_exists( 'wp_handle_upload' ) ){
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }
    
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
                'user_url'           =>   $hash,
            );
            $user = wp_insert_user( $userdata );
            echo 'Tu organización '. $username .' ha sido registrada correctamente <a href="' . get_site_url() . '/wp-login.php">Click aquí para iniciar sesión</a>.';
        }
    }
}

if ( shortcode_exists( 'formulario-registro-empresa' ) ) {
    echo 'ESTOY EN EL IF';
}

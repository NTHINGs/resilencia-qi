<?php if (isset( $_POST['submit'] )) { //El formulario ha sido enviado
  global $reg_errors;
  $reg_errors = new WP_Error;

  $user = sanitize_text_field($_POST['user']);
  $email = sanitize_email($_POST['email']);
  $town= sanitize_text_field($_POST['town']);
  $province = sanitize_text_field($_POST['province']);
  $phone = sanitize_text_field($_POST['phone']);

  //Comprobamos que los campos obligatorios no están vacios
  if ( empty( $user ) ) {
    $reg_errors->add("empty-user", "El campo nombre es obligatorio");
  }
  if ( empty( $email ) ) {
    $reg_errors->add("empty-email", "El campo e-mail es obligatorio");
  }
  if ( empty( $town) ) {
    $reg_errors->add("empty-town", "El campo Población es obligatorio");
  }
  //Comprobamos que el email tenga un formato de email válido
  if ( !is_email( $email ) ) {
    $reg_errors->add( "invalid-email", "El e-mail no tiene un formato válido" );
  }

  if ( is_wp_error( $reg_errors ) ) {
    if (count($reg_errors->get_error_messages()) > 0) {
      foreach ( $reg_errors->get_error_messages() as $error ) {
        echo $error . "<br />";
      }
    }
  }

  if (count($reg_errors->get_error_messages()) == 0) {
    $password = wp_generate_password();

    $userdata = array(
      'user_login' => $user,
      'user_email' => $email,
      'user_pass' => $password
    );

    $user_id = wp_insert_user( $userdata );

    //Si todo ha ido bien, agregamos los campos adicionales
    if ( ! is_wp_error( $user_id ) ) {
      update_user_meta( $user_id, 'user_town', $town );
      update_user_meta( $user_id, 'user_province', $province );
      update_user_meta( $user_id, 'user_phone', $phone );

      wp_new_user_notification( $user_id, 'both' );
    }
  }
}?>

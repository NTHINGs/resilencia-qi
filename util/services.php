<?php
/**
 * Servicios para reportes
 *
 * @package	 resiliencia-qi
 * @since    1.0.0
 */

if ( ! function_exists( 'resiliencia_imprimir_reporte_areas_general' ) ) {
    add_action( 'wp_ajax_nopriv_resiliencia_imprimir_reporte_areas_general', 'resiliencia_imprimir_reporte_areas_general' );
    add_action( 'wp_ajax_resiliencia_imprimir_reporte_areas_general', 'resiliencia_imprimir_reporte_areas_general' );

    function resiliencia_imprimir_reporte_areas_general() {
        global $wpdb;
        $org_id = $_POST['org_id'];
        $search = $_POST['search'];
        $area = $_POST['area'];

		$sql = "SELECT id, nombre, edad, fechaaplicacion FROM {$wpdb->prefix}resiliencia_registros WHERE organizacion = '{$org_id}'";
		if(!empty($search)){
			$sql .= " AND nombre LIKE '%{$search}%'";
		}
		if(!empty($area)) {
			$sql .= " AND area = '{$area}'";
		}

		$data = $wpdb->get_results( $sql, 'ARRAY_A' );
		foreach($data as $index => $row) {
			$resultados = get_resultados($row['id'], $area);
			$data[$index]['autoestima'] = calcular_rango_json('autoestima', (int)$resultados[0]);
			$data[$index]['empatia'] =  calcular_rango_json('empatia', (int)$resultados[1]);
			$data[$index]['autonomia'] = calcular_rango_json('autonomia', (int)$resultados[2]);
			$data[$index]['humor'] = calcular_rango_json('humor', (int)$resultados[3]);
			$data[$index]['creatividad'] = calcular_rango_json('creatividad', (int)$resultados[4]);
			$data[$index]['total'] = calcular_total($resultados);
		}
        wp_send_json($data);
    }
}
<?php
/**
 * Funciones compartidas entre varios archivos del plugin
 *
 *
 * @package	 resiliencia-qi
 * @since    1.0.0
 */

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


function get_user_hash() {
    $current_user = wp_get_current_user();
    $hash = get_user_meta($current_user->ID, 'hash', true);
    return $hash;
}

function get_resultados($cuestionario_id, $area = NULL) {
    global $wpdb, $obj;
    $resultados = array();

    foreach($obj as $grupo => $array_tipo_res) {
        $resultado = 0;
        foreach($array_tipo_res as $tipo => $respuesta) {
            $sql = "SELECT COUNT(RS.respuesta)
                    FROM {$wpdb->prefix}resiliencia_resultados RS, {$wpdb->prefix}resiliencia_preguntas P, {$wpdb->prefix}resiliencia_registros R
                    WHERE RS.pregunta = P.id 
                    AND RS.cuestionario = R.id 
                    AND R.id = '{$cuestionario_id}'
                    AND P.tipo = '{$tipo}'
                    AND P.grupo = '{$grupo}'
                    AND RS.respuesta = '{$respuesta}'";
            if ($area != NULL) {
                $sql .= " AND R.area = '{$area}'";
            }
            $resultado += (int)$wpdb->get_var($sql);
        }
        array_push($resultados, $resultado);
    }

    return $resultados;
}

function get_resultados_por_org($org_id, $area = NULL) {
    global $wpdb, $obj;
    $resultados = array();

    foreach($obj as $grupo => $array_tipo_res) {
        $resultado = 0;
        $sqltotalregistros = "SELECT COUNT(*) FROM {$wpdb->prefix}resiliencia_registros WHERE organizacion = '{$org_id}'";
        if ($area != NULL) {
            $sqltotalregistros .= " AND area = '{$area}'";
        }
        $contador = (int)$wpdb->get_var($sqltotalregistros);
        if ($contador > 0) {
            foreach($array_tipo_res as $tipo => $respuesta) {
                $sql = "SELECT COUNT(RS.respuesta)
                        FROM {$wpdb->prefix}resiliencia_resultados RS, {$wpdb->prefix}resiliencia_preguntas P, {$wpdb->prefix}resiliencia_registros R
                        WHERE RS.pregunta = P.id 
                        AND RS.cuestionario = R.id 
                        AND R.organizacion = '{$org_id}'
                        AND P.tipo = '{$tipo}'
                        AND P.grupo = '{$grupo}'
                        AND RS.respuesta = '{$respuesta}'";
                if ($area != NULL) {
                    $sql .= " AND R.area = '{$area}'";
                }
                $resultado += (int)$wpdb->get_var($sql);
            }
            $promedio = $resultado / $contador;
            array_push($resultados, $promedio);
        } else {
            $promedio = 0;
            array_push($resultados, $promedio);
        }
    }

    return $resultados;
}

function calcular_rango($grupo, $puntaje) {
    $puntajes = array(
        'autoestima' => array(
            'Alto' => array(
                'sup' => 10,
                'inf' => 9,
            ),
            'Medio' => array(
                'sup' => 8,
                'inf' => 6,
            ),
            'Bajo' => array(
                'sup' => 5,
                'inf' => 0,
            ),
        ),
        'empatia' => array(
            'Alto' => array(
                'sup' => 10,
                'inf' => 8,
            ),
            'Medio' => array(
                'sup' => 7,
                'inf' => 5
            ),
            'Bajo' => array(
                'sup' => 4,
                'inf' => 0,
            ),
        ),
        'autonomia' => array(
            'Alto' => array(
                'sup' => 10,
                'inf' => 8,
            ),
            'Medio' => array(
                'sup' => 7,
                'inf' => 4
            ),
            'Bajo' => array(
                'sup' => 3,
                'inf' => 0,
            ),
        ),
        'humor' => array(
            'Alto' => array(
                'sup' => 10,
                'inf' => 7,
            ),
            'Medio' => array(
                'sup' => 6,
                'inf' => 4
            ),
            'Bajo' => array(
                'sup' => 3,
                'inf' => 0,
            ),
        ),
        'creatividad' => array(
            'Alto' => array(
                'sup' => 8,
                'inf' => 6,
            ),
            'Medio' => array(
                'sup' => 5,
                'inf' => 3
            ),
            'Bajo' => array(
                'sup' => 2,
                'inf' => 0,
            ),
        ),
    );
    foreach($puntajes[$grupo] as $rango => $limites) {
        if($puntaje >= $limites['inf'] && $puntaje <= $limites['sup']) {
            $color = "#000";
            switch($rango) {
                case 'Bajo':
                    $color = '#dc3545';
                    break;
                case 'Medio':
                    $color = '#007bff';
                    break;
                case 'Alto':
                    $color = '#28a745';
                    break;
            }
            return "<span style='color: " . $color . ";'>" . $rango . " (" . $puntaje . ")</span>";
        }
    }
}
function calcular_rango_json($grupo, $puntaje) {
    $puntajes = array(
        'autoestima' => array(
            'Alto' => array(
                'sup' => 10,
                'inf' => 9,
            ),
            'Medio' => array(
                'sup' => 8,
                'inf' => 6,
            ),
            'Bajo' => array(
                'sup' => 5,
                'inf' => 0,
            ),
        ),
        'empatia' => array(
            'Alto' => array(
                'sup' => 10,
                'inf' => 8,
            ),
            'Medio' => array(
                'sup' => 7,
                'inf' => 5
            ),
            'Bajo' => array(
                'sup' => 4,
                'inf' => 0,
            ),
        ),
        'autonomia' => array(
            'Alto' => array(
                'sup' => 10,
                'inf' => 8,
            ),
            'Medio' => array(
                'sup' => 7,
                'inf' => 4
            ),
            'Bajo' => array(
                'sup' => 3,
                'inf' => 0,
            ),
        ),
        'humor' => array(
            'Alto' => array(
                'sup' => 10,
                'inf' => 7,
            ),
            'Medio' => array(
                'sup' => 6,
                'inf' => 4
            ),
            'Bajo' => array(
                'sup' => 3,
                'inf' => 0,
            ),
        ),
        'creatividad' => array(
            'Alto' => array(
                'sup' => 8,
                'inf' => 6,
            ),
            'Medio' => array(
                'sup' => 5,
                'inf' => 3
            ),
            'Bajo' => array(
                'sup' => 2,
                'inf' => 0,
            ),
        ),
    );
    foreach($puntajes[$grupo] as $rango => $limites) {
        if($puntaje >= $limites['inf'] && $puntaje <= $limites['sup']) {
            $color = array(0, 0, 0);
            switch($rango) {
                case 'Bajo':
                    $color = array(220, 53, 69);
                    break;
                case 'Medio':
                    $color = array(0, 123, 255);
                    break;
                case 'Alto':
                    $color = array(40, 167, 69);
                    break;
            }
            return array('color' => $color, 'text' => $rango . " (" . $puntaje . ")");
        }
    }
}

function calcular_total($resultados) {
    $total = 0;
    foreach ($resultados as $resultado) {
        $total += (int)$resultado;
    }
    return $total;
}
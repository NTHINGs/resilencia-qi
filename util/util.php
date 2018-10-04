<?php
/**
 * Funciones compartidas entre varios archivos del plugin
 *
 *
 * @package	 resiliencia-qi
 * @since    1.0.0
 */
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
        $resultado = 0;
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
            $resultado += (int)$wpdb->get_var(str_replace($variables, $values, $sql));
        }
        array_push($resultados, $resultado);
    }

    return json_encode($resultados);
}
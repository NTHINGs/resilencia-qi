<?php
/**
 * Tabla para visualizar los resultados
 *
 *
 * @package	 resiliencia-qi
 * @since    1.0.0
 */
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Orgs_Resiliencia_Table extends WP_List_Table {
	 /**
     * Metodo para preparar la informacion de la tabla
     *
     * @return Void
     */
    public function prepare_items($search ='') {
		// Construir columnas
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();
		
		// Traer informacion y ordenarla
        $data = $this->table_data($search);
		usort( $data, array( &$this, 'sort_data' ) );
		$this->_column_headers = array($columns, $hidden, $sortable);

		/** Acciones en lote */
		$this->process_bulk_action();

		// Paginacion
        $perPage = $this->get_items_per_page( 'resultados_per_page', 10 );
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);
        $this->set_pagination_args( array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ) );
        $data = array_slice($data,(($currentPage-1)*$perPage),$perPage);
        $this->items = $data;
	}
	
    /**
	 * Sobreescribir el metodo padre para las columnas. Define las columnas utilizadas para la tabla
     *
     * @return Array
     */
    public function get_columns() {
        $columns = array(
			'nombre'       => 'Nombre',
			'id'           => 'ID',
			'autoestima'   => 'Autoestima',
			'empatia'      => 'Empatía',
			'autonomia'    => 'Autonomía',
			'humor'        => 'Humor',
			'creatividad'  => 'Creatividad',
			'total'		   => 'Total',
		);

        return $columns;
	}

    /**
     * Definir las columnas ocultas
     *
     * @return Array
     */
    public function get_hidden_columns() {
        return array();
	}
	
    /**
     * Definir las columnas "sortable"
     *
     * @return Array
     */
    public function get_sortable_columns() {
        return array(
			'nombre' => array('nombre', true),
			'id' => array( 'id', false ),
		);
	}
	
    /**
     * Construir datos de la tabla
     *
     * @return Array
     */
    private function table_data($search='') {
		global $wpdb;

		if(!empty($search)){
			$args = array(
				'search'         => '*'.esc_attr( $search ).'*',
				'search_columns' => array( 'display_name' )
			);
			$query = new WP_User_Query( $args );
			$data = array();
			$index = 0;
			foreach ( $query->get_results() as $user ) {
				$hash = get_user_meta($user->ID, 'hash', true);
				if($hash) {
					$data[$index]['nombre'] = $user->display_name;
					$data[$index]['id'] = $hash;
					$resultados = get_resultados_por_org($hash);
					$data[$index]['autoestima'] = calcular_rango('autoestima', (int)$resultados[0]);
					$data[$index]['empatia'] =  calcular_rango('empatia', (int)$resultados[1]);
					$data[$index]['autonomia'] = calcular_rango('autonomia', (int)$resultados[2]);
					$data[$index]['humor'] = calcular_rango('humor', (int)$resultados[3]);
					$data[$index]['creatividad'] = calcular_rango('creatividad', (int)$resultados[4]);
					$data[$index]['total'] = calcular_total($resultados);
					$index++;
				}
			}
			return $data;
		} else {
			
			$sql = "SELECT DISTINCT organizacion as id FROM {$wpdb->prefix}resiliencia_registros";

			$data = $wpdb->get_results( $sql, 'ARRAY_A' );

			foreach($data as $index => $row) {
				$data[$index]['nombre'] = get_users(
					array(
						'role' => 'empresa',
						'hash' => $row['id'],
					)
				)[0]->display_name;
				$resultados = get_resultados_por_org($row['id']);
				$data[$index]['autoestima'] = calcular_rango('autoestima', (int)$resultados[0]);
				$data[$index]['empatia'] =  calcular_rango('empatia', (int)$resultados[1]);
				$data[$index]['autonomia'] = calcular_rango('autonomia', (int)$resultados[2]);
				$data[$index]['humor'] = calcular_rango('humor', (int)$resultados[3]);
				$data[$index]['creatividad'] = calcular_rango('creatividad', (int)$resultados[4]);
				$data[$index]['total'] = calcular_total($resultados);
			}
			return $data;
		}
		
	}
	
	/**
     * Construir nombres de columnas
     *
     * @return Mixed
     */
	function column_nombre( $item ) {
		$title = '<strong>' . $item['nombre'] . '</strong>';
	  
		$actions = [
			'view'    => sprintf( '<a href="?page=%s&action=%s&org_id=%s&noheader=true">Ver</a>', $_REQUEST['page'], 'view', $item['id'] ),
		];
	  
		return $title . $this->row_actions( $actions );
	  }
    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default( $item, $column_name ) {
        switch( $column_name ) {
            case 'id':
			case 'nombre':
			case 'autoestima':
			case 'empatia':
			case 'autonomia':
			case 'humor':
			case 'creatividad':
			case 'total':
			case 'organizacion':
                return $item[ $column_name ];
			default:
                return print_r( $item, true ) ;
        }
    }
    /**
     * Allows you to sort the data by the variables set in the $_GET
     *
     * @return Mixed
     */
    private function sort_data( $a, $b ) {
        // Set defaults
        $orderby = 'id';
        $order = 'asc';
        // If orderby is set, use this as the sort column
        if(!empty($_GET['orderby'])) {
            $orderby = $_GET['orderby'];
        }
        // If order is set use this as the order
        if(!empty($_GET['order'])) {
            $order = $_GET['order'];
        }
        $result = strnatcmp($a[$orderby], $b[$orderby]);
        if($order === 'asc') {
            return $result;
        }
        return -$result;
	}

	/**
     * Mensaje para cuando no hay datos
     *
     * @return Mixed
     */
	public function no_items() {
		echo 'No hay resultados.';
	}
	
	public function process_bulk_action() {
		//Detect when a bulk action is being triggered...
		if ( 'view' === $this->current_action() ) {
			wp_redirect(add_query_arg( 'org_id', $_GET['org_id'], admin_url('admin.php?page=resultados-organizacionales') ));
			exit;
		}
	}
}
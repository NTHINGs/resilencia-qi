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

class Resultados_Resiliencia_Table extends WP_List_Table {
	 /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        $data = $this->table_data();
		usort( $data, array( &$this, 'sort_data' ) );
		/** Process bulk action */
		$this->process_bulk_action();
        $perPage = $this->get_items_per_page( 'resultados_per_page', 10 );
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);
        $this->set_pagination_args( array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ) );
        $data = array_slice($data,(($currentPage-1)*$perPage),$perPage);
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
    }
    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns()
    {
        $columns = array(
			'cb'     	   => '<input type="checkbox" />',
            'id'           => 'ID',
            'nombre'       => 'Nombre'
        );
        return $columns;
	}
	public function get_bulk_actions() {
		$actions = [
			'bulk-delete' => 'Eliminar'
		];
		
		return $actions;
	}
    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns()
    {
        return array();
    }
    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns()
    {
        return array(
			'id' => array( 'id', true ),
			'nombre' => array('nombre', false)
		);
    }
    /**
     * Get the table data
     *
     * @return Array
     */
    private function table_data()
    {
        global $wpdb;
		$sql = "SELECT id, nombre, edad, fechaaplicacion FROM {$wpdb->prefix}resiliencia_registros";
		$data = $wpdb->get_results( $sql, 'ARRAY_A' );
		// ['Autoestima', 'Empatía', 'Autonomía', 'Humor', 'Creatividad']
		foreach($data as $index => $row) {
			print $row->id;
			
		}
		// get_resultados($cuestionario_id)
        return $data;
	}
	
	function column_name( $item ) {
		// create a nonce
		$delete_nonce = wp_create_nonce( 'sp_delete_registro' );
		
		$title = '<strong>' . $item['nombre'] . '</strong>';
		
		$actions = [
			'delete' => sprintf( '<a href="?page=%s&action=%s&registro=%s&_wpnonce=%s">Eliminar</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
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
    public function column_default( $item, $column_name )
    {
        switch( $column_name ) {
            case 'id':
            case 'nombre':
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
    private function sort_data( $a, $b )
    {
        // Set defaults
        $orderby = 'id';
        $order = 'asc';
        // If orderby is set, use this as the sort column
        if(!empty($_GET['orderby']))
        {
            $orderby = $_GET['orderby'];
        }
        // If order is set use this as the order
        if(!empty($_GET['order']))
        {
            $order = $_GET['order'];
        }
        $result = $a[$orderby] - $b[$orderby];
        if($order === 'asc')
        {
            return $result;
        }
        return -$result;
	}

	public static function delete_registro( $id ) {
		global $wpdb;
		
		$wpdb->delete(
			"{$wpdb->prefix}resiliencia_registros",
			[ 'id' => $id ],
			[ '%d' ]
		);
	}
	public function no_items() {
		echo 'Nadie ha contestado aún.';
	}

	function column_cb( $item ) {
		return sprintf(
		  '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
		);
	}
	
	public function process_bulk_action() {
		//Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {
	  
		  // In our file that handles the request, verify the nonce.
		  $nonce = esc_attr( $_REQUEST['_wpnonce'] );
	  
		  if ( ! wp_verify_nonce( $nonce, 'sp_delete_registro' ) ) {
			die( 'Go get a life script kiddies' );
		  }
		  else {
			self::delete_registro( absint( $_GET['registro'] ) );
	  
			wp_redirect( esc_url( add_query_arg() ) );
			exit;
		  }
	  
		}
	  
		// If the delete bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
			 || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		) {
	  
		  $delete_ids = esc_sql( $_POST['bulk-delete'] );
	  
		  // loop over the array of record IDs and delete them
		  foreach ( $delete_ids as $id ) {
			self::delete_registro( $id );
	  
		  }
	  
		  wp_redirect( esc_url( add_query_arg() ) );
		  exit;
		}
	}
}
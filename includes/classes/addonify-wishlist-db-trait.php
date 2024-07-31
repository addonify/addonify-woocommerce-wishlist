<?php
/**
 * Trait for performing CRUD operation on a table.
 *
 * @link       https://www.addonify.com
 * @since      2.0.0
 *
 * @package    Addonify_Wishlist
 * @subpackage Addonify_Wishlist/includes
 */

/**
 * Trait for performing CRUD operation on a table.
 *
 * @link       https://www.addonify.com
 * @since      2.0.0
 *
 * @package    Addonify_Wishlist
 * @subpackage Addonify_Wishlist/includes
 */
trait Addonify_Wishlist_DB_Trait {

	/**
	 * CMPriceAction database prefix.
	 *
	 * @var string
	 */
	private static $db_prefix = ADDONIFY_WISHLIST_DB_INITIALS;

	/**
	 * Get API failover table name.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public static function get_table_name() {

		global $wpdb;

		return $wpdb->base_prefix . self::$db_prefix . 'table';
	}

	/**
	 * Retrive all rows from the table.
	 *
	 * @since 1.0.0
	 *
	 * @param string $order_by Order By Column name (Optional).
	 * @param string $order_dir Order direction either asc or desc (Optional).
	 * @return array|false Array of row objects.
	 */
	public static function get_all_rows( $order_by = 'id', $order_dir = 'ASC' ) {

		global $wpdb;

		$table_name = self::get_table_name();

		$order = " ORDER BY {$order_by} {$order_dir} ";

		return $wpdb->get_results( "SELECT * FROM {$table_name} {$order}" ); //phpcs:ignore
	}

	/**
	 * Retrive id of the row which contains the field and value.
	 *
	 * @since 1.0.0
	 * @param string $field Column.
	 * @param string $value Column value.
	 * @return int $id Row id.
	 */
	public static function get_row_id( $field, $value ) {

		global $wpdb;

		$table_name = self::get_table_name();

		return $wpdb->get_var( "SELECT id FROM {$table_name} WHERE {$field} = '{$value}'" ); //phpcs:ignore
	}

	/**
	 * Retrive a row data from the table.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Arguments.
	 * @return object
	 */
	public static function get_row( $args = array() ) {

		global $wpdb;

		$defaults = array(
			'fields' => array(),
			'where'  => array(),
		);

		$args = wp_parse_args( $args, $defaults );

		$table_name = self::get_table_name();

		// Building SELECT fields.
		$fields = '*';
		if ( ! empty( $args['fields'] ) && is_array( $args['fields'] ) ) {
			$fields = implode( ',', array_map( 'esc_sql', $args['fields'] ) );
		}

		$sql = "SELECT {$fields} FROM {$table_name} WHERE 1 = 1";

		// Building WHERE clause.
		if ( ! empty( $args['where'] ) && is_array( $args['where'] ) ) {
			foreach ( $args['where'] as $field => $value ) {
				if ( is_null( $value ) ) {
					$sql .= " AND {$field} IS NULL"; // phpcs:ignore
				} else {
					$sql .= " AND {$field} = '{$value}'"; // phpcs:ignore
				}
			}
		}

		return $wpdb->get_row( $sql ); //phpcs:ignore
	}

	/**
	 * Get Rows with provided field name and value
	 *
	 * @param array $args Arguments.
	 * @return array
	 */
	public static function get_rows( $args ) {

		global $wpdb;

		$defaults = array(
			'fields'   => array(), // Fields to select.
			'where'    => array(), // WHERE conditions.
			'order_by' => 'id', // Default order by field.
			'order'    => 'DESC', // Order direction.
			'limit'    => 0, // Limit of records.
			'offset'   => 0, // Offset for records.
		);

		$args = wp_parse_args( $args, $defaults );

		$table_name = self::get_table_name();

		// Building SELECT fields.
		$fields = '*';
		if ( ! empty( $args['fields'] ) && is_array( $args['fields'] ) ) {
			$fields = implode( ',', array_map( 'esc_sql', $args['fields'] ) );
		}

		$sql = "SELECT {$fields} FROM {$table_name} WHERE 1 = 1";

		// Building WHERE clause.
		if ( ! empty( $args['where'] ) && is_array( $args['where'] ) ) {
			foreach ( $args['where'] as $field => $value ) {
				if ( is_null( $value ) ) {
					$sql .= " AND {$field} IS NULL"; // phpcs:ignore
				} else {
					$sql .= " AND {$field} = '{$value}'"; // phpcs:ignore
				}
			}
		}

		// Building ORDER BY clause.
		$order_by = esc_sql( $args['order_by'] );
		$order    = strtoupper( $args['order'] ) === 'ASC' ? 'ASC' : 'DESC';

		$sql .= " ORDER BY {$order_by} {$order}";

		// Building LIMIT clause.
		$limit = '';
		if ( $args['limit'] > 0 ) {
			$sql = "LIMIT {$args['limit']} OFFSET {$args['offset']}";
		}

		// Execute the query.
		$results = $wpdb->get_results( $sql ); // phpcs:ignore

		return $results;
	}

	/**
	 * Insert data in a new row in the table.
	 *
	 * @since 1.0.0
	 * @param array $data Row data.
	 * @return int|boolean Row ID if data inserted successfully, else false.
	 */
	public static function insert_row( $data ) {

		global $wpdb;

		$table_name = self::get_table_name();

		return $wpdb->insert( $table_name, $data ) ? $wpdb->insert_id : false; //phpcs:ignore
	}

	/**
	 * Updates data of a row in the table.
	 *
	 * @param array $data Update data.
	 * @param array $where Update row condition.
	 * @return int|boolean Number of rows updated if data updated successfully, else false.
	 */
	public static function update_row( $data, $where ) {

		global $wpdb;

		$table_name = self::get_table_name();

		return $wpdb->update( $table_name, $data, $where ); //phpcs:ignore
	}

	/**
	 * Deletes a row data from the table.
	 *
	 * @since 1.0.0
	 * @param int $id Row ID.
	 * @return int|boolean Number of rows deleted if data deleted successfully, else false.
	 */
	public static function delete_row( $id ) {

		if ( ! $id ) {
			return false;
		}

		global $wpdb;

		$table_name = self::get_table_name();

		$row_id = array( 'id' => $id );

		return $wpdb->delete( $table_name, $row_id ); //phpcs:ignore
	}

	/**
	 * Delete on provided where condition.
	 *
	 * @param array $where Where Conditions.
	 */
	public static function delete_where( $where ) {
		if ( empty( $where ) ) {
			return false;
		}
		global $wpdb;

		$table_name = self::get_table_name();

		return $wpdb->delete( $table_name, $where );//phpcs:ignore
	}

	/**
	 * Deletes all rows from the table.
	 *
	 * @since 1.0.0
	 * @return boolean true on success, else false.
	 */
	public static function clear_table() {

		global $wpdb;

		$table_name = self::get_table_name();

		return $wpdb->query( "TRUNCATE TABLE {$table_name}" ); //phpcs:ignore
	}

	/**
	 * Count the rows in the table.
	 *
	 * @since 1.0.5
	 * @return string|null Database query result (as string), or null on failure.
	 */
	public static function total_rows_count() {

		global $wpdb;

		$table_name = self::get_table_name();

		return $wpdb->get_var( "SELECT COUNT(*) FROM {$table_name}" ); //phpcs:ignore
	}

	/**
	 * Get all tables.
	 */
	private function get_all_tables() {

		global $wpdb;

		return $wpdb->get_results( 'show tables', ARRAY_A ); //phpcs:ignore
	}

	/**
	 * Insert multiple rows in table.
	 *
	 * @param array $data Multidimensional data.
	 */
	public function insert_multiple_rows( $data ) {
		$table = $this->get_table_name();
		if ( ! empty( $data ) ) {
			$this->bulk_insert( $table, $data );
		}
	}

	/**
	 * Bulk inserts records into a table using WPDB.  All rows must contain the same keys.
	 * Returns number of affected (inserted) rows.
	 *
	 * @param  string $table Name of table.
	 * @param  array  $rows Rows to be inserted in associative array with their values.
	 * @return int Number of affected rows.
	 */
	private function bulk_insert( $table, $rows ) {
		global $wpdb;

		// Extract column list from first row of data.
		$columns = array_keys( $rows[ array_key_first( $rows ) ] );
		asort( $columns );
		$column_list = '`' . implode( '`, `', $columns ) . '`';

		// Start building SQL, initialise data and placeholder arrays.
		$sql          = "INSERT INTO `$table` ($column_list) VALUES\n";
		$placeholders = array();
		$data         = array();

		// Build placeholders for each row, and add values to data array.
		foreach ( $rows as $row ) {
			ksort( $row );
			$row_placeholders = array();

			foreach ( $row as $value ) {
				$data[]             = $value;
				$row_placeholders[] = is_numeric( $value ) ? '%d' : '%s';
			}

			$placeholders[] = '(' . implode( ', ', $row_placeholders ) . ' )';
		}

		// Stitch all rows together.
		$sql .= implode( ",\n", $placeholders );

		// Run the query.  Returns number of affected rows.
		return $wpdb->query( $wpdb->prepare( $sql, $data ) ); // phpcs:ignore
	}
}

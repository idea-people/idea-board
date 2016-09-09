<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-29
 * Time: 오후 1:19
 */

namespace ideapeople\util\wp;


class CptMetaCopy {
	public $table_name;
	public $post_type;

	public function __construct( $trigger_table_name, $post_type ) {
		global $wpdb;

		$this->table_name = "{$wpdb->prefix}$trigger_table_name";
		$this->post_type  = $post_type;
	}

	public function run() {
		$this->create_copy_meta_table();

		add_action( 'added_post_meta', array( $this, 'added_post_meta' ), 10, 4 );
		add_action( 'updated_post_meta', array( $this, 'updated_post_meta' ), 10, 4 );
		add_action( 'deleted_post_meta', array( $this, 'deleted_post_meta' ), 10, 4 );
	}

	public function added_post_meta( $mid, $object_id, $meta_key, $_meta_value ) {
		global $wpdb, $post_type;

		if ( $post_type == $this->post_type ) {
			$wpdb->insert( $this->table_name, array(
				'meta_id'    => $mid,
				'post_id'    => $object_id,
				'meta_key'   => $meta_key,
				'meta_value' => $_meta_value
			) );
		}
	}

	public function updated_post_meta( $meta_id, $object_id, $meta_key, $_meta_value ) {
		global $wpdb, $post_type;

		if ( $post_type == $this->post_type ) {
			$wpdb->update( $this->table_name, array(
				'meta_value' => $_meta_value
			), array(
				'meta_id'  => $meta_id,
				'post_id'  => $object_id,
				'meta_key' => $meta_key
			) );
		}
	}

	public function deleted_post_meta( $meta_ids, $object_id, $meta_key, $_meta_value ) {
		global $wpdb, $post_type;

		if ( $post_type == $this->post_type ) {
			if ( is_array( $meta_ids ) ) {
				foreach ( $meta_ids as $meta_id ) {
					$wpdb->delete( $this->table_name, array(
						'meta_id' => $meta_id,
					) );
				}
			} else {
				$wpdb->delete( $this->table_name, array(
					'meta_id' => $meta_ids,
				) );
			}
		}
	}

	public function create_copy_meta_table() {
		global $wpdb;

		if ( ! $this->existsTable( $this->table_name ) ) {
			$sql = "CREATE TABLE {$this->table_name} LIKE {$wpdb->postmeta};";
			$wpdb->query( $sql );
		}
	}

	public function existsTable( $tableName ) {
		global $wpdb;

		$query = $wpdb->prepare( "
            SELECT COUNT(*) AS count
            FROM information_schema.tables
            WHERE table_schema = '" . DB_NAME . "'
            AND table_name = %s ", $tableName );

		return $wpdb->get_var( $query );
	}
}
<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-25
 * Time: 오후 5:38
 */

namespace ideapeople\util\wp;


class MetaUtils {
	public static function has_meta( $meta_type, $meta_key, $object_id, $meta_value = null ) {
		global $wpdb;

		if ( ! $meta_type || ! $meta_key || ! is_numeric( $object_id ) ) {
			return false;
		}

		$object_id = absint( $object_id );
		if ( ! $object_id ) {
			return false;
		}

		$table = _get_meta_table( $meta_type );

		if ( ! $table ) {
			return false;
		}

		$column = sanitize_key( $meta_type . '_id' );

		$meta_key = wp_unslash( $meta_key );

		$query = " SELECT COUNT(*) FROM $table WHERE meta_key = %s AND $column = %d ";

		if ( $meta_value ) {
			$query .= " AND meta_value = %s";

			$q = $wpdb->prepare( $query, $meta_key, $object_id, $meta_value );
		} else {
			$q = $wpdb->prepare( $query, $meta_key, $object_id );
		}


		$result = $wpdb->get_var( $q );

		return $result;
	}
}
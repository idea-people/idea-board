<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-25
 * Time: 오후 11:40
 */

namespace ideapeople\util\wp;


class TermUtils {
	public static function insert_or_update_meta( $post_id, $meta_key, $meta_value, $unique = false ) {
		$has_meta = MetaUtils::has_meta( 'term', $meta_key, $post_id );

		if ( $has_meta ) {
			return update_post_meta( $post_id, $meta_key, $meta_value );
		} else {
			return add_post_meta( $post_id, $meta_key, $meta_value, $unique );
		}
	}

	public static function get_term_meta( $term_id, $name, $defaultValue = null ) {
		$value = get_term_meta( $term_id, $name, true );

		if ( ! $value && ! is_null( $defaultValue ) ) {
			return $defaultValue;
		}

		return $value;
	}
}
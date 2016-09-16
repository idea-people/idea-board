<?php
/**
 * User: ideapeople
 * Mail: ideapeople@ideapeople.co.kr
 * Homepage : ideapeople@ideapeople.co.kr
 */

namespace ideapeople\util\wp;


class CommentUtils {
	public static function insert_or_update_meta( $comment_id, $meta_key, $meta_value, $unique = false ) {
		$has_meta = MetaUtils::has_meta( 'comment', $meta_key, $comment_id );

		if ( $has_meta ) {
			return update_comment_meta( $comment_id, $meta_key, $meta_value );
		} else {
			return add_comment_meta( $comment_id, $meta_key, $meta_value, $unique );
		}
	}
}
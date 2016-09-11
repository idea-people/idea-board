<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-09-09
 * Time: 오후 1:48
 */

namespace ideapeople\board;

class Button {
	public static function reply_button( $post = null ) {
		return self::button( 'reply', 'reply', '답글', Rewrite::reply_link( $post ), $post );
	}

	public static function edit_button( $post = null ) {
		$post = get_post( $post );

		if ( $post->post_author != 0 && get_current_user_id() != $post->post_author ) {
			return null;
		}

		return self::button( 'edit', 'edit', '수정', Rewrite::edit_link( $post ), $post );
	}

	public static function write_button( $post = null ) {
		return self::button( 'write', 'edit', '글쓰기', Rewrite::write_link( $post ), $post );
	}

	public static function list_button( $post = null ) {
		return self::button( 'list', 'list', '목록', Rewrite::list_link( $post ), $post );
	}

	public static function read_button( $post = null ) {
		return self::button( 'read', 'read', '글읽기', get_permalink( $post->ID ), $post );
	}

	public static function prev_button() {
		$html = sprintf( '<a href="%s" class="idea-board-button">%s</a>', wp_get_referer(), '이전' );

		return $html;
	}

	public static function delete_button( $post = null ) {
		$post = get_post( $post );

		if ( $post->post_author != 0 && get_current_user_id() != $post->post_author ) {
			return null;
		}

		return self::button( 'delete', 'delete', '삭제', Rewrite::delete_link( $post ), $post );
	}

	public static function button( $type, $check_role, $title, $link, $post = null ) {
		if ( $check_role && ! Capability::current_user_can( $check_role ) ) {
			return null;
		}

		$html = sprintf( '<a href="%s" class="idea-board-button">%s</a>', $link, $title );
		$html = apply_filters( 'idea_board_button_' . $type, $html, $title, $link, $post );

		return $html;
	}
}
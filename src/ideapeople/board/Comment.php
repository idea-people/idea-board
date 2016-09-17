<?php
/**
 * User: ideapeople
 * Mail: ideapeople@ideapeople.co.kr
 * Homepage : ideapeople@ideapeople.co.kr
 */

namespace ideapeople\board;


use ideapeople\util\wp\PasswordUtils;

class Comment {
	public static function is_logged_in_comment( $comment_ID ) {
		$comment = get_comment( $comment_ID );

		$comment_password = self::get_comment_password( $comment_ID );

		return $comment->user_id && empty( $comment_password );
	}

	public static function is_author( $comment_ID ) {
		$comment = get_comment( $comment_ID );

		return $comment->user_id == get_current_user_id();
	}

	public static function comment_password_form( $comment_ID ) {
		$comment = get_comment( $comment_ID );

		$label  = 'pwbox-' . ( empty( $comment->comment_ID ) ? rand() : $comment->comment_ID );
		$output = '<form action="' . add_query_arg( array( 'action' => 'idea_comment_password_check' ), admin_url( '/admin-ajax.php' ) ) . '" class="post-password-form" method="post">
		<p>' . __( 'This content is password protected. To view it please enter your password below:' ) . '</p>
		<p><label for="' . $label . '">' . __( 'Password:' ) . ' <input name="comment_password" id="' . $label . '" type="password" size="20" /></label> <input type="submit" name="Submit" value="' . esc_attr_x( 'Enter', 'post password form' ) . '" /></p></form>
		';

		$output .= '<div class="idea-board-buttons">' . Button::prev_button() . '</div>';

		return $output;
	}

	public static function password_required( $comment_ID ) {
		if ( Capability::is_board_admin() ) {
			return false;
		}

		$comment = get_comment( $comment_ID );

		if ( $comment->user_id != get_current_user_id() && is_user_logged_in() ) {
			return true;
		}

		if ( $comment->user_id == get_current_user_id() && is_user_logged_in() ) {
			return false;
		}

		$password = Comment::get_comment_password( $comment_ID );

		if ( ! is_user_logged_in() && empty( $password ) ) {
			return true;
		}

		if ( ! PasswordUtils::comment_password_required( $password ) ) {
			return false;
		}

		return true;
	}

	public static function get_comment_password( $comment_ID ) {
		return get_comment_meta( $comment_ID, 'comment_password', true );
	}

	public static function add_comment_fields() {
		echo sprintf( '<input type="hidden" name="idea_comment_nonce" value="%s">', wp_create_nonce( 'idea_comment_edit' ) );
		echo sprintf( '<input type="hidden" name="comment_ID" value="%s">', get_query_var( 'comment_ID' ) );
	}

	public static function add_comment_default_fields( $fields ) {
		$post = get_post();

		if ( $post->post_type != PluginConfig::$board_post_type ) {
			return $fields;
		}

		$fields[ 'comment-form-password' ] =
			'<p class="comment-form-password"><label for="comment_password">' . __( 'Password' ) . '<span class="required">*</span></label> ' .
			'<input id="comment_password" name="comment_password" type="password" size="30" maxlength="200" required /></p>';

		$fields = apply_filters( 'idea_board_add_comment_fields', $fields, $post );

		return $fields;
	}
}
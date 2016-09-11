<?php
/**
 * User: ideapeople
 * Mail: ideapeople@ideapeople.co.kr
 * Homepage : ideapeople@ideapeople.co.kr
 */

namespace ideapeople\board;


use ideapeople\util\wp\PasswordUtils;

class Comment {
	public static function comment_password_form( $comment_ID ) {
		$comment = get_comment( $comment_ID );

		$label  = 'pwbox-' . ( empty( $comment->comment_ID ) ? rand() : $comment->comment_ID );
		$output = '<form action="' . add_query_arg( array( 'action' => 'idea_comment_password_check' ), admin_url( '/admin-ajax.php' ) ) . '" class="post-password-form" method="post">
		<p>' . __( 'This content is password protected. To view it please enter your password below:' ) . '</p>
		<p><label for="' . $label . '">' . __( 'Password:' ) . ' <input name="comment_password" id="' . $label . '" type="password" size="20" /></label> <input type="submit" name="Submit" value="' . esc_attr_x( 'Enter', 'post password form' ) . '" /></p></form>
		';

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
}
<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-24
 * Time: 오후 10:22
 */

namespace ideapeople\board\validator;

use ideapeople\board\Capability;
use ideapeople\board\Comment;
use ideapeople\board\PluginConfig;
use ideapeople\board\Post;
use ideapeople\board\view\AuthFailView;
use ideapeople\board\view\PasswordView;
use ideapeople\util\wp\PostUtils;

class ViewValidator {
	public function pre_cap_check_edit_view( $view, $post ) {
		$post = get_post( $post );

		if ( ! $post || $post->post_type != PluginConfig::$board_post_type ) {
			return true;
		}

		if ( Capability::is_board_admin() ) {
			return true;
		}

		if ( PostUtils::is_author( $post ) ) {
			return true;
		}

		$failView     = new AuthFailView();
		$passwordView = new PasswordView();

		if ( ! Capability::current_user_can( 'edit' ) ) {
			return $failView;
		}

		if ( $post->post_author != 0 && get_current_user_id() != $post->post_author ) {
			return $failView;
		}

		if ( Post::password_required( $post ) ) {
			return $passwordView;
		}

		return $view;
	}

	public function pre_cap_check_comment_view( $view, $comment_ID ) {
		$failView = new AuthFailView();

		$password = Comment::get_comment_password( $comment_ID );

		if ( ! is_user_logged_in() && empty( $password ) ) {
			return $failView;
		}

		if ( is_user_logged_in() && ! empty( $password ) ) {
			return $failView;
		}

		if ( Comment::password_required( $comment_ID ) ) {
			return Comment::comment_password_form( $comment_ID );
		}

		return $view;
	}

	public function pre_cap_check_read_view( $view, $post ) {
		$post = get_post( $post );

		if ( ! $post || $post->post_type != PluginConfig::$board_post_type ) {
			return true;
		}

		if ( Capability::is_board_admin() ) {
			return true;
		}

		if ( PostUtils::is_author( $post ) ) {
			return true;
		}

		if ( ! Capability::current_user_can( 'read' ) ) {
			return new AuthFailView();
		}

		if ( Post::is_secret( $post->ID ) ) {
			if ( ! is_user_logged_in() ) {
				if ( empty( $post->post_password ) ) {
					return new AuthFailView();
				}
				if ( Post::password_required( $post ) ) {
					return new PasswordView();
				} else {
					return true;
				}
			} else {
				if ( ! PostUtils::is_author( $post ) ) {
					return new AuthFailView();
				}
			}
		}

		return $view;
	}
}
<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-24
 * Time: 오후 10:22
 */

namespace ideapeople\board;

use ideapeople\board\setting\Setting;
use ideapeople\board\view\AbstractView;
use ideapeople\board\view\AuthFailView;
use ideapeople\board\view\PasswordView;
use ideapeople\util\wp\PostUtils;

class ViewInterceptor {
	/**
	 * @param $view AbstractView
	 * @param $post
	 *
	 *
	 * @param $board Setting
	 *
	 * @return bool|AbstractView|AuthFailView|PasswordView
	 */
	public function pre_cap_check_edit_view( $view, $post, $board ) {
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

	/**
	 * @param $view
	 * @param $post \WP_Post
	 *
	 * @return bool|AuthFailView|PasswordView
	 */
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
			if ( ! is_user_logged_in() && Post::password_required( $post ) ) {
				return new PasswordView();
			} else {
				return new AuthFailView();
			}
		}

		return $view;
	}
}
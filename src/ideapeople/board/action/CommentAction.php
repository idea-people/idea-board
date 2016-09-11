<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-26
 * Time: 오전 1:18
 */

namespace ideapeople\board\action;


use ideapeople\board\AjaxDieHandler;
use ideapeople\board\Capability;
use ideapeople\board\PluginConfig;
use ideapeople\board\Post;
use ideapeople\board\setting\Setting;
use ideapeople\util\http\Request;
use ideapeople\util\wp\CommentUtils;
use ideapeople\util\wp\MetaUtils;
use ideapeople\util\wp\PasswordUtils;
use ideapeople\util\wp\PostUtils;
use WP_Error;

class CommentAction {
	/**
	 * @var \WP_Term
	 */
	public $board_term;

	public function password_check() {
		$password = $_POST['comment_password'];

		if ( is_user_logged_in() ) {
			wp_redirect( wp_get_referer() );
			die;
		}

		PasswordUtils::action_password( $password, 'comment' );
	}

	public function preprocess_comment( $comment ) {
		$post = get_post( $comment['comment_post_ID'] );

		if ( $post->post_type != PluginConfig::$board_post_type ) {
			return $comment;
		}

		$this->board_term = Setting::get_board_from_post( $post->ID );

		add_filter( 'option_comment_whitelist', array( $this, 'option_comment_whitelist' ) );

		add_filter( 'option_comment_moderation', array( $this, 'option_comment_moderation' ) );

		return $comment;
	}

	public function option_comment_moderation( $value ) {
		if ( ! $this->board_term ) {
			return $value;
		}

		$term_id = $this->board_term->term_id;

		return Setting::get_comment_moderation( $term_id );
	}

	public function option_comment_whitelist( $value ) {
		if ( ! $this->board_term ) {
			return $value;
		}

		$term_id = $this->board_term->term_id;

		return Setting::get_comment_whitelist( $term_id );
	}

	public function handle_comment_delete( $comment_ID ) {
		if ( empty( $comment_ID ) ) {
			$comment_ID = &$_GET['comment_ID'];
		}

		do_action( 'idea_board_action_comment_delete_pre', $comment_ID );

		$return_url = Request::getParameter( 'return_url', wp_get_referer() );

		$view = apply_filters( 'pre_cap_check_comment_view', null, $comment_ID );

		if ( $view ) {
			wp_die();
		}

		wp_delete_comment( $comment_ID );

		do_action( 'idea_board_action_comment_delete_after', $comment_ID );

		wp_redirect( $return_url );

		die;
	}

	public function handle_comment_submission( $comment_data ) {
		if ( empty( $comment_data ) ) {
			$comment_data = &$_POST;
		}

		check_ajax_referer( 'idea_comment_edit', 'idea_comment_nonce' );

		$comment_post_ID  = $comment_parent = 0;
		$comment_author   = $comment_author_email = $comment_author_url = $comment_content = null;
		$comment_password = false;

		if ( isset( $comment_data['comment_post_ID'] ) ) {
			$comment_post_ID = (int) $comment_data['comment_post_ID'];
		}
		if ( isset( $comment_data['author'] ) && is_string( $comment_data['author'] ) ) {
			$comment_author = trim( strip_tags( $comment_data['author'] ) );
		}
		if ( isset( $comment_data['email'] ) && is_string( $comment_data['email'] ) ) {
			$comment_author_email = trim( $comment_data['email'] );
		}
		if ( isset( $comment_data['url'] ) && is_string( $comment_data['url'] ) ) {
			$comment_author_url = trim( $comment_data['url'] );
		}
		if ( isset( $comment_data['comment'] ) && is_string( $comment_data['comment'] ) ) {
			$comment_content = trim( $comment_data['comment'] );
		}
		if ( isset( $comment_data['comment_parent'] ) ) {
			$comment_parent = absint( $comment_data['comment_parent'] );
		}
		if ( isset( $comment_data['comment_password'] ) ) {
			$comment_password = $comment_data['comment_password'];
		}

		if ( ! is_user_logged_in() && ! $comment_password ) {
			wp_die();
		}

		$post  = get_post( $comment_post_ID );
		$board = Setting::get_board_from_post( $post );

		do_action( 'idea_board_action_comment_edit_pre', $comment_data, $board );

		if ( $post->post_type != PluginConfig::$board_post_type ) {
			wp_die();
		}

		if ( empty( $post->comment_status ) ) {
			do_action( 'comment_id_not_found', $comment_post_ID );

			return new WP_Error( 'comment_id_not_found' );
		}

		$status = get_post_status( $post );

		if ( ( 'private' == $status ) && ! current_user_can( 'read_post', $comment_post_ID ) ) {
			return new WP_Error( 'comment_id_not_found' );
		}

		$status_obj = get_post_status_object( $status );

		if ( ! comments_open( $comment_post_ID ) ) {
			do_action( 'comment_closed', $comment_post_ID );

			return new WP_Error( 'comment_closed', __( 'Sorry, comments are closed for this item.' ), 403 );
		} elseif ( 'trash' == $status ) {
			do_action( 'comment_on_trash', $comment_post_ID );

			return new WP_Error( 'comment_on_trash' );
		} elseif ( ! $status_obj->public && ! $status_obj->private ) {
			do_action( 'comment_on_draft', $comment_post_ID );

			return new WP_Error( 'comment_on_draft' );
		} else {
			do_action( 'pre_comment_on_post', $comment_post_ID );
		}

		require_once ABSPATH . '/wp-includes/pluggable.php';

		$user = wp_get_current_user();

		if ( $user->exists() ) {
			if ( empty( $user->display_name ) ) {
				$user->display_name = $user->user_login;
			}

			$comment_author       = $user->display_name;
			$comment_author_email = $user->user_email;
			$comment_author_url   = $user->user_url;
			$user_ID              = $user->ID;

			if ( current_user_can( 'unfiltered_html' ) ) {
				if ( ! isset( $comment_data['_wp_unfiltered_html_comment'] )
				     || ! wp_verify_nonce( $comment_data['_wp_unfiltered_html_comment'], 'unfiltered-html-comment_' . $comment_post_ID )
				) {
					kses_remove_filters();
					kses_init_filters();
				}
			}
		} else {
			if ( get_option( 'comment_registration' ) ) {
				return new WP_Error( 'not_logged_in', __( 'Sorry, you must be logged in to post a comment.' ), 403 );
			}
		}

		$comment_type = '';
		$max_lengths  = wp_get_comment_fields_max_lengths();

		if ( get_option( 'require_name_email' ) && ! $user->exists() ) {
			if ( 6 > strlen( $comment_author_email ) || '' == $comment_author ) {
				return new WP_Error( 'require_name_email', __( '<strong>ERROR</strong>: please fill the required fields (name, email).' ), 200 );
			} elseif ( ! is_email( $comment_author_email ) ) {
				return new WP_Error( 'require_valid_email', __( '<strong>ERROR</strong>: please enter a valid email address.' ), 200 );
			}
		}

		if ( isset( $comment_author ) && $max_lengths['comment_author'] < mb_strlen( $comment_author, '8bit' ) ) {
			return new WP_Error( 'comment_author_column_length', __( '<strong>ERROR</strong>: your name is too long.' ), 200 );
		}

		if ( isset( $comment_author_email ) && $max_lengths['comment_author_email'] < strlen( $comment_author_email ) ) {
			return new WP_Error( 'comment_author_email_column_length', __( '<strong>ERROR</strong>: your email address is too long.' ), 200 );
		}

		if ( isset( $comment_author_url ) && $max_lengths['comment_author_url'] < strlen( $comment_author_url ) ) {
			return new WP_Error( 'comment_author_url_column_length', __( '<strong>ERROR</strong>: your url is too long.' ), 200 );
		}

		if ( '' == $comment_content ) {
			return new WP_Error( 'require_valid_comment', __( '<strong>ERROR</strong>: please type a comment.' ), 200 );
		} elseif ( $max_lengths['comment_content'] < mb_strlen( $comment_content, '8bit' ) ) {
			return new WP_Error( 'comment_content_column_length', __( '<strong>ERROR</strong>: your comment is too long.' ), 200 );
		}

		$commentdata = compact(
			'comment_post_ID',
			'comment_author',
			'comment_author_email',
			'comment_author_url',
			'comment_content',
			'comment_type',
			'comment_parent',
			'user_ID'
		);

		$mode = false;

		if ( isset( $_POST['comment_ID'] ) && ! empty( $_POST['comment_ID'] ) ) {
			$commentdata['comment_ID'] = $_POST['comment_ID'];

			$comment_id = $_POST['comment_ID'];

			if ( Capability::is_board_admin() ) {
				$comment = get_comment( $comment_id );

				$comment_author       = $comment->comment_author;
				$comment_author_email = $comment->comment_author_email;
				$comment_author_url   = $comment->comment_author_url;
				$user_ID              = $comment->user_id;

				$commentdata = wp_parse_args( compact(
					'comment_author',
					'comment_author_email',
					'comment_author_url',
					'user_ID' ), $commentdata );
			}

			$mode = 'update';

			wp_update_comment( $commentdata );
		} else {

			$mode = 'insert';

			$comment_id = wp_new_comment( wp_slash( $commentdata ) );
		}

		if ( ! $comment_id ) {
			return new WP_Error( 'comment_save_error', __( '<strong>ERROR</strong>: The comment could not be saved. Please try again later.' ), 500 );
		}

		if ( $comment_password ) {
			CommentUtils::insert_or_update_meta( $comment_id, 'comment_password', $comment_password );
		}

		do_action( 'idea_board_action_comment_edit_after', $comment_data, $comment_id, $board, $mode );

		wp_redirect( get_permalink( $post->ID ) . '#comment-' . $comment_id );

		die;
	}
}
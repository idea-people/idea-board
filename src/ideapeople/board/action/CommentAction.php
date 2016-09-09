<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-26
 * Time: 오전 1:18
 */

namespace ideapeople\board\action;


use ideapeople\board\setting\Setting;
use ideapeople\board\PluginConfig;
use ideapeople\util\http\Request;

class CommentAction {
	/**
	 * @var \WP_Post
	 */
	public $post;

	/**
	 * @var \WP_Term
	 */
	public $board_term;

	public function preprocess_comment( $comment ) {
		$post = get_post( $comment[ 'comment_post_ID' ] );

		if ( $post->post_type != PluginConfig::$board_post_type ) {
			return $comment;
		}

		$this->post       = &$post;
		$this->board_term = Setting::get_board_from_post( $post->ID );

		add_filter( 'option_comment_whitelist', array( $this, 'option_comment_whitelist' ) );
		add_filter( 'option_comment_moderation', array( $this, 'option_comment_moderation' ) );
		add_action( 'comment_post', array( $this, 'comment_post' ) );

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

	public function comment_post( $comment_ID ) {
		$return_url = Request::getParameter( 'return_url', '' );

		if ( $return_url ) {
			$return_url .= '#comment-' . $comment_ID;

			wp_redirect( $return_url );
			die;
		}
	}
}
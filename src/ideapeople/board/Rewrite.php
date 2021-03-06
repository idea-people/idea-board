<?php
/**
 * User: ideapeople
 * Mail: ideapeople@ideapeople.co.kr
 * Homepage : ideapeople@ideapeople.co.kr
 */

namespace ideapeople\board;


use ideapeople\util\http\Request;

class Rewrite {
	public $query_vars = array(
		'page_mode',
		'pid',
		'parent',
		'searchType',
		'searchValue',
		'idea_board_category',
		'comment_ID',
		'edit_mode'
	);

	public $rewrite_modes = array( 'list', 'edit', 'read', 'delete' );

	public static function delete_comment_link( $comment_ID ) {
		$comment = get_comment( $comment_ID );

		$post = get_comment( $comment->comment_post_ID );

		if ( $post->post_type == PluginConfig::$board_post_type ) {
			$url = add_query_arg( array(
				'comment_ID' => $comment_ID,
				'return_url' => urlencode( get_permalink( $post->ID ) )
			), admin_url( '/admin-ajax.php' ) . '?action=idea_board_comment_delete' );;

			return $url;
		}

		return null;
	}

	public function add_rewrite_rules() {
		$posts = get_posts( array(
			'post_type'      => array( 'page' ),
			'posts_per_page' => - 1
		) );

		wp_reset_query();

		foreach ( $posts as $post ) {
			$rule1 = '^' . $post->post_name . '/idea_board/read/([0-9]+)/?';
			add_rewrite_rule( $rule1, 'index.php?page_id=' . $post->ID . '&page_mode=read&pid=$matches[2]', 'top' );
		}

		flush_rewrite_rules();
	}

	public function register_query_var( $query_var ) {
		$args = wp_parse_args( $this->query_vars, $query_var );

		return $args;
	}

	public static function default_args( $post = null ) {
		$post = get_post( $post );

		$paged       = get_query_var( 'paged' );
		$searchType  = get_query_var( 'searchType', Request::get_parameter( 'searchType', false ) );
		$searchValue = get_query_var( 'searchValue', Request::get_parameter( 'searchValue', false ) );
		$category    = get_query_var( 'idea_board_category', Request::get_parameter( 'idea_board_category', false ) );

		$args = array(
			'paged'               => $paged == 0 ? false : $paged,
			'pid'                 => $post ? $post->ID : false,
			'idea_board_category' => $category,
			'searchType'          => $searchType,
			'searchValue'         => $searchValue
		);

		return $args;
	}

	public static function reply_link( $post = null ) {
		$post = get_post( $post );

		$args = wp_parse_args( array(
			'page_mode' => 'edit',
			'pid'       => false,
			'parent'    => $post->ID
		), self::default_args( $post ) );

		$link = add_query_arg( $args );

		return $link;
	}

	public static function post_type_link( $post_link = '', $post, $link = false ) {
		$post = get_post( $post );

		if ( $post->post_type != PluginConfig::$board_post_type ) {
			return $post_link;
		}

		$page = Post::get_board_page( $post->ID );

		$args = wp_parse_args( array(
			'pid'       => $post->ID,
			'page_mode' => 'read'
		), self::default_args( $post ) );

		if ( is_object( $page ) && $page->post_type != PluginConfig::$board_post_type ) {
			$q = add_query_arg( $args, get_permalink( $page->ID ) );
		} else {
			$q = add_query_arg( $args, $link );
		}

		return $q;
	}

	public static function delete_action_link( $post = null ) {
		$post = get_post( $post );

		$page = Post::get_board_page( $post->ID );

		if ( is_object( $page ) && $page->post_type != PluginConfig::$board_post_type ) {
			return add_query_arg( array(
				'pid'                                     => $post->ID,
				'mode'                                    => 'delete',
				'return_url'                              => get_permalink( $page->ID ),
				PluginConfig::$idea_board_edit_nonce_name => wp_create_nonce( PluginConfig::$idea_board_edit_nonce_action ),
			), self::edit_ajax_link() );
		}

		return false;
	}

	public static function delete_link( $post = null ) {
		$post = get_post( $post );

		$page = Post::get_board_page( $post->ID );

		if ( is_object( $page ) && $page->post_type != PluginConfig::$board_post_type ) {
			return add_query_arg( array(
				'pid'        => $post->ID,
				'page_mode'  => 'edit',
				'edit_mode'  => 'delete',
				'return_url' => get_permalink( $page->ID )
			) );
		}

		return false;
	}

	public static function write_link( $post = null ) {
		$post = get_post( $post );

		$args = wp_parse_args( array(
			'page_mode' => 'edit',
			'pid'       => false
		), self::default_args( $post ) );

		$link = add_query_arg( $args );

		return $link;
	}

	public static function comment_delete_link( $comment_ID, $post = null ) {
		$post = get_post( $post );

		$args = wp_parse_args( array(
			'page_mode'  => 'comment_edit',
			'edit_mode'  => 'delete',
			'comment_ID' => $comment_ID
		), self::default_args( $post ) );

		$link = add_query_arg( $args );

		return $link;
	}

	public static function comment_edit_link( $comment_ID, $post = null ) {
		$post = get_post( $post );

		$args = wp_parse_args( array(
			'page_mode'  => 'comment_edit',
			'comment_ID' => $comment_ID
		), self::default_args( $post ) );

		$link = add_query_arg( $args );

		return $link;
	}

	public static function edit_link( $post = null ) {
		$post = get_post( $post );

		$args = wp_parse_args( array(
			'page_mode' => 'edit'
		), self::default_args( $post ) );

		$link = add_query_arg( $args );

		return $link;
	}

	public static function list_link( $post = null, $link = false ) {
		$args = wp_parse_args( array(
			'page_mode' => 'list',
			'pid'       => false
		), self::default_args( $post ) );

		$link = add_query_arg( $args, $link );

		return $link;
	}

	public static function edit_ajax_link( $post = null ) {
		$link = admin_url( '/admin-ajax.php' );

		$args = wp_parse_args( array(
			'action' => PluginConfig::$board_ajax_edit_name,
			'pid'    => false
		), self::default_args( $post ) );

		$url = add_query_arg( $args, $link );

		return $url;
	}
}
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
		'idea_board_category'
	);

	public $rewrite_modes = array( 'list', 'edit', 'read', 'delete' );

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
		$searchType  = get_query_var( 'searchType', Request::getParameter( 'searchType', false ) );
		$searchValue = get_query_var( 'searchValue', Request::getParameter( 'searchValue', false ) );
		$category    = get_query_var( 'idea_board_category', Request::getParameter( 'idea_board_category', false ) );

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
				'page_mode'  => 'delete',
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
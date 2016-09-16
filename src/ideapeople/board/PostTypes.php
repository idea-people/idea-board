<?php

namespace ideapeople\board;

/**
 * User: ideapeople
 * Mail: ideapeople@ideapeople.co.kr
 * Homepage : ideapeople@ideapeople.co.kr
 */
class PostTypes {
	public $board;
	public $board_item;

	public function __construct() {
		$this->board      = PluginConfig::$board_tax;
		$this->board_item = PluginConfig::$board_post_type;
	}

	public function flush() {
		$this->register();

		flush_rewrite_rules();
	}

	public function register() {
		$this->register_board_item();
		$this->register_board();
	}

	public function register_board() {
		$labels = array(
			'name'          => __idea_board( '아이디어 보드' ),
			'singular_name' => __idea_board( '게시판 관리' ),
			'menu_name'     => __idea_board( '게시판 관리' ),
			'all_items'     => __idea_board( '모든 게시판' ),
			'add_new_item'  => __idea_board( '게시판 추가' ),
			'edit_item'     => __idea_board( '게시판 수정' ),
			'update_item'   => __idea_board( '게시판 수정' ),
			'search_items'  => __idea_board( '게시판 검색' ),
			'popular_items' => __idea_board( '게시판' ),
		);

		$taxonomy_optional = array(
			'labels'       => $labels,
			'public'       => false,
			'rewrite'      => false,
			'show_ui'      => true,
			'query_var'    => $this->board,
			'map_meta_cap' => true,
			'hierarchical' => true
		);

		if ( Capability::is_board_admin() && is_admin() ) {
			$args[ 'public' ] = true;
		}

		register_taxonomy( $this->board, array( $this->board_item ), $taxonomy_optional );
		register_taxonomy_for_object_type( $this->board, $this->board_item );
	}

	public function register_board_item() {
		$labels = array(
			'name'          => __idea_board( '아이디어 글' ),
			'singular_name' => __idea_board( '아이디어 글' ),
			'menu_name'     => __idea_board( '아이디어 보드' ),
			'add_new'       => __idea_board( '게시글 쓰기' ),
			'add_new_item'  => __idea_board( '게시글 쓰기' ),
			'edit'          => __idea_board( '게시글 수정' ),
			'edit_item'     => __idea_board( '게시글 수정' ),
			'view'          => __idea_board( '게시글 확인' ),
			'view_item'     => __idea_board( '게시글 확인' ),
			'search_items'  => __idea_board( '검색' ),
			'all_items'     => __idea_board( '게시글 관리' )
		);

		$args = array(
			'public'             => false,
			'rewrite'            => false,
			'publicly_queryable' => false,
			'query_var'          => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'labels'             => $labels,
			'capability_type'    => $this->board_item,
			'supports'           => array(
				'title',
				'editor',
				'author',
				'thumbnail',
				'comments',
				'excerpt',
				'custom-fields'
			),
			'map_meta_cap'       => true
		);

		if ( Capability::is_board_admin() && is_admin() ) {
			$args[ 'public' ] = true;
		}

		register_post_type( $this->board_item, $args );
	}
}
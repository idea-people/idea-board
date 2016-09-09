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
			'name'          => __( PluginConfig::$plugin_name ),
			'singular_name' => __( '게시판 관리' ),
			'menu_name'     => __( '게시판 관리' ),
			'all_items'     => __( '모든 게시판' ),
			'add_new_item'  => __( '게시판 추가' ),
			'edit_item'     => __( '게시판 수정' ),
			'update_item'   => __( '게시판 수정' ),
			'search_items'  => __( '게시판 검색' ),
			'popular_items' => __( '게시판' ),
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
			'name'          => __( '아이디어 글' ),
			'singular_name' => __( '아이디어 글' ),
			'menu_name'     => PluginConfig::$plugin_name,
			'add_new'       => __( '게시글 쓰기' ),
			'add_new_item'  => __( '게시글 쓰기' ),
			'edit'          => __( '게시글 수정' ),
			'edit_item'     => __( '게시글 수정' ),
			'view'          => __( '게시글 확인' ),
			'view_item'     => __( '게시글 확인' ),
			'search_items'  => __( '검색' ),
			'all_items'     => __( '게시글 관리' )
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
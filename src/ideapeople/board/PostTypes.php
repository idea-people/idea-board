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
			'name'          => __idea_board( 'IDEA BOARD' ),
			'singular_name' => __idea_board( 'Management Forum' ),
			'menu_name'     => __idea_board( 'Management Forum' ),
			'all_items'     => __idea_board( 'All Forums' ),
			'add_new_item'  => __idea_board( 'Add Forum' ),
			'edit_item'     => __idea_board( 'Forum modifications' ),
			'update_item'   => __idea_board( 'Forum modifications' ),
			'search_items'  => __idea_board( 'Forum Search' ),
			'popular_items' => __idea_board( 'Forum' ),
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
			'name'          => __idea_board( 'IDEA POST' ),
			'singular_name' => __idea_board( 'IDEA POST' ),
			'menu_name'     => __idea_board( 'IDEA BOARD' ),
			'add_new'       => __idea_board( 'Write posts' ),
			'add_new_item'  => __idea_board( 'Write posts' ),
			'edit'          => __idea_board( 'Edit posts' ),
			'edit_item'     => __idea_board( 'Edit posts' ),
			'view'          => __idea_board( 'Check posts' ),
			'view_item'     => __idea_board( 'Check posts' ),
			'search_items'  => __idea_board( 'Search' ),
			'all_items'     => __idea_board( 'Management posts' )
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
<?php
/**
 * User: ideapeople
 * Mail: ideapeople@ideapeople.co.kr
 * Homepage : ideapeople@ideapeople.co.kr
 */

namespace ideapeople\board;


use ideapeople\util\wp\WpQuerySearch;
use WP_Query;
use wpdb;

class Query extends WP_Query {
	public function __construct( $query = '' ) {
		$query = wp_parse_args( $query, array(
			'post_type'      => PluginConfig::$board_post_type,
			'paged'          => get_query_var( 'paged' ),
			'post_status'    => array(
				'publish',
				'private',
			),
			'posts_per_page' => ! empty( $query[ 'posts_per_page' ] ) ? $query[ 'posts_per_page' ] : 10,
			'tax_query'      => array(
				'relation' => 'AND',
				array(
					'taxonomy' => PluginConfig::$board_tax,
					'field'    => 'name',
					'terms'    => @$query[ 'board' ]
				)
			)
		) );

		parent::__construct( $query );

		$this->start_no = $this->generateStartNo( $query[ 'paged' ], $query[ 'posts_per_page' ] );
	}

	public function parse_query( $query = '' ) {
		parent::parse_query( $query );

		global $wp_the_query;
		$this->query_vars = wp_parse_args( $this->query_vars, $wp_the_query->query_vars );
	}

	public function get_posts() {
		add_filter( 'posts_where', array( $this, 'posts_where' ) );

		do_action( 'idea_board_pre_get_posts', $this );

		$posts = parent::get_posts();

		do_action( 'idea_board_after_get_posts', $this );

		remove_filter( 'posts_where', array( $this, 'posts_where' ) );

		return $posts;
	}

	public function posts_where( $where ) {
		global $wpdb;

		$query_search = new WpQuerySearch( $where );

		$category = get_query_var( 'idea_board_category' );

		if ( $category ) {
			$query_search->queries[ 'q' ][] = "AND (SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key = 'idea_board_category' AND {$wpdb->postmeta}.post_id={$wpdb->posts}.ID)=%s";
			$query_search->queries[ 'p' ][] = $category;
		}

		$where = $query_search->where( $where );

		return $where;
	}

	private function generateStartNo( $paged, $posts_per_page = 10 ) {
		$found_posts = $this->found_posts;

		$paged = $paged == 0 ? 1 : $paged;

		$number = ( $paged - 1 ) * $posts_per_page;

		return $found_posts - $number;
	}
}
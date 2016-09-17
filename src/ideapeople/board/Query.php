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
		add_filter( 'parse_query', array( $this, 'taxonomy_term_in_query' ) );

		$query = wp_parse_args( $query, array(
			'post_type'      => PluginConfig::$board_post_type,
			'paged'          => get_query_var( 'paged' ),
			'post_status'    => array(
				'publish',
				'private',
			),
			'posts_per_page' => ! empty( $query['posts_per_page'] ) ? $query['posts_per_page'] : 10,
			'tax_query'      => array(
				'relation' => 'AND',
				array(
					'taxonomy' => PluginConfig::$board_tax,
					'field'    => 'name',
					'terms'    => @$query['board']
				)
			)
		) );

		parent::__construct( $query );

		$this->start_no = $this->generateStartNo( $query['paged'], $query['posts_per_page'] );
	}

	public static function start_no() {
		global $wp_query;

		return $wp_query->start_no;
	}

	public function parse_query( $query = '' ) {
		parent::parse_query( $query );

		global $wp_the_query;

		$this->query_vars = wp_parse_args( $this->query_vars, $wp_the_query->query_vars );
	}

	public function taxonomy_term_in_query( $query ) {
		global $pagenow;

		$qv = &$query->query_vars;

		if ( $pagenow == 'edit.php'
		     && isset( $qv['post_type'] )
		     && $qv['post_type'] == PluginConfig::$board_post_type
		     && isset( $qv['term'] )
		     && is_numeric( $qv['term'] )
		     && $qv['term'] != 0
		) {
			$term            = get_term_by( 'id', $qv['term'], PluginConfig::$board_tax );
			$qv['tax_query'] = array(
				'relation' => 'AND',
				array(
					'taxonomy' => PluginConfig::$board_tax,
					'field'    => 'name',
					'terms'    => $term->slug
				)
			);
		}
	}

	public function get_posts() {
		do_action( 'idea_board_pre_get_posts', $this );

		$posts = parent::get_posts();

		do_action( 'idea_board_after_get_posts', $this );

		return $posts;
	}

	private function generateStartNo( $paged, $posts_per_page = 10 ) {
		$found_posts = $this->found_posts;

		$paged = $paged == 0 ? 1 : $paged;

		$number = ( $paged - 1 ) * $posts_per_page;

		return $found_posts - $number;
	}

	public static function get_single_post( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'board' => '',
			'p'     => ''
		) );

		$post = null;

		$query = new Query( $args );

		$GLOBALS['wp_query'] = $query;

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$post = get_post();
				break;
			}
		} else {
			$p     = new \stdClass();
			$p->ID = - 1;
			$post  = new \WP_Post( $p );
		}

		return $post;
	}
}
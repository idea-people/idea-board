<?php
/**
 * User: ideapeople
 * Mail: ideapeople@ideapeople.co.kr
 * Homepage : ideapeople@ideapeople.co.kr
 */

namespace ideapeople\util\wp;


use wpdb;

class WpQuerySearch {
	public $query_vars = array(
		'searchType',
		'searchValue'
	);

	public $search = array( 'type' => false, 'value' => false );

	public $searchType;
	public $searchValue;

	public $queries = array(
		'q' => array(),
		'p' => array()
	);

	public function register_query_var( $query_var ) {
		return wp_parse_args( $this->query_vars, $query_var );
	}

	public function where( $where ) {
		/* @var $wpdb wpdb */
		global $wpdb;

		$this->searchType  = $this->search[ 'type' ] ? $this->search[ 'type' ] : get_query_var( 'searchType' );
		$this->searchValue = $this->search[ 'value' ] ? $this->search[ 'value' ] : get_query_var( 'searchValue' );

		if ( ! empty( $this->searchType ) && ! empty( $this->searchValue ) ) {
			$query_like = $this->query_like( $this->searchValue );

			if ( $this->searchType == 'post_title' ) {
				$this->queries[ 'q' ][] = "AND {$wpdb->posts}.post_title like %s";
				$this->queries[ 'p' ][] = $query_like;
			}

			if ( $this->searchType == 'post_content' ) {
				$this->queries[ 'q' ][] = "AND {$wpdb->posts}.post_content like %s";
				$this->queries[ 'p' ][] = $query_like;
			}

			if ( $this->searchType == 'post_title_content' ) {
				$this->queries[ 'q' ][] = "AND ({$wpdb->posts}.post_content like %s OR {$wpdb->posts}.post_title like %s)";
				$this->queries[ 'p' ][] = $query_like;
				$this->queries[ 'p' ][] = $query_like;
			}
		}

		$query = join( ' ', $this->queries[ 'q' ] );

		if ( ! empty( $this->queries[ 'p' ] ) ) {
			$where .= $wpdb->prepare( $query, $this->queries[ 'p' ] );
		} else {
			return $where;
		}

		return $where;
	}

	public function query_like( $value ) {
		global $wpdb;

		return '%' . $wpdb->esc_like( $value ) . '%';
	}
}
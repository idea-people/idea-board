<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-09-17
 * Time: 오후 10:29
 */

namespace ideapeople\board;


class PostTable {
	public function get_columns() {
		$columns = array(
			'no'     => __idea_board( 'No' ),
			'title'  => __idea_board( 'Title' ),
			'date'   => __idea_board( 'Date' ),
			'author' => __idea_board( 'Author' ),
			'hit'    => __idea_board( 'Hit' )
		);

		return $columns;
	}

	public function get_column_value() {
		$columns = array_keys( $this->get_columns() );

		foreach ( $columns as $column ) {

		}
	}
}
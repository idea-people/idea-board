<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-29
 * Time: 오전 10:47
 */

namespace ideapeople\board\view;


use ideapeople\board\setting\Setting;
use ideapeople\util\view\PathView;

abstract class AbstractView extends PathView {
	public function __construct( array $args = array() ) {
		parent::__construct( $args );

		$args = wp_parse_args( $args, array(
			'board_term' => false
		) );

		if ( $args[ 'board_term' ] ) {
			$this->init( $args[ 'board_term' ] );
		} else {
			$this->init();
		}
	}

	public function init( $board_term = null ) {
		/**
		 * @var $board_term \WP_Term
		 */
		$board = Setting::get_board( $board_term );

		if ( is_object( $board ) ) {
			$path = Setting::get_skin_info( $board->term_id );
			$this->setViewPath( $path[ 'path' ] . '/' . $this->getViewName() . '.php' );
		}
	}

	public abstract function getViewName();
}
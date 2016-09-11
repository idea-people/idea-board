<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-09-11
 * Time: 오후 9:37
 */

namespace ideapeople\board;


class AjaxDieHandler {
	public function start_die_handler() {
		add_filter( 'wp_doing_ajax', array( $this, 'wp_doing_ajax' ) );
		add_filter( 'wp_die_ajax_handler', array( $this, 'die_handle' ) );
		add_filter( 'wp_die_handler', array( $this, 'die_handle' ) );
	}

	public function die_handle() {
		return array( $this, 'idea_board_die_handle' );
	}

	public function idea_board_die_handle( $message, $title, $args = array() ) {
		$args = wp_parse_args( $args, array(
			'back_link' => true
		) );

		_default_wp_die_handler( $message, $title, $args );
	}

	public function wp_doing_ajax() {
		return false;
	}

	public function end_die_handler() {
		remove_filter( 'wp_doing_ajax', array( $this, 'wp_doing_ajax' ) );
		remove_filter( 'wp_die_ajax_handler', array( $this, 'die_handle' ) );
		remove_filter( 'wp_die_handler', array( $this, 'die_handle' ) );
	}
}
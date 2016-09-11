<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-09-11
 * Time: 오후 9:37
 */

namespace ideapeople\board;


class AjaxDieHandler {
	public static function start_die_handler() {
		add_filter( 'wp_doing_ajax', array( 'ideapeople\board\AjaxDieHandler', 'wp_doing_ajax' ) );
		add_filter( 'wp_die_ajax_handler', array( 'ideapeople\board\AjaxDieHandler', 'die_handle' ) );
		add_filter( 'wp_die_handler', array( 'ideapeople\board\AjaxDieHandler', 'die_handle' ) );
	}

	public static function die_handle() {
		return array( 'ideapeople\board\AjaxDieHandler', 'idea_board_die_handle' );
	}

	public static function idea_board_die_handle( $message, $title, $args = array() ) {
		$args = wp_parse_args( $args, array(
			'back_link' => true
		) );

		_default_wp_die_handler( $message, $title, $args );
	}

	public static function wp_doing_ajax() {
		return false;
	}

	public static function end_die_handler() {
		remove_filter( 'wp_doing_ajax', array( 'ideapeople\board\AjaxDieHandler', 'wp_doing_ajax' ) );
		remove_filter( 'wp_die_ajax_handler', array( 'ideapeople\board\AjaxDieHandler', 'die_handle' ) );
		remove_filter( 'wp_die_handler', array( 'ideapeople\board\AjaxDieHandler', 'die_handle' ) );
	}
}
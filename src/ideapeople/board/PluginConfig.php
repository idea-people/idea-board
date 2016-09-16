<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-25
 * Time: 오후 2:16
 */

namespace ideapeople\board;

class PluginConfig {
	static $support_php_version = 5.3;
	static $support_wp_version = 4.4;

	static $plugin_name = 'IDEA_BOARD';
	static $plugin_author_email = 'ideapeople@ideapeople.co.kr';
	static $plugin_version = 1.0;
	static $plugin_url, $plugin_path;

	static $__FILE__;

	static $permalink_structure;

	public static function init( $__FILE__ ) {
		self::$__FILE__ = $__FILE__;

		self::$plugin_path = plugin_dir_path( $__FILE__ );
		self::$plugin_url  = plugin_dir_url( $__FILE__ );

		self::$permalink_structure = get_option( 'permalink_structure' );
	}

	static $board_tax = 'idea_board';
	static $board_post_type = 'idea_board_item';
	static $board_admin_role = 'IDEA_BOARD_ADMIN';
	static $board_ajax_edit_name = 'idea_board_edit_post';

	static $idea_board_edit_nonce_name = 'idea_board_edit_nonce';
	static $idea_board_edit_nonce_action = 'idea_board_edit';
}
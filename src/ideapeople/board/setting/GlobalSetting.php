<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-09-16
 * Time: 오후 2:37
 */

namespace ideapeople\board\setting;


use ideapeople\board\action\AdminGlobalAction;

class GlobalSetting {
	public static function get_max_update_file_size() {
		return AdminGlobalAction::instance()->setting->get_option( 'idea_board_max_update_file_size', 50 );
	}

	public static function get_file_mimes() {
		return AdminGlobalAction::instance()->setting->get_option( 'idea_board_file_mimes', wp_get_mime_types() );
	}
}
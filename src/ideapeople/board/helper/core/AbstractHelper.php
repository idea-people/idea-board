<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-09-08
 * Time: 오전 10:26
 */

namespace ideapeople\board\helper\core;


abstract class AbstractHelper implements Helper {
	public function is_activate() {
		return is_plugin_active( $this->get_name() );
	}

	public function get_helper_file() {
		$file = WP_CONTENT_DIR . '/plugins/' . $this->get_name();

		return $file;
	}

	public function get_plugin_data() {
		return get_plugin_data( $this->get_helper_file() );
	}
}
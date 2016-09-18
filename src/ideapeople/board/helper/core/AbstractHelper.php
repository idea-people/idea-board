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

	public function get_plugin_name() {
		return dirname( $this->get_name() );
	}

	public function get_plugin_url() {
		return 'https://wordpress.org/plugins/' . $this->get_plugin_name();
	}

	public function is_installed() {
		$data = $this->get_plugin_data();

		if ( empty( $data[ 'Name' ] ) ) {
			return false;
		}

		return true;
	}
}
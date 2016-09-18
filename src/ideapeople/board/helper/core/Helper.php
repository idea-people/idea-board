<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-30
 * Time: 오후 12:19
 */

namespace ideapeople\board\helper\core;


interface Helper {
	public function run();

	public function get_name();

	public function get_plugin_url();
}
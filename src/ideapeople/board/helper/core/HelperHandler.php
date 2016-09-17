<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-30
 * Time: 오전 11:51
 */

namespace ideapeople\board\helper\core;

class HelperHandler {
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'run' ), 200 );
	}

	/**
	 * @var $helpers AbstractHelper[]
	 */
	public $helpers = array();

	/**
	 * @var $helpers AbstractHelper[]
	 * @return AbstractHelper[]
	 */
	public function get_helpers() {
		return apply_filters( 'idea_board_get_helpers', $this->helpers );
	}

	public function add_helper( Helper $helper ) {
		$this->helpers[] = $helper;
	}

	public function run() {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		foreach ( $this->get_helpers() as $helper ) {
			if ( is_plugin_active( $helper->get_name() ) ) {
				$helper->run();
			}
		}
	}
}
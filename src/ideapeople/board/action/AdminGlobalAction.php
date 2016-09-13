<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-09-08
 * Time: 오전 9:43
 */

namespace ideapeople\board\action;


use ideapeople\board\PluginConfig;
use ideapeople\util\html\Form;
use ideapeople\util\view\PathView;
use ideapeople\util\wp\AdminSettingUtils;

class AdminGlobalAction {
	/**
	 * @return static
	 */
	public static function instance() {
		static $instance;

		if ( ! $instance ) {
			$instance = new static();
		}

		return $instance;
	}

	public $setting;

	public function __construct() {
		$this->setting = new AdminSettingUtils( 'idea_board_global', 'idea_board_global_option', 'idea_board_global_options' );

		add_action( 'admin_menu', array( $this, 'add_page' ) );
	}

	public function view_section_1() {
	}

	public function add_page() {
		add_settings_section(
			'section_1',
			'게시판 환경설정',
			array( $this, 'view_section_1' ),
			$this->setting->slug
		);

		$this->setting->add_field( array(
			'section'       => 'section_1',
			'name'          => 'test',
			'id'            => 'test',
			'label'         => 'test',
			'multiple'      => true,
			'type'          => 'select',
			'default_value' => array( 'a' => 'a', 'b' => 'b' )
		) );

		$this->setting->add_submenu_page(
			'edit.php?post_type=' . PluginConfig::$board_post_type,
			'설정',
			'설정',
			'idea_board_global_settings'
		);
	}
}
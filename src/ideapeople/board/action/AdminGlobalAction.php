<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-09-08
 * Time: 오전 9:43
 */

namespace ideapeople\board\action;


use ideapeople\board\PluginConfig;
use ideapeople\board\setting\GlobalSetting;
use ideapeople\util\common\Utils;
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

		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'add_page' ) );
	}

	public function admin_init() {
		add_settings_section(
			'section_1',
			'게시판 환경설정',
			array( $this, 'view_section_1' ),
			$this->setting->slug
		);

		$this->setting->add_field( array(
			'section' => 'section_1',
			'name'    => 'idea_board_max_update_file_size',
			'id'      => 'idea_board_max_update_file_size',
			'label'   => '업로드 가능한 파일 사이즈(MB)',
			'value'   => GlobalSetting::get_max_update_file_size(),
			'after'   => sprintf( 'MB <p>현재 설정상 최대 업로드 가능한 사이즈는 <strong>%s</strong> 입니다.</p>', Utils::bytes( wp_max_upload_size(), 0, '%01.2f %s' ) )
		) );

		$this->setting->add_field( array(
			'section'       => 'section_1',
			'name'          => 'idea_board_file_mimes',
			'id'            => 'idea_board_file_mimes',
			'label'         => '업로드 허용할 파일타입',
			'multiple'      => true,
			'type'          => 'select',
			'value'         => GlobalSetting::get_file_mimes(),
			'default_value' => wp_get_mime_types(),
			'cssClass'      => 'chosen-select'
		) );
	}

	public function view_section_1() {
	}

	public function add_page() {
		$this->setting->add_submenu_page(
			'edit.php?post_type=' . PluginConfig::$board_post_type,
			'설정',
			'설정',
			'idea_board_global_settings'
		);
	}
}
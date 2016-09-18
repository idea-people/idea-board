<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-09-18
 * Time: 오후 9:53
 */

namespace ideapeople\board;


class DashBoard {
	public $page;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 0 );
		add_filter( 'custom_menu_order', array( $this, 'menu_sort_change' ) );
	}

	function menu_sort_change( $menu_ord ) {
		global $submenu;

		$key = 'edit.php?post_type=' . PluginConfig::$board_post_type;

		$menu = $submenu[ $key ];

		$arr   = array();
		$arr[] = $menu[16];
		$arr[] = $menu[5];
		$arr[] = $menu[10];
		$arr[] = $menu[15];
		$arr[] = $menu[17];

		$submenu[ $key ] = $arr;

		return $menu_ord;
	}

	public function admin_menu() {
		$this->page = add_submenu_page(
			'edit.php?post_type=' . PluginConfig::$board_post_type,
			__( 'Dashboard' ),
			__( 'Dashboard' ),
			'manage_options',
			'idea_board_dash_board',
			array(
				$this,
				'dashboard_page'
			) );

		add_action( 'load-' . $this->page, array( $this, 'on_load_page' ) );
	}

	public function on_load_page() {
		wp_enqueue_script( 'common' );
		wp_enqueue_script( 'wp-lists' );
		wp_enqueue_script( 'postbox' );
		add_thickbox();
		wp_enqueue_script( 'plugin-install' );
		wp_enqueue_script( 'updates' );

		add_meta_box( 'idea_board_dashboard_today_posts', __idea_board( 'Today registered post' ), array(
			$this,
			'meta_box_view'
		), $this->page, 'core', 'core', 'today_posts' );

		add_meta_box( 'idea_board_dashboard_today_comments', __idea_board( 'Today registered comments' ), array(
			$this,
			'meta_box_view'
		), $this->page, 'normal', 'core', 'today_comments' );

		add_meta_box( 'idea_board_dashboard_available_plugins', __idea_board( 'Available plug-in connection' ), array(
			$this,
			'meta_box_view'
		), $this->page, 'side', 'core', 'available_plugins' );

		add_meta_box( 'idea_board_dashboard_system_info', __idea_board( 'System Info' ), array(
			$this,
			'meta_box_view'
		), $this->page, 'core', 'core', 'system_info' );

		add_meta_box( 'idea_board_dashboard_forum_list', __idea_board( 'Forum' ), array(
			$this,
			'meta_box_view'
		), $this->page, 'side', 'core', 'forum_list' );
	}

	public function meta_box_view( $null, $args ) {
		$view_file = $args['args'];

		$file = PluginConfig::$plugin_path . '/views/dashboard/meta_boxes/' . $view_file . '.php';

		require_once $file;
	}

	public function dashboard_page() {
		$file = PluginConfig::$plugin_path . '/views/dashboard/dashboard.php';

		require_once $file;
	}
}
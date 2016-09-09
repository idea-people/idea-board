<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-24
 * Time: 오후 8:22
 */

namespace ideapeople\board;


use ideapeople\board\setting\Setting;
use ideapeople\util\wp\RoleUtils;

class Capability {
	const ROLE_ALL = 'all';
	const ROLE_IS_LOGIN = 'isLogin';
	const ROLE_ADMIN = 'administrator';

	static $default_caps = array(
		'list'           => self::ROLE_ALL,
		'read'           => self::ROLE_ALL,
		'edit'           => self::ROLE_ALL,
		'delete'         => self::ROLE_ALL,
		'notice_edit'    => self::ROLE_ADMIN,
		'comment_edit'   => self::ROLE_ALL,
		'comment_status' => self::ROLE_ADMIN,
		'file_down'      => self::ROLE_ALL,
		'file_upload'    => self::ROLE_ALL,
		'reply_edit'     => self::ROLE_ALL,
		'secret_edit'    => self::ROLE_ADMIN,
		'secret_read'    => self::ROLE_ADMIN
	);

	public static function get_default_cap( $cap ) {
		if ( ! array_key_exists( $cap, self::$default_caps ) ) {
			return false;
		}

		return self::$default_caps[ $cap ];
	}

	public static function current_user_can( $cap, $board = null ) {
		$board = Setting::get_board( $board );

		$cap_roles = get_term_meta( $board->term_id, "role_{$cap}", true );
		$cap_roles = $cap_roles ? $cap_roles : array();

		if ( self::is_board_admin() ) {
			return true;
		}

		if ( in_array( 'all', $cap_roles ) ) {
			return true;
		}

		if ( is_user_logged_in() && in_array( 'is_login', $cap_roles ) ) {
			return true;
		}

		$user = wp_get_current_user();

		foreach ( $user->roles as $role ) {
			if ( in_array( $role, $cap_roles ) ) {
				return true;
			}
		}

		return false;
	}

	public static function is_board_admin() {
		if ( is_super_admin() ) {
			return true;
		}

		if ( RoleUtils::is_user_in_role( get_current_user_id(), PluginConfig::$board_admin_role ) ) {
			return true;
		}

		return false;
	}
}
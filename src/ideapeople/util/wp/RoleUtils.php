<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-25
 * Time: 오후 11:56
 */

namespace ideapeople\util\wp;


use ideapeople\board\CommonUtils;

class RoleUtils {

	public static function is_user_in_role( $user_id, $role ) {
		return in_array( $role, self::get_user_roles_by_user_id( $user_id ) );
	}

	public static function get_user_roles_by_user_id( $user_id ) {
		$user = get_userdata( $user_id );

		return empty( $user ) ? array() : $user->roles;
	}
}
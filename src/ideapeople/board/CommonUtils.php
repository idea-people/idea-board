<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-24
 * Time: 오후 10:54
 */

namespace ideapeople\board;


use ideapeople\board\setting\Setting;
use ideapeople\util\html\Form;
use ideapeople\util\wp\CustomField;
use ideapeople\util\wp\PostUtils;
use ideapeople\util\wp\RoleUtils;

class CommonUtils {
	public static function role_as_select_box( $name, $selected = null, $args = array() ) {
		$roles = new Roles();

		$args = wp_parse_args( $args, array(
			'multiple' => true,
			'class'    => "chosen-select"
		) );

		$options = array();

		foreach ( $roles->get_roles() as $role => $role_name ) {
			$options[ $role ] = $role_name;
		}

		return Form::select( $name, $options, $selected, $args );
	}

	public static function get_post_page() {
		/**
		 * @var $wp_the_query \WP_Query
		 */
		global $wp_the_query;

		if ( $wp_the_query->query_vars['page_id'] ) {
			return get_post( $wp_the_query->query_vars['page_id'] );
		} else if ( $wp_the_query->query_vars['pagename'] ) {
			return get_page_by_path( $wp_the_query->query_vars['pagename'] );
		}

		return false;
	}

	public static function get_post_page_id() {
		return self::get_post_page()->ID;
	}

	public static function get_post_page_link() {
		return get_permalink( self::get_post_page_id() );
	}
}
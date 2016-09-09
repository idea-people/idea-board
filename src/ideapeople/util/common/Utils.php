<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-24
 * Time: 오후 11:37
 */

namespace ideapeople\util\common;


class Utils {
	static function trim( $value, $defaultValue ) {
		if ( is_array( $value ) ) {
			if ( count( $value ) == 0 ) {
				return null;
			}

			foreach ( $value as &$v ) {
				if ( strlen( trim( $v ) ) == 0 ) {
					$v = $defaultValue;
				}
			}

			return $value;
		} else {
			if ( strlen( trim( $value ) ) == 0 ) {
				return $defaultValue;
			} else {
				return $value;
			}
		}
	}

	static function getVar( $target, $key, $defaultValue = null, $trim = false ) {
		if ( is_null( $target ) ) {
			return $defaultValue;
		}

		if ( is_object( $target ) ) {
			$target = (array) $target;
		}

		if ( $trim ) {
			return array_key_exists( $key, $target ) ? self::trim( $target [ $key ], $defaultValue ) :
				$defaultValue;
		} else {
			return array_key_exists( $key, $target ) ? $target [ $key ] : $defaultValue;
		}
	}

	static function clear_object( &$object ) {
		foreach ( $object as $key => &$value ) {
			$value = null;
		}
	}
}
<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-24
 * Time: 오후 11:37
 */

namespace ideapeople\util\common;


class Utils {
	static function to_bytes( $p_sFormatted ) {
		$aUnits = array(
			'B'  => 0,
			'KB' => 1,
			'MB' => 2,
			'GB' => 3,
			'TB' => 4,
			'PB' => 5,
			'EB' => 6,
			'ZB' => 7,
			'YB' => 8
		);
		$sUnit  = strtoupper( trim( substr( $p_sFormatted, - 2 ) ) );
		if ( intval( $sUnit ) !== 0 ) {
			$sUnit = 'B';
		}
		if ( ! in_array( $sUnit, array_keys( $aUnits ) ) ) {
			return false;
		}
		$iUnits = trim( substr( $p_sFormatted, 0, strlen( $p_sFormatted ) - 2 ) );
		if ( ! intval( $iUnits ) == $iUnits ) {
			return false;
		}

		return $iUnits * pow( 1024, $aUnits[ $sUnit ] );
	}

	public static function bytes( $bytes, $force_unit = null, $format = null, $si = true ) {
		$format = ( $format === null ) ? '%01.2f %s' : (string) $format;

		if ( $si == false OR strpos( $force_unit, 'i' ) !== false ) {
			$units = array( 'B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB' );
			$mod   = 1024;
		} // SI prefixes (decimal)
		else {
			$units = array( 'B', 'kB', 'MB', 'GB', 'TB', 'PB' );
			$mod   = 1000;
		}

		// Determine unit to use
		if ( ( $power = array_search( (string) $force_unit, $units ) ) === false ) {
			$power = ( $bytes > 0 ) ? floor( log( $bytes, $mod ) ) : 0;
		}

		return sprintf( $format, $bytes / pow( $mod, $power ), $units[ $power ] );
	}

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

	static function get_value( $target, $key, $defaultValue = null, $trim = false ) {
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
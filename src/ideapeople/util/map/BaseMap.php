<?php
/**
 * User: ideapeople
 * Mail: ideapeople@ideapeople.co.kr
 * Homepage : ideapeople@ideapeople.co.kr
 */

namespace ideapeople\util\map;

abstract class BaseMap implements Map {
	/**
	 * @var array|mixed
	 */
	public $container;

	public function __construct( $container = array() ) {
		$this->container = $this->create_container();
	}

	function size() {
		return count( $this->container );
	}

	function isEmpty() {
		return empty( $this->container );
	}

	function containsKey( $key ) {
		return array_key_exists( $key, $this->container );
	}

	function containsValue( $value ) {
		return in_array( $value, $this->container );
	}

	function put( $key, $value ) {
		$this->container[ $key ] = $value;
	}

	function remove( $key ) {
		unset( $this->container[ $key ] );
	}

	/**
	 * @param $map Map
	 */
	function putAll( $map ) {
		if ( $map instanceof Map ) {
			foreach ( $map->values() as $key => $value ) {
				$this->put( $key, $value );
			}
		} else if ( is_array( $map ) ) {
			foreach ( $map as $key => $value ) {
				$this->put( $key, $value );
			}
		}

	}

	function clear() {
		foreach ( $this->values() as $key => $value ) {
			unset( $this->container[ $key ] );
		}
	}

	function values() {
		return array_values( $this->container );
	}

	function get( $key ) {
		if ( $this->containsKey( $key ) ) {
			return $this->container[ $key ];
		}

		return null;
	}

	function keys() {
		return array_keys( $this->container );
	}

	public abstract function create_container();
}
<?php
/**
 * User: ideapeople
 * Mail: ideapeople@ideapeople.co.kr
 * Homepage : ideapeople@ideapeople.co.kr
 */

namespace ideapeople\util\map;


interface Map {
	function size();

	function isEmpty();

	function containsKey( $key );

	function containsValue( $key );

	function put( $key, $value );

	function remove( $key );

	function putAll( $map );

	function clear();

	function values();

	function get( $key );

	function keys();
}
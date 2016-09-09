<?php
/**
 * User: ideapeople
 * Mail: ideapeople@ideapeople.co.kr
 * Homepage : ideapeople@ideapeople.co.kr
 */

namespace ideapeople\util\view;

use ideapeople\util\map\ArrayMap;

class ModelMap extends ArrayMap {
	public function addAttribute( $name, $data ) {
		$this->put( $name, $data );
	}

	public function removeAttribute( $name ) {
		$this->remove( $name );
	}

	public function getAttribute( $name, $default = null ) {
		if ( $this->containsKey( $name ) ) {
			return $this->get( $name );
		}

		return $default;
	}

	public function getAttributes() {
		return $this->container;
	}

	public function hasAttribute( $name ) {
		return $this->containsKey( $name );
	}

	public function currentState() {
		foreach ( $this->values() as $attribute ) {
			var_dump( $attribute );
		}
	}

	public function getCount() {
		return $this->size();
	}

	public function addAttributes( $collection ) {
		$this->putAll( $collection );
	}
}
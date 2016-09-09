<?php
/**
 * User: ideapeople
 * Mail: ideapeople@ideapeople.co.kr
 * Homepage : ideapeople@ideapeople.co.kr
 */

namespace ideapeople\util\view;


abstract class AbstractView implements View {
	/**
	 * @var ModelMap
	 */
	public $model;

	public function __construct( $args = array() ) {
		$this->model = new ModelMap();
	}

	public function getAttribute( $name, $default = null ) {
		return $this->model->getAttribute( $name, $default );
	}

	public function addAttribute( $name, $value ) {
		$this->model->addAttribute( $name, $value );
	}

	/**
	 * @param $model ModelMap
	 *
	 */
	public function render( $model = null ) {
		$this->mergeAttributes( $model );
	}

	public function mergeAttributes( $model ) {
		if ( is_array( $model ) ) {
			$this->model->addAttributes( $model );
		} else if ( $model instanceof ModelMap ) {
			$this->model->putAll( $model );
		}
	}
}
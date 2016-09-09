<?php
/**
 * User: ideapeople
 * Mail: ideapeople@ideapeople.co.kr
 * Homepage : ideapeople@ideapeople.co.kr
 */

namespace ideapeople\util\view;


use ideapeople\util\map\Map;

class PathView extends AbstractView {
	public $viewPath;

	/**
	 * @var ModelMap
	 */
	public $model;

	public function __construct( $args = array() ) {
		parent::__construct( $args );

		if ( is_string( $args ) ) {
			$this->viewPath = $args;
		}
	}

	public function hasView() {
		return is_file( $this->viewPath );
	}

	/**
	 * @return string
	 * @internal param ModelMap $model
	 *
	 */
	public function readView() {
		if ( ! is_file( $this->getViewPath() ) ) {
			error_log( sprintf( "file not found : %s", $this->viewPath ) );

			return false;
		}

		ob_start();
		extract( $this->model->container );

		require $this->getViewPath();

		$output = ob_get_contents();

		ob_end_clean();

		return $output;
	}

	/**
	 * @param $model ModelMap
	 *
	 * @return mixed
	 */
	public function render( $model = null ) {
		parent::render( $model );

		return $this->readView();
	}

	/**
	 * @return string
	 */
	public function getViewPath() {
		return $this->viewPath;
	}

	/**
	 * @param string $viewPath
	 */
	public function setViewPath( $viewPath ) {
		$this->viewPath = $viewPath;
	}
}
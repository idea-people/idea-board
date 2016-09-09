<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-25
 * Time: 오전 12:48
 */

namespace ideapeople\util\view;


class AjaxView extends AbstractView {
	/**
	 * @param $model ModelMap
	 *
	 * @return mixed
	 */
	public function render( $model = null ) {
		parent::render( $model );

		wp_send_json( $this->model->container );
	}
}
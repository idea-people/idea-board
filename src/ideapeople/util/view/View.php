<?php
/**
 * User: ideapeople
 * Mail: ideapeople@ideapeople.co.kr
 * Homepage : ideapeople@ideapeople.co.kr
 */

namespace ideapeople\util\view;

interface View {
	/**
	 * @param $model ModelMap
	 *
	 */
	public function render( $model = null );
}
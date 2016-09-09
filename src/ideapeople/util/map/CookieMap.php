<?php
/**
 * User: ideapeople
 * Mail: ideapeople@ideapeople.co.kr
 * Homepage : ideapeople@ideapeople.co.kr
 */

namespace ideapeople\util\map;


class CookieMap extends BaseMap {
	public function create_container() {
		return $_COOKIE;
	}
}
<?php
/**
 * User: ideapeople
 * Mail: ideapeople@ideapeople.co.kr
 * Homepage : ideapeople@ideapeople.co.kr
 */

namespace ideapeople\board;


use ideapeople\board\setting\Setting;
use ideapeople\board\view\EditView;
use ideapeople\board\view\ListView;
use ideapeople\board\view\SingleView;
use ideapeople\util\view\PathView;

class ShortCode {
	public function short_code( $atts, $content ) {
		$atts = wp_parse_args( $atts, array(
			'name'      => '',
			'page_mode' => ''
		) );

		$page_mode = $atts['page_mode'] ? $atts['page_mode'] : get_query_var( 'page_mode' );

		$board = Setting::get_board( $atts['name'] );

		$output = PostView::get_view( $board, $page_mode );

		return $output . $content;
	}
}
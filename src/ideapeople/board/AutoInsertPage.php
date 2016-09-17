<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-09-17
 * Time: 오후 5:36
 */

namespace ideapeople\board;


use ideapeople\board\setting\Setting;

class AutoInsertPage {
	function auto_insert( $content ) {
		global $post;

		if ( $post->post_type == PluginConfig::$board_post_type ) {
			return $content;
		}

		$boards = Setting::get_boards();

		foreach ( $boards as $board ) {
			$use_pages = Setting::get_use_pages( $board );

			if ( in_array( $post->ID, $use_pages ) ) {
				return $content . PostView::get_view( $board->term_id, get_query_var( 'page_mode' ) );
			}
		}

		return $content;
	}
}
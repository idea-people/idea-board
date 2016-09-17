<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-09-17
 * Time: 오후 5:38
 */

namespace ideapeople\board;


class PostSort {
	/**
	 * 공지사항 정렬 기능 추가
	 *
	 * @param $queries
	 *
	 * @return array
	 */
	function sort_notice( $queries ) {
		global $wpdb;

		$queries[] = "CONVERT((SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key = 'idea_board_is_notice' AND post_id = wp_posts.ID),DECIMAL)";

		return $queries;
	}
}
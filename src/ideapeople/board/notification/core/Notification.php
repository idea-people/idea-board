<?php
/**
 * User: ideapeople
 * Mail: ideapeople@ideapeople.co.kr
 * Homepage : ideapeople@ideapeople.co.kr
 */

namespace ideapeople\board\notification\core;


interface Notification {
	/**
	 * 글이 등록되었을 때
	 *
	 * @param $post_data array
	 * @param $post_id int
	 * @param $board \WP_Term
	 *
	 * @return mixed
	 */
	function when_post_registered( $post_data, $post_id, $board );

	/**
	 * 글이 수정 되었을 때
	 *
	 * @param $post_data array
	 * @param $post_id int
	 * @param $board \WP_Term
	 *
	 * @return mixed
	 */
	function when_post_updated( $post_data, $post_id, $board );

	/**
	 * 글이 삭제 되었을 때
	 *
	 * @param $post_data array
	 * @param $post_id int
	 * @param $board \WP_Term
	 *
	 * @return mixed
	 */
	function when_post_deleted( $post_data, $post_id, $board );

	/**
	 * 글에 댓글이 등록되었을 때
	 *
	 * @param $comment_data array
	 * @param $comment_id int
	 * @param $board \WP_Term
	 * @param $mode
	 *
	 * @return mixed
	 */
	function when_post_comment_registered( $comment_data, $comment_id, $board, $mode );

	/**
	 * 글에 댓글이 수정되었을 때
	 *
	 * @param $comment_data array
	 * @param $comment_id int
	 * @param $board \WP_Term
	 * @param $mode
	 *
	 * @return mixed
	 */
	function when_post_comment_updated( $comment_data, $comment_id, $board, $mode );

	/**
	 * 답글이 등록되었을 때
	 *
	 * @param $post_data array
	 * @param $post_id int
	 * @param $board \WP_Term
	 *
	 * @return mixed
	 */
	function when_post_reply_registered( $post_data, $post_id, $board );

	/**
	 * 댓글에 댓글이 등록되었을때
	 *
	 * @param $comment_data array
	 * @param $comment_id int
	 * @param $board \WP_Term
	 * @param $mode
	 *
	 * @return mixed
	 */
	function when_comment_comment_registered( $comment_data, $comment_id, $board, $mode );
}
<?php
/**
 * User: ideapeople
 * Mail: ideapeople@ideapeople.co.kr
 * Homepage : ideapeople@ideapeople.co.kr
 */

namespace ideapeople\board\notification\core;


class NotificationHandler {
	/**
	 * @var Notification[]
	 */
	public $notifications = array();

	/**
	 * @return mixed|void|Notification[]
	 */
	public function get_notifications() {
		return apply_filters( 'idea_board_get_notifications', $this->notifications );
	}

	public function handle_comment_edited( $comment_data, $comment_id, $board, $mode ) {
		$is_comment_comment = $comment_data['comment_parent'];

		foreach ( $this->get_notifications() as $notification ) {
			if ( $is_comment_comment ) {
				$notification->when_comment_comment_registered( $comment_data, $comment_id, $board, $mode );
			} else {
				if ( $mode == 'insert' ) {
					$notification->when_post_comment_registered( $comment_data, $comment_id, $board, $mode );
				} else if ( $mode == 'update' ) {
					$notification->when_post_comment_updated( $comment_data, $comment_id, $board, $mode );
				}
			}
		}
	}

	public function handle_post_edited( $post_data, $post_id, $board, $mode ) {
		$is_reply = $post_data['post_parent'];

		foreach ( $this->get_notifications() as $notification ) {
			if ( $is_reply ) {
				$notification->when_post_reply_registered( $post_data, $post_id, $board );
			} else {
				if ( $mode == 'insert' ) {
					$notification->when_post_registered( $post_data, $post_id, $board );
				} else if ( $mode == 'update' ) {
					$notification->when_post_updated( $post_data, $post_id, $board );
				} else if ( $mode == 'delete' ) {
					$notification->when_post_deleted( $post_data, $post_id, $board );
				}
			}
		}
	}
}
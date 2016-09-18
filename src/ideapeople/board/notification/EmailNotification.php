<?php
/**
 * User: ideapeople
 * Mail: ideapeople@ideapeople.co.kr
 * Homepage : ideapeople@ideapeople.co.kr
 */

namespace ideapeople\board\notification;


use ideapeople\board\notification\core\Notification;
use ideapeople\board\Post;

class EmailNotification implements Notification {
	function notification_admin() {
	}

	function when_post_registered( $post_data, $post_id, $board ) {
	}

	function when_post_updated( $post_data, $post_id, $board ) {
	}

	function when_post_deleted( $post_data, $post_id, $board ) {
	}

	function when_post_comment_updated( $comment_data, $comment_id, $board, $mode ) {
	}

	public function wp_mail( $user_email, $title, $message ) {
		add_filter( 'wp_mail_content_type', create_function( '', 'return "text/html"; ' ) );

		$result = wp_mail(
			$user_email,
			wp_strip_all_tags( $title ),
			$message,
			array( 'content-type' => 'text/html' )
		);

		remove_filter( 'wp_mail_content_type', create_function( '', 'return "text/html"; ' ) );

		return $result;
	}

	function when_post_comment_registered( $comment_data, $comment_id, $board, $mode ) {
		$comment = get_comment( $comment_id );

		$post = Post::get_post( $comment->comment_post_ID );

		$post_id = $post->ID;

		$user_email = $comment->comment_author_email;

		$this->wp_mail(
			$user_email,
			sprintf( __idea_board( '%s Your comment has been registered in the article' ), Post::get_the_title( $post_id ) ),
			sprintf( '<a href="%s">Read Post</a>', Post::get_the_permalink( $post_id ) )
		);
	}

	function when_post_reply_registered( $post_data, $post_id, $board ) {
		$user_email = Post::get_user_email( $post_id );
		$this->wp_mail(
			$user_email,
			sprintf( __idea_board( 'The replies were registered on %s posts' ), Post::get_the_title( $post_id ) ),
			sprintf( '<a href="%s">Read Post</a>', Post::get_the_permalink( $post_id ) )
		);
	}

	function when_comment_comment_registered( $comment_data, $comment_id, $board, $mode ) {
		$comment = get_comment( $comment_id );
		$parent  = get_comment( $comment->comment_parent );

		$post = Post::get_post( $comment->comment_post_ID );

		$post_id = $post->ID;

		$user_email = $parent->comment_author_email;

		$this->wp_mail(
			$user_email,
			sprintf( __idea_board( 'Your comment has been registered to comment' ), $comment->comment_content ),
			sprintf( '<a href="%s">Read Post</a>', Post::get_the_permalink( $post_id ) )
		);
	}
}
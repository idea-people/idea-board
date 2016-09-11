<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-09-11
 * Time: 오후 2:08
 */

namespace ideapeople\board\view;


use ideapeople\board\Comment;
use ideapeople\board\Query;
use ideapeople\board\Rewrite;
use ideapeople\board\setting\Setting;
use ideapeople\util\view\View;

class CommentView extends AbstractView {
	public function getViewName() {
		return 'comment_edit';
	}

	public function render( $model = null ) {
		$post = Query::get_single_post( array(
			'board' => Setting::get_board()->name,
			'p'     => get_query_var( 'pid', 1 )
		) );

		add_filter( 'wp_get_current_commenter', array( $this, 'wp_get_current_commenter' ) );

		$comment_ID = get_query_var( 'comment_ID' );

		$view = apply_filters( 'pre_cap_check_comment_view', null, $comment_ID, $post->ID );

		$edit_mode = get_query_var( 'edit_mode' );

		if ( $edit_mode == 'delete' ) {
			if ( $view instanceof View ) {
				$output = $view->render( $this->model );
			} else if ( is_string( $view ) && ! empty( $view ) ) {
				$output = $view;
			} else {
				$output = '<script>location.href="' . Rewrite::delete_comment_link( $comment_ID ) . '"</script>';
			}
		} else {
			if ( $view instanceof View ) {
				$output = $view->render( $this->model );
			} else {
				$output = parent::render( $model );
			}
		}

		wp_reset_query();

		remove_filter( 'wp_get_current_commenter', array( $this, 'wp_get_current_commenter' ) );

		return $output;
	}

	function wp_get_current_commenter( $commenter = array() ) {
		$comment_ID = get_query_var( 'comment_ID' );
		$comment    = get_comment( $comment_ID );

		$commenter[ 'comment_author' ]       = $comment->comment_author;
		$commenter[ 'comment_author_email' ] = $comment->comment_author_email;
		$commenter[ 'comment_author_url' ]   = $comment->comment_author_url;

		return $commenter;
	}
}
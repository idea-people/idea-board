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

		$post->comment_status = 'close';

		$comment_ID = get_query_var( 'comment_ID' );
		
		$view = apply_filters( 'pre_cap_check_comment_view', null, $comment_ID, $post->ID );

		if ( $view instanceof View ) {
			$output = $view->render( $this->model );
		} else if ( is_string( $view ) && ! empty( $view ) ) {
			$output = $view;
		} else {
			$output = parent::render( $model );
		}

		wp_reset_query();

		return $output;
	}
}
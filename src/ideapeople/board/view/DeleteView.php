<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-09-09
 * Time: 오후 8:44
 */

namespace ideapeople\board\view;


use ideapeople\board\Post;
use ideapeople\board\Query;
use ideapeople\board\setting\Setting;
use ideapeople\util\view\View;

class DeleteView extends AbstractView {
	public function getViewName() {
		return 'delete';
	}

	public function render( $model = null ) {
		$post = null;

		$query = new Query( array(
			'board' => Setting::get_board()->name,
			'p'     => get_query_var( 'pid', 1 )
		) );

		$GLOBALS[ 'wp_query' ] = $query;

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$post = get_post();
				break;
			}
		} else {
			$p     = new \stdClass();
			$p->ID = - 1;
			$post  = new \WP_Post( $p );
		}

		$post->comment_status = 'close';

		$view = apply_filters( 'pre_cap_check_edit_view', null, $post, Setting::get_board() );

		if ( $view instanceof View ) {
			$output = $view->render( $this->model );
		} else {
			$output = parent::render( $model );
		}

		return $output;
	}
}
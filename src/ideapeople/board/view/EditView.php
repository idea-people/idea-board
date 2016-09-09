<?php
/**
 * User: ideapeople
 * Mail: ideapeople@ideapeople.co.kr
 * Homepage : ideapeople@ideapeople.co.kr
 */

namespace ideapeople\board\view;


use ideapeople\board\Query;
use ideapeople\board\setting\Setting;
use ideapeople\util\view\View;

class EditView extends AbstractView {
	public function render( $model = null ) {
		wp_enqueue_script( 'jquery-form' );

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

		$view = apply_filters( 'pre_cap_check_edit_view', null, $post, Setting::get_board() );

		if ( $view instanceof View ) {
			$output = $view->render( $this->model );
		} else {
			$output = parent::render( $model );
		}

		wp_reset_query();

		return $output;
	}

	public function getViewName() {
		return 'edit';
	}
}
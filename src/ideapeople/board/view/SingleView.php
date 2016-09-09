<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-24
 * Time: 오후 4:48
 */

namespace ideapeople\board\view;


use ideapeople\board\setting\Setting;
use ideapeople\board\Post;
use ideapeople\board\Query;
use ideapeople\board\Skins;
use ideapeople\board\CommonUtils;
use ideapeople\board\Rewrite;
use ideapeople\util\view\PathView;
use ideapeople\util\view\View;
use ideapeople\util\wp\TermUtils;

class SingleView extends AbstractView {
	public function render( $model = null ) {
		wp_enqueue_script( 'jquery-validate' );
		wp_enqueue_script( 'jquery-validate-ko' );

		$post = null;

		$query = new Query( array(
			'board' => Setting::get_board()->name,
			'p'     => get_query_var( 'pid' )
		) );

		$GLOBALS[ 'wp_query' ] = $query;

		$this->addAttribute( 'query', $query );

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$post = get_post();
				break;
			}
		}

		$view = apply_filters_ref_array( 'pre_cap_check_read_view', array( null, $post ) );

		if ( $view instanceof View ) {
			$output = $view->render( $this->model );
		} else {
			Post::update_read_cnt();

			$output = parent::render( $model );

			$output .= $this->comments_template( Setting::get_board_id() );

			ob_start();

			do_action( 'idea_board_read', $post, $this );

			$output .= ob_get_contents();

			ob_end_clean();
		}

		wp_reset_query();

		return $output;
	}

	public function comments_template( $term_id ) {
		ob_start();

		if ( Setting::get_use_comment( $term_id ) ) {
			if ( Setting::get_use_comment_skin( $term_id ) ) {
				//comments_open
				add_filter( 'comments_open', array( $this, 'comments_open' ), 999 );
				add_filter( 'comments_template', array( $this, '_comments_template' ) );
				comments_template();
				remove_filter( 'comments_template', array( $this, '_comments_template' ) );
				remove_filter( 'comments_open', array( $this, 'comments_open' ), 999 );
			} else {
				comments_template();
			}
		}

		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}

	public function comments_open( $open ) {
		return Post::get_post()->comment_status;
	}

	public function _comments_template( $template ) {
		$skin_path = Setting::get_board_skin_path();

		return $skin_path . '/comments.php';
	}

	public function getViewName() {
		return 'single';
	}
}
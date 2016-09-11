<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-09-04
 * Time: 오후 3:56
 */

namespace ideapeople\board;


use ideapeople\board\setting\Setting;
use ideapeople\board\view\CommentView;
use ideapeople\board\view\DeleteView;
use ideapeople\board\view\EditView;
use ideapeople\board\view\ErrorView;
use ideapeople\board\view\ListView;
use ideapeople\board\view\SingleView;
use ideapeople\util\view\PathView;
use ideapeople\util\view\View;

class PostView {
	static $view_class = array();

	public static function get_view_class() {
		$view_class = apply_filters( 'idea_board_get_view_class', self::$view_class );

		return join( ' ', $view_class );
	}

	public static function get_view( $board, $page_mode = 'list' ) {
		$view = null;

		$skin_path = Setting::get_board_skin_path( $board );
		$header    = new PathView( $skin_path . '/header.php' );
		$footer    = new PathView( $skin_path . '/footer.php' );

		switch ( $page_mode ) {
			case 'edit':
				$view = new EditView();
				break;
			case 'read':
				$view = new SingleView();
				break;
			case 'comment_edit':
				$view = new CommentView();
				break;
			default:
				$view = new ListView();
				break;
		}

		self::$view_class[] = $view->getViewName();

		$output = '';

		$output .= $header->render();

		$output .= $view->render();

		$output .= $footer->render();

		return $output;
	}

	public static function handle_view( $view ) {
		$skin_path = Setting::get_board_skin_path();

		$header = new PathView( $skin_path . '/header.php' );
		$footer = new PathView( $skin_path . '/footer.php' );

		wp_enqueue_style( 'theme-style', get_template_directory_uri() . '/style.css' );
		wp_enqueue_style( 'theme-child-style', get_stylesheet_directory_uri() . '/style.css' );

		if ( $view instanceof View ) {
			ob_start();
			get_header();
			echo $header->render() . $view->render() . $footer->render();
			get_footer();
			$out = ob_get_contents();
			ob_end_clean();
			wp_die( $out );
		} else if ( $view instanceof \WP_Error ) {
			ob_start();
			get_header();
			$errorView = new ErrorView();
			$errorView->addAttribute( 'error', $view );
			echo $header->render() . $errorView->render() . $footer->render();
			get_footer();
			$out = ob_get_contents();
			ob_end_clean();
			wp_die( $out );
		} else if ( is_string( $view ) && ! empty( $view ) ) {
			wp_die( $view );
		} else if ( is_array( $view ) || is_object( $view ) ) {
			wp_send_json( $view );
		}
	}
}
<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-09-07
 * Time: 오후 12:12
 */

namespace ideapeople\board;

use ideapeople\board\setting\Setting;
use ideapeople\util\wp\WpNoprivUploader;

class Editor {
	public static function get_editor( $name ) {
		$editors = self::get_editors();

		if ( in_array( $name, array_keys( $editors ) ) ) {
			return $editors[ $name ];
		}

		return false;
	}

	public static function get_editors( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'wp_editor' => array(
				'name'     => __( 'Wordpress Editor' ),
				'callback' => array( 'ideapeople\board\Editor', 'wp_editor' ),
				'args'     => array()
			),
			'text_area' => array(
				'name'     => __( 'TEXT AREA' ),
				'callback' => array( 'ideapeople\board\Editor', 'text_area' ),
				'args'     => array()
			)
		) );

		return apply_filters( 'idea_board_get_editors', $args );
	}

	public static function text_area( $args ) { ?>
		<label for="<?php echo $args[ 'name' ] ?>" class="idea-board-hide"></label>
		<textarea name="<?php echo $args[ 'name' ] ?>"
		          id="<?php echo $args[ 'name' ] ?>"
		          class="<?php echo $args[ 'name' ] ?>"
		          rows="10"><?php echo $args[ 'content' ] ?></textarea>
		<?php
	}

	public static function wp_editor( $args ) {
		if ( ! is_user_logged_in() ) {
			/**
			 * @var $idea_board_nopriv_uploader WpNoprivUploader;
			 */
			global $idea_board_nopriv_uploader;

			$idea_board_nopriv_uploader->upload_button();
		}

		wp_editor( $args[ 'content' ], $args[ 'name' ], array() );
	}

	public static function get_the_editor( $args = array(), $board = null ) {
		$args = wp_parse_args( $args, array(
			'name'    => '',
			'content' => ''
		) );

		$board_editor = Setting::get_editor( $board );

		$editor = self::get_editor( $board_editor );

		if ( isset( $editor[ 'args' ] ) && ! empty( $editor[ 'args' ] ) ) {
			$args = wp_parse_args( $editor[ 'args' ], $args );
		}

		ob_start();

		call_user_func( $editor[ 'callback' ], $args );
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}
}
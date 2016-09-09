<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-09-07
 * Time: 오후 10:14
 */

namespace ideapeople\board\helper;


use ideapeople\board\helper\core\AbstractHelper;
use WP_Error;

class BwsCaptchaHelper extends AbstractHelper {
	public function run() {
		add_filter( 'cptch_add_form', array( $this, 'cptch_add_form' ) );
		add_filter( 'pre_cap_check_edit_view', array( $this, 'my_form_check' ) );
		add_action( 'idea_board_edit_end', array( $this, 'my_form' ) );
	}

	public function cptch_add_form( $forms ) {
		$forms = array_merge( $forms, array(
			'idea_board_post' => 'idea_board_post'
		) );

		return $forms;
	}

	function my_form() {
		global $cptch_options, $cptch_ip_in_whitelist;
		if ( ! $cptch_ip_in_whitelist ) {
			if ( "" == session_id() ) {
				@session_start();
			}
			if ( isset( $_SESSION[ "cptch_idea_board_post" ] ) ) {
				unset( $_SESSION[ "cptch_idea_board_post" ] );
			}
		}
		?>
		<tr>
			<th>capthca</th>
			<td>
				<?php echo cptch_display_captcha_custom( 'idea_board_post', 'cptch_idea_board_post' ); ?>
			</td>
		</tr>
		<?php
	}

	function my_form_check() {
		if ( ! isset( $_REQUEST[ 'cptch_form' ] ) || ! $_REQUEST[ 'cptch_form' ] ) {
			return null;
		}

		cptch_check_custom_form();

		global $cptch_options;

		$error = new WP_Error();

		$str_key = $cptch_options[ 'str_key' ][ 'key' ];

		if ( cptch_limit_exhausted() ) {
			if ( ! is_wp_error( $error ) ) {
				$error = new WP_Error();
			}
			$error->add( 'captcha_error', __( 'ERROR', 'captcha' ) . ':&nbsp;' . $cptch_options[ 'time_limit_off' ] );
		} elseif ( isset( $_REQUEST[ 'cptch_number' ] ) && "" == $_REQUEST[ 'cptch_number' ] ) {
			if ( ! is_wp_error( $error ) ) {
				$error = new WP_Error();
			}
			$error->add( 'captcha_error', __( 'ERROR', 'captcha' ) . ':&nbsp;' . $cptch_options[ 'no_answer' ] );
		} elseif (
			! isset( $_REQUEST[ 'cptch_result' ] ) ||
			! isset( $_REQUEST[ 'cptch_number' ] ) ||
			! isset( $_REQUEST[ 'cptch_time' ] ) ||
			0 !== strcasecmp( trim( cptch_decode( $_REQUEST[ 'cptch_result' ], $str_key, $_REQUEST[ 'cptch_time' ] ) ), $_REQUEST[ 'cptch_number' ] )
		) {
			if ( ! is_wp_error( $error ) ) {
				$error = new WP_Error();
			}
			$error->add( 'captcha_error', __( 'ERROR', 'captcha' ) . ':&nbsp;' . $cptch_options[ 'wrong_answer' ] );
		}

		if ( $error->errors ) {
			return $error;
		} else {
			return null;
		}
	}

	public function get_name() {
		return 'captcha/captcha.php';
	}
}
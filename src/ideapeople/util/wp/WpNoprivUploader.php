<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-09-05
 * Time: 오후 12:33
 */

namespace ideapeople\util\wp;


use ideapeople\util\http\Request;
use WP_Error;

class WpNoprivUploader {
	public $upload_action;

	public $file_param;

	public $plugin_url;

	public $ajax_url;

	public function __construct( $upload_action, $file_param, $plugin_url ) {
		$this->upload_action = $upload_action;
		$this->file_param    = $file_param;
		$this->plugin_url    = $plugin_url;
		$this->ajax_url      = admin_url( '/admin-ajax.php?action=' ) . $this->upload_action;
	}


	public function ajax_action() {
		add_action( 'wp_ajax_' . $this->upload_action, array( $this, 'action_upload_media' ) );
		add_action( 'wp_ajax_nopriv_' . $this->upload_action, array( $this, 'action_upload_media' ) );
	}

	public function upload_button() {
		wp_register_script( 'idea-nopriv-upload', $this->plugin_url . '/src/ideapeople/util/wp/WpNoprivUploader.js', array(
			'jquery'
		) );
		wp_enqueue_script( 'idea-nopriv-upload' );
		wp_enqueue_style( 'idea-nopriv-upload', $this->plugin_url . '/src/ideapeople/util/wp/WpNoprivUploader.css' );
		?>
		<div class="idea-nopriv-upload-btn-wrap">
			<span class="idea-nopriv-upload-btn"><?php _e( 'Add Media' ) ?></span>
			<input style="display: none;" type="file"
			       name="<?php echo $this->file_param ?>"
			       multiple="multiple"
			       data-file_param="<?php echo $this->file_param ?>"
			       data-ajax_url="<?php echo admin_url( '/admin-ajax.php?action=' ) . $this->upload_action ?>"/>
			<img src="<?php echo WP_CONTENT_URL ?>/../wp-includes/images/spinner.gif" alt=""
			     style="position: relative;top:2px;display: none;" class="idea-nopriv-upload-progress">
		</div>
		<?php
	}

	public function action_upload_media() {
		$files = Request::getFiles( $this->file_param );

		$error = new WP_Error();

		foreach ( $files as $file ) {
			$n = apply_filters( 'sanitize_file_name', $file[ 'tmp_name' ] );

			if ( ! file_is_valid_image( $n ) ) {
				$error->add( 'is_not_image', 'Only images can be uploaded.' );
				$error->add( 'image_name', $n );
			}
		}

		if ( $error->get_error_code() ) {
			wp_send_json_error( $error );
		}

		$error = apply_filters( 'idea_nopriv_upload_pre', $files );

		if ( is_wp_error( $error ) ) {
			wp_send_json_error( $error );
		}

		$error = apply_filters( 'idea_nopriv_upload_pre_' . $this->upload_action, $files );

		if ( is_wp_error( $error ) ) {
			wp_send_json_error( $error );
		}

		$results = array();

		$upload_dir = wp_upload_dir();
		$save_path  = $upload_dir[ 'path' ];
		$save_url   = $upload_dir[ 'url' ];

		foreach ( $files as $file ) {
			$result_file = FileUtils::upload_file_real( $save_path, $save_url, $file );

			if ( ! $result_file[ 'result' ] ) {
				continue;
			}

			$results[] = FileUtils::insert_attachment( $file[ 'name' ], $result_file[ 'url' ], $result_file[ 'type' ], $result_file[ 'file' ], $this->upload_action );
		}

		$attachments = array();

		foreach ( $results as $attachment_id ) {
			$attachments[] = get_post( $attachment_id );
		}

		wp_send_json( array( 'success' => true, 'result' => $results, 'attachments' => $attachments ) );
	}
}
<?php
/**
 * User: ideapeople
 * Mail: ideapeople@ideapeople.co.kr
 * Homepage : ideapeople@ideapeople.co.kr
 */

namespace ideapeople\board\action;


use ideapeople\board\setting\Setting;
use ideapeople\board\PluginConfig;
use ideapeople\util\view\PathView;
use ideapeople\util\wp\MetaUtils;

class AdminAction {
	public function validate_edit_term( $term ) {
		return $term;
	}

	public function delete_term( $term_id ) {
		$meta_keys = Setting::get_meta_keys();

		foreach ( $meta_keys as $meta_key ) {
			delete_term_meta( $term_id, $meta_key );
		}
	}

	public function created_term( $term_id ) {
		foreach ( $_POST[ 'roles' ] as $name => $value ) {
			$meta_key       = 'role_' . $name;
			$new_meta_value = $value ? $value : '';

			$this->update_meta_value( $term_id, $meta_key, get_term_meta( $term_id, $meta_key, true ), $new_meta_value );
		}

		$meta_keys = Setting::get_meta_keys();

		foreach ( $meta_keys as $meta_key ) {
			$new_meta_value = ( isset( $_POST[ $meta_key ] ) ? $_POST[ $meta_key ] : '' );
			$meta_value     = get_term_meta( $term_id, $meta_key, true );

			$this->update_meta_value( $term_id, $meta_key, $meta_value, $new_meta_value );
		}
	}

	public function update_meta_value( $term_id, $meta_key, $meta_value, $new_meta_value ) {
		$has_meta = MetaUtils::has_meta( 'term', $meta_key, $term_id );

		if ( ! $has_meta && $new_meta_value && '' == $meta_value ) {
			add_term_meta( $term_id, $meta_key, $new_meta_value, true );
		} elseif ( $new_meta_value && $new_meta_value != $meta_value ) {
			update_term_meta( $term_id, $meta_key, $new_meta_value );
		} elseif ( '' == $new_meta_value && $meta_value ) {
			update_term_meta( $term_id, $meta_key, null );
		}
	}

	public function edit_view() {
		$view = new PathView( PluginConfig::$plugin_path . 'views/admin/setting.php' );

		Setting::get_board( @$_REQUEST[ 'tag_ID' ] );

		echo $view->render();
	}
}
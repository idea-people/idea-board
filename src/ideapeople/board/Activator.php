<?php
/**
 * User: ideapeople
 * Mail: ideapeople@ideapeople.co.kr
 * Homepage : ideapeople@ideapeople.co.kr
 */

namespace ideapeople\board;

use ideapeople\board\action\FileAction;

class Activator {
	public $roles, $post_type, $file_action;

	public function __construct() {
		$this->post_type   = new PostTypes();
		$this->roles       = new Roles();
		$this->file_action = new FileAction();
	}

	public function register_activation_hook() {
		$this->version_check();

		$this->notification( __idea_board( 'Plugin has been activated' ) );

		$this->post_type->flush();
		$this->roles->add_roles();
		$this->file_action->board_file_utils->create_block_http();
	}

	public function register_deactivation_hook() {
		$this->notification( __idea_board( 'Plugin has been deactivated' ) );

		$this->roles->remove_roles();
	}

	public function version_check() {
		global $wp_version;

		if ( phpversion() < PluginConfig::$support_php_version ) {
			deactivate_plugins( plugin_basename( PluginConfig::$__FILE__ ) );

			wp_die( sprintf( 'This plugin requires PHP Version %s.  Sorry about that.', PluginConfig::$support_php_version ) );

			return false;
		}

		if ( $wp_version * 1 < PluginConfig::$support_wp_version ) {
			deactivate_plugins( plugin_basename( PluginConfig::$__FILE__ ) );

			wp_die( sprintf( 'This plugin requires WP Version %s.  Sorry about that.', PluginConfig::$support_wp_version ) );

			return false;
		}

		return true;
	}

	public function notification( $title ) {
		$message = array(
			sprintf( "<h2>%s <h2><h3>%s</h3>", PluginConfig::$plugin_name, $title ),
			sprintf( 'URL : %s', home_url() ),
			sprintf( 'admin_email :%s', get_bloginfo( 'admin_email' ) )
		);

		add_filter( 'wp_mail_content_type', create_function( '', 'return "text/html"; ' ) );

		wp_mail( PluginConfig::$plugin_author_email, sprintf( '%s가 %s', PluginConfig::$plugin_name, $title ), join( '<br/>', $message ) );

		remove_filter( 'wp_mail_content_type', create_function( '', 'return "text/html"; ' ) );
	}
}
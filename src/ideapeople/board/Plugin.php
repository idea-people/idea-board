<?php
/**
 * User: ideapeople
 * Mail: ideapeople@ideapeople.co.kr
 * Homepage : ideapeople@ideapeople.co.kr
 */

namespace ideapeople\board;

use ideapeople\board\action\AdminAction;
use ideapeople\board\action\CommentAction;
use ideapeople\board\action\FileAction;
use ideapeople\board\action\PostAction;
use ideapeople\board\helper\AdvancedCustomFieldHelper;
use ideapeople\board\helper\BwsCaptchaHelper;
use ideapeople\board\helper\core\HelperLoader;
use ideapeople\board\helper\WordpressPopularPostsHelper;
use ideapeople\board\setting\GlobalSetting;
use ideapeople\util\wp\PluginLoader;
use ideapeople\util\wp\PostOrderGenerator;
use ideapeople\util\wp\WpNoprivUploader;

class Plugin {
	/**
	 * @var PluginLoader
	 */
	public $loader;

	/**
	 * @var PluginLoader
	 */
	public $custom_loader;

	/**
	 * @var PostOrderGenerator
	 */
	public $post_order_generator;

	/**
	 * @var WpNoprivUploader
	 */
	public $nopriv_uploader;

	/**
	 * @var HelperLoader
	 */
	public $helper_loader;

	public function __construct() {
		$this->loader = new PluginLoader();

		$this->custom_loader = new PluginLoader();

		$this->activator_hook();

		$this->register_global();

		$this->plugin_hooks();
	}

	public function activator_hook() {
		$activator = new Activator();

		register_activation_hook( PluginConfig::$__FILE__, array( $activator, 'register_activation_hook' ) );
		register_deactivation_hook( PluginConfig::$__FILE__, array( $activator, 'register_deactivation_hook' ) );
	}

	public function register_global() {
		$this->post_order_generator = new PostOrderGenerator( PluginConfig::$board_post_type, 'idea_board_grp', 'idea_board_ord', 'idea_board_depth' );
		$this->nopriv_uploader      = new WpNoprivUploader( 'idea_board_upload', 'idea_upload_file', PluginConfig::$plugin_url );

		$this->helper_loader = new HelperLoader();
	}

	public function plugin_hooks() {
		$this->nopriv_uploader->ajax_action();

		$this->helper_loader->add_helper( new WordpressPopularPostsHelper() );
		$this->helper_loader->add_helper( new AdvancedCustomFieldHelper() );
		$this->helper_loader->add_helper( new BwsCaptchaHelper() );

		$assets = new Assets();
		$this->loader->add_action( 'admin_enqueue_scripts', $assets, 'admin_enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $assets, 'admin_enqueue_scripts' );
		$this->loader->add_action( 'wp_enqueue_scripts', $assets, 'public_euqueue_scripts' );
		$this->loader->add_action( 'wp_enqueue_scripts', $assets, 'public_euqueue_styles', 999 );

		$types = new PostTypes();
		$this->loader->add_action( 'init', $types, 'register' );

		$rewrite = new Rewrite();
		$this->loader->add_filter( 'query_vars', $rewrite, 'register_query_var' );
		$this->loader->add_filter( 'post_type_link', $rewrite, 'post_type_link', 10, 2 );

		$roles = new Roles();
		$this->loader->add_action( 'admin_init', $roles, 'add_role_caps' );

		$admin_action = new AdminAction();
		$this->loader->add_filter( 'pre_insert_term', $admin_action, 'validate_edit_term', 10, 2 );
		$this->loader->add_action( PluginConfig::$board_tax . "_add_form_fields", $admin_action, 'edit_view' );
		$this->loader->add_action( PluginConfig::$board_tax . "_edit_form", $admin_action, 'edit_view' );
		$this->loader->add_action( "created_" . PluginConfig::$board_tax, $admin_action, 'created_term' );
		$this->loader->add_action( "edit_" . PluginConfig::$board_tax, $admin_action, 'created_term' );
		$this->loader->add_action( 'delete_' . PluginConfig::$board_tax, $admin_action, 'delete_term', 10, 3 );

		$short_code = new ShortCode();
		add_shortcode( 'idea_board', array( $short_code, 'short_code' ) );

		$action = new PostAction();
		$this->loader->add_action( 'wp_ajax_idea_board_edit_post', $action, 'idea_board_edit_post' );
		$this->loader->add_action( 'wp_ajax_nopriv_idea_board_edit_post', $action, 'idea_board_edit_post' );
		$this->loader->add_action( 'save_post_' . PluginConfig::$board_post_type, $action, 'update_idea_post' );

		$viewInterceptor = new ViewInterceptor();
		$this->custom_loader->add_filter( 'pre_cap_check_read_view', $viewInterceptor, 'pre_cap_check_read_view', 10, 2 );
		$this->custom_loader->add_filter( 'pre_cap_check_edit_view', $viewInterceptor, 'pre_cap_check_edit_view', 10, 2 );
		$this->custom_loader->add_filter( 'pre_cap_check_comment_view', $viewInterceptor, 'pre_cap_check_comment_view', 10, 3 );

		$this->loader->add_filter( 'posts_orderby_request', $this->post_order_generator, 'posts_orderby_request', 10, 2 );
		$this->loader->add_action( 'save_post_' . PluginConfig::$board_post_type, $this->post_order_generator, 'update_post_order' );

		$comment_action = new CommentAction();
		$this->loader->add_action( 'preprocess_comment', $comment_action, 'preprocess_comment' );
		$this->loader->add_action( 'wp_ajax_idea_comment_password_check', $comment_action, 'password_check' );
		$this->loader->add_action( 'wp_ajax_nopriv_idea_comment_password_check', $comment_action, 'password_check' );

		$this->loader->add_action( 'wp_ajax_idea_handle_comment_submission', $comment_action, 'handle_comment_submission' );
		$this->loader->add_action( 'wp_ajax_nopriv_idea_handle_comment_submission', $comment_action, 'handle_comment_submission' );

		$file_action = new FileAction();
		$file_action->add_ajax_action();
		$this->loader->add_action( 'wp_ajax_idea_board_delete_attach', $file_action, 'delete_attach_redirect' );
		$this->loader->add_action( 'wp_ajax_nopriv_idea_board_delete_attach', $file_action, 'delete_attach_redirect' );
		$this->custom_loader->add_action( 'idea_board_edited_post', $file_action, 'handle_upload', 10, 2 );

		new GlobalSetting();
	}

	public function run() {
		$this->loader->run();
		$this->custom_loader->run();
	}
}
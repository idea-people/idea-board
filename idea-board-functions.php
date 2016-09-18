<?php
use ideapeople\board\helper\AdvancedCustomFieldHelper;
use ideapeople\board\helper\BpHelper;
use ideapeople\board\helper\BwsCaptchaHelper;
use ideapeople\board\helper\WordpressPopularPostsHelper;
use ideapeople\board\notification\EmailNotification;
use ideapeople\board\PluginConfig;

function idea_board_text_domain() {
	load_plugin_textdomain( 'idea-board', null, trailingslashit( basename( dirname( PluginConfig::$__FILE__ ) ) ) . 'languages' );
}

add_action( 'idea_board_init', 'idea_board_text_domain' );

function __idea_board( $text ) {
	return __( $text, 'idea-board' );
}

function _e_idea_board( $text ) {
	_e( $text, 'idea-board' );
}

function idea_board_allow_html( $t ) {
	$t['input'] = array();

	return $t;
}

add_filter( 'wp_kses_allowed_html', 'idea_board_allow_html' );

function idea_board_add_helpers( $helpers = array() ) {
	$new_helpers = array(
		new WordpressPopularPostsHelper(),
		new AdvancedCustomFieldHelper(),
		new BwsCaptchaHelper(),
		new BpHelper()
	);

	return wp_parse_args( $helpers, $new_helpers );
}

add_filter( 'idea_board_get_helpers', 'idea_board_add_helpers' );

function idea_board_add_notification( $notifications = array() ) {
	$new_notifications = array(
		new EmailNotification()
	);

	return wp_parse_args( $notifications, $new_notifications );
}

add_filter( 'idea_board_get_notifications', 'idea_board_add_notification' );

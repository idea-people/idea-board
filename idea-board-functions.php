<?php
use ideapeople\board\helper\helpers\advanced_custom_field\AdvancedCustomFieldHelper;
use ideapeople\board\helper\helpers\buddypress\BpHelper;
use ideapeople\board\helper\helpers\bws_captcha\BwsCaptchaHelper;
use ideapeople\board\helper\helpers\ultimate_member\UltimateMemberHelper;
use ideapeople\board\helper\helpers\wordpress_popular_posts\WordpressPopularPostsHelper;
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
	$t[ 'input' ] = array();

	return $t;
}

add_filter( 'wp_kses_allowed_html', 'idea_board_allow_html' );

function idea_board_helpers( $helpers = array() ) {
	$new_helpers = array(
		new WordpressPopularPostsHelper(),
		new AdvancedCustomFieldHelper(),
		new BwsCaptchaHelper(),
		new BpHelper(),
		new UltimateMemberHelper()
	);

	return wp_parse_args( $helpers, $new_helpers );
}

add_filter( 'idea_board_get_helpers', 'idea_board_helpers' );

function idea_board_add_notification( $notifications = array() ) {
	$new_notifications = array(
		new EmailNotification()
	);

	return wp_parse_args( $notifications, $new_notifications );
}

add_filter( 'idea_board_get_notifications', 'idea_board_add_notification' );

function idea_board_taxonomy_term_in_query( $query ) {
	global $pagenow;

	$qv = &$query->query_vars;

	if ( $pagenow == 'edit.php'
	     && isset( $qv[ 'post_type' ] )
	     && $qv[ 'post_type' ] == PluginConfig::$board_post_type
	     && isset( $qv[ 'term' ] )
	     && is_numeric( $qv[ 'term' ] )
	     && $qv[ 'term' ] != 0
	) {
		$term              = get_term_by( 'id', $qv[ 'term' ], PluginConfig::$board_tax );
		$qv[ 'tax_query' ] = array(
			'relation' => 'AND',
			array(
				'taxonomy' => PluginConfig::$board_tax,
				'field'    => 'name',
				'terms'    => $term->slug
			)
		);
	}
}

add_filter( 'parse_query', 'idea_board_taxonomy_term_in_query' );

function idea_board_activation_redirect() {
	if ( isset( $_GET[ 'activate-multi' ] ) ) {
		return;
	}

	set_transient( 'idea_board_activation_redirect', true, 30 );
}

add_action( 'idea_board_activation', 'idea_board_activation_redirect' );

function idea_board_do_activation_redirect() {
	if ( ! get_transient( 'idea_board_activation_redirect' ) ) {
		return;
	}

	delete_transient( 'idea_board_activation_redirect' );

	if ( isset( $_GET[ 'activate-multi' ] ) ) {
		return;
	}

	$query_args = array(
		'page'      => 'idea_board_dash_board',
		'post_type' => PluginConfig::$board_post_type
	);

	wp_safe_redirect( add_query_arg( $query_args, admin_url( 'edit.php' ) ) );
}

add_action( 'admin_init', 'idea_board_do_activation_redirect' );

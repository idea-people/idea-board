<?php
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
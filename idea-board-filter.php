<?php
use ideapeople\board\PostView;
use ideapeople\board\setting\Setting;
use ideapeople\board\Post;
use ideapeople\board\PluginConfig;
use ideapeople\util\http\Request;
use ideapeople\util\wp\MetaUtils;
use ideapeople\util\wp\PostUtils;
use ideapeople\util\wp\TermUtils;

function idea_board_save_board_id( $post_ID ) {
	$post = get_post( $post_ID );

	if ( $post->post_type == 'page' ) {

		if ( has_shortcode( $post->post_content, 'idea_board' ) ) {
			$data = PostUtils::get_shortcode_data( 'idea_board', $post->post_content );

			$board_name = $data[ 'attributes' ][ 'name' ];

			$board = Setting::get_board( $board_name );

			if ( $board ) {
				PostUtils::insert_or_update_meta( $post_ID, 'idea_board_page_term', $board->term_id );

				if ( MetaUtils::has_meta( 'term', 'idea_board_page_id', $board->term_id, $post_ID ) ) {
					TermUtils::insert_or_update_meta( $board->term_id, 'idea_board_page_id', $post_ID );
				} else {
					add_term_meta( $board->term_id, 'idea_board_page_id', $post_ID );
				}
			}
		} else {
			$meta = PostUtils::get_post_meta( $post_ID, 'idea_board_page_term', false );

			if ( $meta ) {
				$board = Setting::get_board( $meta );
				delete_term_meta( $board->term_id, 'idea_board_page' );
				delete_post_meta( $post_ID, 'idea_board_page_term' );
			}
		}
	}
}

//add_action( 'save_post', 'idea_board_save_board_id' );

/**
 * @param $protected
 * @param $post
 *
 * @return string
 */
function idea_board_remove_protected( $protected, $post ) {
	$post = get_post( $post );

	if ( $post->post_type == PluginConfig::$board_post_type ) {
		return '%s';
	}

	return $protected;
}

add_filter( 'protected_title_format', 'idea_board_remove_protected', 10, 2 );

/**
 * @param $query
 */
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

function idea_board_add_search_category( $post_type ) {
	global $typenow;
	global $wp_query;

	if ( $typenow == PluginConfig::$board_post_type && $post_type == PluginConfig::$board_post_type ) {
		$boards = Setting::get_boards();

		if ( empty( $boards ) ) {
			return false;
		}

		$taxonomy          = PluginConfig::$board_tax;
		$business_taxonomy = get_taxonomy( $taxonomy );

		wp_dropdown_categories( array(
			'show_option_all' => __( "Show All {$business_taxonomy->label}" ),
			'taxonomy'        => $taxonomy,
			'name'            => 'term',
			'selected'        => @$wp_query->query[ 'term' ],
			'hierarchical'    => true,
			'depth'           => 3,
			'show_count'      => true, // Show # listings in parens
			'hide_empty'      => true, // Don't show businesses w/o listings
		) );
	}
}

add_action( 'restrict_manage_posts', 'idea_board_add_search_category' );

/**
 * @param null $post_id
 *
 * @return null
 */
function idea_board_add_return_url( $post_id = null ) {
	$post = get_post( $post_id );

	if ( $post->post_type != PluginConfig::$board_post_type ) {
		return $post_id;
	}
	?>
	<input type="hidden" name="return_url" value="<?php echo get_permalink( $post->ID ) ?>">
	<?php
}

add_action( 'comment_form', 'idea_board_add_return_url' );

/**
 * @param $author
 *
 * @return mixed|null|string
 */
function idea_board_the_author( $author ) {
	global $post_type;

	if ( $post_type != PluginConfig::$board_post_type ) {
		return $author;
	}

	if ( ! $author ) {
		return Post::get_the_author_nicename();
	}

	return $author;
}

add_filter( 'the_author', 'idea_board_the_author' );

/**
 * 공지사항 정렬 기능 추가
 *
 * @param $queries
 *
 * @return array
 */
function idea_board_post_order_pre( $queries ) {
	global $wpdb;

	$queries[] = "CONVERT((SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key = 'idea_board_is_notice' AND post_id = wp_posts.ID),DECIMAL)";

	return $queries;
}

add_filter( 'idea_post_order_pre', 'idea_board_post_order_pre' );

function idea_board_auto_insert_in_page( $content ) {
	global $post;

	if ( $post->post_type == PluginConfig::$board_post_type ) {
		return $content;
	}

	$boards = Setting::get_boards();

	foreach ( $boards as $board ) {
		$use_pages = Setting::get_use_pages( $board );

		if ( in_array( $post->ID, $use_pages ) ) {
			return $content . PostView::get_view( $board->term_id, get_query_var( 'page_mode' ) );
		}
	}

	return $content;
}

add_filter( 'the_content', 'idea_board_auto_insert_in_page' );

function idea_board_allow_html( $t ) {
	$t[ 'input' ] = array();

	return $t;
}

add_filter( 'wp_kses_allowed_html', 'idea_board_allow_html' );


<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-09-17
 * Time: 오후 4:30
 */

namespace ideapeople\board\action;


use ideapeople\board\PluginConfig;
use ideapeople\board\Post;
use ideapeople\board\setting\Setting;

class AdminPostAction {
	public function add_search_category( $post_type ) {
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

	public function edit_columns( $columns ) {
		$new_columns = array(
			'Email' => __idea_board( 'Email' ),
			'Forum' => __idea_board( 'Forum' )
		);

		return array_merge( $columns, $new_columns );
	}

	public function custom_columns( $column ) {
		global $post;

		if ( $post->ID && $post->post_type == PluginConfig::$board_post_type ) {
			switch ( $column ) {
				case 'Email':
					echo Post::get_user_email( $post->ID );
					break;
				case 'Forum':
					$terms = get_the_terms( $post->ID, PluginConfig::$board_tax );
					if ( is_array( $terms ) ) {
						foreach ( $terms as $key => $value ) {
							$edit_link     = admin_url( 'term.php?post_type=' . PluginConfig::$board_post_type . '&taxonomy=' . PluginConfig::$board_tax . '&tag_ID=' . $value->term_id );
							$terms[ $key ] = '<a href="' . $edit_link . '">' . $value->name . '</a>';
						}

						echo implode( ' | ', $terms );
					}

					break;
			}
		}

		return $column;
	}

	/**
	 * 비회원이 수정한 글을 관리자가 수정할경우 관리자로 작성자가 변경되기 때문에 수정시에는 작성자 변경 안되도록 변경
	 *
	 * @param $data
	 * @param $postarr
	 *
	 * @return mixed
	 */
	function restore_author( $data, $postarr ) {
		if ( isset( $postarr[ 'post_ID' ] ) && $postarr[ 'post_ID' ] ) {
			$post = get_post( $postarr[ 'post_ID' ] );

			$data[ 'post_author' ] = $post->post_author;
		}

		return $data;
	}
}
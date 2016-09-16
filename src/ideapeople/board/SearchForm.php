<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-09-16
 * Time: 오후 5:22
 */

namespace ideapeople\board;


class SearchForm {

	public static function get_search_form() {
		$search_types = apply_filters( 'idea_board_search_types', array(
			'post_title'         => __idea_board( 'Title' ),
			'post_content'       => __idea_board( 'Content' ),
			'post_title_content' => __idea_board( 'Title' ) . '+' . __idea_board( 'Content' )
		) );

		$search_type    = get_query_var( 'searchType' );
		$search_value   = get_query_var( 'searchValue' );
		$query_category = get_query_var( 'idea_board_category' );

		?>
		<div class="idea-search-form idea-board-reset">
			<form method="get">
				<select name="searchType" id="searchType">
					<?php foreach ( $search_types as $key => $value ) : ?>
						<option value="<?php echo $key; ?>"
							<?php echo $key == $search_type ? 'selected' : '' ?>>
							<?php echo $value ?>
						</option>
					<?php endforeach ?>
				</select>
				<input type="text" id="searchValue" name="searchValue"
				       placeholder="<?php _e_idea_board( 'Search Text' ) ?>"
				       value="<?php echo esc_html( $search_value ) ?>">
				<input type="submit" value="<?php _e_idea_board( 'Search' ) ?>">
				<input type="hidden" name="idea_board_category"
				       value="<?php echo $query_category; ?>">
				<input type="hidden" name="paged" value="0">
				<?php if ( ! PluginConfig::$permalink_structure ) { ?>
					<input type="hidden" name="page_id" value="<?php echo get_the_ID() ?>">
				<?php } ?>
			</form>
		</div>
		<?php
	}
}
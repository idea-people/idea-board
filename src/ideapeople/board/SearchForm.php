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
			'post_title'         => __( '제목' ),
			'post_content'       => __( '내용' ),
			'post_title_content' => __( '제목+내용' )
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
				       placeholder="검색어"
				       value="<?php echo esc_html( $search_value ) ?>">
				<input type="submit" value="검색">
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
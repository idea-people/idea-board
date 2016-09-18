<?php
use ideapeople\board\Button;
use ideapeople\board\CategoryForm;
use ideapeople\board\PostTable;
use ideapeople\board\SearchForm;
use ideapeople\board\setting\Setting;
use ideapeople\board\Post;
use ideapeople\board\Rewrite;
use ideapeople\board\CommonUtils;

$board_term     = Setting::get_board();
$page_permalink = CommonUtils::get_post_page_link();

SearchForm::get_search_form();

CategoryForm::get_category_form( $board_term->term_id, $page_permalink );

?>
<table class="idea-board-reset2 idea-board-table">
	<thead>
	<tr>
		<th class="idea-col-no"><?php _e_idea_board( 'No' ); ?></th>
		<th class="idea-col-title"><?php _e_idea_board( 'Title' ); ?></th>
		<th class="idea-col-date"><?php _e_idea_board( 'Date' ); ?></th>
		<th class="idea-col-author "><?php _e_idea_board( 'Author' ); ?></th>
		<th class="idea-col-hit"><?php _e_idea_board( 'Hit' ); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();

			$start_no    = Post::get_start_no();
			$permalink   = Post::get_the_permalink();
			$title       = Post::get_the_title();
			$author_name = Post::get_the_author_nicename();
			$reg_date    = Post::get_the_date( 'Y-m-d' );
			$read_cnt    = Post::get_the_read_cnt();
			?>
			<tr>
				<td class="idea-col-no"><?php echo $start_no; ?></td>
				<td class="idea-col-title idea-text-over">
					<a href="<?php echo $permalink; ?>"><?php echo $title; ?></a>
				</td>
				<td class="idea-col-date"><?php echo $reg_date; ?></td>
				<td class="idea-col-author idea-text-over">
					<?php echo $author_name; ?>
				</td>
				<td class="idea-col-hit"><?php echo $read_cnt; ?></td>
			</tr>
			<?php
		}
	} else {
		echo sprintf( '<tr><td colspan="5">%s.</td></tr>', __idea_board( 'There is no registered History' ) );
	}
	?>
	</tbody>
</table>

<?php
the_posts_pagination( array(
	'mid_size'           => 4,
	'prev_text'          => __( 'Previous page' ),
	'next_text'          => __( 'Next page' ),
	'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page' ) . ' </span>',
) );
?>

<div class="idea-board-buttons">
	<?php echo Button::write_button(); ?>
</div>
<?php
use ideapeople\board\Button;
use ideapeople\board\setting\Setting;
use ideapeople\board\Post;
use ideapeople\board\Rewrite;
use ideapeople\board\CommonUtils;

$board_term     = Setting::get_board();
$page_permalink = CommonUtils::get_post_page_link();

CommonUtils::get_search_form();
CommonUtils::get_category_form( $board_term->term_id, $page_permalink );

?>
<table class="idea-board-reset2 idea-board-table">
	<thead>
	<tr>
		<th class="idea-col-no">순번</th>
		<th class="idea-col-title">제목</th>
		<th class="idea-col-date">작성일</th>
		<th class="idea-col-author ">작성자</th>
		<th class="idea-col-hit">조회수</th>
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
				<td class="idea-col-no">
					<?php echo $start_no; ?>
				</td>
				<td class="idea-col-title idea-text-over">
					<a href="<?php echo $permalink; ?>">
						<?php echo $title; ?>
					</a>
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
		echo '<tr><td colspan="5">등록된 내역이 없습니다.</td></tr>';
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
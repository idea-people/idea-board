<?php

use ideapeople\board\PluginConfig;

$today = getdate();

/**
 * @var $comments WP_Comment[]
 */
$comments = get_comments( array(
	'post_type'  => PluginConfig::$board_post_type,
	'date_query' => array(
		array(
			'year'  => $today[ 'year' ],
			'month' => $today[ 'mon' ],
			'day'   => $today[ 'mday' ],
		),
	)
) );
?>
<table>
	<?php foreach ( $comments as $comment ) : ?>
		<tr>
			<td>
				<a href="<?php the_permalink( $comment->comment_post_ID ); ?>">
					<?php echo esc_html( $comment->comment_content ) ?>
				</a>
			</td>
			<td class="alignright">
				<?php echo $comment->comment_date; ?>
			</td>
		</tr>
	<?php endforeach; ?>
</table>

<?php
use ideapeople\board\PluginConfig;

$today = getdate();

query_posts( array(
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
		<?php while ( have_posts() ) {
			the_post(); ?>
			<tr>
				<td>
					<a href="<?php the_permalink(); ?>">
						<?php the_title(); ?>
					</a>
				</td>
				<td class="alignright"><?php echo get_the_date(); ?></td>
			</tr>
		<?php } ?>
	</table>
<?php wp_reset_query();
<?php
use ideapeople\board\helper\core\AbstractHelper;

/**
 * @var $helpers AbstractHelper[]
 */
$helpers = apply_filters( 'idea_board_get_helpers' );
?>
<table>
	<?php foreach ( $helpers as $helper ):
		?>
		<tr>
			<td>
				<a href="<?php echo $helper->get_plugin_url(); ?>" target="_blank">
					<?php echo $helper->get_plugin_name(); ?>
				</a>
			</td>
			<td class="alignright">
				<?php if ( ! $helper->is_installed() ) :
//					$slug = $helper->get_plugin_name();
//					$link = wp_nonce_url( 'plugin-install.php?tab=plugin-information&plugin=' . $slug, 'install-plugin_' . $slug ) . '&amp;TB_iframe=true&amp;width=600&amp;height=800';
					?>
					<a href="<?php echo $helper->get_plugin_url(); ?>" class="install">install</a>
				<?php endif; ?>
				<?php if ( ! $helper->is_activate() && $helper->is_installed() ) : ?>
					<a href="<?php echo admin_url( 'plugins.php' ); ?>"
					   class="activate">activate</a>
				<?php endif; ?>
			</td>
		</tr>
	<?php endforeach; ?>
</table>

<p>플러그인을 설치해서 마음껏 확장해 보세요</p>
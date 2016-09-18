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
				<?php
				$link = add_query_arg( array(
					'action'        => 'activate',
					'plugin'        => $helper->get_name(),
					'plugin_status' => 'all',
					'_wp_nonce'     => wp_create_nonce()
				), admin_url( 'plugins.php' ) );

				if ( ! $helper->is_installed() ) {
					$plugin_file = $helper->get_plugin_name();
					$link        = wp_nonce_url( 'plugin-install.php?tab=plugin-information&plugin=' . $plugin_file, 'install-plugin_' . $plugin_file ) . '&amp;TB_iframe=true&amp;width=600&amp;height=800';
					?>
					<a href="<?php echo $link; ?>" class="thickbox open-plugin-details-modal">install</a>
				<?php } else if ( ! $helper->is_activate() && $helper->is_installed() ) {
					?>
					<a href="<?php echo wp_nonce_url( $link, 'activate-plugin_' . $helper->get_name() ); ?>"
					   class="activate">activate</a>
				<?php } else {
					$link = add_query_arg( array( 'action' => 'deactivate' ), $link );
					?>
					<a href="<?php echo wp_nonce_url( $link, 'deactivate-plugin_' . $helper->get_name() ); ?>"
					   class="activate">deactivate</a>
				<?php } ?>
			</td>
		</tr>
	<?php endforeach; ?>
</table>

<p><?php _e_idea_board( 'As much as you want to install the plug-in expansion' ); ?></p>
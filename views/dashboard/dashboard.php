<?php use ideapeople\board\PluginConfig; ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/foundicons/3.0.0/foundation-icons.css">
<div class="wrap" id="idea-board-dash-board">
	<h2>
		<?php _e_idea_board( PluginConfig::$plugin_name ); ?>
		<sup><?php echo PluginConfig::$plugin_version; ?></sup>
	</h2>

	<div id="welcome-panel" class="welcome-panel">
		<div class="welcome-panel-content">
			<h2><?php _e_idea_board( sprintf( 'Thank you for using %s', PluginConfig::$plugin_name ) ) ?>!</h2>
			<p class="about-description"></p>
			<div class="welcome-panel-column-container">
				<div class="welcome-panel-column ">
					<h3><?php _e( 'Get Started' ); ?></h3>
					<a class="button button-primary button-hero load-customize hide-if-no-customize"
					   href="<?php echo sprintf( admin_url( 'edit-tags.php' ) . '?taxonomy=%s&post_type=%s', PluginConfig::$board_tax, PluginConfig::$board_post_type ); ?>">
						<?php _e_idea_board( 'Add Forum' ); ?></a>
					<p><?php _e_idea_board( 'Try to start immediately' ); ?></p>
				</div>
				<div class="welcome-panel-column">
					<h3><?php _e_idea_board( 'Please feel free to contact us' ); ?>!</h3>
					<p>
						<a href="http://www.ideapeople.co.kr/plugin-qna/" target="_blank"
						   class="button button-primary button-hero load-customize hide-if-no-customize"><?php _e_idea_board( 'Inquiries or suggestions to' ); ?></a>
					</p>
				</div>
				<div class="welcome-panel-column welcome-panel-last">
					<h3><?php _e_idea_board( 'Help more people can use' ); ?></h3>
					<p><?php _e_idea_board( 'It can be quickly developed' ); ?></p>
					<p>
						<span class="fb-like" data-href="https://www.facebook.com/ipeople2014/" data-layout="button"
						      data-action="like"
						      data-size="large" data-show-faces="true" data-share="true"></span>

						<span id="fb-root"></span>
						<script>(function (d, s, id) {
								var js, fjs = d.getElementsByTagName(s)[0];
								if (d.getElementById(id)) return;
								js = d.createElement(s);
								js.id = id;
								js.src = "//connect.facebook.net/ko_KR/sdk.js#xfbml=1&version=v2.7&appId=1778149215766306";
								fjs.parentNode.insertBefore(js, fjs);
							}(document, 'script', 'facebook-jssdk'));</script>
					</p>

				</div>
			</div>
		</div>
	</div>

	<div id="dashboard-widgets-wrap" class="idea-board-meta-boxes">
		<div id="dashboard-widgets" class="metabox-holder ">
			<div id="postbox-container-1"
			     class="postbox-container">
				<?php do_meta_boxes( $this->page, 'core', null ); ?>
			</div>
			<div id="postbox-container-2"
			     class="postbox-container">
				<?php do_meta_boxes( $this->page, 'normal', null ); ?>
			</div>
			<div id="postbox-container-3"
			     class="postbox-container">
				<?php do_meta_boxes( $this->page, 'side', null ); ?>
			</div>
			<div id="postbox-container-4"
			     class="postbox-container">
				<?php do_meta_boxes( $this->page, 'low', null ); ?>
			</div>
		</div>
	</div>
	<?php
	wp_nonce_field( 'idea-board-metaboxes-general' );
	wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
	wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
	?>
	<ul class="idea-board-sns-list">
		<li><a target="_blank" href="https://www.facebook.com/ipeople2014/"><i class="fi-social-facebook"></i></a>
		</li>
		<li>
			<a href="https://github.com/idea-people/idea-board" target="_blank"><i class="fi-social-github"></i></a>
		</li>
	</ul>
</div>
<script type="text/javascript">
	jQuery(document).ready(function ($) {
		postboxes.add_postbox_toggles('<?php echo $this->page; ?>');
	});
</script>



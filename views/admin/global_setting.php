<?php
use ideapeople\board\setting\GlobalSetting;

$o = GlobalSetting::instance();
?>
<div class="wrap">
	<h2><?php _e( '' ) ?></h2>

	<form action="options.php" method="POST">
		<?php
		settings_fields( $o->option_group );
		do_settings_sections( $o->slug );
		?>
		<?php submit_button(); ?>
	</form>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.6.2/chosen.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.6.2/chosen.jquery.min.js"></script>
<script>
	(function ($) {
		$(document).ready(function () {
			$('.chosen-select').chosen({width: "80%"});
			$('.form-table .role_row').css('visibility', 'visible');
		});
	})(jQuery);
</script>
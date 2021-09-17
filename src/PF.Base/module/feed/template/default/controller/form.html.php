<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<span class="cancel_post"><i class="fa fa-close"></i></span>
{module name='feed.form'}
{literal}
<script>
    js_box_remove($('.fa-close').first().parent());
	$Ready(function() {
		if ($('#panel #global_attachment_status textarea').length) {
			$('#panel #global_attachment_status textarea').focus();
		}
	});
</script>
{/literal}
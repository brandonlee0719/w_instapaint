<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
<div style="padding:10px;">
	<div id="js_preview_data"></div>
</div>
{literal}
<script type="text/javascript">
	$Behavior.ad_preview_1 = function()
	{
		$('#js_preview_data').html(window.opener.$('#html_code').val());
	};
</script>
{/literal}
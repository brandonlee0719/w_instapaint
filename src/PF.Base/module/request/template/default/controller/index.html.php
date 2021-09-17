<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
<div id="js_request_data">
	{plugin call='request.template_controller_index'}
</div>
{literal}
<script type="text/javascript">
	$Behavior.requestCheckData = function ()
	{
		if ($('#js_request_data').html().replace(/^\s+|\s+$/g, '') == '')
		{
			{/literal}
			$('#js_request_data').html('<div class="extra_info">{_p var='there_are_no_new_requests' phpfox_squote=true}</a>');
			{literal}
		}
	}
</script>
{/literal}
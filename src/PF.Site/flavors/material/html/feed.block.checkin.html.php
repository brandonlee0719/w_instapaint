<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>

<a href="#" type="button" id="btn_display_check_in" class="activity_feed_share_this_one_link parent js_hover_title" onclick="return false;">
	<span class="ico ico-checkin-o"></span>
	<span class="js_hover_info">
		{_p var='check_in'}
	</span>
</a>

<script type="text/javascript">
	var bCheckinInit = false;
	$Behavior.prepareInit = function()
	{l}
		$Core.Feed.sIPInfoDbKey = '';
		$Core.Feed.sGoogleKey = '{param var="core.google_api_key"}';
		
		{if isset($aVisitorLocation)}
			$Core.Feed.setVisitorLocation({$aVisitorLocation.latitude}, {$aVisitorLocation.longitude} );
		{else}
			
		{/if}
		
		$Core.Feed.googleReady('{param var="core.google_api_key"}');
	{r}
</script>
<script type="text/javascript" src="{param var='core.url_module'}feed/static/jscript/places.js"></script>
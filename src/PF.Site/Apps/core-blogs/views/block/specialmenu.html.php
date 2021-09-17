<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="profile_blog_special_menu">
    <a href="{$aDrafts.url}" {if $aDrafts.active == true} class="active"{/if}>
    	<div>
			{$aDrafts.total}<span class="badge"> {$aDrafts.phrase}</span>
		</div> 
    </a>
</div>
<div class="clear"></div>

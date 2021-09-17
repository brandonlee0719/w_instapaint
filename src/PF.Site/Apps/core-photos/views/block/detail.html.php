<?php
	defined('PHPFOX') or exit('NO DICE!'); 
?>

{foreach from=$aPhotoDetails name=photodetails key=sKey item=sValue}
    <div class="info">
        <div class="info_left">
            {$sKey}:
        </div>
        <div class="info_right">
            {$sValue}
        </div>
    </div>
{/foreach}
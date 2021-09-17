<?php
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if isset($aCategories)}
    {template file='core.block.category'}
{else}
    {if $bParent}
        <ul class="action">
            <li>
                <a href="{if $aCallback === null}{url link='photo'}{else}{url link=$aCallback.url_home}{/if}" class="js_photo_category">{_p var='all_categories'}</a>{/if}
                {$sCategories}
            {if $bParent}</li>
            </ul>
    {/if}
{/if}
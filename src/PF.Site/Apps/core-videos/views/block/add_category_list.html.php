<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if count($aItems)}
    <select id="video_categories" name="val[category][]" multiple="multiple" class="form-control">
        {foreach from=$aItems item=aCategory}
            <option value="{$aCategory.category_id}" {if isset($aCategory.active)}selected="selected"{/if}>
                {$aCategory.name|convert}
            </option>
            {foreach from=$aCategory.sub item=aSubCategory}
                <option value="{$aSubCategory.category_id}" {if isset($aSubCategory.active)}selected="selected"{/if}>
                    -- {$aSubCategory.name|convert}
                </option>
            {/foreach}
        {/foreach}
    </select>
{else}
    <div class="p_4">
        {_p var='no_categories_added'}
    </div>
{/if}
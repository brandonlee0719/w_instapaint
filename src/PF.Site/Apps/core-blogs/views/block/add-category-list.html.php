<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if !empty($sCategories)}
    <label for="category">{_p var='categories'}</label>
    <select class="form-control" {if $bMultiple}name="val[selected_categories][]" multiple="multiple" size="8"{else}name="val[parent_id]"{/if} style="max-width:100%">
        {if !$bMultiple}<option value="">{_p var='select'}:</option>{/if}
        {$sCategories}
    </select>
{/if}

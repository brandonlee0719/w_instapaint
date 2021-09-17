<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if $bCanEdit}
<li><a href="{url link='groups.add' id=$aPage.page_id}"><span class="ico ico-gear-form-o mr-1"></span>{_p('manage')}</a></li>
{/if}
{if $bCanDelete}
<li class="item_delete">
    <a href="{url link='groups' delete=$aPage.page_id}" class="sJsConfirm" class="no_ajax_link">
        <span class="ico ico-trash-alt-o mr-1"></span>
        {_p('delete_this_group')}
    </a>
</li>
{/if}
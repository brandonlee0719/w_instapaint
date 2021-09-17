<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if $aPage.bCanApprove}
<li><a href="#" onclick="$.ajaxCall('groups.approve', 'page_id={$aPage.page_id}');"><span class="ico ico-check-square-alt mr-1"></span>{_p var='approve'}</a></li>
{/if}
{if $aPage.bCanEdit}
<li><a href="{url link='groups.add' id=$aPage.page_id}"><span class="ico ico-gear-form-o mr-1"></span>{_p var='manage'}</a></li>
{/if}
{if $aPage.bCanDelete}
<li class="item_delete">
    <a href="{if isset($iCurrentProfileId)}{url link='groups' delete=$aPage.page_id profile=$iCurrentProfileId}{else}{url link='groups' delete=$aPage.page_id}{/if}" data-message="{_p var='are_you_sure_you_want_to_delete_this_group_permanently'}" class="no_ajax_link sJsConfirm">
        <span class="ico ico-trash-alt-o mr-1"></span>{_p var='delete'}
    </a>
</li>
{/if}
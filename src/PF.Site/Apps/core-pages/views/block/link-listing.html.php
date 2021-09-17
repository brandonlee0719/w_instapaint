{if $aPage.canApprove}
<li><a href="#" onclick="$.ajaxCall('pages.approve', 'page_id={$aPage.page_id}');"><span class="ico ico-check-square-alt mr-1"></span>{_p var='approve'}</a></li>
{/if}
{if $aPage.canEdit}
<li><a href="{url link='pages.add' id=$aPage.page_id}"><span class="ico ico-gear-form-o mr-1"></span>{_p var='manage'}</a></li>
{/if}
{if $aPage.canDelete}
<li class="item_delete">
   <a href="{if isset($iCurrentProfileId)}{url link='pages' delete=$aPage.page_id profile=$iCurrentProfileId}{else}{url link='pages' delete=$aPage.page_id}{/if}" data-message="{_p var='are_you_sure_you_want_to_delete_this_page_permanently'}" class="no_ajax_link sJsConfirm">
        <span class="ico ico-trash-alt-o mr-1"></span>{_p var='Delete this Page'}
    </a>
</li>
{/if}
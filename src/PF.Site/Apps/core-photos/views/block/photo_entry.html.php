<article class="photo-listing-item feature-photo sponsored-photo" data-url="{$aForms.link}" data-photo-id="{$aForms.photo_id}" id="js_photo_id_{$aForms.photo_id}">
    <div class="item-outer">
        <a class="item-media" {if !$aForms.can_view} class="no_ajax_link" onclick="tb_show('{_p('warning')}', $.ajaxBox('photo.warning', 'height=300&width=350&link={$aForms.link}')); return false;" href="javascript:;" {else} href="{$aForms.link}" {/if}
        {if !empty($aForms.destination)}style="background-image: url({if !$aForms.can_view}{img theme="misc/mature.jpg" return_url=true}{else}{img server_id=$aForms.server_id path='photo.url_photo' file=$aForms.destination suffix='_500' title=$aForms.title return_url=true}{/if})"{/if}>
        </a>
        <div class="item-inner {if $aForms.hasPermission}has-permission{/if}">
            <div class="item-stats text-uppercase mb-1">
                {if $aForms.total_like > 0}<span class="mr-2">{$aForms.total_like|short_number}{if $aForms.total_like == 1} {_p('like')}{else} {_p('likes')}{/if}</span>{/if}
                {if $aForms.total_view > 0}<span>{$aForms.total_view|short_number}{if $aForms.total_view == 1} {_p('view')}{else} {_p('views')}{/if}</span>{/if}
            </div>
            <a class="item-title fw-bold" href="{$aForms.link}">
                {$aForms.title|clean}
            </a>
            {if !isset($bNotShowOwner) || !$bNotShowOwner}
                <span class="item-author">{_p var='post_by'} {$aForms|user}</span>
            {/if}
        </div>
        <div class="item-media-flag">
            {if (isset($sView) && $sView == 'my' || isset($bIsDetail)) && $aForms.view_id == 1}
            <div class="sticky-label-icon sticky-pending-icon">
                <span class="flag-style-arrow"></span>
                <i class="ico ico-clock-o"></i>
            </div>
            {/if}
            {if $aForms.is_sponsor}
            <div class="sticky-label-icon sticky-sponsored-icon">
                <span class="flag-style-arrow"></span>
                <i class="ico ico-sponsor"></i>
            </div>
            {/if}
            {if $aForms.is_featured}
            <div class="sticky-label-icon sticky-featured-icon">
                <span class="flag-style-arrow"></span>
                <i class="ico ico-diamond"></i>
            </div>
            {/if}
        </div>
        
        {if $bShowModerator}
            <div class="{if $bShowModerator} moderation_row{/if}">
               {if !empty($bShowModerator)}
                   <label class="item-checkbox">
                       <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aForms.photo_id}" id="check{$aForms.photo_id}" />
                       <i class="ico ico-square-o"></i>
                   </label>
               {/if}
            </div>
        {/if}
        {if $aForms.hasPermission}
            <div class="item-option photo-button-option">
                <div class="dropdown">
                    <span role="button" class="row_edit_bar_action" data-toggle="dropdown">
                        <i class="ico ico-gear-o"></i>
                    </span>
                    <ul class="dropdown-menu dropdown-menu-right">
                        {template file='photo.block.menu'}
                    </ul>
                </div>
            </div>
        {/if}
    </div>
</article>
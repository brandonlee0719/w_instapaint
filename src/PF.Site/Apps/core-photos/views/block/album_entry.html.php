<article data-url="{$aAlbum.link}" data-uid="{$aAlbum.album_id}" id="js_album_id_{$aAlbum.album_id}" class="photo-album-item pl-1 pr-1">
    <div class="item-outer">
        <div class="item-media">
            <a href="{$aAlbum.link}" style="background-image: url(
                    {if ($aAlbum.mature == 0 || (($aAlbum.mature == 1 || $aAlbum.mature == 2) && Phpfox::getUserId() && Phpfox::getUserParam('photo.photo_mature_age_limit') <= Phpfox::getUserBy('age'))) || $aAlbum.user_id == Phpfox::getUserId()}
                        {if !empty($aAlbum.destination)}
                            {img return_url="true" server_id=$aAlbum.server_id path='photo.url_photo' file=$aAlbum.destination suffix='_500' max_width=500 max_height=500}
                        {else}
                            {param var='photo.default_album_photo'}
                        {/if}
                    {else}
                        {img return_url="true" theme='misc/mature.jpg' alt=''}
                    {/if}
            )">
            </a>
            <span class="item-total-photo">
                <i class="ico ico-photos-alt-o"></i>
                {if isset($aAlbum.total_photo)}
                    {if $aAlbum.total_photo == '1'}1{else}{$aAlbum.total_photo|number_format}{/if}
                {/if}
                {plugin call='photo.template_block_album_entry_extra_info'}
            </span>
        </div>
           
        <div class="item-inner {if $aAlbum.hasPermission}has-permission{/if} mt-2">
            <div class="item-title"><a class="text-transition" href="{$aAlbum.link}">{$aAlbum.name|clean}</a></div>
            <div class="item-author">{_p var="create_by"} {$aAlbum|user}</div>
            {if $aAlbum.hasPermission}
                <div class="item-option">
                    <div class="dropdown">
                        <span role="button" class="row_edit_bar_action" data-toggle="dropdown">
                            <i class="ico ico-gear-o"></i>
                        </span>
                        <ul class="dropdown-menu dropdown-menu-right">
                            {template file='photo.block.menu-album'}
                        </ul>
                    </div>
                </div>
            {/if}
        </div>

        {if $bShowModerator && $aAlbum.canDelete}
            <div class="moderation_row">
                <label class="item-checkbox">
                   <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aAlbum.album_id}" id="check{$aAlbum.album_id}" />
                   <i class="ico ico-square-o"></i>
               </label>
            </div>
        {/if}
    </div>
    
</article>

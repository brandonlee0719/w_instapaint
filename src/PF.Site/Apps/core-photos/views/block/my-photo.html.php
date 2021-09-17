<?php
    defined('PHPFOX') or exit('NO DICE!');
?>

{if !count($aPhotos)}
    <div class="extra_info">
        {_p var='no_photos_uploaded_yet'}
    </div>
{else}
    <div class="item-container photo-widget-block recent-photo photo-app">
        {foreach from=$aPhotos item=aPhoto}
            <article class="photos-{$iCount}">
                <div class="item-outer">
                    <a class="item-media" {if !$aPhoto.can_view}onclick="tb_show('{_p('warning')}', $.ajaxBox('photo.warning', 'height=300&width=350&link={$aPhoto.link}')); return false;" href="javascript:;"{else}href="{$aPhoto.link}"{/if} title="{_p var='title_by_full_name' title=$aPhoto.title|clean full_name=$aPhoto.full_name|clean}" {if !empty($aPhoto.destination)}style="background-image: url({if !$aPhoto.can_view}{img theme="misc/mature.jpg" return_url=true}{else}{img server_id=$aPhoto.server_id path='photo.url_photo' file=$aPhoto.destination suffix='_500' max_width=500 max_height=500 class="hover_action" title=$aPhoto.title return_url=true }{/if})"{/if}>
                    </a>
                    {if $aPhoto.total_like > 0 }
                        <div class="item-statistic">
                            <span class="like-count"><i class="ico ico-thumbup mr-1"></i>{$aPhoto.total_like|short_number}</span>
                        </div>
                    {/if}
                </div>
            </article>
        {/foreach}
    </div>
{/if}
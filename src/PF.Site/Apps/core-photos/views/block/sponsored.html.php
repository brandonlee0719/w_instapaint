<?php

defined('PHPFOX') or exit('NO DICE!'); 

?>
<ul class="photo-featured">
    {foreach from=$aSponsorPhotos item=aSponsorPhoto}
        <li>
            <a {if !$aSponsorPhoto.can_view} class="no_ajax_link" onclick="tb_show('{_p('warning')}', $.ajaxBox('photo.warning', 'height=300&width=350&link={$aSponsorPhoto.link}')); return false;" href="javascript:;" {else} href="{$aSponsorPhoto.link}" {/if}
                style="background-image: url(
                    {if $aSponsorPhoto.can_view}
                        {img server_id=$aSponsorPhoto.server_id path='photo.url_photo' file=$aSponsorPhoto.destination suffix='_500' max_width=500 max_height=500 return_url="true"}
                    {elseif $aSponsorPhoto.mature}
                        {img theme="misc/mature.jpg" return_url=true}
                    {/if}
                    )">
            </a>
            {if $aSponsorPhoto.total_like > 0}
                <span class="photo_like pl-1 pr-1">
                    <span class="count"><i class="ico ico-thumbup"></i> {$aSponsorPhoto.total_like|short_number}</span>
                </span>
            {/if}
        </li>
    {/foreach}
</ul>
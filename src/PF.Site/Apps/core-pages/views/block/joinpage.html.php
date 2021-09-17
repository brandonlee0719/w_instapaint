<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
{if !Phpfox::getUserBy('profile_page_id') && Phpfox::isUser()}
    {if isset($aPage) && $aPage.reg_method == '2' && !isset($aPage.is_invited) && $aPage.page_type == '1'}
    {else}
        {if isset($aPage) && isset($aPage.is_reg) && $aPage.is_reg}
        {else}
            {if isset($aPage) && !empty($aPage.is_liked)}
            <div class="dropdown">
                <a role="button" class="btn btn-round btn-default btn-icon item-icon-liked pages_like_join pages_unlike_unjoin" data-toggle="dropdown">
                    <span class="ico ico-thumbup"></span>{_p var='liked'}<span class="ml-1 ico ico-caret-down"></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-right">
                    <li>
                        <a role="button" onclick="$.ajaxCall('like.delete', 'type_id=pages&amp;item_id={$aPage.page_id}'); return false;">
                            <span class="mr-1 ico ico-thumbdown"></span>{_p var='unlike'}
                        </a>
                    </li>
                </ul>
            </div>
            {else}
            <button class="btn btn-round btn-primary btn-gradient btn-icon item-icon-like" onclick="$.ajaxCall('like.add', 'type_id=pages&item_id={$aPage.page_id}&reload=1');">
                <span class="ico ico-thumbup-o"></span>{_p var='like'}
            </button>
            {/if}
        {/if}
    {/if}
{/if}

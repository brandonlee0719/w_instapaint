<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="group-listing item-container group-home in">
    {foreach from=$aGroupsList item=aPage}
    <article class="group-item">
        <div class="item-outer">
            <div class="group-photo">
                <a href="{$aPage.url}">
                    {img server_id=$aPage.image_server_id title=$aPage.title path='pages.url_image' file=$aPage.image_path suffix='_200_square' max_width='200' max_height='200' is_page_image=true}
                </a>
                <!-- desc grid view mode -->
                <a href="{$aPage.url}" class="item-desc">
                    <div class="item-desc-main">{$aPage.text_parsed|striptag|stripbb}</div>
                </a>
            </div>
            <div class="item-inner">
                <div class="item-title">
                    <a href="{$aPage.url}" class="link pages_title ">{$aPage.title|clean}</a>
                </div>
                <div class="item-number-member">
                    {if $aPage.total_like != 1}
                        {_p var='groups_total_members', total=$aPage.total_like}
                    {else}
                        {_p var='groups_total_member', total=$aPage.total_like}
                    {/if}
                </div>
                {if !empty($aPage.bShowItemActions)}
                <!-- please condition when show -->
                <div class="item-option group-button-option">
                    <div class="dropdown">
                        <a class="btn dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true"
                           aria-expanded="true">
                            <i class="ico ico-gear-o"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right" id="js_group_entry_options_{$aPage.page_id}">
                            {template file='groups.block.link-listing'}
                        </ul>
                    </div>
                </div>
                {/if}
            </div>
        </div>
    </article>
    {/foreach}
</div>

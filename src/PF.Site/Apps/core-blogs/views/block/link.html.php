<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
{if $aItem.canEdit}
	<li><a title="{_p var='edit_this_blog'}" href="{url link='blog.add' id=$aItem.blog_id}"><span class="ico ico-pencilline-o mr-1"></span>{_p var='edit'}</a></li>
    <li role="separator" class="divider"></li>
{/if}
  
{if $aItem.canPublish}
    <li><a title="{_p var='publish_this_blog'}" href="{url link='blog.add' id=$aItem.blog_id publish=1}"><span class="ico ico-upload mr-1"></span>{_p var='publish'}</a></li>
{/if}
  
{if $aItem.canApprove}
    {if !empty($bBlogView)}
        <li><a href="javascript:void(0)" onclick="$.ajaxCall('blog.approve', 'id={$aItem.blog_id}'); return false;" title="{_p var='approve_this_blog'}"><span class="ico ico-check-square-alt mr-1"></span>{_p var='approve'}</a></li>
    {elseif isset($sView) && $sView == 'pending'}
        <li><a href="javascript:void(0)" onclick="$.ajaxCall('blog.approve', 'inline=true&amp;id={$aItem.blog_id}'); return false;" title="{_p var='approve_this_blog'}"><span class="ico ico-check-square-alt mr-1"></span>{_p var='approve'}</a></li>
    {/if}
{/if}

{if $aItem.canSponsorInFeed}
<li>
    {if Phpfox::getService('feed')->canSponsoredInFeed('blog', $aItem.blog_id)}
    <a title="{_p var='sponsor_in_feed'}" href="{url link='ad.sponsor' where='feed' section='blog' item=$aItem.blog_id}">
        <span class="ico ico-sponsor mr-1"></span>{_p var='sponsor_in_feed'}
    </a>
    {else}
    <a title="{_p var='unsponsor_in_feed'}" href="javascript:void(0)" onclick="$.ajaxCall('ad.removeSponsor', 'type_id=blog&item_id={$aItem.blog_id}', 'GET'); return false;">
        <span class="ico ico-sponsor mr-1"></span>{_p var="unsponsor_in_feed"}
    </a>
    {/if}
</li>
{/if}

{if $aItem.is_approved == 1 && $aItem.post_status == BLOG_STATUS_PUBLIC}
{if $aItem.canSponsor || $aItem.canFeature || $aItem.canPurchaseSponsor}
    {if $aItem.canSponsor}
        {if !$aItem.is_sponsor}
        <li id="js_photo_sponsor_{$aItem.blog_id}">
            <a title="{_p var='sponsor_this_blog'}" href="javascript:void(0)" onclick="$.ajaxCall('blog.sponsor','blog_id={$aItem.blog_id}&type=1'); return false;">
                <span class="ico ico-sponsor mr-1"></span>{_p var='sponsor_this_blog'}
            </a>
        </li>
        {else}
        <li id="js_blog_unsponsor_{$aItem.blog_id}">
            <a title="{_p var='unsponsor_this_blog'}" href="javascript:void(0)" onclick="$.ajaxCall('blog.sponsor','blog_id={$aItem.blog_id}&type=0'); return false;">
                <span class="ico ico-sponsor mr-1"></span>{_p var='unsponsor_this_blog'}
            </a>
        </li>
        {/if}
    {elseif $aItem.canPurchaseSponsor}
        <li>
            <a title="{_p var='sponsor_this_blog'}" href="{permalink module='ad.sponsor' id=$aItem.blog_id}section_blog/">
                <span class="ico ico-sponsor mr-1"></span>{_p var='sponsor_this_blog'}
            </a>
        </li>
    {/if}

    {if $aItem.canFeature}
        <li id="js_blog_feature_{$aItem.blog_id}">
            {if $aItem.is_featured}
                <a href="javascript:void(0)" title="{_p var='un_feature_this_blog'}" onclick="$.ajaxCall('blog.feature', 'blog_id={$aItem.blog_id}&amp;type=0'); return false;"><span class="ico ico-diamond-o mr-1"></span>{_p var='un_feature'}</a>
            {else}
                <a href="javascript:void(0)" title="{_p var='feature_this_blog'}" onclick="$.ajaxCall('blog.feature', 'blog_id={$aItem.blog_id}&amp;type=1'); return false;"><span class="ico ico-diamond-o mr-1"></span>{_p var='feature'}</a>
            {/if}
        </li>
    {/if}
<li role="separator" class="divider"></li>
{/if}
{/if}
{if $aItem.canDelete}
	<li class="item_delete"><a href="javascript:void(0)" onclick="$Core.jsConfirm({l}{r}, function(){l}$.ajaxCall('blog.delete', 'blog_id={$aItem.blog_id}{if isset($bIsDetail)}&is_detail=1{/if}');{r}, function(){l}{r}); return false;" data-message="{_p var='are_you_sure_you_want_to_delete_this_blog_permanently' phpfox_squote=true}" title="{_p var='delete_blog'}"><span class="ico ico-trash-o mr-1"></span>{_p var='delete'}</a></li>
{/if}
{plugin call='blog.template_block_entry_links_main'}

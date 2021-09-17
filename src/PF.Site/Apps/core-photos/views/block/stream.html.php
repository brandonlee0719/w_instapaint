<?php
defined('PHPFOX') or exit('NO DICE!');
?>

{if $aForms.view_id == 1}
    {template file='core.block.pending-item-action'}
{/if}
<div class="item_view">
    <div class="item_info">
        {img user=$aForms suffix='_50_square'}
        <div class="item_info_author">
            <div>{_p var='by__u'} {$aForms|user:'':'':50}</div>
            <div>{$aForms.time_stamp|convert_time} {if !empty($aForms.album_id)}{_p var='in'} <a href="{$aForms.album_url}">{$aForms.album_title|convert|clean|split:45|shorten:75:'...'}</a>{/if}
            </div>
        </div>
    </div>
    <div class="item-comment mb-2">
        {if $aForms.view_id == 0}
            <div>
               {module name='feed.mini-feed-action'}
           </div>
       {/if}
       <span class="item-total-view">
           <span>{$aForms.total_view|short_number}</span>{if $aForms.total_view == 1} {_p var='view_lowercase'}{else} {_p var='views_lowercase'}{/if}
       </span>
    </div>
    {if $aForms.hasPermission}
        <div class="item_bar">
            <div class="dropdown">
                <span role="button" data-toggle="dropdown" class="item_bar_action">
                    <i class="ico ico-gear-o"></i>
                </span>
                <ul class="dropdown-menu dropdown-menu-right">
                    {template file='photo.block.menu'}
                </ul>
            </div>
        </div>
    {/if}
</div>

<div class="photos_view dont-unbind" data-photo-id="{$aForms.photo_id}">
    <div class="photos_view_loader">
    <i class="fa fa-spin fa-circle-o-notch"></i>
</div>
	<div class="image_load_holder dont-unbind" data-image-src="{img id='js_photo_view_image' server_id=$aForms.server_id path='photo.url_photo' suffix='' file=$aForms.destination time_stamp=true title=$aForms.title return_url=true}" data-image-src-alt="{img id='js_photo_view_image' server_id=$aForms.server_id path='photo.url_photo' suffix='_1024' file=$aForms.destination time_stamp=true title=$aForms.title return_url=true}"></div>
	{if PHPFOX_IS_AJAX_PAGE}
	     <span class="_a_back"><i class="ico ico-arrow-left"></i>{_p var='back'}</span>
	{/if}
	
    {literal}
        <script>
            var preLoadImages = false;
            var preSetActivePhoto = false;
        </script>
    {/literal}
</div>
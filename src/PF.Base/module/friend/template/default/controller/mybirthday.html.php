<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Display the image details when viewing an image.
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Miguel Espinoza
 * @package  		Module_Friend
 * @version 		$Id: detail.class.php 254 2009-02-23 12:36:20Z Miguel_Espinoza $
 */
?>
<div class="item-container" id="collection-birthday-messages">
{foreach from=$aMessages item=aMessage name=iMessages}
    <article class="birthday {if is_int($phpfox.iteration.iMessages/2)}row1{else}row2{/if}{if $phpfox.iteration.iMessages == 1} row_first{/if}">
        <div class="item-outer">
            <a class="item-media-src" href="#">
                {img user=$aMessage suffix='_50' max_width=50 max_height=50}
            </a>
            <div class="item-inner">
                <div class="item-desc">
                    <div>{_p var='user_link_wished_you_a_happy_birthday' user=$aMessage}{if !empty($aMessage.birthday_message)}:{/if} </div>
                    <span class="extra_info" style="position:relative; left:25px; top:5px;">
                        {$aMessage.birthday_message|parse}
                    </span>
                    {if !empty($aMessage.file_path)}
                    <span style="margin-left:110px;">
                        {img id='js_photo_view_image' thickbox=true path='egift.url_egift' file=$aMessage.file_path suffix='_120' max_width=120 max_height=120 title=$aMessage.title time_stamp=true}
                    </span>
                    {/if}
                </div>
            </div>
        </div>
    </article>
{foreachelse}
	<div class="extra_info">
		{_p var='no_birthday_messages_found'}
	</div>
{/foreach}
</div>
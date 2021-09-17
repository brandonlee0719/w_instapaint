<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Forum
 * @version 		$Id: $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form class="form forum-app" method="post" action="#" id="js_forum_reply" onsubmit="return $Core.forum.submitQuickReply(this);">
	<div><input type="hidden" name="val[attachment]" class="js_attachment" value="" /></div>
	{if isset($iTotalPosts)}
		<div><input type="hidden" name="val[total_post]" value="{$iTotalPosts}" /></div>
	{/if}
    {if $aCallback !== false}
        <div><input type="hidden" name="val[group_id]" value="{$aCallback.item}" /></div>
    {/if}
    {if isset($iThreadId)}
        <div><input type="hidden" name="val[thread_id]" value="{$iThreadId}" /></div>
    {/if}
	<div class="form-group item-areabox-reply">
		<div id="js_forum_reply_post">
            {editor id='reply_text' placeholder='your_reply_dot_dot_dot'}
		</div>
	</div>
    {if Phpfox::isModule('captcha') && Phpfox::getUserParam('forum.enable_captcha_on_posting')}
        {module name='captcha.form' sType='forum'}
    {/if}
    <div class="form-footer">
	<div class="form_extra item-subscribe" style="display: block;">
        <div class="form-group ">
            <div class="privacy-block-content">
                <div class="item_is_active_holder" id="js_reply_subscribe">
                    <span class="js_item_active item_is_active"><input type="radio" name="val[is_subscribed]" value="1" class="v_middle"{value type='radio' id='is_subscribed' default='1' selected='true'}/>{_p var="yes"}</span>
                    <span class="js_item_active item_is_not_active"><input type="radio" name="val[is_subscribed]" value="0" class="v_middle"{value type='radio' id='is_subscribed' default='0'}/>{_p var="no"}</span>
                </div>
                <label>{_p var='subscribe'}</label>
            </div>
        </div>
	</div>
	<div class="item-reply">
    	<button id="js_forum_reply_submit_btn" type="submit" class="btn btn-primary">{_p var='post_reply'}</button>
    </div>
</div>
</form>
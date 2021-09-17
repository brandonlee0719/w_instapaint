<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Miguel Espinoza
 * @package  		Module_Contact
 * @version 		$Id: index.html.php 1870 2010-09-28 15:09:21Z Miguel_Espinoza $
 */

defined('PHPFOX') or exit('NO DICE!');

?>
{$sCreateJs}
<div class="main_break">
	<form method="post" action="{url link='contact'}" id="js_contact_form" onsubmit="{$sGetJsForm}" class="form">
        <div class="form-group">
            <label for="category_id">{required}{_p var='category'}</label>
            <select class="form-control" name="val[category_id]" id="category_id">
                <option value="">{_p var='select'}:</option>
                {foreach from=$aCategories item=sCategory}
                <option value="{$sCategory.title}"{value type='select' id='category_id' default=$sCategory.title}>{$sCategory.title|convert|clean}</option>
                {foreachelse}
                <option value="#">{_p var='currently_unavailable'}</option>
                {/foreach}

            </select>
        </div>

		{if Phpfox::isUser()}
			<div><input type="hidden" name="val[full_name]" id="full_name" value="{$sFullName}" size="30" /></div>
		{else}
            <div class="form-group">
                <label for="full_name">{required}{_p var='full_name'}</label>
                <input type="text" name="val[full_name]" id="full_name" value="{value type='input' id='full_name'}" size="30" />
            </div>
		{/if}

        <div class="form-group">
            <label for="subject">{required}{_p var='subject'}</label>
            <input class="form-control" type="text" name="val[subject]" id="subject" value="{value type='input' id='subject'}" size="30" />
        </div>

		{if Phpfox::isUser()}
			<div><input type="hidden" name="val[email]" id="email" value="{$sEmail}" size="30" /></div>
		{else}
        <div class="form-group">
            <label for="email">{required}{_p var='email'}</label>
            <input class="form-control" type="text" name="val[email]" id="email" value="{value type='input' id='email'}" size="30" />
        </div>
		{/if}
		<div class="table form-group">
			<div class="table_left">
				<label for="message">{required}{_p var='message'}:</label>
			</div>
			<div class="table_right">
				<textarea class="form-control" cols="60" rows="10" name="val[text]">{value id='text' type='textarea'}</textarea>
			</div>
			<div class="clear"></div>
		</div>
        <div class="checkbox">
        <label>
            <input type="checkbox" name="val[copy]" value="1"{value id='copy' type='checkbox' default='1'}/> {_p var='send_yourself_a_copy'}
        </label>
        </div>
		{if Phpfox::isModule('captcha') && Phpfox::getParam('contact.contact_enable_captcha')}
			{module name='captcha.form' sType=contact}
		{/if}

		<div class="form-group">
			<input type="submit" value="{_p var='submit'}" class="button btn-primary" />
			<div class="t_right"><span id="js_comment_process"></span></div>
		</div>
	</form>
	{if Phpfox::getParam('core.display_required')}
	<div class="form-group">
		{required} {_p var='required_fields'}
	</div>
	{/if}
</div>
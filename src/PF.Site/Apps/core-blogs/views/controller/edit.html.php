<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
{if count($aItems)}
{foreach from=$aItems key=iKey item=aBlog}
<div style="border-bottom:1px #ccc solid; margin-bottom:10px;">
<div class="go_left">
	<input type="checkbox" name="id[]" class="checkbox" value="{$aBlog.blog_id}" id="js_id_row{$aBlog.blog_id}" />
</div>
<div class="go_left">
	<a href="{url link='blog.add' id=$aBlog.blog_id}">{$aBlog.title|clean}</a>
	<div class="p_4">
		{if isset($aBlog.info)}{_p var='categories'}: {$aBlog.info} <br />{/if}
        {if isset($aBlog.tag_list)}{_p var='tags'}: {module name='tag.item' sType='my_blogs' sTags=$aBlog.tag_list iItemId=$aBlog.blog_id iUserId=$aBlog.user_id} <br />{/if}
        {_p var='status'}: <a href="#">{if $aBlog.post_status == 1}{_p var='published'}{else}{_p var='draft'}{/if}</a> <br />
		{_p var='date'}: {$aBlog.time_stamp|date:'core.global_update_time'} <br />
	</div>
</div>
<div class="t_right">
	<a href="#">{_p var='delete'}</a>
</div>
<div class="clear"></div>
</div>
{/foreach}

<br />

{pager}

{/if}

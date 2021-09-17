<?php 
defined('PHPFOX') or exit('NO DICE!');

?>
{if $bIsRegistration}
<div class="t_right p_top_10" style="font-size:10pt; font-weight:bold;">
	<a href="{$sNextUrl}">{_p var='skip_this_step'}</a>
</div>	
{/if}

<div class="">
	<div class="form-group">
		{if isset($aValid)}
		{if count($aValid)}
		{_p var='you_have_successfully_sent_an_invitation_to'}:
		<div class="p_4">
			<div class="label_flow" style="height:100px;">
			{foreach from=$aValid name=emails item=sEmail}
				<div class="{if is_int($phpfox.iteration.emails/2)}row1{else}row2{/if} {if $phpfox.iteration.emails == 1} row_first{/if}">{$sEmail}</div>
			{/foreach}
			</div>
		</div>
		<br />
		{/if}
		
		{if count($aInValid)}
		{_p var='the_following_emails_were_not_sent'}:
		<div class="p_4">
			<div class="label_flow" style="height:100px;">
			{foreach from=$aInValid name=emails item=sEmail}
				<div class="{if is_int($phpfox.iteration.emails/2)}row1{else}row2{/if} {if $phpfox.iteration.emails == 1} row_first{/if}">{$sEmail}</div>
			{/foreach}
			</div>
		</div>
		<br />
		{/if}	
		
		{if count($aUsers)}
		{_p var='the_following_users_are_already_a_member_of_our_community'}:
		<div class="p_4">
			<div class="label_flow" style="height:100px;">
			{foreach from=$aUsers name=users item=aUser}
				<div class="{if is_int($phpfox.iteration.users/2)}row1{else}row2{/if} {if $phpfox.iteration.users == 1} row_first{/if}" id="js_invite_user_{$aUser.user_id}">
			{if $aUser.user_id == Phpfox::getUserId()}
				{$aUser.email} - {_p var='that_s_you'}
			{else}			
				{$aUser.email} - {$aUser|user}{if !$aUser.friend_id} - <a href="#?call=friend.request&amp;user_id={$aUser.user_id}&amp;width=420&amp;height=250&amp;invite=true" class="inlinePopup" title="{_p var='add_to_friends'}">{_p var='add_to_friends'}</a>{/if}
			{/if}
				</div>
			{/foreach}
			</div>
		</div>	
		{/if}
		{else}
			{_p var='invite_your_friends_to_b_title_b' title=$sSiteTitle}
			<br />
			<br />
			{if Phpfox::getParam('invite.make_friends_on_invitee_registration')}
				{_p var='your_friend_will_automatically_be_added_to_your_friends_list_when_they_join'}
			{/if}
		{/if}
	</div>
	
	{plugin call='invite.template_controller_index_h3_start'}
	<div class="form-group">
		<h3>{_p var='email_your_friends'}</h3>
	</div>

	<form class="form" method="post" action="{if $bIsRegistration}{url link='invite.register'}{else}{url link='invite'}{/if}">
		<div class="">
            <label for="">{_p var='subject'}</label>
            {_p var='full_name_invites_you_to_title' full_name=$sFullName title=$sSiteTitle}
		</div>
		<div class="">
            <label for="">{_p var='from'}</label>
            {$sSiteEmail}
		</div>
		<div class="form-group">
            <label for="emails">{_p var='to'}</label>
            <textarea class="form-control autogrow" rows="3" id="emails" name="val[emails]" style="width:90%; height: auto;" onkeydown="$Core.resizeTextarea($(this));" onkeyup="$Core.resizeTextarea($(this));" autofocus></textarea>
            <p class="help-block">
                {_p var='separate_multiple_emails_with_a_comma'}
            </p>
		</div>
        <input type="submit" value="{_p var='send_invitation_s'}" class="btn btn-primary" />
	</form>
</div>
<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
{literal}
	<script type="text/javascript">
	$Core.loadStaticFile(getParam('sJsHome') + 'static/jscript/jquery/plugin/jquery.limitTextarea.js');
	function processList(sValue)
	{
		if (sValue == '')
		{
			return false;
		}
		
		if (sValue == 'create_new')
		{
			$('#js_list_options').hide(); 
			$('#js_add_new_list').show();
			return false;
		}
		
		return false;
	}

	function resetList()
	{
		$('#js_add_new_list').hide(); 
		$('#js_list_options').show();
		
		$('option').each(function()
		{
			this.selected = false;
		});
		
		return false;	
	}
	</script>
{/literal}


<form method="post" action="#" id="js_process_request" onsubmit="return false;">
{if $bInvite}
	<div>
		<input type="hidden" name="val[invite]" value="true" />
	</div>
{/if}
{if $bSuggestion}
	<div>
		<input type="hidden" name="val[suggestion]" value="true" />
	</div>
{/if}
{if $bPageSuggestion}
	<div>
		<input type="hidden" name="val[page_suggestion]" value="true" />
	</div>
{/if}
<div>
	<input type="hidden" name="val[user_id]" value="{$aUser.user_id}" />
</div>
<div class="item-user-request-outer">
<div class="item-user-request-cover" style="background-image:url('{$aUser.cover_photo_link}')"></div>
<div class="user-request-main">	
<div class="item-user-request-image" id="profile_picture_container">
	{img user=$aUser suffix='_50_square' max_width=50 max_height=50}
</div>
<div class="request_text item-user-request-info">
{if $sError}
	{if $sError == 'already_asked'}
		<div>{_p var='you_have_already_asked_full_name_to_be_your_friend' full_name=$aUser.full_name}</div>
	{elseif $sError == 'user_asked_already'}
		<div>{_p var='full_name_has_already_asked_to_be_your_friend' full_name=$aUser.full_name}</div>
		<div class="p_4">
			{_p var='would_you_like_to_accept_their_request_to_be_friends'}
			<div class="p_4 item-user-request-info-button">
				<input type="submit" onclick="$('#js_process_request').ajaxCall('friend.processRequest', 'type=yes&amp;user_id={$aUser.user_id}');" value="{_p var='yes'}" class="button btn-primary" />
				<input type="submit" onclick="$('#js_process_request').ajaxCall('friend.processRequest', 'type=no&amp;user_id={$aUser.user_id}');" value="{_p var='no'}" class="button btn-primary" />
			</div>
		</div>	
	{elseif $sError == 'same_user'}
		<div>{_p var='cannot_add_yourself_as_a_friend'}</div>
	{elseif $sError == 'already_friends'}
		<div>{_p var='you_are_already_friends_with_full_name' full_name=$aUser.full_name}</div>
	{/if}
</div>
{else}
	<div class="item-user-alert">{_p var='user_link_will_have_to_confirm_that_you_are_friends' user=$aUser}</div>
    {if $iMutualCount}
	<div class="item-user-mutualfriend">{if $iMutualCount == 1}{_p var='1_mutual_friend'}{else}{_p var='total_mutual_friends' total=$iMutualCount}{/if}</div>
    {/if}
    {if $sAdditionalInfo}
	<div class="item-user-info">{$sAdditionalInfo}</div>
    {/if}
	</div>
	</div>
	<div class="clear"></div>
	<div class="p_top_15 item-user-request-action">
		<div class="table_clear" id="container_submit_friend_request">
			<button type="submit" onclick="{if $bSuggestion}$('#js_friend_suggestion').hide(); $('#js_friend_suggestion_loader').show(); {/if}$('#js_process_image').show(); $('#js_process_request').ajaxCall('friend.addRequest');"  class="btn btn-primary btn-icon"><span class="ico ico-user1-plus-o "></span>{_p var='send_friend_request'}</button> <span id="js_process_image" style="display:none;">{img theme='ajax/add.gif'}</span>
		</div>
        <div class="alert alert-success ml-2 mr-2" id="friend_request_alert" style="display: none;">
            {_p var='friend_request_successfully_sent'}
        </div>
	</div>
{/if}
</div>
</form>
<script type="text/javascript">$Core.init();</script>
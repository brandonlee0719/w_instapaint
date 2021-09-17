
  <?php
/**
 *
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		phpFox
 * @package  		Poll
 *
 */

defined('PHPFOX') or exit('NO DICE!'); 

?>
<script type="text/javascript">
	{literal}
	function plugin_addFriendToSelectList()
	{
		$('#js_allow_list_input').show();
	}
	{/literal}
</script>
{$sCreateJs}
<div class="main_break"></div>
<div style="display:none;" class="placeholder">
    <div class="js_prev_block">
        <input type="text" tabindex="-1" name="val[answer][1][answer]" value="" size="30" class="form-control js_answers v_middle" />
        <span class="js_arrow_up_down ml-1">
            <i class="ico ico-arrows-move"></i>
        </span>
        <a href="javascript:void()" onclick="if(iMinAnswers == 0) {l} iMinAnswers = {$iMin}; {r} return removeAnswer(this);">
            <i class="ico ico-trash-alt-o""></i>
        </a>
    </div>
    <div class="js_next_block"></div>
</div>


<form  method="post" action="{if $bIsCustom}#{else}{url link='poll.add'}{if isset($aForms.poll_id)}id_{$aForms.poll_id}{/if}{/if}" name="js_poll_form" id="js_poll_form" onsubmit="{if $bIsCustom}if ({$sGetJsForm}) {l} $Core.poll.submitCustomPoll(this); {r} return false;{else}{$sGetJsForm}{/if}" {if Phpfox::getUserParam('poll.poll_can_upload_image')}enctype="multipart/form-data"{/if}>
	<div id="js_custom_privacy_input_holder">
	{if $bIsEdit}
		{module name='privacy.build' privacy_item_id=$aForms.poll_id privacy_module_id='poll'}	
	{/if}
	</div>
    <div><input type="hidden" name="val[attachment]" class="js_attachment" value="{value type='input' id='attachment'}" /></div>
	{if isset($aForms.poll_id) && isset($aForms.user_id)}
	<div><input type="hidden" name="val[poll_id]" value="{$aForms.poll_id}"></div>
	<div><input type="hidden" name="val[user_id]" value="{$aForms.user_id}"></div>
	{/if}
	{if $bIsCustom}	
	<div><input type="hidden" name="val[module_id]" value="{$sModuleId}"></div>
	{/if}
	
    <div class="form-group">
        <label for="question" class="text-capitalize">
            {required}{_p var='question'}:
        </label>
            <input class="form-control close_warning" tabindex="1" type="text" name="val[question]" id="question" value="{value type='input' id='question'}" size="40" />
    </div>

    <div class="form-group">
        <label class="text-capitalize">
            {_p var='description'}:
        </label>
        {editor id='description'}
    </div>

    {if Phpfox::getUserParam('poll.poll_can_upload_image')}
      {if !empty($aForms.current_image) && !empty($aForms.poll_id)}
        {module name='core.upload-form' type='poll' current_photo=$aForms.current_image id=$aForms.poll_id}
      {else}
        {module name='core.upload-form' type='poll' }
      {/if}
    {/if}

    <div class="form-group poll-app create">
        <label class="text-capitalize">
            {required}{_p var='answers'}:
        </label>
        <div class="sortable mb-2 dont-unbind-children">
            {if isset($aForms.answer) && count($aForms.answer)}
                {foreach from=$aForms.answer item=aAnswer name=iAnswer}
                <div class="placeholder sortable_item_{$phpfox.iteration.iAnswer}" id="sortable_item_{$phpfox.iteration.iAnswer}">
                    <div class="js_prev_block">
                        <input type="text" name="val[answer][{$phpfox.iteration.iAnswer}][answer]" value="{$aAnswer.answer|clean}" size="30" class="form-control js_answers close_warning" />
                        {if isset($aAnswer.answer_id)}
                               <input type="hidden" name="val[answer][{$phpfox.iteration.iAnswer}][answer_id]" class="hdnAnswerId close_warning" value="{$aAnswer.answer_id}">
                        {/if}
                        <span class="js_arrow_up_down ml-1">
                            <i class="ico ico-arrows-move"></i>
                        </span>
                        <a href="javascript:void()" onclick="return removeAnswer(this);">
                            <i class="ico ico-trash-alt-o""></i>
                        </a>
                    </div>
                    <div class="js_next_block"></div>
                </div>
                {/foreach}
            {else}
                {for $i = 1; $i <= $iTotalAnswers; $i++}
                    <div class="placeholder sortable_item_{$i}" id="sortable_item_{$i}">
                        <div class="js_prev_block">
                            <input type="text" tabindex="2" name="val[answer][{$i}][answer]" value="" size="30" class="form-control js_answers close_warning" />
                            <span class="js_arrow_up_down ml-1">
                                <i class="ico ico-arrows-move"></i>
                            </span>
                            <a href="javascript:void()" onclick="if(iMinAnswers == 0) {l} iMinAnswers = {$iMin}; {r} return removeAnswer(this);">
                                <i class="ico ico-trash-alt-o""></i>
                            </a>
                        </div>
                        <div class="js_next_block"></div>
                    </div>
                {/for}
            {/if}
        </div>
        <button class="btn btn-default text-uppercase btn-sm" onclick="if(iMaxAnswers == 0) {l} iMaxAnswers = {$iMaxAnswers}; {r} return appendAnswer(this);"><i class="ico ico-plus-circle-o mr-1"></i>{_p var='add_more'}</button>
    </div>

    <div class="form-group poll-app create votes">
        <div class="privacy-block-content">
            <div class="item_is_active_holder {if !isset($aForms.hide_vote)}item_selection_active{else}{if $aForms.hide_vote}item_selection_not_active{else}item_selection_active{/if}{/if}">
                <span class="js_item_active item_is_active"><input type="radio" name="val[hide_vote]" value="0" class="checkbox" style="vertical-align:middle;"{value type='checkbox' id='hide_vote' default='0' selected=true}/> {_p var='yes'}</span>
                <span class="js_item_active item_is_not_active"><input type="radio" name="val[hide_vote]" value="1" class="checkbox" style="vertical-align:middle;"{value type='checkbox' id='hide_vote' default='1'}/> {_p var='no'}</span>
            </div>
            <div class="inner">
                <label>{_p var='public_votes'}:</label>
                <div class="extra_info">
                    {_p var='displays_all_users_who_voted_and_what_choice_they_voted_for'}
                </div>
            </div>
        </div>
    </div> 

    <div class="form-group poll-app create votes">
        <div class="privacy-block-content">
            <div class="item_is_active_holder">
                <span class="js_item_active item_is_active"><input type="radio" name="val[is_multiple]" value="1" class="v_middle"{value type='radio' id='is_multiple' default='1'}/> {_p var='yes'}</span>
                <span class="js_item_active item_is_not_active"><input type="radio" name="val[is_multiple]" value="0" class="v_middle"{value type='radio' id='is_multiple' default='0' selected='true'}/> {_p var='no'}</span>
            </div>
            <label>
                {_p var='allow_multiple_choice'}:
            </label>
        </div>
    </div>
      <div class="form-group poll-app create votes poll-end-time">
          <div class="privacy-block-content">
              <div class="item_is_active_holder">
                  <span class="js_item_active js_poll_expire item_is_active"><input type="radio" name="val[enable_close]" value="1" class="v_middle"{value type='radio' id='enable_close' default='1'}/> {_p var='yes'}</span>
                  <span class="js_item_active js_poll_expire item_is_not_active"><input type="radio" name="val[enable_close]" value="0" class="v_middle"{value type='radio' id='enable_close' default='0' selected='true'}/> {_p var='no'}</span>
              </div>
              <div class="inner">
                  <label>{_p var='set_close_time'}:</label>
                  <div class="extra_info">
                      {_p var='poll_will_be_closed_after_this_time_and_nobody_can_vote_on_it'}
                  </div>
              </div>
          </div>
      </div>
      <div class="form-group js_poll_expire_select_time {if !isset($aForms.enable_close) || !$aForms.enable_close}hide{/if}">
          <div style="position: relative;" class="js_poll_select">
              {select_date prefix='close_' id='_close' start_year='current_year' end_year='+1' field_separator=' / ' field_order='MDY' default_all=true add_time=true start_hour='+1' time_separator='poll.time_separator'}
          </div>
      </div>
    {if !$bIsCustom && Phpfox::isModule('privacy')}
    <div class="form-group">
        <label>
            {_p var='privacy'}:
        </label>
        {module name='privacy.form' privacy_name='privacy' privacy_info='poll.control_who_can_see_this_poll' default_privacy='poll.default_privacy_setting'}
    </div>
        {if Phpfox::isModule('comment')}
            <div class="form-group-follow hidden">
                <div class="table_left">
                    {_p var='comment_privacy'}:
                </div>
                <div class="table_right">
                    {module name='privacy.form' privacy_name='privacy_comment' privacy_info='poll.control_who_can_comment_on_this_poll' privacy_no_custom=true}
                </div>
            </div>
        {/if}
    {/if}

    {if Phpfox::isModule('captcha') && Phpfox::getUserParam('poll.poll_require_captcha_challenge')}
    {module name='captcha.form' sType=poll}
    {/if}

    <div class="form-group poll-app create footer">
        <button type="submit" class="btn btn-primary js_poll_submit_button" name="submit_poll">{if isset($aForms.poll_id)}{_p var='update'}{else}{_p var='submit'}{/if}</button>
    </div>
    {if !$bIsCustom}
    <div class="hidden">
        <h3>{_p var='additional_options'}</h3>
        <div class="form-group-follow">
            <div class="table_left">
                {_p var='public_votes'}:
            </div>
            <div class="table_right">
                <div class="item_is_active_holder">
                    <span class="js_item_active item_is_active"><input type="radio" name="val[hide_vote]" value="0" class="checkbox" style="vertical-align:middle;"{value type='checkbox' id='hide_vote' default='0' selected=true}/> {_p var='yes'}</span>
                    <span class="js_item_active item_is_not_active"><input type="radio" name="val[hide_vote]" value="1" class="checkbox" style="vertical-align:middle;"{value type='checkbox' id='hide_vote' default='1'}/> {_p var='no'}</span>
                </div>
                <div class="extra_info">
                    {_p var='displays_all_users_who_voted_and_what_choice_they_voted_for'}
                </div>
            </div>
        </div>
        <div class="form-group-follow">
            <div class="table_left">
                {_p var='randomize_answers'}:
            </div>
            <div class="table_right">
                <div class="item_is_active_holder">
                    <span class="js_item_active item_is_active">
                        <input type="radio" name="val[randomize]" value="1" class="checkbox" onclick="$('.js_arrow_up_down').hide();" style="vertical-align:middle;"{value type='checkbox' id='randomize' default='1'}/>
                        {_p var='yes'}
                    </span>
                    <span class="js_item_active item_is_not_active">
                        <input type="radio" name="val[randomize]" value="0" class="checkbox" onclick="$('.js_arrow_up_down').show();" style="vertical-align:middle;"{value type='checkbox' id='randomize' default='0'}/>
                        {_p var='no'}
                    </span>
                </div>
            </div>
        </div>
        {plugin call='poll.template_controller_add_end'}

        <div class="table_clear">
            <ul class="table_clear_button">
                <li><input type="submit" name="submit_poll" value="{if isset($aForms.poll_id)}{_p var='update'}{else}{_p var='submit'}{/if}" class="button btn-primary js_poll_submit_button" /></li>
                {if Phpfox::getUserParam('poll.poll_can_edit_own_polls') && !$bIsCustom}
                <li><input type="submit" name="submit_design" value="{_p var='save_and_design_this_poll'}" class="button button_off" name="design" /></li>
                {if isset($aForms.poll_id)}
                <li><input type="button" value="{_p var='skip_and_design_this_poll'}" onclick="window.location.href = '{url link='poll.design' id=$aForms.poll_id}';" class="button button_off" /></li>
                {/if}
                {/if}
            </ul>
            <div class="clear"></div>
        </div>
    </div>
	{/if}
</form>
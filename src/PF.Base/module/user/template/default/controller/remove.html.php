<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="main_break">
    <div class="error_message">{_p var='are_you_sure_you_want_to_delete_your_account'}</div>
    {if Phpfox::isModule('friend')}
    <div class="table-responsive">
        <table class="table" width="100%">
            <tbody>
                <tr>
                    {foreach from=$aFriends item=aFriend name=friends}
                    <td align='center'>
                        {img id='sJsUserImage_'$aFriend.friend_id'' user=$aFriend suffix='_120_square' max_width=1200
                        max_height=120}
                        <br/>
                        {_p var='user_info_will_miss_you' user_info=$aFriend|user}
                    </td>
                    {/foreach}
                </tr>
            </tbody>
        </table>
    </div>
    {/if}
</div>
<div class="clear"></div>
<div class="main_break">
    <form class="form" action="{url link='user.remove.confirm'}" method="post">
        <div class="form-group">
            {if !empty($aReasons)}
            <label for="reason">{required}{_p var='why_are_you_deleting_your_account'}</label>
                {foreach from=$aReasons item=aReason name=reasons}
                <div class="p_2">
                    <label>
                        <input type="checkbox" name='val[reason][]' value="{$aReason.delete_id}" class="v_middle"/> {_p
                        var=$aReason.phrase_var}
                    </label>
                </div>
                {/foreach}
            {/if}

            <div class="form-group"></div>

            <div class="form-group">
                <label> {_p var='please_tell_us_why'}</label>
                <textarea  class="form-control" cols="40" rows="4" name="val[feedback_text]"></textarea>
            </div>

            {if !Phpfox::getUserBy('fb_user_id') && !Phpfox::getUserBy('janrain_user_id')}
            <div class="form-group">
                <label for="password">{_p var='enter_your_password'}</label>
                <input  class="form-control" type="password" id="password" name="val[password]" size="20" autocomplete="off"/>
            </div>
            {/if}
            <div class="form-group">
                <input type="submit"
                               data-message="{_p var='are_you_absolutely_sure_this_operation_cannot_be_undone' phpfox_squote=true}"
                               class="btn btn-danger sJsConfirm" value="{_p var='delete_my_account'}"/>
                <input type="button" class="btn button_off"
                               onclick="window.location='{url link='user.setting'}'"
                               value="{_p var='cancel_uppercase'}"/></li>
            </div>
        </div>
    </form>
</div>
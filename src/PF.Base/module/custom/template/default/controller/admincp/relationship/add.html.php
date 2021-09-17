<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<form class="form" action="{url link='admincp.custom.relationships'}" method="post">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">{_p var='add_status'}</div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="">{_p var='status_name'}</label>
                {if isset($aEdit)}
                {module name='language.admincp.form' type='text' id='new' var_name=$aEdit.phrase.new}
                {else}
                {module name='language.admincp.form' type='text' id='new'}
                {/if}
                <p class="help-block">
                    {_p var='you_can_add_a_language_phrase_if_you_enter_it_like_this'}: <br />
                    {l}phrase var='module.phrase_var'{r} <br />
                    {_p var='otherwise_the_script_will_create_the_language_phrase_for_you'}
                </p>
            </div>
            <div class="form-group">
                <label for="">{_p var='feed_when_confirmed'}</label>
                {if isset($aEdit)}
                {module name='language.admincp.form' type='text' id='feed_with' var_name=$aEdit.phrase.feed_with}
                {else}
                {module name='language.admincp.form' type='text' id='feed_with'}
                {/if}
                <p class="help-block">
                    {_p var='this_is_the_message_for_the_feed_when_the_relationship_has_been_confirmed'}
                </p>
            </div>

            <div class="form-group">
                <label for="">{_p var='feed_before_confirming'}</label>
                {if isset($aEdit)}
                {module name='language.admincp.form' type='text' id='feed_new' var_name=$aEdit.phrase.feed_new}
                {else}
                {module name='language.admincp.form' type='text' id='feed_new'}
                {/if}
                <p class="help-block">
                    {_p var='this_message_will_be_shown_in_the_feed_when_a_user_has_set_a_relationship'}
                </p>
            </div>

            <div class="form-group">
                <label>
                    {_p var='requires_confirmation'}
                </label>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="val[confirmation]" {if isset($aEdit) && $aEdit.confirmation == 1}checked="checked" {/if}>{_p var='if_this_field_is_enabled_this_relationship_status_requires_that_both_users_agree_on_displaying_their_relationship'}
                    </label>
                </div>
            </div>
            <div class="form-group">
                <p class="help-block">
                    {_p var='for_all_these_phrases_the_following_transformations_apply'}:
                    <br />{l}with_user_name{r} {_p var='user_name_of_the_receiving_party'}
                    <br />{l}with_full_name{r} {_p var='full_name_of_the_receiving_party'}
                    <br />{l}user_name{r} {_p var='sender_s_user_name'}
                    <br />{l}full_name{r} {_p var='sender_s_full_name'}
                    <br />{l}their{r} {_p var='sender_s_possessive_adjective_his_her'}
                </p>
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" value="{if isset($aEdit)} {_p var='edit_status'} {else}{_p var='add_status'}{/if}" class="btn btn-primary">
        </div>
    </div>
</form>
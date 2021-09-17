<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<form method="post" action="{url link='admincp.privacy'}" class="form">
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {_p var='add_new_privacy_rule'}
        </div>
    </div>
    <div class="panel-body">
            <div class="form-group">
                <label for="url">
                    {_p var='url'}:
                </label>
                <input class="form-control" type="text" id="url" name="val[url]" value="{value type='input' id='url'}" size="30" style="width:95%;" />
                <p class="help-block">
                    {_p var='provide_full_path'}
                </p>
            </div>
            <div class="form-group">
                <label>{_p var='user_groups'}</label>
                {foreach from=$aUserGroups item=aUserGroup}
                <div class="checkbox">
                    <label><input type="checkbox" name="val[user_group][]" value="{$aUserGroup.user_group_id}" /> {$aUserGroup.title|convert|clean}</label>
                </div>
                {/foreach}
                <p class="help-block">{_p var='select_a_user_group_this_rule_should_apply_to'}</p>
            </div>

            <div class="form-group">
                <label>{_p var='wildcard'}</label>
                <div class="item_is_active_holder">
                    <span class="js_item_active item_is_active"><input type="radio" name="val[wildcard]" value="1" {value type='radio' id='wildcard' default='1'}/> {_p var='yes'}</span>
                    <span class="js_item_active item_is_not_active"><input type="radio" name="val[wildcard]" value="0" {value type='radio' id='wildcard' default='0' selected='true'}/> {_p var='no'}</span>
                </div>
                <p class="help-block">{_p var='option_sub_section'}</p>
            </div>
            <div class="form-group">
                <input type="submit" value="{_p var='submit'}" class="btn btn-primary" />
            </div>
    </div>
</div>
</form>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">{_p var='rules'}</div>
    </div>
    <div class="panel-body">
        {if count($aRules)}
        <form method="post" action="{url link='admincp.ad'}" class="form">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w30"></th>
                            <th class="w30">{_p var='url'}</th>
                            <th>{_p var='user_groups'}</th>
                            <th>{_p var='wildcard'}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$aRules key=iKey item=aRule}
                        <tr>
                            <td class="t_center">
                                <a href="#" class="js_drop_down_link" title="{_p var='manage'}"></a>
                                <div class="link_menu">
                                    <ul class="dropdown-menu">
                                        <li><a href="{url link='admincp.privacy' delete=$aRule.rule_id}">{_p var='delete'}</a></li>
                                    </ul>
                                </div>
                            </td>
                            <td>{$aRule.url|clean}{if $aRule.wildcard}*{/if}</td>
                            <td>{$aRule.user_groups}</td>
                            <td>{if $aRule.wildcard}Yes{else}No{/if}</td>
                        </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
        </form>
        {else}
        <div class="well">
            {_p var='there_are_no_privacy_rules_at_the_moment'}
        </div>
        {/if}
    </div>
</div>
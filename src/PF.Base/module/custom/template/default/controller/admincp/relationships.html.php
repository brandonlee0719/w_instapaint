<?php
defined('PHPFOX') or exit('No dice!');
?>
<div class="block_content">
    <form action="{url link='admincp.custom.relationships'}" method="post">
        {if (isset($aStatuses) && is_array($aStatuses) && !empty($aStatuses))}
        <div class="table-responsive">
            <table class="table table-admin">
                <thead>
                <tr>
                    <th class="w30"></th>
                    <th> {_p var='status_name'} </th>
                    <th> {_p var='feed_when_confirmed'} </th>
                    <th> {_p var='feed_when_new'} </th>
                    <th> {_p var='confirmation'} </th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$aStatuses name=status item=aStatus}
                <tr class="{if is_int($phpfox.iteration.status/2)}tr{else}{/if}" >
                    <td>
                        <a href="#" class="js_drop_down_link" title="{_p var='Manage'}"></a>
                        <div class="link_menu">
                            <ul class="dropdown-menu">
                                <li><a href="{url link='admincp.custom.relationships' delete=$aStatus.relation_id}" class="sJsConfirm">{_p var='delete'}</a></li>
                                <li><a href="{url link='admincp.custom.relationship.add' id=$aStatus.relation_id}">{_p var='edit'}</a></li>
                            </ul>
                        </div>
                    </td>
                    <td> {if isset($aStatus.phrase.new)} {module name='language.admincp.form' type='label' id=$aStatus.relation_id var_name=$aStatus.phrase.new} {/if} </td>
                    <td> {if isset($aStatus.phrase.feed_with)} {module name='language.admincp.form' type='label' id=$aStatus.relation_id var_name=$aStatus.phrase.feed_with} {/if} </td>
                    <td> {if isset($aStatus.phrase.feed_new)} {module name='language.admincp.form' type='label' id=$aStatus.relation_id var_name=$aStatus.phrase.feed_new}  {/if} </td>
                    <td> <input type="checkbox" disabled="disabled" name="confirmation" {if $aStatus.confirmation == 1}checked="checked"{/if}> </td>
                </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
        {else}
        {_p var='no_relationship_statuses_have_been_added'}
        {/if}
    </form>
</div>
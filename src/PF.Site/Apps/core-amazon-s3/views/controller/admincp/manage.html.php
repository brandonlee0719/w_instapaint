<?php
defined('PHPFOX') or exit('NO DICE');
/**
 * @author Neil J.<neil@phpfox.com>
 */
?>

{if !$bIsValidKey}
{_p var="Your AWS access key and secret key are not valid."}
{else}
<div class="alert alert-warning">
    {_p var="Old files can not be moved to new bucket"}
</div>
<div class="panel panel-default">
    <div class="panel-heading">{_p var="Select a bucket"}:</div>
    <div class="panel-body">
        {if count($aBuckets)}
        <form action="{url link='admincp.amazons3.manage'}" method="post" enctype="multipart/form-data">
            {foreach from=$aBuckets key=sKey value=aBucket}
            <div class="radio">
                <label><input name="val[bucket]" type="radio" value="{$aBucket.name}" {if $aBucket.in_use} checked {/if}>{$aBucket.name}</label>
            </div>
            {/foreach}
            <div class="radio">
                <a href="javascript:void(0)"
                   onclick="tb_show('{_p var=\'Create a new bucket\'}', $.ajaxBox('amazons3.createBucket'));">{_p
                    var="Create a new bucket"}</a>
            </div>
            <div class="form-group">
                <input type="submit" name="val[submit]" value="{_p var='Save'}" class="btn btn-default">
            </div>
        </form>
        {else}
        No buckets found
        {/if}
    </div>
</div>
{/if}

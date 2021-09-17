<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		phpFox
 * @package 		phpFox
 */
 
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="panel panel-default">
    <div class="panel-body">
        {if count($aComments)}
            {foreach from=$aComments item=aRow}
            {template file='comment.block.entry'}
            {/foreach}
            {pager}
        {else}
            <div class="alert alert-info">
                {_p var='no_comments'}
            </div>
        {/if}
    </div>
</div>

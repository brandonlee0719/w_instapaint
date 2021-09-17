<?php

defined('PHPFOX') or exit('NO DICE!');

?>

{if isset($aFeed.friends_tagged) && $iTotal = count($aFeed.friends_tagged)}
    {_p('with')}&nbsp;
    {if ($iRemain = ($iTotal - 1)) > 1}
        {$aFeed.friends_tagged.0|user} {_p('and')}
        <div class="dropdown" style="display: inline-block;">
            <a href="#" role="button" data-toggle="dropdown">{$iRemain} {_p('others')}</a>
            <ul class="dropdown-menu">
                {foreach from=$aFeed.friends_tagged item=aUser key=iKey}
                    {if $iKey > 0}
                        <li class="item">{$aUser|user}</li>
                    {/if}
                {/foreach}
            </ul>
        </div>
    {else}
        {foreach from=$aFeed.friends_tagged item=aUser key=iKey}
            {$aUser|user} {if ($iKey+1) < $iTotal}{_p('and')} {/if}
        {/foreach}
    {/if}
{/if}

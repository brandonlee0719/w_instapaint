<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

{if $iStatus == 1}
    {_p var='this_site_is_very_concerned_about_security'}
{else}
    {if $iViewId == 1}
        {_p var='your_account_is_pending_approval'}
    {else}
        {_p var='your_account_has_been_dennied'}
    {/if}
{/if}
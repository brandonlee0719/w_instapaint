<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
{if $bAllowRegistration}
{template file='user.controller.register'}
{else}
{template file='user.controller.login'}
{/if}

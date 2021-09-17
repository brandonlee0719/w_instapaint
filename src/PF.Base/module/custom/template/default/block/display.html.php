<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

{foreach from=$aCustomMain item=aCustom}
  {if $sTemplate == 'info'}
    {if !empty($aCustom.value)}
      {module name='custom.block' data=$aCustom template=$sTemplate edit_user_id=$aUser.user_id}
    {/if}
  {else}
    {module name='custom.block' data=$aCustom template=$sTemplate edit_user_id=$aUser.user_id}
  {/if}
{/foreach}

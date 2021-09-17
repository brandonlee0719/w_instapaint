<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: currency.html.php 1883 2010-10-05 08:43:21Z Miguel_Espinoza $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>

{foreach from=$aCurrencies key=sCurrencyName item=aCurrencyItem}
<div class="input-group">
    <span class="input-group-addon" title="{_p var=$aCurrencyItem.name}">
        {$aCurrencyItem.symbol}
    </span>
    <input class="form-control" type="text" name="{$sCurrencyFieldName}[{$sCurrencyName}]" value="{if isset($aCurrencyItem.value)}{$aCurrencyItem.value|clean}{else}0{/if}" size="10" />
</div>
<br/>
{/foreach}

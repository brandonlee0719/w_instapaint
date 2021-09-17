<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: form.html.php 977 2009-09-12 15:29:04Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="form-group {if !PHPFOX_IS_TECHIE}hide{/if}">
	<label class="{if $bProductIsRequired}required{/if}" for="{if !$bUseClass}product_id{/if}">{_p var='product'}</label>
    <select name="val[product_id]" class="form-control" {if $bUseClass}class{else}id{/if}="product_id">
    {foreach from=$aProducts item=aProduct}
        <option value="{$aProduct.product_id}"{value type='select' id='product_id' default=$aProduct.product_id}>{$aProduct.title}</option>
    {/foreach}
    </select>
</div>
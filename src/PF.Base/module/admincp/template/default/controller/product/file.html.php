<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Admincp
 * @version 		$Id: file.html.php 1149 2009-10-07 10:14:46Z Raymond_Benc $
 */

defined('PHPFOX') or exit('NO DICE!');

?>
{if count($aNewProducts)}
<div class="table-responsive">
    <table class="table table-admin">
        <thead>
            <tr>
                <th>{_p var='product'}</th>
                <th>{_p var='version'}</th>
                <th class="w100">{_p var='action'}</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$aNewProducts key=iKey item=aProduct}
            <tr>
                <td>
                {if !empty($aProduct.url)}<a href="{$aProduct.url}" target="_blank">{/if}{$aProduct.title|clean}{if !empty($aProduct.url)}</a>{/if}
                    {if !empty($aProduct.description)}
                    <div class="extra_info">
                        {$aProduct.description|clean}
                    </div>
                    {/if}
                </td>
                <td class="t_center">{if empty($aProduct.version)}N/A{else}{$aProduct.version}{/if}</td>
                <td class="t_center">
                    <a href="{url link='admincp.product.file' install=$aProduct.product_id}" title="Click to install this product.">{_p var='install'}</a>
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
</div>
{else}
<div class="alert alert-empty" role="alert">
    {_p var='nothing_new_to_install'}.
</div>
{/if}
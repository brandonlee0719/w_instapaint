<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="egift-item">
    {if !empty($aInvoice.file_path)}
            <a href="{img file=$aInvoice.file_path server_id=$aInvoice.server_id suffix='' thickbox=true path='egift.url_egift' return_url=true}" class="thickbox">
        <div class="image-item">
        	    <span style="background-image: url({img file=$aInvoice.file_path server_id=$aInvoice.server_id suffix='_120' thickbox=true path='egift.url_egift' return_url=true});"></span>
        </div>
            </a>
    {/if}
    <div class="content-item">
        <span class="user-from">{$aInvoice.send_from|user}</span> <span>{_p var='sent__l'}</span> <span class="user-to">{$aInvoice.send_to|user}</span> <span>{_p var='a_gift'}:</span> <span class="gift-title">{$aInvoice.title|clean}</span>
    </div>
</div>
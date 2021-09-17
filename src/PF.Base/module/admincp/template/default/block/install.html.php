{if isset($aNewProducts) && !empty($aNewProducts)}
<div class="block">
    <div class="title">Installations</div>
    <div class="content">
        {foreach from=$aNewProducts item=product}
        <div class="clearfix item-separated">
            <div class="pull-left">
                <strong>{$product.title|clean}</strong> - <span class="">{$product.version}</span>
            </div>
            <div class="pull-right">
                <a class="btn btn-danger btn-xs" href="{url link='admincp.product.file' install=$product.product_id}">{_p var='install'}</a>
            </div>
        </div>
        {/foreach}
    </div>
</div>
{/if}
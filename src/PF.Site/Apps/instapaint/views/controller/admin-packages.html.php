{literal}

{/literal}

<div class="alert alert-info" role="alert"><i class="fas fa-images"></i> In this section, you can create, edit, and delete packages with details such as frame sizes, frame types, and shipping types.</div>

{if $frameSizes}
<h2>Frame Sizes</h2>
<table class="table table-bordered table-hover">
    <thead>
    <tr>
        <th style="width: 20%" scope="col">Frame size</th>
        <th style="width: 70%" scope="col">Description</th>
        <th scope="col" style="text-align: right; width: 10%">Price (USD)</th>
    </tr>
    </thead>
    <tbody>
    {foreach from=$frameSizes name=frameSize item=frameSize}
    <tr style="cursor: pointer" onclick="top.location.href='/admin-dashboard/packages/frame-size/edit/{$frameSize.frame_size_id}/'">
        <td scope="row">{$frameSize.name_phrase}</td>
        <td>{$frameSize.description_phrase}</td>
        <td style="text-align: right">${$frameSize.price_usd}</td>
    </tr>
    {/foreach}
    </tbody>
</table>
<br>
{/if}

{if $frameTypes}
<h2>Frame Types</h2>
<table class="table table-bordered table-hover">
    <thead>
    <tr>
        <th style="width: 20%" scope="col">Frame type</th>
        <th style="width: 70%" scope="col">Description</th>
        <th scope="col" style="text-align: right; width: 10%">Price (USD)</th>
    </tr>
    </thead>
    <tbody>
    {foreach from=$frameTypes name=frameType item=frameType}
    <tr style="cursor: pointer" onclick="top.location.href='/admin-dashboard/packages/frame-type/edit/{$frameType.frame_type_id}/'">
        <td scope="row">{$frameType.name_phrase}</td>
        <td>{$frameType.description_phrase}</td>
        <td style="text-align: right">${$frameType.price_usd}</td>
    </tr>
    {/foreach}
    </tbody>
</table>
<br>
{/if}

{if $shippingTypes}
<h2>Shipping Types</h2>
<table class="table table-bordered table-hover">
    <thead>
    <tr>
        <th style="width: 20%" scope="col">Shipping type</th>
        <th style="width: 70%" scope="col">Description</th>
        <th scope="col" style="text-align: right; width: 10%">Price (USD)</th>
    </tr>
    </thead>
    <tbody>
    {foreach from=$shippingTypes name=shippingType item=shippingType}
    <tr style="cursor: pointer" onclick="top.location.href='/admin-dashboard/packages/shipping-type/edit/{$shippingType.shipping_type_id}/'">
        <td scope="row">{$shippingType.name_phrase}</td>
        <td>{$shippingType.description_phrase}</td>
        <td style="text-align: right">${$shippingType.price_usd}</td>
    </tr>
    {/foreach}
    </tbody>
</table>
<br>
{/if}

{if $packages}
<h2>Packages</h2>
<table class="table table-bordered table-hover">
    <thead>
    <tr>
        <th scope="col" style="width: 30%">Frame size</th>
        <th scope="col" style="width: 30%">Frame type</th>
        <th scope="col" style="width: 30%">Shipping type</th>
        <th scope="col" style="text-align: right; width: 10%">Total price</th>
    </tr>
    </thead>
    <tbody>
    {foreach from=$packages name=package item=package}
    <tr style="cursor: pointer" onclick="top.location.href='/admin-dashboard/packages/delete/{$package.package_id}/'">
        <td>{$package.frame_size_name} (${$package.frame_size_price})</td>
        <td>{$package.frame_type_name} (${$package.frame_type_price})</td>
        <td>{$package.shipping_type_name} (${$package.shipping_type_price})</td>
        <td style="text-align: right">${$package.total_price}</td>
    </tr>
    {/foreach}
    </tbody>
</table>
<br>
{/if}

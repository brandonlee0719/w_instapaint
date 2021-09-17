{literal}
<style>
    .order {
        border: 1px #ddd solid;
        border-radius: 5px;
        margin-bottom: 15px;
    }
    .order:nth-last-child(2) {
        margin-bottom: 0;
    }
    .order-photo {
        max-width: 100%;
        max-height: 400px;
    }
    .order-header {
        padding: 15px 0;

    }
    .order-body {
        padding: 20px 0;
    }
    .order-header {
        border-bottom: 1px solid lightgrey;
        background-color: #f6f6f6;
        color: #555;
    }
    .order-header > div:last-child {
        text-align: right;
    }
    .order-header-title {
        margin-bottom: 2px;
        text-transform: uppercase;
        font-size: 11px;
    }
    .order-body-title {
        font-size: 16px;
        color: #000;
        text-transform: capitalize;
    }
    .order-body-description {
        margin-bottom: 12px;
    }
    .order-photo-container {
        text-align: center;
    }
    .order-summary-data {
        line-height: 22px;
    }
    .order a:hover {
        text-decoration: underline;
    }
    a.btn:hover {
        text-decoration: none;
    }
</style>
{/literal}

<div class="alert alert-info" role="alert">On this page you can see all the available orders on Instapaint. You can check their details and take the orders you want to paint.</div>

{if $orders.open}
{foreach from=$orders.open name=order item=order}
<div class="order container-fluid">
    <div class="row order-header">
        <div class="col-md-4">
            <div class="order-header-title">Order placed</div>
            <div class="order-header-content">{$order.created_timestamp|convert_time}</div>
        </div>

        <div class="col-md-6">
            <div class="order-header-title">Ship to</div>
            <div class="order-header-content">{$order.order_details.shipping_address.full_name}</div>
        </div>
        <div class="col-md-2">
            <div class="order-header-title">Order # {$order.order_id}</div>

        </div>
    </div>

    <div class="row order-body">
        <div class="col-md-3 order-photo-container">
            <img class="order-photo" src="{$order.photo_path}">
        </div>
        <div class="col-md-9">

            <div class="order-body-title">Frame size</div>
            <div class="order-body-description">{$order.order_details.package.frame_size_name} - {$order.order_details.package.frame_size_description}</div>

            <div class="order-body-title">Frame type</div>
            <div class="order-body-description">{$order.order_details.package.frame_type_name} - {$order.order_details.package.frame_type_description}</div>

            <div class="order-body-title">Shipping</div>
            <div class="order-body-description">{$order.order_details.package.shipping_type_name} - {$order.order_details.package.shipping_type_description}</div>

            <div class="order-body-title">Painting style</div>
            <div class="order-body-description">{if $order.style_name}
                {$order.style_name}<br>
                <img style="max-width: 120px" src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails/{$order.style}.jpg">

                {else}--{/if}</div>

            <div class="order-body-title">Order notes</div>
            <div class="order-body-description">{if $order.order_notes}{$order.order_notes}{else}--{/if}</div>

            <div class="order-body-title">Shipping address</div>
            <div class="order-body-description shipping-address-data">
                {$order.order_details.shipping_address.full_name}<br>
                {$order.order_details.shipping_address.street_address}<br>
                {if $order.order_details.shipping_address.street_address_2}{$order.order_details.shipping_address.street_address_2}<br>{/if}
                {$order.order_details.shipping_address.city}, {$order.order_details.shipping_address.state_province_region} {$order.order_details.shipping_address.zip_code}<br>
                {$order.order_details.shipping_address.country_name}<br>
            </div>

            <div class="alert alert-warning" role="alert">
                Click the button below to take this order and start painting this photo!<br><br><a href="/client-dashboard/orders/pay/{$order.order_id}/" type="button" class="btn btn-primary">Take this order</a>
            </div>
        </div>
    </div>
</div>
{/foreach}
{/if}

{if !$orders.open && !$orders.completed && !$orders.pending_payment}
<p>You don't have any orders yet. Click the button below to place your first order!</p>
<a href="/client-dashboard/orders/add/" type="button" class="btn btn-primary">Create a new order</a>
{/if}

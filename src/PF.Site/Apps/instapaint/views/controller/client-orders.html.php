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
    .photo-title {
        font-size: 16px;
        text-transform: capitalize;
        color: #555;
        margin-bottom: 4px;
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

<div class="alert alert-info" role="alert"><i class="fas fa-boxes"></i> On this page you can manage all the orders that you've placed on InstaPaint. You can also <strong><a href="/client-dashboard/orders/add/">create new orders here.</a></strong></div>
{if $orders.open || $orders.completed || $orders.pending_payment}
<div style="text-align: center"">
<a type="button" class="btn btn-primary" href="/client-dashboard/orders/add/">Create a new order</a>
<hr>
{/if}

</div>
{if $orders.pending_payment}
<h2 id="pending-payment-orders-section">Pending Payment Orders</h2>
<div class="alert alert-info" role="alert"><i class="far fa-money-bill-alt"></i> These orders require a payment to be made.</div>
{foreach from=$orders.pending_payment name=order item=order}
<div class="order container-fluid">
    <div class="row order-header">
        <div class="col-md-2">
            <div class="order-header-title">Order placed</div>
            <div class="order-header-content">{$order.created_timestamp|convert_time}</div>
        </div>
        <div class="col-md-2">
            <div class="order-header-title">Total</div>
            <div class="order-header-content">{$order.price_with_discount_formatted}</div>
        </div>
        <div class="col-md-6">
            <div class="order-header-title">Ship to</div>
            <div class="order-header-content">{$order.order_details.shipping_address.full_name}</div>
        </div>
        <div class="col-md-2">
            <div class="order-header-title">Order # {$order.order_id}</div>
            <div class="order-header-content"><a href="/client-dashboard/order/{$order.order_id}/">Order Details</a> | <a href="/client-dashboard/invoice/{$order.order_id}/">Invoice</a></div>
        </div>
    </div>

    <div class="row order-body">
        <div class="col-md-3 order-photo-container">
            <img class="order-photo" src="{$order.photo_path}">
        </div>
        <div class="col-md-9">

            {if $order.status_name == 'Pending Payment'}
            <div class="alert alert-warning" role="alert">
                <div class="order-body-title">Pending Payment</div>
                Please complete your payment for this order. After your payment is made we will proceed with the fulfillment of your order.<br><br><a href="/client-dashboard/orders/pay/{$order.order_id}/" type="button" class="btn btn-danger">Make Payment</a>
            </div>
            {else}
            <div class="order-body-title">Order status</div>
            <div class="order-body-description">{$order.status_name} - {$order.status_description}</div>
            {/if}

            {if $order.is_expedited == '1'}
            <div class="alert alert-info" role="alert">
                <i class="fas fa-fast-forward"></i> Expedited Service Requested for {$order.expedited_days}
            </div>
            {/if}

            <div class="order-body-title">Frame size</div>
            <div class="order-body-description">{$order.order_details.package.frame_size_name} - {$order.order_details.package.frame_size_description}</div>

            <div class="order-body-title">Frame type</div>
            <div class="order-body-description">{$order.order_details.package.frame_type_name} - {$order.order_details.package.frame_type_description}</div>

            <div class="order-body-title">Shipping</div>
            <div class="order-body-description">{$order.order_details.package.shipping_type_name} - {$order.order_details.package.shipping_type_description}</div>

            <div class="order-body-title">Order summary</div>
            <div class="order-summary-data">
                <span id="summary-frame-size">Frame Size:</span> {$order.order_details.package.frame_size_name} ({$order.order_details.package.frame_size_description}):<span class="pull-right" id="summary-frame-size-price">{$order.frame_size_price_formatted}</span><br>
                <span id="summary-frame-type">Frame Type:</span> {$order.order_details.package.frame_type_name} ({$order.order_details.package.frame_type_description}):<span class="pull-right" id="summary-frame-type-price">{$order.frame_type_price_formatted}</span><br>
                <span id="summary-shipping-type">Shipping Type:</span> {$order.order_details.package.shipping_type_name} ({$order.order_details.package.shipping_type_description}):<span class="pull-right" id="summary-shipping-type-price">{$order.shipping_type_price_formatted}</span><br>
                <span id="summary-faces-line"><span id="summary-faces">Faces ({$order.faces}):</span><span class="pull-right" id="summary-faces-price">{$order.faces_price_formatted}</span><br></span>
                <span id="summary-style-line"><span id="summary-style">Style ({$order.order_details.style.name}):</span><span class="pull-right" id="summary-style-price">${$order.order_details.style.price_str}</span><br></span>
                {if $order.is_expedited == '1'}<span id="summary-expedited-service-line"><span id="summary-expedited-service">Expedited Service:</span><span class="pull-right" id="summary-expedited-service-price">$50.00</span><br></span>{/if}
                {if $order.order_details.discount}
                <span id="discount-line"><span id="summary-discount">Discount: {$order.order_details.discount.name} ({$order.order_details.discount.discount_percentage}% Off):</span><span class="pull-right" id="summary-discount-price">-{$order.discount_price_formatted}</span><br></span>
                {/if}
                <span id="summary-total"><strong>Grand Total:</strong></span><span class="pull-right"><strong id="summary-total-price">{$order.price_with_discount_formatted}</strong></span>
            </div>
        </div>
    </div>
</div>
{/foreach}
{/if}

{if $orders.open}
{if $orders.pending_payment}
<br>
<hr>
{/if}
<h2 id="open-orders-section">Open Orders</h2>
<div class="alert alert-info" role="alert"><i class="fas fa-dolly-flatbed"></i> These orders are open and in progress.</div>
{foreach from=$orders.open name=order item=order}
<div class="order container-fluid">
    <div class="row order-header">
        <div class="col-md-2">
            <div class="order-header-title">Order placed</div>
            <div class="order-header-content">{$order.created_timestamp|convert_time}</div>
        </div>
        <div class="col-md-2">
            <div class="order-header-title">Total</div>
            <div class="order-header-content">{$order.price_with_discount_formatted}</div>
        </div>
        <div class="col-md-6">
            <div class="order-header-title">Ship to</div>
            <div class="order-header-content">{$order.order_details.shipping_address.full_name}</div>
        </div>
        <div class="col-md-2">
            <div class="order-header-title">Order # {$order.order_id}</div>
            <div class="order-header-content"><a href="/client-dashboard/order/{$order.order_id}/">Order Details</a> | <a href="/client-dashboard/invoice/{$order.order_id}/">Invoice</a></div>
        </div>
    </div>

    <div class="row order-body">
        <div class="col-md-3 order-photo-container">
            <img class="order-photo" src="{$order.photo_path}">
        </div>
        <div class="col-md-9">
            {if $order.painter_user_name}
            <div class="order-body-title">Who is painting it</div>
            <a href="/{$order.painter_user_name}/" target="_blank" style="display: inline-block">
                {if $order.painter_user_image}
                <img src="/PF.Base/file/pic/user/{$order.painter_user_image}" style="border-radius: 100%; max-width: 70px; margin-top: 3px; margin-bottom: 3px">
                {/if}
                <div class="order-body-description" style="text-align: center">{$order.painter_full_name}</div>
            </a>
            {/if}

            {if $order.is_expedited == '1'}
            <div class="alert alert-info" role="alert">
                <i class="fas fa-fast-forward"></i> Expedited Service Requested for {$order.expedited_days}
            </div>
            {/if}

            {if $order.status_name == 'Pending Payment'}
            <div class="alert alert-warning" role="alert">
                <div class="order-body-title">Pending Payment</div>
                Please complete your payment for this order. After your payment is made we will proceed with the fulfillment of your order.<br><br><a href="/client-dashboard/orders/pay/{$order.order_id}/" type="button" class="btn btn-danger">Make Payment</a>
            </div>
            {else}
                <div class="order-body-title">Order status</div>
                <div class="order-body-description">{$order.status_name} - {$order.status_description}</div>
            {/if}

            <div class="order-body-title">Frame size</div>
            <div class="order-body-description">{$order.order_details.package.frame_size_name} - {$order.order_details.package.frame_size_description}</div>

            <div class="order-body-title">Frame type</div>
            <div class="order-body-description">{$order.order_details.package.frame_type_name} - {$order.order_details.package.frame_type_description}</div>

            <div class="order-body-title">Shipping</div>
            <div class="order-body-description">{$order.order_details.package.shipping_type_name} - {$order.order_details.package.shipping_type_description}</div>

            <div class="order-body-title">Order summary</div>
            <div class="order-summary-data">
                <span id="summary-frame-size">Frame Size:</span> {$order.order_details.package.frame_size_name} ({$order.order_details.package.frame_size_description}):<span class="pull-right" id="summary-frame-size-price">{$order.frame_size_price_formatted}</span><br>
                <span id="summary-frame-type">Frame Type:</span> {$order.order_details.package.frame_type_name} ({$order.order_details.package.frame_type_description}):<span class="pull-right" id="summary-frame-type-price">{$order.frame_type_price_formatted}</span><br>
                <span id="summary-shipping-type">Shipping Type:</span> {$order.order_details.package.shipping_type_name} ({$order.order_details.package.shipping_type_description}):<span class="pull-right" id="summary-shipping-type-price">{$order.shipping_type_price_formatted}</span><br>
                <span id="summary-faces-line"><span id="summary-faces">Faces ({$order.faces}):</span><span class="pull-right" id="summary-faces-price">{$order.faces_price_formatted}</span><br></span>
                <span id="summary-style-line"><span id="summary-style">Style ({$order.order_details.style.name}):</span><span class="pull-right" id="summary-style-price">${$order.order_details.style.price_str}</span><br></span>
                {if $order.order_details.discount}
                    <span id="discount-line"><span id="summary-discount">Discount: {$order.order_details.discount.name} ({$order.order_details.discount.discount_percentage}% Off):</span><span class="pull-right" id="summary-discount-price">-{$order.discount_price_formatted}</span><br></span>
                {/if}
                <span id="summary-total"><strong>Grand Total:</strong></span><span class="pull-right"><strong id="summary-total-price">{$order.price_with_discount_formatted}</strong></span>
            </div>
        </div>
    </div>
</div>
{/foreach}
{/if}

{if $orders.completed}
    {if $orders.open || $orders.pending_payment}
    <br>
    <hr>
    {/if}
<h2 id="completed-orders-section">Completed Orders</h2>
<div class="alert alert-success" role="alert"><i class="fas fa-truck-loading"></i> These orders have been completed and shipped, if you haven't received them yet, they are on the way!</div>
{foreach from=$orders.completed name=order item=order}
<div class="order container-fluid">
    <div class="row order-header">
        <div class="col-md-2">
            <div class="order-header-title">Order placed</div>
            <div class="order-header-content">{$order.created_timestamp|convert_time}</div>
        </div>
        <div class="col-md-2">
            <div class="order-header-title">Order shipped</div>
            <div class="order-header-content">{$order.shipped_timestamp|convert_time}</div>
        </div>
        <div class="col-md-6">
            <div class="order-header-title">Total</div>
            <div class="order-header-content">{$order.price_with_discount_formatted}</div>
        </div>
        <div class="col-md-2">
            <div class="order-header-title">Order # {$order.order_id}</div>
            <div class="order-header-content"><a href="/client-dashboard/order/{$order.order_id}/">Order Details</a> | <a href="/client-dashboard/invoice/{$order.order_id}/">Invoice</a></div>
        </div>
    </div>

    <div class="row order-body">
        <div class="col-md-3 order-photo-container">
            <div class="photo-title">Original Photo</div>
            <img class="order-photo" src="{$order.photo_path}"><br><br>
            <div class="photo-title">Oil Painting</div>
            <img class="order-photo" src="{$order.painting_path}">
        </div>
        <div class="col-md-9">
            <div class="alert alert-success">

                <i class="fas fa-check"></i> This order has been shipped.<br><br>

            <div class="order-body-title" style="margin-bottom: 5px">Shipping notes</div>
            <div class="order-body-description" style="white-space: pre-wrap; margin-bottom: 0">{$order.shipping_notes}</div>
            </div>

            {if $order.is_expedited == '1'}
            <div class="alert" role="alert">
                <i class="fas fa-fast-forward"></i> Expedited Service Requested for {$order.expedited_days}
            </div>
            {/if}

            <div class="order-body-title">Painted by</div>
            <a href="/{$order.painter_user_name}/" target="_blank" style="display: inline-block">
                {if $order.painter_user_image}
                <img src="/PF.Base/file/pic/user/{$order.painter_user_image}" style="border-radius: 100%; max-width: 70px; margin-top: 3px; margin-bottom: 3px">
                {/if}
                <div class="order-body-description" style="text-align: center">{$order.painter_full_name}</div>
            </a>

            <div class="order-body-title">Frame size</div>
            <div class="order-body-description">{$order.order_details.package.frame_size_name} - {$order.order_details.package.frame_size_description}</div>

            <div class="order-body-title">Frame type</div>
            <div class="order-body-description">{$order.order_details.package.frame_type_name} - {$order.order_details.package.frame_type_description}</div>

            <div class="order-body-title">Shipping</div>
            <div class="order-body-description">{$order.order_details.package.shipping_type_name} - {$order.order_details.package.shipping_type_description}</div>

            <div class="order-body-title">Order summary</div>
            <div class="order-summary-data">
                <span id="summary-frame-size">Frame Size:</span> {$order.order_details.package.frame_size_name} ({$order.order_details.package.frame_size_description}):<span class="pull-right" id="summary-frame-size-price">{$order.frame_size_price_formatted}</span><br>
                <span id="summary-frame-type">Frame Type:</span> {$order.order_details.package.frame_type_name} ({$order.order_details.package.frame_type_description}):<span class="pull-right" id="summary-frame-type-price">{$order.frame_type_price_formatted}</span><br>
                <span id="summary-shipping-type">Shipping Type:</span> {$order.order_details.package.shipping_type_name} ({$order.order_details.package.shipping_type_description}):<span class="pull-right" id="summary-shipping-type-price">{$order.shipping_type_price_formatted}</span><br>
                <span id="summary-faces-line"><span id="summary-faces">Faces ({$order.faces}):</span><span class="pull-right" id="summary-faces-price">{$order.faces_price_formatted}</span><br></span>
                <span id="summary-style-line"><span id="summary-style">Style ({$order.order_details.style.name}):</span><span class="pull-right" id="summary-style-price">${$order.order_details.style.price_str}</span><br></span>
                {if $order.is_expedited == '1'}<span id="summary-expedited-service-line"><span id="summary-expedited-service">Expedited Service:</span><span class="pull-right" id="summary-expedited-service-price">$50.00</span><br></span>{/if}
                {if $order.order_details.discount}
                <span id="discount-line"><span id="summary-discount">Discount: {$order.order_details.discount.name} ({$order.order_details.discount.discount_percentage}% Off):</span><span class="pull-right" id="summary-discount-price">-{$order.discount_price_formatted}</span><br></span>
                {/if}
                <span id="summary-total"><strong>Grand Total:</strong></span><span class="pull-right"><strong id="summary-total-price">{$order.price_with_discount_formatted}</strong></span>
            </div>
        </div>
    </div>
</div>
{/foreach}
{/if}

{if $orders.open || $orders.completed || $orders.pending_payment}
<hr>
<div style="text-align: center"">
<a type="button" class="btn btn-primary" href="/client-dashboard/orders/add/">Create a new order</a>
{/if}

{if !$orders.open && !$orders.completed && !$orders.pending_payment}
<p>You don't have any orders yet. Click the button below to place your first order!</p>
<a href="/client-dashboard/orders/add/" type="button" class="btn btn-primary">Create a new order</a>
{/if}

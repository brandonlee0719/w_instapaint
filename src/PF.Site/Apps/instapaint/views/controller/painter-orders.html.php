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
    .painting-photo {
        max-width: 100%;
        max-width: 250px;
        margin-top: 5px;
    }
</style>

{/literal}

{if $orderLimitReached}
<div class="alert alert-info" role="alert"><i class="fas fa-stopwatch"></i> <strong>Daily Limit Reached</strong><br> You can place up to {$maxDailyOrders} new orders per day. Come back later to find more orders! <a href="/painter-dashboard/orders/">Click here to see your current orders.</a> </div>
{/if}

{if $orders.open}
<h2 id="open-orders-section">Open Orders</h2>
<div class="alert alert-info" role="alert"><i class="far fa-image"></i> These are the orders you are currently painting.</div>

{foreach from=$orders.open name=order item=order}
<div class="order container-fluid">
    <div class="row order-header">
        <div class="col-md-2">
            <div class="order-header-title">Order placed</div>
            <div class="order-header-content">{$order.created_timestamp|convert_time}</div>
        </div>
        <div class="col-md-2">
            <div class="order-header-title">Client</div>
            <div class="order-header-content"><a target="_blank" href="/{$order.client_user_name}">{$order.client_full_name}</a></div>
        </div>
        {if $order.reviewed_timestamp}
        <div class="col-md-2">
            <div class="order-header-title">Order taken</div>
            <div class="order-header-content">{$order.assigned_timestamp|convert_time}</div>
        </div>
        <div class="col-md-4">
            <div class="order-header-title">Order reviewed</div>
            <div class="order-header-content">{$order.reviewed_timestamp|convert_time}</div>
        </div>
        {else}
        <div class="col-md-6">
            <div class="order-header-title">Order taken</div>
            <div class="order-header-content">{$order.assigned_timestamp|convert_time}</div>
        </div>
        {/if}
        <div class="col-md-2">
            <div class="order-header-title">Order #</div>
            <div class="order-header-content">{$order.order_id}</div>
        </div>
    </div>

    <div class="row order-body">
        <div class="col-md-3 order-photo-container">
            <img class="order-photo" src="{$order.photo_path}"><br><br>
            <a href="{$order.original_photo_path}" download="instapaint-order-{$order.order_id}" type="button" class="btn btn-default">Download image</a>
        </div>
        <div class="col-md-9">
            {if $order.is_denied}
            <div class="alert alert-danger">
                <div class="order-body-title">Admin feedback</div>
                <div class="order-body-description" style="white-space: pre-wrap; margin-bottom: 0">{$order.feedback}</div>
            </div>
            {/if}

            {if $order.is_expedited == '1'}
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-fast-forward"></i> Expedited Service Requested for {$order.expedited_days}
            </div>
            {/if}

            <div class="alert alert-info" role="alert"><i class="fas fa-exclamation-circle"></i> Do not ship an order until it has been approved. Click the blue button to complete the order.</strong></div>

            <div class="order-body-title">Frame size</div>
            <div class="order-body-description">{$order.order_details.package.frame_size_name} - {$order.order_details.package.frame_size_description}</div>

            <div class="order-body-title">Frame type</div>
            <div class="order-body-description">{$order.order_details.package.frame_type_name} - {$order.order_details.package.frame_type_description}</div>

            <div class="order-body-title">Shipping</div>
            <div class="order-body-description">{$order.order_details.package.shipping_type_name} - {$order.order_details.package.shipping_type_description}</div>

            <div class="order-body-title">Number of faces</div>
            <div class="order-body-description">{$order.faces}</div>

            <div class="order-body-title">Painting style</div>
            <div class="order-body-description">{if $order.style_name}
                {$order.style_name}<br>
                <img style="max-width: 120px" src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails-200px/{$order.style}.jpg">

                {else}--{/if}</div>

            {if $order.order_notes}
            <div class="order-body-title">Order notes</div>
            <div class="order-body-description">{$order.order_notes}</div>
            {/if}

            <div class="order-body-title">Client info</div>
            <div class="order-body-description">
                Name: {$order.client_full_name}<br>
                Phone number: ({$order.order_details.shipping_address.dial_code_iso}) {$order.order_details.shipping_address.phone_number}<br>
                Email: {$order.client_email}
            </div>

            <div class="order-body-title">Shipping address</div>
            <div class="order-body-description shipping-address-data">
                {$order.order_details.shipping_address.full_name}<br>
                {$order.order_details.shipping_address.street_address}<br>
                {if $order.order_details.shipping_address.street_address_2}{$order.order_details.shipping_address.street_address_2}<br>{/if}
                {if $order.order_details.shipping_address.security_access_code}Building access code: {$order.order_details.shipping_address.security_access_code}<br>{/if}
                {$order.order_details.shipping_address.city}, {$order.order_details.shipping_address.state_province_region} {$order.order_details.shipping_address.zip_code}<br>
                {$order.order_details.shipping_address.country_name}<br>
            </div>

            <div class="alert alert-warning" role="alert" style="margin-bottom: 0">
                Use the button below to update this order.<br><br>
                <a href="/painter-dashboard/complete-order/{$order.order_id}/" type="button" class="btn btn-primary">Complete this order</a>
            </div>
        </div>
    </div>
</div>
{/foreach}
{/if}


{if $orders.approval_request_sent}
{if $orders.open}
<br>
<hr>
{/if}
<h2 id="orders-sent-for-approval-section">Orders Sent For Approval</h2>
<div class="alert alert-info" role="alert"><i class="fas fa-clipboard-list"></i> These are the paintings you have sent for approval. An administrator will verify them as soon as possible.</div>
{foreach from=$orders.approval_request_sent name=order item=order}
<div class="order container-fluid">
    <div class="row order-header">
        <div class="col-md-2">
            <div class="order-header-title">Order placed</div>
            <div class="order-header-content">{$order.created_timestamp|convert_time}</div>
        </div>
        <div class="col-md-2">
            <div class="order-header-title">Client</div>
            <div class="order-header-content"><a target="_blank" href="/{$order.client_user_name}">{$order.client_full_name}</a></div>
        </div>
        <div class="col-md-2">
            <div class="order-header-title">Order taken</div>
            <div class="order-header-content">{$order.assigned_timestamp|convert_time}</div>
        </div>
        <div class="col-md-4">
            <div class="order-header-title">Approval request</div>
            <div class="order-header-content">{$order.request_timestamp|convert_time}</div>
        </div>
        <div class="col-md-2">
            <div class="order-header-title">Order #</div>
            <div class="order-header-content">{$order.order_id}</div>
        </div>
    </div>

    <div class="row order-body">
        <div class="col-md-3 order-photo-container">
            <div class="photo-title">Original Photo</div>
            <img class="order-photo" src="{$order.photo_path}"><br><br>
            <a href="{$order.original_photo_path}" download="instapaint-order-{$order.order_id}" type="button" class="btn btn-default">Download image</a><br><br>
            <div class="photo-title">Oil Painting</div>
            <img class="order-photo" src="{$order.painting_path}">
        </div>
        <div class="col-md-9">
            <div class="alert alert-info" role="alert"><i class="fas fa-exclamation-circle"></i> An administrator will verify this order as soon as possible.</strong></div>

            {if $order.is_expedited == '1'}
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-fast-forward"></i> Expedited Service Requested for {$order.expedited_days}
            </div>
            {/if}

            <div class="order-body-title">Frame size</div>
            <div class="order-body-description">{$order.order_details.package.frame_size_name} - {$order.order_details.package.frame_size_description}</div>

            <div class="order-body-title">Frame type</div>
            <div class="order-body-description">{$order.order_details.package.frame_type_name} - {$order.order_details.package.frame_type_description}</div>

            <div class="order-body-title">Shipping</div>
            <div class="order-body-description">{$order.order_details.package.shipping_type_name} - {$order.order_details.package.shipping_type_description}</div>

            <div class="order-body-title">Number of faces</div>
            <div class="order-body-description">{$order.faces}</div>

            <div class="order-body-title">Painting style</div>
            <div class="order-body-description">{if $order.style_name}
                {$order.style_name}<br>
                <img style="max-width: 120px" src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails-200px/{$order.style}.jpg">

                {else}--{/if}</div>

            {if $order.order_notes}
            <div class="order-body-title">Order notes</div>
            <div class="order-body-description">{$order.order_notes}</div>
            {/if}

            <div class="order-body-title">Client info</div>
            <div class="order-body-description">
                Name: {$order.client_full_name}<br>
                Phone number: ({$order.order_details.shipping_address.dial_code_iso}) {$order.order_details.shipping_address.phone_number}<br>
                Email: {$order.client_email}
            </div>

            <div class="order-body-title">Shipping address</div>
            <div class="order-body-description shipping-address-data">
                {$order.order_details.shipping_address.full_name}<br>
                {$order.order_details.shipping_address.street_address}<br>
                {if $order.order_details.shipping_address.street_address_2}{$order.order_details.shipping_address.street_address_2}<br>{/if}
                {if $order.order_details.shipping_address.security_access_code}Building access code: {$order.order_details.shipping_address.security_access_code}<br>{/if}
                {$order.order_details.shipping_address.city}, {$order.order_details.shipping_address.state_province_region} {$order.order_details.shipping_address.zip_code}<br>
                {$order.order_details.shipping_address.country_name}<br>
            </div>

        </div>
    </div>
</div>
{/foreach}
{/if}

{if $orders.approved_by_admin}
{if $orders.approval_request_sent || $orders.open}
<br>
<hr>
{/if}
<h2 id="orders-approved-for-shipping-section">Orders Approved For Shipping</h2>
<div class="alert alert-success" role="alert"><i class="fas fa-check"></i> These paintings have been approved and you can ship them to their respective customer.</div>

{foreach from=$orders.approved_by_admin name=order item=order}
<div class="order container-fluid">
    <div class="row order-header">
        <div class="col-md-2">
            <div class="order-header-title">Order placed</div>
            <div class="order-header-content">{$order.created_timestamp|convert_time}</div>
        </div>
        <div class="col-md-2">
            <div class="order-header-title">Client</div>
            <div class="order-header-content"><a target="_blank" href="/{$order.client_user_name}">{$order.client_full_name}</a></div>
        </div>
        <div class="col-md-2">
            <div class="order-header-title">Order taken</div>
            <div class="order-header-content">{$order.assigned_timestamp|convert_time}</div>
        </div>
        <div class="col-md-4">
            <div class="order-header-title">Order Approved</div>
            <div class="order-header-content">{$order.reviewed_timestamp|convert_time}</div>
        </div>
        <div class="col-md-2">
            <div class="order-header-title">Order #</div>
            <div class="order-header-content">{$order.order_id}</div>
        </div>
    </div>

    <div class="row order-body">
        <div class="col-md-3 order-photo-container">
            <img class="order-photo" src="{$order.painting_path}"><br><br>
            <a href="{$order.original_photo_path}" download="instapaint-order-{$order.order_id}" type="button" class="btn btn-default">Download image</a>
        </div>
        <div class="col-md-9">
            {if $order.is_denied}
            <div class="alert alert-danger">
                <div class="order-body-title">Admin feedback</div>
                <div class="order-body-description" style="white-space: pre-wrap; margin-bottom: 0">{$order.feedback}</div>
            </div>
            {/if}

            {if $order.is_expedited == '1'}
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-fast-forward"></i> Expedited Service Requested for {$order.expedited_days}
            </div>
            {/if}

            <div class="alert alert-warning" role="alert"><i class="fas fa-truck"></i> Please ship this painting to the address provided in the details below.</strong></div>


            <div class="order-body-title">Frame size</div>
            <div class="order-body-description">{$order.order_details.package.frame_size_name} - {$order.order_details.package.frame_size_description}</div>

            <div class="order-body-title">Frame type</div>
            <div class="order-body-description">{$order.order_details.package.frame_type_name} - {$order.order_details.package.frame_type_description}</div>

            <div class="order-body-title">Shipping</div>
            <div class="order-body-description">{$order.order_details.package.shipping_type_name} - {$order.order_details.package.shipping_type_description}</div>

            <div class="order-body-title">Number of faces</div>
            <div class="order-body-description">{$order.faces}</div>

            <div class="order-body-title">Painting style</div>
            <div class="order-body-description">{if $order.style_name}
                {$order.style_name}<br>
                <img style="max-width: 120px" src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails-200px/{$order.style}.jpg">

                {else}--{/if}</div>

            {if $order.order_notes}
            <div class="order-body-title">Order notes</div>
            <div class="order-body-description">{$order.order_notes}</div>
            {/if}

            <div class="order-body-title">Client info</div>
            <div class="order-body-description">
                Name: {$order.client_full_name}<br>
                Phone number: ({$order.order_details.shipping_address.dial_code_iso}) {$order.order_details.shipping_address.phone_number}<br>
                Email: {$order.client_email}
            </div>

            <div class="order-body-title">Shipping address</div>
            <div class="order-body-description shipping-address-data">
                {$order.order_details.shipping_address.full_name}<br>
                {$order.order_details.shipping_address.street_address}<br>
                {if $order.order_details.shipping_address.street_address_2}{$order.order_details.shipping_address.street_address_2}<br>{/if}
                {if $order.order_details.shipping_address.security_access_code}Building access code: {$order.order_details.shipping_address.security_access_code}<br>{/if}
                {$order.order_details.shipping_address.city}, {$order.order_details.shipping_address.state_province_region} {$order.order_details.shipping_address.zip_code}<br>
                {$order.order_details.shipping_address.country_name}<br>
            </div>

            <div class="order-body-title">Weekend delivery preferences</div>
            <div class="order-body-description">
                <div>{if $order.order_details.shipping_address.can_receive_on_sunday}<i class="fas fa-check"></i>{else}<i class="fas fa-times"></i>{/if} Sunday</div>
                <div>{if $order.order_details.shipping_address.can_receive_on_saturday}<i class="fas fa-check"></i>{else}<i class="fas fa-times"></i>{/if} Saturday</div>
            </div>

            <div class="alert alert-warning" role="alert" style="margin-bottom: 0">
                After you have shipped this order click the button below to proceed<br><br>
                <a href="/painter-dashboard/ship-order/{$order.order_id}/" type="button" class="btn btn-primary">Complete the shipping process</a>
            </div>
        </div>
    </div>
</div>
{/foreach}
{/if}


{if $orders.shipped_orders}
{if $orders.approval_request_sent || $orders.open || $orders.approved_by_admin}
<br>
<hr>
{/if}
<h2 id="shipped-orders-section">Shipped Orders</h2>
<div class="alert alert-success" role="alert"><i class="fas fa-truck"></i> These are the paintings you have shipped to customers.</div>

{foreach from=$orders.shipped_orders name=order item=order}
<div class="order container-fluid">
    <div class="row order-header">
        <div class="col-md-2">
            <div class="order-header-title">Order placed</div>
            <div class="order-header-content">{$order.created_timestamp|convert_time}</div>
        </div>
        <div class="col-md-2">
            <div class="order-header-title">Client</div>
            <div class="order-header-content"><a target="_blank" href="/{$order.client_user_name}">{$order.client_full_name}</a></div>
        </div>
        <div class="col-md-2">
            <div class="order-header-title">Order taken</div>
            <div class="order-header-content">{$order.assigned_timestamp|convert_time}</div>
        </div>
        <div class="col-md-2">
            <div class="order-header-title">Order approved</div>
            <div class="order-header-content">{$order.reviewed_timestamp|convert_time}</div>
        </div>
        <div class="col-md-2">
            <div class="order-header-title">Order shipped</div>
            <div class="order-header-content">{$order.shipped_timestamp|convert_time}</div>
        </div>
        <div class="col-md-2">
            <div class="order-header-title">Order #</div>
            <div class="order-header-content">{$order.order_id}</div>
        </div>
    </div>

    <div class="row order-body">
        <div class="col-md-3 order-photo-container">
            <div class="photo-title">Original Photo</div>
            <img class="order-photo" src="{$order.photo_path}"><br><br>
            <a href="{$order.original_photo_path}" download="instapaint-order-{$order.order_id}" type="button" class="btn btn-default">Download image</a><br><br>
            <div class="photo-title">Oil Painting</div>
            <img class="order-photo" src="{$order.painting_path}">
        </div>
        <div class="col-md-9">
            {if $order.is_denied}
            <div class="alert alert-danger">
                <div class="order-body-title">Admin feedback</div>
                <div class="order-body-description" style="white-space: pre-wrap; margin-bottom: 0">{$order.feedback}</div>
            </div>
            {/if}

            <div class="alert alert-success" role="alert"><i class="fas fa-check"></i> This order has been shipped.</strong></div>

                <div class="order-body-title" style="margin-bottom: 5px">Shipping notes</div>
                <div class="order-body-description" style="white-space: pre-wrap;">{$order.shipping_notes}</div>


            {if $order.shipment_receipt_path}
            <div class="order-body-title">Shipment receipt</div>
            <div class="order-body-description" style="margin-top: 3px; max-width: 200px;"><a href="{$order.original_shipment_receipt_path}" target="_blank"><img class="order-photo" src="{$order.shipment_receipt_path}"></a></div>
            {/if}

            <hr>


            <div class="order-body-title">Expedited service</div>
            <div class="order-body-description">{if $order.is_expedited == '1'}Yes{else}No{/if}</div>


            <div class="order-body-title">Frame size</div>
            <div class="order-body-description">{$order.order_details.package.frame_size_name} - {$order.order_details.package.frame_size_description}</div>

            <div class="order-body-title">Frame type</div>
            <div class="order-body-description">{$order.order_details.package.frame_type_name} - {$order.order_details.package.frame_type_description}</div>

            <div class="order-body-title">Shipping</div>
            <div class="order-body-description">{$order.order_details.package.shipping_type_name} - {$order.order_details.package.shipping_type_description}</div>

            <div class="order-body-title">Number of faces</div>
            <div class="order-body-description">{$order.faces}</div>

            <div class="order-body-title">Painting style</div>
            <div class="order-body-description">{if $order.style_name}
                {$order.style_name}<br>
                <img style="max-width: 120px" src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails-200px/{$order.style}.jpg">

                {else}--{/if}</div>
            
            {if $order.order_notes}
            <div class="order-body-title">Order notes</div>
            <div class="order-body-description">{$order.order_notes}</div>
            {/if}

        </div>
    </div>
</div>
{/foreach}
{/if}

{if !$orders.open && !$orders.approval_request_sent && !$orders.approved_by_admin && !$orders.shipped_orders}
<div class="alert alert-info" role="alert"><i class="fas fa-info-circle"></i> You haven't taken any orders yet. <a href="/painter-dashboard/available-orders">Click here to see the available orders!</a> </div>
{/if}

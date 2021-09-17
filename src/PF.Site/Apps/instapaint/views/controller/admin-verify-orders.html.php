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

{if $orders.approval_request_sent}

<div class="alert alert-info" role="alert"><i class="fas fa-edit"></i> Here you can approve or the reject orders that have been completed by painters.</div>

{foreach from=$orders.approval_request_sent name=order item=order}
<div class="order container-fluid">
    <div class="row order-header">
        <div class="col-md-2">
            <div class="order-header-title">Order placed</div>
            <div class="order-header-content">{$order.created_timestamp|convert_time}</div>
        </div>
        <div class="col-md-2">
            <div class="order-header-title">Order taken</div>
            <div class="order-header-content">{$order.assigned_timestamp|convert_time}</div>
        </div>
        <div class="col-md-2">
            <div class="order-header-title">Approval request</div>
            <div class="order-header-content">{$order.request_timestamp|convert_time}</div>
        </div>
        <div class="col-md-4">
            <div class="order-header-title">Client</div>
            <div class="order-header-content"><a target="_blank" href="/{$order.client_user_name}">{$order.client_full_name}</a></div>
        </div>
        <div class="col-md-2">
            <div class="order-header-title">Order # {$order.order_id}</div>
            <div class="order-header-content"><a href="/admin-dashboard/invoice/{$order.order_id}/">Invoice</a></div>
        </div>
    </div>

    <div class="row order-body">
        <div class="col-md-3 order-photo-container">
            <div class="photo-title">Original Photo</div>
            <a href="{$order.original_photo_path}" target="_blank"><img class="order-photo" src="{$order.photo_path}"></a><br><br>
            <div class="photo-title">Oil Painting</div>
            <a href="{$order.original_painting_path}" target="_blank"><img class="order-photo" src="{$order.painting_path}"></a>
        </div>
        <div class="col-md-9">

            {if $order.is_expedited == '1'}
            <div class="alert alert-danger" role="alert">
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

            <div class="alert alert-warning" role="alert" style="margin-bottom: 0; margin-top: 15px;">
                Use the buttons below to update this order<br><br>
                <a href="/admin-dashboard/reject-order/{$order.order_id}/" type="button" class="btn btn-danger">Reject this order</a>&nbsp;
                <a href="/admin-dashboard/approve-order/{$order.order_id}/" type="button" class="btn btn-primary">Approve this order</a>
            </div>
        </div>
    </div>
</div>
{/foreach}
{else}
<div class="alert alert-info" role="alert" style="margin-bottom: 0"><i class="fas fa-info-circle"></i> There aren't any approval requests at this moment.</div>
{/if}

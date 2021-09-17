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
    .photo-title {
        font-size: 16px;
        text-transform: capitalize;
        color: #555;
        margin-bottom: 4px;
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

    #photo-preview {
        display: none;
    }

    #photo-preview img {
        max-width: 380px;
        max-height: 500px;
    }

    #photo-error-empty {
        display: none;
    }
</style>

{/literal}

{if $order}
<div class="alert alert-info" role="alert"><i class="fas fa-times-circle"></i> To reject this painting, provide feedback and click the red button in the description below.</div>
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
        <div class="col-md-6">
            <div class="order-header-title">Approval request</div>
            <div class="order-header-content">{$order.request_timestamp|convert_time}</div>
        </div>
        <div class="col-md-2">
            <div class="order-header-title">Order # {$order.order_id}</div>
            <div class="order-header-content"><a href="/admin-dashboard/invoice/{$order.order_id}/">Invoice</a></div>
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

            <div class="alert alert-info" role="alert"><i class="fas fa-exclamation-circle"></i> Enter some feedback about this painting that you'd like the painter to see.</div>

            <form method="post">

            <div class="order-body-title" style="margin-bottom: 5px">Feedback for painter</div>
            <div class="form-group">
                <textarea id="notes" type="text" required name="val[feedback]" class="form-control" placeholder="Write why this order is being rejected..."></textarea>
            </div>

            <div class="form-group">
                <input type="hidden" name="val[token]" class="form-control" required="required" value="{$token}"/>
            </div>

            <hr>
            <button id="submit-button" type="submit" class="btn btn-danger" name="_submit">Reject this order</button>
            </form>
        </div>
    </div>
</div>
{/if}
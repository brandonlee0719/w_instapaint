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

{if $orderLimitReached}
<div class="alert alert-info" role="alert"><i class="fas fa-stopwatch"></i> <strong>Daily Limit Reached</strong><br> You can place up to {$maxDailyOrders} new orders per day. Come back later to find more orders! <a href="/painter-dashboard/orders/">Click here to see your current orders.</a> </div>
{/if}

{if $order}
<div class="alert alert-info" role="alert"><i class="far fa-image"></i> To complete this order, upload a photo of your finished painting and click the green button. The painting will be reviewed by an administrator as soon as possible.</div>
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
        <div class="col-md-6">
            <div class="order-header-title">Order taken</div>
            <div class="order-header-content">{$order.assigned_timestamp|convert_time}</div>
        </div>
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

            <div class="alert alert-info" role="alert"><i class="fas fa-exclamation-circle"></i> Do not ship an order until it has been approved. Click the green button to complete the order.</strong></div>

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

            <div class="order-body-title" style="margin-bottom: 5px">Photo of finished painting</div>
            <div class="order-body-description shipping-address-data">
            {module name='core.upload-form' type='instapaint' }
            </div>

            <div class="form-group" id="photo-preview">
                <div><img style="max-width: 400px"></div>
            </div>

            <form method="post">

            <div class="form-group">
                <input type="hidden" name="val[token]" class="form-control" required="required" value="{$token}"/>
            </div>

            <div class="form-group">
                <input id="temp-file-input" type="hidden" name="val[temp_file]" class="form-control" required="required" value=""/>
            </div>

            <hr>
            <div class="form-group" id="photo-error-empty">
                <div class="alert alert-danger" role="alert">Please upload a photo of the finished painting and try again.</div>
            </div>
            <button id="submit-button" type="submit" class="btn btn-success" name="_submit">Send for approval</button>
            </form>
        </div>
    </div>
</div>

{literal}
<script>
    $Ready(function() {
        $('#submit-button').on('click', function () {
            // If no photo, show error
            if (!$('.dz-preview').hasClass('dz-success')) {
                $('#photo-error-empty').css('display', 'block');
                return false;
            } else {
                $('#photo-error-empty').css('display', 'none');
            }
        });
    });
</script>
{/literal}

{elseif !$orderLimitReached}
<div class="alert alert-info" role="alert"><i class="fas fa-info-circle"></i> You haven't taken any orders yet. <a href="/painter-dashboard/available-orders">Click here to see the available orders!</a> </div>
{/if}

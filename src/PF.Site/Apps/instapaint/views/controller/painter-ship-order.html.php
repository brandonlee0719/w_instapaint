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

{if $order}
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

            <div class="alert alert-warning" role="alert"><i class="fas fa-truck"></i> Before proceeding, make sure you have shipped this order to the shipping address in the details below.</strong></div>

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

            <div class="alert alert-warning" role="alert"><i class="fas fa-exclamation-circle"></i> Enter the shipping information below, such as the tracking number or tracking URL. (The client and admins will see it)</div>

            <form method="post">

            <div class="order-body-title" style="margin-bottom: 5px">Shipping notes</div>
            <div class="form-group">
                <textarea id="notes" type="text" name="val[shipping_notes]" class="form-control" placeholder="Tracking number, tracking URL, etc..." required></textarea>
            </div>

            <div class="alert alert-warning" role="alert"><i class="fas fa-exclamation-circle"></i> Upload a photo or screenshot of your shipment receipt. (Only admins will see it)</div>

            <div class="order-body-title" style="margin-bottom: 5px">Shipment Receipt</div>
            <div class="order-body-description shipping-address-data">
                {module name='core.upload-form' type='instapaint'}
            </div>

            <div class="form-group">
                <input type="hidden" name="val[token]" class="form-control" required="required" value="{$token}"/>
            </div>
                <hr>
            <div class="form-group">
                <div class="checkbox">
                    <label><input type="checkbox" required name="val[confirm_information]">I confirm that the above details are correct and that I have shipped this painting to the specified address.</label>
                </div>
            </div>

            <div class="form-group">
                <input id="temp-file-input" type="hidden" name="val[temp_file]" class="form-control" required="required" value=""/>
            </div>

            <div class="form-group" id="photo-error-empty">
                <div class="alert alert-danger" role="alert">Please upload a photo of the shipment receipt and try again.</div>
            </div>


            <button id="submit-button" type="submit" class="btn btn-success" name="_submit">Mark as shipped</button>
            </form>
        </div>
    </div>
</div>
{/if}

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
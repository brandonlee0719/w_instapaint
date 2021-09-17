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
    .order-summary, #discount-line {
        display: none;
    }
</style>
{/literal}

{if $csrfError}
<div class="alert alert-danger" role="alert"><strong>CSRF detected!</strong> For your security, please logout immediately and report this incident to a developer.</div>
{elseif !$addresses}

<div class="order container-fluid">
    <div class="row order-body">
        <div class="col-md-3 order-photo-container">
            <img class="order-photo" src="/PF.Base/file/pic/photo/partial_orders/{$partial_order.unique_id}/{$partial_order.thumbnail_path}">
        </div>
        <div class="col-md-9">
            <div class="order-body-title">Frame size</div>
            <div class="order-body-description">{$package.frame_size_name} - {$package.frame_size_description}</div>

            <div class="order-body-title">Frame type</div>
            <div class="order-body-description">{$package.frame_type_name} - {$package.frame_type_description}</div>

            <div class="order-body-title">Shipping</div>
            <div class="order-body-description">{$package.shipping_type_name} - {$package.shipping_type_description}</div>

            {if $partial_order.order_notes}
            <div class="order-body-title">Order notes</div>
            <div class="order-body-description">{$partial_order.order_notes}</div>
            {/if}
        </div>
    </div>
</div>


<h3>Step 1: Add your shipping address</h3>
<p>Let's begin by adding your shipping address. You can save several addresses and use them on future orders.</p>
<a type="button" class="btn btn-primary" href="/client-dashboard/addresses/add/">Add New Address</a>
{else}
{literal}
<style>
    .form-group {
        max-width: 500px;
    }
    .form-group-wide {
        max-width: 100%;
    }
    .form-group-short {
        max-width: 310px;
    }
    .order-summary-data {
        line-height: 22px;
    }
    .coupon-message {
        display: none;
    }
    #photo-error-empty {
        display: none;
    }
    #photo-preview {
        display: none;
    }
    #photo-preview img {
        max-width: 380px;
        max-height: 500px;
    }
</style>
{/literal}
<form method="post">

    <div class="order container-fluid">
        <div class="row order-body">
            <div class="col-md-3 order-photo-container">
                <img class="order-photo" src="/PF.Base/file/pic/photo/partial_orders/{$partial_order.unique_id}/{$partial_order.thumbnail_path}">
            </div>
            <div class="col-md-9">
                <div class="order-body-title">Frame size</div>
                <div class="order-body-description">{$package.frame_size_name} - {$package.frame_size_description}</div>

                <div class="order-body-title">Frame type</div>
                <div class="order-body-description">{$package.frame_type_name} - {$package.frame_type_description}</div>

                <div class="order-body-title">Shipping</div>
                <div class="order-body-description">{$package.shipping_type_name} - {$package.shipping_type_description}</div>
            </div>
        </div>
    </div>

    <div class="form-group form-group-wide">
        <label class="control-label" for="shipping-address">Shipping address</label>
        <select id="shipping-address" type="text" name="val[shipping_address]" class="form-control" required>
            <option value="" selected="selected">Select a shipping address</option>
            {foreach from=$addresses name=address item=address}
            <option value="{$address.address_id}" {if $address.is_default}selected{/if}>{$address.full_name}, {$address.street_address}, {$address.city}, {$address.state_province_region} {$address.zip_code}, {$address.country_name}</option>
            {/foreach}
        </select>
    </div>

    <label class="control-label" for="coupon-code">Coupon code</label> (Optional)
    <div class="input-group form-group-short">

        <input onkeypress="return event.keyCode != 13;"id="coupon-code" type="text" name="val[coupon_code]" class="form-control" placeholder="Optional coupon code">
        <span class="input-group-btn">
        <button id="coupon-code-button" class="btn btn-default" type="button" disabled>Apply coupon</button>
   </span>
    </div>

    <div class="form-group coupon-message" id="coupon-code-error-message-unknown">
        <br>
        <div class="alert alert-danger" role="alert"><strong>Unknown code.</strong> Please select a frame size, frame type, and shipping option and try again.</div>
    </div>

    <div class="form-group coupon-message" id="coupon-code-error-message-invalid">
        <br>
        <div class="alert alert-warning" role="alert">This coupon code is not valid.</div>
    </div>

    <div class="form-group coupon-message" id="coupon-code-success-message-valid">
        <br>
        <div class="alert alert-success" role="alert">Your coupon code was applied successfully!</div>
    </div>

    <div class="form-group">
        <input type="hidden" name="val[token]" class="form-control" required="required" value="{$token}"/>
    </div>

    <div class="form-group">
        <input id="package-id" type="hidden" name="val[package_id]" class="form-control" required="required" value="{$package.package_id}"/>
    </div>

    <div class="form-group">
        <input id="cancel-order" type="hidden" name="val[cancel_order]" class="form-control" required="required" value=""/>
    </div>

    <div class="form-group order-summary">
        <h3>Order Summary</h3>
        <div class="order-summary-data">
            <span id="summary-frame-size">Frame Size:</span><span class="pull-right" id="summary-frame-size-price">${$package.frame_size_price}</span><br>
            <span id="summary-frame-type">Frame Type:</span><span class="pull-right" id="summary-frame-type-price">${$package.frame_type_price}</span><br>
            <span id="summary-shipping-type">Shipping Type:</span><span class="pull-right" id="summary-shipping-type-price">${$package.shipping_type_price}</span><br>
            <script>
                var num_faces = {$faces.number};
                var faces_price = {$faces.price};
            </script>
            <span id="summary-faces-line"><span id="summary-faces">Faces ({$faces.number}):</span><span class="pull-right" id="summary-faces-price">{$faces.price_formatted}</span><br></span>

            <script>
                var stylePrice = {$style.price};
            </script>
            <span id="summary-style">Style ({$style.name}):</span><span class="pull-right" id="summary-style-price">${$style.price_str}</span><br>

            {if $partial_order.is_expedited}
            <script>
                var isExpedited = true;
            </script>
            <span id="summary-expedited-service-line"><span id="summary-expedited-service">Expedited Service:</span><span class="pull-right" id="summary-expedited-service-price">$80.00</span><br></span>
            {else}
                <script>
                    var isExpedited = false;
                </script>
            {/if}
            <span id="discount-line"><span id="summary-discount">Discount:</span><span class="pull-right" id="summary-discount-price">0</span><br></span>
            <span id="summary-total"><strong>Grand Total:</strong></span><span class="pull-right"><strong id="summary-total-price">${$total_price}</strong></span>
        </div>
    </div>

    <div class="form-group">
        <hr>
        <div class="form-group" id="photo-error-empty">
            <br>
            <div class="alert alert-danger" role="alert">Please upload a photo and try again.</div>
        </div>
        <a href="/first-order-complete/?cancel=true" class="btn btn-primary">Cancel order</a>
        <button id="submit-button" type="submit" class="btn btn-danger pull-right" name="_submit">Place order</button>
    </div>

</form>

{literal}

<script>
    var packages;
    $Ready(function() {
        $(document).ready(function() {
            if (top.location.pathname.includes("/first-order-complete")) {

                function getSelectedPackage() {

                    var packageId = $('#package-id').val();

                    for (var i = 0; i < packages.length; i++) {
                        if (packages[i].package_id == packageId) {
                            return packages[i];
                        }
                    }
                }

                function updateSummary() {
                    var selectedPackage = getSelectedPackage();

                    if (!selectedPackage) {
                        $('.order-summary').css('display', 'none');
                    } else {
                        $.ajax({
                            url: "/client-dashboard/orders/add/?ajax=sale&package_id=" + selectedPackage.package_id
                        }).done(function(data) {

                            if (data != false) {
                                $('#summary-discount').html('Discount: ' + data.name + ' (' + data.discount_percentage + '% Off):');

                                var discountAmount = getTotalPrice() * (parseFloat(data.discount_percentage) / 100);
                                $('#summary-discount-price').html('-' + discountAmount.toLocaleString('en-US',  { style: 'currency', currency: 'USD' }));
                                $('#summary-total-price').html((getTotalPrice() - discountAmount).toLocaleString('en-US',  { style: 'currency', currency: 'USD' }));
                                $('#discount-line').css('display', 'block');
                            } else {
                                $('#summary-discount-price').html(0.0.toLocaleString('en-US',  { style: 'currency', currency: 'USD' }));
                                $('#discount-line').css('display', 'none');
                            }
                        });

                        $('#summary-frame-size').html('Frame Size: ' + selectedPackage.frame_size_name + ' (' + selectedPackage.frame_size_description + '):');
                        $('#summary-frame-type').html('Frame Type: ' + selectedPackage.frame_type_name + ' (' + selectedPackage.frame_type_description + '):');
                        $('#summary-shipping-type').html('Shipping Type: ' + selectedPackage.shipping_type_name + ' (' + selectedPackage.shipping_type_description + '):');

                        $('#summary-frame-size-price').html(parseFloat(selectedPackage.frame_size_price).toLocaleString('en-US',  { style: 'currency', currency: 'USD' }));
                        $('#summary-frame-type-price').html(parseFloat(selectedPackage.frame_type_price).toLocaleString('en-US',  { style: 'currency', currency: 'USD' }));
                        $('#summary-shipping-type-price').html(parseFloat(selectedPackage.shipping_type_price).toLocaleString('en-US',  { style: 'currency', currency: 'USD' }));
                        $('#summary-faces-price').html(parseFloat(faces_price).toLocaleString('en-US',  { style: 'currency', currency: 'USD' }));
                        $('#summary-total-price').html(getTotalPrice().toLocaleString('en-US',  { style: 'currency', currency: 'USD' }));
                        $('.order-summary').css('display', 'block');
                    }
                }

                function getTotalPrice() {
                    var selectedPackage = getSelectedPackage();
                    var expeditedPrice = isExpedited ? 80.00 : 0;
                    return (parseFloat(selectedPackage.frame_size_price) + parseFloat(selectedPackage.frame_type_price) + parseFloat(selectedPackage.shipping_type_price) + parseFloat(expeditedPrice) + parseFloat(faces_price)) + parseFloat(stylePrice);
                }

                function hideCouponMessages() {
                    $('.coupon-message').css('display', 'none');
                }

                function clearCoupon() {
                    hideCouponMessages();
                    $('#coupon-code').val('');
                    $('#coupon-code').removeAttr('readonly');
                    $('#coupon-code-button').attr('disabled', 'disabled');
                    $('#coupon-code-button').html('Apply coupon');
                }

                $.ajax({
                    url: "/client-dashboard/orders/add/?ajax=packages"
                }).done(function(data) {
                    packages = data;
                    updateSummary();
                });

                // Handle coupon code
                $('#coupon-code-button').on('click', function () {

                    hideCouponMessages();

                    if($('#coupon-code').val().trim() == '') {
                        return;
                    }

                    $.ajax({
                        url: "/client-dashboard/orders/add/?ajax=coupon&package_id=" + getSelectedPackage().package_id + '&coupon=' + $('#coupon-code').val()
                    }).done(function(data) {

                        if (data == false) {
                            $('#coupon-code').val('');
                            $('#coupon-code-button').attr('disabled', 'disabled');
                            $('#coupon-code-error-message-invalid').css('display', 'block');
                        } else {
                            // Show that coupon was applied:
                            $('#coupon-code').attr('readonly', 'readonly');
                            $('#coupon-code-button').attr('disabled', 'disabled');
                            $('#coupon-code-button').html('Coupon appplied');

                            $('#summary-discount').html('Discount: ' + data.name + ' (' + data.discount_percentage + '% Off):');

                            var discountAmount = getTotalPrice() * (parseFloat(data.discount_percentage) / 100);
                            $('#summary-discount-price').html('-' + discountAmount.toLocaleString('en-US',  { style: 'currency', currency: 'USD' }));
                            $('#summary-total-price').html((getTotalPrice() - discountAmount).toLocaleString('en-US',  { style: 'currency', currency: 'USD' }));
                            $('#discount-line').css('display', 'block');
                            $('#coupon-code-success-message-valid').css('display', 'block');
                        }
                    });

                });

                // Handle coupon field
                $('#coupon-code').on('keyup', function () {
                    if ($(this).val().trim() == '') {
                        $('#coupon-code-button').attr('disabled', 'disabled');
                    } else {
                        $('#coupon-code-button').removeAttr('disabled');
                    }
                });

            }
        });
    });
</script>
{/literal}
{/if}

{if $csrfError}
<div class="alert alert-danger" role="alert"><strong>CSRF detected!</strong> For your security, please logout immediately and report this incident to a developer.</div>
{elseif !$addresses}
<h3>Step 1: Add your shipping address</h3>
<p>Let's begin by adding your shipping address. You can save several addresses and use them on future orders.</p>
<a type="button" class="btn btn-primary" href="/client-dashboard/addresses/add/">Add New Address</a>
{else}
{literal}
<script src="https://unpkg.com/tippy.js@3/dist/tippy.all.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

<script src="/PF.Site/Apps/instapaint/assets/image-picker/image-picker.js"></script>
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
    .order-summary {
        display: none;
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
    #summary-expedited-service-line {
        display: none;
    }
    #form-group-faces {
        display: none;
    }
    button.btn-processing[disabled],
    button.btn-processing[disabled]:hover {
        opacity: 1;
        color: #212529;
        background-color: #ffc107;
        border-color: #ffc107;
        transition-property: none;
        cursor: progress;
    }
    #summary-faces-line {
        display: none;
    }

    ul.thumbnails.image_picker_selector {
        overflow: auto;
        list-style-image: none;
        list-style-position: outside;
        list-style-type: none;
        padding: 0px;
        margin: 0px;
        margin-top: 5px;
    }
    ul.thumbnails.image_picker_selector ul {
        overflow: auto;
        list-style-image: none;
        list-style-position: outside;
        list-style-type: none;
        padding: 0px;
        margin: 0px; }
    ul.thumbnails.image_picker_selector li.group {width:100%;}
    ul.thumbnails.image_picker_selector li.group_title {
        float: none; }
    ul.thumbnails.image_picker_selector li {
        max-width: 125px;
        margin: 0px 12px 12px 0px;
        float: left; }
    ul.thumbnails.image_picker_selector li .thumbnail {
        border-radius: 4px;
        padding: 6px;
        border: 1px solid #dddddd;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none; }
    ul.thumbnails.image_picker_selector li .thumbnail img {
        -webkit-user-drag: none; }
    ul.thumbnails.image_picker_selector li .thumbnail.selected {
        background: #0088cc; }

    ul.thumbnails.image_picker_selector li .thumbnail.selected p {
        color: white; }

    .image_picker_selector .thumbnail {
        margin-bottom: 0px;
    }

    .image_picker_selector .thumbnail  p{
        margin-bottom: 5px;
        margin-top: 5px;
    }

    .image_picker_image {
        max-width: 100%;
    }

    @media only screen and (max-width: 600px) {
        ul.thumbnails.image_picker_selector li {
            max-width: 45%;
        }
    }
</style>
<script>
    function imageToDataUri(img, width, height) {

        // create an off-screen canvas
        var canvas = document.createElement('canvas'),
            ctx = canvas.getContext('2d');

        // set its dimension to target size
        canvas.width = width;
        canvas.height = height;

        // draw source image into the off-screen canvas:
        ctx.drawImage(img, 0, 0, width, height);

        // encode image to data-uri with base64 version of compressed image
        return canvas.toDataURL('image/jpeg');
    }

    function CountFaces(imageData) {
        AWS.region = "us-east-2";
        var rekognition = new AWS.Rekognition();
        var params = {
            Image: {
                Bytes: imageData
            },
            Attributes: [
                'ALL'
            ]
        };
        rekognition.detectFaces(params, function (err, data) {
            if (err) {
                $('#faces-screen').hide();
                $('#faces').val('');
                $('#faces, #form-group-faces').show();

                // Enable buttons:
                $('#submit-button')
                    .removeAttr('disabled')
                    .removeClass('btn-processing')
                    .html('Place order');

                $('#photo-upload-button').html('Choose file');
                $('.submit-button-group')
                    .css('cursor', 'default');
            } else {
                var numFaces = data.FaceDetails.length;

                // Enable buttons:
                $('#submit-button')
                    .removeAttr('disabled')
                    .removeClass('btn-processing')
                    .html('Place order');

                // Update faces input:
                if (numFaces > 10) {
                    $('#faces, #faces-screen').val(10);
                } else {
                    $('#faces, #faces-screen').val(numFaces);
                }

                //$('#faces-screen').css('display', 'block');
                $('#faces').css('display', 'none').change();
                $('#summary-faces-line').show();

                $('.submit-button-group')
                    .css('cursor', 'default');
            }
        });
    }

    function ProcessImage() {
        // Disable buttons:
        $('#next-step-button, #photo-upload-button')
            .addClass('btn-processing')
            .attr('disabled', 'disabled')
            .html('<i class="fa fa-refresh fa-spin"></i>&nbsp;&nbsp;Detecting faces');

        $('#form-faces-section').hide();

        AnonLog();
        var control = document.getElementById("image-file");
        var file = control.files[0];

        // Load base64 encoded image
        var reader = new FileReader();
        reader.onload = (function (theFile) {
            return function (e) {
                var img = document.createElement('img');
                var image = null;
                img.src = e.target.result;

                img.onload = function (ev) {
                    // Resize image to send it faster to Rekognition:
                    var maxSide = 1000;
                    if (img.width > maxSide || img.height > maxSide) {
                        var longestSide = img.width > img.height ? img.width : img.height;
                        var factor = longestSide / maxSide;
                        var resizedImage = imageToDataUri(img,parseInt(img.width/factor), parseInt(img.height/factor));
                    }

                    resizedImage = resizedImage ? resizedImage : img.src;
                    try {
                        image = atob(resizedImage.split("data:image/jpeg;base64,")[1]);
                    } catch (e) {
                        alert("Not an image file we can process");
                        return;
                    }
                    //unencode image bytes for Rekognition DetectFaces API
                    var length = image.length;
                    imageBytes = new ArrayBuffer(length);
                    var ua = new Uint8Array(imageBytes);
                    for (var i = 0; i < length; i++) {
                        ua[i] = image.charCodeAt(i);
                    }
                    //Call Rekognition
                    CountFaces(imageBytes);
                };

            };
        })(file);
        reader.readAsDataURL(file);
    }

    //Provides anonymous log on to AWS services
    function AnonLog() {

        // Configure the credentials provider to use your identity pool
        // Initialize the Amazon Cognito credentials provider
        AWS.config.region = 'us-east-2'; // Region
        AWS.config.credentials = new AWS.CognitoIdentityCredentials({
            IdentityPoolId: 'us-east-2:2ba91d5a-c10c-4f28-a73a-8af691e0d743',
        });
        // Make the call to obtain credentials
        AWS.config.credentials.get(function () {
            // Credentials will be available when this function is called.
            var accessKeyId = AWS.config.credentials.accessKeyId;
            var secretAccessKey = AWS.config.credentials.secretAccessKey;
            var sessionToken = AWS.config.credentials.sessionToken;
        });
    }
    window.onload = function (ev) {
        // Override file drop zone event handlers:
        $Core.Instapaint.dropzoneOnSuccess = function (dropZone, file, info) {
            $fileId = JSON.parse(info).file;

            $('#temp-file-input').val($fileId);

            var reader  = new FileReader();
            reader.onloadend = function () {
                $('#photo-preview').find('img').attr('src', reader.result);
            };

            // Process image with Rekognition //

            $('#photo-error-empty').hide();

            // Disable buttons:
            $('#submit-button')
                .addClass('btn-processing')
                .attr('disabled', 'disabled')
                .html('<i class="fa fa-refresh fa-spin"></i>&nbsp;&nbsp;Detecting faces');
            $('.submit-button-group')
                .css('cursor', 'progress');

            $('#form-group-faces').hide();

            AnonLog();

            reader.onload = function (ev1) {
                var img = document.createElement('img');
                var image = null;
                img.src = reader.result;

                img.onload = function (ev) {
                    // Resize image to send it faster to Rekognition:
                    var maxSide = 1000;
                    if (img.width > maxSide || img.height > maxSide) {
                        var longestSide = img.width > img.height ? img.width : img.height;
                        var factor = longestSide / maxSide;
                        var resizedImage = imageToDataUri(img,parseInt(img.width/factor), parseInt(img.height/factor));
                    }

                    resizedImage = resizedImage ? resizedImage : img.src;
                    try {
                        image = atob(resizedImage.split("data:image/jpeg;base64,")[1]);
                    } catch (e) {
                        alert("Not an image file we can process");
                        return;
                    }
                    //unencode image bytes for Rekognition DetectFaces API
                    var length = image.length;
                    imageBytes = new ArrayBuffer(length);
                    var ua = new Uint8Array(imageBytes);
                    for (var i = 0; i < length; i++) {
                        ua[i] = image.charCodeAt(i);
                    }
                    //Call Rekognition
                    CountFaces(imageBytes);
                };
            };

            if (file) {
                reader.readAsDataURL(file);
            }

            $('#photo-preview').css('display', 'block');
            //$('#photo-preview').find('img').attr('src', dropZone.find('.dz-image').find('img').attr('src'));
        };

        $Core.Instapaint.dropzoneOnRemovedFile = function () {
            $('#temp-file-input').val('');
            $('#photo-preview').css('display', 'none');
            $('#faces').val('').change();
        };

        $('#order-form').submit(function () {
            if (document.getElementById("image-file").files.length === 0) {
                $('#choose-photo-error-message').css('display', 'block');
                $('#form-faces-section').css('display', 'none');
                return false;
            } else {
                $('#next-step-button')
                    .attr('disabled', 'disabled')
                    .css('cursor', 'progress')
                    .html('<i class="fa fa-refresh fa-spin"></i>&nbsp;&nbsp;Creating order');
                $('#photo-upload-button')
                    .attr('disabled', 'disabled');
            }
        });

        $('#image-file').on('change', function () {
            if (document.getElementById("image-file").files.length === 0) {
                $('#image-preview, #form-faces-section').css('display', 'none');
                $('#choose-photo-error-message').css('display', 'block');
                $('#photo-upload-button').html('Choose file');
                return false;
            } else {
                $('#image-preview').css('display', 'block');
                $('#choose-photo-error-message').css('display', 'none');

                // Analyze image with Rekognition:
                ProcessImage();
            }
        });
    };

</script>
{/literal}
<script src="/PF.Site/Apps/instapaint/assets/front-page/js/aws-cognito-sdk.min.js"></script>
<script src="/PF.Site/Apps/instapaint/assets/front-page/js/amazon-cognito-identity.min.js"></script>
<script src="https://sdk.amazonaws.com/js/aws-sdk-2.16.0.min.js"></script>
<form method="post">

    <div class="form-group form-group-wide">
        <label class="control-label" for="shipping-address">Shipping address</label>
        <select id="shipping-address" type="text" name="val[shipping_address]" class="form-control" required>
            <option value="" selected="selected">Select a shipping address</option>
            {foreach from=$addresses name=address item=address}
            <option value="{$address.address_id}" {if $address.is_default}selected{/if}>{$address.full_name}, {$address.street_address}, {$address.city}, {$address.state_province_region} {$address.zip_code}, {$address.country_name}. {$address.dial_code} {$address.phone_number}</option>
            {/foreach}
            <option value="" id="add-new-address-option">Add a new address</option>
        </select>
    </div>

    <div class="form-group">
        <label class="control-label" for="frame-size">Size</label>
        <select id="frame-size" type="text" name="val[frame_size]" class="form-control" required>
            <option value="" selected="selected">Select a frame size</option>
            {foreach from=$frameSizes name=size item=size}
            <option value="{$size.frame_size_id}" >{$size.name_phrase} - {$size.description_phrase} (${$size.price_usd})</option>
            {/foreach}
        </select>
    </div>

    <div class="form-group">
        <label class="control-label" for="frame-type">Frame type</label>
        <select id="frame-type" type="text" name="val[frame_type]" class="form-control" required>
            <option value="" selected="selected">Select a frame type</option>
            {foreach from=$frameTypes name=type item=type}
            <option value="{$type.frame_type_id}" >{$type.name_phrase} - {$type.description_phrase} (${$type.price_usd})</option>
            {/foreach}
        </select>
    </div>

    <div class="form-group">
        <label class="control-label" for="shipping-type">Shipping</label>
        <select id="shipping-type" type="text" name="val[shipping_type]" class="form-control" required>
            <option value="" selected="selected">Select a shipping type</option>
            {foreach from=$shippingTypes name=type item=type}
            <option value="{$type.shipping_type_id}" >{$type.name_phrase} - {$type.description_phrase} (${$type.price_usd})</option>
            {/foreach}
        </select>
    </div>

    <div id="form-group-faces" class="form-group">
        <label class="control-label" for="faces">Faces</label>
        <select id="faces-screen" type="text" name="val[faces]" class="form-control" disabled="disabled">
            <option value="0">0 ($0)</option>
            <option value="1">1 (Included)</option>
            <option value="2">2 (+$25)</option>
            <option value="3">3 (+$50)</option>
            <option value="4">4 (+$75)</option>
            <option value="5">5 (+$100)</option>
            <option value="6">6 (+$125)</option>
            <option value="7">7 (+$150)</option>
            <option value="8">8 (+$175)</option>
            <option value="9">9 (+$200)</option>
            <option value="10">10 (+$225)</option>
        </select>
        <select id="faces" type="text" name="val[faces]" class="form-control" required style="display: none;">
            <option value="">Select the number of faces</option>
            <option value="0" selected="selected">0 ($0)</option>
            <option value="1">1 (Included)</option>
            <option value="2">2 (+$25)</option>
            <option value="3">3 (+$50)</option>
            <option value="4">4 (+$75)</option>
            <option value="5">5 (+$100)</option>
            <option value="6">6 (+$125)</option>
            <option value="7">7 (+$150)</option>
            <option value="8">8 (+$175)</option>
            <option value="9">9 (+$200)</option>
            <option value="10">10 (+$225)</option>
        </select>
    </div>

    <div class="form-group">
        <label class="control-label" for="expedited" title="What is this? An optional handling fee to prioritise your painting so that it is completed within 48 hours of placing your order and shipped as soon as possible.">Expedited service</label>
        <div>
            <label class="checkbox-inline"><input id="expedited" type="checkbox" name="val[expedited]" value="1"><span style="top: 1px; position: relative; left: 0px;">Prioritize your painting (+$80)</span></label>
        </div>
    </div>

    <div class="form-group" id="delivery-time-section" style="display: none;">
        <label for="expedited-date">Choose your date</label>
        <div style="margin-bottom: 7px; margin-top: 5px;">
            <i class="fa fa-calendar" style="margin-right: 3px"></i> <input id="expedited-date" name="val[expedited_date]" value="{$expeditedMinDate}" type="date" min="{$expeditedMinDate}" />
        </div>
        <br>
    </div>


        <label class="control-label">Photo</label>
        {module name='core.upload-form' type='instapaint' }

    <div class="form-group" id="photo-preview">
        <label class="control-label">Photo preview</label>
        <div><img></div>
    </div>

    <div class="form-group">
        <label for="notes" data-tippy-arrow="true" data-tippy-arrow-type="round" data-tippy="Click the selected style to change it">Choose your style (click it to change)</label>
        <select id="style-picker" name="val[style]" class="image-picker style-picker">
            <option data-img-src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails/magic-wand.jpg" data-img-class="Artists Choice" data-img-alt="Artist's Choice" value="0">Artist's Choice</option>
            <option data-img-src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails/sample53.jpg" data-img-class="Style 1" data-img-alt="Style 1" value="1">Style 1</option>
            <option data-img-src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails/sample78.jpg" data-img-class="Style 2" data-img-alt="Style 2" value="2">Style 2</option>
            <option data-img-src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails/sample138.jpg" data-img-class="Style 3" data-img-alt="Style 3" value="3">Style 3</option>
            <option data-img-src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails/sample136.jpg" data-img-class="Style 4" data-img-alt="Style 4" value="4">Style 4</option>
            <option data-img-src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails/sample100.jpg" data-img-class="Style 5" data-img-alt="Style 5" value="5">Style 5</option>
            <option data-img-src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails/sample139.jpg" data-img-class="Style 6" data-img-alt="Style 6" value="6">Style 6</option>
            <option data-img-src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails/sample140.jpg" data-img-class="Style 7" data-img-alt="Style 7" value="7">Style 7</option>
            <option data-img-src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails/sample115.jpg" data-img-class="Style 8" data-img-alt="Style 8" value="8">Style 8</option>
            <option data-img-src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails/sample141.jpg" data-img-class="Style 9" data-img-alt="Style 9" value="9">Style 9</option>
            <option data-img-src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails/sample101.jpg" data-img-class="Style 10" data-img-alt="Style 10" value="10">Style 10</option>
            <option data-img-src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails/sample111.jpg" data-img-class="Style 11" data-img-alt="Style 11" value="11">Style 11</option>
            <option data-img-src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails/sample88.jpg" data-img-class="Style 12" data-img-alt="Style 12" value="12">Style 12</option>
            <option data-img-src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails/sample117.jpg" data-img-class="Style 13" data-img-alt="Style 13" value="13">Style 13</option>
        </select>
    </div>

    <div class="form-group">
        <label class="control-label" for="notes">Order notes</label> (Optional)
        <textarea id="notes" type="text" name="val[notes]" class="form-control" placeholder="Optional notes about this order"></textarea>
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
        <input id="temp-file-input" type="hidden" name="val[temp_file]" class="form-control" required="required" value=""/>
    </div>

    <div class="form-group order-summary">
        <h3>Order Summary</h3>
        <div class="order-summary-data">
            <span id="summary-frame-size">Frame Size:</span><span class="pull-right" id="summary-frame-size-price">0</span><br>
            <span id="summary-frame-type">Frame Type:</span><span class="pull-right" id="summary-frame-type-price">0</span><br>
            <span id="summary-shipping-type">Shipping Type:</span><span class="pull-right" id="summary-shipping-type-price">0</span><br>
            <span id="summary-faces-line"><span id="summary-faces" style="cursor: default; text-decoration: underline;" data-tippy-arrow="true" data-tippy-arrow-type="round" data-tippy="The base packages cover 1 face. Since each additional face will require more time and skill, any painting with 2 or more faces will require $25 fee for the additional faces. The first face is free.">Faces (1):</span><span class="pull-right" id="summary-faces-price">$0.00</span><br></span>
            <span id="summary-expedited-service-line"><span id="summary-expedited-service">Expedited Service:</span><span class="pull-right" id="summary-expedited-service-price">$80.00</span><br></span>
            <span id="discount-line"><span id="summary-discount">Discount:</span><span class="pull-right" id="summary-discount-price">0</span><br></span>
            <span id="summary-total"><strong>Grand Total:</strong></span><span class="pull-right"><strong id="summary-total-price">0</strong></span>
        </div>
    </div>

    <div class="form-group submit-button-group">
        <hr>
        <div class="form-group" id="photo-error-empty">
            <br>
            <div class="alert alert-danger" role="alert">Please upload a photo and try again.</div>
        </div>
        <button id="submit-button" type="submit" class="btn btn-primary" name="_submit">Place order</button>
    </div>

</form>

<script>
    var packages = JSON.parse('{$packages}');
</script>

{literal}
<script>
    var defaultExpeditedDate;

    $Ready(function() {
        $(document).ready(function() {

            defaultExpeditedDate = $('#expedited-date').val();

            $('#expedited').on('change', function () {
                if ($('#expedited').is(":checked")) {
                    $('#expedited-date').attr('required', 'required');
                    $('#delivery-time-section').slideDown();
                }
                else {
                    $('#expedited-date').removeAttr('required');
                    $('#expedited-date').val(defaultExpeditedDate);
                    $('#delivery-time-section').slideUp();
                }

            });

            if (top.location.pathname.includes("/client-dashboard/orders/add")) {

                function getSelectedPackage() {
                    var frameSizeId = $('#frame-size').val();
                    var frameTypeId = $('#frame-type').val();
                    var shippingTypeId = $('#shipping-type').val();

                    for (var i = 0; i < packages.length; i++) {
                        if (packages[i].frame_size_id == frameSizeId && packages[i].frame_type_id == frameTypeId && packages[i].shipping_type_id == shippingTypeId) {
                            return packages[i];
                        }
                    }
                }

                function updateSummary() {
                    var frameSizeId = $('#frame-size').val();
                    var frameTypeId = $('#frame-type').val();
                    var shippingTypeId = $('#shipping-type').val();

                    var selectedPackage = getSelectedPackage();

                    var numFaces = $('#faces').val();

                    if (!selectedPackage || numFaces === '') {
                        $('.order-summary').css('display', 'none');
                    } else {
                        $('#summary-discount-price').html(0.0.toLocaleString('en-US',  { style: 'currency', currency: 'USD' }));
                        $('#discount-line').css('display', 'none');
                        $.ajax({
                            url: "/client-dashboard/orders/add/?ajax=sale&package_id=" + selectedPackage.package_id
                        }).done(function(data) {

                            if (data != false) {
                                $('#summary-discount').html('Discount: ' + data.name + ' (' + data.discount_percentage + '% Off):');

                                var discountAmount = getTotalPrice() * (parseFloat(data.discount_percentage) / 100);
                                $('#summary-discount-price').html('-' + discountAmount.toLocaleString('en-US',  { style: 'currency', currency: 'USD' }));
                                $('#summary-total-price').html((getTotalPrice() - discountAmount).toLocaleString('en-US',  { style: 'currency', currency: 'USD' }));
                                $('#discount-line').css('display', 'block');
                            }
                        });

                        $('#summary-frame-size').html('Frame Size: ' + selectedPackage.frame_size_name + ' (' + selectedPackage.frame_size_description + '):');
                        $('#summary-frame-type').html('Frame Type: ' + selectedPackage.frame_type_name + ' (' + selectedPackage.frame_type_description + '):');
                        $('#summary-shipping-type').html('Shipping Type: ' + selectedPackage.shipping_type_name + ' (' + selectedPackage.shipping_type_description + '):');

                        // Update expedited service summary line:

                        if ($('#expedited').is(':checked')) {
                            $('#summary-expedited-service-line').css('display', 'initial');
                        } else {
                            $('#summary-expedited-service-line').css('display', 'none');
                        }

                        // Update faces price:
                        var faces_price = parseInt($('#faces').val()) === 0 ? 0.00 : (parseInt($('#faces').val()) - 1) * 25.00;
                        $('#summary-faces-price').html(parseFloat(faces_price).toLocaleString('en-US',  { style: 'currency', currency: 'USD' }));

                        // Update faces text:
                        $('#summary-faces').html('Faces (' + $('#faces').val() + '):');

                        $('#summary-frame-size-price').html(parseFloat(selectedPackage.frame_size_price).toLocaleString('en-US',  { style: 'currency', currency: 'USD' }));
                        $('#summary-frame-type-price').html(parseFloat(selectedPackage.frame_type_price).toLocaleString('en-US',  { style: 'currency', currency: 'USD' }));
                        $('#summary-shipping-type-price').html(parseFloat(selectedPackage.shipping_type_price).toLocaleString('en-US',  { style: 'currency', currency: 'USD' }));
                        $('#summary-total-price').html(getTotalPrice().toLocaleString('en-US',  { style: 'currency', currency: 'USD' }));
                        $('.order-summary').css('display', 'block');
                    }
                }

                function getTotalPrice() {
                    var selectedPackage = getSelectedPackage();
                    var expeditedPrice = 0;
                    if ($('#expedited').is(':checked')) {
                        expeditedPrice = 80;
                    }
                    var faces_price = parseInt($('#faces').val()) === 0 ? 0.00 : (parseInt($('#faces').val()) - 1) * 25.00;
                    return (parseFloat(selectedPackage.frame_size_price) + parseFloat(selectedPackage.frame_type_price) + parseFloat(selectedPackage.shipping_type_price) + parseFloat(expeditedPrice) + parseFloat(faces_price));
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

                $('#submit-button').on('click', function () {
                    // If no photo, show error
                    if (!$('.dz-preview').hasClass('dz-success')) {
                        $('#photo-error-empty').css('display', 'block');
                        return false;
                    } else {
                        $('#photo-error-empty').css('display', 'none');
                    }
                });

                // Handle coupon code
                $('#coupon-code-button').on('click', function () {

                    hideCouponMessages();

                    if($('#coupon-code').val().trim() == '') {
                        return;
                    }

                    var frameSizeId = $('#frame-size').val();
                    var frameTypeId = $('#frame-type').val();
                    var shippingTypeId = $('#shipping-type').val();

                    if (frameSizeId == '' || frameTypeId == '' || shippingTypeId == '') {
                        $('#coupon-code-error-message-unknown').css('display', 'block');
                    } else {

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
                    }
                });

                // Handle coupon field
                $('#coupon-code').on('keyup', function () {
                    if ($(this).val().trim() == '') {
                        $('#coupon-code-button').attr('disabled', 'disabled');
                    } else {
                        $('#coupon-code-button').removeAttr('disabled');
                    }
                });

                $('#shipping-address').on('change', function () {
                   if ($('#add-new-address-option').is(':selected')) {
                       top.location.href = '/client-dashboard/addresses/add/';
                   }
                });

                if ($('#add-new-address-option').is(':selected')) {
                    $('#shipping-address').val('');
                }

                $('#frame-size, #frame-type, #shipping-type, #expedited, #faces').on('change', function () {
                    clearCoupon();
                    updateSummary();
                });

            }
        });
    });
</script>

<script type="text/javascript">
    var stylesAreVisible = true;
    (function($) {
        // You pass-in jQuery and then alias it with the $-sign
        // So your internal code doesn't change
        $(".style-picker").imagepicker({
            hide_select : true,
            show_label  : true,
            clicked : function () {
                if (stylesAreVisible) {
                    hideStyles();
                } else {
                    showStyles();
                }
            }
        });

        hideStyles();

        function hideStyles() {
            $('.image_picker_selector .thumbnail:not(.selected)').parent().hide();
            stylesAreVisible = false;
        }

        function showStyles() {
            $('.image_picker_selector .thumbnail').parent().show();
            stylesAreVisible = true;
        }
    })(jQuery);
</script>
{/literal}
{/if}

<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

use Phpfox;

class ClientFirstOrderAdd extends \Phpfox_Component
{
    public function process()
    {
        if (Phpfox::isUser()) {
            url()->send('/');
        }

        $services = [
            'packages' => Phpfox::getService('instapaint.packages'),
            'settings' => Phpfox::getService('instapaint.settings')
        ];

        $frameSizes = $services['packages']->getFrameSizes();
        $frameTypes = $services['packages']->getFrameTypes();
        $shippingTypes = $services['packages']->getShippingTypes();

        $settings = $services['settings']->getSettings();

        $expeditedMinDate = date('Y-m-d', strtotime('+ ' . $settings['expedited_min_days'] . ' days'));

        // The form values
        $vals = $_POST['val'];

        if ($vals) { // Form was sent

            if (empty($vals['frame_size'])) {
                \Phpfox_Error::set(_p('Frame size is required'));
                $error = 'Frame size is required';
            }

            if (empty($vals['frame_type'])) {
                \Phpfox_Error::set(_p('Frame type is required'));
                $error = 'Frame type is required';
            }

            if (empty($vals['shipping_type'])) {
                \Phpfox_Error::set(_p('Shipping is required'));
                $error = 'Shipping is required';
            }

            $expeditedDays = ceil ( ( strtotime($vals['expedited_date']) - time() ) / 60 / 60 / 24 );

            if (empty($vals['expedited_date']) || (int) $expeditedDays < $settings['expedited_min_days']) {
                \Phpfox_Error::set(_p('Please select a valid expedited service date'));
                $error = 'Please select a valid expedited service date';
            }

            if (!isset($vals['style']) || !$services['packages']->isValidStyle($vals['style'])) {
                \Phpfox_Error::set(_p('Please select a valid style'));
                $error = 'Please select a valid style';
            }

            if (\Phpfox_Error::isPassed()) {

                $uniqueId = uniqid();

                $savedPhoto = $services['packages']->savePartialOrderPhoto($_FILES['photo'], $uniqueId);

                if ($savedPhoto[0] == 'success') {
                    // Save order
                    // Check that package exists:
                    $packageExists = $services['packages']->packageExists((int) $vals['frame_size'], (int) $vals['frame_type'], (int) $vals['shipping_type']);

                    if ($packageExists) {
                        // save partial order
                        $savedOrder = $services['packages']->savePartialOrder($vals, $savedPhoto[1]['original'], $savedPhoto[1]['thumbnail'],  $uniqueId, $packageExists['package_id'], $vals['expedited'], $vals['faces'], $expeditedDays, $vals['style']);

                        if ($savedOrder) {
                            $this->url()->send('user/register/?partial_order_id=' . $uniqueId, null, 'One last step! Sign up to Instapaint in seconds.', null, 'success', false);
                        } else {
                            $error = "There was an error adding your order, please try again.";
                        }
                    } else {
                        $error = "Your selection didn't match an available package.";
                    }
                } else {
                    $error = $savedPhoto[1];
                }

            }
        }

        // Include head HTML:
        require_once PHPFOX_DIR_SITE . 'Apps/instapaint/assets/front-page/templates/head.php';

        ?>

        <style>
            #header-upload-button {
                display: none;
            }
            #image-preview[src=""] {
                display: none;
            }
            #photo-upload-button {
                margin-top: 3px;
            }
            button.btn-processing[disabled],
            button.btn-processing[disabled]:hover {
                opacity: 1;
                color: #212529;
                background-color: #ffc107;
                border-color: #ffc107;
                transition-property: none;
                padding-left: 5px;
                padding-right: 5px;
                cursor: progress;
            }
            #photo-upload-button[disabled]:not(.btn-processing),
            #next-step-button[disabled]:not(.btn-processing) {
                background-color: #337ab7;
                border-color: #2e6da4;
                opacity: 0.9;
                cursor: not-allowed !important;
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
                margin-bottom: 3px;
            }

            @media only screen and (max-width: 600px) {
                ul.thumbnails.image_picker_selector li {
                    max-width: 45%;
                }
            }

            @media (min-width: 992px) and (max-width: 1199px) {

                #hero {
                    min-height: 200px !important;
                }
            }

            @media (min-width: 768px) and (max-width: 1199px) {
                #hero {
                    min-height: 170px !important;
                }
            }

            .fade.in {
                z-index: 9999;
            }

            .modal-open .modal {
                z-index: 10000;
            }

            .media-body, .media-left, .media-right {
                width: auto;
            }

            .media-left {
                width: 150px;
            }

            .modal-dialog .media .media-body .media-heading {
                font-size: 24px;
                color: #1ac6ff;
                font-weight: 400;
            }

            .modal-dialog .media .media-body {
                color: #0b4254;
            }

            .style-guide-label {
                margin-top: 5px;
                display: inline-block;
                font-size: 15px;
            }

        </style>
        <script>
            var defaultExpeditedDate;
            var stylesAreVisible = true;

            function hideStyles() {
                $('.image_picker_selector .thumbnail:not(.selected)').parent().hide();
                stylesAreVisible = false;
            }

            function showStyles() {
                $('.image_picker_selector .thumbnail').parent().show();
                stylesAreVisible = true;
            }

            function showSelectedStyleOnly() {
                $('.image_picker_selector .thumbnail:not(.selected)').parent().hide();
                $('.image_picker_selector .thumbnail.selected').parent().show();
                stylesAreVisible = false;
            }

            function selectStyle(styleId) {
                $("#style-picker").val(styleId)
                .data('picker').sync_picker_with_select();
                showSelectedStyleOnly();
            }

            $(function () {

                $('.style-guide-option').on('click', function () {
                    $('#styleGuideModal').modal('hide');
                    selectStyle($(this).attr('data-style-id'));
                    return false;
                });

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

                // Read a page's GET URL variables and return them as an associative array.
                function getUrlVars()
                {
                    var vars = [], hash;
                    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
                    for(var i = 0; i < hashes.length; i++)
                    {
                        hash = hashes[i].split('=');
                        vars.push(hash[0]);
                        vars[hash[0]] = hash[1];
                    }
                    return vars;
                }

                // Set painting style automatically
                if (getUrlVars()['style']) {
                    $('#style-picker').val(getUrlVars()['style']);
                    $("#style-picker").data('picker').sync_picker_with_select();
                }

                hideStyles();

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

                function ProcessImage() {

                    // Disable buttons:
                    $('#next-step-button, #photo-upload-button')
                        .attr('disabled', 'disabled');

                    $('#next-step-button, #photo-upload-button')
                        .removeAttr('disabled')
                        .removeClass('btn-processing');
                    $('#next-step-button').html('Next Step');

                    $('#photo-upload-button').html('Choose file');
                }

                $('#order-form').submit(function () {
                    if (document.getElementById("image-file").files.length === 0) {
                        $('#choose-photo-error-message').css('display', 'block');
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
                        $('#image-preview').css('display', 'none');
                        $('#choose-photo-error-message').css('display', 'block');
                        $('#photo-upload-button').html('Choose file');
                        return false;
                    } else {
                        $('#image-preview').css('display', 'block');
                        $('#choose-photo-error-message').css('display', 'none');

                        ProcessImage();
                    }
                });
            });
        </script>
        <script src="/PF.Site/Apps/instapaint/assets/front-page/js/aws-cognito-sdk.min.js"></script>
        <script src="/PF.Site/Apps/instapaint/assets/front-page/js/amazon-cognito-identity.min.js"></script>
        <script src="/PF.Site/Apps/instapaint/assets/image-picker/image-picker.js"></script>
        <script src="https://sdk.amazonaws.com/js/aws-sdk-2.16.0.min.js"></script>
        <script src="https://unpkg.com/tippy.js@3/dist/tippy.all.min.js"></script>

<div class="static-header light clearfix" id="hero" style="min-height: 100px;">
    &nbsp;
</div>
<div class="container-fluid">
    <div class="row text-center">
        <div class="col-md-8">
            <form class="order-form" id="order-form" method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-12">
                        <h4 style="position: relative !important; text-align: left;"><strong>Order information</strong></h4>
                        <hr>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <?php

                            if ($error) { ?>

                                <div class="alert alert-danger" style="padding-left: 16px;">
                                    <strong>There was a problem: </strong> <?php echo $error; ?>
                                </div>

                                <?php
                            }
                            ?>
                            <div class="alert alert-info" style="padding-left: 16px;">
                                <strong>Already have an account?</strong> You can easily place orders from <a href="/user/login/">your Dashboard!</a>
                            </div>
                            <div class="col-md-4 ">
                                <label>Upload your image</label>
                                <input type="file" name="photo" id="image-file" class="btn btn-sm btn-primary" accept="image/png, image/jpeg" style="display: none">
                                <button id="photo-upload-button" type="button" class="btn btn-primary btn-block" onclick="document.getElementById('image-file').click();">Choose file</button>
                                <br>
                                <img class="img-thumbnail img-responsive" src="" name="image-preview" id="image-preview">
                            </div>
                            <div class="col-md-8 text-left">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Size <small>*Custom sizes available upon request</small></label>
                                        <select class="form-control" id="size" name="val[frame_size]" required="required">
                                            <?php

                                            foreach ($frameSizes as $element) {
                                                echo '<option value="' . $element['frame_size_id'] . '">' .  $element['name_phrase'] . ' - ' . $element['description_phrase'] . ' ($' . $element['price_usd'] . ')';
                                            }

                                            ?>
                                        </select>
                                        <br>
                                    </div>
                                    <div class="col-md-12 margin-top">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label>Frame Type<small></small></label>
                                                <select class="form-control" name="val[frame_type]">
                                                    <?php

                                                    foreach ($frameTypes as $element) {
                                                        echo '<option value="' . $element['frame_type_id'] . '">' .  $element['name_phrase'] . ' - ' . $element['description_phrase'] . ' ($' . $element['price_usd'] . ')';
                                                    }

                                                    ?>
                                                </select>
                                                <br>
                                            </div>
                                            <div class="col-md-12">
                                                <label>Shipping<small> (from the day of order to day of delivery)</small></label>
                                                <select class="form-control" name="val[shipping_type]">
                                                    <?php

                                                    foreach ($shippingTypes as $element) {
                                                        echo '<option value="' . $element['shipping_type_id'] . '">' .  $element['name_phrase'] . ' - ' . $element['description_phrase'] . ' ($' . $element['price_usd'] . ')';
                                                    }

                                                    ?>
                                                </select>
                                                <br>
                                            </div>
                                            <div id="form-faces-section" class="col-md-12">
                                                <label style="cursor: default;" data-tippy-arrow="true" data-tippy-arrow-type="round" data-tippy="The base packages cover 1 face or pet. Since each additional face or pet will require more time and skill, any painting with 2 or more faces or pets will require $25 fee for the additional faces or pets. The first face or pet is free.">Faces & Pets</label>
                                                <select id="select-faces-amount" class="form-control" name="val[faces]" required>
                                                    <?php
                                                    echo '<option value="" selected="selected">Select the total number of faces & pets</option>';
                                                    echo '<option value="0">0 ($0)</option>';
                                                    echo '<option value="1">1 (Free)</option>';
                                                    for ($i = 2; $i <= 10; $i++) {
                                                        $extraFacePrice = 25;
                                                        $price = ($i - 1) * $extraFacePrice;
                                                        echo "<option value='$i'>$i (+$$price)</option>";
                                                    }

                                                    ?>
                                                </select>
                                                <br>
                                            </div>
                                            <div class="col-md-12">
                                                <label>Expedited Service</label>
                                                <div style="margin-bottom: 7px">
                                                    <label style="" class="checkbox-inline"><input id="expedited" type="checkbox" name="val[expedited]" value="1"><span style="top: 5px; position: relative; left: 6px;">Prioritize your painting (+$80)</span></label>
                                                </div>
                                                <br>
                                            </div>
                                            <div class="col-md-12" id="delivery-time-section" style="display: none;">
                                                <label for="expedited-date">Choose your date</label>
                                                <div style="margin-bottom: 7px; margin-top: 5px;">
                                                    <i class="fa fa-calendar" style="margin-right: 3px"></i> <input id="expedited-date" name="val[expedited_date]" value="<?php echo $expeditedMinDate; ?>" type="date" min="<?php echo $expeditedMinDate; ?>" />
                                                </div>
                                                <div style="font-size: 14px">*If the shipment is delayed in transit past the delivery date, we will refund 50% of the expedited fee. Delivery guarantees may be delayed if you request numerous revisions.</div>
                                                <br>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 margin-top">
                                        <label for="notes" data-tippy-arrow="true" data-tippy-arrow-type="round" data-tippy="Click the selected style to change it">Choose your style</label>

                                        <br>
                                        <!-- Button trigger modal -->
                                        <button type="button" class="btn btn-primary style-guide-button" data-toggle="modal" data-target="#styleGuideModal" style="margin-top: 5px; margin-bottom: 5px;">
                                            <span class="glyphicon glyphicon-th-large" aria-hidden="true"></span> View Style Guide
                                        </button>

                                        <!-- Modal -->
                                        <div class="modal fade" id="styleGuideModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                        <h4 class="modal-title" id="myModalLabel" style="color: purple; font-weight: bold;">Style Guide</h4>
                                                    </div>
                                                    <div class="modal-body">

                                                        <div class="media">
                                                            <div class="media-left">
                                                                <a href="#" class="style-guide-option" data-style-id="5">
                                                                    <img class="media-object" src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails-200px/5.jpg">
                                                                </a>
                                                            </div>
                                                            <div class="media-body">
                                                                <h4 class="media-heading"><?= $services['packages']->getStyleInfo(5)['name'] ?></h4>
                                                                Recommended to preserve treasured memories<br>
                                                                <span class="label label-warning style-guide-label">Best Seller</span>
                                                                <?php if ($services['packages']->getStyleInfo(5)['price'] > 0) { ?>
                                                                    <span class="label label-success style-guide-label">+ $<?= $services['packages']->getStyleInfo(5)['price_str'] ?></span>
                                                                <?php } else { ?>
                                                                    <span class="label label-info style-guide-label">Free</span>
                                                                <?php } ?>
                                                            </div>
                                                        </div>

                                                        <div class="media">
                                                            <div class="media-left">
                                                                <a href="#" class="style-guide-option" data-style-id="7">
                                                                    <img class="media-object" src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails-200px/7.jpg">
                                                                </a>
                                                            </div>
                                                            <div class="media-body">
                                                                <h4 class="media-heading"><?= $services['packages']->getStyleInfo(7)['name'] ?></h4>
                                                                Recommended for portraits of children and for extra vibrancy<br>
                                                                <span class="label label-warning style-guide-label">Best Seller</span>
                                                                <?php if ($services['packages']->getStyleInfo(7)['price'] > 0) { ?>
                                                                    <span class="label label-success style-guide-label">+ $<?= $services['packages']->getStyleInfo(7)['price_str'] ?></span>
                                                                <?php } else { ?>
                                                                    <span class="label label-info style-guide-label">Free</span>
                                                                <?php } ?>
                                                            </div>
                                                        </div>

                                                        <div class="media">
                                                            <div class="media-left">
                                                                <a href="#" class="style-guide-option" data-style-id="11">
                                                                    <img class="media-object" src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails-200px/11.jpg">
                                                                </a>
                                                            </div>
                                                            <div class="media-body">
                                                                <h4 class="media-heading"><?= $services['packages']->getStyleInfo(11)['name'] ?></h4>
                                                                Recommended for a flattering self-portrait, bringing attention to the facial features and expression of the subject<br>
                                                                <span class="label label-warning style-guide-label">Best Seller</span>
                                                                <?php if ($services['packages']->getStyleInfo(11)['price'] > 0) { ?>
                                                                    <span class="label label-success style-guide-label">+ $<?= $services['packages']->getStyleInfo(11)['price_str'] ?></span>
                                                                <?php } else { ?>
                                                                    <span class="label label-info style-guide-label">Free</span>
                                                                <?php } ?>
                                                            </div>
                                                        </div>

                                                        <div class="media">
                                                            <div class="media-left">
                                                                <a href="#" class="style-guide-option" data-style-id="17">
                                                                    <img class="media-object" src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails-200px/17.jpg">
                                                                </a>
                                                            </div>
                                                            <div class="media-body">
                                                                <h4 class="media-heading"><?= $services['packages']->getStyleInfo(17)['name'] ?></h4>
                                                                Recommended for a classic, timeless piece<br>
                                                                <?php if ($services['packages']->getStyleInfo(17)['price'] > 0) { ?>
                                                                    <span class="label label-success style-guide-label">+ $<?= $services['packages']->getStyleInfo(17)['price_str'] ?></span>
                                                                <?php } else { ?>
                                                                    <span class="label label-info style-guide-label">Free</span>
                                                                <?php } ?>
                                                            </div>
                                                        </div>

                                                        <div class="media">
                                                            <div class="media-left">
                                                                <a href="#" class="style-guide-option" data-style-id="15">
                                                                    <img class="media-object" src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails-200px/15.jpg">
                                                                </a>
                                                            </div>
                                                            <div class="media-body">
                                                                <h4 class="media-heading"><?= $services['packages']->getStyleInfo(15)['name'] ?></h4>
                                                                For emphasizing very fine details in closeups of objects and faces<br>
                                                                <?php if ($services['packages']->getStyleInfo(15)['price'] > 0) { ?>
                                                                    <span class="label label-success style-guide-label">+ $<?= $services['packages']->getStyleInfo(15)['price_str'] ?></span>
                                                                <?php } else { ?>
                                                                    <span class="label label-info style-guide-label">Free</span>
                                                                <?php } ?>
                                                            </div>
                                                        </div>

                                                        <div class="media">
                                                            <div class="media-left">
                                                                <a href="#" class="style-guide-option" data-style-id="9">
                                                                    <img class="media-object" src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails-200px/9.jpg">
                                                                </a>
                                                            </div>
                                                            <div class="media-body">
                                                                <h4 class="media-heading"><?= $services['packages']->getStyleInfo(9)['name'] ?></h4>
                                                                Recommended to highlight natural beauty with minimal brushstrokes<br>
                                                                <?php if ($services['packages']->getStyleInfo(9)['price'] > 0) { ?>
                                                                    <span class="label label-success style-guide-label">+ $<?= $services['packages']->getStyleInfo(9)['price_str'] ?></span>
                                                                <?php } else { ?>
                                                                    <span class="label label-info style-guide-label">Free</span>
                                                                <?php } ?>
                                                            </div>
                                                        </div>

                                                        <div class="media">
                                                            <div class="media-left">
                                                                <a href="#" class="style-guide-option" data-style-id="18">
                                                                    <img class="media-object" src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails-200px/18.jpg">
                                                                </a>
                                                            </div>
                                                            <div class="media-body">
                                                                <h4 class="media-heading"><?= $services['packages']->getStyleInfo(18)['name'] ?></h4>
                                                                For dramatic lights and shadows, imitating the Renaissance period<br>
                                                                <?php if ($services['packages']->getStyleInfo(18)['price'] > 0) { ?>
                                                                    <span class="label label-success style-guide-label">+ $<?= $services['packages']->getStyleInfo(18)['price_str'] ?></span>
                                                                <?php } else { ?>
                                                                    <span class="label label-info style-guide-label">Free</span>
                                                                <?php } ?>
                                                            </div>
                                                        </div>

                                                        <div class="media">
                                                            <div class="media-left">
                                                                <a href="#" class="style-guide-option" data-style-id="12">
                                                                    <img class="media-object" src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails-200px/12.jpg">
                                                                </a>
                                                            </div>
                                                            <div class="media-body">
                                                                <h4 class="media-heading"><?= $services['packages']->getStyleInfo(12)['name'] ?></h4>
                                                                For very clera and crisp lines and patters, recommended for portraits of cars, buildings, landmarks, or an intricate background<br>
                                                                <?php if ($services['packages']->getStyleInfo(12)['price'] > 0) { ?>
                                                                    <span class="label label-success style-guide-label">+ $<?= $services['packages']->getStyleInfo(12)['price_str'] ?></span>
                                                                <?php } else { ?>
                                                                    <span class="label label-info style-guide-label">Free</span>
                                                                <?php } ?>
                                                            </div>
                                                        </div>

                                                        <div class="media">
                                                            <div class="media-left">
                                                                <a href="#" class="style-guide-option" data-style-id="13">
                                                                    <img class="media-object" src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails-200px/13.jpg">
                                                                </a>
                                                            </div>
                                                            <div class="media-body">
                                                                <h4 class="media-heading"><?= $services['packages']->getStyleInfo(13)['name'] ?></h4>
                                                                Recommended for a warm, nostalgic feeling imitating a flashback scene in the cinema<br>
                                                                <?php if ($services['packages']->getStyleInfo(13)['price'] > 0) { ?>
                                                                    <span class="label label-success style-guide-label">+ $<?= $services['packages']->getStyleInfo(13)['price_str'] ?></span>
                                                                <?php } else { ?>
                                                                    <span class="label label-info style-guide-label">Free</span>
                                                                <?php } ?>
                                                            </div>
                                                        </div>

                                                        <div class="media">
                                                            <div class="media-left">
                                                                <a href="#" class="style-guide-option" data-style-id="6">
                                                                    <img class="media-object" src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails-200px/6.jpg">
                                                                </a>
                                                            </div>
                                                            <div class="media-body">
                                                                <h4 class="media-heading"><?= $services['packages']->getStyleInfo(6)['name'] ?></h4>
                                                                For unique expression using spontaneous brushstrokes and colors<br>
                                                                <?php if ($services['packages']->getStyleInfo(6)['price'] > 0) { ?>
                                                                    <span class="label label-success style-guide-label">+ $<?= $services['packages']->getStyleInfo(6)['price_str'] ?></span>
                                                                <?php } else { ?>
                                                                    <span class="label label-info style-guide-label">Free</span>
                                                                <?php } ?>
                                                            </div>
                                                        </div>

                                                        <div class="media">
                                                            <div class="media-left">
                                                                <a href="#" class="style-guide-option" data-style-id="2">
                                                                    <img class="media-object" src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails-200px/2.jpg">
                                                                </a>
                                                            </div>
                                                            <div class="media-body">
                                                                <h4 class="media-heading"><?= $services['packages']->getStyleInfo(2)['name'] ?></h4>
                                                                Just as the name suggests, your artist will put your face on a regal suit<br>
                                                                <?php if ($services['packages']->getStyleInfo(2)['price'] > 0) { ?>
                                                                    <span class="label label-success style-guide-label">+ $<?= $services['packages']->getStyleInfo(2)['price_str'] ?></span>
                                                                <?php } else { ?>
                                                                    <span class="label label-info style-guide-label">Free</span>
                                                                <?php } ?>
                                                            </div>
                                                        </div>

                                                        <div class="media">
                                                            <div class="media-left">
                                                                <a href="#" class="style-guide-option" data-style-id="3">
                                                                    <img class="media-object" src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails-200px/3.jpg">
                                                                </a>
                                                            </div>
                                                            <div class="media-body">
                                                                <h4 class="media-heading"><?= $services['packages']->getStyleInfo(3)['name'] ?></h4>
                                                                For a bohemian/hippie result using shattered brushstrokes and prism lighting effect<br>
                                                                <?php if ($services['packages']->getStyleInfo(3)['price'] > 0) { ?>
                                                                    <span class="label label-success style-guide-label">+ $<?= $services['packages']->getStyleInfo(3)['price_str'] ?></span>
                                                                <?php } else { ?>
                                                                    <span class="label label-info style-guide-label">Free</span>
                                                                <?php } ?>
                                                            </div>
                                                        </div>

                                                        <div class="media">
                                                            <div class="media-left">
                                                                <a href="#" class="style-guide-option" data-style-id="14">
                                                                    <img class="media-object" src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails-200px/14.jpg">
                                                                </a>
                                                            </div>
                                                            <div class="media-body">
                                                                <h4 class="media-heading"><?= $services['packages']->getStyleInfo(14)['name'] ?></h4>
                                                                For a playful and magical painting using rich rainbow colors<br>
                                                                <?php if ($services['packages']->getStyleInfo(14)['price'] > 0) { ?>
                                                                    <span class="label label-success style-guide-label">+ $<?= $services['packages']->getStyleInfo(14)['price_str'] ?></span>
                                                                <?php } else { ?>
                                                                    <span class="label label-info style-guide-label">Free</span>
                                                                <?php } ?>
                                                            </div>
                                                        </div>

                                                        <div class="media">
                                                            <div class="media-left">
                                                                <a href="#" class="style-guide-option" data-style-id="16">
                                                                    <img class="media-object" src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails-200px/16.jpg">
                                                                </a>
                                                            </div>
                                                            <div class="media-body">
                                                                <h4 class="media-heading"><?= $services['packages']->getStyleInfo(16)['name'] ?></h4>
                                                                For palette knife strokes to create a 3D effect. Recommended for home decor paintings<br>
                                                                <?php if ($services['packages']->getStyleInfo(16)['price'] > 0) { ?>
                                                                    <span class="label label-success style-guide-label">+ $<?= $services['packages']->getStyleInfo(16)['price_str'] ?></span>
                                                                <?php } else { ?>
                                                                    <span class="label label-info style-guide-label">Free</span>
                                                                <?php } ?>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <select id="style-picker" name="val[style]" class="image-picker style-picker">
                                            <option data-img-src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails-200px/0.jpg" data-img-label="<div class='style-picker-name'>Artist's Choice</div>" data-img-class="artists-choice" data-img-alt="Artist's Choice" value="0">Artist's Choice</option>
                                            <option data-img-src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails-200px/5-best-seller.jpg" data-img-label="<div class='style-picker-name'><?= $services['packages']->getStyleInfo(5)['name'] ?></div><?php if ($services['packages']->getStyleInfo(5)['price'] > 0) { ?> <span class='style-picker-price label label-success style-guide-label'>+ $<?= $services['packages']->getStyleInfo(5)['price_str'] ?></span><?php } else { ?><span class='style-picker-price label label-info style-guide-label'>Free</span><?php } ?>" data-img-class="style-5" value="5">Photorealistic</option>
                                            <option data-img-src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails-200px/7-best-seller.jpg" data-img-label="<div class='style-picker-name'><?= $services['packages']->getStyleInfo(7)['name'] ?></div><?php if ($services['packages']->getStyleInfo(7)['price'] > 0) { ?> <span class='style-picker-price label label-success style-guide-label'>+ $<?= $services['packages']->getStyleInfo(7)['price_str'] ?></span><?php } else { ?><span class='style-picker-price label label-info style-guide-label'>Free</span><?php } ?>" data-img-class="style-7" value="7">Enhanced Colors</option>
                                            <option data-img-src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails-200px/11-best-seller.jpg" data-img-label="<div class='style-picker-name'><?= $services['packages']->getStyleInfo(11)['name'] ?></div><?php if ($services['packages']->getStyleInfo(11)['price'] > 0) { ?> <span class='style-picker-price label label-success style-guide-label'>+ $<?= $services['packages']->getStyleInfo(11)['price_str'] ?></span><?php } else { ?><span class='style-picker-price label label-info style-guide-label'>Free</span><?php } ?>" data-img-class="style-11" value="11">Soft Focus</option>
                                            <option data-img-src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails-200px/17.jpg" data-img-label="<div class='style-picker-name'><?= $services['packages']->getStyleInfo(17)['name'] ?></div><?php if ($services['packages']->getStyleInfo(17)['price'] > 0) { ?> <span class='style-picker-price label label-success style-guide-label'>+ $<?= $services['packages']->getStyleInfo(17)['price_str'] ?></span><?php } else { ?><span class='style-picker-price label label-info style-guide-label'>Free</span><?php } ?>" data-img-class="style-17" value="17">Black & White</option>
                                            <option data-img-src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails-200px/15.jpg" data-img-label="<div class='style-picker-name'><?= $services['packages']->getStyleInfo(15)['name'] ?></div><?php if ($services['packages']->getStyleInfo(15)['price'] > 0) { ?> <span class='style-picker-price label label-success style-guide-label'>+ $<?= $services['packages']->getStyleInfo(15)['price_str'] ?></span><?php } else { ?><span class='style-picker-price label label-info style-guide-label'>Free</span><?php } ?>" data-img-class="style-15" value="15">Delicate Brushwork</option>
                                            <option data-img-src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails-200px/9.jpg" data-img-label="<div class='style-picker-name'><?= $services['packages']->getStyleInfo(9)['name'] ?></div><?php if ($services['packages']->getStyleInfo(9)['price'] > 0) { ?> <span class='style-picker-price label label-success style-guide-label'>+ $<?= $services['packages']->getStyleInfo(9)['price_str'] ?></span><?php } else { ?><span class='style-picker-price label label-info style-guide-label'>Free</span><?php } ?>" data-img-class="style-9" value="9">Natural</option>
                                            <option data-img-src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails-200px/18.jpg" data-img-label="<div class='style-picker-name'><?= $services['packages']->getStyleInfo(18)['name'] ?></div><?php if ($services['packages']->getStyleInfo(18)['price'] > 0) { ?> <span class='style-picker-price label label-success style-guide-label'>+ $<?= $services['packages']->getStyleInfo(18)['price_str'] ?></span><?php } else { ?><span class='style-picker-price label label-info style-guide-label'>Free</span><?php } ?>" data-img-class="style-18" value="18">Dramatic Depth</option>
                                            <option data-img-src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails-200px/12.jpg" data-img-label="<div class='style-picker-name'><?= $services['packages']->getStyleInfo(12)['name'] ?></div><?php if ($services['packages']->getStyleInfo(12)['price'] > 0) { ?> <span class='style-picker-price label label-success style-guide-label'>+ $<?= $services['packages']->getStyleInfo(12)['price_str'] ?></span><?php } else { ?><span class='style-picker-price label label-info style-guide-label'>Free</span><?php } ?>" data-img-class="style-12" value="12">Structured</option>
                                            <option data-img-src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails-200px/13.jpg" data-img-label="<div class='style-picker-name'><?= $services['packages']->getStyleInfo(13)['name'] ?></div><?php if ($services['packages']->getStyleInfo(13)['price'] > 0) { ?> <span class='style-picker-price label label-success style-guide-label'>+ $<?= $services['packages']->getStyleInfo(13)['price_str'] ?></span><?php } else { ?><span class='style-picker-price label label-info style-guide-label'>Free</span><?php } ?>" data-img-class="style-13" value="13">Soft Blur</option>
                                            <option data-img-src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails-200px/6.jpg" data-img-label="<div class='style-picker-name'><?= $services['packages']->getStyleInfo(6)['name'] ?></div><?php if ($services['packages']->getStyleInfo(6)['price'] > 0) { ?> <span class='style-picker-price label label-success style-guide-label'>+ $<?= $services['packages']->getStyleInfo(6)['price_str'] ?></span><?php } else { ?><span class='style-picker-price label label-info style-guide-label'>Free</span><?php } ?>" data-img-class="style-6" value="6">Abstract Expressionism</option>
                                            <option data-img-src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails-200px/2.jpg" data-img-label="<div class='style-picker-name'><?= $services['packages']->getStyleInfo(2)['name'] ?></div><?php if ($services['packages']->getStyleInfo(2)['price'] > 0) { ?> <span class='style-picker-price label label-success style-guide-label'>+ $<?= $services['packages']->getStyleInfo(2)['price_str'] ?></span><?php } else { ?><span class='style-picker-price label label-info style-guide-label'>Free</span><?php } ?>" data-img-class="style-2" value="2">Make Me Royal</option>
                                            <option data-img-src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails-200px/3.jpg" data-img-label="<div class='style-picker-name'><?= $services['packages']->getStyleInfo(3)['name'] ?></div><?php if ($services['packages']->getStyleInfo(3)['price'] > 0) { ?> <span class='style-picker-price label label-success style-guide-label'>+ $<?= $services['packages']->getStyleInfo(3)['price_str'] ?></span><?php } else { ?><span class='style-picker-price label label-info style-guide-label'>Free</span><?php } ?>" data-img-class="style-3" value="3">Whimsical</option>
                                            <option data-img-src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails-200px/14.jpg" data-img-label="<div class='style-picker-name'><?= $services['packages']->getStyleInfo(14)['name'] ?></div><?php if ($services['packages']->getStyleInfo(14)['price'] > 0) { ?> <span class='style-picker-price label label-success style-guide-label'>+ $<?= $services['packages']->getStyleInfo(14)['price_str'] ?></span><?php } else { ?><span class='style-picker-price label label-info style-guide-label'>Free</span><?php } ?>" data-img-class="style-14" value="14">Rainbow Brushwork</option>
                                            <option data-img-src="/PF.Site/Apps/instapaint/assets/gallery-thumbnails-200px/16.jpg" data-img-label="<div class='style-picker-name'><?= $services['packages']->getStyleInfo(16)['name'] ?></div><?php if ($services['packages']->getStyleInfo(16)['price'] > 0) { ?> <span class='style-picker-price label label-success style-guide-label'>+ $<?= $services['packages']->getStyleInfo(16)['price_str'] ?></span><?php } else { ?><span class='style-picker-price label label-info style-guide-label'>Free</span><?php } ?>" data-img-class="style-16" value="16">3D Effect</option>
                                        </select>
                                    </div>

                                    <div class="col-md-12 margin-top">
                                        <label for="notes">Order Notes</label>
                                        <textarea class="form-control" name="val[order_notes]" id="notes" rows="4" placeholder="Write any clarifications or requirements to your artist here."><?php echo $vals['order_notes']; ?></textarea>
                                    </div>

                                    <div class="col-md-12" id="choose-photo-error-message" style="display: none;">
                                        <div class="alert alert-danger" style="padding-left: 15px; padding-right: 15px; margin-top: 15px; margin-bottom: 0;">
                                            Please upload an image.
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <br>
                                        <button id="next-step-button" class="btn btn-primary btn-block">Next Step</button>
                                    </div>

                                    <div class="col-md-12" style="margin-top: 50px">
                                        <img src="/PF.Site/Apps/instapaint/assets/front-page/img/paypal-credit.jpeg" />
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-11 col-md-offset-1">
                    <br><br><br>
                    <h4>InstaPaint <span class="highlight">Explained</span></h4>
                    <span>InstaPaint makes getting a professional oil painting a breeze. Simply upload your photo, and we take care of the rest.</span>
                </div>
                <div class="col-md-11 col-md-offset-1">
                    <br>
                    <p class="lead">With paintings starting at an incredible $79 (for what normally costs thousands), now anyone can turn their favorite photos into professional, hand painted, works of art.</p>
                    <p class="lead">You won't find better prices at this quality with the same fast turnaround time. Our artists specialize 100% on oil paintings.</p>
                    <img src="/PF.Site/Apps/instapaint/assets/front-page/img/features/people.jpg" class="img-responsive" alt="process 3" style="margin: 0 auto;">
                </div>
            </div>
        </div>
    </div>
    <!-- /.row -->
</div>
<br>
<br>

<?php

        // Include footer HTML:
        require_once PHPFOX_DIR_SITE . 'Apps/instapaint/assets/front-page/templates/footer.php';


        exit();
        // Get the services we need
        $services = [
            'instapaint' => Phpfox::getService('instapaint'),
            'security' => Phpfox::getService('instapaint.security'),
            'client' => Phpfox::getService('instapaint.client')
        ];

        // Allow access to painters only
        $services['security']->allowAccess([
            $services['security']::CLIENT_GROUP_ID
        ]);



        // Get phpFox core template service
        $template = $this->template();

        // Set view title
        $template->setTitle('Client Dashboard » Add Address');

        // Font Awesome
        $instapaintService = \Phpfox::getService('instapaint');
        $template->setHeader([
            $instapaintService::FONT_AWESOME_LINK
        ]);

        $template->setBreadCrumb('Add Address');

        // Build menu
        $template->buildSectionMenu('admin-dashboard', $services['instapaint']->insertMenuAfter(
            $services['instapaint']->getClientDashboardMenu(), // Menus array
            'My Addresses', // Reference menu name
            ['Add Address' => 'client-dashboard.addresses.add'] // Menu to be inserted
        ));

        // Get country list:
        $countries = $services['client']->getCountries();

        // Pass security token to template:
        $template->assign([
            'token' => $services['security']->getCSRFToken(),
            'countries' => $countries
        ]);

    }
}

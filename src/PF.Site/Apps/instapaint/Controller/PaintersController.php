<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

use Phpfox;

class PaintersController extends \Phpfox_Component
{
    public function process()
    {
        if (Phpfox::isUser()) {
            url()->send('/');
        }

        $services = [
            'packages' => Phpfox::getService('instapaint.packages')
        ];

        $frameSizes = $services['packages']->getFrameSizes();
        $frameTypes = $services['packages']->getFrameTypes();
        $shippingTypes = $services['packages']->getShippingTypes();

        // The form values
        $vals = $_POST['val'];

        if ($vals) { // Form was sent

            if (empty($vals['frame_size'])) {
                \Phpfox_Error::set(_p('Frame size is required'));
            }

            if (empty($vals['frame_type'])) {
                \Phpfox_Error::set(_p('Frame type is required'));
            }

            if (empty($vals['shipping_type'])) {
                \Phpfox_Error::set(_p('Shipping is required'));
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
                        $savedOrder = $services['packages']->savePartialOrder($vals, $savedPhoto[1]['original'], $savedPhoto[1]['thumbnail'],  $uniqueId, $packageExists['package_id']);

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

            @media (max-width: 480px) {
                div.body-text {
                    padding: 0px 35px;
                    font-size: 16px;
                    line-height: 25px;
                }
                .alert.alert-info {
                    padding-left: 16px;
                }
            }

            .body-text {
                position: relative;
                display: block;
                padding: 0 170px;
                text-align: center;
                font-size: 20px;
                line-height: 33px;
                border: 0;
                font-weight: 300;
                margin-bottom: 35px
            }

            @media (min-width: 992px) and (max-width: 1199px) {

                #feedback.section .section-header {
                    margin-top: 80px;
                }
            }

            @media (min-width: 768px) and (max-width: 1199px) {
                #feedback.section .section-header {
                    margin-top: 80px;
                }
            }
        </style>
<div class="static-header light clearfix" id="hero" style="min-height: 100px;">
    &nbsp;
</div>
        <section id="feedback" class="section light">
            <div class="container">
                <div class="section-header animated hiding" data-animation="fadeInDown">
                    <h2>Become a <span class="highlight">Painter</span> on InstaPaint</h2>
                </div>
                <div class="section-content">

                    <div class="body-text" style="margin-bottom: 55px">
                        Join our team of professional artists and turn your talent into earnings by creating amazing oil paintings from photos!
                    </div>
                </div>
            </div>
        </section>

        <div class="alert alert-info" style="text-align: center">
            <strong>Already have an account?</strong> Login to <a href="/user/login/">your Dashboard!</a>
        </div>
        <section id="features-list" class="section dark">

            <div class="container animated hiding" data-animation="fadeInDown">
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <article class="center">
                        <i class="icon icon-badges-votes-05 icon-active"></i>
                        <span class="h7">Get Approved</span>
                        <p class="thin">Become one of our approved professional painters.</p>
                    </article>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <article class="center">
                        <i class="icon icon-graphic-design-04 icon-active"></i>
                        <span class="h7">Choose What To Paint</span>
                        <p class="thin">You are free to choose the photos you want to paint.</p>
                    </article>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <article class="center">
                        <i class="icon icon-shopping-03 icon-active"></i>
                        <span class="h7">Make Money</span>
                        <p class="thin">Make money with your talent and turn your passion into earnings.</p>
                    </article>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <article class="center">
                        <i class="icon icon-badges-votes-16 icon-active"></i>
                        <span class="h7">Build Your Portfolio</span>
                        <p class="thin">Build your reputation with a portfolio of awesome paintings.</p>
                    </article>
                </div>
            </div>

            <a id="painters-become-painter-button-1" style="margin-bottom: 0; margin-top: 30px" href="/user/register/?user_type=painter" class="cta-become-painter btn btn-primary btn-lg animated bounceIn visible" data-animation="bounceIn" data-delay="700">Become a painter</a>
        </section>
<br>
<br>

<?php

        // Include footer HTML:
        require_once PHPFOX_DIR_SITE . 'Apps/instapaint/assets/front-page/templates/footer.php';


        exit();

    }
}

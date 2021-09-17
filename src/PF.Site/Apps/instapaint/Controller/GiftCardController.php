<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

use Phpfox;

class GiftCardController extends \Phpfox_Component
{
    public function process()
    {
        if (Phpfox::isUser()) {
            url()->send('/');
        }

        $stripeTest = false;

        if ($stripeTest) {
            $stripe = [
                'secret_key' => 'sk_test_d2kZuachjcAQe1i2Pv32LXkN',
                'publishable_key' => 'pk_test_hbKDmTUs5ch0YGYgcEyh3qSm'
            ];
        } else {
            $stripe = [
                'secret_key' => 'sk_live_VSCwjJsCZdg1C8sOkaRPVAee',
                'publishable_key' => 'pk_live_wS8tCxPTa6IgWIi2yDBPfLcA'
            ];
        }

        \Stripe\Stripe::setApiKey($stripe['secret_key']);

        $services = [
            'giftCards' => Phpfox::getService('instapaint.gift-cards')
        ];

        // Gift card prices:
        $giftCardPrices = $services['giftCards']->getPrices();

        // The form values
        $vals = $_POST['val'];

        if ($vals) { // Form was sent


            if (empty($vals['gift_card_value'])) {
                \Phpfox_Error::set(_p('Gift card value is required'));
                $error = 'Gift card value is required';
            }

            if (!array_key_exists($vals['gift_card_value'], $giftCardPrices)) {
                \Phpfox_Error::set(_p('Gift card value is not valid'));
                $error = 'Gift card value is not valid';
            }

            if (empty($vals['client_name'])) {
                \Phpfox_Error::set(_p("Client's name is required"));
                $error = "Client's name is required";
            }

            if (empty($vals['client_email'])) {
                \Phpfox_Error::set(_p("Client's email is required"));
                $error = "Client's email is required";
            }

            if (empty($vals['recipient_name'])) {
                \Phpfox_Error::set(_p("Recipient's name is required"));
                $error = "Recipient's name is required";
            }

            if (empty($vals['recipient_email'])) {
                \Phpfox_Error::set(_p("Recipient's email is required"));
                $error = "Recipient's email is required";
            }

            if (\Phpfox_Error::isPassed()) {

                // Process with Stripe:

                $token  = $vals['stripe_token'];
                $email  = $vals['client_email'];

                $customer = \Stripe\Customer::create([
                    'email' => $email,
                    'source'  => $token,
                ]);

                $charge = \Stripe\Charge::create([
                    'customer' => $customer->id,
                    'amount'   => $giftCardPrices[$vals['gift_card_value']],
                    'currency' => 'usd',
                ]);

                if ($charge->status === 'succeeded') {

                    $savedPurchase = $services['giftCards']->savePurchase(
                            $vals['gift_card_value'],
                            $giftCardPrices[$vals['gift_card_value']] / 100,
                            $vals['client_name'],
                            $vals['client_email'],
                            $vals['recipient_name'],
                            $vals['recipient_email'],
                            $customer->id,
                            $charge->id
                    );

                    if ($savedPurchase) {
                        //$this->url()->send('user/register/?partial_order_id=' . $uniqueId, null, 'One last step! Sign up to Instapaint in seconds.', null, 'success', false);
                        $success = "Congratulations! Your purchase was successful.";
                    } else {
                        $error = "There was an error processing your payment, please try again.";
                    }

                } else {
                    $error = "There was an error processing your payment, please try again.";
                }
            }
        }

        // Include head HTML:
        require_once PHPFOX_DIR_SITE . 'Apps/instapaint/assets/front-page/templates/head.php';

        ?>
        <style>

            #buy-giftcard-button {
                margin-top: 15px;
                margin-left: auto;
                margin-right: auto;
                display: block;
            }

            .terms-section div {
                border: 1px solid grey;
                padding: 30px;
                margin-bottom: 30px;
                margin-top: 30px;
            }

            #content-section p {
                line-height: 32px;
            }

            #content-section {
                padding-top: 60px;
                padding-bottom: 20px;
            }

            #header-upload-button {
                display: none;
            }
            #image-preview[src=""] {
                display: none;
            }

            @media (max-width: 480px) {
                div.body-text {
                    padding: 0px 35px;
                    font-size: 20px;
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

            #giftcard-form,
            #giftcard-form .form-control {
                font-size: 20px;
            }

            #giftcard-form .form-control {
                height: 38px;
            }

            #giftcard-form {
                margin-top: 20px;
                margin-bottom: 20px;
            }

            p,
            .body-text {
                font-size: 20px;
            }

            h2 {
                font-size: 54px;
            }

            #buy-giftcard-button {
                font-size: 18px;
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

            @media (max-width: 800px) {
                #footer {
                    background-attachment: scroll !important;
                }
            }

        </style>

        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
<div class="static-header light clearfix" id="hero" style="min-height: 100px;">
    &nbsp;
</div>
        <section id="feedback" class="section light">
            <div class="container">
                <div class="section-header animated hiding" data-animation="fadeInDown">
                    <h2>InstaPaint <span class="highlight">Gift Card</span></h2>
                </div>
                <div class="section-content">
                    <div class="body-text" style="margin-bottom: 55px">
                        Give the gift of custom artwork with this gift card redeemable in our online store!
                    </div>
                </div>
            </div>
        </section>

        <section id="content-section" class="section dark" style="text-align: left">

            <div class="section-content row">
                <div class="col-sm-12 col-md-8 col-md-offset-2 animated fadeInLeft visible" data-animation="fadeInLeft">

                    <?php

                    if ($error) { ?>

                        <div class="alert alert-danger" style="padding-left: 16px;">
                            <strong>There was a problem: </strong> <?php echo $error; ?>
                        </div>

                        <?php
                    }

                    if ($success) { ?>

                        <div class="alert alert-success" style="padding-left: 16px;">
                            <?php echo $success; ?>
                        </div>

                        <?php
                    }
                    ?>

                    <p><strong>Order today. Receive your virtual gift card number for immediate use.</strong></p>
                    <p><img src="/PF.Site/Apps/instapaint/assets/front-page/img/gift-card.jpg" /></p>
                    <p><strong>CARD SALE</strong> - Get up to $100 off a gift card instantly with discounted pricing below.</p>
                </div>
            </div>

            <div class="section-content row">
                <div class="col-sm-12 col-md-8 col-md-offset-2 animated fadeInLeft visible" data-animation="fadeInLeft">

                    <div class="col-md-12">
                        <form id="giftcard-form" method="POST">
                            <label for="giftcard-value-input" style="margin-bottom: 7px"><i class="fas fa-dollar-sign"></i> Card value</label>
                            <select class="form-control" id="giftcard-value-input" name="val[gift_card_value]" required="required">
                                <?php
                                    foreach ($giftCardPrices as $cardValue => $cardPrice) {
                                ?>
                                    <option value="<?= $cardValue ?>" <?php if ($cardValue == $vals['gift_card_value']) { echo 'selected'; } ?>>$<?= $cardValue ?> Gift Card for $<?= $cardPrice / 100 ?></option>
                                <?php
                                    }
                                ?>


                            </select>

                            <br>

                            <br>
                            <label for="your-name" style="margin-bottom: 7px"><i class="far fa-user"></i> Your name</label>
                            <input class="form-control" id="your-name" name="val[client_name]" required value="<?= $vals['client_name'] ?>">

                            <br>
                            <label for="your-email" style="margin-bottom: 7px"><i class="far fa-envelope"></i> Your email</label>
                            <input class="form-control" id="your-email" name="val[client_email]" required type="email" value="<?= $vals['client_email'] ?>">

                            <br>

                            <br>
                            <label for="recipient-name" style="margin-bottom: 7px"><i class="far fa-user"></i> Recipient's name</label>
                            <input class="form-control" id="recipient-name" name="val[recipient_name]" required value="<?= $vals['recipient_name'] ?>">

                            <br>
                            <label for="recipient-email" style="margin-bottom: 7px"><i class="far fa-envelope"></i> Recipient's email</label>
                            <input class="form-control" id="recipient-email" name="val[recipient_email]" required type="email" value="<?= $vals['recipient_email'] ?>">

                            <input hidden id="stripe-token-id" name="val[stripe_token]">

                            <br>

                            <button id="buy-giftcard-button" class="btn btn-primary">Buy Gift Card</button>

                            <script src="https://checkout.stripe.com/checkout.js"></script>

                            <script>
                                var giftCardPrices = JSON.parse('<?= $services["giftCards"]->getPricesJson() ?>');

                                var handler = StripeCheckout.configure({
                                    key: '<?= $stripe['publishable_key'] ?>',
                                    image: '/PF.Site/Apps/instapaint/assets/front-page/img/stripe-image.jpg',
                                    locale: 'en',
                                    token: function(token) {
                                        // You can access the token ID with `token.id`.
                                        // Get the token ID to your server-side code for use.
                                        $('#stripe-token-id').val(token.id);
                                        $('#giftcard-form').submit();
                                    }
                                });

                                document.getElementById('giftcard-form').addEventListener('submit', function(e) {
                                    // Open Checkout with further options:
                                    handler.open({
                                        name: 'InstaPaint',
                                        description: '$' + $('#giftcard-value-input').val() + ' Gift Card',
                                        amount: giftCardPrices[$('#giftcard-value-input').val()],
                                        email: $('#your-email').val(),
                                        panelLabel: 'Buy Gift Card ({{amount}})'
                                    });
                                    e.preventDefault();
                                });

                                // Close Checkout on page navigation:
                                window.addEventListener('popstate', function() {
                                    handler.close();
                                });
                            </script>
                        </form>
                    </div>

                </div>
            </div>

            <div class="section-content row terms-section">
                <div class="col-sm-12 col-md-8 col-md-offset-2 animated fadeInLeft visible" data-animation="fadeInLeft">
                    <p><strong>By placing an order,</strong> you agree to be bound by our Terms of Service, our Terms of Sale. Custom work is not returnable or cancellable once work is started, but you can order risk free with our 100% Money Back Guarantee. All orders are sold and shipped directly by our partner galleries and studios while fully protected by Instapaint.com from fraud, damage, or loss until delivery.</p>
                    <p><strong>Free 24 hour worry-free cancellation.</strong> Made a mistake or changed your mind? No worries. Just email us within 24 hours of ordering to cancel.</p>
                    <p><strong>#1 in customer support.</strong> Get direct-from-artist support for your order while enjoying Instapaint-backed purchase protection to ensure you get exaxctly what you ordered.</p>
                    <p><strong>Most orders delivered to your door within 1-3 weeks with expedited shipping selected.</strong> Actual times may vary. Submit an order to see estimates from various artists.</p>
                </div>
            </div>

            <div class="section-content row">
                <div class="col-sm-12 col-md-8 col-md-offset-2 animated fadeInLeft visible" data-animation="fadeInLeft">
                    <p><strong>We ship worldwide,</strong> however taxes and tariffs for countries outside the United States are the buyer's responsibility. UK purchasers are required to pay the VAT to receive items.</p>
                </div>
            </div>

        </section>
<br>
<br>

<?php

        // Include footer HTML:
        require_once PHPFOX_DIR_SITE . 'Apps/instapaint/assets/front-page/templates/footer.php';


        exit();

    }
}

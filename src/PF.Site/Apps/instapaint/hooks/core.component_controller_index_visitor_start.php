<?php

// Include head HTML:
require_once PHPFOX_DIR_SITE . 'Apps/instapaint/assets/front-page/templates/head.php';

?>

    <style>
        _:-ms-fullscreen, :root .ie11up { display: inline; }
        .check-ie {
            display: none;
        }

        .slick-prev:before, .slick-next:before {
            color: #1ac6ff;
            font-size: 45px;
        }

        .slick-prev {
            left: -50px;
        }

        @media only screen and (max-width: 768px) {
            .slick-prev {
                left: -27px;
            }

            .slick-next {
                right: -12px;
            }

            button.slick-prev.slick-arrow,
            button.slick-next.slick-arrow {
                z-index: 99;
            }

            .slick-prev:before, .slick-next:before {
                color: #1ac6ff;
                font-size: 35px;
            }
        }
    </style>

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css" integrity="sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz" crossorigin="anonymous">

    <link href="/PF.Site/Apps/instapaint/assets/front-page/library/lightbox2/css/lightbox.css" rel="stylesheet">

    <script src="/PF.Site/Apps/instapaint/assets/instashow/elfsight-instagram-feed.js"></script>
    <script src="/PF.Site/Apps/instapaint/assets/front-page/library/lightbox2/js/lightbox.min.js"></script>

    <script>
        lightbox.option({
            'resizeDuration': 0,
            'imageFadeDuration': 0,
            'fadeDuration': 200
        })
    </script>

    <div id="hero" class="static-header light clearfix">
    <div class="video-wrapper">
        <div class="container" style="top: 105px">
            <div class="row">
                <div class="large-6 columns splash-text">
                    <h3 class="animated hiding" data-animation="bounceInDown" data-delay="0">Convert Any <span class="highlight" id="quotes">Family Portrait,Landscape Photo,Personal Photo,Group Photo,Selfie Pic,Wedding Photo,Childhood Pic</span><BR>Into a 100% Hand-Painted Oil Painting</h3>

                    <p class="animated hiding" data-animation="fadeInDown" data-delay="500" style="font-size: 18px;">
                        <i class="fas fa-check"><span class="check-ie ie11up">&#9745;</span></i> &nbsp;Paintings starting at the best value price of $79<br>
                        <i class="fas fa-check"><span class="check-ie ie11up">&#9745;</span></i> &nbsp;Satisfaction guaranteed or full refund<br>
                        <i class="fas fa-check"><span class="check-ie ie11up">&#9745;</span></i> &nbsp;Free shipping worldwide<br>
                        <i class="fas fa-check"><span class="check-ie ie11up">&#9745;</span></i> &nbsp;Complimentary gift wrap option<br>
                        <i class="fas fa-check"><span class="check-ie ie11up">&#9745;</span></i> &nbsp;Choose your Artist<br>
                        <i class="fas fa-certificate"><span class="check-ie ie11up">&#9745;</span></i> &nbsp;New! Add a personalised message to your painting!<br>
                    </p>

                    <ul class="list-inline order-now" style="padding-top: 15px">
                        <li><a id="home-buy-button-1" href="/first-order/" class="cta-buy btn btn-primary btn-lg animated hiding attention-button" data-animation="bounceIn" data-delay="700">Buy Now</a></li>
                    </ul>
                </div>
                <div class="large-6 columns">
                    <div id='before-after-container' class="twentytwenty-container">
                        <img onload='$("#before-after-container").twentytwenty({default_offset_pct:.5});' src="/PF.Site/Apps/instapaint/assets/front-page/img/before.jpg" />
                        <img src="/PF.Site/Apps/instapaint/assets/front-page/img/after.jpg" />
                    </div>
                    <video id="instapaint-video" loop autoplay muted controls style="text-align: center; max-width: 100%" poster="/PF.Site/Apps/instapaint/assets/front-page/img/video-poster.png">
                        <source src="/PF.Site/Apps/instapaint/assets/video.mp4" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>

            </div>
        </div>
    </div>
</div>

    <a id="showHere"></a>

    <div data-is
         data-is-api="/PF.Site/Apps/instapaint/assets/instashow/api/index.php"
         data-is-source="@instapaint_official"
         data-is-width="auto"
         data-is-layout="slider"
         data-is-columns="5"
         data-is-rows="1"
         data-is-slider-autoplay="5"
         data-is-responsive='{ "700": { "columns": 3, "rows": 2, "gutter": 10 }}'
         data-is-lang="en">
    </div>

    <section id="about" class="section dark">
        <div class="container">
            <div class="tab-content alt">
                <div class="tab-pane active" id="first-tab-alt">
                    <div class="section-content row">
                        <div class="col-sm-6 animated hiding" data-animation="fadeInLeft">
                            <img src="/PF.Site/Apps/instapaint/assets/front-page/img/features/people.jpg" class="img-responsive" alt="process 3" />
                        </div>
                        <div class="col-sm-6 animated hiding" data-animation="fadeInRight">
                            <br/>
                            <article class="center">
                                <h3 style="margin-top: 0">InstaPaint <span class="highlight">Explained</span></h3>
                                <p style="line-height: 27px; margin-bottom: 15px;">InstaPaint makes getting a professional oil painting a breeze.  Simply upload your photo, and we take care of the rest.</p>
                                <p align="left" style="text-align: center; margin-bottom: 15px;">We partner with professional artists who freehand paint and add their own unique creative skills to your piece.</p>
                                <p align="left" style="text-align: center;">Our artists are vetted rigorously before they can join InstaPaint and paint your order. Our artists are particularly skilled at painting faces and fine details. </p>
                                <div align="left">
                                    <hr style="margin-top: 10px">
                                    <strong>With our service, you can:</strong><br><br>

                                    <p><i class="far fa-check-circle"></i> &nbsp;<strong>Choose your own style</strong> - such as photo-realism, abstract expression, and pop-art</p>
                                    <p><i class="far fa-check-circle"></i> &nbsp;<strong>Change background details</strong> of the photo – such as the landscape, borders, and colours</p>
                                    <p><i class="far fa-check-circle"></i> &nbsp;<strong>Add extras</strong> in the order notes</p>
                                    <p><i class="far fa-check-circle"></i> &nbsp;<strong>Message your painter</strong> to keep updated on your painting</p>
                                    <p><i class="far fa-check-circle"></i> &nbsp;<strong>Simply let the painters handle</strong> for you if you want no changes to your photo</p>
                                    <p><i class="far fa-check-circle"></i> &nbsp;<strong>Choose expedited service</strong> to jump the queue and get your painting shipped in a week.</p>
                                    <div style="margin: 30px 0; text-align: center">
                                        <a target="_blank" href="https://www.trustspot.io/store/InstaPaint"><img src="https://s3.amazonaws.com/trustspot-downloads/simple_badge3_large.png"></a>
                                    </div>

                                </div>
                            </article>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<section id="feedback" class="section light">
        <div class="container">
            <div class="section-header animated hiding" data-animation="fadeInDown">
                <h2 id="reviews">What <span class="highlight">Clients</span> Say</h2>
            </div>
            <div class="section-content">

                <!-- BEGIN SLIDER CONTENT -->
                <style>
                    .review-stars {
                        margin-bottom: 10px;
                        color: gold;
                        font-size: 28px;
                    }
                </style>
                <div class="col-sm-10 col-sm-offset-1">
                    <div class="flexslider testimonials-slider center animated hiding" data-animation="fadeInTop">
                        <ul class="slides">
                            <li>
                                <div class="testimonial resp-center clearfix">
                                    <div class="review-stars">
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                    </div>
                                    <blockquote>
                                        I really do love the quality of work that is produced by the painters, the first time I saw one I was mesmerized by how great it looked.
                                    </blockquote>
                                </div>
                            </li>
                            <li>
                                <div class="testimonial resp-center clearfix">
                                    <div class="review-stars">
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                    </div>
                                    <blockquote>
                                        We had our family vacation photo painted and absolutely love it! It really captured a great moment in a unique timeless way, and looks fantastic in our living room. Thank you!
                                    </blockquote>
                                </div>
                            </li>
                            <li>
                                <div class="testimonial resp-center clearfix">
                                    <div class="review-stars">
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                    </div>
                                    <blockquote>
                                        The painting was exquisite and beautifully painted. My mother loved it!
                                    </blockquote>
                                </div>
                            </li>
                            <li>
                                <div class="testimonial resp-center clearfix">
                                    <div class="review-stars">
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                    </div>
                                    <blockquote>
                                        Thanks for making the ordering process for such a complex painting so simple. I had many changes I wanted to make and you guys accommodated them all. My family loved the painting!
                                    </blockquote>
                                </div>
                            </li>
                            <li>
                                <div class="testimonial resp-center clearfix">
                                    <div class="review-stars">
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="far fa-star" aria-hidden="true"></i>
                                    </div>
                                    <blockquote>
                                        I think I couldn't have gotten better service anywhere else. I ordered a large painting, very satisfied. Wife was happy.
                                    </blockquote>
                                </div>
                            </li>
                            <li>
                                <div class="testimonial resp-center clearfix">
                                    <div class="review-stars">
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                    </div>
                                    <blockquote>
                                        I got an old photo of my grandmother painted as a gift for my dad...everyone cried when they saw the painting. It was breathtaking! Thanks so much...I'll order again
                                    </blockquote>
                                </div>
                            </li>
                            <li>
                                <div class="testimonial resp-center clearfix">
                                    <div class="review-stars">
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                        <i class="fa fa-star" aria-hidden="true"></i>
                                    </div>
                                    <blockquote>
                                        I ordered a painting done of a picture I saw online. My artist did an impressive job. Its now hanging on my bedroom wall! The colors are really lovely and brighten my home
                                    </blockquote>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- END SLIDER -->
            </div>
        </div>
    </section>    

    <section id="feedback-controls" class="section light">
        <div class="container">
            <div class="col-md-10 col-md-offset-1">
                <!-- BEGIN CONTROLS -->
                <div class="flex-manual">
                    <div class="col-xs-6 col-sm-3 wrap">
                        <div class="switch flex-active">
                            <img alt="client" src="/PF.Site/Apps/instapaint/assets/front-page/img/Jaylene.jpg" class="sm-pic img-circle pull-left" width="69" height="70"  style="height:69px; width:69px">
                            <p>
                                <span class="highlight">Alex D</span>
                                <span class="review-date" style="color: #68AC3C;"><i class="far fa-check-circle"></i> Verified Buyer</span>
                            </p>
                        </div>
                    </div>

                    <div class="col-xs-6 col-sm-3 wrap">
                        <div class="switch pull-left">
                            <img alt="client" src="/PF.Site/Apps/instapaint/assets/front-page/img/AlexS.jpg" class="sm-pic img-circle pull-left" width="69" height="70" style="height:69px; width:69px">
                            <p>
                                <span class="highlight">Andrew M</span>
                                <span class="review-date" style="color: #68AC3C;"><i class="far fa-check-circle"></i> Verified Buyer</span>
                            </p>
                        </div>
                    </div>

                    <div class="col-xs-6 col-sm-3 wrap">
                        <div class="switch">
                            <img alt="client" src="/PF.Site/Apps/instapaint/assets/front-page/img/dominique.jpg" class="sm-pic img-circle pull-left" width="68" height="69" style="height:69px; width:69px">
                            <p>
                                <span class="highlight">Kirsty</span>
                                <span class="review-date" style="color: #68AC3C;"><i class="far fa-check-circle"></i> Verified Buyer</span>
                            </p>
                        </div>
                    </div>

                    <div class="col-xs-6 col-sm-3 wrap">
                        <div class="switch">
                            <img alt="client" src="/PF.Site/Apps/instapaint/assets/front-page/img/avatar.png" class="sm-pic img-circle pull-left" width="68" height="69" style="height:69px; width:69px">
                            <p>
                                <span class="highlight">Violet Lang</span>
                                <span class="review-date" style="color: #68AC3C;"><i class="far fa-check-circle"></i> Verified Buyer</span>
                            </p>
                        </div>
                    </div>

                    <div class="col-xs-6 col-sm-3 wrap">
                        <div class="switch">
                            <img alt="client" src="/PF.Site/Apps/instapaint/assets/front-page/img/avatar.png" class="sm-pic img-circle pull-left" width="68" height="69" style="height:69px; width:69px">
                            <p>
                                <span class="highlight">Jake Poole</span>
                                <span class="review-date" style="color: #68AC3C;"><i class="far fa-check-circle"></i> Verified Buyer</span>
                            </p>
                        </div>
                    </div>

                    <div class="col-xs-6 col-sm-3 wrap">
                        <div class="switch">
                            <img alt="client" src="/PF.Site/Apps/instapaint/assets/front-page/img/avatar.png" class="sm-pic img-circle pull-left" width="68" height="69" style="height:69px; width:69px">
                            <p>
                                <span class="highlight">Samia Nicholls</span>
                                <span class="review-date" style="color: #68AC3C;"><i class="far fa-check-circle"></i> Verified Buyer</span>
                            </p>
                        </div>
                    </div>

                    <div class="col-xs-6 col-sm-3 wrap">
                        <div class="switch">
                            <img alt="client" src="/PF.Site/Apps/instapaint/assets/front-page/img/avatar.png" class="sm-pic img-circle pull-left" width="68" height="69" style="height:69px; width:69px">
                            <p>
                                <span class="highlight">Jill Phoebe Atkins</span>
                                <span class="review-date" style="color: #68AC3C;"><i class="far fa-check-circle"></i> Verified Buyer</span>
                            </p>
                        </div>
                    </div>
                </div>
                <!-- END CONTROLS -->
            </div>
        </div>
    </section>

    <section id="testimonials-slideshow-section">
        <div class="container">
            <h4 style="text-align: center;"><span class="highlight">Our Customers Love Our Work. </span>Buy With A Peace Of Mind.</h4>
            <div id="myCarousel" class="carousel slide" data-ride="carousel">

                <!-- Wrapper for slides -->
                <div class="carousel-inner" style="background-color: white;">

                    <div class="item active">
                        <img src="/PF.Site/Apps/instapaint/assets/front-page/img/testimonials/wedding-min.png" style="width:100%;">
                        <div class="customQuotes">
                            <blockquote>
                                <p style="text-align: left; padding: 5px; font-weight: normal">The painting was a perfect wedding gift and presenting it to
                                    my brother was an amazing bonding moment for my family.
                                    The painting preserved their engagement in such a magical way.</p>
                            </blockquote>
                            <p style="text-align: center;"><span class="highlight">Mike</span></p>
                        </div>
                    </div>

                    <div class="item">
                        <img src="/PF.Site/Apps/instapaint/assets/front-page/img/testimonials/lauren-min.png" style="width:100%;">
                        <div class="customQuotes">
                            <blockquote>
                                <p style="text-align: left; padding: 5px; font-weight: normal">The detail and likeness of the painting to the photo is
                                    incredible! I specifically asked my painter to do a photorealism style, but you can choose any style you want.
                                    I ordered expedited servive (a great feature) so that I could surprise my husband
                                    with the painting. He was so touched by the gift! Will order again for more paintings of
                                    my kids and parents.</p>
                            </blockquote>
                            <p style="text-align: center;"><span class="highlight">Lauren</span></p>
                        </div>
                    </div>

                    <div class="item">
                        <img src="/PF.Site/Apps/instapaint/assets/front-page/img/testimonials/john-min.png" style="width:100%;">
                        <div class="customQuotes">
                            <blockquote>
                                <p style="text-align: left; padding: 5px; font-weight: normal">The painting my artist did of my grandkids was very detailed
                                    and good. My artist accommodated my numerous revision requests.</p>
                            </blockquote>
                            <p style="text-align: center;"><span class="highlight">John</span></p>
                        </div>
                    </div>

                    <div class="item">
                        <img src="/PF.Site/Apps/instapaint/assets/front-page/img/testimonials/yasha-min.png" style="width:100%;">
                        <div class="customQuotes">
                            <blockquote>
                                <p style="text-align: center; padding: 5px; font-weight: normal">My painting is awesome! I can't believe what a great job my
                                    artist did!</p>
                            </blockquote>
                            <p style="text-align: center;"><span class="highlight">Yasha</span></p>
                        </div>
                    </div>
                </div>

                <!-- Left and right controls -->
                <a class="left carousel-control" href="#myCarousel" data-slide="prev">
                    <span class="glyphicon glyphicon-chevron-left"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="right carousel-control" href="#myCarousel" data-slide="next">
                    <span class="glyphicon glyphicon-chevron-right"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </div>
    </section>

    <section id="feedback" class="section light free-shipping-banner" style="padding: 0">
        <div class="container">
            <div class="section-header animated hiding" data-animation="fadeInDown">
                <p style="font-size: 20px">Free Worldwide Shipping Included In Our Prices. Each Painting Is Lovingly Gift Wrapped.</p>
            </div>
        </div>
    </section>

    <!-- <hr class="no-margin" style="margin-top: 15px;"/> -->

    <section id="process" class="section dark">
        <div class="container">
            <div class="section-content row">

                <div class="col-md-6">
                    <h3>Featured Artists</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="caption">
                                <p class="featured-artist-name" style="font-size: 16px; font-weight: bold; margin-top: 20px; margin-bottom: 5px">Gia, Head Artist of Partner Gallery</p>
                                    <img style="margin: auto;" src="./PF.Site/Apps/instapaint/assets/front-page/img/features/gia.jpg" class="img-responsive">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="caption">
                                <p class="featured-artist-name" style="font-size: 16px; font-weight: bold; margin-top: 20px; margin-bottom: 5px">David, Senior Artist in Partner Studio</p>
                                <img style="margin: auto;" src="./PF.Site/Apps/instapaint/assets/front-page/img/features/David.jpg" class="img-responsive">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="caption">
                                <p class="featured-artist-name" style="font-size: 16px; font-weight: bold; margin-top: 20px; margin-bottom: 5px">Tim, Senior Artist in Partner Gallery</p>
                                <img style="margin: auto;" src="./PF.Site/Apps/instapaint/assets/front-page/img/features/Tim.jpg" class="img-responsive">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="caption">
                                <p class="featured-artist-name" style="font-size: 16px; font-weight: bold; margin-top: 20px; margin-bottom: 5px">Laura, Senior Artist in Partner Gallery</p>
                                <img style="margin: auto;" src="./PF.Site/Apps/instapaint/assets/front-page/img/features/Laura.jpg" class="img-responsive">
                            </div>
                        </div>
                    </div>
                </div>

                <div id="painted-by-professional-artists" class="col-md-6 pull-right animated hiding" data-animation="fadeInRight">
                    <article>
                        <h3>Painted by Professional Artists With <span class="highlight">Unique Artistic Flair</span></h3>
                        <div class="sub-title"></div>

                        <div class="embed-responsive embed-responsive-16by9" style="margin-bottom: 15px;">
                            <iframe width="100%" height="auto" src="https://www.youtube.com/embed/HLwcLbRC9pE" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>

                        <p>Our artists are from our partner studios and art galleries around the world.
                            We also accept skilled freelance artists with a 10+ years track record of showing consistency, technical precision and strong creative direction in their portfolio.</p>

                        <p>We only accept the best of the best, no hobby artists and no art factory workers here. Each painting is painstakingly hand-painted and fine-tuned to ensure you get an art-piece worth treasuring and keeping in your personal collection forever.</p>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <section id="feedback" class="section light free-shipping-banner" style="padding: 0">
        <div class="container">
            <div class="section-header animated hiding" data-animation="fadeInDown">
                <h2 id="reviews">Browse Our <span class="highlight">Excellent</span> Portfolio</h2>
            </div>
        </div>
    </section>

    <section id="gallery-preview-section" class="section dark">
        <div class="container">
            <div class="section-content row">

                <div class="col-sm-12 animated hiding" data-animation="fadeInRight">
                    <div class="gallery-preview">
                        <div>
                            <div class="gallery-preview-block">
                                <img data-lazy="/PF.Site/Apps/instapaint/assets/gallery-images/pets-and-other-animals/sample76-600.jpg">
                                <h2>Pets &amp; Other Animals <i class="fas fa-chevron-circle-down"></i></h2>
                                <ul>
                                    <li>Dog paintings</li>
                                    <li>Cat paintings</li>
                                    <li>Lion paintings</li>
                                    <li>Bird paintings</li>
                                    <li>Elephant paintings</li>
                                    <li>Rabbit paintings</li>
                                    <li>Frog paintings</li>
                                    <li>Zebra paintings</li>
                                </ul>
                            </div>
                        </div>

                        <div>
                            <div class="gallery-preview-block">
                                <img data-lazy="/PF.Site/Apps/instapaint/assets/gallery-images/family-and-children/sample126-600.jpg">
                                <h2>Children &amp; Family <i class="fas fa-chevron-circle-down"></i></h2>
                                <ul>
                                    <li>Adult, children &amp; parents paintings</li>
                                    <li>Family portraits</li>
                                    <li>Family vacation paintings</li>
                                    <li>Baby &amp; parent(s) paintings</li>
                                    <li>Baby paintings</li>
                                    <li>Toddler paintings</li>
                                    <li>Siblings paintings</li>
                                </ul>
                            </div>
                        </div>

                        <div>
                            <div class="gallery-preview-block">
                                <img data-lazy="/PF.Site/Apps/instapaint/assets/gallery-images/couples/sample148-600.jpg">
                                <h2>Couples <i class="fas fa-chevron-circle-down"></i></h2>
                                <ul>
                                    <li>Couple portraits</li>
                                    <li>Couple vacation paintings</li>
                                    <li>Intimate moments paintings</li>
                                    <li>Wedding paintings</li>
                                    <li>Honeymoon paintings</li>
                                </ul>
                            </div>
                        </div>

                        <div>
                            <div class="gallery-preview-block">
                                <img data-lazy="/PF.Site/Apps/instapaint/assets/gallery-images/self-portraits/sample38-600.jpg">
                                <h2>Self Portraits <i class="fas fa-chevron-circle-down"></i></h2>
                                <ul>
                                    <li>Women paintings</li>
                                    <li>Men paintings</li>
                                    <li>Intimate moments paintings</li>
                                    <li>Wedding paintings</li>
                                    <li>Honeymoon paintings</li>
                                </ul>
                            </div>
                        </div>

                        <div>
                            <div class="gallery-preview-block">
                                <img data-lazy="/PF.Site/Apps/instapaint/assets/gallery-images/special-events/happy couple-600.jpg">
                                <h2>Special Events <i class="fas fa-chevron-circle-down"></i></h2>
                                <ul>
                                    <li>Graduation paintings</li>
                                    <li>Wedding &amp; engagement paintings</li>
                                    <li>Party paintings</li>
                                    <li>Performing arts paintings</li>
                                    <li>Birthday paintings</li>
                                    <li>Military parade paintings</li>
                                </ul>
                            </div>
                        </div>

                        <div>
                            <div class="gallery-preview-block">
                                <img data-lazy="/PF.Site/Apps/instapaint/assets/gallery-images/landscape-and-fantasy/balls_-600.jpg">
                                <h2>Landscape &amp; Fantasy <i class="fas fa-chevron-circle-down"></i></h2>
                                <ul>
                                    <li>Buildings &amp; street paintings</li>
                                    <li>Nature scenery paintings</li>
                                    <li>Bend reality abstract paintings</li>
                                    <li>Fantasy world paintings</li>
                                </ul>
                            </div>
                        </div>

                        <div>
                            <div class="gallery-preview-block">
                                <img data-lazy="/PF.Site/Apps/instapaint/assets/gallery-images/vintage-restoration/vintage after-600.jpg">
                                <h2>Vintage Restoration <i class="fas fa-chevron-circle-down"></i></h2>
                                <ul>
                                    <li>Restore old photos paintings</li>
                                </ul>
                            </div>
                        </div>

                        <div>
                            <div class="gallery-preview-block">
                                <img data-lazy="/PF.Site/Apps/instapaint/assets/gallery-images/merge/merged-cropped-600.jpg">
                                <h2>Merge Different Pictures <i class="fas fa-chevron-circle-down"></i></h2>
                                <ul>
                                    <li>Combine photos paintings</li>
                                </ul>
                            </div>
                        </div>

                        <div>
                            <div class="gallery-preview-block">
                                <img data-lazy="/PF.Site/Apps/instapaint/assets/gallery-images/replicas-and-classics/last supper-600.jpg">
                                <h2>Replicas &amp; Classics <i class="fas fa-chevron-circle-down"></i></h2>
                                <ul>
                                    <li>Copy another painting</li>
                                    <li>Renaissance &amp; historical paintings</li>
                                </ul>
                            </div>
                        </div>

                        <div>
                            <div class="gallery-preview-block">
                                <img data-lazy="/PF.Site/Apps/instapaint/assets/gallery-images/graphic-art/popart-600.jpg">
                                <h2>Words &amp; Pop Art <i class="fas fa-chevron-circle-down"></i></h2>
                                <ul>
                                    <li>Graphic art paintings</li>
                                </ul>
                            </div>
                        </div>

                        <div>
                            <div class="gallery-preview-block">
                                <img data-lazy="/PF.Site/Apps/instapaint/assets/gallery/sample88-600.jpg">
                                <h2>Cars &amp; Motorcycles <i class="fas fa-chevron-circle-down"></i></h2>
                                <ul>
                                    <li>Cars &amp; motorbikes paintings</li>
                                    <li>Aircrafts paintings</li>
                                </ul>
                            </div>
                        </div>

                        <div>
                            <div class="gallery-preview-block">
                                <img data-lazy="/PF.Site/Apps/instapaint/assets/gallery-images/life-and-culture/oranges in bed-600.jpg">
                                <h2>Life &amp; Culture <i class="fas fa-chevron-circle-down"></i></h2>
                                <ul>
                                    <li>Funny cartoonish paintings</li>
                                    <li>Harry Potter paintings</li>
                                </ul>
                            </div>
                        </div>

                        <div>
                            <div class="gallery-preview-block">
                                <img data-lazy="/PF.Site/Apps/instapaint/assets/gallery-images/fruits-and-flowers/flowers-600.jpg">
                                <h2>Flowers &amp; Fruits <i class="fas fa-chevron-circle-down"></i></h2>
                                <ul>
                                </ul>
                            </div>
                        </div>

                        <div>
                            <div class="gallery-preview-block">
                                <img data-lazy="/PF.Site/Apps/instapaint/assets/gallery-images/remove-objects/removeobject1-600.jpg">
                                <h2>Remove Objects <i class="fas fa-chevron-circle-down"></i></h2>
                                <ul>
                                    <li>Remove things from pictures paintings</li>
                                </ul>
                            </div>
                        </div>

                        <div>
                            <div class="gallery-preview-block">
                                <img data-lazy="/PF.Site/Apps/instapaint/assets/gallery-images/change-background/changbackground-600.jpg">
                                <h2>Change Background <i class="fas fa-chevron-circle-down"></i></h2>
                                <ul>
                                    <li>Studio background paintings</li>
                                    <li>Outdoors background paintings</li>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    <style>
        .slick-initialized .slick-slide {
            outline: none;
        }
        .gallery-preview-block {
            padding: 0 10px;
        }
        #gallery-preview-section {
            padding: 50px 0 0 0;
        }
        .gallery-preview-block h2 {
            text-transform: uppercase;
            font-weight: bold;
            font-size: 22px;
        }
        .gallery-preview-block ul {
            list-style-type: none;
            padding: 0;
            font-size: 18px;
        }
        .gallery-preview-block ul li{
            line-height: 1.5;
        }
    </style>

    <section id="process" class="section dark">
        <div class="container">
            <div class="section-content row">

                <hr class="clearfix" />

                <div class="col-sm-6 pull-right animated hiding" data-animation="fadeInRight">
                    <img src="/PF.Site/Apps/instapaint/assets/front-page/img/features/content_image1.jpg" class="img-responsive" alt="process 2" />
                </div>
                <div class="col-sm-6 animated hiding" data-animation="fadeInLeft">
                    <br/><br/>
                    <article>
                        <h3>This is <span class="highlight">NOT</span> a Machine Painting!</h3>
                        <div class="sub-title"></div>
                        <p>InstaPaint's team of certified professional artists create absolutely stunning works of art that will 'WOW' your friends and colleagues.  Our paintings are done by hand and are not a machine printed image that simply 'looks' hand painted.  These finished works of art make the perfect gift for a girlfriend, wife, fiancé, business associate, client, lover, friend, brother, sister, cousin, grandparent, son, daughter, aunt, uncle, client, vendor, or simply for yourself!</p>
                    </article>
                </div>

                <hr class="clearfix" />

                <div class="col-sm-6 animated hiding" data-animation="fadeInLeft">
                    <img src="/PF.Site/Apps/instapaint/assets/front-page/img/features/helmet.jpg" class="img-responsive" alt="process 3" />
                </div>
                <div class="col-sm-6 animated hiding" data-animation="fadeInRight">
                    <br/><br/>
                    <article>
                        <h3><span class="highlight">Ordering is easy. </span>Upload your photo, checkout, and you're done!</h3>
                        <div class="sub-title"></div>
                        <p>Our artists are skilled at doing:</p>
                        <ul style="list-style-type: none; text-align: left; padding: 0 5px 0 0; display: inline-block; line-height: 1.5em;">
                            <li>Dog paintings</li>
                            <li>Wedding paintings</li>
                            <li>City paintings</li>
                            <li>History paintings</li>
                            <li>Christmas paintings</li>
                            <li>Flower paintings</li>
                            <li>Wildlife paintings</li>
                            <li>Boat paintings</li>
                            <li>Water sport paintings</li>
                            <li>Bird paintings</li>
                        </ul>
                        <ul style="list-style-type: none; text-align: left; padding: 0 5px 0 0; display: inline-block; line-height: 1.5em;">
                            <li>Campfire paintings</li>
                            <li>Graduation paintings</li>
                            <li>Cat paintings</li>
                            <li>Sports paintings</li>
                            <li>Car paintings</li>
                            <li>Military paintings</li>
                            <li>Night paintings</li>
                            <li>Winter paintings</li>
                            <li>Food paintings</li>
                            <li>Self portraits</li>
                        </ul>
                        <ul style="list-style-type: none; text-align: left; padding: 0; display: inline-block; line-height: 1.5em;">
                            <li>Horse paintings</li>
                            <li>Music paintings</li>
                            <li>Aircraft paintings</li>
                            <li>Toy paintings</li>
                            <li>Dinosaur paintings</li>
                            <li>Couple paintings</li>
                            <li>Family paintings</li>
                            <li>Celebration paintings</li>
                            <li>Baby & kids paintings</li>
                            <li>Landscape paintings</li>
                        </ul>
                    </article>
                </div>

                <hr class="clearfix" />

                <div class="col-sm-6 pull-right animated hiding" data-animation="fadeInRight">
                    <img src="/PF.Site/Apps/instapaint/assets/front-page/img/features/profitable.jpg" class="img-responsive" alt="process 2" />
                </div>
                <div class="col-sm-6 animated hiding" data-animation="fadeInLeft">
                    <br/><br/>
                    <article>
                        <h3><span class="highlight">How Long Does it Take?</span></h3>
                        <div class="sub-title"></div>
                        <p>From the time you enter in your order, it will up to 20 hours for your painting to commence. Depending on the complexity of your image, it takes our painters anywhere from 3 - 7 days to complete a painting but this can vary depending on the complexity of the painting.</p>
                        <p>For an extra fee you can pay for expedited service so that your painting is prioritized right away, shortening your waiting time as much as possible. Your painting will be done within 1- 3 days.</p>
                        <p>Once the painting is finalized and dried, it will be shipped. Shipping speed varies from 10 - 20 days depending on where you are located around the globe. Rush shipping is also available and your painting will arrive in 5 – 7 days.</p>
                        <p>We have an unlimited revisions and adjustments policy so that you can change your painting until you are satisfied. However please note that the more you revise the painting, the longer it will take to be completed and sent to you.</p>
                    </article>
                </div>



                <hr class="clearfix" />

                <div class="col-sm-6 animated hiding" data-animation="fadeInLeft">
                    <img src="/PF.Site/Apps/instapaint/assets/front-page/img/features/searchable.jpg" class="img-responsive" alt="process 3" />
                </div>
                <div class="col-sm-6 animated hiding" data-animation="fadeInRight">
                    <br/><br/>
                    <article>
                        <h3>Can I change the<span class="highlight"> background </span>of my image?</h3>
                        <div class="sub-title"></div>
                        <p>Yes! Background replacement is available. Simply choose from the options when you order or write some instructions in the comments before you place your order.</p>
                    </article>
                </div>


                <hr class="clearfix" />

                <div class="col-sm-6 pull-right animated hiding" data-animation="fadeInRight">
                    <img src="/PF.Site/Apps/instapaint/assets/front-page/img/features/leadfinder.jpg" class="img-responsive" alt="process 2" />
                </div>
                <div class="col-sm-6 animated hiding" data-animation="fadeInLeft">
                    <br/><br/>
                    <article>
                        <h3>Are different styles available?</h3>
                        <div class="sub-title"></div>
                        <p>Yes! Simply upload a reference photo, from which you would like your painting to look like. Or just write a comment to our painters, in terms of how you would like your painting to look; Eg. black and white, cubism, etc. </p>
                    </article>
                </div>



                <hr class="clearfix" />

                <div class="col-sm-6 animated hiding" data-animation="fadeInLeft">
                    <img src="/PF.Site/Apps/instapaint/assets/front-page/img/features/birdseye.jpg" class="img-responsive" alt="process 3" />
                </div>
                <div class="col-sm-6 animated hiding" data-animation="fadeInRight">
                    <br/><br/>
                    <article>
                        <h3>How do I order?</h3>
                        <div class="sub-title"></div>
                        <p>Simply scroll down to begin the process. Upload your image either from your device and/or social media account, make a few selections, check out, and your painting will be on its way!</p>
                    </article>
                </div>

            </div>
        </div>
    </section>

    <section id="features" class="section inverted">
        <div class="container">
            <div class="section-content">
                <div id="featuredTab">
                    <ul class="list-unstyled animated hiding" data-animation="fadeInRight">
                        <li class="active">
                            <a href="#home" data-toggle="tab">
                                <div class="tab-info">
                                    <div class="tab-title">Hand Paintings Only</div>
                                    <div class="tab-desc">&nbsp;</div>
                                </div>

                            </a>
                        </li>
                        <li>
                            <a href="#profile" data-toggle="tab">
                                <div class="tab-info">
                                    <div class="tab-title">Different finish styles available</div>
                                    <div class="tab-desc"></div>
                                </div>

                            </a>
                        </li>
                        <li>
                            <a href="#messages" data-toggle="tab">
                                <div class="tab-info">
                                    <div class="tab-title">Museum quality painting</div>
                                    <div class="tab-desc">&nbsp;</div>
                                </div>

                            </a>
                        </li>
                    </ul>

                    <div class="tab-content animated hiding" data-animation="fadeInLeft">
                        <div class="tab-pane in active" id="home"><img src="/PF.Site/Apps/instapaint/assets/front-page/img/features/rich_features_1.png" class="img-responsive animated hiding" data-animation="fadeInLeft" alt="macbook" /></div>
                        <div class="tab-pane" id="profile"><img src="/PF.Site/Apps/instapaint/assets/front-page/img/features/rich_features_2.png" class="img-responsive animated hiding" data-animation="fadeInLeft" alt="macbook" /></div>
                        <div class="tab-pane" id="messages"><img src="/PF.Site/Apps/instapaint/assets/front-page/img/features/rich_features_3.png" class="img-responsive animated hiding" data-animation="fadeInLeft" alt="macbook" /></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="features-list" class="section dark">
        <div class="container animated hiding" data-animation="fadeInDown">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <article class="center">
                    <i class="icon icon-office-44 icon-active"></i>
                    <span class="h7">Quick Upload</span>
                    <p class="thin">Our artists will commence work on your painting as soon as your order goes through.</p>
                </article>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <article class="center">
                    <i class="icon icon-shopping-18 icon-active"></i>
                    <span class="h7">Easy Checkout Process</span>
                    <p class="thin">The entire process only takes 5 minutes. </p>
                </article>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <article class="center">
                    <i class="icon icon-seo-icons-27 icon-active"></i>
                    <span class="h7">Upload Any Photo</span>
                    <p class="thin">Or, upload a photo from your phone/computer.</p>
                </article>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <article class="center">
                    <i class="icon icon-office-24 icon-active"></i>
                    <span class="h7">Quick Turnaround</span>
                    <p class="thin">Depending on your shipping speed, you will receive the final product 2-6 weeks after ordering. </p>
                </article>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <article class="center">
                    <i class="icon icon-graphic-design-13 icon-active"></i>
                    <span class="h7">Various Art Styles</span>
                    <p class="thin">Don't like the original? Choose something new. </p>
                </article>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <article class="center">
                    <i class="icon icon-arrows-37 icon-active"></i>
                    <span class="h7">Impress Your Friends!</span>
                    <p class="thin">There literally is no better way to show off than to show them your work of art. </p>
                </article>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <article class="center">
                    <i class="icon icon-badges-votes-14 icon-active"></i>
                    <span class="h7">Certified Professional Artists</span>
                    <p class="thin">All of our artists have been professionally trained and all paintings are approved to ship by our staff once complete. </p>
                </article>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <article class="center">
                    <i class="icon icon-badges-votes-16 icon-active"></i>
                    <span class="h7">5-Star Ratings</span>
                    <p class="thin">Our paintings receive top awards, year after year. </p>
                </article>
            </div>
        </div>
    </section>

    <section id="product" class="section dark">
        <div class="container">
            <div class="section-header animated hiding" data-animation="fadeInDown">
                <h2>OUR PAINTING <span class="highlight">PACKAGES</span></h2>
                <div class="sub-heading">
                    Prices vary based on size, number of faces in the photo, and finish.
                </div>
            </div>
            <div class="section-content row">
                <div class="col-sm-4">
                    <div class="package-column animated hiding" data-animation="flipInY" data-delay="500">
                        <div class="package-title">Small Oil Painting Package</div>
                        <div class="package-price">
                            <div class="period">Starting at</div>
                            <div class="price"><span class="currency">$</span>79</div>
                        </div>
                        <div class="package-detail">
                            <ul class="list-unstyled">
                                <li><strong>10" x 10" Square Standard size</strong> (or choose another)</li>
                                <li><strong>Standard Shipping</strong><br> - FREE</li>
                                <li><strong>All Art Styles</strong> </li>
                            </ul>
                            <a id="home-buy-button-2" href="/first-order/" class="cta-buy btn btn-secondary btn-block attention-button-secondary">Get started!</a>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="package-column animated hiding" data-animation="flipInY" data-delay="500">
                        <div class="package-title">Medium Oil Painting Package</div>
                        <div class="package-price">
                            <div class="period">Starting at</div>
                            <div class="price"><span class="currency">$</span>179</div>
                        </div>
                        <div class="package-detail">
                            <ul class="list-unstyled">
                                <li><strong>15" x 15" Square Standard size</strong> (or choose another)</li>
                                <li><strong>Standard Shipping</strong><br> - FREE</li>
                                <li><strong>All Art Styles</strong> </li>
                            </ul>
                            <a id="home-buy-button-3" href="/first-order/" class="cta-buy btn btn-secondary btn-block attention-button-secondary">Get started!</a>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="package-column animated hiding" data-animation="flipInY" data-delay="500">
                        <div class="package-title">Large Oil Painting Package</div>
                        <div class="package-price">
                            <div class="period">Starting at</div>
                            <div class="price"><span class="currency">$</span>244</div>
                        </div>
                        <div class="package-detail">
                            <ul class="list-unstyled">
                                <li><strong>22" x 22" Square Standard size</strong> (or choose another)</li>
                                <li><strong>Standard Shipping</strong><br> - FREE</li>
                                <li><strong>All Art Styles</strong> </li>
                            </ul>
                            <a id="home-buy-button-4" href="/first-order/" class="cta-buy btn btn-secondary btn-block attention-button-secondary">Get started!</a>
                        </div>
                    </div>
                </div>
            </div>

            <div id="payments-credit-home" class="col-md-12" style="margin-top: 50px; margin-bottom: 50px">
                <a href="/first-order/">
                    <img src="/PF.Site/Apps/instapaint/assets/front-page/img/paypal-credit.jpeg" />
                </a>
            </div>

            <p style="color: #6f6f6f; text-align: center; margin-top: 30px; margin-bottom: 0; max-width: 62.5em; margin-right: auto; margin-left: auto; font-size: 18px">The below prices include free standard shipping, gift wrap option and unlimited revisions</p>
            <div style="overflow-x: auto">
                <table id="price-calculator-table" class="table table-hover">
                    <thead>
                    <tr>
                        <th><i class="fas fa-ruler-combined"></i><br>dimensions</th>
                        <th style="min-width: 100px;"><i class="fas fa-user"></i><br>1 person</th>
                        <th style="min-width: 110px;"><i class="fas fa-user-friends"></i><br>2 persons</th>
                        <th style="min-width: 100px;"><i class="fas fa-dog"></i><br>1 pet</th>
                        <th style="min-width: 160px;"><i class="fas fa-user-plus"></i> <i class="fas fa-dog fa-flip-horizontal"></i><br>1 person + 1 pet</th>
                        <th><i class="fas fa-image"></i><br>landscape</th>
                        <th style="min-width: 250px;">
                            <i class="fas fa-edit"></i><br>
                            <select id="calculator-custom-persons" class="calculator-custom-field">
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                                <option value="13">13</option>
                                <option value="14">14</option>
                                <option value="15">15</option>
                                <option value="16">16</option>
                                <option value="17">17</option>
                                <option value="18">18</option>
                                <option value="19">19</option>
                                <option value="20">20</option>
                            </select>
                            <label for="calculator-custom-persons" style="font-weight: 300">persons</label> +
                            <select id="calculator-custom-pets" class="calculator-custom-field">
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                                <option value="13">13</option>
                                <option value="14">14</option>
                                <option value="15">15</option>
                                <option value="16">16</option>
                                <option value="17">17</option>
                                <option value="18">18</option>
                                <option value="19">19</option>
                                <option value="20">20</option>
                            </select>
                            <label for="calculator-custom-pets" style="font-weight: 300">pets</label>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td class="price-calculator-size-td"><a href="/PF.Site/Apps/instapaint/assets/front-page/img/painting-sizes/10x10.png" data-lightbox="image-1" data-title="10 x 10 in"><i class="far fa-eye"></i> 10 x 10 in</a></td>
                        <td class="1-person-price" data-1-person-price="79">$79</td>
                        <td>$104</td>
                        <td class="1-pet-price" data-1-pet-price="79">$79</td>
                        <td class="1-pet-price" data-1-pet-price="104">$104</td>
                        <td class="1-person-price" data-1-person-price="79">$79</td>
                        <td class="custom-price"></td>
                    </tr>
                    <tr>
                        <td class="price-calculator-size-td"><a href="/PF.Site/Apps/instapaint/assets/front-page/img/painting-sizes/15x15.png" data-lightbox="image-2" data-title="15 x 15 in"><i class="far fa-eye"></i> 15 x 15 in</a></td>
                        <td class="1-person-price" data-1-person-price="179">$179</td>
                        <td>$204</td>
                        <td class="1-pet-price" data-1-pet-price="179">$179</td>
                        <td class="1-pet-price" data-1-pet-price="174">$204</td>
                        <td class="1-person-price" data-1-person-price="179">$179</td>
                        <td class="custom-price"></td>
                    </tr>
                    <tr>
                        <td class="price-calculator-size-td"><a href="/PF.Site/Apps/instapaint/assets/front-page/img/painting-sizes/22x22.png" data-lightbox="image-3" data-title="22 x 22 in"><i class="far fa-eye"></i> 22 x 22 in</a></td>
                        <td class="1-person-price" data-1-person-price="244">$244</td>
                        <td>$269</td>
                        <td class="1-pet-price" data-1-pet-price="244">$244</td>
                        <td class="1-pet-price" data-1-pet-price="269">$269</td>
                        <td class="1-person-price" data-1-person-price="244">$244</td>
                        <td class="custom-price"></td>
                    </tr>
                    <tr>
                        <td class="price-calculator-size-td"><a href="/PF.Site/Apps/instapaint/assets/front-page/img/painting-sizes/12x16.png" data-lightbox="image-4" data-title="12 x 16 in"><i class="far fa-eye"></i> 12 x 16 in</a></td>
                        <td class="1-person-price" data-1-person-price="159">$159</td>
                        <td>$204</td>
                        <td class="1-pet-price" data-1-pet-price="159">$159</td>
                        <td class="1-pet-price" data-1-pet-price="204">$204</td>
                        <td class="1-person-price" data-1-person-price="159">$159</td>
                        <td class="custom-price"></td>
                    </tr>
                    <tr>
                        <td class="price-calculator-size-td"><a href="/PF.Site/Apps/instapaint/assets/front-page/img/painting-sizes/16x20.png" data-lightbox="image-5" data-title="16 x 20 in"><i class="far fa-eye"></i> 16 x 20 in</a></td>
                        <td class="1-person-price" data-1-person-price="204">$204</td>
                        <td>$229</td>
                        <td class="1-pet-price" data-1-pet-price="204">$204</td>
                        <td class="1-pet-price" data-1-pet-price="229">$229</td>
                        <td class="1-person-price" data-1-person-price="204">$204</td>
                        <td class="custom-price"></td>
                    </tr>
                    <tr>
                        <td class="price-calculator-size-td"><a href="/PF.Site/Apps/instapaint/assets/front-page/img/painting-sizes/18x24.png" data-lightbox="image-6" data-title="18 x 24 in"><i class="far fa-eye"></i> 18 x 24 in</a></td>
                        <td class="1-person-price" data-1-person-price="224">$224</td>
                        <td>$249</td>
                        <td class="1-pet-price" data-1-pet-price="224">$224</td>
                        <td class="1-pet-price" data-1-pet-price="249">$249</td>
                        <td class="1-person-price" data-1-person-price="224">$224</td>
                        <td class="custom-price"></td>
                    </tr>
                    <tr>
                        <td class="price-calculator-size-td"><a href="/PF.Site/Apps/instapaint/assets/front-page/img/painting-sizes/20x24.png" data-lightbox="image-7" data-title="20 x 24 in"><i class="far fa-eye"></i> 20 x 24 in</a></td>
                        <td class="1-person-price" data-1-person-price="244">$244</td>
                        <td>$278</td>
                        <td class="1-pet-price" data-1-pet-price="244">$244</td>
                        <td class="1-pet-price" data-1-pet-price="278">$278</td>
                        <td class="1-person-price" data-1-person-price="244">$244</td>
                        <td class="custom-price"></td>
                    </tr>
                    <tr>
                        <td class="price-calculator-size-td"><a href="/PF.Site/Apps/instapaint/assets/front-page/img/painting-sizes/24x36.png" data-lightbox="image-8" data-title="24 x 36 in"><i class="far fa-eye"></i> 24 x 36 in</a></td>
                        <td class="1-person-price" data-1-person-price="363">$363</td>
                        <td>$388</td>
                        <td class="1-pet-price" data-1-pet-price="363">$363</td>
                        <td class="1-pet-price" data-1-pet-price="388">$388</td>
                        <td class="1-person-price" data-1-person-price="363">$363</td>
                        <td class="custom-price"></td>
                    </tr>
                    <tr>
                        <td class="price-calculator-size-td"><a href="/PF.Site/Apps/instapaint/assets/front-page/img/painting-sizes/30x40.png" data-lightbox="image-9" data-title="30 x 40 in"><i class="far fa-eye"></i> 30 x 40 in</a></td>
                        <td class="1-person-price" data-1-person-price="439">$439</td>
                        <td>$464</td>
                        <td class="1-pet-price" data-1-pet-price="439">$439</td>
                        <td class="1-pet-price" data-1-pet-price="464">$464</td>
                        <td class="1-person-price" data-1-person-price="439">$439</td>
                        <td class="custom-price"></td>
                    </tr>
                    <tr>
                        <td class="price-calculator-size-td"><a href="/PF.Site/Apps/instapaint/assets/front-page/img/painting-sizes/36x48.png" data-lightbox="image-10" data-title="36 x 48 in"><i class="far fa-eye"></i> 36 x 48 in</a></td>
                        <td class="1-person-price" data-1-person-price="577">$577</td>
                        <td>$602</td>
                        <td class="1-pet-price" data-1-pet-price="577">$577</td>
                        <td class="1-pet-price" data-1-pet-price="602">$602</td>
                        <td class="1-person-price" data-1-person-price="577">$577</td>
                        <td class="custom-price"></td>
                    </tr>
                    <tr>
                        <td class="price-calculator-size-td"><a href="/PF.Site/Apps/instapaint/assets/front-page/img/painting-sizes/48x72.png" data-lightbox="image-11" data-title="48 x 72 in"><i class="far fa-eye"></i> 48 x 72 in</a></td>
                        <td class="1-person-price" data-1-person-price="730">$730</td>
                        <td>$755</td>
                        <td class="1-pet-price" data-1-pet-price="730">$730</td>
                        <td class="1-pet-price" data-1-pet-price="755">$755</td>
                        <td class="1-person-price" data-1-person-price="730">$730</td>
                        <td class="custom-price"></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <!--<div class="section-header animated hiding" data-animation="fadeInDown">
                <h2>WE ARE ALSO SELLING <span class="highlight">GIFTCARDS</span></h2>
                <div class="sub-heading">
                    Using these giftcards you can able to have a large amount of discounts when ordering our painting packages!
                </div>
                <br>
                <a href="http://instapaint.com/giftcards" class="btn btn-secondary">View details</a>
            </div>-->
        </div>
    </section>       

    <section id="newsletter" class="long-block light">
        <!-- <div class="container animated hiding" data-animation="fadeInDown">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <article class="center" style="max-width: 450px; margin-left: auto; margin-right: auto; margin-bottom: 0; margin-top: 15px;">
                <div id='before-after-container2' class="twentytwenty-container">
                    <img onload='$("#before-after-container2").twentytwenty({default_offset_pct:.5});' src="/PF.Site/Apps/instapaint/assets/front-page/img/before-1.jpg" />
                    <img src="/PF.Site/Apps/instapaint/assets/front-page/img/after-1.jpg" />
                </div>
            </article>
        </div>
        </div> -->

        <div class="container animated hiding" data-animation="fadeInDown" style="margin-top: 12px">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <article class="center" style="max-width: 450px; margin-left: auto; margin-right: auto; margin-bottom: 0; margin-top: 15px;">
                    <div id='before-after-container3' class="twentytwenty-container">
                        <img onload='$("#before-after-container3").twentytwenty({default_offset_pct:.5});' src="/PF.Site/Apps/instapaint/assets/front-page/img/before-2-1.jpg" />
                        <img src="/PF.Site/Apps/instapaint/assets/front-page/img/after-2-1.jpg" />
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section id="as-seen-on" class="section dark" style="padding: 60px 0 60px 0;">
        <div class="container">
            <div id="as-seen-on" class="section-header animated hiding" data-animation="fadeInDown">
                <h2>AS <span class="highlight">SEEN ON</span></h2>
            </div>
            <div class="section-content row">
                <div class="col-sm-4">
                    <img src="/PF.Site/Apps/instapaint/assets/front-page/img/elfster.jpg" />
                    <img src="/PF.Site/Apps/instapaint/assets/front-page/img/steemit.jpg" />
                </div>

                <div class="col-sm-4">
                    <img src="/PF.Site/Apps/instapaint/assets/front-page/img/grantkemp.jpg" />
                    <p style="font-size: 16px; padding-top: 5px;"><strong>Grant Kemp</strong>, Celebrity on "Bachelor in Paradise"</p>
                </div>

                <div class="col-sm-4">
                    <img src="/PF.Site/Apps/instapaint/assets/front-page/img/giftinsider.jpg" />
                </div>
            </div>
        </div>
    </section>

    <section class="section" style="padding: 30px 0; background-color: #FAFAFA">
        <iframe src="https://www.trustspot.io/merchant/TrustModule/badge/InstaPaint" width="160" height="100" seamless allowTransparency="true" scrolling="no" frameborder="0" style='border:none; overflow: hidden;'><p>View Our Reviews On TrustSpot</p></iframe>
    </section>

    <section class="section dark" style="padding: 20px 0 50px 0">
        <div class="container">
            <div class="section-header animated hiding" data-animation="fadeInDown" style="margin-bottom: 40px;">
                <h2>ABOUT <span class="highlight">US</span></h2>
            </div>
            <div class="section-content row">
                <div class="col-sm-12">
                    <div id="about-us-container">
                        <p>In 2012, InstaPaint was founded with the idea of bringing museum quality, customised oil paintings to the end user with a hassle-free experience.</p>
                        <p>Fine art should not only belong in exhibitions but in everyone's home.</p>
                        <p>Let us know how you like your art piece! Tag us or follow us on <a href="https://www.instagram.com/instapaint_official/" target="_blank">Instagram</a> or on <a target="_blank" href="https://www.facebook.com/InstaPaintOfficial/">Facebook.</a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Price Calculator Object
        var priceCalculator = {};

        $(function() {

            // Method to update values from fields
            priceCalculator.updateValues = function() {
                priceCalculator.numPersons = parseInt( $('#calculator-custom-persons').val() );
                priceCalculator.numPets = parseInt( $('#calculator-custom-pets').val() );
            };

            // Compute prices and display them to the user
            priceCalculator.calculate = function () {
                $('#price-calculator-table .custom-price').each(function (index) {
                    if (priceCalculator.numPersons === 0 && priceCalculator.numPets === 0) {
                        $(this).html('<i class="far fa-question-circle"></i>');
                    } else {
                        var basePrice = parseInt( $(this).siblings('.1-person-price').attr('data-1-person-price') );
                        var totalPrice;

                        // Compute price based on predefined rules
                        if (priceCalculator.numPersons > 0 && priceCalculator.numPets === 0) {
                            totalPrice = basePrice + (priceCalculator.numPersons - 1) * 25;
                        } else if (priceCalculator.numPersons === 0 && priceCalculator.numPets > 0) {
                            totalPrice = basePrice + (priceCalculator.numPets - 1) * 25;
                        } else {
                            totalPrice = basePrice + (priceCalculator.numPersons - 1) * 25 + priceCalculator.numPets * 25;
                        }

                        $(this).html('$' + totalPrice);
                    }
                });
            };

            // Update values from fields
            priceCalculator.updateValues();

            // Calculate prices
            priceCalculator.calculate();

            // Update values and calculate when fields change
            $('#calculator-custom-persons, #calculator-custom-pets').on('change', function () {
                priceCalculator.updateValues();
                priceCalculator.calculate();
            });

        });
    </script>

    <script type="text/javascript" src="/PF.Site/Apps/instapaint/assets/front-page/library/slick/slick/slick.min.js"></script>

    <script>
        $(document).ready(function(){
            $('.gallery-preview').slick({
                lazyLoad: 'ondemand',
                infinite: true,
                slidesToShow: 3,
                slidesToScroll: 3,
                responsive: [
                    {
                        breakpoint: 600,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            infinite: true,
                            dots: false
                        }
                    },
                    {
                        breakpoint: 800,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 2,
                            infinite: true,
                            dots: false
                        }
                    }
                ]
            });
        });
    </script>
<?php

// Include footer HTML:
require_once PHPFOX_DIR_SITE . 'Apps/instapaint/assets/front-page/templates/footer.php';

exit();

<?php

namespace Apps\Instapaint\Controller;

// Index controller must be child of \Phpfox_Component class.

use Phpfox;

class GalleryController extends \Phpfox_Component
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

        <script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
        <script src="https://unpkg.com/imagesloaded@4/imagesloaded.pkgd.min.js"></script>
        <script>
            function openNav() {
                document.getElementById("mySidenav").style.width = "375px";
            }
            $(document).ready(function(){
                $(".closebtn").click(function(){
                    document.getElementById("mySidenav").style.width = "0%";
                    for(let i=1; i<16; i++) {
                        document.getElementById("myDropdown"+i).classList.remove("show");
                    }
                });
            });

            /* When the user clicks on the button, 
            toggle between hiding and showing the dropdown content */
            function myFunction(index) {
                document.getElementById("myDropdown"+index).classList.toggle("show");
            }
        </script>
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
            .browse-category {
                position: fixed;
                top: 300px;
                left: 10px;
                padding: 24px;
                font-size: 20px;
                text-align: center;
                z-Index: 100;
                border-color: #a88734 #9c7e31 #846a29;
                box-shadow: 0 1px 0 rgba(255,255,255,.6) inset;
                background: linear-gradient(to bottom,#f7dfa5,#f0c14b);
                color: black !important;
                border-radius: 6px;
                font-weight: normal;
                text-transform: capitalize;
                transition: none;
            }

            .browse-category:hover {
                background: linear-gradient(to bottom,#f5d78e,#eeb933)
            }

            .sidenav {
                z-Index: 100000;
                height: 100%;
                width: 0;
                position: fixed;
                top: 0;
                left: 0;
                background-color: #f1f1f1;
                overflow-x: hidden;
                transition: 0.5s;
                padding-top: 70px;
            }

            .sidenav .closebtn {
                position: absolute;
                top: 0;
                right: 25px;
                font-size: 36px;
                margin-left: 50px;
                cursor: pointer;
            }

            .category-header {
                margin-left: 10%;
                font-weight: bold;
                font-size: 24px;
            }

            .dropdown {
                float: left;
                overflow: hidden;
            }

            .dropdown-list {
                display: grid;
                padding-left: 10%;
            }

            .dropdown .dropbtn {
                font-size: 20px;
                cursor: pointer;
                font-size: 16px;  
                border: none;
                outline: none;
                padding: 5px 0px;
                color: #5959b5;
                background-color: inherit;
                font-family: inherit;
                margin: 0;
            }

            .dropdown-content {
                display: none;
                min-width: 160px;
                z-index: 1;
            }

            .dropdown-content a {
                font-size: 17px;
                float: none;
                color: #3d9abb;
                padding: 5px 16px;
                text-decoration: none;
                display: block;
                text-align: left;
            }

            .show {
                display: block;
            }
            
        </style>

        <div class="sidenav" id="mySidenav">
            <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
            <p class="category-header">Browse By Category</p>
            <div class="dropdown-list">
                <div class="dropdown">
                    <button class="dropbtn dropbtn1" onclick="myFunction(1)">PETS & OTHER ANIMALS
                        <i class="fa fa-caret-down"></i>
                    </button>
                    <div class="dropdown-content" id="myDropdown1">
                        <a href="#">Dog paintings</a>
                        <a href="#">Lion paintings</a>
                        <a href="#">Bird paintings</a>
                        <a href="#">Elephant paintings</a>
                        <a href="#">Rabbit paintings</a>
                        <a href="#">Frog paintings</a>
                        <a href="#">Zeobra paintings</a>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="dropbtn dropbtn2" onclick="myFunction(2)">FAMILY & CHILDREN
                        <i class="fa fa-caret-down"></i>
                    </button>
                    <div class="dropdown-content" id="myDropdown2">
                        <a href="#">Adult children & parents paintings</a>
                        <a href="#">Family get-together paintings</a>
                        <a href="#">Family vacation paintings</a>
                        <a href="#">Baby & parent(s) paintings</a>
                        <a href="#">Baby paintings</a>
                        <a href="#">Toddler paintings</a>
                        <a href="#">Siblings paintings</a>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="dropbtn dropbtn3" onclick="myFunction(3)">SELF PORTRAITS
                        <i class="fa fa-caret-down"></i>
                    </button>
                    <div class="dropdown-content" id="myDropdown3">
                        <a href="#">Women paintings</a>
                        <a href="#">Men paintings</a>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="dropbtn dropbtn4" onclick="myFunction(4)">COUPLES
                        <i class="fa fa-caret-down"></i>
                    </button>
                    <div class="dropdown-content" id="myDropdown4">
                        <a href="#">Couple portraits paintings</a>
                        <a href="#">Couple vacation paintings</a>
                        <a href="#">Intimate moments paintings</a>
                        <a href="#">Wedding paintings</a>
                        <a href="#">Honeymoon paintings</a>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="dropbtn dropbtn5" onclick="myFunction(5)">SPECIAL EVENT
                        <i class="fa fa-caret-down"></i>
                    </button>
                    <div class="dropdown-content" id="myDropdown5">
                        <a href="#">Graduation paintings</a>
                        <a href="#">Wedding & engagement paintings</a>
                        <a href="#">Party paintings</a>
                        <a href="#">Performing arts paintings</a>
                        <a href="#">Birthday paintings</a>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="dropbtn dropbtn6" onclick="myFunction(6)">LANDSCAPE & FLOWERS
                        <i class="fa fa-caret-down"></i>
                    </button>
                    <div class="dropdown-content" id="myDropdown6">
                        <a href="#">Buildings & street paintings</a>
                        <a href="#">Nature scenery paintings</a>
                        <a href="#">Flower paintings</a>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="dropbtn dropbtn7" onclick="myFunction(7)">VINTAGE RESTORATION
                        <i class="fa fa-caret-down"></i>
                    </button>
                    <div class="dropdown-content" id="myDropdown7">
                        <a href="#">Restore old photos paintings</a>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="dropbtn dropbtn8" onclick="myFunction(8)">MERGE DIFFERENT PICTURES
                        <i class="fa fa-caret-down"></i>
                    </button>
                    <div class="dropdown-content" id="myDropdown8">
                        <a href="#">Combine photos paintings</a>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="dropbtn dropbtn9" onclick="myFunction(9)">CLASSICS REPRODUCTION
                        <i class="fa fa-caret-down"></i>
                    </button>
                    <div class="dropdown-content" id="myDropdown9">
                        <a href="#">Copy another paintings</a>
                        <a href="#">Renaissance & Historical paintings</a>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="dropbtn dropbtn10" onclick="myFunction(10)">WORDS & POPART
                        <i class="fa fa-caret-down"></i>
                    </button>
                    <div class="dropdown-content" id="myDropdown10">
                        <a href="#">Graphic art paintings</a>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="dropbtn dropbtn11" onclick="myFunction(11)">CARS & MOTOCYLES
                        <i class="fa fa-caret-down"></i>
                    </button>
                    <div class="dropdown-content" id="myDropdown11">
                        <a href="#">Cars & motorbikes paintings</a>
                        <a href="#">Aircrafts paintings</a>
                        <a href="#">Ship & boats paintings</a>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="dropbtn dropbtn12" onclick="myFunction(12)">MODERN LIFE & CULTURE
                        <i class="fa fa-caret-down"></i>
                    </button>
                    <div class="dropdown-content" id="myDropdown12">
                        <a href="#">Funny cartoonish paintings</a>
                        <a href="#">Harry Potter paintings</a>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="dropbtn dropbtn13" onclick="myFunction(13)">ABSTRACT
                        <i class="fa fa-caret-down"></i>
                    </button>
                    <div class="dropdown-content" id="myDropdown13">
                        <a href="#">Bend reality paintings</a>
                        <a href="#">Fantasy world paintings</a>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="dropbtn dropbtn14" onclick="myFunction(14)">REMOVE OBJECTS
                        <i class="fa fa-caret-down"></i>
                    </button>
                    <div class="dropdown-content" id="myDropdown14">
                        <a href="#">Remove things from pictures paintings</a>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="dropbtn dropbtn15" onclick="myFunction(15)">CHANGE BACKGROUND
                        <i class="fa fa-caret-down"></i>
                    </button>
                    <div class="dropdown-content" id="myDropdown15">
                        <a href="#">Studio background paintings</a>
                        <a href="#">Outdoors background paintings</a>
                    </div>
                </div>
            </div>
        </div>

        <a class="browse-category" id="browse_category" onclick="openNav()">Browse By Category</a>
        


        <div class="static-header light clearfix" id="hero" style="min-height: 100px;">
            &nbsp;
        </div>
        
        <section id="feedback" class="section light">
            <div class="container" style="margin-bottom: 55px">
                <div class="section-header animated hiding" data-animation="fadeInDown">
                    <h2><img style="position: relative; bottom: 15px;" src="/PF.Site/Apps/instapaint/assets/front-page/img/logo.png" data-index="0" /> <span class="highlight" style="font-size: 65px">Gallery</span></h2>
                </div>
            </div>
        </section>

        <section id="features-list" class="section dark" style="padding: 0">

            <style>
                /* clear fix */
                .grid:after {
                    content: '';
                    display: block;
                    clear: both;
                }

                /* ---- .grid-item ---- */


                .fixmenu-stick {
                    z-index: 999;
                }

                .grid {
                    max-width: 1000px;
                    margin: auto;
                    margin-top: 10px;
                }

                .grid-item {
                    max-width: 50%;
                    padding: 10px;
                }

                .painting-testimonial {
                    width: 100%;
                    background-color: black;
                    color: white;
                    padding: 15px;
                }

                .twentytwenty-wrapper {
                    border: none;
                    box-shadow: none;
                    margin-bottom: 0;
                }

                .twentytwenty-drag-me {
                    position: relative;
                    top: 9px;
                    left: 0;
                    font-size: 12px;
                    color: white;
                }

                @media (min-width: 992px) and (max-width: 1199px) {

                    #feedback.section .section-header {
                        margin-top: 100px;
                    }
                }

                @media (min-width: 768px) and (max-width: 1199px) {
                    #feedback.section .section-header {
                        margin-top: 100px;
                    }
                }
            </style>

            <div class="grid gallery">
                <div class="grid-item">
                    <img src="/PF.Site/Apps/instapaint/assets/gallery/sample149.jpg" data-index="0" />
                    <div class="painting-testimonial">"The detail and likeness of the painting to the photo is incredible! I specifically asked my painter to do a photorealism style, but you can choose any style you want. I ordered expedited service (a great feature) so that I could surprise my husband with the painting. He was so touched by the gift! Will order again for more paintings of the kids and my parents." - Lauren Holden</div>
                </div>
                <div class="grid-item">
                    <img src="/PF.Site/Apps/instapaint/assets/gallery/sample999.jpg" data-index="0" />
                    <div class="painting-testimonial">"The painting I received was so beautiful and service was quick. I was able to approve the order before it was shipped so the painter revised the painting until it met my expectations fully." - Grant Kemp, Celebrity on The Bachelor</div>
                </div>
                <div class="grid-item">
                    <div id='before-after-container-1' class="twentytwenty-container">
                        <img onload='$("#before-after-container-1").twentytwenty({default_offset_pct:.5});' src="/PF.Site/Apps/instapaint/assets/gallery/sample143before.jpg" />
                        <img src="/PF.Site/Apps/instapaint/assets/gallery/sample143.jpg" />
                    </div>
                    <div class="painting-testimonial" style="visibility: hidden;">"This was the perfect gift celebrating our relationship. The ordering process was easy and customer service was excellent, fast and responsive." - Nicholas Navarini</div>
                </div>
                <div class="grid-item">
                    <div id='before-after-container' class="twentytwenty-container">
                        <img onload='$("#before-after-container").twentytwenty({default_offset_pct:.5});' src="/PF.Site/Apps/instapaint/assets/gallery/sample102before.jpg" />
                        <img src="/PF.Site/Apps/instapaint/assets/gallery/sample102.jpg" />
                    </div>
                    <div class="painting-testimonial">"The painting was simply magnificent, I am so happy with the way it turned out and will definitely order another with my family in it. - Clara Blackmore</div>
                </div>
            </div>

            <script>
                var showGalleryImage;
                $(document).ready(function () {
                    var $grid = $('.gallery').masonry({
                        itemSelector: '.grid-item'
                    });

                    // layout Masonry after each image loads
                    $grid.imagesLoaded().progress( function(instance, image) {
                        $grid.masonry('layout');
                    });
                });
            </script>

            <a id="gallery-buy-button-1" style="margin-bottom: 10px; margin-top: 20px" href="/first-order/" class="cta-buy attention-button btn btn-primary btn-lg animated bounceIn visible" data-animation="bounceIn" data-delay="700">BUY NOW</a>

            <section id="feedback" class="section light" style="margin-top: 20px; margin-bottom: 0; padding: 0;">
                <div class="container">
                    <div class="section-header animated hiding" data-animation="fadeInDown" style="padding: 10px;">
                        <h2 id="reviews">Different <span class="highlight">Painting</span> Styles</h2>

                        <p style="font-size: 18px;">Click on any painting we have done below to use the same style. Or we will choose the best style suitable for your painting.</p>
                        <p style="font-size: 18px;">Or you can make up your own style in the order notes.</p>
                    </div>
                </div>
            </section>

            <div class="grid gallery">
                <div class="grid-item">
                    <a id="gallery-style-1" class="cta-buy" href="/first-order/?style=1"><img src="/PF.Site/Apps/instapaint/assets/gallery/sample53.jpg" data-index="0" /></a>
                </div>
                <div class="grid-item">
                    <a id="gallery-style-2" class="cta-buy" href="/first-order/?style=2"><img src="/PF.Site/Apps/instapaint/assets/gallery/sample78.jpg" data-index="0" /></a>
                </div>
                <div class="grid-item">
                    <a id="gallery-style-3" class="cta-buy" href="/first-order/?style=3"><img src="/PF.Site/Apps/instapaint/assets/gallery/sample138.jpg" data-index="0" /></a>
                </div>
                <div class="grid-item">
                    <a id="gallery-style-4" class="cta-buy" href="/first-order/?style=4"><img src="/PF.Site/Apps/instapaint/assets/gallery/sample136.jpg" data-index="0" /></a>
                </div>
                <div class="grid-item">
                    <a id="gallery-style-5" class="cta-buy" href="/first-order/?style=5"><img src="/PF.Site/Apps/instapaint/assets/gallery/sample100.jpg" data-index="0" /></a>
                </div>
                <div class="grid-item">
                    <a id="gallery-style-6" class="cta-buy" href="/first-order/?style=6"><img src="/PF.Site/Apps/instapaint/assets/gallery/sample139.jpg" data-index="0" /></a>
                </div>
                <div class="grid-item">
                    <a id="gallery-style-7" class="cta-buy" href="/first-order/?style=7"><img src="/PF.Site/Apps/instapaint/assets/gallery/sample140.jpg" data-index="0" /></a>
                </div>
                <div class="grid-item">
                    <a id="gallery-style-8" class="cta-buy" href="/first-order/?style=8"><img src="/PF.Site/Apps/instapaint/assets/gallery/sample115.jpg" data-index="0" /></a>
                </div>
                <div class="grid-item">
                    <a id="gallery-style-9" class="cta-buy" href="/first-order/?style=9"><img src="/PF.Site/Apps/instapaint/assets/gallery/sample141.jpg" data-index="0" /></a>
                </div>
                <div class="grid-item">
                    <a id="gallery-style-10" class="cta-buy" href="/first-order/?style=10"><img src="/PF.Site/Apps/instapaint/assets/gallery/sample101.jpg" data-index="0" /></a>
                </div>
                <div class="grid-item">
                    <a id="gallery-style-11" class="cta-buy" href="/first-order/?style=11"><img src="/PF.Site/Apps/instapaint/assets/gallery/sample111.jpg" data-index="0" /></a>
                </div>
                <div class="grid-item">
                    <a id="gallery-style-12" class="cta-buy" href="/first-order/?style=12"><img src="/PF.Site/Apps/instapaint/assets/gallery/sample88.jpg" data-index="0" /></a>
                </div>
                <div class="grid-item" style="max-width: 100%; min-width: 100%;">
                    <a id="gallery-style-13" class="cta-buy" href="/first-order/?style=13"><img src="/PF.Site/Apps/instapaint/assets/gallery/sample117.jpg" data-index="0" /></a>
                </div>
            </div>

            <div style="margin-top: 60px; margin-bottom: 20px;" data-animation="fadeInDown" class="animated hiding">
                <img src="/PF.Site/Apps/instapaint/assets/gallery/sample142.jpg" data-index="0" />
            </div>

            <a id="gallery-buy-button-2" style="margin-bottom: 0; margin-top: 10px" href="/first-order/" class="cta-buy attention-button btn btn-primary btn-lg animated bounceIn visible" data-animation="bounceIn" data-delay="700">BUY NOW</a>

        </section>
<br>
<br>

<?php

        // Include footer HTML:
        require_once PHPFOX_DIR_SITE . 'Apps/instapaint/assets/front-page/templates/footer.php';


        exit();

    }
}

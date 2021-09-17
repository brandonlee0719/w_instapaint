PF.event.on('on_page_column_init_end', function() {
    var owl = $('.membership-package-slider');
    if (!owl.length || owl.prop('built')) {
        return false;
    }
    var itemcheck=3;
    //check empty-left,right
    if (($( "#page_subscribe_index #main" ).hasClass( "empty-left" ))&($( "#page_subscribe_index #main" ).hasClass( "empty-right" ))){
        itemcheck=4;
    }else if(($( "#page_subscribe_index #main" ).hasClass( "empty-left" )) || ($( "#page_subscribe_index #main" ).hasClass( "empty-right" ))){
        itemcheck=3;
    }else {
        itemcheck=2;
    }
    owl.prop('built', true);
    owl.addClass('dont-unbind-children');
    var rtl = false;
    if ($("html").attr("dir") == "rtl") {
        rtl = true;
    }
    var item_amount = parseInt(owl.find('.item').length);
    var more_than_one_item = item_amount > 1;

    owl.owlCarousel({
        rtl: rtl,
        items: 1,
        //loop: more_than_one_item,
        navText: ["<i class='ico ico-angle-left'></i>", "<i class='ico ico-angle-right'></i>"],
        mouseDrag: more_than_one_item,
        margin:15,
        nav: false,
        dotsEach: true,
        autoplay: more_than_one_item,
        responsiveClass: true,
        responsive: {
            // breakpoint from 768 up
            768: {
                items:2,
                margin:10,
            },
            992: {
                items:itemcheck,
                nav:true,
            }
        }
    });
});

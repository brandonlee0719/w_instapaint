!function(){"use strict";window.app={el:{},fn:{}},app.el.window=$(window),app.el.document=$(document),app.el.loader=$("#loader"),app.el.mask=$("#mask"),app.fn.screenSize=function(){var e=app.el.window.width()},app.el.loader.delay(700).fadeOut(),app.el.mask.delay(1200).fadeOut("slow"),app.el.window.resize(function(){app.fn.screenSize()}),0!=$(".testimonials-slider").length&&$(".testimonials-slider").flexslider({manualControls:".flex-manual .switch",nextText:"",prevText:"",startAt:1,slideshow:!1,direction:"horizontal",animation:"slide"});if(0==$("#registration").length)new Headhesive(".navigation-header",{offset:"#showHere",classes:{clone:"fixmenu-clone",stick:"fixmenu-stick",unstick:"fixmenu-unstick"}});$("body").hasClass("home")&&$(".navigation-bar").onePageNav({currentClass:"active",changeHash:!0,scrollSpeed:750,scrollThreshold:.5,easing:"swing"}),app.el.window.width()>1024?$(".animated").appear(function(){var e=$(this),a=e.data("animation"),i=e.data("delay");i?setTimeout(function(){e.addClass(a+" visible"),e.removeClass("hiding")},i):(e.addClass(a+" visible"),e.removeClass("hiding"))},{accY:-150}):$(".animated").css("opacity",1),$(window).scroll(function(){$(this).scrollTop()>500?$(".back-to-top").fadeIn():$(".back-to-top").fadeOut(),$(".navigation-bar-right li").removeClass("active")}),$(".back-to-top").click(function(){return $("html, body").animate({scrollTop:0,easing:"swing"},750),$(".navigation-bar-right li").removeClass("active"),!1});var e=new Date;e.setDate(e.getDate()+10),$(".countdown").countdown({until:e,compact:!0,padZeroes:!0,layout:$(".countdown").html()}),$("#image-file").on("change",function(){readURL(this,"#image-preview")}),$("#image-ref-file").on("change",function(){readURL(this,"#image-ref-preview")})}();
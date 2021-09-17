PF.event.on('on_page_column_init_end', function() {
  var owl = $('.announcement-slider');
  if (!owl.length || owl.prop('built')) {
    return false;
  }
  owl.prop('built', true);
  owl.addClass('dont-unbind-children');
  var rtl = false;
  if ($('html').attr('dir') == 'rtl') {
    rtl = true;
  }
  var item_amount = parseInt(owl.find('.item').length),
    more_than_one_item = item_amount > 1;

  $Core.Announcement.totalItems = item_amount;

  owl.on('initialized.owl.carousel', function (e) {
    $Core.Announcement.currentIndex = e.item.index;
    setTimeout(function() {
      $('#js_core_announcement_carousel').trigger('refresh.owl.carousel');
    }, 100);
  }).on('changed.owl.carousel', function (e) {
    $Core.Announcement.currentIndex = e.item.index;
  }).owlCarousel({
    rtl: rtl,
    items: 1,
    loop: more_than_one_item,
    navText: [
      '<i class=\'ico ico-angle-left\'></i>',
      '<i class=\'ico ico-angle-right\'></i>'],
    mouseDrag: more_than_one_item,
    nav: false,
    dotsEach: true,
    autoplay: more_than_one_item,
    responsiveClass: true,
    responsive: {
      992: {
        nav: true,
      },
    },
  });
});

$Core.Announcement = {
  currentIndex: 0,

  totalItems: 0,

  deleteCarouselItem: function(id) {
    $('#js_core_announcement_carousel').trigger('remove.owl.carousel', [$Core.Announcement.currentIndex - 2]).trigger('refresh.owl.carousel'); // $Core.Announcement.currentIndex - 2: I minus 2 because in autoplay mode, owl carousel clone 2 slides before our real item
    $.ajaxCall('announcement.hideAnnouncement', 'id=' + id);
  }
};
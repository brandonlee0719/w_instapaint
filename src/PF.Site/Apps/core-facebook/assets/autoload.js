/**
 * Load the routine when PHPfox is ready
 */
$Ready(function() {

  /**
   * Loop thru all profile images with a connection to Facebook
   */
  $('.image_object:not(.fb_built)[data-object="fb"]').each(function() {
    var t = $(this),
        src = '//graph.facebook.com/' + t.data('src') +
            '/picture?type=square&width=200&height=200';

    t.addClass('fb_built');
    t.attr('src', src);
  });

  // Add the FB login button
  if (!$('.fb_login_go_cache').length && (typeof(Fb_Login_Disabled) == 'undefined' || !Fb_Login_Disabled)) {
    var l = $('#js_block_border_user_login-block form');
    if (l.length) {
      l.before(
          '<span class="fb_login_go fb_login_go_cache"><span class="ico ico-facebook"></span>Facebook</span>');
    } else {
      l = $('[data-component="guest-actions"]');
      l.append(
          '<div class="facebook-login-header"><span class="fb_login_go fb_login_go_cache"><span class="ico ico-facebook"></span> <span class="facebook-login-label">Facebook</span></span></div>');
      $('.login-menu-btns-xs').
          after(
              '<span class="fb_login_go fb_login_go_cache"><span class="ico ico-facebook"></span>Facebook</span>');
    }
  }

  // Click event to send the user to log into Facebook
  $('.fb_login_go').click(function() {
    PF.url.send('/fb/login', true);
  });
});
$Core.searchFriendsInput = {
  aParams: {},
  iCnt: 0,
  aFoundUsers: {},
  aLiveUsers: {},
  sId: '',
  bNoSearch: false,
  isBeingBuilt: false,
  sCurrentSearchId: '',

  aFoundUser: {}, // Store the found user here
  sHtml: '', // Store the final html here. Useful for onBeforePrepend
  init: function($aParams) {
    this.aParams = $aParams;
    if (!isset(this.aParams['search_input_id'])) {
      this.aParams['search_input_id'] = 'search_input_name_' +
          Math.round(Math.random() * 10000);
    }
    if (this._get('no_build')) {
      this.sId = $aParams['id'].replace('#', '');
    }
    else {
      this.sId = $aParams['id'].replace('#', '').replace('.', '') +
          '__tmp__';
    }

    //set params data on DOM
    var element = $($aParams['id']);
    if (element.length > 0 && empty($aParams['no_build'])) {
      this.sCurrentSearchId = element.attr('id');
      element.data('params', $aParams);
      element.addClass('search_friend_params_built');

      if (!element.hasClass('build-users') &&
          !empty($aParams['live_users']) && empty(element.data('users'))) {
        this.setLiveUsers($aParams['live_users']);
        element.addClass('build-users');
      }
    }

    this.build();
  },

  build: function() {
    var $sHtml = '';
    if (!this._get('no_build')) {
      $sHtml += '<input type="text" id="' + this._get('search_input_id') +
          '" name="null" placeholder="' + this._get('default_value') +
          '" autocomplete="off" onfocus="$Core.searchFriendsInput.buildFriends(this);" onkeyup="$Core.searchFriendsInput.getFriends(this);" style="width:100%;" class="form-control js_temp_friend_search_input" />';
      $sHtml += '<div class="js_temp_friend_search_form" style="display:none;"></div>';

      $(this._get('id')).html($sHtml);
      this.buildFriends(this._get('search_input_id'));
    }
    else {
      $sHtml += '<div class="js_temp_friend_search_form js_temp_friend_search_form_main" style="display:none;"></div>';
      $('#' + this.sId).find('form:first').append($sHtml);
    }

    $('#' + this.sId).
        find('.js_temp_friend_search_input').
        keypress(function(e) {
          switch (e.keyCode) {
            case 9:
            case 40:
            case 38:
              var $iNextCnt = 0;
              $('.js_friend_search_link').each(function() {
                $iNextCnt++;
                if ($(this).
                        hasClass(
                            'js_temp_friend_search_form_holder_focus')) {
                  $(this).
                      removeClass(
                          'js_temp_friend_search_form_holder_focus');

                  return false;
                }
              });

              if (!$iNextCnt) {
                return false;
              }

              $Core.searchFriendsInput.bNoSearch = true;

              var $iNewCnt = 0;
              var $iActualFocus = 0;
              $('.js_friend_search_link').each(function() {
                $iNewCnt++;
                if ((e.keyCode == 38
                        ? ($iNextCnt - 1) == $iNewCnt
                        : ($iNextCnt + 1) == $iNewCnt)) {
                  $iActualFocus++;
                  $(this).
                      addClass('js_temp_friend_search_form_holder_focus');
                  return false;
                }
              });

              if (!$iActualFocus) {
                $('.js_friend_search_link').each(function() {
                  $(this).
                      addClass('js_temp_friend_search_form_holder_focus');

                  return false;
                });
              }

              return false;
              break;
            case 13:
              $Core.searchFriendsInput.bNoSearch = true;
              $('.js_friend_search_link').each(function() {
                if ($(this).
                        hasClass(
                            'js_temp_friend_search_form_holder_focus')) {
                  $Core.searchFriendsInput.processClick(this,
                      $(this).attr('rel'));
                }
              });
              break;
            default:
              // p(e.keyCode);
              break;
          }
        });
  },

  buildFriends: function($oObj) {
    if (this.isBeingBuilt === false && empty($Cache.friends) &&
        !isset(this.aParams['is_mail'])) {
      $($oObj).val('');
      this.isBeingBuilt = true;
      $.ajaxCall('friend.buildCache',
          (this._get('allow_custom') ? '&allow_custom=1' : ''), 'GET');
    }
  },

  getFriends: function($oObj) {
    this.sCurrentSearchId = $($oObj).
        closest('.search_friend_params_built').
        attr('id');
    if (empty($oObj.value)) {
      this.closeSearch($oObj);
      return;
    }
    if (this.bNoSearch) {
      this.bNoSearch = false;
      return;
    }
    if (isset(this._get('is_mail')) && this._get('is_mail') == true) {
      $.ajaxCall('friend.getLiveSearch', 'parent_id=' +
          $($oObj).attr('id') + '&search_for=' + $($oObj).val() +
          '&width=' + this._get('width') + '&total_search=' +
          $Core.searchFriendsInput._get('max_search'), 'GET');
      return;
    }
    var $iFound = 0;
    var $sHtml = '';

    var aFriends = JSON.parse(JSON.stringify($Cache.friends));

    var aViewer = this._get('viewer');
    if (!empty(aViewer)) {
      aFriends[aFriends.length] = aViewer;
    }

    $(aFriends).each(function($sKey, $aUser) {
      var $mRegSearch = new RegExp($oObj.value, 'i');

      if ($aUser['full_name'].match($mRegSearch)) {
        if ($Core.searchFriendsInput.isLiveUser($aUser['user_id'])) {
          return;
        }
        $iFound++;

        $Core.searchFriendsInput.storeUser($aUser['user_id'], $aUser);

        if (($aUser['user_image'].substr(0, 5) == 'http:') ||
            ($aUser['user_image'].substr(0, 6) == 'https:')) {
          $aUser['user_image'] = '<img src="' + $aUser['user_image'] + '">';
        }
        $sHtml += '<li><div rel="' + $aUser['user_id'] +
            '" class="js_friend_search_link ' + (($iFound === 1 &&
                !$Core.searchFriendsInput._get('global_search'))
                ? 'js_temp_friend_search_form_holder_focus'
                : '') +
            '" onclick="return $Core.searchFriendsInput.processClick(this, \'' +
            $aUser['user_id'] + '\');"><span class="image">' +
            $aUser['user_image'] + '</span><span class="user">' +
            $aUser['full_name'] + '</span></div></li>';
        if ($iFound > $Core.searchFriendsInput._get('max_search')) {
          return false;
        }
      }
    });

    var obj = $($oObj).parent().find('.js_temp_friend_search_form');
    var m_global = 0;
    if (this._get('panel_mode')) {
      if ($('.panel_xs').length && $('.panel_xs').is(':visible')) {
        obj = $('.panel_xs').find('.js_temp_friend_search_form');
        m_global = true;
      }
      else {
        obj = $('#panel').find('.js_temp_friend_search_form');
        m_global = false;
      }
    }
    if ($('.panel_xs').length && $('.panel_xs').is(':visible')) {
      obj = $('.panel_xs').find('.js_temp_friend_search_form');
      m_global = true;
    }
    if ($iFound) {
      if (this._get('global_search') || m_global) {
        $sHtml += '<li><a href="#" class="holder_notify_drop_link" onclick="$(\'#header_search_form\').submit(); return false;">' +
            oTranslations['show_more_results_for_search_term'].replace(
                '{search_term}', htmlspecialchars($oObj.value)) +
            '</a></li>';
      }

      obj.html('<div class="js_temp_friend_search_form_holder"><ul>' +
          $sHtml + '</ul></div>');
      if (typeof mCustomScrollbar !== 'undefined') {
        obj.find('.js_temp_friend_search_form_holder').mCustomScrollbar({
          theme: 'minimal-dark',
        });
      }
      obj.show();
    }
    else {
      obj.html('').hide();
    }
  },

  storeUser: function($iUserId, $aData) {
    this.aFoundUsers[$iUserId] = $aData;
  },

  removeSelected: function($oObj, $iUserId) {
    var searchId = $($oObj).data('search-id');

    if (!empty(searchId)) {
      this.sCurrentSearchId = searchId;
    }
    if (this.isLiveUser($iUserId)) {
      this.removeLiveUser($iUserId);
    }
    var searchFriendHolder = $($oObj).
        parents('div#js_mail_search_friend_placement');
    if (searchFriendHolder.find('li').length == 1) {
      searchFriendHolder.empty();
    }
    else {
      $($oObj).closest('span').remove();
      if (this._get('input_type') == 'single') {
        var singleInput = $(this._get('single_input'));
        singleInput.val(singleInput.val().replace($iUserId, '').replace(/(^,)|(,$)/g, ''));
      }
    }
  },

  processClick: function($oObj, $iUserId) {
    this.sCurrentSearchId = $($oObj).
        closest('.search_friend_params_built').
        attr('id');
    if (!isset(this.aFoundUsers[$iUserId])) {
      return false;
    }

    if (this.isLiveUser($iUserId)) {
      return false;
    }

    $('#' + this._get('search_input_id')).val('');
    this.addLiveUser($iUserId);
    this.bNoSearch = false;
    var $aUser = this.aFoundUser = this.aFoundUsers[$iUserId],
        $oPlacement = $(this._get('placement'));
    if(typeof $aUser === 'object' && typeof $aUser['full_name'] === 'string'){
      $aUser['full_name'] = $aUser['full_name'].replace(/&#039;/g,"'");
    }

    //$($oObj).parents('.js_friend_search_form:first').find('.js_temp_friend_search_input').val('').focus();
    $($oObj).closest('.js_temp_friend_search_form').html('').hide();

    if ($oPlacement.find('span#js_friend_search_row_' +
            $aUser['user_id']).length == 0) {
      var $sHtml = '';
      $sHtml += '<span id="js_friend_search_row_' + $aUser['user_id'] + '" class="item-user-selected">';

      $sHtml += '<span class="item-name">'+  $aUser['full_name']
          +'</span>';

      $sHtml += '<a role="button" class="friend_search_remove" data-search-id="' +
          this.sCurrentSearchId +
          '" title="Remove" onclick="$Core.searchFriendsInput.removeSelected(this, ' +
          $iUserId + ');  return false;"><i class="ico ico-close"></i></a>';

      if (this._get('input_type') == 'multiple') {
        $sHtml += '<input type="hidden" name="' +
            this._get('input_name') + '[]" value="' + $aUser['user_id'] +
            '" />';
      } else {
        var singleInput = $(this._get('single_input'));
        singleInput.val(singleInput.val() + ',' + $aUser['user_id']);
      }
      this.sHtml = $sHtml;

      if (this._get('onBeforePrepend')) {
         this._get('onBeforePrepend')(this._get('onBeforePrepend'));
      }

      $oPlacement.prepend(this.sHtml);

    }

    if (this._get('onclick')) {
      this._get('onclick')(this._get('onclick'));
    }

    if (this._get('global_search')) {
      window.location.href = $aUser['user_profile'];
      $($oObj).parents('.js_temp_friend_search_form:first').hide();
    }

    this.aFoundUsers = {};

    if (this._get('inline_bubble')) {
      $('#' + this._get('search_input_id') + '').val('').focus();
    }

    return false;
  },

  closeSearch: function($oObj) {
    $($oObj).parent().find('.js_temp_friend_search_form').html('').hide();
  },

  _get: function($sParam) {
    if (this.sCurrentSearchId &&
        $('#' + this.sCurrentSearchId).length > 0) {
      var dataElement = $('#' + this.sCurrentSearchId);
      if (dataElement.length > 0) {
        var $aParams = dataElement.data('params');
        if (!empty($aParams)) {
          return isset($aParams[$sParam]) ? $aParams[$sParam] : '';
        }
      }
    }

    return (isset(this.aParams[$sParam]) ? this.aParams[$sParam] : '');
  },

  isLiveUser: function($iUserId) {
    var dataElement = $('#' + this.sCurrentSearchId);
    var aLiveUsers = dataElement.data('users');
    return (empty(aLiveUsers) || empty(aLiveUsers[$iUserId])
        ? false
        : true);
  },

  addLiveUser: function($iUserId) {
    var dataElement = $('#' + this.sCurrentSearchId);
    var aLiveUsers = dataElement.data('users');
    if (empty(aLiveUsers)) {
      aLiveUsers = {};
    }
    aLiveUsers[$iUserId] = 1;
    dataElement.data('users', aLiveUsers);
  },

  removeLiveUser: function($iUserId) {
    var dataElement = $('#' + this.sCurrentSearchId);
    var aLiveUsers = dataElement.data('users');
    if (!empty(aLiveUsers)) {
      delete aLiveUsers[$iUserId];
    }
    dataElement.data('users', aLiveUsers);
  },
  setLiveUsers: function($aUsers) {
    var dataElement = $('#' + this.sCurrentSearchId);
    var aLiveUsers = {};
    for (i = 0; i < $aUsers.length; i++) {
      aLiveUsers[$aUsers[i]] = 1;
    }
    dataElement.data('users', aLiveUsers);
  },
};
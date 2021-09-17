var Core_Pages = {
  cropmeImgSrc: '',
  cmds: {
    add_new_page: function(ele, evt) {
      tb_show(oTranslations['add_new_page'],
          $.ajaxBox('pages.addPage', 'height=400&width=550&type_id=' +
              ele.data('type-id')));
      return false;
    },

    select_category: function(ele, evt) {
      $('[class^=select-category-]').hide();
      $('.select-category-' + ele.val()).show();
      $('#select_sub_category_id').val(0);
    },

    add_page_process: function(ele, evt) {
      evt.preventDefault();
      ele.ajaxCall('pages.add');
      // disable submit button
      var submit = $('input[type="submit"]', ele);
      submit.prop('disabled', true).addClass('submitted');
    },

    init_google_map: function(ele) {
      /* Load Google */
      if (($('body#page_pages_add').length === 0 &&
              $('body#page_pages_view').length === 0) ||
          typeof oParams['core.google_api_key'] === 'undefined') {
        return;
      }

      if (!$Core.PagesLocation.bGoogleReady &&
          typeof oParams['core.google_api_key'] !== 'undefined') {
        ele.hide();
        $Core.PagesLocation.loadGoogle(ele);
      }

      $Core.PagesLocation.sMapId = ele.attr('id');

      var initPagesLocation = setInterval(function() {
        if (typeof google !== 'undefined') {
          $Core.PagesLocation.init();
          clearInterval(initPagesLocation);
        }
      }, 500);

      $('a[rel^=js_pages_block_]').click(function() {
        if ($(this).attr('rel') == 'js_pages_block_location') {
          ele.show();
          google.maps.event.trigger($Core.PagesLocation.gMap, 'resize');
          if (typeof $Core.PagesLocation.gMap.panTo === 'function') {
            $Core.PagesLocation.gMap.panTo($Core.PagesLocation.gMyLatLng);
          }
          $($Core.PagesLocation).on('mapCreated', function() {
            if ($('#' + $Core.PagesLocation.sMapId).data('location-set') ==
                'false') {
              $Core.PagesLocation.gMap.setCenter($Core.PagesLocation.gMyLatLng);
            }
          });
        }
        else {
          ele.hide();
        }
      });
    },

    admin_delete_category_image: function(ele) {
      $Core.jsConfirm(
          {message: oTranslations['are_you_sure_you_want_to_delete_this_category_image']},
          function() {
            $.ajaxCall('pages.deleteCategoryImage',
                'type_id=' + ele.data('type-id'));
          }, function() {});
    },

    admin_edit_category_change: function(ele) {
      ele.val() != 0 ? $('#image_select').hide() : $('#image_select').show();
    },

    check_url: function(ele) {
      if ($('#js_vanity_url_new').val() != $('#js_vanity_url_old').val()) {
        $Core.processForm('#js_pages_vanity_url_button');
        $(ele.parents('form:first')).ajaxCall('pages.changeUrl');
      }
      return false;
    },

    init_drag: function(ele) {
      Core_drag.init({table: ele.data('table'), ajax: ele.data('ajax')});
    },

    search_member: function(ele, evt) {
      var parentBlock = $('.pages-block-members'),
          activeTab = $('li.active a', parentBlock),
          container = $(ele.data('container')),
          resultContainer = $(ele.data('result-container'));

      if (ele.val()) {
        container.addClass('hide');
        resultContainer.removeClass('hide');
        $.ajaxCall('pages.getMembers', 'tab=' + activeTab.data('tab') + '&container=' + ele.data('result-container') + '&page_id=' + ele.data('page-id') + '&search=' + ele.val());
      } else {
        container.removeClass('hide');
        resultContainer.addClass('hide');
      }
    },

    change_tab: function(ele, evt) {
      evt.preventDefault();
      var container = $(ele.data('container')),
          resultCotainer = $(ele.data('result-container'));

      // hide search result div, show container div
      container.hasClass('hide') && container.removeClass('hide');
      !resultCotainer.hasClass('hide') && resultCotainer.addClass('hide');

      // only show moderation in `all members` tab
      if (ele.data('tab') === 'all') {
        $('.moderation_placeholder').removeClass('hide');
      } else {
        $('.moderation_placeholder').addClass('hide');
      }

      // ajax call to get tab members
      $.ajaxCall('pages.getMembers', 'tab=' + ele.data('tab') + '&container=' + ele.data('container') + '&page_id=' + ele.data('page-id'));
    },

    remove_admin: function(ele, evt) {
      $Core.jsConfirm({
        message: ele.data('message')
      }, function() {
        $.ajaxCall('pages.removeAdmin', 'page_id=' + ele.data('page-id') + '&user_id=' + ele.data('user-id'))
      }, function() {});
    },

    remove_member: function(ele, evt) {
      $Core.jsConfirm({
        message: ele.data('message')
      }, function() {
        $.ajaxCall('pages.removeMember', 'page_id=' + ele.data('page-id') + '&user_id=' + ele.data('user-id'))
      }, function() {});
    },

    disable_submit: function(form) {
      $('input[type="submit"]', form).prop('disabled', true).addClass('submitted');
    },

    like_page: function(ele, evt) {
      ele.fadeOut('fast', function() {
        ele.prev().fadeIn('fast');
      });
      $.ajaxCall('like.add', 'type_id=pages&pages_not_reload=true&item_id=' + ele.data('id')); return false;
    },
  },
  checkVal: function() {
    return $('#add_select').val() == '0';
  },
  readyAdd: function() {
    $('#add_select').change(function() {
      if (Core_Pages.checkVal()) {
        $('#is_group').hide();
      }
      else {
        $('#is_group').show();
      }
    });
  },

  resetSubmit: function() {
    $('input[type="submit"].submitted').each(function() {
      $(this).prop('disabled', false).removeClass('submitted');
    });
  },

  updateCounter: function(selector) {
    var ele = $(selector),
        counter = ele.html().substr(1, ele.html().length - 2);

    ele.html('('+ (parseInt(counter) - 1) +')');
  }
};

$(document).on('click', '[data-app="core_pages"]', function(evt) {
  var action = $(this).data('action'),
      type = $(this).data('action-type');
  if (type === 'click' && Core_Pages.cmds.hasOwnProperty(action) &&
      typeof Core_Pages.cmds[action] === 'function') {
    Core_Pages.cmds[action]($(this), evt);
  }
});

$(document).on('change', '[data-app="core_pages"]', function(evt) {
  var action = $(this).data('action'),
      type = $(this).data('action-type');
  if (type === 'change' && Core_Pages.cmds.hasOwnProperty(action) &&
      typeof Core_Pages.cmds[action] === 'function') {
    Core_Pages.cmds[action]($(this), evt);
  }
});

$(document).on('submit', '[data-app="core_pages"]', function(evt) {
  var action = $(this).data('action'),
      type = $(this).data('action-type');
  if (type === 'submit' && Core_Pages.cmds.hasOwnProperty(action) &&
      typeof Core_Pages.cmds[action] === 'function') {
    Core_Pages.cmds[action]($(this), evt);
  }
});

$(document).on('keyup', '[data-app="core_pages"]', function(evt) {
  var action = $(this).data('action'),
      type = $(this).data('action-type');
  if (type === 'keyup' && Core_Pages.cmds.hasOwnProperty(action) &&
      typeof Core_Pages.cmds[action] === 'function') {
    Core_Pages.cmds[action]($(this), evt);
  }
});

$Behavior.pagesInitElements = function() {
  $('[data-app="core_pages"]').each(function() {
    var t = $(this);
    if (t.data('action-type') === 'init' &&
        Core_Pages.cmds.hasOwnProperty(t.data('action')) &&
        typeof Core_Pages.cmds[t.data('action')] === 'function') {
      Core_Pages.cmds[t.data('action')](t);
    }
  });
};

$Core.Pages = {
  setAsCover: function(iPageId, iPhotoId) {
    $.ajaxCall('pages.setCoverPhoto', 'page_id=' + iPageId + '&photo_id=' +
        iPhotoId);
  },

  removeCover: function(iPageId) {
    $Core.jsConfirm({}, function() {
      $.ajaxCall('pages.removeCoverPhoto', 'page_id=' + iPageId);
    }, function() {
    });
  },
};

$Behavior.pagesBuilder = function() {
  // Creating/Editing pages
  if ($Core.exists('#js_pages_add_holder')) {
    $('.pages_add_category select').change(function() {
      $(this).parent().parent().find('.js_pages_add_sub_category').hide();
      $(this).
          parent().
          parent().
          find('#js_pages_add_sub_category_' + $(this).val()).
          show();
      $('#js_category_pages_add_holder').
          val($(this).
              parent().
              parent().
              find('#js_pages_add_sub_category_' + $(this).val() +
                  ' option:first').
              val());
    });

    $('.js_pages_add_sub_category select').change(function() {
      $('#js_category_pages_add_holder').val($(this).val());
    });
  }
};

$Behavior.contentHeight = function() {
  $('#content').height($('.main_timeline').height());
};

$Behavior.fixSizeTinymce = function() {
  //The magic code to add show/hide custom event triggers
  (function($) {
    $.each(['show', 'hide'], function(i, ev) {
      var el = $.fn[ev];
      $.fn[ev] = function() {
        this.trigger(ev);
        return el.apply(this, arguments);
      };
    });
  })(jQuery);

  $('#js_pages_block_info').on('show', function() {
    $('.mceIframeContainer.mceFirst.mceLast iframe').height('275px');
  });
};

/* Implements Google Places into Pages */
$Core.PagesLocation = {
  bGoogleReady: false,
  /* Here we store the places gotten from Google and Pages. This array is reset as the user moves away from the found place */

  aPlaces: [],

  /* The id of the div that will display the map of the current location */
  sMapId: '',

  /* Google requires the key to be passed so we store it here*/
  sGoogleKey: '',

  /* Google's Geocoder object */
  gGeoCoder: undefined,

  /* Google's marker in the map */
  gMarker: undefined,

  /* If the browser does not support Navigator we can get the latitude and longitude using the IPInfoDBKey */
  sIPInfoDbKey: '',

  /* Google object holding my location*/
  gMyLatLng: undefined,

  /* This is the google map object, we can control the map from this variable */
  gMap: {},

  /* This function is triggered by the callback from loading the google api*/
  loadGoogle: function() {
    if ($Core.PagesLocation.bGoogleReady) {
      return false;
    }
    if (typeof google !== 'undefined') {
      $Core.PagesLocation.bGoogleReady = true;
      return false;
    }
    sAddr = window.location.protocol + '//';

    var script = document.createElement('script');
    script.type = 'text/javascript';
    script.src = sAddr +
        'maps.google.com/maps/api/js?libraries=places&sensor=true&key=' +
        oParams['core.google_api_key'] +
        '&callback=$Core.PagesLocation.init';
    document.body.appendChild(script);
    $Core.PagesLocation.bGoogleReady = true;
  },

  init: function() {
    var map = $('#' + $Core.PagesLocation.sMapId);
    if (typeof map === 'undefined') {
      return;
    }

    $($Core.PagesLocation).on('gotVisitorLocation', function() {
      $Core.PagesLocation.updateLatLngField($Core.PagesLocation.gMyLatLng.lat(),
          $Core.PagesLocation.gMyLatLng.lng());
      $Core.PagesLocation.createMap();
      $Core.PagesLocation.createSearch();
    });

    if (typeof map.data('lat') !== 'undefined') {
      $Core.PagesLocation.gMyLatLng = new google.maps.LatLng(
          map.data('lat'), map.data('lng'));
      // update location name
      $('#txt_location_name').val(map.data('lname'));
      $($Core.PagesLocation).trigger('gotVisitorLocation');
    }
    else {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
              $Core.PagesLocation.gMyLatLng = new google.maps.LatLng(
                  position.coords.latitude, position.coords.longitude);
              $($Core.PagesLocation).trigger('gotVisitorLocation');
            },
            function() {
              $Core.PagesLocation.getLocationWithoutHtml5();
            });
      }
      else {
        $Core.PagesLocation.getLocationWithoutHtml5();
      }
    }

    $('#js_add_location_suggestions').
        css({'max-height': '150px', 'overflow-y': 'auto'});
    $($Core.PagesLocation).trigger('mapCreated');
  },

  updateLatLngField: function(lat, lng) {
    if ($('#txt_location_latlng').val() == '') {
      $('#txt_location_latlng').val(lat + ',' + lng);
    }
  },

  /* Ready the input for the search */
  createSearch: function() {
    if ($.isEmptyObject($Core.PagesLocation.gMap)) {
      return;
    }
    $Core.PagesLocation.gSearch = new google.maps.places.PlacesService(
        $Core.PagesLocation.gMap);

    /* Prepare the input field so the user can type in locations */
    $('#txt_location_name').on('keyup', function() {
      var sName = $(this).val();
      if (sName.length < 3 || sName == $Core.PagesLocation.sLastName) {
        $('#js_add_location_suggestions').hide();
        return;
      }
      $Core.PagesLocation.sLastName = sName;
      $Core.PagesLocation.gSearch.nearbySearch
      (
          {
            location: $Core.PagesLocation.gMyLatLng,
            radius: 6000,
            keyword: sName,
          },
          function(results, status) {
            if (status == google.maps.places.PlacesServiceStatus.OK) {
              $Core.PagesLocation.aPlaces = results;
              $Core.PagesLocation.displaySuggestions();
            }
          }
      );
    });
  },

  createMap: function() {
    var oMapOptions = {
          zoom: 13,
          mapTypeId: google.maps.MapTypeId.ROADMAP,
          center: $Core.PagesLocation.gMyLatLng,
        },
        map = $('#' + $Core.PagesLocation.sMapId);

    $Core.PagesLocation.gMap = new google.maps.Map(
        document.getElementById($Core.PagesLocation.sMapId), oMapOptions);
    $Core.PagesLocation.gSearch = new google.maps.places.PlacesService(
        $Core.PagesLocation.gMap);

    var height;
    if ($('#main').hasClass('empty-right') || $('#main').hasClass('empty-left')) {
      height = '300px';
    } else {
      height = '250px';
    }
    map.css({height: height, width: '100%', display: 'block'});

    google.maps.event.trigger($Core.PagesLocation.gMap, 'resize');

    /* Build the marker */
    $Core.PagesLocation.gMarker = new google.maps.Marker({
      map: $Core.PagesLocation.gMap,
      position: $Core.PagesLocation.gMyLatLng,
      draggable: true,
      animation: google.maps.Animation.DROP,
    });
    $Core.PagesLocation.gMap.panTo($Core.PagesLocation.gMyLatLng);

    /* Now attach an event for the marker */
    google.maps.event.addListener($Core.PagesLocation.gMarker, 'mouseup',
        function() {
          /* Refresh gMyLatLng*/
          $Core.PagesLocation.gMyLatLng = new google.maps.LatLng(
              $Core.PagesLocation.gMarker.getPosition().lat(),
              $Core.PagesLocation.gMarker.getPosition().lng());

          /* Refresh the hidden input */
          $('#txt_location_latlng').
              val($Core.PagesLocation.gMyLatLng.lat() + ',' +
                  $Core.PagesLocation.gMyLatLng.lng());

          /* Center the map */
          $Core.PagesLocation.gMap.panTo($Core.PagesLocation.gMyLatLng);

          /* Get the establishments near the new location */
          $Core.PagesLocation.getEstablishments(
              $Core.PagesLocation.displaySuggestions);
        });
    $($Core.PagesLocation).trigger('mapCreated');
    $($Core.PagesLocation.gMarker).trigger('mouseup');
  },

  getEstablishments: function(oObj) {
    $Core.PagesLocation.gSearch.nearbySearch({
      location: $Core.PagesLocation.gMyLatLng,
      radius: '500',
    }, function(aResults, iStatus) {
      if (iStatus == google.maps.places.PlacesServiceStatus.OK) {
        $Core.PagesLocation.aPlaces = aResults;
        if (typeof oObj == 'function') {
          oObj();
        }
        $($Core.PagesLocation).trigger('gotEstablishments');
      }
    });

  },

  displaySuggestions: function() {
    var sOut = '';
    $Core.PagesLocation.aPlaces.map(function(oPlace) {
      sOut += '<div class="js_div_place" onmouseover="$Core.PagesLocation.hintPlace(\'' +
          oPlace['id'] +
          '\');" onclick="$Core.PagesLocation.chooseLocation(\'' +
          oPlace['id'] + '\');">';
      sOut += '<div class="js_div_place_name">' + oPlace['name'] + '</div>';
      if (typeof oPlace['vicinity'] != 'undefined') {
        sOut += '<div class="js_div_place_vicinity">, ' +
            oPlace['vicinity'] + '</div>';
      }
      sOut += '</div>';
    });

    $('#js_add_location_suggestions').
        html(sOut).
        css({'z-index': 900, 'max-height': '150px'}).
        show();
  },

  hintPlace: function(sId) {
    $Core.PagesLocation.aPlaces.map(function(oPlace) {
      if (oPlace.id == sId) {
        $Core.PagesLocation.gMap.panTo(oPlace['geometry']['location']);
        $Core.PagesLocation.gMarker.setPosition(
            oPlace['geometry']['location']);
      }
    });
  },

  chooseLocation: function(sId) {
    $Core.PagesLocation.aPlaces.map(function(oPlace) {
      if (oPlace.id == sId) {
        $('#txt_location_name').val(oPlace.name);
        $('#txt_location_latlng').
            val(oPlace.geometry.location.lat() + ',' +
                oPlace.geometry.location.lng());
        $('#js_add_location_suggestions').hide();
      }
    });
  },

  getLocationWithoutHtml5: function() {
    /* Get visitor's city  */
    var sCookieLocation = getCookie('core_places_location');
    if (sCookieLocation != null) {
      var aLocation = sCookieLocation.split(',');
      $Core.PagesLocation.gMyLatLng = new google.maps.LatLng(aLocation[0],
          aLocation[1]);
      $($Core.PagesLocation).trigger('gotVisitorLocation');
    }
    else {
      $.ajaxCall('pages.getMyCity');
    }
  },
};

$Core.Pages.Claim = {
  approve: function(iClaimId) {
    $Core.jsConfirm(
        {message: oTranslations['are_you_sure_you_want_to_transfer_ownership']},
        function() {
          $.ajaxCall('pages.approveClaim', 'claim_id=' + iClaimId);
        }, function() {
        });
  },

  deny: function(iClaimId) {
    $Core.jsConfirm(
        {message: oTranslations['are_you_sure_you_want_to_deny_this_claim_request']},
        function() {
          $.ajaxCall('pages.denyClaim', 'claim_id=' + iClaimId);
        }, function() {
        });
  },
};

$(document).ready(function() {
  if (Core_Pages.checkVal()) {
    $('#is_group').hide();
  }
  Core_Pages.readyAdd();
});

$Behavior.crop_pages_image_photo = function() {
  if (Core_Pages.cropmeImgSrc == '') {
    return;
  }
  $('.image-editor').cropit({
    imageState: {
      src: Core_Pages.cropmeImgSrc,
    },
    smallImage: 'allow',
    maxZoom: 2,
  });

  $('.rotate-cw').click(function() {
    $('.image-editor').cropit('rotateCW');
  });
  $('.rotate-ccw').click(function() {
    $('.image-editor').cropit('rotateCCW');
  });

  $('.export').click(function() {
    var imageData = $('.image-editor').cropit('export');
    window.open(imageData);
  });
};

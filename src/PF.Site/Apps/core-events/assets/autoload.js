$Ready(function () {
    $(document).on('click', '[data-toggle="event_rsvp"]', function () {
        var element = $(this),
            container = element.closest('.open'),
            input = container.find('input:first'),
            button = container.find('[data-toggle="dropdown"]'),
            event_id = element.data('event-id'),
            rel = element.attr('rel');

        container.removeClass('is_attending');
        container.find('.is_active_image').removeClass('is_active_image');
        element.addClass('is_active_image');

        var $sContent = element.html();
        $sContent = '<i class="ico ico-check"></i>&nbsp;' + $sContent;
        //check attending option is selected
        if (rel == 1) {
            container.addClass('is_attending');
        }

        $.ajaxCall('event.addRsvp', 'id=' + event_id + '&inline=1' + '&rsvp=' + rel);
        button.find('span.txt-label').html($sContent);
    });
});


$Behavior.addNewEvent = function () {
    $('.js_event_change_group').click(function () {
        if ($(this).parent().hasClass('locked')) {
            return false;
        }

        aParts = explode('#', this.href);

        $('.js_event_block').hide();
        $('#js_event_block_' + aParts[1]).show();
        $(this).parents('.header_bar_menu:first').find('li').removeClass('active');
        $(this).parent().addClass('active');
        $('#js_event_add_action').val(aParts[1]);
    });

    $('.js_mp_category_list').change(function () {
        var iParentId = parseInt(this.id.replace('js_mp_id_', ''));

        $('.js_mp_category_list').each(function () {
            if (parseInt(this.id.replace('js_mp_id_', '')) > iParentId) {
                $('#js_mp_holder_' + this.id.replace('js_mp_id_', '')).hide();

                this.value = '';
            }
        });

        $('#js_mp_holder_' + $(this).val()).show();
    });
};


$Core.event =
    {
        sUrl: '',

        url: function (sUrl) {
            this.sUrl = sUrl;
        },

        action: function (oObj, sAction) {
            aParams = $.getParams(oObj.href);

            $('.dropContent').hide();

            switch (sAction) {
                case 'edit':
                    window.location.href = this.sUrl + 'add/id_' + aParams['id'] + '/';
                    break;
                case 'delete':
                    var url = this.sUrl;
                    $Core.jsConfirm({}, function () {
                        window.location.href = url + 'delete_' + aParams['id'] + '/';
                    }, function () {
                    });
                    break;
                default:

                    break;
            }

            return false;
        },

        deleteImage: function (iEventId) {
            $Core.jsConfirm({message: oTranslations['are_you_sure']}, function () {
                $.ajaxCall('event.deleteImage', 'id=' + iEventId);
            }, function () {

            });
            return false;
        },
        deleteEvent: function (ele) {

            if (!ele.data('id')) return false;

            $Core.jsConfirm({message: ele.data('message')}, function () {
                $.ajaxCall('event.delete', 'id=' + ele.data('id') + '&is_detail=' + ele.data('is-detail'));
            }, function () {
            });

            return false;
        },
    };


$Behavior.initViewEvent = function () {
    var bDisable = true;
    if ($('.js_event_rsvp:checked').length < 1) {
        $('#btn_rsvp_submit').attr('disabled', 'disabled');
        $('.js_event_rsvp').click(function () {
            $('#btn_rsvp_submit').removeAttr('disabled', '');
        });
    }
};

//Map JS

var oMarker;
var oGeoCoder;
var sQueryAddress;
var oMap;
var oLatLng;
var bDoTrigger = false;
/* This function takes the information from the input fields and moves the map towards that location*/
function inputToMap() {
    var sQueryAddress = $('#address').val() + ' ' + $('#postal_code').val() + ' ' + $('#city').val();
    if ($('#js_country_child_id_value option:selected').val() > 0) {
        sQueryAddress += ' ' + $('#js_country_child_id_value option:selected').text();

        //$.ajaxCall('core.getChildre','country_iso=' + $('#country_iso option:selected').val());
    }
    sQueryAddress += ' ' + $('#country_iso option:selected').text();
    debug('Searching for: ' + sQueryAddress);
    oGeoCoder.geocode({
            'address': sQueryAddress
        }, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                oLatLng = new google.maps.LatLng(results[0].geometry.location.lat(), results[0].geometry.location.lng());
                oMarker.setPosition(oLatLng);
                oMap.panTo(oLatLng);
                $('#input_gmap_latitude').val(oMarker.position.lat());
                $('#input_gmap_longitude').val(oMarker.position.lng());
            }
        }
    );
    if (bDoTrigger) {
        google.maps.event.trigger(oMarker, 'dragend');
        bDoTrigger = false;
    }
}

function initialize() {
    if (typeof(aInfo) == 'undefined') return;
    oGeoCoder = new google.maps.Geocoder();
    oLatLng = new google.maps.LatLng(aInfo.latitude, aInfo.longitude);

    var myOptions = {
        zoom: 11,
        center: oLatLng,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        mapTypeControl: false,
        streetViewControl: false
    };
    oMap = new google.maps.Map(document.getElementById("mapHolder"), myOptions);
    oMarker = new google.maps.Marker({
        draggable: true,
        position: oLatLng,
        map: oMap
    });


    /* Fake the dragend to populate the city and other input fields */
    google.maps.event.trigger(oMarker, 'dragstart');
    google.maps.event.trigger(oMarker, 'dragend');
    google.maps.event.addListener(oMarker, "dragend", function () {
        debug('drag end');
        $('#input_gmap_latitude').val(oMarker.position.lat());
        $('#input_gmap_longitude').val(oMarker.position.lng());
        oLatLng = new google.maps.LatLng(oMarker.position.lat(), oMarker.position.lng());
        oGeoCoder.geocode({
                'latLng': oLatLng
            },
            function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    $('#city').val('');
                    $('#postal_code').val('');
                    //debug (results[0]);
                    for (var i in results[0]['address_components']) {
                        if (results[0]['address_components'][i]['types'][0] == 'locality') {
                            $('#city').val(results[0]['address_components'][i]['long_name']);
                        }
                        if (results[0]['address_components'][i]['types'][0] == 'country') {
                            var sCountry = $('#country_iso option:selected').val();
                            $('#js_country_iso_option_' + results[0]['address_components'][i]['short_name']).attr('selected', 'selected');
                            if (sCountry != $('#country_iso option:selected').val()) {
                                $('#country_iso').change();
                            }
                        }
                        if (results[0]['address_components'][i]['types'][0] == 'postal_code') {
                            $('#postal_code').val(results[0]['address_components'][i]['long_name']);
                        }
                        if (results[0]['address_components'][i]['types'][0] == 'street_address') {
                            $('#address').val(results[0]['address_components'][i]['long_name']);
                        }
                        if (isset($('#js_country_child_id_value')) && results[0]['address_components'][i]['types'][0] == 'administrative_area_level_1') {
                            $('#js_country_child_id_value option').each(function () {
                                if ($(this).text() == results[0]['address_components'][i]['long_name']) {
                                    $(this).attr('selected', 'selected');
                                    bHasChanged = true;
                                }
                            });
                        }
                    }
                }
            });
    });
    /* Sets events for when the user inputs info */
    inputToMap();
}

function loadScript() {
    sAddr = 'http://';
    if (window.location.protocol == "https:") {
        sAddr = 'https://';
    }
    var script = document.createElement('script');
    script.type = 'text/javascript';
    script.src = sAddr + 'maps.google.com/maps/api/js?sensor=false&callback=initialize';
    document.body.appendChild(script);
}


$(document).ready(function () {
    $('#js_country_child_id_value').change(function () {
        debug("Cleaning  city, postal_code and address");
        $('#city').val('');
        $('#postal_code').val('');
        $('#address').val('');
    });
    $('#country_iso, #js_country_child_id_value').change(inputToMap);
    $('#address, #postal_code, #city').blur(inputToMap);
    // loadScript();
});

var core_events_onchangeDeleteCategoryType = function (type) {
    if (type == 2)
        $('#category_select').show();
    else
        $('#category_select').hide();
};
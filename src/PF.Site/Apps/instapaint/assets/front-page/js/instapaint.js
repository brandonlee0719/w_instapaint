var timer = null;

function errorHandle(xhr, status, error) {
  "use strict";
  console.log(xhr);
  console.log(status);
  console.log(error);
  // load the modal
  // LoadIssueModal();
}

function ajax(url, input, dataType) {
  "use strict";
  console.log(baseUrl + url);
  return $.ajax({
    type: 'POST',
    url: baseUrl + url,
    data: input,
    dataType: dataType ? text : 'text',
    cache: false,
    error: function (xhr, status, error) {
      errorHandle(xhr, status, error);
      if (xhr.getResponseHeader(tokenName)) token = xhr.getResponseHeader(tokenName);
    }
  }).done(function (response, textStatus, xhr) {
    if (xhr.getResponseHeader(tokenName)) token = xhr.getResponseHeader(tokenName);
  });
}

function doAjaxLogin() {
  var $modal = $('.modal.in');
  $.ajax({
    type: 'post',
    url: '/auth/ajaxLoginAction',
    data: {
      'login_string': $modal.find('#login_string').val(),
      'login_pass': $modal.find('#login_pass').val(),
      'login_token': $modal.find('[name="login_token"]').val()
    },
    dataType: 'json',
    cache: false
  }).done(function (response) {
    if (response.status === 1) {
      $modal.modal('hide');
    } else {
      $modal.find('#login-error').show();
      $modal.find('[name="login_token"]').val(response.token);
    }
  });
}

function selectAll(parent) {
  "use strict";
  var i = 0,
    checkboxes = document.getElementsByName('check_id[]');
  for (i in checkboxes) {
    checkboxes[i].checked = parent.checked;
  }
}

function fileUpload(url, input) {
  "use strict";
  return $.ajax({
    type: 'POST',
    url: baseUrl + url,
    data: input,
    processData: false,
    contentType: false,
    error: function (xhr, status, error) {
      errorHandle(xhr, status, error);
      if (xhr.getResponseHeader(tokenName)) token = xhr.getResponseHeader(tokenName);
    }
  }).done(function (response, textStatus, xhr) {
    if (xhr.getResponseHeader(tokenName)) token = xhr.getResponseHeader(tokenName);
  });
}

function notify(type, content) {
  var $notify = $('#notify');

  $notify.removeClass('animate');
  $notify.addClass(type);

  stop();

  $notify.find('#notify-content').html(content);

  setTimeout(function () {
    $notify.addClass('animate');
    $notify.removeClass(type);
  }, 50);

  $notify.addClass('open');

  start($notify);
}

function stop() {
  if (timer) {
    clearTimeout(timer);
    timer = null;
  }
}

function start($notify) {
  timer = setTimeout(function () {
    $notify.removeClass('animate');
    $notify.removeClass('new');
    $notify.removeClass('open');
    $notify.find('#notify-content').html('');
  }, 5000);
}

function debounce(fn, delay) {
  var timer = null;
  return function () {
    var context = this, args = arguments;
    clearTimeout(timer);
    timer = setTimeout(function () {
      fn.apply(context, args);
    }, delay);
  };
}

function modalLoad(title, data, buttons) {
  "use strict";
  var footer = '',
    submit_id = '',
    submit_class = '',
    submit_click = '',
    cancel_class = '',
    cancel_id = '',
    cancel_dismiss = '',
    cancel_click = '',
    $modal = $('#global-modal');

  $modal.find('.modal-body').html('');

  if (buttons.prefix) {
    footer += (buttons.prefix + ' ');
  }

  if (buttons.cancel) {
    if (buttons.cancel.id) {
      cancel_id = 'id="' + buttons.cancel.id + '" ';
    }
    if (buttons.cancel.class) {
      cancel_class = ' ' + buttons.cancel.class;
    }
    if (buttons.cancel.onclick) {
      cancel_click = ' onclick="' + buttons.cancel.onclick + '"';
    }

    cancel_dismiss = buttons.cancel.dismiss === false ? '' : 'data-dismiss="modal"';
    // footer += ('<button class="btn btn-default" data-dismiss="modal">' + ucfirst(buttons.cancel.text) + '</button>');
    footer += ('<button ' + cancel_id + cancel_click + 'class="btn btn-default' + cancel_class + '" ' + cancel_dismiss + '>' + ucfirst(buttons.cancel.text) + '</button>');
  }

  if (data.size) {
    $modal.find('.modal-dialog').addClass(data.size);
  } else {
    $modal.find('.modal-dialog').removeClass('modal-lg');
    $modal.find('.modal-dialog').removeClass('modal-sm');
  }

  if (buttons.submit) {
    if (buttons.submit.id) {
      submit_id = 'id="' + buttons.submit.id + '" ';
    }
    if (buttons.submit.class) {
      submit_class = ' ' + buttons.submit.class;
    }
    if (buttons.submit.onclick) {
      submit_click = ' onclick="' + buttons.submit.onclick + '"';
    }
    footer += ('<button ' + submit_id + submit_click + 'class="btn btn-primary' + submit_class + '">' + ucfirst(buttons.submit.text) + '</button>');
  }

  return ajax('modal/get', data)
    .done(function (result) {
      $modal.find('.modal-title').html(title);
      if (result !== undefined && result !== '') {
        $modal.find('.modal-body').html(result);
        $modal.find('.modal-footer').html(footer);
      } else {
        $modal.find('.modal-body').html('Modal template not found.');
      }
    })
    .fail(function (xhr, status, message) {
      $modal.find('.modal-title').html(status);
      $modal.find('.modal-footer').html('<button class="btn btn-default" data-dismiss="modal">Dismiss</button>');
    })
    .always(function (response) {
      $modal.modal('show');
    });
}

function LoadIssueModal() {
  var $modal = $('#global-modal'),
    title = 'Oops! There was an issue while processing your request',
    template = {
      module: 'client',
      template: 'report_issue'
    },
    buttons = {
      submit: {
        id: 'btn-submitIssue',
        text: 'submit'
      }
    };
  modalLoad(title, template, buttons)
}

function stripEmpty(data) {
  'use strict';

  if (data === undefined) {
    return;
  }

  var data_cleaned = [],
    ilen1 = data.length,
    ilen2 = 0,
    i1 = 0,
    i2 = 0,
    row = [];
  for (i1 = 0; i1 < ilen1; i1++) {
    if (data[i1] !== null) {
      row = data[i1];
      ilen2 = row.length;
      for (i2 = 0; i2 < ilen2; i2++) {
        if (row[i2] !== null && row[i2] !== '') {
          data_cleaned.push(row);
          break;
        }
      }
    }
  }
  if (data_cleaned.length !== 0) {
    return data_cleaned;
  } else {
    return false;
  }
}

function ucfirst(string) {
  return string.charAt(0).toUpperCase() + string.substr(1);
}

$(document).ready(function () {
  $('#notify-close').on('click', function () {
    $(this).closest('#notify').removeClass('open');
  });

  $('#notify-body').on('mouseover', function () {
    stop();
  });

  $('#notify-body').on('mouseleave', function () {
    start($(this).closest('#notify'));
  });
});


function checkUrl(url) {
  if (url.match(/\./)) {
    return true;
  }
  return false;
}

function readURL(input, preview) {

  if (input.files && input.files[0]) {
      var reader = new FileReader();

      reader.onload = function (e) {
          $(preview).attr('src', e.target.result);
      }

      reader.readAsDataURL(input.files[0]);
  }
}
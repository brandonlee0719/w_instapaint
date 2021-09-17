var oProgressBar = {};

var sImages = "",
    sCurrentProgressLocation = "",
    bIsHTML5ProgressUpload = false;
if (window.FormData !== undefined) {
    bIsHTML5ProgressUpload = true;
}
$Core.loadStaticFile(getParam('sProgressCssFile'));

/**
 * Function is called when the upload is complete.
 */
function completeProgress() {
    // Check if we have a plug-in
    if (function_exists('plugin_completeProgress')) {
        plugin_completeProgress();
    }
    var html = '<a href="javascript:void(0)" role="button" class="btn btn-primary photo-upload-button" onclick="showUploadForm();"><i class="fa fa-upload"></i> ' + oTranslations['upload_more'] + '</a>';
    if (hasErrors === 0) {
        // 1. upload successfully and redirect to (edit || detail) page
        window.location.href = sCurrentProgressLocation + sImages;
        return;
    } else if (hasUploaded > 0) {
        // 2. both error and uploaded
        // we have 2 button: upload more, (go to edit page || go to detail page)
        var editPageBtn = '<a href="'+ sCurrentProgressLocation + sImages +'" role="button" class="btn btn-primary photo-upload-button"><i class="fa fa-edit"></i>  ' + oTranslations['edit_photos'] + '</a>',
            detailPageBtn = '<a href="'+ sCurrentProgressLocation +'" role="button" class="btn btn-primary photo-upload-button"><i class="fa fa-sign-out"></i> ' + oTranslations['go_to_detail'] + '</a>';
        if (typeof edit_after_upload !== 'undefined') {
            html += editPageBtn;
        } else {
            html += detailPageBtn;
        }
    }
    else {
        // 3. have no photo uploaded
        // only button upload again
        html = '<a href="javascript:void(0)" role="button" class="btn btn-primary photo-upload-button" onclick="showUploadForm();"><i class="fa fa-refresh"></i> ' + oTranslations['upload_again'] + '</a>';
    }
    $('#js_progress_cache_loader').before(html);
}

function showUploadForm() {
    $('.js_tmp_upload_bar').remove();
    $(oProgressBar['holder']).show();
    $Core.progressBarInit();
    $('.photo-upload-button').hide();
}

function startProcess(bForm, bForceImage) {

}

function getProgress(sProgressKey) {

}

function startProgress(sProgressKey) {

}

var iNewInputBars = 0;

function addMoreToProgressBar() {
    iNewInputBars++;
    if (oProgressBar['add_more'] == false) {
        return false;
    }

    if ((iNewInputBars + oProgressBar['total_image']) >= oProgressBar['max_upload']) {
        iNewInputBars--;
        return false;
    }

    $('.js_uploader_files_input').each(function () {
        if (empty(this.value)) {
            iNewInputBars--;
            $(this).parent().remove();
        }
    });
    var sAccept = '';
    if (oProgressBar['file_id'] == "image" || oProgressBar['file_id'] == "image[]") {
        sAccept = ' accept="image/*" ';
    } else if (oProgressBar['file_id'] == "mp3") {
        sAccept = ' accept="audio/*" ';
    }
    $('#js_uploader_files_outer').append('<div class="js_uploader_files" id="js_new_add_input_' + iNewInputBars + '"><input ' + sAccept + ' type="file" name="' + oProgressBar['file_id'] + '" class="js_uploader_files_input" size="30" onchange="addMoreToProgressBar();" /></div>' + "\n");

    return false;
}

function removeMoreToProgressBar(iId) {

}

var iTotalImagesToBeUploaded = 0;
var iTotalUploadedFiles = 0;
var hasUploaded = 0;
var hasErrors = 0;

$Core.progressBarInit = function () {
    if (!isset(oProgressBar['html5upload']) || (isset(oProgressBar['html5upload']) && !oProgressBar['html5upload'])) {
        bIsHTML5ProgressUpload = false;
    }
    p('__LOADING_IMAGE_UPLOADER__');
    if ($(oProgressBar['uploader']).length > 0) {
        $(oProgressBar['progress_id']).html('<div id="js_progress_outer" style="width:300px;"><div id="js_progress_inner"><span id="js_progress_percent_value">0</span>/100%</div></div>');
        sInput = '<div id="js_uploader_files_outer">';
        if (bIsHTML5ProgressUpload) {
            oProgressBar['total'] = 1;
        }
        for (i = 1; i <= oProgressBar['total']; i++) {
            var sAccept = '';
            if (oProgressBar['file_id'] == "image" || oProgressBar['file_id'] == "image[]") {
                sAccept = ' accept="image/*" ';
            } else if (oProgressBar['file_id'] == "mp3") {
                sAccept = ' accept="audio/*" ';
            }
            sInput += '<div class="js_uploader_files"><input ' + (bIsHTML5ProgressUpload ? 'multiple="multiple"' : '') + sAccept + ' type="file" name="' + oProgressBar['file_id'] + '" class="js_uploader_files_input" size="30" ' + (bIsHTML5ProgressUpload ? '' : 'onchange="addMoreToProgressBar();"') + ' /></div>' + "\n";
        }
        sInput += '</div>';

        var iDivHeight = $(oProgressBar['holder']).innerHeight();

        $(oProgressBar['holder']).after('<div id="js_progress_cache_loader" style="height:' + (iDivHeight <= 0 ? '200' : iDivHeight) + 'px; display:none;"></div>');

        if (isset(oProgressBar['frame_id'])) {
            sInput += '<iframe id="' + oProgressBar['frame_id'] + '" name="' + oProgressBar['frame_id'] + '" height="500" width="500" frameborder="1" style="display:none;"></iframe>';
        }

        $(oProgressBar['uploader']).html(sInput);

        if (bIsHTML5ProgressUpload) {
            $('.js_uploader_files_input')[0].addEventListener("change", function (e) {
                iTotalImagesToBeUploaded = 0;
                hasUploaded = 0;
                hasErrors = 0;

                $(oProgressBar['holder']).hide();
                $('html, body').animate({
                    scrollTop: $(oProgressBar['uploader']).scrollTop()
                });
                var files = e.target.files || e.dataTransfer.files;
                iTotalUploadedFiles = files.length;
                if (iTotalUploadedFiles > oProgressBar.max_upload) {
                    $('#js_upload_error_message').html('<div class="alert alert-danger">' + oTranslations['maximum_number_of_images_you_can_upload_each_time_is'] + ' ' + oProgressBar.max_upload + '</div>').show();
                    setTimeout(function () {
                        $('#js_upload_error_message').html('');
                        $(oProgressBar['holder']).show();
                        $Core.progressBarInit();
                    }, 3000);
                } else {
                    for (var i = 0, f; f = files[i]; i++) {
                        if (i >= oProgressBar['max_upload']) {
                            break;
                        }
                        if (isset(oProgressBar['valid_file_ext'])) {
                            sExt = f.name.split('.').pop().toLowerCase();
                            if ($.inArray(sExt, oProgressBar['valid_file_ext']) == -1) {
                                sExts = '';
                                for (iExt in oProgressBar['valid_file_ext']) {
                                    if (iExt > 0) {
                                        sExts += ', ';
                                    }
                                    sExts += oProgressBar['valid_file_ext'][iExt];
                                }
                                hasErrors++;
                                $('#js_upload_error_message').html('<div class="alert alert-danger">' + oTranslations['not_a_valid_file_extension_we_only_allow_ext'].replace('{ext}', sExts) + '</div>').show();
                                continue;
                            }
                        }
                        ParseFile(f, i);
                        UploadFile(f, i);
                    }
                }
            }, false);
        }
    }
};

function ParseFile(file, iCnt) {

    $(oProgressBar['holder']).after("<div id=\"js_tmp_upload_" + iCnt + "\" class=\"js_tmp_upload_bar\"><div class=\"js_tmp_upload_bar_content\" title=\"" + file.name + "\">" + file.name + "</div><div class=\"js_tmp_upload_bar_upload\"></div></div>").hide();
    if (file.type.indexOf("image") == 0) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#js_tmp_upload_' + iCnt).prepend('<div class="js_temp_photo_holder"><img src="' + e.target.result + '" style="max-width:25px; max-height:25px;" /></div>');
        }
        reader.readAsDataURL(file);
    }
};

function UploadFile(file, iCnt) {
    $('div#js_upload_error_message').html('');
    var data = new FormData();
    data.append('ajax_upload', file);
    $.ajax({
        xhr: function () {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function (e) {
                var pc = parseInt((e.loaded / e.total * 100));
                $('#js_tmp_upload_' + iCnt + '').find('.js_tmp_upload_bar_upload').width(pc + '%').show();
                if (pc === 100 && iTotalImagesToBeUploaded === (iCnt + 1)) {

                }
            }, false);

            return xhr;
        },
        url: $(oProgressBar['holder']).find('form').attr('action'),
        data: data,
        cache: false,
        contentType: false,
        processData: false,
        headers: {
            'X-FileName': encodeURI(file.name),
            'X-File-Size': file.size,
            'X-File-Type': file.type,
            'X-Post-Form': $(oProgressBar['holder']).find('form').getForm()
        },
        type: 'POST',
        error: function (error) {
            var eJson = {};
            if (typeof(error.responseJSON) !== 'undefined')
                eJson = error.responseJSON;
            $('#js_tmp_upload_' + iCnt + '').addClass('has_failed');
            if(typeof eJson.upload_error_message !== 'undefined' && eJson.upload_error_message != '')
                var ele = $('#js_tmp_upload_' + iCnt + '').find('.js_tmp_upload_bar_content');
                ele.append('. ' + eJson.upload_error_message);
                ele.attr('title', ele.text());
            hasErrors++;
            if ((hasUploaded + hasErrors) == iTotalUploadedFiles)
                completeProgress();
        },
        success: function (data) {
            eval(data);
        }
    });
};

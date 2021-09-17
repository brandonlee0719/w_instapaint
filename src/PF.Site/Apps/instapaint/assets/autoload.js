
$Ready(function() {

});

$Core.Instapaint = {
    dropzoneOnSuccess : function (dropZone, file, info) {

        $fileId = JSON.parse(info).file;

        $('#temp-file-input').val($fileId);

        var reader  = new FileReader();
        reader.onloadend = function () {
            $('#photo-preview').find('img').attr('src', reader.result);
        }

        if (file) {
            reader.readAsDataURL(file);
        }

        $('#photo-preview').css('display', 'block');
        //$('#photo-preview').find('img').attr('src', dropZone.find('.dz-image').find('img').attr('src'));
    },
    dropzoneOnError : function () {

        $('#temp-file-input').val('');
        $('#photo-preview').css('display', 'none');
    },
    dropzoneOnRemovedFile : function () {

        $('#temp-file-input').val('');
        $('#photo-preview').css('display', 'none');
    }
}

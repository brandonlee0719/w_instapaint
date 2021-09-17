<?php
echo '<script type="text/javascript"> 
$Behavior.CKEditor_forum_submit_reply = function() {
    $(\'#js_forum_form_submit_btn\').on(\'click\', function () {
        CKEDITOR.instances.text.updateElement();
    });
    
    $(\'#js_forum_form_submit_btn\').on(\'submit\', function () {
        CKEDITOR.instances.text.updateElement();
    });
}
</script>';

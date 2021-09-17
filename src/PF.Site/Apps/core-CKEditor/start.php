<?php
route('/ckeditor/admincp', function() {
    auth()->isAdmin(true);

    return view('admincp.html', [
        'link' => \Phpfox_Url::instance()->makeUrl('admincp.setting.edit', array('module-id' => 'core#allow_html'))
    ]);
});

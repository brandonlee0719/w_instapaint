<?php
if (\Phpfox::getUserBy('profile_page_id')) {
    if (!isset($sActualFile)) {
        $sActualFile = Phpfox::getLib('image.helper')->display(array(
                'server_id' => $aPhoto['server_id'],
                'path' => 'photo.url_photo',
                'file' => $aPhoto['destination'],
                'suffix' => '_1024',
                'return_url' => true
            )
        );
    }
    \Phpfox::getService('pages.process')->setProfilePicture($sActualFile);
}

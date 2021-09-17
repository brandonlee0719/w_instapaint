<?php
defined('PHPFOX') or exit('NO DICE');

/**
 * @author Neil J. <neil@phpfox.com>
 *
 * Class User_Component_Block_Profile_Photo
 */
class User_Component_Block_Profile_Photo extends Phpfox_Component
{
    public function process()
    {
        $aUser = Phpfox::getService('user')->getUser(Phpfox::getUserId());

        $sFileName = Phpfox::getUserBy('user_image');
        if (!empty($aUser['user_image'])) {
            $sImage = Phpfox_Image_Helper::instance()->display(array(
                    'server_id' => Phpfox::getUserBy('server_id'),
                    'title' => Phpfox::getUserBy('full_name'),
                    'path' => 'core.url_user',
                    'file' => $sFileName,
                    'suffix' => '',
                    'no_default' => true,
                    'return_url' => true,
                )
            );

            $this->template()->assign(array(
                    'sProfileImage' => $sImage,
                )
            );
        }


        $this->template()->assign([
            'aUser' => $aUser,
            'iMaxFileSize' => (Phpfox::getUserParam('user.max_upload_size_profile_photo') === 0 ? null : ((Phpfox::getUserParam('user.max_upload_size_profile_photo') / 1024) * 1048576)),
        ]);
        return 'block';
    }
}

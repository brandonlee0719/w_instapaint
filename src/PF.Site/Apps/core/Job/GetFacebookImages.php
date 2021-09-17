<?php
namespace Apps\Phpfox_Core\Job;

use Core\Queue\JobAbstract;
use Phpfox;

class GetFacebookImages extends JobAbstract
{
    /**
     * Perform a job item
     */
    public function perform()
    {
        $aParams = $this->getParams();
        $sImage = fox_get_contents("https://graph.facebook.com/" . $aParams['iFbId'] . "/picture?type=large");
        $sFileName = md5('user_avatar' . time()) . '.jpg';

        file_put_contents(Phpfox::getParam('core.dir_user') . $sFileName, $sImage);

        // check in case using cdn
        $aImage = (Phpfox::getService('user.process')->uploadImage($aParams['iUserId'], false, Phpfox::getParam('core.dir_user') . $sFileName));
        @unlink(Phpfox::getParam('core.dir_user') . $sFileName);

        count($aImage) && db()->update(':user', ['user_image' => $aImage['user_image']], ['user_id' => $aParams['iUserId']]);
    }
}
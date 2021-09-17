<?php
namespace Apps\phpFox_RESTful_API\Ajax;

use Phpfox_Ajax;

/**
 * Class Ajax
 *
 * @package Apps\phpFox_RESTful_API\Ajax
 */
class Ajax extends Phpfox_Ajax
{
    public function toggleClient(){
        $storage = \Phpfox::getService('restful_api.storage');
        $iClientId = $this->get('id');
        $iActive = $this->get('active');
        $storage->toggleClient($iClientId, $iActive);
    }
}
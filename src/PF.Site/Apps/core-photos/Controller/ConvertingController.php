<?php

namespace Apps\Core_Photos\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class ConvertingController extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isUser(true);
        $aImage = Phpfox::getService('photo')->getForConverting(Phpfox::getUserId());
        if (empty($aImage)) {
            die('aImage is empty and userId: ' . Phpfox::getUserId());
        }

        foreach ($aImage as $iKey => $aImg) {
            $aImage[$iKey]['completed'] = 'false';
            $aImage[$iKey]['picup'] = '1';
        }
        $sImage = urlencode(base64_encode(json_encode($aImage)));
        $this->template()
            ->setHeader(array(
                '<script type="text/javascript"> $Behavior.imageRun = function(){$.ajaxCall("photo.process", "photos=' . $sImage . '&action=picup"); } </script>"'

            ))
            ->setTemplate('blank');

    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('photo.component_controller_converting_clean')) ? eval($sPlugin) : false);
    }
}
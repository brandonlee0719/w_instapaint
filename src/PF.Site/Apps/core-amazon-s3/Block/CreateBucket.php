<?php

namespace Apps\PHPfox_AmazonS3\Block;

defined('PHPFOX' or exit('NO DICE'));

use Phpfox_Component;
use Apps\PHPfox_AmazonS3\Model;

class CreateBucket extends Phpfox_Component
{
    public function process()
    {
        $oClientS3 = new Model\AmazonS3();
        $aAllRegions = $oClientS3->getAllRegion();
        $this->template()->assign([
            'aAllRegions' => $aAllRegions
        ]);
    }
}

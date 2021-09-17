<?php

namespace Apps\PHPfox_AmazonS3\Controller\Admin;

use Admincp_Component_Controller_App_Index;
use Apps\PHPfox_AmazonS3\Model;
use Phpfox;

/**
 * Class ManageController
 * @author Neil J. <neil@phpfox.com>
 * @package Apps\PHPfox_AmazonS3\Controller\Admin
 */
class ManageController extends Admincp_Component_Controller_App_Index
{
    public function process()
    {
        $sAWsKey = setting('cdn_amazon_id');
        $sAWsSecret = setting('cdn_amazon_secret');
        $oS3 = new Model\AmazonS3($sAWsKey, $sAWsSecret);
        $this->template()->assign([
            'bIsValidKey' => $bIsValidKey = $oS3->isValidKey()
        ]);

        if ($aVals = $this->request()->getArray('val')) {
            $oS3->saveBucket($aVals['bucket']);
            Phpfox::addMessage('Bucket changed');
            Phpfox::getLib('url')->send('admincp.amazons3.manage');
        }
        if ($bIsValidKey) {
            $aBuckets = $oS3->getAllBucket();
            $this->template()->assign([
                'aBuckets' => $aBuckets,
            ]);
        }
    }
}

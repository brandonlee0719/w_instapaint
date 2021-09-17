<?php
namespace Apps\PHPfox_AmazonS3\Ajax;

defined('PHPFOX') or exit('NO DICE');

use Phpfox_Ajax;
use Phpfox;
use Apps\PHPfox_AmazonS3\Model;

/**
 * Class Ajax
 * @author Neil J. <neil@phpfox.com>
 *
 * @package Apps\PHPfox_AmazonS3\Ajax
 */
class Ajax extends Phpfox_Ajax
{
    public function createBucket()
    {
        Phpfox::getBlock('amazons3.createBucket');
    }

    public function processCreateBucket()
    {
        $aVals = $this->get('val');
        if (empty($aVals['bucket'])) {
            return \Phpfox_Error::set(_p("Bucket can not empty"));
        }
        $sAWsKey = setting('cdn_amazon_id');
        $sAWsSecret = setting('cdn_amazon_secret');
        $oClientS3 = new Model\AmazonS3($sAWsKey, $sAWsSecret);
        $sStatus = $oClientS3->createBucket($aVals['bucket'], $aVals['region']);
        if ($sStatus === true) {
            $oClientS3->saveBucket($aVals['bucket']);
            Phpfox::addMessage("Bucket is added successfully");
            $this->call("window.location.href='" . Phpfox::getLib('url')->makeUrl('admincp.amazons3.manage') . "';");
        } else {
            return \Phpfox_Error::set($sStatus);
        }
    }
}

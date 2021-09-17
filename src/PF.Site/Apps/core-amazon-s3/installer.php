<?php


$installer = new Core\App\Installer();
$installer->onInstall(function () use ($installer) {
    
    if (file_exists(__DIR__ . '/vendor/autoload.php')) {
        require_once __DIR__ . '/vendor/autoload.php';
    }

    $sAWsKey = Phpfox::getLib('database')->select('value_actual')
        ->from(':setting')
        ->where('module_id="amazons3" AND var_name="cdn_amazon_id"')
        ->executeField();

    $sAWsSecret = Phpfox::getLib('database')->select('value_actual')
        ->from(':setting')
        ->where('module_id="amazons3" AND var_name="cdn_amazon_secret"')
        ->executeField();

    $sBucketName = Phpfox::getLib('database')->select('value_actual')
        ->from(':setting')
        ->where('module_id="amazons3" AND var_name="cdn_bucket"')
        ->executeField();

    $sRegion = Phpfox::getLib('database')->select('value_actual')
        ->from(':setting')
        ->where('module_id="amazons3" AND var_name="cdn_region"')
        ->executeField();

    if (empty($sRegion) && !empty($sBucketName) && !empty($sAWsSecret) && !empty($sAWsKey)) {
        $oS3 = new Apps\PHPfox_AmazonS3\Model\AmazonS3($sAWsKey, $sAWsSecret);
        $oS3->saveBucket($sBucketName);
    }
});

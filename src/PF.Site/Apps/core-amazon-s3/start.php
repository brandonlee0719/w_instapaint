<?php

// Check if CDN is enabled
if (setting('cdn_enabled')) {
    // Attach an event to the CDN bootloader to our Model
    new Core\Event([
        'lib_phpfox_cdn' => 'Apps\PHPfox_AmazonS3\Model\CDN'
    ]);
}

\Phpfox_Module::instance()
    ->addComponentNames('controller', [
        'amazons3.admincp.manage' => Apps\PHPfox_AmazonS3\Controller\Admin\ManageController::class,
    ])
    ->addComponentNames('block', [
        'amazons3.createBucket' => Apps\PHPfox_AmazonS3\Block\CreateBucket::class,
    ])
    ->addComponentNames('ajax', [
        'amazons3.ajax' => Apps\PHPfox_AmazonS3\Ajax\Ajax::class,
    ])
    ->addTemplateDirs([
        'amazons3' => PHPFOX_DIR_SITE_APPS . 'core-amazon-s3' . PHPFOX_DS . 'views'
    ])
    ->addAliasNames('amazons3', 'PHPfox_AmazonS3');
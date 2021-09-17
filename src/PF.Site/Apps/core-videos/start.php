<?php

namespace Apps\PHPfox_Videos;

use Phpfox;
use Phpfox_Module;

Phpfox_Module::instance()
    ->addAliasNames('v', 'PHPfox_Videos')
    ->addServiceNames([
        'v.video' => Service\Video::class,
        'v.callback' => Service\Callback::class,
        'v.category' => Service\Category::class,
        'v.process' => Service\Process::class,
        'v.browse' => Service\Browse::class
    ])
    ->addTemplateDirs([
        'v' => PHPFOX_DIR_SITE_APPS . 'core-videos' . PHPFOX_DS . 'views',
    ])
    ->addComponentNames('controller', [
        'v.index' => Controller\IndexController::class,
        'v.profile' => Controller\ProfileController::class,
        'v.play' => Controller\PlayController::class,
        'v.share' => Controller\ShareController::class,
        'v.upload' => Controller\UploadController::class,
        'v.callback' => Controller\CallbackController::class,
        'v.edit' => Controller\EditController::class
    ])
    ->addComponentNames('controller', [
        'v.admincp.utilities' => Controller\Admin\UtilitiesController::class,
        'v.admincp.add-category' => Controller\Admin\AddCategoryController::class,
        'v.admincp.delete-category' => Controller\Admin\DeleteCategoryController::class,
        'v.admincp.category' => Controller\Admin\CategoryController::class,
        'v.admincp.convert' => Controller\Admin\ConvertController::class,
    ])
    ->addComponentNames('ajax', [
        'v.ajax' => Ajax\Ajax::class
    ])
    ->addComponentNames('block', [
        'v.category' => Block\Category::class,
        'v.add_category_list' => Block\AddCategoryList::class,
        'v.featured' => Block\Featured::class,
        'v.sponsored' => Block\Sponsored::class,
        'v.suggested' => Block\Suggested::class,
        'v.feed_video' => Block\FeedVideo::class
    ]);

group('/v', function () {

    // BackEnd routes
    route('/admincp', function () {
        auth()->isAdmin(true);
        Phpfox::getLib('module')->dispatch('v.admincp.category');

        return 'controller';
    });

    // FrontEnd routes
    route('/category/:id/:name/*', 'v.index')
        ->where([':id' => '([0-9]+)']);
    route('/play/:id/*', 'v.play');
    route('/', 'v.index');
    route('/share/', 'v.share');
    route('/url/', 'v.url');
    route('/upload/', 'v.upload');
    route('/callback/', 'v.callback');
    // old video route
    route('/manage/', 'v.edit');
    // new video route
    route('/edit/', 'v.edit');
    route('/delete/:delete', 'v.index')
        ->where([':delete' => '([0-9]+)']);
});

// Change url from /v => /video
group('/video', function () {
    // BackEnd routes
    route('/admincp/category/order', function () {
        auth()->isAdmin(true);
        $ids = request()->get('ids');
        $ids = trim($ids, ',');
        $ids = explode(',', $ids);
        $values = [];
        foreach ($ids as $key => $id) {
            $values[$id] = $key + 1;
        }
        Phpfox::getService('core.process')->updateOrdering([
                'table' => 'video_category',
                'key' => 'category_id',
                'values' => $values,
            ]
        );
        Phpfox::getLib('cache')->removeGroup('video');
        return true;
    });

    // FrontEnd routes
    route('/category/:id/:name/*', 'v.index')
        ->where([':id' => '([0-9]+)']);
    route('/play/:id/*', 'v.play');
    route('/', 'v.index');
    route('/share/', 'v.share');
    route('/url/', 'v.url');
    route('/upload/', 'v.upload');
    route('/callback/', 'v.callback');
    route('/edit/', 'v.edit');
    route('/delete/:delete', 'v.index')
        ->where([':delete' => '([0-9]+)']);
});

$sDefaultVideoPhoto = flavor()->active->default_photo('video_default_photo', true);
if (!$sDefaultVideoPhoto) {
    $sDefaultVideoPhoto = setting('core.path_actual') . 'PF.Site/Apps/core-videos/assets/images/default_thumbnail.png';
}
Phpfox::getLib('setting')->setParam('video.default_video_photo', $sDefaultVideoPhoto);
Phpfox::getLib('setting')->setParam('v.dir_image', PHPFOX_DIR_FILE . 'pic' . PHPFOX_DS . 'video' . PHPFOX_DS);
Phpfox::getLib('setting')->setParam('v.url_image', Phpfox::getParam('core.url_pic') . 'video/');

<?php
namespace Apps\Core_Marketplace;

use Phpfox;
use Phpfox_Module;

Phpfox_Module::instance()
    ->addAliasNames('marketplace', 'Core_Marketplace')
    ->addServiceNames([
        'marketplace.category' => Service\Category\Category::class,
        'marketplace.category.process' => Service\Category\Process::class,
        'marketplace' => Service\Marketplace::class,
        'marketplace.process' => Service\Process::class,
        'marketplace.callback' => Service\Callback::class,
        'marketplace.browse' => Service\Browse::class
    ])
    ->addTemplateDirs([
        'marketplace' => PHPFOX_DIR_SITE_APPS . 'core-marketplace' . PHPFOX_DS . 'views'
    ])
    ->addComponentNames('controller', [
        'marketplace.admincp.add' => Controller\Admin\AddController::class,
        'marketplace.admincp.index' => Controller\Admin\IndexController::class,
        'marketplace.admincp.delete' => Controller\Admin\DeleteController::class,
        'marketplace.invoice.index' => Controller\Invoice\IndexController::class,
        'marketplace.add' => Controller\AddController::class,
        'marketplace.index' => Controller\IndexController::class,
        'marketplace.profile' => Controller\ProfileController::class,
        'marketplace.purchase' => Controller\PurchaseController::class,
        'marketplace.view' => Controller\ViewController::class,
        'marketplace.frame-upload' => Controller\FrameUploadController::class
    ])
    ->addComponentNames('ajax', [
        'marketplace.ajax' => Ajax\Ajax::class
    ])
    ->addComponentNames('block', [
        'marketplace.category' => Block\CategoryBlock::class,
        'marketplace.featured' => Block\FeaturedBlock::class,
        'marketplace.feed' => Block\FeedBlock::class,
        'marketplace.info' => Block\InfoBlock::class,
        'marketplace.invite' => Block\InviteBlock::class,
        'marketplace.list' => Block\ListBlock::class,
        'marketplace.menu' => Block\MenuBlock::class,
        'marketplace.my' => Block\MyBlock::class,
        'marketplace.related' => Block\RelatedBlock::class,
        'marketplace.photo' => Block\PhotoBlock::class,
        'marketplace.profile' => Block\ProfileBlock::class,
        'marketplace.rows' => Block\RowsBlock::class,
        'marketplace.sponsored' => Block\SponsoredBlock::class
    ]);
group('/marketplace', function () {
    // BackEnd routes
    route('/admincp', function () {
        auth()->isAdmin(true);
        Phpfox::getLib('module')->dispatch('marketplace.admincp.index');
        return 'controller';
    });

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
                'table' => 'marketplace_category',
                'key' => 'category_id',
                'values' => $values,
            ]
        );
        Phpfox::getLib('cache')->remove();
        return true;
    });
    route('/', 'marketplace.index');
    route('/invoice/*', 'marketplace.invoice.index');
    route('/purchase/*', 'marketplace.purchase');
    route('/category/:id/:name/*', 'marketplace.index')->where([':id' => '([0-9]+)']);
    route('/:id/*', 'marketplace.view')->where([':id' => '([0-9]+)']);
    route('/add/*', 'marketplace.add');
    route('/frame-upload', 'marketplace.frame-upload');
});

$sDefaultListingPhoto = flavor()->active->default_photo('marketplace_default_photo', true);
if (!$sDefaultListingPhoto) {
    $sDefaultListingPhoto = setting('core.path_actual') . 'PF.Site/Apps/core-marketplace/assets/image/no_image.png';
}

Phpfox::getLib('setting')->setParam('marketplace.marketplace_default_photo', $sDefaultListingPhoto);

Phpfox::getLib('setting')->setParam('marketplace.thumbnail_sizes', array(50, 120, 200, 400));


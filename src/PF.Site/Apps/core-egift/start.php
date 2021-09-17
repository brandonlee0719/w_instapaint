<?php

namespace Apps\Core_eGifts;

use Phpfox;

Phpfox::getLib('module')
    ->addAliasNames('egift', 'Core_eGifts')
    ->addServiceNames([
        'egift' => Service\Egift::class,
        'egift.callback' => Service\Callback::class,
        'egift.process' => Service\Process::class,
        'egift.category' => Service\Category\Category::class,
        'egift.category.process' => Service\Category\Process::class
    ])
    ->addTemplateDirs([
        'egift' => PHPFOX_DIR_SITE_APPS . 'core-egift' . PHPFOX_DS . 'views'
    ])
    ->addComponentNames('controller', [
        'egift.admincp.categories' => Controller\Admin\CategoriesController::class,
        'egift.admincp.manage-gifts' => Controller\Admin\ManageGiftsController::class,
        'egift.admincp.invoices' => Controller\Admin\InvoicesController::class,
        'egift.admincp.add-category' => Controller\Admin\AddCategoryController::class,
        'egift.admincp.delete-category' => Controller\Admin\DeleteCategoryController::class,
        'egift.admincp.add-gift' => Controller\Admin\AddGiftController::class,
    ])
    ->addComponentNames('block', [
        'egift.received-gifts' => Block\ReceivedGifts::class,
        'egift.display' => Block\Display::class,
        'egift.list-egifts' => Block\ListEgifts::class
    ])
    ->addComponentNames('ajax', [
        'egift.ajax' => Ajax\Ajax::class
    ]);

group('/egift', function () {
    // BackEnd routes
    route('/admincp', function () {
        auth()->isAdmin(true);
        Phpfox::getLib('module')->dispatch('egift.admincp.categories');
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
                'table' => 'egift_category',
                'key' => 'category_id',
                'values' => $values,
            ]
        );
        return true;
    });

    route('/admincp/category/order-egifts', function () {
        auth()->isAdmin(true);
        $ids = request()->get('ids');
        $ids = trim($ids, ',');
        $ids = explode(',', $ids);
        $values = [];
        foreach ($ids as $key => $id) {
            $values[$id] = $key + 1;
        }
        Phpfox::getService('core.process')->updateOrdering([
                'table' => 'egift',
                'key' => 'egift_id',
                'values' => $values,
            ]
        );
        return true;
    });

    // FrontEnd routes
    route('/callback/', 'egift.callback');
});
Phpfox::getLib('setting')->setParam('egift.url_egif', Phpfox::getParam('core.url_pic') . 'egift/');

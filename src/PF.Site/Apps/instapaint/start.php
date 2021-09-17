<?php

namespace Apps\Instapaint;

// Load phpFox module service instance, this is core of phpFox service,
// module service contains your app configuration.
$module =\Phpfox_Module::instance();

// Register your controller here
$module->addComponentNames('controller', [
    'instapaint.my-dashboard' => Controller\MyDashboardController::class,
    'instapaint.client-dashboard' => Controller\ClientDashboardController::class,
    'instapaint.client-thank-you' => Controller\ClientThankYouController::class,
    'instapaint.client-orders' => Controller\ClientOrdersController::class,
    'instapaint.client-orders-add' => Controller\ClientOrdersAddController::class,
    'instapaint.client-orders-cancel' => Controller\ClientOrdersCancelController::class,
    'instapaint.client-orders-pay' => Controller\ClientOrdersPayController::class,
    'instapaint.client-order' => Controller\ClientOrderController::class,
    'instapaint.client-invoice' => Controller\ClientInvoiceController::class,
    'instapaint.client-addresses' => Controller\ClientAddressesController::class,
    'instapaint.client-addresses-add' => Controller\ClientAddressesAddController::class,
    'instapaint.client-addresses-delete' => Controller\ClientAddressesDeleteController::class,
    'instapaint.client-addresses-edit' => Controller\ClientAddressesEditController::class,
    'instapaint.client-addresses-set-default' => Controller\ClientAddressesSetDefaultController::class,
    'instapaint.client-first-order-add' => Controller\ClientFirstOrderAdd::class,
    'instapaint.painters' => Controller\PaintersController::class,
    'instapaint.giftcard' => Controller\GiftCardController::class,
    'instapaint.gallery' => Controller\GalleryController::class,
    'instapaint.client-first-order-complete' => Controller\ClientFirstOrderComplete::class,
    'instapaint.painter-dashboard' => Controller\PainterDashboardController::class,
    'instapaint.painter-approval-request' => Controller\PainterApprovalRequestController::class,
    'instapaint.painter-orders' => Controller\PainterOrdersController::class,
    'instapaint.painter-payments' => Controller\PainterPaymentsController::class,
    'instapaint.painter-available-orders' => Controller\PainterAvailableOrdersController::class,
    'instapaint.painter-take-order' => Controller\PainterTakeOrderController::class,
    'instapaint.painter-drop-order' => Controller\PainterDropOrderController::class,
    'instapaint.painter-complete-order' => Controller\PainterCompleteOrderController::class,
    'instapaint.painter-ship-order' => Controller\PainterShipOrderController::class,
    'instapaint.admin-dashboard' => Controller\AdminDashboardController::class,
    'instapaint.admin-orders' => Controller\AdminOrdersController::class,
    'instapaint.admin-invoice' => Controller\AdminInvoiceController::class,
    'instapaint.admin-open-orders' => Controller\AdminOpenOrdersController::class,
    'instapaint.admin-shipped-orders' => Controller\AdminShippedOrdersController::class,
    'instapaint.admin-verify-orders' => Controller\AdminVerifyOrdersController::class,
    'instapaint.admin-reject-order' => Controller\AdminRejectOrderController::class,
    'instapaint.admin-approve-order' => Controller\AdminApproveOrderController::class,
    'instapaint.admin-painters' => Controller\AdminPaintersController::class,
    'instapaint.admin-painters-approval-requests' => Controller\AdminPaintersApprovalRequestsController::class,
    'instapaint.admin-painters-approval-request' => Controller\AdminPaintersApprovalRequestController::class,
    'instapaint.admin-painters-set-daily-limit' => Controller\AdminPaintersSetDailyLimitController::class,
    'instapaint.admin-painters-settings' => Controller\AdminPaintersSettingsController::class,
    'instapaint.admin-settings' => Controller\AdminSettingsController::class,
    'instapaint.admin-painters-approval-request-approve' => Controller\AdminPaintersApprovalRequestApproveController::class,
    'instapaint.admin-painters-approval-request-deny' => Controller\AdminPaintersApprovalRequestDenyController::class,
    'instapaint.admin-painters-approved' => Controller\AdminPaintersApprovedController::class,
    'instapaint.admin-painters-unapproved' => Controller\AdminPaintersUnapprovedController::class,
    'instapaint.admin-discounts' => Controller\AdminDiscountsController::class,
    'instapaint.admin-discounts-add' => Controller\AdminDiscountsAddController::class,
    'instapaint.admin-discounts-delete' => Controller\AdminDiscountsDeleteController::class,
    'instapaint.admin-discounts-edit' => Controller\AdminDiscountsEditController::class,
    'instapaint.admin-packages' => Controller\AdminPackagesController::class,
    'instapaint.admin-packages-add' => Controller\AdminPackagesAddController::class,
    'instapaint.admin-packages-delete' => Controller\AdminPackagesDeleteController::class,
    'instapaint.admin-packages-frame-size-add' => Controller\AdminPackagesFrameSizeAddController::class,
    'instapaint.admin-packages-frame-size-edit' => Controller\AdminPackagesFrameSizeEditController::class,
    'instapaint.admin-packages-frame-size-delete' => Controller\AdminPackagesFrameSizeDeleteController::class,
    'instapaint.admin-packages-frame-type-add' => Controller\AdminPackagesFrameTypeAddController::class,
    'instapaint.admin-packages-frame-type-edit' => Controller\AdminPackagesFrameTypeEditController::class,
    'instapaint.admin-packages-frame-type-delete' => Controller\AdminPackagesFrameTypeDeleteController::class,
    'instapaint.admin-packages-shipping-type-add' => Controller\AdminPackagesShippingTypeAddController::class,
    'instapaint.admin-packages-shipping-type-edit' => Controller\AdminPackagesShippingTypeEditController::class,
    'instapaint.admin-packages-shipping-type-delete' => Controller\AdminPackagesShippingTypeDeleteController::class,
    'instapaint.admin-users' => Controller\AdminUsersController::class
]);

// Register template directory
$module->addTemplateDirs([
    'instapaint' => PHPFOX_DIR_SITE_APPS . 'instapaint/views',
]);

// Register service
$module->addServiceNames([
    'instapaint' => Service\Instapaint::class,
    'instapaint.security' => Service\Security::class,
    'instapaint.packages' => Service\Packages::class,
    'instapaint.admin' => Service\Admin::class,
    'instapaint.painter' => Service\Painter::class,
    'instapaint.client' => Service\Client::class,
    'instapaint.stats' => Service\Stats::class,
    'instapaint-browse-painter-approval-requests.browse'=> Service\BrowsePainterApprovalRequests::class,
    'instapaint-browse-users.browse'=> Service\BrowseUsers::class,
    'instapaint-browse-discounts.browse'=> Service\BrowseDiscounts::class,
    'instapaint.callback'=> Service\Callback::class,
    'instapaint.events'=> Service\Events::class,
    'instapaint.settings' => Service\Settings::class,
    'instapaint.gift-cards' => Service\GiftCards::class,
]);


route('my-dashboard',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.my-dashboard');
    return 'controller';
});

route('client-dashboard',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.client-dashboard');
    return 'controller';
});

route('client-dashboard/thank-you/:id',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.client-thank-you');
    return 'controller';
})->where([':id' => '([0-9]+)']);

route('client-dashboard/orders',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.client-orders');
    return 'controller';
});

route('client-dashboard/orders/add',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.client-orders-add');
    return 'controller';
});

route('client-dashboard/orders/cancel',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.client-orders-cancel');
    return 'controller';
});

route('client-dashboard/orders/pay/:id',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.client-orders-pay');
    return 'controller';
})->where([':id' => '([0-9]+)']);

route('client-dashboard/order/:id',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.client-order');
    return 'controller';
})->where([':id' => '([0-9]+)']);

route('client-dashboard/invoice/:id',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.client-invoice');
    return 'controller';
})->where([':id' => '([0-9]+)']);

route('admin-dashboard/invoice/:id',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.admin-invoice');
    return 'controller';
})->where([':id' => '([0-9]+)']);


route('client-dashboard/addresses',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.client-addresses');
    return 'controller';
});

route('client-dashboard/addresses/add',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.client-addresses-add');
    return 'controller';
});

route('client-dashboard/addresses/edit/:id',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.client-addresses-edit');
    return 'controller';
})->where([':id' => '([0-9]+)']);

route('client-dashboard/addresses/delete/:id',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.client-addresses-delete');
    return 'controller';
})->where([':id' => '([0-9]+)']);

route('client-dashboard/addresses/set-default',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.client-addresses-set-default');
    return 'controller';
});

route('first-order',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.client-first-order-add');
    return 'controller';
});

route('painters',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.painters');
    return 'controller';
});

route('giftcard',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.giftcard');
    return 'controller';
});

route('gallery',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.gallery');
    return 'controller';
});

route('first-order-complete',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.client-first-order-complete');
    return 'controller';
});

route('painter-dashboard',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.painter-dashboard');
    return 'controller';
});

route('painter-dashboard/available-orders',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.painter-available-orders');
    return 'controller';
});

route('painter-dashboard/take-order/:id',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.painter-take-order');
    return 'controller';
})->where([':id' => '([0-9]+)']);

route('painter-dashboard/drop-order/:id',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.painter-drop-order');
    return 'controller';
})->where([':id' => '([0-9]+)']);

route('painter-dashboard/complete-order/:id',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.painter-complete-order');
    return 'controller';
})->where([':id' => '([0-9]+)']);

route('painter-dashboard/ship-order/:id',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.painter-ship-order');
    return 'controller';
})->where([':id' => '([0-9]+)']);

route('painter-dashboard/approval-request',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.painter-approval-request');
    return 'controller';
});

route('painter-dashboard/orders',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.painter-orders');
    return 'controller';
});

route('painter-dashboard/payments',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.painter-payments');
    return 'controller';
});

route('admin-dashboard',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.admin-dashboard');
    return 'controller';
});

route('admin-dashboard/orders',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.admin-orders');
    return 'controller';
});

route('admin-dashboard/shipped-orders',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.admin-shipped-orders');
    return 'controller';
});

route('admin-dashboard/verify-orders',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.admin-verify-orders');
    return 'controller';
});

route('admin-dashboard/reject-order/:id',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.admin-reject-order');
    return 'controller';
})->where([':id' => '([0-9]+)']);

route('admin-dashboard/approve-order/:id',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.admin-approve-order');
    return 'controller';
})->where([':id' => '([0-9]+)']);

route('admin-dashboard/open-orders',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.admin-open-orders');
    return 'controller';
});

route('admin-dashboard/painters',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.admin-painters');
    return 'controller';
});

route('admin-dashboard/painters/approval-requests',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.admin-painters-approval-requests');
    return 'controller';
});

route('admin-dashboard/painters/approval-request/:id',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.admin-painters-approval-request');
    return 'controller';
})->where([':id' => '([0-9]+)']);

route('admin-dashboard/painters/set-daily-limit/:id',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.admin-painters-set-daily-limit');
    return 'controller';
})->where([':id' => '([0-9]+)']);

route('admin-dashboard/painters/settings',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.admin-painters-settings');
    return 'controller';
});

route('admin-dashboard/settings',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.admin-settings');
    return 'controller';
});

route('admin-dashboard/painters/approval-request/approve',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.admin-painters-approval-request-approve');
    return 'controller';
});

route('admin-dashboard/painters/approval-request/deny',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.admin-painters-approval-request-deny');
    return 'controller';
});

route('admin-dashboard/painters/approved',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.admin-painters-approved');
    return 'controller';
});

route('admin-dashboard/painters/unapproved',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.admin-painters-unapproved');
    return 'controller';
});

group('/admin-dashboard/discounts', function () {
    route('/', 'instapaint.admin-discounts');
    route('/add', 'instapaint.admin-discounts-add');
    route('/delete', 'instapaint.admin-discounts-delete');
    route('/edit/:id', 'instapaint.admin-discounts-edit')->where([':id' => '([0-9]+)']);
});

group('/admin-dashboard/packages', function () {
    route('/', 'instapaint.admin-packages');
    route('/add', 'instapaint.admin-packages-add');
    route('/delete/:id', 'instapaint.admin-packages-delete')->where([':id' => '([0-9]+)']);
    route('/frame-size/add', 'instapaint.admin-packages-frame-size-add');
    route('/frame-size/edit/:id', 'instapaint.admin-packages-frame-size-edit')->where([':id' => '([0-9]+)']);
    route('/frame-size/delete/:id', 'instapaint.admin-packages-frame-size-delete')->where([':id' => '([0-9]+)']);
    route('/frame-type/add', 'instapaint.admin-packages-frame-type-add');
    route('/frame-type/edit/:id', 'instapaint.admin-packages-frame-type-edit')->where([':id' => '([0-9]+)']);
    route('/frame-type/delete/:id', 'instapaint.admin-packages-frame-type-delete')->where([':id' => '([0-9]+)']);
    route('/shipping-type/add', 'instapaint.admin-packages-shipping-type-add');
    route('/shipping-type/edit/:id', 'instapaint.admin-packages-shipping-type-edit')->where([':id' => '([0-9]+)']);
    route('/shipping-type/delete/:id', 'instapaint.admin-packages-shipping-type-delete')->where([':id' => '([0-9]+)']);
});

route('admin-dashboard/users',function (){
    \Phpfox_Module::instance()->dispatch('instapaint.admin-users');
    return 'controller';
});

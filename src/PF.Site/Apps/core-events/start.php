<?php
namespace Apps\Core_Events;

use Phpfox;
use Phpfox_Module;

Phpfox_Module::instance()
    ->addAliasNames('event', 'Core_Events')
    ->addServiceNames([
        'event' => Service\Event::class,
        'event.browse' => Service\Browse::class,
        'event.api' => Service\Api::class,
        'event.callback' => Service\Callback::class,
        'event.process' => Service\Process::class,
        'event.category' => Service\Category\Category::class,
        'event.category.process' => Service\Category\Process::class
    ])
    ->addTemplateDirs([
        'event' => PHPFOX_DIR_SITE_APPS . 'core-events' . PHPFOX_DS . 'views'
    ])
    ->addComponentNames('controller', [
        'event.index' => Controller\IndexController::class,
        'event.add' => Controller\AddController::class,
        'event.profile' => Controller\ProfileController::class,
        'event.view' => Controller\ViewController::class,
        'event.admincp.add' => Controller\Admin\AddController::class,
        'event.admincp.index' => Controller\Admin\IndexController::class,
        'event.admincp.delete' => Controller\Admin\DeleteController::class
    ])
    ->addComponentNames('ajax', [
        'event.ajax' => Ajax\Ajax::class,
    ])
    ->addComponentNames('block', [
        'event.attending' => Block\AttendingBlock::class,
        'event.browse' => Block\BrowseBlock::class,
        'event.category' => Block\CategoryBlock::class,
        'event.featured' => Block\FeaturedBlock::class,
        'event.info' => Block\InfoBlock::class,
        'event.invite' => Block\InviteBlock::class,
        'event.list' => Block\ListBlock::class,
        'event.menu' => Block\MenuBlock::class,
        'event.mini' => Block\MiniBlock::class,
        'event.profile' => Block\ProfileBlock::class,
        'event.rsvp' => Block\RsvpBlock::class,
        'event.sponsored' => Block\SponsoredBlock::class,
        'event.suggestion' => Block\SuggestionBlock::class
    ]);
group('/event', function () {
    // BackEnd routes
    route('/admincp', function () {
        auth()->isAdmin(true);
        Phpfox::getLib('module')->dispatch('event.admincp.index');
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
                'table' => 'event_category',
                'key' => 'category_id',
                'values' => $values,
            ]
        );
        Phpfox::getLib('cache')->remove();
        return true;
    });
    route('/', 'event.index');
    route('/add/*', 'event.add');
    route('/:id/*', 'event.view')->where([':id' => '([0-9]+)']);
    route('/category/:id/*', 'event.index')->where([':id' => '([0-9]+)']);

});

$sDefaultEventPhoto = flavor()->active->default_photo('event_default_photo', true);
if (!$sDefaultEventPhoto) {
    $sDefaultEventPhoto = setting('core.path_actual') . 'PF.Site/Apps/core-events/assets/image/no_image.png';
}
Phpfox::getLib('setting')->setParam('event.event_default_photo', $sDefaultEventPhoto);
//set cache for categories in 10 minutes.
Phpfox::getLib('setting')->setParam('event.categories_cache_time', 10);
//image thumbnails
Phpfox::getLib('setting')->setParam('event.thumbnail_sizes', array(50, 200, 400, 1024));

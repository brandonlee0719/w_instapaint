<?php

namespace Apps\Core_RSS;

use Phpfox;

Phpfox::getLib('module')
    ->addAliasNames('rss', 'Core_RSS')
    ->addServiceNames([
        'rss'               => Service\Rss::class,
        'rss.callback'      => Service\Callback::class,
        'rss.process'       => Service\Process::class,
        'rss.group'         => Service\Group\Group::class,
        'rss.group.process' => Service\Group\Process::class,
        'rss.log'           => Service\Log\Log::class,
    ])
    ->addTemplateDirs([
        'rss' => PHPFOX_DIR_SITE_APPS . 'core-rss' . PHPFOX_DS . 'views',
    ])
    ->addComponentNames('controller', [
        'rss.index'               => Controller\IndexController::class,
        'rss.log'                 => Controller\LogController::class,
        'rss.profile'             => Controller\ProfileController::class,
        'rss.admincp.add'         => Controller\Admin\AddController::class,
        'rss.admincp.index'       => Controller\Admin\IndexController::class,
        'rss.admincp.log'         => Controller\Admin\LogController::class,
        'rss.admincp.add-group'   => Controller\Admin\AddGroupController::class,
        'rss.admincp.group' => Controller\Admin\GroupIndexController::class,
    ])
    ->addComponentNames('ajax', [
        'rss.ajax' => Ajax\Ajax::class,
    ])
    ->addComponentNames('block', [
        'rss.info' => Block\InfoBlock::class,
        'rss.log'  => Block\LogBlock::class,
    ]);
group('/rss', function () {
    // BackEnd routes
    route('/admincp', function () {
        auth()->isAdmin(true);
        Phpfox::getLib('module')->dispatch('rss.admincp.index');
        return 'controller';
    });
    route('/admincp/feed/order', function () {
        auth()->isAdmin(true);
        $ids = request()->get('ids');
        $ids = trim($ids, ',');
        $ids = explode(',', $ids);
        $values = [];
        foreach ($ids as $key => $id) {
            $values[$id] = $key + 1;
        }
        Phpfox::getService('core.process')->updateOrdering([
                'table'  => 'rss',
                'key'    => 'feed_id',
                'values' => $values,
            ]
        );
        Phpfox::getLib('cache')->remove('rss', 'substr');
        return true;
    });
    route('/admincp/group/order', function () {
        auth()->isAdmin(true);
        $ids = request()->get('ids');
        $ids = trim($ids, ',');
        $ids = explode(',', $ids);
        $values = [];
        foreach ($ids as $key => $id) {
            $values[$id] = $key + 1;
        }
        Phpfox::getService('core.process')->updateOrdering([
                'table'  => 'rss_group',
                'key'    => 'group_id',
                'values' => $values,
            ]
        );
        Phpfox::getLib('cache')->remove('rss', 'substr');
        return true;
    });
    route('/', 'rss.index');
});

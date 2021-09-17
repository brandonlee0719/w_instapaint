<?php

Phpfox_Module::instance()
    ->addAliasNames('pages', 'Core_Pages')
    ->addServiceNames([
        'pages.browse' => Apps\Core_Pages\Service\Browse::class,
        'pages.callback' => Apps\Core_Pages\Service\Callback::class,
        'pages.facade' => Apps\Core_Pages\Service\Facade::class,
        'pages' => Apps\Core_Pages\Service\Pages::class,
        'pages.process' => Apps\Core_Pages\Service\Process::class,
        'pages.type' => Apps\Core_Pages\Service\Type::class,
        'pages.category' => Apps\Core_Pages\Service\Category::class
    ])
    ->addTemplateDirs([
        'pages' => PHPFOX_DIR_SITE_APPS . 'core-pages' . PHPFOX_DS . 'views',
    ])
    ->addComponentNames('ajax', [
        'pages.ajax' => Apps\Core_Pages\Ajax\Ajax::class
    ])
    ->addComponentNames('block', [
        'pages.admin' => Apps\Core_Pages\Block\Admin::class,
        'pages.category' => Apps\Core_Pages\Block\Category::class,
        'pages.cropme' => Apps\Core_Pages\Block\Cropme::class,
        'pages.delete-category' => Apps\Core_Pages\Block\DeleteCategory::class,
        'pages.header' => Apps\Core_Pages\Block\Header::class,
        'pages.like' => Apps\Core_Pages\Block\Like::class,
        'pages.login' => Apps\Core_Pages\Block\Login::class,
        'pages.login-user' => Apps\Core_Pages\Block\LoginUser::class,
        'pages.menu' => Apps\Core_Pages\Block\Menu::class,
        'pages.photo' => Apps\Core_Pages\Block\Photo::class,
        'pages.profile' => Apps\Core_Pages\Block\Profile::class,
        'pages.widget' => Apps\Core_Pages\Block\Widget::class,
        'pages.add-page' => Apps\Core_Pages\Block\AddPage::class,
        'pages.people-also-like' => Apps\Core_Pages\Block\PeopleAlsoLike::class,
        'pages.pending' => Apps\Core_Pages\Block\Pending::class,
        'pages.search-member' => Apps\Core_Pages\Block\SearchMember::class
    ])
    ->addComponentNames('controller', [
        'pages.add' => Apps\Core_Pages\Controller\AddController::class,
        'pages.all' => Apps\Core_Pages\Controller\AllController::class,
        'pages.frame' => Apps\Core_Pages\Controller\FrameController::class,
        'pages.index' => Apps\Core_Pages\Controller\IndexController::class,
        'pages.profile' => Apps\Core_Pages\Controller\ProfileController::class,
        'pages.view' => Apps\Core_Pages\Controller\ViewController::class,
        'pages.widget' => Apps\Core_Pages\Controller\WidgetController::class,
        'pages.photo' => Apps\Core_Pages\Controller\PhotoController::class,
        'pages.members' => Apps\Core_Pages\Controller\MembersController::class,

        'pages.admincp.add' => Apps\Core_Pages\Controller\Admin\AddCategoryController::class,
        'pages.admincp.claim' => Apps\Core_Pages\Controller\Admin\ClaimController::class,
        'pages.admincp.index' => Apps\Core_Pages\Controller\Admin\IndexController::class,
        'pages.admincp.integrate' => Apps\Core_Pages\Controller\Admin\IntegrateController::class
    ]);

group('/pages', function () {
    // admincp
    route('/admincp/add', function () {
        Phpfox::getLib('module')->dispatch('pages.admincp.add');

        return 'controller';
    });
    route('/admincp', function () {
        Phpfox::getLib('module')->dispatch('pages.admincp.index');

        return 'controller';
    });

    // front
    route('/add/*', 'pages.add');
    route('/all', 'pages.all');
    route('/frame', 'pages.frame');
    route('/', 'pages.index');
    route('/category/:id/*', 'pages.index')->where([':id' => '([0-9]+)']);
    route('/sub-category/:id/*', 'pages.index')->where([':id' => '([0-9]+)']);
    route('/:id/*', 'pages.view')->where([':id' => '([0-9]+)']);
    route('/widget', 'pages.widget');
    route('/photo', 'pages.photo');
});

// default cover
$sDefaultCover = flavor()->active->default_photo('pages_cover_default', true);
if (!$sDefaultCover) {
    $sDefaultCover = setting('core.path_actual') . 'PF.Site/Apps/core-pages/assets/img/default_pagecover.png';
}
Phpfox::getLib('setting')->setParam('pages.default_cover_photo', $sDefaultCover);

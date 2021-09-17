<?php
namespace Apps\Core_Forums;

use Phpfox;
use Phpfox_Module;

Phpfox_Module::instance()
    ->addAliasNames('forum', 'Core_Forums')
    ->addServiceNames([
        'forum' => Service\Forum::class,
        'forum.callback' => Service\Callback::class,
        'forum.process' => Service\Process::class,
        'forum.moderate' => Service\Moderate\Moderate::class,
        'forum.moderate.process' => Service\Moderate\Process::class,
        'forum.post' => Service\Post\Post::class,
        'forum.post.process' => Service\Post\Process::class,
        'forum.subscribe' => Service\Subscribe\Subscribe::class,
        'forum.subscribe.process' => Service\Subscribe\Process::class,
        'forum.thread' => Service\Thread\Thread::class,
        'forum.thread.process' => Service\Thread\Process::class,
    ])
    ->addTemplateDirs([
        'forum' => PHPFOX_DIR_SITE_APPS . 'core-forums' . PHPFOX_DS . 'views'
    ])
    ->addComponentNames('controller', [
        'forum.admincp.add' => Controller\Admin\AddController::class,
        'forum.admincp.index' => Controller\Admin\IndexController::class,
        'forum.admincp.permission' => Controller\Admin\PermissionController::class,
        'forum.admincp.delete' => Controller\Admin\DeleteController::class,
        'forum.forum' => Controller\ForumController::class,
        'forum.index' => Controller\IndexController::class,
        'forum.post' => Controller\PostController::class,
        'forum.read' => Controller\ReadController::class,
        'forum.recent' => Controller\RecentController::class,
        'forum.rss' => Controller\RssController::class,
        'forum.search' => Controller\SearchController::class,
        'forum.tag' => Controller\TagController::class,
        'forum.thread' => Controller\ThreadController::class
    ])
    ->addComponentNames('ajax', [
        'forum.ajax' => Ajax\Ajax::class
    ])
    ->addComponentNames('block', [
        'forum.admincp.moderator' => Block\Admin\ModeratorBlock::class,
        'forum.copy' => Block\CopyBlock::class,
        'forum.merge' => Block\MergeBlock::class,
        'forum.move' => Block\MoveBlock::class,
        'forum.recent-post' => Block\RecentPostBlock::class,
        'forum.recent-thread' => Block\RecentThreadBlock::class,
        'forum.thanks' => Block\ThanksBlock::class,
        'forum.feed-rows' => Block\FeedRowsBlock::class,
        'forum.sponsored' => Block\SponsoredBlock::class,
        'forum.reply' => Block\ReplyBlock::class
    ]);
group('/forum', function () {
    // BackEnd routes
    route('/admincp', function () {
        auth()->isAdmin(true);
        Phpfox::getLib('module')->dispatch('forum.admincp.index');
        return 'controller';
    });
    route('/thread/:id/*', 'forum.thread')->where([':id' => '([0-9]+)']);
    route('/search/*', 'forum.search');
    route('/rss/*', 'forum.rss');
    route('/:id/*', 'forum.forum')->where([':id' => '([0-9]+)']);
    route('/post/thread/*', 'forum.post');
    route('/read/*', 'forum.read');
    route('/tag/*', 'forum.tag');
    route('/*', 'forum.index');
});


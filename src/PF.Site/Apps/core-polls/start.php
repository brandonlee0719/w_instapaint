<?php

namespace Apps\Core_Polls;

use Phpfox;

\Phpfox_Module::instance()
    ->addAliasNames('poll', 'Core_Polls')
    ->addServiceNames([
        'poll' => Service\Poll::class,
        'poll.browse' => Service\Browse::class,
        'poll.callback' => Service\Callback::class,
        'poll.process' => Service\Process::class
    ])
    ->addTemplateDirs([
        'poll' => PHPFOX_DIR_SITE_APPS . 'core-polls' . PHPFOX_DS . 'views'
    ])
    ->addComponentNames('controller', [
        'poll.index' => Controller\IndexController::class,
        'poll.view' => Controller\ViewController::class,
        'poll.add' => Controller\AddController::class,
        'poll.design' => Controller\DesignController::class,
        'poll.profile' => Controller\ProfileController::class
    ])
    ->addComponentNames('ajax', [
        'poll.ajax' => Ajax\Ajax::class,
    ])
    ->addComponentNames('block', [
        'poll.featured' => Block\FeaturedBlock::class,
        'poll.sponsored' => Block\SponsoredBlock::class,
        'poll.share' => Block\ShareBlock::class,
        'poll.vote' => Block\VoteBlock::class,
        'poll.votes' => Block\VotesBlock::class,
        'poll.new' => Block\NewBlock::class,
        'poll.feed-rows' => Block\FeedRowsBlock::class,
        'poll.answer-voted' => Block\AnswerVotedBlock::class,
        'poll.latest-votes' => Block\LatestVotesBlock::class
    ]);

group('/poll', function () {
    route('/', 'poll.index');
    route('/add/*', 'poll.add');
    route('/design/*', 'poll.design');
    route('/:id/*', 'poll.view')->where([':id' => '([0-9]+)']);
});
Phpfox::getLib('setting')->setParam('poll.thumbnail_sizes', [50, 150, 200, 500]);

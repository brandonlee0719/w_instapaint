<?php
namespace Apps\Core_Quizzes;

use Phpfox;
use Phpfox_Module;

Phpfox_Module::instance()
    ->addAliasNames('quiz', 'Core_Quizzes')
    ->addServiceNames([
        'quiz' => Service\Quiz::class,
        'quiz.process' => Service\Process::class,
        'quiz.browse' => Service\Browse::class,
        'quiz.callback' => Service\Callback::class
    ])
    ->addTemplateDirs([
        'quiz' => PHPFOX_DIR_SITE_APPS . 'core-quizzes' . PHPFOX_DS . 'views'
    ])
    ->addComponentNames('controller', [
        'quiz.add' => Controller\AddController::class,
        'quiz.index' => Controller\IndexController::class,
        'quiz.profile' => Controller\ProfileController::class,
        'quiz.view' => Controller\ViewController::class,
    ])
    ->addComponentNames('ajax', [
        'quiz.ajax' => Ajax\Ajax::class
    ])
    ->addComponentNames('block', [
        'quiz.stat' => Block\StatBlock::class,
        'quiz.takenby' => Block\TakenByBlock::class,
        'quiz.featured' => Block\FeaturedBlock::class,
        'quiz.sponsored' => Block\SponsoredBlock::class,
        'quiz.feed-rows' => Block\FeedRowsBlock::class
    ]);

group('/quiz', function () {
    route('/', 'quiz.index');
    route('/add/*', 'quiz.add');
    route('/:id/*', 'quiz.view')->where([':id' => '([0-9]+)']);
});
Phpfox::getLib('setting')->setParam('quiz.thumbnail_sizes', array(50, 200, 500));

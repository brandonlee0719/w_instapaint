<?php

namespace Apps\Core_Blogs;

use Phpfox;

define('DISABLE_COMMENT', 3);
define('BLOG_STATUS_DRAFT', 2);
define('BLOG_STATUS_PUBLIC', 1);
define('BLOG_BANNED', 9);
define('BLOG_TEXT_SHORTEN', 500);
define('ACTIVATE', 1);
define('INACTIVATE', 0);
define('PRIVACY_EVERYONE', 0);
define('PRIVACY_CUSTOM', 4);


Phpfox::getLib('module')
    ->addAliasNames('blog', 'Core_Blogs')
    ->addServiceNames([
        'blog.category' => Service\Category\Category::class,
        'blog.category.process' => Service\Category\Process::class,
        'blog.api' => Service\Api::class,
        'blog' => Service\Blog::class,
        'blog.browse' => Service\Browse::class,
        'blog.cache.remove' => Service\Cache\Remove::class,
        'blog.callback' => Service\Callback::class,
        'blog.process' => Service\Process::class,
        'blog.permission' => Service\Permission::class,
    ])
    ->addTemplateDirs([
        'blog' => PHPFOX_DIR_SITE_APPS . 'core-blogs' . PHPFOX_DS . 'views'
    ])
    ->addComponentNames('controller', [
        'blog.admincp.category' => Controller\Admin\CategoryController::class,
        'blog.admincp.add' => Controller\Admin\AddCategoryController::class,
        'blog.admincp.delete-category' => Controller\Admin\DeleteCategoryController::class,
        'blog.index' => Controller\IndexController::class,
        'blog.add' => Controller\AddController::class,
        'blog.profile' => Controller\ProfileController::class,
        'blog.view' => Controller\ViewController::class,
    ])
    ->addComponentNames('ajax', [
        'blog.ajax' => Ajax\Ajax::class
    ])
    ->addComponentNames('block', [
        'blog.add-category-list' => Block\AddCategoryList::class,
        'blog.new' => Block\BlogNew::class,
        'blog.preview' => Block\Preview::class,
        'blog.categories' => Block\Categories::class,
        'blog.featured' => Block\Featured::class,
        'blog.sponsored' => Block\Sponsored::class,
        'blog.related' => Block\Related::class,
        'blog.top' => Block\TopBloggers::class,
        'blog.topic' => Block\PopularTopic::class,
        'blog.feed' => Block\Feed::class,
    ]);

group('/blog', function () {
    // BackEnd routes
    route('/admincp', function () {
        auth()->isAdmin(true);
        Phpfox::getLib('module')->dispatch('blog.admincp.category');
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
                'table' => 'blog_category',
                'key' => 'category_id',
                'values' => $values,
            ]
        );

        Phpfox::getLib('cache')->remove();
        return true;
    });

    // FrontEnd routes
    route('/', 'blog.index');
    route('/tag/:slug', 'blog.index');
    route('/category/:id/:name/*', 'blog.index')->where([':id' => '([0-9]+)']);
    route('/:id/*', 'blog.view')->where([':id' => '([0-9]+)']);
    route('/add/*', 'blog.add');
    route('/edit/:id/', 'blog.add')->where([':id' => '([0-9]+)']);
    route('/callback/', 'blog.callback');
});

Phpfox::getLib('setting')->setParam('blog.url_photo', Phpfox::getParam('core.url_pic') . 'blog/');

Phpfox::getLib('setting')->setParam('blog.thumbnail_sizes', [240, 500, 1024]);
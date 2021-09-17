<?php

namespace Core;

use Core\View\Functions;

class View
{

    public static $template = 'layout';
    public static $cache_path = null;

    private $_loader;
    private $_env;
    private $_render = [];

    public function __construct()
    {

        if (self::$cache_path === null) {
            self::$cache_path = PHPFOX_DIR_FILE . 'cache' . PHPFOX_DS . 'twig-'. php_sapi_name() . PHPFOX_DS;
        }

        Event::trigger('view_cache_path');

        $Template = \Phpfox_Template::instance();

        $this->_loader = new View\Loader();

        $this->_loader->addPath(PHPFOX_DIR . 'theme' . PHPFOX_DS . 'default' . PHPFOX_DS . 'html', 'Theme');

        $this->_loader->addPath(PHPFOX_DIR . 'views', 'Base');

        $this->_env = new View\Environment($this->_loader, [
            'cache'      => (((defined('PHPFOX_IS_TECHIE') && PHPFOX_IS_TECHIE) || defined('PHPFOX_NO_TEMPLATE_CACHE')) ? false : self::$cache_path),
            'autoescape' => false,
        ]);

        $this->_env->setBaseTemplateClass('Core\View\Base');

        $this->_env->addFilter(new \Twig_SimpleFilter('short_number', function($number){
            return \Phpfox::getService('core.helper')->shortNumber($number);
        }));

        $this->_env->addFunction(new \Twig_SimpleFunction('permalink', function ($link, $id, $title) {
            return \Phpfox_Url::instance()->permalink($link, $id, $title);
        }));

        $this->_env->addFunction(new \Twig_SimpleFunction('shorten', function ($str, $max_length) {
            return \Phpfox_Parse_Output::instance()->shorten($str, $max_length, _p('view_more'), true);
        }));

        $this->_env->addFunction(new \Twig_SimpleFunction('url', function ($url, $params = []) {
            return \Phpfox_Url::instance()->makeUrl($url, $params);
        }));

        $this->_env->addFunction(new \Twig_SimpleFunction('is_user', function () {
            return \Phpfox::isUser();
        }));

        $this->_env->addFunction(new \Twig_SimpleFunction('is_admin', function () {
            return \Phpfox::isAdmin();
        }));

        $this->_env->addFunction(new \Twig_SimpleFunction('setting', function () {
            return call_user_func_array('setting', func_get_args());
        }));

        $this->_env->addFunction(new \Twig_SimpleFunction('user_group_setting', function () {
            return call_user_func_array('user_group_setting', func_get_args());
        }));

        $this->_env->addFunction(new \Twig_SimpleFunction('user', function () {
            return call_user_func_array('user', func_get_args());
        }));

        $this->_env->addFunction(new \Twig_SimpleFunction('parse', function ($str) {
            return \Phpfox_Parse_Output::instance()->parse($str);
        }));

        $this->_env->addFunction(new \Twig_SimpleFunction('privacy', function () {

            echo '<div class="table"><div class="table_left">' . _p('Privacy') . '</div><div class="table_right">';
            \Phpfox::getBlock('privacy.form', [
                'privacy_name' => 'privacy',
                'privacy_info' => _p('Control who can see this item.'),
            ]);
            echo '</div></div>';

            return '';
        }));

        $this->_env->addFunction(new \Twig_SimpleFunction('privacy_build', function ($module_id, $item_id) {
            echo '<div id="js_custom_privacy_input_holder">';
            \Phpfox::getBlock('privacy.build', [
                'privacy_module_id' => $module_id,
                'privacy_item_id'   => $item_id,
            ]);
            echo '</div>';

            return '';
        }));

        $this->_env->addFunction(new \Twig_SimpleFunction('comments', function () {

            \Phpfox::getBlock('feed.comment');

            return '';
        }));

        $this->_env->addFunction(new \Twig_SimpleFunction('payment', function ($params) {
            $params = new \Core\Object($params);

            \Phpfox::getBlock('api.gateway.form', [
                'gateway_data' => [
                    'item_number'                => '@App/' . $params->callback . '|' . $params->id,
                    'currency_code'              => 'USD',
                    'amount'                     => $params->amount,
                    'item_name'                  => $params->name,
                    'return'                     => $params->return,
                    'recurring'                  => '',
                    'recurring_cost'             => '',
                    'alternative_cost'           => '',
                    'alternative_recurring_cost' => '',
                ],
            ]);

            return '';
        }));

        $this->_env->addFunction(new \Twig_SimpleFunction('pager', function () {
            $u = \Phpfox_Url::instance();
            if (!isset($_GET['page'])) {
                $_GET['page'] = 0;
            }
            $_GET['page']++;
            $u->setParam('page', $_GET['page']);
            $u->setParam('app-pager', '1');
            $url = $u->current();

            $html
                = '
				<div class="js_pager_view_more_link">
					<a href="' . $url . '" class="next_page">
						<i class="fa fa-spin fa-circle-o-notch"></i>
						<span>' . _p("View More") . '</span>
					</a>
				</div>
			';

            return $html;
        }));

        $clone = [
            '_p',
            'moment',
            'phrase',
            'asset',
        ];

        foreach ($clone as $function) {
            $this->_env->addFunction(new \Twig_SimpleFunction($function, function () use ($function) {
                return call_user_func_array($function, func_get_args());
            }));
        }

        $this->_env->addFunction(new \Twig_SimpleFunction('get_controller_name', function () {
            return \Phpfox_Module::instance()->getControllerName();
        }));
    }

    public function env()
    {
        return $this->_env;
    }

    public function loader()
    {
        return $this->_loader;
    }

    function view($name, $params = [])
    {
        if (substr($name, 0, 1) != '@') {
            return $this->render($name, $params);
        }

        $parts = explode('/', $name);
        $app = str_replace('@', '', $parts[0]);
        unset($parts[0]);
        $path = implode('/', $parts);
        if ($app == 'Flavor') {
            $this->_loader->addPath(flavor()->active->path . 'html/', 'Flavor');
        } else {
            $obj = (new \Core\App())->get($app);
            $this->_loader->addPath($obj->path . 'views', $app);
        }

        return $this->_env->render($name, $params);
    }

    public function render($name, array $params = [])
    {
        $this->_render = [
            'name'   => $name,
            'params' => $params,
        ];

        return $this;
    }

    public function getContent($force = false)
    {

        if ($force) {
            return $this->_env->render('@Theme/' . self::$template . '.html', $this->_render);
        }

        $Template = \Phpfox_Template::instance();
        if (!$this->_render) {
            \Phpfox_Module::instance()->getControllerTemplate();
            $content = ob_get_contents();
            ob_clean();

            $this->_render['name'] = '@Base/' . self::$template . '.html';
            $this->_render['params']['content'] = $content;
        }

        $params = $this->_render['params'];
        $params['content'] = $this->_env->render($this->_render['name'], $params);
        if (PHPFOX_IS_AJAX_PAGE) {
            $content = (string)new View\Functions('content', $params['content']);

            return $content;
        }

        $params['content'] = new View\Functions('content', $params['content']);
        $params['header'] = $Template->getHeader();
        $params['title'] = $Template->getTitle();
        $params['js'] = new View\Functions('js');
        $params['nav'] = new View\Functions('nav');
        $params['sticky_bar'] = new View\Functions('sticky_bar');
        $params['sticky_bar_sm'] = new View\Functions('sticky_bar_sm');
        $params['sticky_bar_xs'] = new View\Functions('sticky_bar_xs');
        $params['menu_sub'] = new View\Functions('menu_sub');
        $params['breadcrumb_menu'] = new View\Functions('breadcrumb_menu');

        $params['site_logo'] = \Phpfox::getParam('core.site_title');
        $params['site_link'] = \Phpfox::getLib('url')->makeUrl('');

        $params['menu'] = new View\Functions('menu');
        $params['share'] = new View\Functions('share');
        $params['notify'] = new View\Functions('notify');
        $params['search'] = new View\Functions('search');

        $params['footer'] = new View\Functions('footer');
        $params['copyright'] = new View\Functions('copyright');
        $params['errors'] = new View\Functions('errors');
        $params['top'] = new View\Functions('top');
        $params['location_1'] = new View\Functions('location_1');
        $params['location_2'] = new View\Functions('location_2');
        $params['location_3'] = new View\Functions('location_3');
        $params['location_4'] = new View\Functions('location_4');
        $params['location_5'] = new View\Functions('location_5');
        $params['location_6'] = new View\Functions('location_6');
        $params['location_7'] = new View\Functions('location_7');
        $params['location_8'] = new View\Functions('location_8');
        $params['location_9'] = new View\Functions('location_9');
        $params['location_10'] = new View\Functions('location_10');
        $params['location_11'] = new View\Functions('location_11');
        $params['location_12'] = new View\Functions('location_12');
        $params['main_top'] = new View\Functions('main_top');
        $params['left'] = new View\Functions('left');
        $params['right'] = new View\Functions('right');
        $params['h1'] = new View\Functions('h1');
        $params['breadcrumb'] = new View\Functions('breadcrumb');
        $params['notification'] = new View\Functions('notification');
        $params['logo'] = new View\Functions('logo');
        $pageId = \Phpfox_Module::instance()->getPageId();
        $params['body'] = 'id="page_' . $pageId . '" class="' . \Phpfox_Module::instance()->getPageClass() . '"';

        $params['menu_nav'] = new View\Functions('menu_nav');

        $params['main_class'] = (($Template->bIsSample) ? 'force' : '');
        $function = new Functions('');
        if (!$function->checkContent(1) && !$function->checkContent(9)) {
            $params['main_class'] .= ' empty-left';
        }
        if (!$function->checkContent(3) && !$function->checkContent(10)) {
            $params['main_class'] .= ' empty-right';
        }

        $locale = \Phpfox_Locale::instance()->getLang();

        if ($pageId == 'route_flavors_manage') {
            $locale['direction'] = 'ltr';
        }

        $params['html'] = 'xmlns="http://www.w3.org/1999/xhtml" dir="' . $locale['direction'] . '" lang="' . $locale['language_code'] . '"';

        return $this->_env->render('@Theme/' . self::$template . '.html', $params);
    }
}
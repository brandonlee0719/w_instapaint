<?php

namespace Core\View;

class Functions
{
    private $_method;
    private $_extra;

    public function __construct($method, $extra = null)
    {
        $this->_method = $method;
        $this->_extra = $extra;
    }

    public function __toString()
    {

        try {
            $Template = \Phpfox_Template::instance();

            switch ($this->_method) {
                case 'search':
                    \Phpfox::getBlock('search.panel');
                    break;
                case 'js':
                    return \Phpfox::getLib('template')->getFooter();
                    break;
                case 'footer':
                    \Phpfox::getBlock('core.template-menufooter');
                    break;
                case 'copyright':
                    \Phpfox::getBlock('core.template-copyright');
                    break;
                case 'share':
                    \Phpfox::getBlock('feed.form2', ['menu' => true]);
                    break;
                case 'notify':
                    \Phpfox::getBlock('core.template-notification');
                    break;
                case 'menu':
                    \Phpfox::getBlock('core.template-menu');
                    break;
                case 'sticky_bar':
                    \Phpfox::getBlock('core.template-notification');
                    break;
                case 'sticky_bar_sm':
                    \Phpfox::getBlock('core.template-notification-sm');
                    break;
                case 'sticky_bar_xs':
                    \Phpfox::getBlock('core.template-notification-xs');
                    break;
                case 'menu_sub':
                    \Phpfox::getBlock('core.template-menusub');
                    break;
                case 'breadcrumb_menu':
                    \Phpfox::getBlock('core.template-breadcrumbmenu');
                    break;
                case 'menu_nav':
                    echo view('@PHPfox_Core/menu-nav.html');
                    break;
                case 'nav':
                    \Phpfox::getBlock('feed.form2', ['menu' => true]);
                    \Phpfox::getBlock('core.template-notification');
                    \Phpfox::getBlock('core.template-menu');
                    break;
                case 'content':
                    if (\Phpfox_Request::instance()->get('app-pager')) {
                        return $this->_extra;
                    }

                    $isSearch = (\Phpfox_Request::instance()->get('page') ? true : false);
                    if ($isSearch && PHPFOX_IS_AJAX_PAGE) {
                        \Phpfox_Module::instance()->getControllerTemplate();
                        $content = ob_get_contents();
                        ob_clean();
                        return $content;
                    }

                    if (!PHPFOX_IS_AJAX_PAGE) {
                        echo '<div class="_block_' . $this->_method . '">';
                    }

                    if (defined('PHPFOX_IS_TECHIE') && PHPFOX_IS_TECHIE && !\Phpfox::isAdminPanel() && !PHPFOX_IS_AJAX) {
                        $controller = \Phpfox_Module::instance()->getPageId();
                        $css = \Phpfox_Module::instance()->getPageClass();
                        if (!$css) {
                            $css = 'n/a';
                        }
                        $actual_id = $controller;
                        $controller = str_replace('_', '.', $controller);
                        $controller = str_replace('route.', 'route_', $controller);
                        if (isset($_SERVER["CONTENT_TYPE"]) && strtolower($_SERVER["CONTENT_TYPE"]) != 'application/json') {
                            echo '<div id="pf_techie_mode"><span>Route:</span> ' . $controller . ' <span>ID:</span> #' . $actual_id . ' <span>CSS:</span> ' . $css . '</div>';
                        }
                    }

                    $this->_loadBlocks(2);
                    if ($this->_extra) {
                        echo $this->_extra;
                    } else {
                        try {
                            \Phpfox_Module::instance()->getControllerTemplate();
                        } catch (\Exception $e) {
                            exit($e->getMessage());
                        }
                    }
                    $this->_loadBlocks(4);
                    if (!PHPFOX_IS_AJAX_PAGE) {
                        echo '</div>';
                    }
                    if (PHPFOX_IS_AJAX_PAGE) {
                        $content = ob_get_contents();
                        ob_clean();
                        return $content;
                    }
                    break;
                case 'location_1':
                    $this->_loadBlocks(1);
                    break;
                case 'location_2':
                    $this->_loadBlocks(2);
                    break;
                case 'location_3':
                    $this->_loadBlocks(3);
                    break;
                case 'location_4':
                    $this->_loadBlocks(4);
                    break;
                case 'location_5':
                    $this->_loadBlocks(5);
                    break;
                case 'location_6':
                    $this->_loadBlocks(6);
                    break;
                case 'location_7':
                    $this->_loadBlocks(7);
                    break;
                case 'location_8':
                    $this->_loadBlocks(8);
                    break;
                case 'location_9':
                    $this->_loadBlocks(9);
                    break;
                case 'location_10':
                    $this->_loadBlocks(10);
                    break;
                case 'location_11':
                    $this->_loadBlocks(11);
                    break;
                case 'location_12':
                    $this->_loadBlocks(12);
                    break;
                case 'main_top':
                    if (!PHPFOX_IS_AJAX_PAGE) {
                        echo '<div class="_block_top">';
                    }
                    $Template->getLayout('search');
                    if (!PHPFOX_IS_AJAX_PAGE) {
                        echo '</div>';
                    }

                    $this->_loadBlocks(7);
                    break;
                case 'top':
                    $this->_loadBlocks(11);
                    if (!PHPFOX_IS_AJAX_PAGE) {
                        echo '<div class="_block_top">';
                    }
                    $Template->getLayout('search');
                    if (!PHPFOX_IS_AJAX_PAGE) {
                        echo '</div>';
                    }

                    $this->_loadBlocks(7);
                    break;
                case 'errors':
                    $Template->getLayout('error');
                    break;
                case 'left':
                    $this->_loadBlocks(1);
                    $this->_loadBlocks(9);
                    break;
                case 'right':
                    $this->_loadBlocks(3);
                    $this->_loadBlocks(10);
                    break;
                case 'logo':
                    \Phpfox::getBlock('core.template-logo');
                    break;
                case 'breadcrumb':
                    if (!PHPFOX_IS_AJAX_PAGE) {
                        echo '<div class="_block_' . $this->_method . '">';
                    }

                    $bIsDetailPage = false;
                    $fullControllerName = \Phpfox::getLib('module')->getFullControllerName();

                    foreach (
                        [
                            '.view',
                            '.detail',
                            '.edit',
                            '.delete',
                            '.add',
                            '.thread',
                            '.create',
                            '.post',
                            '.upload',
                            '.album',
                        ] as $name
                    ) {
                        if (strpos($fullControllerName, $name)) {
                            $bIsDetailPage = true;
                        }
                    }

                    (($sPlugin = \Phpfox_Plugin::get('core_view_functions_breadcrumb')) ? eval($sPlugin) : false);
                    $Template->assign('bIsDetailPage', $bIsDetailPage);
                    $Template->getLayout('breadcrumb');
                    if (!PHPFOX_IS_AJAX_PAGE) {
                        echo '</div>';
                    }
                    break;
                case 'title':
                    echo $Template->getTitle();
                    break;
                case 'h1':
                    if (!PHPFOX_IS_AJAX_PAGE) {
                        echo '<div class="_block_' . $this->_method . '">';
                    }
                    list($breadcrumbs, $title) = $Template->getBreadCrumb();
                    if (count($title)) {
                        echo '<h1><a href="' . $title[1] . '">' . \Phpfox_Parse_Output::instance()->clean($title[0]) . '</a></h1>';
                    }
                    if (!PHPFOX_IS_AJAX_PAGE) {
                        echo '</div>';
                    }
                    break;
            }

        } catch (\Exception $e) {
            register_shutdown_function(function () use ($e) {
                ob_clean();
                throw new \Exception($e->getMessage(), $e->getCode(), $e);
            });
        }

        return '';
    }

    /**
     * @var array
     */
    private static $blocks  = [];

    public function checkContent($location)
    {
        return $this->_loadBlocks($location, true);
    }

    private function _loadBlocks($location, $checkContent = false)
    {
        if(!isset(self::$blocks[$location])){
            ob_start();
            if ($location == 3) {
                echo \Phpfox_Template::instance()->getSubMenu();
            }
            $blocks = \Phpfox_Module::instance()->getModuleBlocks($location);

            foreach ($blocks as $block) {
                $mClass = $block;
                $aParams = [];
                if (is_array($block) && isset($block['type_id'])) {
                    if ($block['type_id'] == 0) {
                        $mClass = $block['component'];
                        $aParams = $block['params'];
                    } elseif ($block['type_id'] == 1 || $block['type_id'] == 2) {
                        $mClass = [$block['component']];
                        $aParams = $block['params'];
                    }
                }
                \Phpfox::getBlock($mClass, $aParams);
            }
            self::$blocks[$location] = ob_get_clean();

        }

        if($checkContent){
            return !!trim(self::$blocks[$location]);
        }
        if (\Phpfox_Template::instance()->bIsSample) {
            echo '<div class="block_sample" onclick="window.parent.$(\'#location\').val(' . $location
                . '); window.parent.js_box_remove(window.parent.$(\'.js_box\').find(\'.js_box_content\')[0]);">[Block: ' . $location . ']</div>';
        }

        echo '<div class="_block location_' . $location . '" data-location="' . $location . '">';

        echo self::$blocks[$location];

        echo '</div>';
    }
}
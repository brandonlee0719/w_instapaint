<?php

namespace Apps\Core_Photos\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;
use Phpfox_Url;

class Category extends Phpfox_Component
{
    public function process()
    {
        if (!Phpfox::isAdminPanel()) {
            $iCurrentCategory = $this->getParam('sCurrentCategory', null);
            $iParentCategoryId = $this->getParam('iParentCategoryId', 0);
            $aCategories = Phpfox::getService('photo.category')->getForBrowse(null,
                $this->getParam('sPhotoCategorySubSystem', null));
            if (empty($aCategories)) {
                return false;
            }
            if (defined('PHPFOX_IS_USER_PROFILE')) {
                return false;
            }
            $aCallback = $this->getParam('aCallback', false);
            if ($aCallback !== false && is_array($aCategories)) {
                $sHomeUrl = '/' . Phpfox_Url::instance()->doRewrite($aCallback['url_home_array'][0]) . '/' . implode('/',
                        $aCallback['url_home_array'][1]) . '/' . Phpfox_Url::instance()->doRewrite('photo') . '/';
                foreach ($aCategories as $iKey => $aCategory) {
                    $aCategories[$iKey]['url'] = preg_replace('/^http:\/\/(.*?)\/' . Phpfox_Url::instance()->doRewrite('photo') . '\/(.*?)$/i',
                        'http://\\1' . $sHomeUrl . '\\2', $aCategory['url']);
                    if (isset($aCategory['sub'])) {
                        foreach ($aCategory['sub'] as $iSubKey => $aSubCategory) {
                            $aCategories[$iKey]['sub'][$iSubKey]['url'] = preg_replace('/^http:\/\/(.*?)\/' . Phpfox_Url::instance()->doRewrite('photo') . '\/(.*?)$/i',
                                'http://\\1' . $sHomeUrl . '\\2', $aSubCategory['url']);
                        }
                    }
                }
            }

            if (!is_array($aCategories)) {
                return false;
            }
            $this->template()->assign(array(
                    'aCategories' => $aCategories,
                    'iCurrentCategory' => $iCurrentCategory,
                    'iParentCategoryId' => $iParentCategoryId,
                    'sHeader' => _p('categories'),
                )
            );
            return 'block';
        }

        if ($this->getParam('bIsTagSearch') === true) {
            return false;
        }
        $aCallback = $this->getParam('aCallback', null);
        $sCategories = Phpfox::getService('photo.category')->get($this->getParam('anchor', true));
        if ($aCallback !== null) {
            $sCategories = preg_replace('/href=\"(.*?)\/photo\/(.*?)\"/i',
                'href="' . Phpfox_Url::instance()->makeUrl($aCallback['url_home']) . '\\2"', $sCategories);
        }
        $this->template()->assign(array(
                'sHeader' => _p('categories'),
                'sCategories' => $sCategories,
                'bParent' => $this->getParam('parent', true)
            )
        );
        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('photo.component_block_category_clean')) ? eval($sPlugin) : false);
    }
}
<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Page_Component_Controller_Admincp_Add extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $bIsEdit = false;
        $aValidation = array(
            'product_id' => _p('select_product'),
            'title' => _p('missing_title'),
            'title_url' => _p('missing_url_title'),
            'is_active' => _p('specify_page_active'),
            'text' => _p('page_missing_data')
        );

        $oValid = Phpfox_Validator::instance()->set(array('sFormName' => 'js_form', 'aParams' => $aValidation));

        if (($iPageId = $this->request()->getInt('id')) || ($iPageId = $this->request()->getInt('page_id'))) {
            Phpfox::getUserParam('page.can_manage_custom_pages', true);

            $aPage = Phpfox::getService('page')->getForEdit($iPageId);
            if (isset($aPage['page_id'])) {
                $bIsEdit = true;
                if (Phpfox::isModule('tag')) {
                    $aTags = Phpfox::getService('tag')->getTagsById('page', $aPage['page_id']);
                    if (isset($aTags[$aPage['page_id']])) {
                        $aPage['tag_list'] = '';
                        foreach ($aTags[$aPage['page_id']] as $aTag) {
                            $aPage['tag_list'] .= ' ' . $aTag['tag_text'] . ',';
                        }
                        $aPage['tag_list'] = trim(trim($aPage['tag_list'], ','));
                    }
                }
                $aCache = storage()->get('page_cache_' . $aPage['page_id']);

                if (!empty($aCache)) {
                    $aPage['add_menu'] = $aCache->value->add_menu;
                }
                if ($aPage['add_menu'] == null) {
                    $aPage['add_menu'] = 0;
                }

                $this->template()->assign(array(
                        'aForms' => $aPage,
                        'aAccess' => (empty($aPage['disallow_access']) ? null : unserialize($aPage['disallow_access']))
                    )
                );
            }
        }

        if ($aVals = $this->request()->getArray('val')) {
            Phpfox::getLib('parse.input')->allowTitle(Phpfox::getLib('parse.input')->cleanTitle($aVals['title_url']),
                _p('invalid_title'));

            if ($oValid->isValid($aVals)) {
                if ($bIsEdit) {
                    $sMessage = _p('page_successfully_updated');
                    $sReturn = Phpfox::getService('page.process')->update($aPage['page_id'], $aVals, $aPage['user_id']);
                    $aUrl = null;
                } else {
                    $sMessage = _p('successfully_added');
                    $sReturn = Phpfox::getService('page.process')->add($aVals);
                    $aUrl = null;
                }

                if ($sReturn) {
                    return [
                        'redirect' => $this->url()->makeUrl($sReturn, null, $sMessage)
                    ];
                }
            } else {
                $aError = Phpfox_Error::get();
                if (is_array($aError)) {
                    $sError = implode(' ', $aError);
                } else {
                    $sError = $aError;
                }

                return [
                    'error' => $sError
                ];
            }
        }

        try {
            $app = new \Core\App();
            $app->get('phpFox_CKEditor');
            $bCkeditorEnabled = true;
        } catch (Exception $e) {
            $bCkeditorEnabled = false;
        }
        $bUseEditor = Phpfox::getParam('core.allow_html') && $bCkeditorEnabled;

        $this->template()
            ->setSectionTitle('<a href="' . $this->url()->makeUrl('admincp.page') . '">' . _p('custom_pages') . '</a>')
            ->setTitle($bIsEdit ? _p('edit_page') : _p('add_new_page'))
            ->setBreadCrumb($bIsEdit ? _p('edit_page') : _p('add_new_page'))
            ->assign(array(
                    'bUseEditor' => $bUseEditor,
                    'aProducts' => Phpfox::getService('admincp.product')->get(),
                    'aUserGroups' => Phpfox::getService('user.group')->get(),
                    'sCreateJs' => $oValid->createJS(),
                    'sGetJsForm' => $oValid->getJsForm(),
                    'bIsEdit' => $bIsEdit,
                    'aModules' => Phpfox_Module::instance()->getModules(),
                    'bFormIsPosted' => (count($aVals) ? true : false)
                )
            )
            ->setEditor()
            ->setActiveMenu('admincp.appearance.page')
            ->setHeader(array(
                    'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                    'switch_menu.js' => 'static_script',
                    '<script type="text/javascript">var Attachment = {sCategory: "page", iItemId: "' . (isset($aPage['page_id']) ? $aPage['page_id'] : '') . '"};</script>'
                )
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('page.component_controller_admincp_add_clean')) ? eval($sPlugin) : false);
    }
}

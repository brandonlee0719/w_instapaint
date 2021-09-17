<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright       [PHPFOX_COPYRIGHT]
 * @author          phpFox
 * @package         Module_Admincp
 */
class Admincp_Component_Controller_Block_Add extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        Phpfox::getUserParam('admincp.can_add_new_block', true);
        $bIsEdit = false;

        if (($iEditId = $this->request()->getInt('id')) || ($iEditId = $this->request()->getInt('block_id'))) {
            $aRow = Phpfox::getService('admincp.block')->getForEdit($iEditId);
            $bIsEdit = true;

            $this->template()->assign(array(
                    'aForms' => $aRow,
                    'aAccess' => (empty($aRow['disallow_access']) ? null : unserialize($aRow['disallow_access']))
                )
            );
        }

        $aValidation = array(
            'title' => [
                'def' => 'required',
                'title' => _p('block_title_is_required')
            ],
            'type_id' => [
                'def' => 'required',
                'title' => _p('block_type_is_required')
            ],
        );

        $oValid = Phpfox_Validator::instance()->set(array('sFormName' => 'js_form', 'aParams' => $aValidation));

        if ($aVals = $this->request()->getArray('val')) {
            if ($aVals['type_id'] === '0') {
                $aValidation['component'] = [
                    'def' => 'required',
                    'title' => _p('component_is_required')
                ];
                $oValid = Phpfox_Validator::instance()->set(array('sFormName' => 'js_form', 'aParams' => $aValidation));
            }
            if ($oValid->isValid($aVals)) {
                if ($bIsEdit) {
                    $sMessage = _p('successfully_updated');
                    Phpfox::getService('admincp.block.process')->update($aRow['block_id'], $aVals);
                } else {
                    $sMessage = _p('block_successfully_added');
                    Phpfox::getService('admincp.block.process')->add($aVals);
                }

                $aUrl = array(
                    'block',
                    'm_connection' => empty($aVals['m_connection']) ? 'site_wide' : $aVals['m_connection']
                );

                $this->url()->send('admincp', $aUrl, $sMessage);
            }
        }

        if (Phpfox::getParam('core.enabled_edit_area')) {
            $this->template()->setHeader(array(
                    'editarea/edit_area_full.js' => 'static_script',
                    '<script type="text/javascript">				
						editAreaLoader.init({
							id: "source_code"	
							,start_highlight: true
							,allow_resize: "both"
							,allow_toggle: false
							,word_wrap: false
							,language: "en"
							,syntax: "php"
						});		
					</script>'
                )
            );
        }

        $aStyles = Phpfox::getService('theme.style')->getStyles();
        if ($bIsEdit) {
            foreach ($aStyles as $iKey => $aStyle) {
                if (isset($aRow['style_id']) && isset($aRow['style_id'][$aStyle['style_id']])) {
                    $aStyles[$iKey]['block_is_selected'] = $aRow['style_id'][$aStyle['style_id']];
                }
            }
        }

        $this->template()->assign(array(
            'aProducts' => Phpfox::getService('admincp.product')->get(),
            'aControllers' => Phpfox::getService('admincp.component')->get(true),
            'aComponents' => Phpfox::getService('admincp.component')->get(),
            'aUserGroups' => Phpfox::getService('user.group')->get(),
            'sCreateJs' => $oValid->createJS(),
            'sGetJsForm' => $oValid->getJsForm(),
            'bIsEdit' => $bIsEdit,
            'aStyles' => $aStyles
        ))
            ->setTitle(_p('block_manager'))
            ->setBreadCrumb(_p('block_manager'), $this->url()->makeUrl('admincp.block'))
            ->setBreadCrumb(($bIsEdit ? _p('editing') . ': ' . (empty($aRow['m_connection']) ? _p('site_wide') : $aRow['m_connection']) . (empty($aRow['component']) ? '' : '::' . rtrim(str_replace('|',
                        '::', $aRow['component']),
                        '::')) . (empty($aRow['title']) ? '' : ' (' . Phpfox_Locale::instance()->convert($aRow['title']) . ')') : _p('add_new_block')),
                $this->url()->makeUrl('admincp.block.add'), true)
            ->setActiveMenu('admincp.appearance.block')
            ->setTitle(_p('add_new_block'));
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('admincp.component_controller_block_add_clean')) ? eval($sPlugin) : false);
    }
}

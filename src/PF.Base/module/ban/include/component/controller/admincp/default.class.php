<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Ban_Component_Controller_Admincp_Default
 */
class Ban_Component_Controller_Admincp_Default extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $aBanFilter = $this->getParam('aBanFilter');
        $sFindValue = '';

        if (($iDeleteId = $this->request()->getInt('delete'))) {
            if (Phpfox::getService('ban.process')->delete($iDeleteId)) {
                $this->url()->send($aBanFilter['url'], null, _p('filter_successfully_deleted'));
            }
        }

        $aValidation = $this->getParam('aValidation',null);
        $oValidator = Phpfox::getLib('validator');
        if($aValidation){
            $oValidator->set(['sFormName' => 'js_form', 'aParams' => $aValidation]);
        }

        if (($sBanValue = $this->request()->get('find_value'))) {
            $aBan = $this->request()->getArray('aBan');
            $aVals = array_merge([
                'type_id' => $aBanFilter['type'],
                'find_value' => $sFindValue = $sBanValue,
                'replacement' => $this->request()->get('replacement', null)
            ], $aBan);
            $isValid =true;

            if($aValidation and !$oValidator->isValid($aVals)){
                $isValid = false;
            }
            if ($isValid and Phpfox::getService('ban.process')->add($aVals, $aBanFilter)) {
                $this->url()->send($aBanFilter['url'], null, _p('filter_successfully_added'));
            }
        }
        $aFilters = Phpfox::getService('ban')->getFilters($aBanFilter['type']);

        foreach ($aFilters as $iKey => $aFilter) {
            $aFilters[$iKey]['s_user_groups_affected'] = '';
            if (is_array($aFilter['user_groups_affected'])) {
                foreach ($aFilter['user_groups_affected'] as $aGroup) {
                    $aFilters[$iKey]['s_user_groups_affected'] .= Phpfox_Locale::instance()->convert($aGroup['title']) . ', ';
                }
                $aFilters[$iKey]['s_user_groups_affected'] = rtrim($aFilters[$iKey]['s_user_groups_affected'], ', ');
            }
        }

        $this->template()->setTitle(_p('ban') . ': ' . $aBanFilter['title'])
            ->setBreadCrumb(_p('ban_filters'))
            ->setSectionTitle(_p('ban') . ': ' . $aBanFilter['title'])
            ->setActiveMenu('admincp.maintain.ban')
            ->setActionMenu([
                _p('usernames') => [
                    'url' => $this->url()->makeUrl('admincp.ban.username'),
                ],
                _p('emails') => [
                    'url' => $this->url()->makeUrl('admincp.ban.email'),
                ],
                _p('ip_address') => [
                    'url' => $this->url()->makeUrl('admincp.ban.ip'),
                ],
                _p('display') => [
                    'url' => $this->url()->makeUrl('admincp.ban.display'),
                ],
                _p('words') => [
                    'url' => $this->url()->makeUrl('admincp.ban.word'),
                ]
            ])
            ->assign([
                'sFindValue'=>$sFindValue,
                'aFilters' => $aFilters,
                'aBanFilter' => $aBanFilter
            ]);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ban.component_controller_admincp_default_clean')) ? eval($sPlugin) : false);
    }
}

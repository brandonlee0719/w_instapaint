<?php

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Subscribe_Component_Controller_Admincp_Add
 */
class Subscribe_Component_Controller_Admincp_Add extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
        $bIsEdit = false;
        if (($iId = $this->request()->getInt('id'))) {
            if (($aPackage = Phpfox::getService('subscribe')->getForEdit($iId))) {
                $bIsEdit = true;
                $this->template()->assign([
                    'aForms'=> $aPackage,
                    'sPhraseTitle'=>$aPackage['title'],
                    'sPhraseDescription'=>$aPackage['description'],
                ]);
                $this->setParam('currency_value_val[cost]', unserialize($aPackage['cost']));
                if (!empty($aPackage['recurring_cost'])) {
                    $this->setParam('currency_value_val[recurring_cost]', unserialize($aPackage['recurring_cost']));
                }
            }
        }

        if (($aVals = $this->request()->getArray('val'))) {

            if ($bIsEdit) {
                if (Phpfox::getService('subscribe.process')->update($aPackage['package_id'], $aVals)) {
                    $this->url()->send('admincp.subscribe.add', array('id' => $aPackage['package_id']),
                        _p('package_successfully_update'));
                }
            } else {
                if (Phpfox::getService('subscribe.process')->add($aVals)) {
                    $this->url()->send('admincp.subscribe', null, _p('package_successfully_added'));
                }
            }
        }

		$this->template()
            ->setTitle(($bIsEdit ? _p('editing_subscription_package') . ': ' . $aPackage['title'] : _p('create_new_subscription_package')))
            ->setBreadCrumb(_p('Members'),'#')
            ->setBreadCrumb(_p('subscriptions'),$this->url()->makeUrl('admincp.subscribe'))
			->setBreadCrumb(($bIsEdit ? _p('editing') . ': ' . Phpfox_Locale::instance()->convert($aPackage['title']) : _p('create_new_subscription_package')), null, true)
            ->setActiveMenu('admincp.member.subscribe')
			->assign(array(
					'aUserGroups' => Phpfox::getService('user.group')->get(),
					'bIsEdit' => $bIsEdit,
				)
			);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('subscribe.component_controller_admincp_add_clean')) ? eval($sPlugin) : false);
	}
}
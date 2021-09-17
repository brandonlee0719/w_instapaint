<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Controller_Admincp_Cancellations_Feedback
 */
class User_Component_Controller_Admincp_Cancellations_Feedback extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{		
		$this->_setMenuName('admincp.user.cancellations.feedback');

        // process sort params
        $iOptionId  = $this->request()->get('option_id');
        $sSort = str_replace('+', ' ', $this->request()->get('sort', ''));
		$aFeedbacks = Phpfox::getService('user.cancellations')->getFeedback($sSort, $iOptionId);
		
		$this->template()->setTitle(_p('view_feedback_on_cancellations'))
            ->setBreadCrumb(_p('Members'),'#')
			->setBreadCrumb(_p('cancelled_members'), $this->url()->makeUrl('admincp.user.cancellations.feedback'))
            ->setActiveMenu('admincp.member.cancellations')
            ->setActionMenu([
                _p('manage_cancellation_options') => [
                    'url' => $this->url()->makeUrl('admincp.user.cancellations.manage'),
                ],
                _p('add_new_option') => [
                    'url' => $this->url()->makeUrl('admincp.user.cancellations.add'),
                    'class'=>'popup',
                ],

            ])
			->assign(array(
					'aFeedbacks' => $aFeedbacks,
                    'sCurrent' => $sSort
				)
			);		
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('user.component_controller_admincp_add_clean')) ? eval($sPlugin) : false);
	}
}

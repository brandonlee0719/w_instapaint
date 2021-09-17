<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox_Component
 * @version 		$Id: controller.class.php 103 2009-01-27 11:32:36Z Raymond_Benc $
 */
class Notification_Component_Controller_Index extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		Phpfox::isUser(true);
		
		$iPage = $this->request()->getInt('page');
		$iPageTotal = 100;
        Phpfox_Pager::instance()->set(array('page' => $this->search()->getPage(), 'size' => $this->search()->getDisplay(), 'count' => $this->search()->browse()->getCount()));
		list($iCnt, $aNotifications) = Phpfox::getService('notification')->getForBrowse($iPage, $iPageTotal);
        Phpfox_Pager::instance()->set(array('page' => $iPage, 'size' => $iPageTotal, 'count' => $iCnt));
        if (!count($aNotifications) && PHPFOX_IS_AJAX){
            //If no item more, show empty page
            $this->template()->assign([
                'bNoContent' => true
            ]);
        }
		$this->template()->setTitle(_p('notifications'))
			->setBreadCrumb(_p('notifications'), $this->url()->makeUrl('notification'))
			->setHeader(array(	
					'view.js' => 'module_notification'
				)
			)
			->assign(array(
					'aNotifications' => $aNotifications
				)
			);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('notification.component_controller_index_clean')) ? eval($sPlugin) : false);
	}
}
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
 * @package  		Module_Core
 * @version 		$Id: index-member.class.php 5908 2013-05-13 07:28:31Z Raymond_Benc $
 */
class Core_Component_Controller_Index_Member extends Phpfox_Component 
{
	/**
	 * Controller
	 */
	public function process()
	{
		if ($sPlugin = Phpfox_Plugin::get('core.component_controller_index_member_start'))
		{
		    eval($sPlugin);
		}

		if ($this->request()->segment(1) != 'hashtag') {
			Phpfox::isUser(true);
		}
		
		if ($this->request()->get('req3') == 'customize')
		{				
				define('PHPFOX_IN_DESIGN_MODE', true);
				define('PHPFOX_CAN_MOVE_BLOCKS', true);	
				
				if (($iTestStyle = $this->request()->get('test_style_id')))
				{
					if (Phpfox_Template::instance()->testStyle($iTestStyle))
					{
						
					}
				}
				
				$aDesigner = array(
					'current_style_id' => Phpfox::getUserBy('style_id'),
					'design_header' => _p('customize_dashboard'),
					'current_page' => $this->url()->makeUrl(''),
					'design_page' => $this->url()->makeUrl('core.index-member', 'customize'),
					'block' => 'core.index-member',				
					'item_id' => Phpfox::getUserId(),
					'type_id' => 'user'
				);
				
				$this->setParam('aDesigner', $aDesigner);	
				
				$this->template()->setPhrase(array(
								'are_you_sure'
							)
						)
						->setHeader('cache', array(
								'design.js' => 'module_theme',
								'select.js' => 'module_theme'
							)
						);				
		}
		else 
		{
			$this->template()->setHeader('cache', array(						
						'sort.js' => 'module_theme',
						'design.js' => 'module_theme',
					)
				);
		}

		$this->template()->setHeader('cache', array(
					'jquery/plugin/jquery.highlightFade.js' => 'static_script',
					'jquery/plugin/jquery.scrollTo.js' => 'static_script'
				)
			)
			->setEditor(array(
					'load' => 'simple'					
			)
		);
	}
}
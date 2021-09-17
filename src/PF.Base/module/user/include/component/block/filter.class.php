<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Block_Filter
 */
class User_Component_Block_Filter extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$aSearch = $this->request()->getArray('search');
		if(is_array($aSearch) && !empty($aSearch))
		{
			$this->template()->assign(array(
					'sCountryISO' => isset($aSearch['country']) ? Phpfox::getLib('parse.output')->htmlspecialchars($aSearch['country']) : '',
					'sCountryChildId' => isset($aSearch['country_child_id']) ? Phpfox::getLib('parse.output')->htmlspecialchars($aSearch['country_child_id']) : ''
				)
			);
		}

		$this->template()->assign([
			'sHeader' => _p('find_friends')
		]);
		return 'block';
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('user.component_block_filter_clean')) ? eval($sPlugin) : false);
	}
}

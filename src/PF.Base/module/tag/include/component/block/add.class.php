<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Tag_Component_Block_Add
 */
class Tag_Component_Block_Add extends Phpfox_Component 
{
	/**
	 * Controller
	 */
	public function process()
	{

        if (!Phpfox::getParam('tag.enable_tag_support')) {
            return false;
        }

		$this->template()->assign(array(
				'sTagType' => $this->getParam('sType'),
				'bSeparate' => $this->getParam('separate', true),
				'iItemId' => $this->getParam('tag_id')
			)
		);	
	}
	
	public function clean()
	{
		$this->template()->clean(array(
				'sTagType',
				'bSeparate',
				'iItemId'
			)
		);	
	}
}

<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Block_ShowSpamQuestion
 */
class User_Component_Block_ShowSpamQuestion extends Phpfox_Component 
{
	/**
	 * Controller
	 */
	public function process()
	{
		$aQuestions = Phpfox::getService('user')->getSpamQuestions();
		if (empty($aQuestions))
		{
			return false;
		}
		if (Phpfox::getParam('user.require_all_spam_questions_on_signup') == false)
		{
			$aQuestions = array($aQuestions[array_rand($aQuestions)]);
		}
		
		// Hide the url to these images
		$oServ = Phpfox::getService('core');
		foreach ($aQuestions as $iKey => $aQuestion)
		{
			$sHash = $oServ->getHashForImage($aQuestion['image_path']);
			$aQuestions[$iKey]['hash'] = $sHash;
		}
		
		$this->template()->assign(array(
				'aQuestions' => $aQuestions
			)
		);
        return null;
	}
}

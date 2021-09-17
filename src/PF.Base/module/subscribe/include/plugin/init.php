<?php
defined('PHPFOX') or exit('NO DICE!');

if (!PHPFOX_IS_AJAX)
{
	$mRedirectId = Phpfox::getService('subscribe.purchase')->getRedirectId();
	if (is_numeric($mRedirectId) && $mRedirectId > 0)
	{
		Phpfox_Url::instance()->send('subscribe.register', array('id' => $mRedirectId), _p('please_complete_your_purchase'));
	}

    $mRedirectId = Phpfox::getService('subscribe.purchase')->isCompleteSubscribe();
	if (is_numeric($mRedirectId) && $mRedirectId > 0)
	{
		Phpfox_Url::instance()->send('subscribe.register', array('id' => $mRedirectId), _p('please_complete_your_purchase'));
	}
}
?>
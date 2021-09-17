<?php

$this->_upgradeDatabase('3.3.0beta2');

$this->_db()->update(Phpfox::getT('user_group_setting'), array('is_hidden' => '1'), 'name = \'allow_delete_every_message\'');
$this->_db()->update(Phpfox::getT('user_group_setting'), array('is_hidden' => '1'), 'name = \'can_use_privacy_settings\'');

$bCompleted = true;

?>
<?php

if ($this->_sController == 'index-member' && Phpfox::isAdmin() && request()->get('preview')) {
	$this->_sController = 'index-visitor';
	\Phpfox::getService('user.auth')->reset();
}
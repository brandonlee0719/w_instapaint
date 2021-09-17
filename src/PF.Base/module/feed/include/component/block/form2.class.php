<?php
defined('PHPFOX') or exit('NO DICE!');

class Feed_Component_Block_Form2 extends Phpfox_Component {
	public function process() {
		if (!Phpfox::isUser()) {
			return false;
		}

		$this->template()->assign([
			'bShowMenu' => $this->getParam('menu', false)
		]);
        return null;
	}

	public function clean() {
		$this->template()->clean('bShowMenu');
		$this->clearParam('menu');
	}
}
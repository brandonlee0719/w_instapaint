<?php
defined('PHPFOX') or exit('NO DICE!');


class Admincp_Component_Controller_Setting_Redirection extends Phpfox_Component {
    
	public function process() {
		$f = PHPFOX_DIR_SETTINGS . 'redirection.sett.php';
		$enabled = (file_exists($f) ? true : false);
		if ($this->request()->isPost()) {
			$m = _p('Successfully enabled redirection!');
			if ($enabled) {
				$m = _p('Successfully disabled redirection!');
				unlink($f);
			} else {
				file_put_contents($f, "<?php\n");
			}

			$this->url()->send('admincp.setting.redirection', null, $m);
		}

		$this->template()->setTitle(_p('URL Match'))
            ->setBreadCrumb(_p('Settings'),'#')
            ->setBreadCrumb(_p('URL Match'))
			->setSectionTitle(_p('URL Match'))
            ->setActiveMenu('admincp.setting.redirection')
			->assign([
				'enabled' => $enabled,
				'info' => _p('If you enable this feature it will redirect a URL if the hostname does not match your active hostname.')
			]);
	}
}
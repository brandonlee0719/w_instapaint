<?php

class Admincp_Component_Controller_App_Ping extends Phpfox_Component {
	public function process() {

		$url = $this->request()->get('url');
		if (substr($url, 0, 8) != 'https://') {
			exit('p("Not an app")');
		}
		$file = PHPFOX_DIR_FILE . 'static' . PHPFOX_DS . md5($url) . '.log';
		if (!file_exists($file)) {
			$out = '';
		}
		else {
			$out = file_get_contents($file);
		}

		header('Content-type: application/javascript');
		echo "var js = " . json_encode(['output' => $out]) . ";";
		echo "$('#debug_info').html(js.output);";
		echo "setTimeout(runPing, 400);";
		exit;
	}
}
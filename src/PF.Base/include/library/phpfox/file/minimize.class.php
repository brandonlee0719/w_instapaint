<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

use MatthiasMullie\Minify;

class Phpfox_File_Minimize {
	/**
	 * @var Minify\CSS
	 */
	private $_minify;

	/**
	 * @return $this
	 */
	public static function instance() {
		return Phpfox::getLib('file.minimize');
	}

	public function css($path, $files) {
		$content = '';
        $home = home();
		foreach ($files as $file) {
			$sContent = file_get_contents($file);
			$sContent = preg_replace_callback('/url\([\'"](.*?)[\'"]\)/is', function($aMatches) use($file,$home) {
				$sMatch = trim($aMatches[1]);
				$sMatch = str_replace('../../../../', '', $sMatch);
				$sMatch = trim($sMatch);

				$path = (substr($sMatch, 0, 7) == 'PF.Base' ? $home . $sMatch : str_replace('../', str_replace(PHPFOX_PARENT_DIR, $home, dirname(dirname($file)) . '/'), $sMatch));
				if(substr($path, 0,7) == 'http://'){
				    $path  = substr($path, 5);
                }elseif(substr($path, 0,8) == 'https://'){
                    $path  = substr($path, 6);
                }
				return 'url(\'' . $path . '\')';
			}, $sContent);
			$sContent = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $sContent);
			$sContent = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $sContent);

			$content .= $sContent;
		}

		file_put_contents($path, $content);

		return true;
	}

	public function _replaceImages($aMatches) {
		$sMatch = trim($aMatches[1]);
		$sMatch = str_replace('../', '', $sMatch);

		d($aMatches);

		return 'url(\'' . Phpfox_Template::instance()->getStyle('image', $sMatch) . '\')';
	}

	public function js($path, $content, $extra = '') {
		$this->_minify = new Minify\JS();

		if (!is_array($content)) {
			$content = [$content];
		}

		foreach ($content as $file) {
			$this->_minify->add($file);
		}

		$data = $this->_minify->minify();

		file_put_contents($path, $data);

        if ($extra)
        {
            file_put_contents($path, $extra, FILE_APPEND);
        }

		return true;
	}
}
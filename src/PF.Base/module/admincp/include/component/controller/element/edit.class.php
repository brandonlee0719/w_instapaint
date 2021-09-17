<?php
defined('PHPFOX') or exit('NO DICE!');

class Admincp_Component_Controller_Element_Edit extends Phpfox_Component {
    
	public function process() {
		$controller = base64_decode($this->request()->get('controller'));
		list($module, $controller) = explode('.', $controller);
		$name = $module  . '.controller.' . $controller;

		if (($saved = $this->request()->get('content'))) {
			$val = $this->request()->get('val');

			Phpfox::getService('theme.template.process')->update([
				'product_id' => 'phpfox',
				'type' => 'meta',
				'theme' => 'default',
				'name' => $module  . '.' . $controller,
				'text' => json_encode($val)
			]);

			Phpfox::getService('theme.template.process')->update([
				'product_id' => 'phpfox',
				'type' => 'controller',
				'theme' => 'default',
				'name' => $name,
				'text' => $saved
			]);

			return [
				'saved' => true
			];
		}

		if (strpos($name, '/')) {
			$name = explode('/', $name)[0];
		}

		$file = $this->template()->getTemplateFile($name);
		$data = (is_array($file) ? $file[0] : file_get_contents($file));

		$data = str_replace("\r\n", "\n", $data);

		$aParts = explode('?>', $data);
		if (isset($aParts[1]) && preg_match('/PHPFOX/', $aParts[0]))
		{
			$data = ltrim($aParts[1]);
		}

		$this->template()->setBreadCrumb(_p('page_editor'), $this->url()->makeUrl('current'), true);
		$this->template()->assign([
			'url' => $this->url()->makeUrl('current'),
			'data' => $data
		]);
        return null;
	}
}
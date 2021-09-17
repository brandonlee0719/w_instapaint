<?php

class Admincp_Component_Controller_App_Add extends Phpfox_Component
{
    public function process()
    {
        $type = null;
        $file = null;
        $auth_id = null;
        $auth_key = null;
        $extra = null;
        $productId = null;
        $appDir = null;
        $name = null;
        $vendor = null;
        $version = null;
        $zip = $this->request()->get('zip');

        if ($this->request()->get('t')) {
	        $token = (new Core\Home(PHPFOX_LICENSE_ID, PHPFOX_LICENSE_KEY))->install_token(['token' => $this->request()->get('t')]);
	        foreach ($token as $key => $value) {
		        $this->request()->set($key, $value);
	        }
        }

        if ($this->request()->get('type') == 'in-app') {
            $product = json_decode($this->request()->get('product'));
            $app = (new Core\App())->getByInternalId($this->request()->get('parent_id'));

            $this->url()->send('admincp.app', ['id' => $app->id, 'child_id' => $product->id]);
            exit;
        }

	    if (isset($_SERVER['REDIRECT_STATUS']) && $_SERVER['REDIRECT_STATUS'] == '403') {
		    parse_str(str_replace('/admincp/app/add/?', '&', $_SERVER['REQUEST_URI']), $params);
		    foreach ($params as $key => $value) {
			    $this->request()->set($key, $value);
		    }
	    }

        if (isset($_SERVER['HTTP_X_FILE_NAME']) || $this->request()->get('download')) {
            $downloadUrl = $this->request()->get('download', '', false);


            $type = $this->request()->get('type');
            $extra = $this->request()->get('product');
            $product = json_decode($extra, true);
            $appDir = isset($product['apps_dir']) ? $product['apps_dir'] : null;
            $productId = isset($product['id']) ? $product['id'] : null;
            $dir = PHPFOX_DIR_FILE . 'static' . PHPFOX_DS . uniqid() . PHPFOX_DS;
            $auth_id = $this->request()->get('auth_id');
            $auth_key = $this->request()->get('auth_key');

	        if ($type == 'theme') {
		        if ($this->request()->get('t')) {
			        echo '
		                <form id="verify" target="_top" method="post" action="' . $this->url()->makeUrl('admincp.theme.add') . '">
		                    <input type="hidden" name="download" value="' . $downloadUrl . '">
		                </form>
		                <script>
		                    window.document.getElementById(\'verify\').submit();
		                </script>
		            ';
			        exit;
		        }
		        url()->send('/admincp/theme/add', ['download' => urlencode($downloadUrl)]);
	        }

            if ($zip == null or empty($zip)) {
                if (!is_dir($dir)) {
                    if (!mkdir($dir, 0777, true)) {
                        exit('Could not write to ' . dirname($dir));
                    }
                    chmod($dir, 0777);
                }
            }

            if ($zip === null or empty($zip)) {
                $zip = $dir . 'import.zip';
                if (isset($_FILES['ajax_upload']) && file_exists($_FILES['ajax_upload']['tmp_name'])) {
                    file_put_contents($zip, file_get_contents($_FILES['ajax_upload']['tmp_name']));
                } else {
                    file_put_contents($zip, file_get_contents('php://input'));
                }
            }

            if ($downloadUrl) {


                $zip = $dir . 'import.zip';
                $ch = curl_init($downloadUrl);

                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_MAXREDIRS      => 3,
                    CURLOPT_TIMEOUT        => 120,
                ]);

                $content = curl_exec($ch);

                if ($error = curl_errno($ch)) {
                    exit(curl_error($ch));
                }
                curl_close($ch);
                file_put_contents($zip, $content);
            }
        }
        if (!empty($zip)) {
            $archive = new ZipArchive();
            $zipStatus = $archive->open($zip, ZipArchive::CHECKCONS);
            if ($zipStatus !== true) {
                Phpfox::addMessage(_p('Not a valid zip file.'));
                return [
                    'redirect' => $this->url()->makeUrl('admincp.apps')
                ];
            }
            $json = $archive->getFromName('package.json');
            $locateName = null;
            $configWalk = [
                'package.json'  => '',
                '/package.json' => '',
                '/app/Install.php' => 'app',
                'app/Install.php'  => 'app',
                'Install.php'      => 'app',
                '/Install.php'     => 'app',

            ];

            foreach ($configWalk as $tempLocaleName => $tempType) {
                if (false !== $archive->locateName($tempLocaleName)) {
                    $locateName = $tempLocaleName;
                    if ($tempType != '') {
                        $type = $tempType;
                    }
                    break;
                }
            }

            if (!$locateName) {
                $tempLocateName = $archive->getNameIndex(0);
                if (substr($tempLocateName, -9) == '.zip.json') {
                    $locateName = $tempLocateName;
                    $type = 'theme';
                }
                else if (substr($tempLocateName, -4) == '.xml') {
                    $locateName = $tempLocateName;
                    $type = 'language';
                }
            }

            if ($locateName) {
                $data = json_decode($archive->getFromName($locateName), true);

	            if ($type == 'theme' && isset($data['name'])) {
		            Phpfox_Template::instance()->setTemplate('blank');
		            $this->template()->assign([
			           'error' => 'This theme is incompatible with this products version.'
		            ]);

		            return false;
	            }

                if (!$type) {
                    $type = $data['type'];
                }

	            if ($type == 'app' && isset($data['type']) && $data['type'] == 'product') {
		            $type = 'module';
	            }

                if (!empty($data['id']) ) {
                    $productId = !empty($data['id']) ? $data['id'] : null;
                }

                if (!empty($data['name'])) {
                    $name = !empty($data['name']) ? $data['name'] : null;
                }

                if (!empty($data['version'])) {
                    $version = !empty($data['version']) ? $data['version'] : null;
                }

                if (!empty($data['vendor'])) {
                    $vendor = strip_tags(!empty($data['vendor']) ? $data['vendor'] : '');
                }
                if (!empty($data['apps_dir'])) {
                    $appDir = strip_tags(!empty($data['apps_dir']) ? $data['apps_dir'] : '');
                }
            }
	        
            $archive->close();
            $urlParams = [
                'zip' => $zip,
                'type' => $type,
                'id' => $productId,
                'name' => $name,
                'vendor' => $vendor,
                'version' => $version,
                'auth_id' => $auth_id,
                'auth_key' => $auth_key,
                'apps_dir' => base64_encode($appDir),
                'extra_info' => base64_encode($extra),
            ];
            if ($this->request()->getHeader('X-Requested-With')) {
                return [
                    'redirect' => $this->url()->makeUrl('admincp.store.verify', $urlParams),
                ];
            } else {
	            if ($this->request()->get('t')) {
		            $form = '';
		            foreach ($urlParams as $key => $value) {
			            $form .= '<input type="hidden" name="' . $key . '" value="' . $value . '">';
		            }
		            echo '
		                <form id="verify" target="_top" method="post" action="' . $this->url()->makeUrl('admincp.store.verify') . '">
		                    ' . $form . '
		                </form>
		                <script>
		                    window.document.getElementById(\'verify\').submit();
		                </script>
		            ';
		            exit;
	            }

                exit('<script>window.top.location.href = \'' . $this->url()->makeUrl('admincp.store.verify', $urlParams) . '\';</script>');
            }
        }

        if (($val = $this->request()->getArray('val'))) {
            $App = (new Core\App())->make($val['name']);

            Phpfox::addMessage(_p('App successfully created.'));
            Phpfox_Cache::instance()->remove();

            return [
                'redirect' => $this->url()->makeUrl('admincp.app', ['id' => $App->id])
            ];
        }

        $this->template()->setBreadCrumb(_p('New App'), $this->url()->current(), true);
        return null;
    }
}
<?php
defined('PHPFOX') or exit('NO DICE!');
define('PHPFOX_APP_INSTALLING', true);
/**
 * Class Admincp_Component_Controller_Store_Ftp
 */
class Admincp_Component_Controller_Store_Ftp extends Phpfox_Component
{
    public function process()
    {
        $listMethod = [
            "file_system" => _p('file_system'),
            "ftp" => _p('ftp'),
            "sftp" => _p('SFTP with password'),
            "key" => _p('SFTP with key'),
        ];

        $currentUploadMethod = Phpfox::getParam('core.upload_method');
        $currentHostName = Phpfox::getParam('core.ftp_host_name');
        $currentPort = Phpfox::getParam('core.ftp_port');
        $currentUsername = Phpfox::getParam('core.ftp_user_name');
        $currentPassword = Phpfox::getParam('core.ftp_password');
        $type = $this->request()->get('type');
        $productName = $this->request()->get('productName');
        $productId = $this->request()->get('productId');
        $appDir = $this->request()->get('apps_dir');
        $extra_info = $this->request()->get('extra_info');
        $extra = json_decode(base64_decode($extra_info), true);

	    $this->template()->setSectionTitle('<a href="' . $this->url()->current() . '">' . _p('Install Method') . '</a>');

        $this->template()->assign([
            'productId' => $productId,
            'listMethod' => $listMethod,
            'currentUploadMethod' => $currentUploadMethod,
            'currentHostName' => $currentHostName,
            'currentPort' => $currentPort,
            'currentUsername' => $currentUsername,
            'currentPassword' => $currentPassword,
            'type' => $type,
            'productName' => $productName,
            'apps_dir' => $appDir,
            'extra_info' => $extra_info,
            'targetDirectory' => $this->request()->get('targetDirectory')
        ]);

        //get account info
        if ($aVals = $this->request()->getArray('val')) {
            //update setting value
            Phpfox::getService('admincp.store.verify')->updateSetting($aVals);
            $aVals['extra'] = json_decode(base64_decode($aVals['extra_info']), true);
            if (isset($_FILES["fileprivate"]["tmp_name"]) && !empty($_FILES["fileprivate"]["tmp_name"])) {
                $aVals['key'] = file_get_contents($_FILES['fileprivate']['tmp_name']);
            }
            $manager = new \Core\Installation\Manager($aVals);
            if ($manager->verifyFtpAccount()) {
                try {

	                if (is_numeric($aVals['productName'])) {
		                if (is_numeric($productName)) {
			                foreach ((new \Core\App())->all() as $app) {
				                if (isset($app->store_id) && $app->store_id == $aVals['productName']) {
					                $aVals['productName'] = $app->id;
					                break;
				                }
			                }
		                }
	                }
                    $url = $manager->install($aVals);
                    echo '<script>window.top.location.href = \'' . $url . '\';</script>';
                    exit;
                } catch (\Exception $ex) {
	                if (PHPFOX_DEBUG) {
		                throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
	                }
                    return \Phpfox_Error::set($ex->getMessage());
                }

            } else {
                if (Phpfox_Error::isPassed()) {
                    Phpfox_Error::set(_p('Your ftp account doesn\'t work'));
                }
                return false;
            }
        }
        return null;
    }
}
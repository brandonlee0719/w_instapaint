<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Admincp_Component_Controller_Trial_Index extends Phpfox_Component
{
    public function process() {

        if (defined('PHPFOX_TRIAL_MODE')) {
            $this->url()->send('admincp');
        }

        if (PHPFOX_LICENSE_ID != '') {
            $this->url()->send('admincp');
        }

        if (($val = $this->request()->get('val'))) {
            $Home = new Core\Home($val['license_id'], $val['license_key']);
            $response = $Home->verify([
                'url' => Phpfox::getParam('core.path')
            ]);
            if (isset($response->license)) {
                $data = "<?php define('PHPFOX_LICENSE_ID', '{$val['license_id']}'); define('PHPFOX_LICENSE_KEY', '{$val['license_key']}');";
                $data .= "\n\nif (!defined('PHPFOX_PACKAGE_ID')) {define('PHPFOX_PACKAGE_ID', '{$response->license->package_id}');}";
                file_put_contents(PHPFOX_DIR_SETTINGS . 'license.sett.php', $data);

                $this->url()->send('admincp', null, 'Successfully upgraded to a licensed version.');
            }
        }

        $this->template()->setTitle('Upgrade to Licensed Version')
            ->setBreadCrumb('Upgrade to Licensed Version', $this->url()->makeUrl('admincp.trial'));
    }
}
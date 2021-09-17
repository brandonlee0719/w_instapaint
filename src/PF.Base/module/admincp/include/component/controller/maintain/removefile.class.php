<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Admincp_Component_Controller_Maintain_Removefile
 */
class Admincp_Component_Controller_Maintain_Removefile extends Phpfox_Component
{

    public function process()
    {
        $aFiles = Phpfox::getService('admincp.maintain.deletefiles')->getListFiles();
        $ssh = $this->request()->get('ssh');
        if (count($aFiles) && $ssh) {
            header("Content-type: text/plain");
            header("Content-Disposition: attachment; filename=delete_file.sh");
            foreach ($aFiles as $sFile) {
                echo 'rm -rf ' . dirname(PHPFOX_DIR) . PHPFOX_DS . $sFile . "\n";
            }
            exit();
        }
        $currentHostName = Phpfox::getParam('core.ftp_host_name');
        $currentPort = Phpfox::getParam('core.ftp_port');
        $currentUsername = Phpfox::getParam('core.ftp_user_name');
        $currentPassword = Phpfox::getParam('core.ftp_password');
        $aVals = $this->request()->get('val');
        if (count($aFiles) && isset($aVals['submit'])) {
            $manager = new \Core\Installation\Manager($aVals);
            if ($manager->verifyFtpAccount()) {
                try {
                    foreach ($aFiles as $sFile) {
                        $manager->deleteFile(dirname(PHPFOX_DIR) . PHPFOX_DS . $sFile, true);
                    }
                } catch (\Exception $ex) {
                    if (PHPFOX_DEBUG) {
                        throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
                    }
                    return \Phpfox_Error::set($ex->getMessage());
                }

                Phpfox::addMessage(_p("All old files deleted"));
                $this->url()->send('admincp.maintain.removefile');

            } else {
                if (Phpfox_Error::isPassed()) {
                    Phpfox_Error::set(_p('your_FTP_sFTP_account_does_not_work'));
                }
            }
        }
        $this->template()
            ->setTitle(_p("Remove files no longer used"))
            ->setSectionTitle(_p("Remove files no longer used"))
            ->setActiveMenu('admincp.maintain.removefile')
            ->setBreadCrumb(_p("Remove files no longer used"))
            ->assign([
                'aFiles' => $aFiles,
                'site_path' => dirname(PHPFOX_DIR) . PHPFOX_DS,
                'currentHostName' => $currentHostName,
                'currentPort' => $currentPort,
                'currentUsername' => $currentUsername,
                'currentPassword' => $currentPassword,
            ])
            ->setHeader([
                'bootstrap.min.css' => "style_css",
                'bootstrap.min.js' => "static_script"
            ]);
        return null;
    }

    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('admincp.component_controller_maintain_removefile_clean')) ? eval($sPlugin) : false);
    }
}

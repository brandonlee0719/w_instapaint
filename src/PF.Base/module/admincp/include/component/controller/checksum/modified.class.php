<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Admincp_Component_Controller_Checksum_Modified extends Phpfox_Component
{
    public function process()
    {
        if (defined('PHPFOX_TRIAL_MODE') && PHPFOX_TRIAL_MODE) {
            return false;
        }
        $check = array();
        $failed = 0;

        if ($this->request()->get('check')) {
            $this->template()->assign('check', true);
            $aChecksumFiles = [PHPFOX_DIR_INCLUDE . 'checksum' . PHPFOX_DS . 'md5'];

            // check modified files of apps
            $aAppDirs = array_filter(glob(PHPFOX_DIR_SITE_APPS . '*'), 'is_dir');
            foreach ($aAppDirs as $sDir) {
                $sChecksumSrc = $sDir . PHPFOX_DS . 'checksum';
                if (!file_exists($sChecksumSrc)) {
                    continue;
                }
                $aChecksumFiles[] = $sChecksumSrc;
            }

            // check modified files of modules that do not belong to core
            $aModules = db()->select('module_id')->from(':module')->where("product_id != 'phpfox'")->executeRows();
            foreach (array_column($aModules, 'module_id') as $sModuleId) {
                $sChecksumSrc = PHPFOX_DIR_MODULE . $sModuleId . PHPFOX_DS . 'checksum';
                if (!file_exists($sChecksumSrc)) {
                    continue;
                }
                $aChecksumFiles[] = $sChecksumSrc;
            }

            // check modified files of themes
            $themeDirs = array_filter(glob(PHPFOX_DIR_SITE . 'flavors' . PHPFOX_DS . '*'), 'is_dir');
            foreach ($themeDirs as $themeDir) {
                $sChecksumSrc = $themeDir . PHPFOX_DS . 'checksum';
                if (!file_exists($sChecksumSrc)) {
                    continue;
                }
                $aChecksumFiles[] = $sChecksumSrc;
            }

            foreach ($aChecksumFiles as $checksumFile){
                list($aChecked, $iFailed) = $this->_checkFiles($checksumFile);
                $check = array_merge($check, $aChecked);
                $failed += $iFailed;
            }
        }

        $this->template()
            ->setTitle(_p('checking_modified_files'))
            ->setSectionTitle(_p('modified_files'))
            ->setBreadCrumb(_p('modified_files'))
            ->setActiveMenu('admincp.maintain.modified')
            ->assign(array(
                'url' => $this->url()->makeUrl('admincp.checksum.modified', ['check' => true]),
                'files' => $check,
                'failed' => $failed
            ));
    }

    private function _checkFiles($sChecksumSrc)
    {
        $iFailed = 0;
        $aCheck = [];
        $lines = file($sChecksumSrc);
        foreach ($lines as $line) {
            $line = trim($line);
            $parts = explode(' ', $line, 2);
            $file = PHPFOX_PARENT_DIR . trim($parts[1]);
            $file_name = trim($parts[1]);

            if ($file_name == 'PF.Base/include/checksum/md5') {
                continue;
            }

            $message = '';
            $has_failed = false;
            $file = str_replace('/', PHPFOX_DS, $file);

            if (!file_exists($file)) {
                $message = 'MISSING';
                $iFailed++;
                $has_failed = true;
            } else {
                if (md5(file_get_contents($file)) == $parts[0]) {

                } else {
                    $message = 'MODIFIED <!-- ' . md5(file_get_contents($file)) . ' vs ' . $parts[0] . ' -->';
                    $iFailed++;
                    $has_failed = true;
                }
            }

            if ($has_failed) {
                $aCheck[$file_name] = $message;
            }
        }

        return [$aCheck, $iFailed];
    }
}

<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Admincp
 * @version 		$Id: index.class.php 2831 2011-08-12 19:44:19Z Raymond_Benc $
 */
class Admincp_Component_Controller_Checksum_Unknown extends Phpfox_Component {

    public function process() {

        $check = array();
        $unknown = array();

        if ($this->request()->get('check')) {
            $this->template()->assign('check', true);
            $lines = file(PHPFOX_DIR_INCLUDE . 'checksum' . PHPFOX_DS . 'md5');
            foreach ($lines as $line) {
                $line = trim($line);
                $parts = explode(' ', $line, 2);
                $check[trim($parts[1])] = true;
            }

            $files = Phpfox_File::instance()->getAllFiles(PHPFOX_PARENT_DIR);
            foreach ($files as $file) {
                $file = str_replace(PHPFOX_PARENT_DIR, '', $file);

                if (substr($file, -15) == 'server.sett.php'
                    || substr($file, 0, 13) == 'PF.Base' . PHPFOX_DS . 'file' . PHPFOX_DS . ''
                    || substr($file, 0, 16) == 'PF.Base' . PHPFOX_DS . 'install' . PHPFOX_DS . ''
                    || substr($file, 0, 5) == '.git' . PHPFOX_DS . ''
                    || substr($file, 0, 6) == '.idea' . PHPFOX_DS . ''
                    || substr($file, 0, 7) == 'PF.Dev' . PHPFOX_DS . ''
                    || substr($file, 0, 8) == 'PF.Demo' . PHPFOX_DS . ''
                    || $file == '.DS_Store'
                    || $file == '.htaccess'
                    || $file == 'include' . PHPFOX_DS . 'setting' . PHPFOX_DS . 'dev.sett.php'
                    || substr($file, 0, 8) == 'PF.Site' . PHPFOX_DS . ''
                    || $file == 'PF.Base' . PHPFOX_DS . 'module' . PHPFOX_DS . 'admincp' . PHPFOX_DS . 'include' . PHPFOX_DS . 'service' . PHPFOX_DS . 'setting' . PHPFOX_DS . 'process.class.php'
                ) {
                    continue;
                }

                $file = str_replace(PHPFOX_DS, '/', $file);

                if (!isset($check[$file])) {
                    $unknown[] = $file;
                }
            }
        }

        $this->template()
            ->setTitle(_p('checking_unknown_files'))
            ->setSectionTitle(_p('unknown_files'))
            ->setBreadCrumb(_p('unknown_files'))
            ->setActiveMenu('admincp.maintain.unknown')
            ->assign(array(
                'url' => $this->url()->makeUrl('admincp.checksum.unknown', ['check' => true]),
                'unknown' => $unknown,
                'total' => count($unknown)
            ));
    }

}
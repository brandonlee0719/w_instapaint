<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author           Raymond Benc
 * @package          Module_Language
 * @version          $Id: index.class.php 4316 2012-06-21 13:57:37Z
 *                   Miguel_Espinoza $
 */
class Language_Component_Controller_Admincp_Index extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        Phpfox::getUserParam('language.can_manage_lang_packs', true);

        if (($sExportId = $this->request()->get('export'))) {
            $oArchiveExport = Phpfox_Archive_Export::instance()->set(['zip']);

            if (($aData = Phpfox::getService('language')
                ->exportForDownload($sExportId, false))
            ) {
                $oArchiveExport->download('phpfox-language-' . $aData['name']
                    . '', 'zip', $aData['folder'], $aData['server_id']);
            }
        }

        $aLanguages = Phpfox::getService('language')->getForAdminCp();

        if ($iDefault = $this->request()->get('default')) {
            if (Phpfox::getService('language.process')->setDefault($iDefault)) {
                $this->url()->send('admincp.language',
                    _p('default_language_package_reset'));
            }
        }

        $this->template()->setActionMenu([
            _p('new_language')             => [
                'url'   => $this->url()->makeUrl('admincp.language.add'),
                'class' => 'popup light',
            ],
            _p('new_phrase')               => [
                'url'   => $this->url()->makeUrl('admincp.language.phrase.add'),
                'class' => 'popup light',
            ],
            _p('manual_import')            => [
                'url'   => $this->url()->makeUrl('admincp.language.import'),
                'class' => 'light',
            ],
            _p('find_more_language_packs') => [
                'url'   => $this->url()
                    ->makeUrl('admincp.store', ['load' => 'language']),
                'class' => '',
            ],
        ]);
        $this->template()
            ->setBreadCrumb(_p('manage_language_packages'))
            ->setSectionTitle(_p('languages'))
            ->setActiveMenu('admincp.globalize.language')
            ->assign([
                'aLanguages' => $aLanguages,
            ]);
    }
}
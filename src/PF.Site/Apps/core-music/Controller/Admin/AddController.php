<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Music\Controller\Admin;

defined('PHPFOX') or exit('NO DICE!');

class AddController extends \Admincp_Component_Controller_App_Index
{
    /**
     * Controller
     */
    public function process()
    {
        parent::process();
        if ($iDelete = $this->request()->getInt('delete')) {
            if (\Phpfox::getService('music.genre.process')->delete($iDelete)) {
                $this->url()->send('admincp.music', null, _p('successfully_deleted_genres'));
            }
        }

        $bIsEdit = false;
        $iEditId = $this->request()->getInt('edit');
        $aLanguages = \Phpfox::getService('language')->getAll(true);
        if ($iEditId) {
            $bIsEdit = true;
            $aGenre = \Phpfox::getService('music.genre')->getForEdit($iEditId);
            if (!isset($aGenre['genre_id'])) {
                $this->url()->send('admincp.app', ['id' => 'Core_Music'], _p('genre_not_found'));
            }
            $this->template()->assign([
                'aForms' => $aGenre,
                'iEditId' => $iEditId
            ]);
        }

        if ($aVals = $this->request()->getArray('val')) {
            if ($aVals = $this->_validate($aVals)) {
                if ($bIsEdit) {
                    if (\Phpfox::getService('music.genre.process')->update($aVals)) {
                        $this->url()->send('admincp.app', ['id' => 'Core_Music'], _p('Genre successfully updated'));
                    }
                } else {
                    if (\Phpfox::getService('music.genre.process')->add($aVals)) {
                        $this->url()->send('admincp.app', ['id' => 'Core_Music'], _p('genre_successfully_added'));
                    }
                }
            }
        }

        $this->template()->setTitle(($bIsEdit ? _p('Edit Genre') : _p('add_genre')))
            ->setBreadCrumb(($bIsEdit ? _p('Edit Genre') : _p('add_genre')))
            ->assign([
                'bIsEdit' => $bIsEdit,
                'aLanguages' => $aLanguages,
                'iEditId' => $iEditId
            ]);
    }

    /**
     * validate input value
     * @param $aVals
     *
     * @return bool
     */
    private function _validate($aVals)
    {
        $return = \Phpfox::getService('language')->validateInput($aVals, 'name', false);
        if (!$return) {
            \Phpfox_Error::reset();
            \Phpfox_Error::set(_p('genre_name_is_required'));
        }
        return $return;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = \Phpfox_Plugin::get('music.component_controller_admincp_add_clean')) ? eval($sPlugin) : false);
    }
}
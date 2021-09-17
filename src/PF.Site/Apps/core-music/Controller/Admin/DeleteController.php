<?php

namespace Apps\Core_Music\Controller\Admin;

use Phpfox;
use Phpfox_Component;

defined('PHPFOX') or exit('NO DICE!');

class DeleteController extends Phpfox_Component
{
    public function process()
    {
        $iDeleteId = $this->request()->getInt('delete');
        if ($iDeleteId && $aRow = Phpfox::getService('music.genre')->getGenre($iDeleteId)) {
            $iTotalItems = Phpfox::getService('music.genre')->getTotalItemBelongToGenre($iDeleteId);
            $this->template()->assign([
                    'iTotalItems' => $iTotalItems,
                    'iDeleteId' => $iDeleteId,
                    'aGenres' => Phpfox::getService('music.genre')->getList(0, $iDeleteId, false),
                ]
            );

            if (($aVals = $this->request()->getArray('val'))) {
                if (Phpfox::getService('music.genre.process')->delete($iDeleteId, $aVals)) {
                    $this->url()->send('admincp.app', ['id' => 'Core_Music'],
                        _p('successfully_deleted_genres'));
                }
            }
        } else {
            \Phpfox_Error::display(_p('genre_not_found'));
        }

        $this->template()->setTitle(_p('delete_genre'))
            ->setBreadCrumb(_p('delete_genre'));
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = \Phpfox_Plugin::get('photo.component_controller_admincp_delete_category_clean')) ? eval($sPlugin) : false);
    }
}
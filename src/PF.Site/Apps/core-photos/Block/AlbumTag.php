<?php

namespace Apps\Core_Photos\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

class AlbumTag extends Phpfox_Component
{
    public function process()
    {
        $aAlbum = $this->getParam('aAlbum');
        $view = $this->getParam('view', 'block');
        $page = $this->request()->get('page', 1);
        $iLimit = ($view == 'all') ? 10 : $this->getParam('limit',4);

        if (!(int)$iLimit)
            return false;

        list($iCnt, $aUsers) = Phpfox::getService('photo.album')->inThisAlbum($aAlbum['album_id'], $iLimit, $page);

        if (!$iCnt) {
            return false;
        }

        if ($view == 'all') {
            \Phpfox_Pager::instance()->set(array(
                'page' => $page,
                'size' => $iLimit,
                'count' => $iCnt,
                'ajax' => 'photo.browseAlbumTags',
                'popup' => true
            ));
        }
        $this->template()->assign(array(
                'sHeader' => _p('in_this_album'),
                'aTaggedUsers' => $aUsers,
                'sView' => $view,
                'iPage' => $page
            )
        );

        if ($iCnt > $iLimit) {
            $this->template()
                ->assign([
                    'aFooter' => array(
                        _p('view_all_total',['total' => $iCnt]) => 'javascript:$Core.box(\'photo.browseAlbumTags\', 400, \'album_id=' . $aAlbum['album_id'] . '\'); void(0);',
                    )
                ]);
        }
        return 'block';
    }
    /**
     * @return array
     */
    public function getSettings()
    {
        return [
            [
                'info' => _p('In This Album Limit'),
                'description' => _p('Define the limit of how many tagged users can be displayed when viewing the album detail section. Set 0 will hide this block'),
                'value' => 4,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
        ];
    }
    /**
     * @return array
     */
    public function getValidation()
    {
        return [
            'limit' => [
                'def' => 'int',
                'min' => 0,
                'title' => _p('"In This Album Limit" must be greater than or equal to 0')
            ],
        ];
    }
    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('photo.component_block_album_tag_clean')) ? eval($sPlugin) : false);
    }
}
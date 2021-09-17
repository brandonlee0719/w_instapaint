<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Forums\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class ThanksBlock extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $iPostId = (int)$this->request()->get('post_id', 0);
        if (!$iPostId) {
            return false;
        }
        $iPage = $this->getParam('page', 1);
        $iLimit = 6;
        $aThanks = Phpfox::getService('forum.post')->getThanksForPost($iPostId, $iPage, $iLimit, $iCount );

        $aParamsPager = array(
            'page' => $iPage,
            'size' => $iLimit,
            'count' => $iCount,
            'paging_mode' => 'pagination',
            'ajax_paging' => [
                'block' => 'forum.thanks',
                'params' => [
                    'post_id' => $iPostId,
                ],
                'container' => '.js_users_thank_post'
            ]
        );
        $this->template()->assign(array(
                'aThanks' => $aThanks,
                'iPostId' => $iPostId,
                'iPage' => $iPage,
                'bIsPaging' => $this->getParam('ajax_paging', 0)
            )
        );
        Phpfox::getLib('pager')->set($aParamsPager);

        return 'block';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('forum.component_block_thanks_clean')) ? eval($sPlugin) : false);
    }
}
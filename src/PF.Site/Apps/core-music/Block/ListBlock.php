<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Music\Block;

use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class ListBlock extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        //Hide this blog when login as page
        if (Phpfox::getUserBy('profile_page_id') > 0) {
            return false;
        }
        if (defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }
        $sGenre = $this->getParam('sGenre');
        if ((int)$sGenre) {
            return false;
        }
        $aGenres = \Phpfox::getService('music.genre')->getList(1);

        if (!count($aGenres)) {
            return false;
        }

        $this->template()->assign(array(
                'sHeader' => _p('genres'),
                'aGenres' => $aGenres,
                'aCategories' => $aGenres,
                'iCurrentGenre' => $this->request()->getInt('req3')
            )
        );

        return 'block';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = \Phpfox_Plugin::get('music.component_block_list_clean')) ? eval($sPlugin) : false);
    }
}
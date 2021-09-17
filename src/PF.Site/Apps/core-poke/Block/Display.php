<?php

namespace Apps\Core_Poke\Block;

use Phpfox;
use Phpfox_Component;

defined('PHPFOX') or exit('No dice!');

/**
 * Displays all the pokes this user has received so they can poke back
 */
class Display extends Phpfox_Component
{
    public function process()
    {
        $iUserId = Phpfox::getUserId();
        $bIsBlock = true;
        $bIsPaging = $this->getParam('isPaging');
        list($iTotalPokes, $aPokes) = array(0, array());

        // Not Paging. Show on block 3
        if (!$bIsPaging) {
            $iLimit = $this->getParam('limit', 5);
            if ($iLimit) {
                list($iTotalPokes, $aPokes) = Phpfox::getService('poke')->getPokesForUser($iUserId, 1,
                    $iLimit);
            }
            if ($iTotalPokes > $iLimit) {
                $this->template()->assign([
                    'aFooter' => array(
                        _p('view_all_total',['total' => $iTotalPokes]) => 'javascript:$Core.box(\'poke.viewMore\', 450, \'isPaging=true\'); void(0);'
                    )
                ]);
            }
        } // Pagination section
        else {
            // Get params
            $iLimit = 10;
            $iPage = $this->getParam('page', 1);
            $bIsBlock = false;
            list($iTotalPokes, $aPokes) = Phpfox::getService('poke')->getPokesForUser($iUserId, $iPage, $iLimit);

            // Set params for pagination
            $aParamsPager = array(
                'page' => $iPage,
                'size' => $iLimit,
                'count' => $iTotalPokes,
                'paging_mode' => 'pagination',
                'ajax_paging' => [
                    'block' => 'poke.display',
                    'params' => [
                        'isPaging' => true
                    ],
                    'container' => '.js_core_poke_list_items'
                ]
            );

            Phpfox::getLib('pager')->set($aParamsPager);

            $this->template()->assign(array(
                'bIsPaging' => $this->getParam('ajax_paging', 0)
            ));
        }

        if (!$iTotalPokes) {
            return false;
        }

        $this->template()->assign(array(
            'aPokes' => $aPokes,
            'sHeader' => _p('pokes'),
            'iTotalPokes' => $iTotalPokes,
            'bIsBlock' => $bIsBlock,
            'iLimit' => $iLimit,
        ));

        return 'block';
    }

    /**
     * @return array
     */
    function getSettings()
    {
        return [
            [
                'info' => _p('Pokes Limit'),
                'description' => _p('Define the limit of how many other users poked current logged user can be displayed when viewing in member home page. Set 0 will hide this block.'),
                'value' => 5,
                'type' => 'integer',
                'var_name' => 'limit',
            ]
        ];
    }
}


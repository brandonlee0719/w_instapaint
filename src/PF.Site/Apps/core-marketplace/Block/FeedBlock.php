<?php
namespace Apps\Core_Marketplace\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Database;

defined('PHPFOX') or exit('NO DICE!');


class FeedBlock extends Phpfox_Component
{

    public function process()
    {
        $iFeedId = $this->getParam('this_feed_id');
        if ($iFeedId) {
            $aFeed = Phpfox::getService('feed')->getFeed($iFeedId);
            if (!$aFeed || ($aFeed['type_id'] != 'marketplace')) {
                return false;
            }
            $aRow = Phpfox_Database::instance()->select('e.*')
                ->from(Phpfox::getT('marketplace'), 'e')
                ->where('e.listing_id = ' . (int)$aFeed['item_id'])
                ->execute('getSlaveRow');

            if (!$aRow) {
                return false;
            }

            $aRow['is_in_feed'] = true;
            $aRow['url'] = Phpfox::permalink('marketplace', $aRow['listing_id'], $aRow['title']);

            $aRow['categories'] = Phpfox::getService('marketplace.category')->getCategoriesById($aRow['listing_id']);
            $this->template()->assign('aListing', $aRow);

        }

    }
}
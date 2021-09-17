<?php
namespace Apps\Core_Marketplace\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Database;

defined('PHPFOX') or exit('NO DICE!');

class RowsBlock extends Phpfox_Component
{

    public function process()
    {
        $iFeedId = $this->getParam('this_feed_id');
        if ($iFeedId) {
            $aFeed = Phpfox::getService('feed')->getFeed($iFeedId);
            if (!$aFeed || ($aFeed['type_id'] != 'marketplace')) {
                return false;
            }
            $aRow = Phpfox_Database::instance()->select('e.*, mc.name AS category_name, et.description_parsed')
                ->from(Phpfox::getT('marketplace'), 'e')
                ->leftJoin(Phpfox::getT('marketplace_text'), 'et', 'et.listing_id = e.listing_id')
                ->leftJoin(Phpfox::getT('marketplace_category_data'), 'mcd', 'mcd.listing_id = e.listing_id')
                ->leftJoin(Phpfox::getT('marketplace_category'), 'mc', 'mc.category_id = mcd.category_id')
                ->where('e.listing_id = ' . (int)$aFeed['item_id'])
                ->execute('getSlaveRow');

            if (!$aRow) {
                return false;
            }

            $aRow['is_in_feed'] = true;
            $aRow['url'] = Phpfox::permalink('marketplace', $aRow['listing_id'], $aRow['title']);
            $this->template()->assign('aListing', $aRow);

        }

    }
}
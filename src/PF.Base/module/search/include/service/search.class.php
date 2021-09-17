<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Search_Service_Search
 */
class Search_Service_Search extends Phpfox_Service 
{
	public function query($sQuery, $iPage, $iTotalShow, $sView = null)
	{
		if ($sView !== null && Phpfox::isModule($sView))
		{
			Phpfox::callback($sView . '.globalUnionSearch', $this->preParse()->clean($sQuery));
		}
		else if ($sView !== null && Phpfox::isApps($sView))
		{
			$this->database()->select('
				item.feed_id AS item_id,
				\'\' AS item_title,
				item.time_stamp AS item_time_stamp,
				item.user_id AS item_user_id,
				item.type_id AS item_type_id,
				item.content AS item_photo,
				\'\' AS item_photo_server')
				->from(Phpfox::getT('feed'), 'item')
				->where('' . $this->database()->searchKeywords('item.content', $sQuery) . ' AND '. $this->database()->searchKeywords('item.type_id', $sView))
				->union();
		}
		else
		{
			Phpfox::massCallback('globalUnionSearch', $this->preParse()->clean($sQuery));

			$this->database()->select('
				item.feed_id AS item_id,
				\'\' AS item_title,
				item.time_stamp AS item_time_stamp,
				item.user_id AS item_user_id,
				item.type_id AS item_type_id,
				item.content AS item_photo,
				0 AS item_photo_server')
				->from(Phpfox::getT('feed'), 'item')
				->where('' . $this->database()->searchKeywords('item.content', $sQuery))
				->union();
		}
		

		$aRows = $this->database()->select('item.*, ' . Phpfox::getUserField())
				->unionFrom('item', true)
				->join(Phpfox::getT('user'), 'u', 'u.user_id = item.item_user_id')
				->limit($iPage, $iTotalShow)
				->order('item_time_stamp DESC')
				->execute('getSlaveRows');

		$aResults = array();
		foreach ($aRows as $iKey => $aRow)
		{
			if (app()->exists($aRow['item_type_id'])) {
				$app = app($aRow['item_type_id']);
				if ($app->map_search) {
					$data = json_decode($aRow['item_photo']);
					if (isset($data->{$app->map_search->title})) {
						$aRow['item_title'] = $data->{$app->map_search->title};
						$aRow['item_link'] = url(str_replace(':id', $aRow['item_id'], $app->map_search->link));
						$aRow['item_name'] = _p($app->map_search->info);

						$aResults[] = $aRow;
					}
				}

				continue;
			}

			$aResults[] = array_merge($aRow, (array) Phpfox::callback($aRow['item_type_id'] . '.getSearchInfo', $aRow));
		}

		if (Phpfox::getParam('core.section_privacy_item_browsing') && !empty($aResults))
		{
			// Check for special filters
			$aToParse = array();
			// Group results by their module
			foreach ($aResults as $aResult)
			{
				$aToParse[$aResult['item_type_id']][] = $aResult['item_id'];
			}


			foreach ($aToParse as $sModule => $aItems)
			{
				if (Phpfox::hasCallback($sModule, 'filterSearchResults'))
				{
					$aNotAllowed = Phpfox::callback($sModule . '.filterSearchResults', $aItems);

					if (!empty($aNotAllowed))
					{
						foreach ($aNotAllowed as $aItem)
						{
							foreach ($aResults as $iKey => $aResult)
							{
								if ($aResult['item_type_id'] == $aItem['item_type_id'] && $aResult['item_id'] == $aItem['item_id'])
								{
									unset($aResults[$iKey]);
								} 
							}
						}
					}
				}
			}
		}
		return $aResults;
	}

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     * @return null
     */
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('search.service_search__call'))
		{
			eval($sPlugin);
            return null;
		}
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}	
}
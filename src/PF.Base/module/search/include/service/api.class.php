<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Search_Service_Api
 */
class Search_Service_Api extends \Core\Api\ApiServiceBase
{
    public function __construct()
    {
        $this->setPublicFields([
            'item_id',
            'item_title',
            'item_time_stamp',
            'item_user_id',
            'item_type_id',
            'item_photo',
            'item_photo_server',
            'item_link',
            'item_name'
        ]);
    }

    /**
     * @description: search global
     * @return array|bool
     */
    public function get()
    {
        if (!Phpfox::getUserParam('search.can_use_global_search'))
        {
            return $this->error(_p('You don\'t have permission to use this feature.'));
        }

        $this->requireParams(['keyword']);
        $sQuery = $this->request()->get('keyword');
        $sView = $this->request()->get('view', null);
        $this->initSearchParams();
        $aSearchResults = Phpfox::getService('search')->query($sQuery, $this->getSearchParam('page'), $this->getSearchParam('limit'), $sView);
        $results = [];
        foreach ($aSearchResults as $aItem)
        {
            $results[] = $this->getItem($aItem);
        }

        return $this->success($results);
    }
}

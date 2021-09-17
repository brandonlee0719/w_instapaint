<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');


class Link_Service_Api extends \Core\Api\ApiServiceBase
{
    /**
     * @description: attach link
     * @return array|bool
     */
    public function post()
    {
        $this->isUser();
        $aVals = $this->request()->getArray('val');

        $this->requireParams(['url'], $aVals);
        $aCallback = null;
        if (!empty($aVals['module']) && !empty($aVals['item_id']) && Phpfox::hasCallback($aVals['module'], 'addLink'))
        {
            $aVals['callback_module'] = $aVals['module'];
            $aVals['callback_item_id'] = $aVals['item_id'];
            $aVals['parent_user_id'] = $aVals['item_id'];
            $aCallback = Phpfox::callback($aVals['module'] . '.addLink', $aVals);
        }

        if (filter_var($aVals['url'], FILTER_VALIDATE_URL) === false)
        {
            return $this->error(_p('not_a_valid_link'));
        }

        $aLink = Phpfox::getService('link')->getLink($aVals['url']);

        if (!$aLink)
        {
            return $this->error(_p('not_a_valid_link'));
        }

        $aVals['link'] = $aLink;
        $aVals['link']['url'] = $aVals['link']['link'];
        if (isset($aVals['link']['default_image']))
        {
            $aVals['link']['image'] = $aVals['link']['default_image'];
        }

        if (!empty($aVals['text']))
        {
            $aVals['status_info'] = $aVals['text'];
        }
        else
        {
            $aVals['status_info'] = $aVals['url'];
        }

        if (!empty($aVals['user_id']))
        {
            $aVals['parent_user_id'] = $aVals['user_id'];
        }
        $iId = Phpfox::getService('link.process')->add($aVals, false, $aCallback);

        $aFeed = Phpfox::getService('feed')->callback($aCallback)->get(null, $iId);

        if (count($aFeed))
        {
            $aItem = Phpfox::getService('feed.api')->getItem($aFeed[0]);
            return $this->success($aItem, [_p('Link successfully attached.')]);
        }

        return $this->error();
    }
}
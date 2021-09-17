<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Feed_Service_Api
 */
class Feed_Service_Api extends \Core\Api\ApiServiceBase
{
    /**
     * Feed_Service_Api constructor.
     */
    public function __construct()
    {
        $this->setPublicFields([
            'feed_id',
            'app_id',
            'privacy',
            'privacy_comment',
            'type_id',
            'user_id',
            'parent_user_id',
            'item_id',
            'time_stamp',
            'feed_reference',
            'parent_feed_id',
            'parent_module_id',
            'time_update',
            'content',
            'total_view',
            'can_post_comment',
            'feed_link',
            'feed_title',
            'feed_info',
            'feed_icon',
            'feed_status',
            'feed_image',
            'total_image',
            'enable_like',
            'total_likes',
            'likes',
            'feed_is_liked',
            'feed_like_phrase'
        ]);
    }
    
    /**
     * @description: process for get detail info of a feed
     *
     * @param array $params
     * @param array $messages
     *
     * @return array|bool
     */
    public function get($params, $messages = [])
    {
        $iFeedId = $params['id'];
        $aFeeds = Phpfox::getService('feed')->get(null, $iFeedId);
        if (empty($aFeeds))
        {
            return $this->error(_p('The {{ item }} cannot be found.', ['item' => _p('feed__l')]));
        }

        $aFeed = $aFeeds[0];

        if (Phpfox::getService('user.block')->isBlocked(null, $aFeed['user_id']))
        {
            return $this->error(_p('The {{ item }} cannot be found.', ['item' => _p('feed__l')]));
        }

        if (Phpfox::isModule('privacy'))
        {
            if (!Phpfox::getService('privacy')->check('feed', $aFeed['feed_id'], $aFeed['user_id'], $aFeed['privacy'], null, true, true))
            {
                return $this->error('You don\'t have permission to {{ action }} this {{ item }}.', ['action' => _p('view__l'), 'item' => _p('feed__l')]);
            }
        }


        $aItem = $this->getItem($aFeed);
        return $this->success($aItem, $messages);
    }
    
    /**
     * @description: delete a feed by feed id
     *
     * @param $params
     *
     * @return array|bool
     */
    public function delete($params)
    {
        $iFeedId = $params['id'];
        $aFeed = Phpfox::getService('feed')->getFeed($iFeedId);
        if (empty($aFeed) || empty($aFeed['feed_id']))
        {
            return $this->error(_p('The {{ item }} cannot be found.', ['item' => _p('feed__l')]));
        }
        $result = Phpfox::getService('feed.process')->deleteFeed($iFeedId);
        if (!$result)
        {
            return $this->error(_p('You cannot {{ action }} this {{ item }}.', ['action' => _p('delete__l'), 'item' => _p('feed__l')]));
        }
        return $this->success([], [_p('{{ item }} successfully deleted.', ['item' => _p('feed')])]);
    }
    
    /**
     * @description: update user status
     *
     * @param $params
     *
     * @return array|bool
     */
    public function put($params)
    {
        $iFeedId = $params['id'];
        $aFeed = Phpfox::getService('feed')->getUserStatusFeed([], $iFeedId);
        if (empty($aFeed) || empty($aFeed['feed_id']))
        {
            return $this->error(_p('The {{ item }} cannot be found.', ['item' => _p('feed__l')]));
        }

        if ($aFeed['type_id'] != 'user_status')
        {
            return $this->error(_p('Cannot edit this feed. You can only edit \'Status\' feed only.'));
        }

        if (!(Phpfox::getUserParam('feed.can_edit_own_user_status') && $aFeed['user_id'] == Phpfox::getUserId()) && !Phpfox::getUserParam('feed.can_edit_other_user_status'))
        {
            return $this->error(_p('You don\'t have permission to {{ action }} this {{ item }}.', ['action' => _p('delete__l'), 'item' => _p('feed__l')]));
        }
        $aVals = Phpfox_Request::instance()->getArray('val');

        if (!empty($aVals['privacy']) && $aVals['privacy'] == 4 && empty($aVals['privacy_list']))
        {
            return $this->error(_p('Privacy List is required for a custom privacy feed.'));
        }
        $aVals = array_merge([
            'user_status' => $aFeed['feed_status'],
            'privacy' => $aFeed['privacy'],
            'privacy_comment' => $aFeed['privacy_comment']
        ], $aVals);
        if (!Phpfox::getService('user.process')->editStatus($iFeedId, $aVals))
        {
            return $this->error(_p('Cannot {{ action }} this {{ item }}.', ['action' => _p('update__l'), 'item' => _p('feed__l')]));
        }
        return $this->get($params, [_p('{{ item }} successfully updated.', ['item' => _p('feed')])]);
    }
    
    /**
     * @description: get feeds for main feed, user profile or item profile
     *
     * @return array|bool
     */
    public function gets()
    {
        //support get feed of item
        $aCallback = $this->request()->getArray('callback');
        if (($module = $this->request()->get('module')) && ($itemId = $this->request()->get('item_id')))
        {
            if (Phpfox::hasCallback($module, 'getFeedDisplay'))
            {
                $aCallback = Phpfox::callback($module . '.getFeedDisplay', $itemId);
            }
            $aCallback = array_merge(['module' => $module, 'item_id' => $itemId], $aCallback);
        }

        //support check item permission
        if (isset($aCallback['module']) && isset($aCallback['item_id']) && Phpfox::hasCallback($aCallback['module'], 'canGetFeeds'))
        {
            $bCanGetFeeds = Phpfox::callback($aCallback['module'] . '.canGetFeeds', $aCallback['item_id']);
            if (!$bCanGetFeeds)
            {
                return $this->error(_p('You don\'t have permission to get feeds of this item.') , true);
            }
        }

        $iUserId = $this->request()->get('user_id', null);
        $this->initSearchParams();
        $aParams = [
            'user_id' => $iUserId,
            'limit' => $this->getSearchParam('limit'),
            'page' => $this->getSearchParam('page')
        ];

        //get feed for user profile
        if ($iUserId)
        {
            if (!Phpfox::getService('user')->isUser($iUserId, true))
            {
                return $this->error(_p('The {{ item }} cannot be found.', ['item' => _p('user__l')]));
            }

            if (Phpfox::getService('user.block')->isBlocked(null, $iUserId) || !Phpfox::getService('user.privacy')->hasAccess($iUserId, 'feed.view_wall'))
            {
                return $this->error(_p('Sorry, information of this user isn\'t available for you.'));
            }

            if (empty($aCallback))
            {
                $aParams['include_friend_feeds'] = true;
            }
        }

        $aRows = Phpfox::getService('feed')->callback($aCallback)->get($aParams, null);

        $result = [];
        foreach ($aRows as $aRow)
        {
            $result[] = $this->getItem($aRow);
        }
        return $this->success($result);
    }

    /**
     * @description: add user status, comment on profile, add feed on pages, groups, events
     *
     * @return array|bool|mixed
     */
    public function post()
    {
        $this->isUser();
        $aVals = $this->request()->getArray('val');
        if (empty($aVals['user_status']))
        {
            return error(_p('Field "{{ field }}" is required.', ['field' => 'val[user_status]']));
        }
        if (!empty($aVals['privacy']) && $aVals['privacy'] == 4 && empty($aVals['privacy_list']))
        {
            return $this->error(_p('Privacy List is required for a custom privacy feed.'));
        }

        //get callback for adding feed on item
        $aCallback = $this->request()->getArray('callback');
        if (($module = $this->request()->get('module')) && ($itemId = $this->request()->get('item_id')))
        {
            if (Phpfox::hasCallback($module, 'getFeedComment'))
            {
                $aCallback = Phpfox::callback($module . '.getFeedComment', $itemId, $aVals);
                if ($aCallback === false)
                {
                    return $this->error(_p("You don't have permission to add new {{ item }} on this item.", ['item' => _p('feed comment')]), true);
                }
            }
            $aCallback = array_merge(['module' => $module, 'item_id' => $itemId], $aCallback);
        }

        //handle case add user status
        $iUserId = $this->request()->getInt('user_id', 0);
        if ((!$iUserId || $iUserId == Phpfox::getUserId()) && empty($aCallback))
        {
            if ($id = Phpfox::getService('user.process')->updateStatus($aVals))
            {
                return $this->get(['id' => $id], [_p('{{ item }} successfully added.', ['item' => _p('status')])]);
            }
            return $this->error(_p('Cannot post new user status.'), true);
        }

        //check permission for case add feed comment on others profile
        if ($iUserId && (Phpfox::getService('user.block')->isBlocked(null, $iUserId) || !(Phpfox::getUserParam('profile.can_post_comment_on_profile') && Phpfox::getService('user.privacy')->hasAccess($iUserId, 'feed.share_on_wall')))) {
            $this->error(_p('You don\'t have permission to post comment on this profile.'));
        }

        if ($iUserId)
        {
            $aVals['parent_user_id'] = $iUserId;
        }
        if (!empty($aCallback['item_id']))
        {
            $aVals['parent_user_id'] = $aCallback['item_id'];
        }

        if ($iId = Phpfox::getService('feed.process')->callback($aCallback)->addComment($aVals))
        {
            if (!empty($aCallback['module']) && !empty($aCallback['item_id']) && Phpfox::hasCallback($aCallback['module'], 'onAddFeedCommentAfter'))
            {
                Phpfox::callback($aCallback['module'] . '.onAddFeedCommentAfter', $aCallback['item_id']);
            }
            Phpfox::getService('feed')->callback($aCallback);
            return $this->get(['id' => $iId], [_p('{{ item }} successfully added.', ['item' => _p('Feed comment')])]);
        }

        return $this->error(_p('Cannot add new {{ item }} on this.', ['item' => _p('feed comment')]), true);
    }

    /**
     * @description: share a feed item
     *
     * @return array|bool
     */
    public function share()
    {
        $this->isUser();
        $aVals = $this->request()->getArray('val');
        $this->requireParams(['item_id', 'module_id', 'type'], $aVals);

        $aVals['parent_feed_id'] = $aVals['item_id'];
        $aVals['parent_module_id'] = $aVals['module_id'];

        if ($aVals['type'] == '2')
        {
            if (empty($aVals['friends']))
            {
                return $this->error(_p('select_a_friend_to_share_this_with_dot'));
            }
            else
            {
                $ids = [];
                foreach ($aVals['friends'] as $iFriendId)
                {
                    $aShare = array(
                        'user_status' => $aVals['content'],
                        'parent_user_id' => $iFriendId,
                        'parent_feed_id' => $aVals['parent_feed_id'],
                        'parent_module_id' => $aVals['parent_module_id']
                    );

                    if (Phpfox::getService('user.privacy')->hasAccess($iFriendId, 'feed.share_on_wall') && Phpfox::getUserParam('profile.can_post_comment_on_profile'))
                    {
                        $ids[] = Phpfox::getService('feed.process')->addComment($aShare);
                    }
                    else
                    {
                        return $this->error(_p('You cannot share this item on the friend with id {{ id }}.', ['id' => $iFriendId]));
                    }
                }
            }

            $this->request()->set('ids', implode(',', $ids));
            $aFeeds = Phpfox::getService('feed')->get();
            $results = [];
            foreach ($aFeeds as $aFeed)
            {
                $results[] = $this->getItem($aFeed);
            }

            return $this->success($results, [_p('successfully_shared_this_item_on_your_friends_wall')]);
        }

        elseif ($aVals['type'] == '1')
        {
            $aShare = array(
                'user_status' => $aVals['content'],
                'privacy' => '0',
                'privacy_comment' => '0',
                'parent_feed_id' => $aVals['parent_feed_id'],
                'parent_module_id' => $aVals['parent_module_id'],
                'no_check_empty_user_status' => true,
            );

            if (($iId = Phpfox::getService('user.process')->updateStatus($aShare)))
            {
                return $this->get(['id' => $iId], [_p('successfully_shared_this_item')]);
            }
        }

        return $this->error(_p('Request is invalid.'), true);
    }
}
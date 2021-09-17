<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Comment_Service_Api
 */
class Comment_Service_Api extends \Core\Api\ApiServiceBase
{
    public function __construct()
    {
        $this->setPublicFields([
            'is_liked',
            'comment_id',
            'parent_id',
            'type_id',
            'item_id',
            'user_id',
            'owner_user_id',
            'child_total',
            'total_like',
            'text',
            'unix_time_stamp'
        ]);
    }

    /**
     * @description: get info of a comment
     * @param array $params
     * @param array $messages
     *
     * @return array|bool
     */
    public function get($params, $messages = [])
    {
        $comment = Phpfox::getService('comment')->getComment($params['id']);
        if (empty($comment))
        {
            return $this->error(_p('The {{ item }} cannot be found.', ['item' => _p('comment__l')]), true);
        }

        //check permission to view parent item
        if (!empty($comment['type_id']) && !empty($comment['item_id']) && Phpfox::hasCallback($comment['type_id'], 'canViewItem'))
        {
            if (!Phpfox::callback($comment['type_id'] . '.canViewItem', $comment['item_id']))
            {
                return $this->error(_p('You don\'t have permission to {{ action }} this {{ item }}.', ['action' => _p('view__l'), 'item' => _p('comment__l')]), true);
            }
        }

        $aItem = $this->getItem($comment);
        return $this->success($aItem, $messages);
    }
    
    /**
     * @description: add comment for a specific item
     * @return array|bool
     */
    public function post()
    {
        //check is user
        $this->isUser();

        //validate params
        $this->requireParams([
            'type',
            'item_id'
        ], $this->request()->getArray('val'));

        $aVals = $this->request()->get('val');
        if (isset($aVals['type']) && $aVals['type'] != 'app') {
            $sVar = Phpfox::callback($aVals['type'] . '.getAjaxCommentVar');
            if ($sVar !== null)
            {
                Phpfox::getUserParam($sVar, true);
            }
        }

        //check permission
        if (!Phpfox::getUserParam('comment.can_post_comments'))
        {
            return $this->error(_p('Your user group is not allowed to add comments.'));
        }

        if ($aVals['type'] == 'profile' && !Phpfox::getService('user.privacy')->hasAccess($aVals['item_id'], 'comment.add_comment'))
        {
            return $this->error(_p('You cannot comment on this profile.'));
        }

        if (!Phpfox::getUserParam('comment.can_comment_on_own_profile') && $aVals['type'] == 'profile' && $aVals['item_id'] == Phpfox::getUserId() && empty($aVals['parent_id']))
        {
            return $this->error(_p('you_cannot_write_a_comment_on_your_own_profile'));
        }

        if (($iFlood = Phpfox::getUserParam('comment.comment_post_flood_control')) !== 0)
        {
            $aFlood = array(
                'action' => 'last_post', // The SPAM action
                'params' => array(
                    'field' => 'time_stamp', // The time stamp field
                    'table' => Phpfox::getT('comment'), // Database table we plan to check
                    'condition' => 'type_id = \'' . Phpfox_Database::instance()->escape($aVals['type']) . '\' AND user_id = ' . Phpfox::getUserId(), // Database WHERE query
                    'time_stamp' => $iFlood * 60 // Seconds);
                )
            );

            // actually check if flooding
            if (Phpfox::getLib('spam')->check($aFlood))
            {
                return $this->error(_p('posting_a_comment_a_little_too_soon_total_time', array('total_time' => Phpfox::getLib('spam')->getWaitTime())));
            }
        }

        if (empty($aVals['text']) || Phpfox::getLib('parse.format')->isEmpty($aVals['text'])
            || (isset($aVals['default_feed_value']) && $aVals['default_feed_value'] == $aVals['text']))
        {
            return $this->error(_p('Field "{{ field }}" is required.', ['field' => 'val[text]']));
        }

        if (empty($aVals['parent_id']))
        {
            $aVals['parent_id'] = 0;
        }

        $aVals['is_api'] = true;
        if ($aVals['type'] == 'app' && !empty($aVals['parent_module']) &&  Phpfox::hasCallback($aVals['parent_module'], 'getFeedDetails'))
        {
            $aCallback = Phpfox::callback($aVals['parent_module']. '.getFeedDetails', $aVals['item_id']);
            $aVals['table_prefix'] = isset($aCallback['table_prefix']) ? $aCallback['table_prefix'] : '';
        }

        if (($mId = Phpfox::getService('comment.process')->add($aVals)) === false)
        {
            return $this->error();
        }

        if ($mId == 'pending_moderation')
        {
            return $this->error(_p('your_comment_was_successfully_added_moderated'));
        }

        return $this->get(['id' => $mId], [_p('{{ item }} successfully added.', ['item' => _p('comment')])]);
    }

    /**
     * @description: delete a comment
     * @param $params
     *
     * @return array|bool
     */
    public function delete($params)
    {
        $comment = Phpfox::getService('comment')->getComment($params['id']);
        if (empty($comment))
        {
            return $this->error(_p('The {{ item }} cannot be found.', ['item' => _p('comment__l')]), true);
        }

        if (!Phpfox::getService('comment.process')->deleteInline($params['id'], $comment['type_id']))
        {
            return $this->error(_p('You don\'t have permission to {{ action }} this {{ item }}.', ['action' => _p('view__l'), 'item' => _p('comment__l')]), true);
        }

        return $this->success([], [_p('{{ item }} successfully deleted.', ['item' => _p('comment')])]);
    }

    /**
     * @description get comments of an item
     * @return array|bool
     */
    public function gets()
    {
        //validate params
        $this->requireParams([
            'type_id',
            'item_id'
        ]);

        $this->initSearchParams();
        $type = $this->request()->get('type_id');
        $id = $this->request()->get('item_id');

        //check permission
        if (!empty($type) && !empty($comment['item_id']) && Phpfox::hasCallback($id, 'canViewItem'))
        {
            if (!Phpfox::callback($type . '.canViewItem', $id))
            {
                return $this->error(_p('You don\'t have permission to view comments of this item.'), true);
            }
        }

        $sPrefix = '';
        if ($type == 'app' && Phpfox::hasCallback($this->request()->get('parent_module', ''), 'getFeedDetails'))
        {
            $aCallback = Phpfox::callback($this->request()->get('parent_module', '') . '.getFeedDetails', $this->request()->get('item_id'));
            $sPrefix = isset($aCallback['table_prefix']) ? $aCallback['table_prefix'] : $sPrefix;
        }

        $aRows = Phpfox::getService('comment')->getCommentsForFeed($type, $id, $this->getSearchParam('limit'), $this->getSearchParam('page'), null, $sPrefix);

        $result = [];
        foreach ($aRows as $aRow)
        {
            $result[] = $this->getItem($aRow);
        }
        return $this->success($result);
    }

    /**
     * @description: edit a comment
     * @param $params
     *
     * @return array|bool
     */
    public function put($params)
    {
        $comment = Phpfox::getService('comment')->getComment($params['id']);
        if (empty($comment))
        {
            return $this->error(_p('The {{ item }} cannot be found.', ['item' => _p('comment__l')]), true);
        }

        $this->requireParams(['text']);

        if (Phpfox::getLib('parse.format')->isEmpty($this->request()->get('text')))
        {
            return $this->error(_p('Field {{ field }} is required.', ['field' => 'text']));
        }

        if (Phpfox::getService('comment.process')->updateText($params['id'], $this->request()->get('text')))
        {
            return $this->get(['id' => $params['id']], [_p('{{ item }} successfully updated.', ['item' => _p('comment')])]);
        }

        return $this->error(_p('Cannot {{ action }} this {{ item }}.', ['action' => _p('edit__l'), 'item' => _p('comment__l')]));
    }
}
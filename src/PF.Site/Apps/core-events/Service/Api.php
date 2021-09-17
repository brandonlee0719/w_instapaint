<?php

namespace Apps\Core_Events\Service;

use Phpfox;
use Phpfox_Database;
use Phpfox_Error;
use Phpfox_Validator;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Api
 * @package Apps\Core_Events\Service
 */
class Api extends \Core\Api\ApiServiceBase
{
    /**
     * Event_Service_Api constructor.
     */
    public function __construct()
    {
        $this->setPublicFields([
            "rsvp_id",
            "event_id",
            "view_id",
            "is_featured",
            "is_sponsor",
            "privacy",
            "privacy_comment",
            "module_id",
            "item_id",
            "user_id",
            "title",
            "location",
            "country_iso",
            "country_child_id",
            "postal_code",
            "city",
            "time_stamp",
            "start_time",
            "end_time",
            "image_path",
            "server_id",
            "total_comment",
            "total_like",
            "gmap",
            "address",
            "description",
            "event_date",
            "categories"
        ]);
    }

    /**
     * @description: update a blog
     *
     * @param $params
     *
     * @return array|bool
     */
    public function put($params)
    {
        $this->isUser();

        //check blog is exists
        $aEvent = Phpfox::getService('event')->getForEdit($params['id']);

        if (empty($aEvent) || empty($aEvent['event_id'])) {
            return $this->error(_p('This {{ item }} cannot be found.', ['item' => _p('event__l')]), true);
        }

        //validate data
        $aValidation = [];
        $aVals = $this->request()->getArray('val');

        if (isset($aVals['title'])) {
            $aValidation['title'] = [
                'def' => 'required',
                'title' => _p('Field "{{ field }}" is required.', ['field' => 'val[title]'])
            ];
        }

        if (isset($aVals['location'])) {
            $aValidation['location'] = [
                'def' => 'required',
                'title' => _p('Field "{{ field }}" is required.', ['field' => 'val[location]'])
            ];
        }

        $oValid = Phpfox_Validator::instance()->set(array(
                'sFormName' => 'js_event_form',
                'aParams' => $aValidation
            )
        );

        if ($oValid->isValid($aVals)) {
            if (!empty($aVals['delete_image']) && !Phpfox::getService('event.process')->deleteImage($aEvent['event_id'])) {
                return $this->error(_p('Cannot delete banner of this event.'));
            }

            $aVals = array_merge($aEvent, $aVals);
            if (Phpfox::getService('event.process')->update($aEvent['event_id'], $aVals, $aEvent)) {
                return $this->get(['id' => $aEvent['event_id']],
                    [_p('{{ item }} successfully updated.', ['item' => _p('event')])]);
            }
        }

        return $this->error(_p('Cannot {{ action }} this {{ item }}.',
            ['action' => _p('edit__l'), 'item' => _p('event__l')]), true);
    }

    /**
     * @description: get info of an event
     *
     * @param array $params
     * @param array $messages
     *
     * @return array|bool
     */
    public function get($params, $messages = [])
    {
        if (!($aEvent = Phpfox::getService('event')->canViewItem($params['id'], true))) {
            return $this->error(_p('You don\'t have permission to {{ action }} this {{ item }}.',
                ['action' => _p('view__l'), 'item' => _p('event__l')]), true);
        }
        $aItem = $this->getItem($aEvent);
        if (!empty($params['getDataOnly'])) {
            return $aItem;
        }
        return $this->success($aItem, $messages);
    }

    /**
     * @description: delete an event
     *
     * @param $params
     *
     * @return array|bool
     */
    public function delete($params)
    {
        $this->isUser();

        if (!Phpfox::getService('event.process')->delete($params['id'])) {
            return $this->error(_p('Cannot {{ action }} this {{ item }}.',
                ['action' => _p('delete__l'), 'item' => _p('event__l')]), true);
        }

        return $this->success([], [_p('{{ item }} successfully deleted.', ['item' => _p('event')])]);
    }

    /**
     * @description: add new event item
     * @return array|bool
     */
    public function post()
    {
        //check permission
        $this->isUser();
        if (!Phpfox::getUserParam('event.can_create_event')) {
            return $this->error(_p('You don\'t have permission to add new {{ item }}.', ['item' => _p('event__l')]));
        }

        $aVals = $this->request()->getArray('val');
        $sModule = '';
        $iItem = 0;
        $aCallback = false;
        if (!empty($aVals['module_id']) && !empty($aVals['item_id'])) {
            if (Phpfox::hasCallback($aVals['module_id'],
                    'viewEvent') && ($aCallback = Phpfox::callback($aVals['module_id'] . '.viewEvent',
                    $aVals['item_id'])) === false
            ) {
                return $this->error(_p('Cannot find the parent item.'));
            }

            $sModule = $aVals['module_id'];
            $iItem = $aVals['item_id'];
            if (Phpfox::hasCallback($aVals['module_id'],
                    'checkPermission') && !Phpfox::callback($aVals['module_id'] . '.checkPermission', $aVals['item_id'],
                    'event.share_events')
            ) {
                return $this->error(_p('You don\'t have permission to add new {{ item }} on this item.',
                    ['item' => _p('event__l')]));
            }
        }

        //validate data
        $aValidation = [
            'title' => _p('Field "{{ field }}" is required.', ['field' => 'val[title]']),
            'location' => _p('Field "{{ field }}" is required.', ['field' => 'val[location]'])
        ];

        $oValidator = Phpfox_Validator::instance()->set([
                'sFormName' => 'js_event_form',
                'aParams' => $aValidation
            ]
        );

        if ($oValidator->isValid($aVals)) {
            if (($iFlood = Phpfox::getUserParam('event.flood_control_events')) !== 0) {
                $aFlood = array(
                    'action' => 'last_post', // The SPAM action
                    'params' => array(
                        'field' => 'time_stamp', // The time stamp field
                        'table' => Phpfox::getT('event'), // Database table we plan to check
                        'condition' => 'user_id = ' . Phpfox::getUserId(), // Database WHERE query
                        'time_stamp' => $iFlood * 60 // Seconds);
                    )
                );

                // actually check if flooding
                if (Phpfox::getLib('spam')->check($aFlood)) {
                    return $this->error(_p('you_are_creating_an_event_a_little_too_soon') . ' ' . Phpfox::getLib('spam')->getWaitTime());
                }
            }
            if (Phpfox_Error::isPassed() && $iId = Phpfox::getService('event.process')->add($aVals,
                    (!empty($aVals['module_id']) !== false ? $sModule : 'event'), ($aCallback !== false ? $iItem : 0))
            ) {
                return $this->get(['id' => $iId], [_p('{{ item }} successfully added.', ['item' => _p('event')])]);
            }
        }
        return $this->error();
    }

    /**
     * @description: browse events
     *
     * @return array|bool
     */
    public function gets()
    {
        if (!Phpfox::getUserParam('event.can_access_event')) {
            return $this->error('You don\'t have permission to browse {{ items }}.', ['items' => _p('events__l')]);
        }

        $userId = $this->request()->get('user_id', null);
        if ($userId) {
            $aUser = Phpfox::getService('user')->get($userId);
            if (!$aUser) {
                return $this->error(_p('The {{ item }} cannot be found.', ['item' => _p('user__l')]));
            }

            if (Phpfox::isUser() && Phpfox::getService('user.block')->isBlocked(null, $userId)) {
                return $this->error('Sorry, this content isn\'t available right now');
            }

            $this->search()->setCondition('AND m.view_id ' . ($aUser['user_id'] == Phpfox::getUserId() ? 'IN(0,2)' : '= 0') . ' AND m.module_id = "event" AND m.privacy IN(' . (Phpfox::getParam('core.section_privacy_item_browsing') ? '%PRIVACY%' : Phpfox::getService('core')->getForBrowse($aUser)) . ') AND m.user_id = ' . (int)$aUser['user_id']);
        }

        $this->initSearchParams();
        $oServiceEventBrowse = Phpfox::getService('event.browse');
        $sCategory = null;
        $sView = $this->request()->get('view', false);
        $moduleId = $this->request()->get('module_id', null);
        $itemId = $this->request()->get('item_id', null);

        $this->search()->set(array(
            'type' => 'event',
            'field' => 'm.event_id',
            'ignore_blocked' => true,
            'search_tool' => array(
                'default_when' => 'all-time',
                'when_field' => 'start_time',
                'when_end_field' => 'end_time',
                'when_upcoming' => true,
                'when_ongoing' => true,
                'table_alias' => 'm',
                'search' => array(
                    'default_value' => _p('search_events'),
                    'name' => 'search',
                    'field' => 'm.title'
                ),
                'sort' => array(
                    'latest' => array('m.start_time', _p('latest'), 'ASC'),
                    'most-liked' => array('m.total_like', _p('most_liked')),
                    'most-talked' => array('m.total_comment', _p('most_discussed'))
                ),
                'show' => [$this->getSearchParam('limit')]
            )
        ));

        $aBrowseParams = array(
            'module_id' => 'event',
            'alias' => 'm',
            'field' => 'event_id',
            'table' => Phpfox::getT('event'),
            'hide_view' => array('pending', 'my')
        );

        $bCanBrowse = false;
        switch ($sView) {
            case 'pending':
                if (Phpfox::getUserParam('event.can_approve_events')) {
                    $bCanBrowse = true;
                    $this->search()->setCondition('AND m.view_id = 1');
                }
                break;
            case 'my':
                if (Phpfox::isUser()) {
                    $bCanBrowse = true;
                    $this->search()->setCondition('AND m.user_id = ' . Phpfox::getUserId());
                }
                break;
            default:
                $bCanBrowse = true;
                if ($moduleId && $itemId) {
                    if (Phpfox::hasCallback($moduleId, 'getItem') && Phpfox::callback($moduleId . '.getItem',
                            $itemId) === false
                    ) {
                        return $this->error(_p('Cannot find the parent item.'));
                    }

                    if (Phpfox::hasCallback($moduleId,
                            'checkPermission') && !Phpfox::callback($moduleId . '.checkPermission', $itemId,
                            'event.view_browse_events')
                    ) {
                        return $this->error(_p('You don\'t have permission to browse {{ items }} on this item.',
                            ['items' => _p('events__l')]));
                    }
                    $this->search()->setCondition('AND m.view_id = 0 AND m.privacy IN(%PRIVACY%) AND m.module_id = \'' . Phpfox_Database::instance()->escape($moduleId) . '\' AND m.item_id = ' . (int)$itemId . '');
                } else {
                    switch ($sView) {
                        case 'attending':
                            $oServiceEventBrowse->attending(1);
                            break;
                        case 'may-attend':
                            $oServiceEventBrowse->attending(2);
                            break;
                        case 'not-attending':
                            $oServiceEventBrowse->attending(3);
                            break;
                        case 'invites':
                            $oServiceEventBrowse->attending(0);
                            break;
                    }

                    if ($sView == 'attending') {
                        $this->search()->setCondition('AND m.view_id = 0 AND m.privacy IN(%PRIVACY%)');
                    } else {
                        $this->search()->setCondition('AND m.view_id = 0 AND m.privacy IN(%PRIVACY%) AND m.item_id = 0');
                    }
                }
                break;
        }

        if (!$bCanBrowse) {
            return $this->error('You don\'t have permission to browse those {{ items }}.',
                ['items' => _p('events__l')]);
        }

        $category = $this->request()->get('category', null);
        if ($category) {
            $this->search()->setCondition('AND mcd.category_id = ' . (int)$category);
            $oServiceEventBrowse->category($category);
        }

        if ($sView == 'featured') {
            $this->search()->setCondition('AND m.is_featured = 1');
        }

        $this->search()->browse()->params($aBrowseParams)->execute();

        $aLists = $this->search()->browse()->getRows();
        $result = [];
        foreach ($aLists as $aItems) {
            if (is_array($aItems)) {
                foreach ($aItems as $aItem) {
                    $aEvent = $this->get(['id' => $aItem['event_id'], 'getDataOnly' => true]);
                    if ($aEvent) {
                        $result[] = $aEvent;
                    }
                }
            }
        }
        return $this->success($result);
    }

    /**
     * @description: update user rsvp
     *
     * @param $params
     *
     * @return array|bool
     */
    public function updateRsvp($params)
    {
        $this->isUser();
        $this->requireParams(['rsvp']);

        if (!($aEvent = Phpfox::getService('event')->canViewItem($params['id'], true))) {
            return $this->error(_p('You don\'t have permission to {{ action }} this {{ item }}.',
                ['action' => _p('view__l'), 'item' => _p('event__l')]), true);
        }
        if (Phpfox::getService('event.process')->addRsvp($params['id'], $this->request()->get('rsvp'),
            Phpfox::getUserId())
        ) {
            return $this->success([], [_p('{{ item }} successfully updated.', ['item' => _p('RSVP')])]);
        }

        return $this->error(_p('Cannot update RSVP.'), true);
    }

    /**
     * @param array $params
     *
     * @return array|bool
     */
    public function getGuests($params)
    {
        if (!($aEvent = Phpfox::getService('event')->canViewItem($params['id'], true))) {
            return $this->error(_p('You don\'t have permission to {{ action }} this {{ item }}.',
                ['action' => _p('view__l'), 'item' => _p('event__l')]), true);
        }

        $this->initSearchParams();
        $iRsvp = $this->request()->get('rsvp', 1);
        $iPage = $this->getSearchParam('page');
        $iPageSize = $this->getSearchParam('limit');

        list(, $aInvites) = Phpfox::getService('event')->getInvites($aEvent['event_id'], $iRsvp, $iPage, $iPageSize);
        $results = [];
        foreach ($aInvites as $aInvite) {
            $results[] = $this->getItem($aInvite, 'public',
                ['invite_id', 'event_id', 'rsvp_id', 'user_id', 'time_stamp', 'user_name', 'full_name']);
        }

        return $this->success($results, []);
    }
}
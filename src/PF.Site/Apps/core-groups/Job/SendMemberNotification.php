<?php

namespace Apps\PHPfox_Groups\Job;

use Core\Queue\JobAbstract;
use Phpfox;

/**
 * Class SendMemberNotification
 *
 * @package Apps\PHPfox_Groups\Job
 */
class SendMemberNotification extends JobAbstract
{
    /**
     * @inheritdoc
     */
    public function perform()
    {
        $aParams = $this->getParams();

        if (empty($aParams['owner_id'])) {
            $this->delete();

            return;
        }

        $aOwner = Phpfox::getService('user')->getUser($aParams['owner_id']);
        $aGroupPerms = Phpfox::getService('groups')->getPermsForPage($aParams['page_id']);
        $iPerm = isset($aGroupPerms[$aParams['item_perm']]) ? $aGroupPerms[$aParams['item_perm']] : 0;
        $aGroup = Phpfox::getService('groups')->getPage($aParams['page_id']);

        if (!empty($aParams['item_type']) && Phpfox::hasCallback($aParams['item_type'], 'getLink')) {
            $sLink = Phpfox::callback($aParams['item_type'] . '.getLink', [
                'item_id' => $aParams['item_id']
            ]);
        } else {
            $sLink = Phpfox::getService('groups')->getUrl($aGroup['page_id'], $aGroup['title'], $aGroup['vanity_url']);
        }
        if ($iPerm == 2) {
            $aUsers = Phpfox::getService('groups')->getPageAdmins($aParams['page_id']);
        } else {
            list(, $aUsers) = Phpfox::getService('groups')->getMembers($aParams['page_id']);
        }

        foreach ($aUsers as $aUser) {
            // do not send notification to owner if owner upload photo
            if (isset($aParams['owner_id']) && ($aUser['user_id'] == $aParams['owner_id'])) {
                continue;
            }
            // send notification
            Phpfox::getService('notification.process')->add($aParams['item_type'] . '_newItem_groups',
                $aParams['item_id'], $aUser['user_id'], $aParams['owner_id']);
            // send email
            Phpfox::getLib('mail')->to($aUser['user_id'])
                ->translated(true)
                ->subject(_p('full_name_post_some_items_on_your_group_title', [
                    'full_name' => $aOwner['full_name'],
                    'title' => $aGroup['title'],
                    'items' => $aParams['items_phrase']
                ]))
                ->message(_p('full_name_post_some_items_on_your_group_title_link', [
                    'full_name' => $aOwner['full_name'],
                    'link' => $sLink,
                    'title' => $aGroup['title'],
                    'items' => $aParams['items_phrase']
                ]))
                ->notification('comment.add_new_comment')
                ->send();
        }

        $this->delete();
    }
}

<?php

namespace Apps\PHPfox_Groups\Job;

use Core\Queue\JobAbstract;
use Phpfox;

class SendMemberJoinNotification extends JobAbstract
{
    public function perform()
    {
        $aParams = $this->getParams();

        // get group's admins
        $aAdmins = Phpfox::getService('groups')->getPageAdmins($aParams['aGroup']['page_id']);
        // group link
        $sLink = Phpfox::getService('groups')->getUrl($aParams['aGroup']['page_id'], $aParams['aGroup']['title'],
            $aParams['aGroup']['vanity_url']);

        $aUser = Phpfox::getService('user')->get($aParams['iUserId']);
        // send notifiation for admins of group
        foreach ($aAdmins as $aAdmin) {
            if ($aParams['iUserId'] != $aAdmin['user_id']) {
                Phpfox::getLib('mail')->to($aAdmin['user_id'])
                    ->subject(_p('{{ full_name }} joined your group "{{ title }}"',
                        ['full_name' => $aUser['full_name'], 'title' => $aParams['aGroup']['title']]))
                    ->message(_p('{{ full_name }} joined your group "<a href="{{ link }}">{{ title }}</a>" To view this group follow the link below: <a href="{{ link }}">{{ link }}</a>',
                        [
                            'full_name' => $aUser['full_name'],
                            'link' => $sLink,
                            'title' => $aParams['aGroup']['title']
                        ]))
                    ->translated()
                    ->notification('like.new_like')
                    ->send();

                Phpfox::getService('notification.process')->add('groups_like', $aParams['aGroup']['page_id'],
                    $aAdmin['user_id'], $aParams['iUserId'], true);
            }
        }

        $this->delete();
    }
}

<?php

namespace Apps\Core_Newsletter\Job;

use Core\Queue\JobAbstract;
use Phpfox;

class SendEmailUsers extends JobAbstract
{
    /**
     * Perform a job item
     */
    public function perform()
    {
        $aParams = $this->getParams();
        $iNewsletterId = $aParams['newsletter_id'];
        $iLastId = $aParams['last_id'];
        $sCacheId = 'CORE_NEWSLETTER_TOTAL_USERS_SENT_' . $iNewsletterId;

        // Step 1. Need to check the newsletter state is in progress
        db()->where('n.newsletter_id = ' . (int)$iNewsletterId);

        $aNewsletterInfo = db()->select('*')
            ->join(Phpfox::getT('newsletter_text'), 'nt', 'nt.newsletter_id = n.newsletter_id')
            ->from(Phpfox::getT('newsletter'), 'n')
            ->execute('getSlaveRow');

        // If the newsletter is not exits anymore or not in progress
        if (empty($aNewsletterInfo['newsletter_id']) || $aNewsletterInfo['state'] != CORE_NEWSLETTER_STATUS_IN_PROGRESS) {
            return $this->delete();
        }

        // Step 2: Get the pending members [the round field is unnecessary now]
        $sSelect = Phpfox::getUserField() . ', un.user_id as notification, u.email, u.language_id';
        $sWhere = 'u.user_id > ' . $iLastId;
        // filter the audience
        if (isset($aNewsletterInfo['age_from']) && $aNewsletterInfo['age_from'] > 0) {
            $iFromDate = PHPFOX_TIME - (YEAR_IN_SECOND * $aNewsletterInfo['age_from']);
            $sWhere .= ' AND u.birthday_search < ' . $iFromDate;
        }
        if (isset($aNewsletterInfo['age_to']) && $aNewsletterInfo['age_to'] > 0) {
            $iToDate = PHPFOX_TIME - (YEAR_IN_SECOND * $aNewsletterInfo['age_to']);
            $sWhere .= ' AND u.birthday_search > ' . $iToDate;
        }
        if (isset($aNewsletterInfo['country_iso']) && $aNewsletterInfo['country_iso'] != '') {
            $sWhere .= ' AND country_iso = \'' . $aNewsletterInfo['country_iso'] . '\''; // no extra checks here since it comes directly from DB
        }
        if (isset($aNewsletterInfo['gender']) && $aNewsletterInfo['gender'] > 0) {
            $sWhere .= ' AND gender = ' . (int)$aNewsletterInfo['gender'];
        }
        if (!empty($aNewsletterInfo['user_group_id'])) {
            $aUserGroups = unserialize($aNewsletterInfo['user_group_id']);

            $sWhere .= ' AND u.user_group_id IN(' . implode(',', $aUserGroups) . ')';
        }

        $aUsers = db()->select($sSelect)
            ->from(Phpfox::getT('user'), 'u')
            ->leftJoin(Phpfox::getT('user_notification'), 'un', 'un.user_id = u.user_id')
            ->where($sWhere)
            ->order('u.user_id')
            ->limit((int)$aNewsletterInfo['total'])
            ->execute('getSlaveRows');

        // Save total when the first job run
        if ($iLastId == 0) {
            $iTotalUsers = db()->select('COUNT(u.user_id)')
                ->from(Phpfox::getT('user'), 'u')
                ->where($sWhere)
                ->executeField();

            db()->update(Phpfox::getT('newsletter'), ['total_users' => $iTotalUsers],
                'newsletter_id = ' . $aNewsletterInfo['newsletter_id']);
        }

        // check if the newsletter is completed
        $iTotalSending = count($aUsers);
        if ($iTotalSending == 0) {
            if ($aNewsletterInfo['archive']) {
                db()->update(Phpfox::getT('newsletter'),
                    array('state' => CORE_NEWSLETTER_STATUS_COMPLETED, 'job_id' => null),
                    'newsletter_id = ' . (int)$aNewsletterInfo['newsletter_id']);
            } else {
                db()->delete(Phpfox::getT('newsletter'), 'newsletter_id = ' . $aNewsletterInfo['newsletter_id']);
                db()->delete(Phpfox::getT('newsletter_text'), 'newsletter_id = ' . $aNewsletterInfo['newsletter_id']);
            }

            storage()->del($sCacheId);
            return $this->delete();
        }

        $sOriginalHtmlText = $aNewsletterInfo['text_html'];
        $sOriginalPlainText = $aNewsletterInfo['text_plain'];

        // Step 3: Send the message
        // keyword substitution
        $aSearch = array('{FULL_NAME}', '{USER_NAME}', '{SITE_NAME}');

        $oCache = Phpfox::getLib('cache');
        $sCacheId = $oCache->set('newsletter_' . $iNewsletterId . '_' . $iLastId);
        $oCache->save($sCacheId, $aUsers);

        foreach ($aUsers as $aUser) {
            $aTemp = $aNewsletterInfo;
            if (isset($aUser['notification']) && $aUser['notification'] != '' && $aTemp['privacy'] != 1) { // user does not want to receive mails and admin set this newsletter to NOT override this
                continue;
            }

            $aReplace = array($aUser['full_name'], $aUser['user_name'], Phpfox::getParam('core.site_title'));

            $aTemp['text_html'] = str_ireplace($aSearch, $aReplace, $sOriginalHtmlText);
            $aTemp['subject'] = str_ireplace($aSearch, $aReplace, $aTemp['subject']);

            if ($aTemp['text_plain'] !== null) {
                $aTemp['text_plain'] = str_ireplace($aSearch, $aReplace, $sOriginalPlainText);
            }
            unset($aReplace);
            Phpfox::getService('newsletter.process')->sendExternal($aTemp, $aUser);

            // Save current sent
            $oTotalSent = storage()->get($sCacheId);
            if (is_object($oTotalSent)) {
                $iTotalSent = (int)$oTotalSent->value;
                ++$iTotalSent;
                storage()->del($sCacheId);
                storage()->set($sCacheId, $iTotalSent);
            } else {
                storage()->set($sCacheId, 1);
            }
        }

        // Add new job
        Phpfox::getLib('queue')->instance()->addJob('core_newsletter_send_email_users', [
            'newsletter_id' => $aNewsletterInfo['newsletter_id'],
            'last_id' => !empty($aUser['user_id']) ? $aUser['user_id'] : 99999999,
        ], null, 3600);

        return $this->delete();
    }
}

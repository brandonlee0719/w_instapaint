<?php

namespace Apps\Instapaint\Service;

class BrowsePainterApprovalRequests extends \Phpfox_Service
{
    public function __construct()
    {

    }

    /**
     *
     */
    public function query()
    {
        db()->select('u.last_login, u.joined, dl.daily_limit, ');
        // Include approver_full_name field
        db()->select('user.full_name as approver_full_name, ')->leftJoin(\Phpfox::getT('user'), 'user',
            'user.user_id = ar.approver_user_id')
            // Include daily jobs limit
            ->leftJoin(\Phpfox::getT('instapaint_painter_daily_jobs_limit'), 'dl',
                'ar.user_id = dl.painter_user_id');
    }

    public function getQueryJoins($bIsCount = false, $bNoQueryFriend = false)
    {


    }

    public function processRows(&$aRows)
    {
        $defaultDailyLimit = db()->select('*')
            ->from(':instapaint_painter_daily_jobs_limit')
            ->where(['painter_user_id' => 0])
            ->executeRow();
        $defaultDailyLimit = (int) $defaultDailyLimit['daily_limit'];

        // Add number to each row to display in template:
        foreach ($aRows as $iKey => $aRow) {
            // Cryptic code to get the correct row number through ajax requests:
            $aRows[$iKey]['number'] = ($this->search()->getPage() ? $this->search()->getPage() - 1 : 0) * $this->search()->getDisplay() + ($iKey + 1);

            // Process daily jobs limit:
            $aRows[$iKey]['daily_limit'] = isset($aRows[$iKey]['daily_limit']) && (int) $aRows[$iKey]['daily_limit'] >= 0 ? (int) $aRows[$iKey]['daily_limit'] : (int) $defaultDailyLimit;
        }
    }
}

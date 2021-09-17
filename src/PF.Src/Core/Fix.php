<?php

namespace Core;

use Phpfox;

class Fix
{
    public function fixAppsLikeData($sAppId, $aTables = [])
    {
        $aTables = array_merge($aTables, ['pages_feed' => 'pages_']);

        foreach ($aTables as $key => $value) {
            $aRows = db()->select('l.like_id, l.item_id')
                ->from(':like', 'l')
                ->join(Phpfox::getT($key), 'f', 'l.item_id = f.feed_id')
                ->where('l.type_id = \'app\' AND f.type_id = \'' . $sAppId . '\'')
                ->execute('getSlaveRows');

            foreach ($aRows as $aRow) {
                db()->update(':like',
                    ['feed_table' => $value . 'feed'],
                    ['like_id' => $aRow['like_id']]
                );
            }
        }
    }

    public function fixAppsCommentData($sAppId, $aTables = [])
    {
        $aTables = array_merge($aTables, ['pages_feed' => 'pages_']);

        foreach ($aTables as $key => $value) {
            $aRows = db()->select('c.comment_id')
                ->from(':comment', 'c')
                ->join(Phpfox::getT($key), 'f', 'c.item_id = f.feed_id')
                ->where('c.type_id = \'app\' AND f.type_id = \'' . $sAppId . '\'')
                ->execute('getSlaveRows');

            foreach ($aRows as $aRow) {
                db()->update(':comment',
                    ['feed_table' => $value . 'feed'],
                    ['comment' => $aRow['comment_id']]
                );
            }
        }
    }
}
<?php
namespace Apps\Core_Forums\Controller;

use Phpfox;
use Phpfox_Component;

defined('PHPFOX') or exit('NO DICE!');

class RecentController extends Phpfox_Component
{
    public function process()
    {
        header('Content-type: application/javascript');
        $ids = [];
        $forums = Phpfox::getService('forum')->getForums();
        foreach ($forums as $forum) {
            $ids[] = $forum['forum_id'];

            $childs = Phpfox::getService('forum')->id($forum['forum_id'])->getChildren();
            if ($childs) {
                foreach ($childs as $id) {
                    $ids[] = $id;
                }
            }
        }

        if (empty($ids)) {
            $ids = array(0);
        }
        $ids = Phpfox::getService('forum.thread')->getCanViewForumIdList($ids);
        $cond[] = 'ft.forum_id IN(' . implode(',', $ids) . ') AND ft.group_id = 0 AND ft.view_id >= 0';
        list(, $threads) = Phpfox::getService('forum.thread')
            ->get($cond, 'ft.time_update DESC', 0, 20);
        $json = [];
        foreach ($threads as $thread) {
            $json[] = (object)[
                'thread_id' => $thread['thread_id'],
                'title' => $thread['title'],
                'permalink' => Phpfox::permalink('forum.thread', $thread['thread_id'], $thread['title']),
                'user' => htmlspecialchars($thread['full_name']),
                'created' => Phpfox::getLib('date')->convertTime($thread['time_stamp'])
            ];
        }

        echo ';function __Threads(callback) { var threads = ' . json_encode($json) . '; if (typeof(callback) == \'function\') { callback(threads); } };';
        exit;
    }
}
<?php

namespace Core\Queue;

use Phpfox;
use Phpfox_Database;

/**
 * Class JobRepositoryDatabase
 *
 * @package Core\Queue
 */
class JobRepositoryDatabase implements JobRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function addJob($data, $queue_name, $expire_time, $waiting_time)
    {
        $is_running = 0;
        $last_run = 0;

        if ($waiting_time) {
            $is_running = 1;
            $last_run = time() - $expire_time + $waiting_time;
        }
        $iId = Phpfox_Database::instance()->insert(
            Phpfox::getT('cron_job'), [
            'queue_name'  => (string)$queue_name,
            'data'        => $data,
            'expire_time' => $expire_time,
            'is_running'  => $is_running,
            'last_run'    => $last_run,
        ]);

        return $iId;
    }

    /**
     * @inheritdoc
     */
    public function getJob($queue_name)
    {
        $db = Phpfox_Database::instance();

        try {

            $db->update(Phpfox::getT('cron_job'), ['is_running' => 0], 'last_run+expire_time<' . time());

            $db->beginTransaction();

            $where = 'is_running=0 and queue_name=\'' . $queue_name . '\'';

            $row = $db->select('*')
                ->from(Phpfox::getT('cron_job'))
                ->where($where)
                ->forUpdate()
                ->execute('getRow');

            if ($row) {
                $db->update(Phpfox::getT('cron_job'), [
                    'is_running' => 1,
                    'last_run' => time(),
                ], 'id=' . $row['id']);
            }


            $db->commit();

            return [
                'reversation_id' => isset($row['id']) ? $row['id'] : 0,
                'job_id' => isset($row['id']) ? $row['id'] : 0,
                'data' => isset($row['data']) ? $row['data'] : [],
            ];

        } catch (\Exception $ex) {
            $db->rollback();
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function getJobs($queue_name, $limit)
    {
        $db = Phpfox_Database::instance();

        try {
            $db->beginTransaction();

            $where = 'is_running=0 and queue_name=\'' . $queue_name . '\'';

            $rows = $db->select('*')
                ->from(Phpfox::getT('cron_job'))
                ->where($where)
                ->forUpdate()
                ->limit(1, $limit)
                ->execute('getRows');

            if ($rows) {
                foreach ($rows as $row) {
                    $db->update(Phpfox::getT('cron_job'), [
                        'is_running' => 1,
                        'last_run'   => time(),
                    ], 'id=' . $row['id']);
                }
            }

            $db->commit();

            return array_map(function ($row) {
                return [
                    'reversation_id' => $row['id'],
                    'job_id'         => $row['id'],
                    'data'           => $row['data'],
                ];
            }, $rows);

        } catch (\Exception $ex) {
            $db->rollback();
        }

        return [];
    }

    /**
     * @inheritdoc
     */
    public function deleteJob($reversationId)
    {
        Phpfox_Database::instance()
            ->delete(Phpfox::getT('cron_job'), 'id=' . intval($reversationId));
    }
}
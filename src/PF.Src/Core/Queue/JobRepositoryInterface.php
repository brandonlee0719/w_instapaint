<?php

namespace Core\Queue;

interface JobRepositoryInterface
{
    /**
     *
     * @param string $data
     * @param string $queue_name
     * @param int    $expire_time
     * @param int    $waiting_time
     *
     * @return int Return Job ID
     */
    public function addJob($data, $queue_name, $expire_time, $waiting_time);

    /**
     * @param string $queue_name
     *
     * @return array|bool
     */
    public function getJob($queue_name);

    /**
     * @param null $queue_name
     * @param int  $limit
     *
     * @return array
     */
    public function getJobs($queue_name, $limit);

    /**
     * Delete job from queue
     *
     * @param $reversationId
     */
    public function deleteJob($reversationId);
}
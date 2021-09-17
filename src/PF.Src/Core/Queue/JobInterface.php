<?php

namespace Core\Queue;

/**
 * Interface JobInterface
 *
 * @package Core\Queue
 */
interface JobInterface
{
    /**
     * Perform a job item
     */
    public function perform();
}
<?php

namespace Core\Queue;

use Core\Event;
use Phpfox_Cache;
use Phpfox_Plugin;

/**
 * Class Manager
 *
 * @package Core\Queue
 */
class Manager
{
    /**
     * Maximum job fetch per request
     */
    const DEFAULT_LIMIT = 5;

    /**
     * Default life time
     */
    const DEFAULT_LIFETIME = 600;

    /**
     * Default queue name
     */
    const DEFAULT_QUEUE_NAME = 'default';

    /**
     * @var JobRepositoryInterface
     */
    private $handler;

    /**
     * @var Manager
     */
    private static $singleton;

    /**
     * @var array
     */
    private $handlerNames = ['empty'];

    /**
     * Manager constructor
     */
    private function __construct()
    {

    }

    /**
     * @return Manager
     */
    public static function instance()
    {
        if (null == self::$singleton) {
            self::$singleton = new self();
            self::$singleton->loadHandlers();
        }

        return self::$singleton;
    }

    private function loadHandlers()
    {
        $oCache = Phpfox_Cache::instance();
        $iCachedId = $oCache->set('queue_handlers');

        if (!($this->handlerNames = $oCache->get($iCachedId))) {

            (($sPlugin = Phpfox_Plugin::get('job_queue_init')) ? eval($sPlugin) : false);

            if (!$this->handlerNames) {
                $this->handlerNames = ['empty'];
            }

            $oCache->save($iCachedId, $this->handlerNames);
        }
    }

    /**
     * @param string $name
     * @param string $class
     *
     * @return Manager
     */
    public function addHandler($name, $class)
    {
        $this->handlerNames[$name] = $class;

        return $this;
    }

    /**
     * @return \Core\Queue\JobRepositoryInterface
     */
    private function create()
    {
        $handler = Event::trigger('lib_phpfox_job_queue_get_handler');

        if (null == $handler) {
            $handler = new JobRepositoryDatabase();
        }

        return $handler;
    }

    /**
     * @return JobRepositoryInterface
     */
    public function getHandler()
    {
        if (null == $this->handler) {
            $this->handler = $this->create();
        }

        return $this->handler;
    }

    /**
     * <example>
     * addJob('notify_liked', [user_id: 1, item_id: 4], 0, 600, 0)
     * </example>
     *
     * @param string $name
     * @param mixed  $params
     * @param string $queue_name default  "default'
     * @param int    $expire_time
     * @param int    $waiting_time
     *
     * @return int
     */
    public function addJob($name, $params, $queue_name = null, $expire_time = 0, $waiting_time = 0)
    {
        if (empty($this->handlerNames[$name]) && (!defined('PHPFOX_INSTALLER') || !PHPFOX_INSTALLER)) {
            return 0;
        }

        if (null == $queue_name) {
            $queue_name = self::DEFAULT_QUEUE_NAME;
        }

        if (null == $expire_time) {
            $expire_time = self::DEFAULT_LIFETIME;
        }

        $data = json_encode([
            'job'    => $name,
            'params' => $params,
        ]);

        return $this->getHandler()->addJob($data, $queue_name, $expire_time, $waiting_time);
    }

    /**
     * @param string $queue_name
     *
     * @return array|bool
     */
    public function getJob($queue_name = null)
    {
        if (null == $queue_name) {
            $queue_name = self::DEFAULT_QUEUE_NAME;
        }

        $item = $this->getHandler()->getJob($queue_name);

        if ($item) {
            $this->makeJob($item);
        }

        return null;
    }

    /**
     * @param array $item
     *
     * @return JobInterface
     */
    private function makeJob($item)
    {
        $data = json_decode($item['data'], true);

        $params = $data['params'];

        if (!empty($this->handlerNames[$data['job']])) {
            $class = $this->handlerNames[$data['job']];

            return new $class($item['reversation_id'], $item['job_id'], $data['job'], $params);
        }

        // fallback handlers
        $this->deleteJob($item['reversation_id']);

        return null;
    }

    /**
     * @param null $queue_name
     * @param int  $limit
     *
     * @return JobInterface[]
     */
    public function getJobs($queue_name = null, $limit = null)
    {
        if (null == $queue_name) {
            $queue_name = self::DEFAULT_QUEUE_NAME;
        }

        if (null == $limit) {
            $limit = self::DEFAULT_LIMIT;
        }

        $items = $this->getHandler()->getJobs($queue_name, $limit);

        $jobs = [];

        foreach ($items as $item) {
            if (false != ($job = $this->makeJob($item))) {
                $jobs[] = $job;
            }
        }

        return $jobs;
    }

    /**
     * Delete job from queue
     *
     * @param $reversationId
     */
    public function deleteJob($reversationId)
    {
        $this->getHandler()->deleteJob($reversationId);
    }

    public function work()
    {
        $check_time = time();
        while ($check_time + 600 > time()) {
            // do check connect again

            $db  = \Phpfox::getLib('database');

            $db->reconnect();

            $jobs = $this->getJobs();
            if ($jobs) {
                foreach ($jobs as $job) {
                    $job->perform();
                }
                sleep(2);
            } else {
                sleep(3);
            }
        }
    }
}
<?php

namespace Core;

class Profiler
{
    /**
     * @var array
     */
    private $items = [];

    /**
     * @var int
     */
    private $index = 0;

    /**
     * @var Profiler
     */
    static private $_instance;

    /**
     *
     */
    private function _construct()
    {
    }

    /**
     * @return \Core\Profiler
     */
    private static function instance()
    {
        if (null == self::$_instance) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    /**
     * @param      $group
     * @param null $info
     *
     * @return string
     */
    private function _start($group, $info = null)
    {
        $time = microtime(true);

        ++$this->index;

        $this->items[$this->index] = [
            'group'      => $group,
            'info'       => $info,
            'time_start' => $time,
            'time_stop'  => $time,
            'time_usage' => 0,
        ];

        return $this->index;
    }

    private function _end($key)
    {
        if (empty($this->items[$key])) {
            return;
        }

        $time = microtime(true);

        $this->items[$key]['time_end'] = $time;
        $this->items[$key]['time_usage'] = $time - $this->items[$key]['time_start'];
    }

    /**
     * @return string
     */
    private function _groups()
    {
        $groups = [];

        foreach ($this->items as $item) {

            $key = $item['group'];


            if (empty($groups[$key])) {
                $groups[$key] = [
                    'group'        => $key,
                    'counter'      => 0,
                    'time_total'   => 0,
                    'time_average' => 0,
                    'time_max'     => 0,
                ];
            }

            $groups[$key]['counter'] += 1;
            $groups[$key]['time_total'] += $item['time_usage'];

            if ($groups[$key]['time_max'] < $item['time_usage']) {
                $groups[$key]['time_max'] = $item['time_usage'];
            }
        }

        foreach ($groups as $key => $group) {
            $groups[$key]['time_average'] = $groups[$key]['time_total'] / $groups[$key]['counter'];
        }

        return json_encode([
            'groups'  => $groups,
            'details' => $this->items,
        ], JSON_PRETTY_PRINT);
    }

    /**
     * @param $group
     * @param $info
     *
     * @return string
     */
    public static function start($group, $info = null)
    {
        return self::instance()->_start($group, $info);
    }

    /**
     * @param $key
     */
    public static function end($key)
    {
        self::instance()->_end($key);
    }

    /**
     *
     */
    public static function groups()
    {
        return self::instance()->_groups();
    }
}
<?php

namespace Core;

/**
 * Class Search
 *
 * @package Core
 *
 * @method Search primary($column)
 * @method Search field($name)
 * @method Search url($url)
 * @method Search sort(array $sort)
 * @method Search show(array $show)
 */
class Search
{
    private $_cache = [];

    public function __construct()
    {

    }

    public function __call($method, $args)
    {
        $this->_cache[$method] = $args;

        return $this;
    }


    public function make($type = '')
    {
        if (empty($type)) {
            $type = '';
        }
        return \Phpfox_Search::instance()->set([
            'type'        => $type,
            'field'       => $this->_cache['primary'][0],
            'search_tool' => [
                'search' => [
                    'action'        => $this->_cache['url'][0],
                    'default_value' => _p('Search ' . ucfirst($type) . '...'),
                    'name'          => 'search',
                    'field'         => [$this->_cache['field'][0]],
                ],
                'sort'   => $this->_cache['sort'][0],
                'show'   => $this->_cache['show'][0],
            ],
        ]);
    }
}
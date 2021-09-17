<?php

namespace Core;

class Cache
{
    private $_name;
    private $_cache;
    private $_id;

    public function __construct($name = null)
    {
        $this->_cache = \Phpfox_Cache::instance();
        if ($name !== null) {
            $this->_name = $name;
            $this->_id = $this->_cache->set($name);
        }
    }

    public function set($key = null, $value = null)
    {
        if ($value === null && $this->_name) {
            $value = $key;
            $key = $this->_name;
        }
        return $this->_cache->save($this->_cache->set($key), $value);
    }

    public function del($key = null)
    {
        if ($key === null && $this->_name) {
            $key = $this->_name;
        }

        return $this->_cache->remove($this->_cache->set($key));
    }

    public function get($key = null, $minutes = 0)
    {
        if ($key === null && $this->_name) {
            $key = $this->_name;
        }
        $sId = $this->_cache->set($key);
        return $this->_cache->get($sId, $minutes);
    }

    public function exists($key = null)
    {
        if ($key === null && $this->_name) {
            $key = $this->_name;
        }

        $key = $this->_cache->set($key);
        return $this->_cache->isCached($key);
    }

    public function purge()
    {
        return $this->_cache->remove();
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->_cache, $method], $args);
    }
}
<?php

namespace Core;

/**
 * Class Storage
 *
 * @package Core
 *
 * @method Storage order($direction = 'ASC')
 */
class Storage
{
    private $_args
        = [
            'order' => 'ASC',
        ];

    /**
     * @var \Core\Db
     */
    public $db;

    public function __construct()
    {
        $this->db = new Db();
    }

    public function updateById($id, $value)
    {
        $cache = $this->getById($id);
        $values = (is_string($value) ? $value : array_merge((array)$cache->value, (array)$value));

        $this->db->update(':cache', [
            'cache_data' => json_encode($values),
        ], ['cache_id' => $id]);

        $this->_reset($cache->key, $cache->data_size);

        return $this->getById($id);
    }

    public function update($key, $value, $id = null)
    {
        $cache = $this->get($key, $id);
        if (is_string($value)) {
            $values = $value;
        } elseif (is_array($value) && is_object($cache)) {
            $values = array_merge($value, (array)$cache->value);
        } else {
            $values = (array)$value;
        }

        $where = ['file_name' => $key];
        if ($id) {
            $where['data_size'] = (int)$id;
        }
        $this->db->update(':cache', [
            'cache_data' => json_encode($values),
        ], $where);

        $this->_reset($key, $id);

        return $this->get($key, $id);
    }

    public function delById($id)
    {
        $cache = $this->getById($id);
        $this->db->delete(':cache', ['cache_id' => (int)$id]);
        $this->_reset($cache->key, $cache->data_size);

        return true;
    }

    public function del($key, $id = null)
    {
        if ($id) {
            $this->_reset($key, $id);
            $this->db->delete(':cache', ['file_name' => $key, 'data_size' => $id]);
            $cacheKey = ($id === null) ? $key : $key . '/' . $id;
            $cache = cache('storage_' . $cacheKey);
            $cache->del();

            return true;
        }

        $this->_reset($key);
        $this->db->delete(':cache', ['file_name' => $key]);

        return true;
    }

    public function set($key, $value, $id = 0)
    {
        $cache = $this->all($key);
        $iteration = (count($cache));
        $iteration++;

        $cacheId = $this->db->insert(':cache', [
            'file_name'  => $key,
            'cache_data' => json_encode($value),
            'data_size'  => (int)$id,
            'time_stamp' => $iteration,
        ]);

        $cacheKey = ($id === 0) ? $key : $key . '/' . $id;
        $cache = cache('storage_' . $cacheKey);
        $cache->del();

        return $cacheId;
    }

    public function incr($key, $number = 1, $id = null)
    {
        $total = 1;
        $storage = $this->get($key, $id);
        if (isset($storage->value)) {
            $total = ((int)$storage->value + $number);
        }
        $this->del($key, $id);
        $this->set($key, $total, ($id ? $id : 0));

        return $total;
    }

    /**
     * @param $key
     * @param $order
     *
     * @return Storage\Object[]
     */
    public function all($key, $order = null)
    {
        $return = [];
        if ($order === null) {
            $order = 'time_stamp ' . $this->_args['order'];
        }
        $objects = $this->db->select('*')->from(':cache')->where(['file_name' => $key])->order($order)->all();
        foreach ($objects as $object) {
            $return[] = $this->_build($object);
        }

        return $return;
    }

    public function updateOrderById($ids)
    {
        if (is_string($ids)) {
            $ids = explode(',', trim($ids, ','));
        }

        $ids = array_reverse($ids);
        $iteration = 0;
        foreach ($ids as $id) {
            $iteration++;
            db()->update(':cache', ['time_stamp' => $iteration], ['cache_id' => (int)$id]);
        }

        return true;
    }

    public function getById($id)
    {
        $object = $this->db->select('*')->from(':cache')->where(['cache_id' => $id])->get();
        if (isset($object['cache_id'])) {
            return $this->_build($object);
        }

        return null;
    }

    public function get($key, $id = null)
    {
        $where = ['file_name' => $key];
        if ($id !== null) {
            $where['data_size'] = (int)$id;
        }

        $cacheKey = ($id === null) ? $key : $key . '/' . $id;
        $cache = cache('storage_' . $cacheKey);
        if (false !== ($object = $cache->get())) {
            
        } else {
            $object = $this->db->select('*')->from(':cache')->where($where)->get();
            if (is_array($object)) {
                $cache->set('storage/' . $cacheKey, $object);
            }
        }

        $item = null;
        if (isset($object['cache_id'])) {
            $item = $this->_build($object);
        }

        return $item;
    }

    public function __call($method, $args)
    {
        if (isset($this->_args[$method])) {
            $this->_args[$method] = $args[0];
        }
        return $this;
    }

    private function _build($object)
    {
        return new Storage\Object([
            'id'        => $object['cache_id'],
            'key'       => $object['file_name'],
            'order'     => $object['time_stamp'],
            'value'     => json_decode($object['cache_data']),
            'data_size' => $object['data_size'],
        ]);
    }

    private function _reset($key, $id = null)
    {
        cache('storage_' . $key . ($id ? '_' . $id : ''))->del();
    }
}
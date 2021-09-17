<?php

namespace Core;

/**
 * Interface defining a client able to execute commands against Redis.
 *
 * All the commands exposed by the client generally have the same signature as
 * described by the Redis documentation, but some of them offer an additional
 * and more friendly interface to ease programming which is described in the
 * following list of methods:
 *
 * @method int    del(array $keys)
 * @method string dump($key)
 * @method int    exists($key)
 * @method int    expire($key, $seconds)
 * @method int    expireat($key, $timestamp)
 * @method array  keys($pattern)
 * @method int    move($key, $db)
 * @method mixed  object($subcommand, $key)
 * @method int    persist($key)
 * @method int    pexpire($key, $milliseconds)
 * @method int    pexpireat($key, $timestamp)
 * @method int    pttl($key)
 * @method string randomkey()
 * @method mixed  rename($key, $target)
 * @method int    renamenx($key, $target)
 * @method array  scan($cursor, array $options = null)
 * @method array  sort($key, array $options = null)
 * @method int    ttl($key)
 * @method mixed  type($key)
 * @method int    append($key, $value)
 * @method int    bitcount($key, $start = null, $end = null)
 * @method int    bitop($operation, $destkey, $key)
 * @method int    decr($key)
 * @method int    decrby($key, $decrement)
 * @method string get($key)
 * @method array  get_as_array($key)
 * @method int    getbit($key, $offset)
 * @method string getrange($key, $start, $end)
 * @method string getset($key, $value)
 * @method int    incr($key)
 * @method int    incrby($key, $increment)
 * @method string incrbyfloat($key, $increment)
 * @method array  mget(array $keys)
 * @method mixed  mset(array $dictionary)
 * @method int    msetnx(array $dictionary)
 * @method mixed  psetex($key, $milliseconds, $value)
 * @method mixed  set($key, $value, $expireResolution = null, $expireTTL = null, $flag = null)
 * @method int    setbit($key, $offset, $value)
 * @method int    setex($key, $seconds, $value)
 * @method int    setnx($key, $value)
 * @method int    setrange($key, $offset, $value)
 * @method int    strlen($key)
 * @method int    hdel($key, array $fields)
 * @method int    hexists($key, $field)
 * @method string hget($key, $field)
 * @method array  hgetall($key)
 * @method int    hincrby($key, $field, $increment)
 * @method string hincrbyfloat($key, $field, $increment)
 * @method array  hkeys($key)
 * @method int    hlen($key)
 * @method array  hmget($key, array $fields)
 * @method mixed  hmset($key, array $dictionary)
 * @method array  hscan($key, $cursor, array $options = null)
 * @method int    hset($key, $field, $value)
 * @method int    hsetnx($key, $field, $value)
 * @method array  hvals($key)
 * @method array  blpop(array $keys, $timeout)
 * @method array  brpop(array $keys, $timeout)
 * @method array  brpoplpush($source, $destination, $timeout)
 * @method string lindex($key, $index)
 * @method int    linsert($key, $whence, $pivot, $value)
 * @method int    llen($key)
 * @method string lpop($key)
 * @method int    lpush($key, array $values)
 * @method int    lpushx($key, $value)
 * @method array  lrange($key, $start, $stop)
 * @method int    lrem($key, $count, $value)
 * @method mixed  lset($key, $index, $value)
 * @method mixed  ltrim($key, $start, $stop)
 * @method string rpop($key)
 * @method string rpoplpush($source, $destination)
 * @method int    rpush($key, array $values)
 * @method int    rpushx($key, $value)
 * @method int    sadd($key, array $members)
 * @method int    scard($key)
 * @method array  sdiff(array $keys)
 * @method int    sdiffstore($destination, array $keys)
 * @method array  sinter(array $keys)
 * @method int    sinterstore($destination, array $keys)
 * @method int    sismember($key, $member)
 * @method array  smembers($key)
 * @method int    smove($source, $destination, $member)
 * @method string spop($key)
 * @method string srandmember($key, $count = null)
 * @method int    srem($key, $member)
 * @method array  sscan($key, $cursor, array $options = null)
 * @method array  sunion(array $keys)
 * @method int    sunionstore($destination, array $keys)
 * @method int    zadd($key, array $membersAndScoresDictionary)
 * @method int    zcard($key)
 * @method string zcount($key, $min, $max)
 * @method string zincrby($key, $increment, $member)
 * @method int    zinterstore($destination, array $keys, array $options = null)
 * @method array  zrange($key, $start, $stop, array $options = null)
 * @method array  zrangebyscore($key, $min, $max, array $options = null)
 * @method int    zrank($key, $member)
 * @method int    zrem($key, $member)
 * @method int    zremrangebyrank($key, $start, $stop)
 * @method int    zremrangebyscore($key, $min, $max)
 * @method array  zrevrange($key, $start, $stop, array $options = null)
 * @method array  zrevrangebyscore($key, $min, $max, array $options = null)
 * @method int    zrevrank($key, $member)
 * @method int    zunionstore($destination, array $keys, array $options = null)
 * @method string zscore($key, $member)
 * @method array  zscan($key, $cursor, array $options = null)
 * @method array  zrangebylex($key, $start, $stop, array $options = null)
 * @method int    zremrangebylex($key, $min, $max)
 * @method int    zlexcount($key, $min, $max)
 * @method int    pfadd($key, array $elements)
 * @method mixed  pfmerge($destinationKey, array $sourceKeys)
 * @method int    pfcount(array $keys)
 * @method mixed  pubsub($subcommand, $argument)
 * @method int    publish($channel, $message)
 * @method mixed  discard()
 * @method array  exec()
 * @method mixed  multi()
 * @method mixed  unwatch()
 * @method mixed  watch($key)
 * @method mixed  eval($script, $numkeys, $keyOrArg1 = null, $keyOrArgN = null)
 * @method mixed  evalsha($script, $numkeys, $keyOrArg1 = null, $keyOrArgN = null)
 * @method mixed  script($subcommand, $argument = null)
 * @method mixed  auth($password)
 * @method string echo ($message)
 * @method mixed  ping($message = null)
 * @method mixed  select($database)
 * @method mixed  bgrewriteaof()
 * @method mixed  bgsave()
 * @method mixed  client($subcommand, $argument = null)
 * @method mixed  config($subcommand, $argument = null)
 * @method int    dbsize()
 * @method mixed  flushall()
 * @method mixed  flushdb()
 * @method array  info($section = null)
 * @method int    lastsave()
 * @method mixed  save()
 * @method mixed  slaveof($host, $port)
 * @method mixed  slowlog($subcommand, $argument = null)
 * @method array  time()
 * @method array  command()
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class Redis
{
    private static $_obj = null;

    public function __construct()
    {
        if (self::$_obj === null) {
            $setting = PHPFOX_DIR_SETTINGS . 'redis.sett.php';
            if (file_exists($setting)) {
                $settings = require($setting);
                if (!empty($settings['enabled'])) {
                    self::$_obj = new \Predis\Client($settings);
                    try {
                        self::$_obj->connect();
                    } catch (\Exception $e) {
                        self::$_obj = false;
                    }
                }
            }

            self::$_obj = false;
        }
    }

    public function enabled()
    {
        return (is_object(self::$_obj) ? true : false);
    }

    public function user($id = null, $full = false)
    {
        if ($id === null) {
            $id = user()->id;
        }

        $user = $this->get('user/' . $id);
        if (!$user) {
            $row = db()->select('u.*, uf.*')
                ->from(':user', 'u')
                ->join(':user_field', 'uf', 'uf.user_id = u.user_id')
                ->where(['u.user_id' => $id])
                ->get();

            $this->set('user/' . $id, $row);

            $user = $this->get('user/' . $id);
        }

        $user = (array)$user;

        if ($full) {
            $key = 'activity/points/' . $user['user_id'];
            if (!redis()->exists($key)) {
                $total = db()->select('*')->from(':user_activity')->where(['user_id' => $user['user_id']])->get();
                redis()->incrby($key, $total['activity_points']);
            }

            $user['activity_points'] = redis()->get($key);
            $user['title'] = $user['full_name'];
            $user['is_friend'] = (redis()->get('is/friends/' . user()->id . '/' . $user['user_id']) ? true : false);
            $user['is_friend_of_friend'] = false;
            $user['is_friend_request'] = false;
            if (auth()->isLoggedIn() && \Phpfox::isModule('friend') && user()->id != $user['user_id']) {
                $user['is_friend_of_friend'] = (\Phpfox::getService('friend')->isFriendOfFriend($user['user_id']) ? true : false);
                if (!$user['is_friend']) {
                    $iRequestId = \Phpfox::getService('friend.request')->isRequested(user()->id, $user['user_id'], true);
                    $user['is_friend_request'] = ($iRequestId ? 2 : false);
                    $user['is_friend_request_id'] = $iRequestId;
                    if (!$user['is_friend_request']) {
                        $iRequestId = \Phpfox::getService('friend.request')->isRequested($user['user_id'], user()->id, true);
                        $user['is_friend_request'] = ($iRequestId ? 3 : false);
                        $user['is_friend_request_id'] = $iRequestId;
                    }
                }
            }
        }

        return $user;
    }

    public function __call($method, $args)
    {
        if (!is_object(self::$_obj)) {
            error('Redis not initiated.');
        }

        $actual_command = $method;
        switch ($method) {
            case 'set':
                $args[1] = json_encode($args[1]);
                break;
            case 'get_as_array':
                $method = 'get';
                break;
        }

        $return = call_user_func_array([self::$_obj, $method], $args);

        switch ($actual_command) {
            case 'get':
                $return = json_decode($return);
                break;
            case 'get_as_array':
                $return = json_decode($return, true);
                break;
        }

        return $return;
    }
}
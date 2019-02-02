<?php
/**
 * Created by PhpStorm.
 * User: Администратор
 * Date: 10.12.13
 * Time: 12:02
 */

namespace Cpeople\Classes\Cache;

//use Bitrix\Main\DB\Exception;

class EngineMemcached implements Engine
{
    /**
     * @var Memcache
     */
    static $cache;
    private $ttl = 30;
    private $cacheIdPrefix = 'cp_';

    public function __construct($options = null)
    {
        if (!function_exists('memcache_connect'))
        {
            throw new CacheException("Memcached module is not installed");
        }

        $this->connect($options);
    }

    private function connect($options)
    {
        if (!isset(self::$cache))
        {
            $options = $options || array();
            if (empty($options['host'])) $options['host'] = 'localhost';
            if (empty($options['port'])) $options['port'] = 11211;

            if (!self::$cache = memcache_connect($options['host'], $options['port']))
            {
                throw new CacheException("Could not connect to memcached server on {$options['host']}:{$options['port']}");
            }
        }

        return self::$cache;
    }

    public function setTTL($ttl)
    {
        $this->ttl = (int) $ttl;
    }

    public function valid($cacheId, $ttl = 0)
    {
        return (bool) self::$cache->get($cacheId);
    }

    public function save($cacheId, $data)
    {
        self::$cache->set($this->getCacheId($cacheId), $data, false, $this->ttl);
    }

    public function get($cacheId)
    {
        return self::$cache->get($this->getCacheId($cacheId));
    }

    public function clear()
    {
        self::$cache->flush();
    }

    public function clearByTag($tag)
    {
    }

    private function getCacheId($cacheId)
    {
        return $this->cacheIdPrefix . $cacheId;
    }
}
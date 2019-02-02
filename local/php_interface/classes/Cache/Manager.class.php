<?php
/**
 * Created by PhpStorm.
 * User: graymur
 * Date: 26.11.13
 * Time: 13:12
 */

namespace Cpeople\Classes\Cache;


class Manager
{
    static $instance;
    static $cachePath;
    static $enabled = true;
    private $lastCached;

    /**
     * @var \Cpeople\Classes\Cache\Engine
     */
    private $engine;

    private function __construct()
    {

    }

    static function instance()
    {
        if (!isset(self::$instance))
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function setEngine(\Cpeople\Classes\Cache\Engine $engine)
    {
        $this->engine = $engine;
    }

    public function enabled()
    {
        return self::$enabled;
//        return isset(self::$enabled) ? self::$enabled : \Cpeople\Classes\Registry::bitrixCacheEnabled();
    }

    public function setEnabled($value)
    {
        self::$enabled = (bool) $value;
    }

    public function setCachePath($path)
    {
        if (!file_exists($path))
        {
            @mkdir($path, 0777, true);
        }

        $this->check(file_exists($path), 'Cache path does not exist');

        self::$cachePath = $path;
    }

    private function check($condition, $exceptionMessage)
    {
        if (!$condition)
        {
            throw new CacheException($exceptionMessage);
        }
    }

    public function valid($cacheId, $ttl = null)
    {
        if (!$this->enabled())
        {
            return false;
        }

        return $this->engine->valid($cacheId, $ttl);
    }

    public function start()
    {
        ob_start();
    }

    public function end($cacheId, $flush = true)
    {
        $data = ob_get_clean();

        $this->save($cacheId, $data);

        if ($flush)
        {
            echo $data;
        }
    }

    public function serialize($cacheId, $data)
    {
        $this->save($cacheId, serialize($data));
    }

    public function save($cacheId, $data)
    {
        if (!$this->enabled())
        {
            return false;
        }

        $this->engine->save($cacheId, $data);
    }

    public function get($cacheId)
    {
//        $this->check($this->valid($cacheId), "Cache with ID $cacheId does not exist");

        return $this->engine->get($cacheId);
    }

    public function unserialize($cacheId)
    {
        return unserialize($this->get($cacheId));
    }

    public function output($cacheId)
    {
        echo $this->get($cacheId);
    }

    public function clear()
    {
        $this->engine->clear();
    }

    public function clearByTag($tag)
    {
        $this->engine->clearByTag($tag);
    }
}


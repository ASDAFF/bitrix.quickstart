<?php
/**
 * Created by PhpStorm.
 * User: Администратор
 * Date: 10.12.13
 * Time: 12:02
 */

namespace Cpeople\Classes\Cache;

class EngineAPC implements Engine
{
    public function __construct($path)
    {
    }

    public function valid($cacheId, $ttl)
    {
    }

    public function save($cacheId, $data)
    {
    }

    public function get($cacheId)
    {
    }

    public function clear()
    {
    }

    public function clearByTag($tag)
    {
    }
}
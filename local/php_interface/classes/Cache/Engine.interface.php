<?php
/**
 * Created by PhpStorm.
 * User: Администратор
 * Date: 10.12.13
 * Time: 12:00
 */

namespace Cpeople\Classes\Cache;

interface Engine
{
    public function valid($cacheId, $ttl);
    public function get($cacheId);
    public function clear();
    public function clearByTag($tag);
    public function save($cacheId, $data);
}
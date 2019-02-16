<?php
/**
 * Created by PhpStorm.
 * User: Администратор
 * Date: 10.12.13
 * Time: 12:02
 */

namespace Cache;

class EngineFiles implements Engine
{
    private $path;
    private $defaultTTL = 3600;

    public function __construct($path, $defaultTTL = 3600)
    {
        $this->path = $path;
        $this->defaultTTL = (int) $defaultTTL;

        if (!file_exists($this->path))
        {
            @mkdir($this->path, 0777, true);
        }
    }

    private function getFileName($cacheId)
    {
        $md5 = md5($cacheId);
        return $this->path . DIRECTORY_SEPARATOR . substr($md5, 0, 2) . DIRECTORY_SEPARATOR . $md5 . '.txt';
    }

    public function valid($cacheId, $ttl = null)
    {
        $retval = true;

        if (empty($ttl))
        {
            $ttl = $this->defaultTTL;
        }

        try
        {
            $file = $this->getFileName($cacheId);

            if (!file_exists($file))
            {
                throw new CacheException('file does not exist');
            }

            if (time() - filemtime($file) > $ttl)
            {
                throw new CacheException('too old');
            }
        }
        catch (CacheException $e)
        {
            $retval = false;
        }

        return $retval;
    }

    public function save($cacheId, $data)
    {
        $file = $this->getFileName($cacheId);

        $dir = dirname($file);

        if (!file_exists($dir))
        {
            mkdir($dir, 0777, true);
        }

        if (gettype($data) != 'string')
        {
            $data = serialize($data);
        }

        file_put_contents($file, $data);
        chmod($file, 0666);
    }

    public function get($cacheId)
    {
        $retval = file_get_contents($this->getFileName($cacheId));

        if (substr($retval, 0, 2) == 'a:')
        {
            $retval = unserialize($retval);
        }

        return $retval;
    }

    public function clear()
    {
        $objects = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->path),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach($objects as $name => $object)
        {
            if ($object->isFile())
            {
                unlink($object->getRealPath());
            }
        }
    }

    public function clearByTag($tag)
    {

    }
}
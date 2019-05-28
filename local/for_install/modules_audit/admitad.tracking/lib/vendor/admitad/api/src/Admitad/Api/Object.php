<?php

namespace Admitad\Api;

class Object extends \ArrayObject
{
    public function __construct($data = array())
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $value = new Object($value);
                }
                $this[$key] = $value;
            }
        }
    }
    public function __get($key)
    {
        return $this[$key];
    }

    public function offsetGet($key)
    {
        return $this->offsetExists($key) ? parent::offsetGet($key) : null;
    }
}

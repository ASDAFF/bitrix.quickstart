<?php
/**
 * Created by PhpStorm.
 * User: Администратор
 * Date: 17.03.14
 * Time: 9:39
 */

namespace Cpeople\Classes\Base;

class Collection implements \Countable, \ArrayAccess, \SeekableIterator
{
    protected $list;
    protected $length = 0;

    public function __construct($list = array())
    {
        $this->setList($list);
    }

    private function updateCount()
    {
        $this->length = count($this->list);
    }

    public function setList($list)
    {
        $this->list = (array) $list;
        $this->updateCount();
    }

    public function push()
    {
        foreach (func_get_args() as $item)
        {
            $this->list[] = $item;
        }

        $this->updateCount();
    }

    public function merge()
    {
        foreach (func_get_args() as $array)
        {
            $this->list[] = array_merge($this->list, $array);
        }

        $this->updateCount();
    }

    public function pop()
    {
        \array_pop($this->list);
        $this->updateCount();
    }

    public function unshift($item)
    {
        \array_unshift($this->list);
        $this->updateCount();
    }

    public function shift($item)
    {
        \array_shift($this->list);
        $this->updateCount();
    }

    public function getBy($key, $value)
    {
        $retval = array();

        foreach ($this as $t)
        {
            if ($t->__get($key) == $value)
            {
                $retval[] = $t;
            }
        }

        return empty($retval) ? false : $retval;
    }

    public function getNotBy($key, $value)
    {
        $retval = array();

        foreach ($this as $t)
        {
            if ($t->__get($key) != $value)
            {
                $retval[] = $t;
            }
        }

        return empty($retval) ? false : $retval;
    }

    public function getById($id)
    {
        return $this->getFirstBy('id', $id);
    }

    public function getByCode($code)
    {
        return $this->getFirstBy('code', $code);
    }

    public function getCollectionBy($key, $value)
    {
        return new self($this->getBy($key, $value));
    }

    public function getFirstBy($key, $value)
    {
        $retval = @reset($this->getBy($key, $value));
        return empty($retval) ? false : $retval;
    }

    public function getLastBy($key, $value)
    {
        $retval = end($this->getBy($key, $value));
        return empty($retval) ? false : $retval;
    }

    public function getByOffset($offset)
    {
        return empty($this->list[$offset]) ? false : $this->list[$offset];
    }

    public function getByValueInList($key, $values)
    {
        $retval = array();

        for ($i = 0; $i < $this->length; $i++)
        {
            if (in_array($this->list[$i]->__get($key), $values))
            {
                $retval[] = $this->list[$i];
            }
        }

        return empty($retval) ? false : $retval;
    }

    public function getColumn($key)
    {
        $retval = array();

        foreach ($this->list as $item)
        {
            $retval[] = $item->{$key};
        }

        return $retval;
    }

    public function eq($offset)
    {
        return $this->getByOffset($offset);
    }

    /**
     * TODO
     */
    public function sort()
    {

    }

    public function removeBy($key, $value)
    {
        $this->updateCount();
    }

    /**
     *  interfaces function implementation
     */
    public function offsetSet($offset, $value)
    {
        $this->container[$offset] = $value;
    }

    public function offsetExists($offset)
    {
        return isset($this->list[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->list[$offset]);
        $this->updateCount();
    }

    public function offsetGet($offset)
    {
        return isset($this->list[$offset]) ? $this->list[$offset] : null;
    }

    public function seek($position)
    {
        $this->position = $position;

        if (!$this->valid())
        {
            throw new \OutOfBoundsException("invalid seek position ($position)");
        }
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function current()
    {
        return $this->list[$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    public function valid()
    {
        return isset($this->list[$this->position]);
    }

    public function count()
    {
        return $this->length;
    }

    public function getNext($element, $cycle = true)
    {
        $key = array_search($element, $this->list);

        if ($key === false) return false;

        $key++;

        if ($key >= $this->length)
        {
            $key = 0;
        }

        return $this->list[$key];
    }

    public function getPrev($element, $cycle = true)
    {
        $key = array_search($element, $this->list);

        if ($key === false) return false;

        $key--;

        if ($key < 0)
        {
            $key = $this->length - 1;
        }

        return $this->list[$key];
    }
}


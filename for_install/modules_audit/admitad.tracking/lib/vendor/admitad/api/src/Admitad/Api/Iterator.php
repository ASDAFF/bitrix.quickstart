<?php

namespace Admitad\Api;

class Iterator implements \Iterator, \Countable
{
    /**
     * @var Api
     */
    protected $api;

    protected $offset;

    protected $initialized = false;
    protected $method;
    protected $params;
    protected $meta;
    protected $results;
    protected $finished = false;

    public function __construct(Api $api, $method, array $params = array(), $limit = 200)
    {
        $this->api = $api;
        $this->method = $method;
        $this->params = $params;
        $this->limit = $limit;
    }

    public function current()
    {
        if (!$this->initialized) {
            throw new \LogicException('Rewind first');
        }

        return current($this->results);
    }


    public function next()
    {
        if (!$this->initialized) {
            throw new \LogicException('Rewind first');
        }

        if ($this->finished) {
            return;
        }

        $this->offset++;

        if ($this->meta['count'] <= $this->offset) {
            $this->finished = true;
            return;
        }

        if (!next($this->results)) {
            $this->load();
        }
    }

    protected function load()
    {
        $result = $this->api->get($this->method, array_merge($this->params, array(
            'limit' => $this->limit,
            'offset' => $this->offset
        )))->getResult();

        $this->meta = $result['_meta'] ?: array(
            'limit' => $this->limit,
            'offset' => $this->offset,
            'count' => 0
        );

        $this->results =  $result['results']->getArrayCopy() ?: array();

        if ($this->meta['limit'] < $this->limit) {
            $this->limit = $this->meta['limit'];
        }

        if (empty($this->results)) {
            $this->finished = true;
        }
    }


    public function key()
    {
        if (!$this->initialized) {
            throw new \LogicException('Rewind first');
        }

        if ($this->finished) {
            return null;
        }

        return $this->offset;
    }


    public function valid()
    {
        if (!$this->initialized) {
            throw new \LogicException('Rewind first');
        }

        return !$this->finished;
    }


    public function rewind()
    {

        if ($this->initialized && 0 === $this->offset) {
            return;
        }
        $this->offset = 0;
        $this->initialized = true;
        $this->finished = false;
        $this->load();
    }

    public function count()
    {
        if (!$this->initialized) {
            throw new \LogicException('Rewind first');
        }

        return $this->meta['count'];
    }
}

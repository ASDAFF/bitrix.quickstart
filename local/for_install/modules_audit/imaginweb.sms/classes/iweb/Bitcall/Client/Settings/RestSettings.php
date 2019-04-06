<?php
/**
 * Created by http://bitcall.ru
 * User: Kurdikov P.S.
 * Date: 17.05.2014
 */


namespace Bitcall\Client\Settings;


class RestSettings {
    private $aliases;
    private $address;

    function __construct($address, $aliases)
    {
        $this->address = $address;
        $this->aliases = $aliases;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return mixed
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    public function getText()
    {
        return $this->getUrl('text');
    }

    public function getFastText()
    {
        return $this->getUrl('fastText');
    }

    public function getIvr()
    {
        return $this->getUrl('ivr');
    }

    public function getFastIvr()
    {
        return $this->getUrl('fastIvr');
    }

    public function getTextTask()
    {
        return $this->getUrl('textTask');
    }

    public function getIvrTask()
    {
        return $this->getUrl('ivrTask');
    }

    public function getStatus()
    {
        return $this->getUrl('status');
    }

    private function getUrl($name)
    {
        return $this->address . '/' . $this->aliases[$name];
    }
}
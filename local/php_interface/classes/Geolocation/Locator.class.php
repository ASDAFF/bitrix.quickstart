<?php
/**
 * User: graymur
 * Date: 06.11.13
 * Time: 16:06
 */

namespace Cpeople\Classes\Geolocation;

abstract class Locator implements LocatorInterface
{
    protected $ip;

    public function __construct()
    {
        $this->setIP($this->detectIP());
    }

    private function detectIP()
    {
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_REAL_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR') as $key)
        {
            if (!empty($_SERVER[$key]))
            {
                return $_SERVER[$key];
            }
        }
    }

    public function setIP($ip)
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP))
        {
            throw new GeoLocException('Invalid IP');
        }

        $this->ip = $ip;
    }
}

interface LocatorInterface
{
    /**
     * @return \Cpeople\Classes\Geolocation\Result $result
     */
    function locate();
}

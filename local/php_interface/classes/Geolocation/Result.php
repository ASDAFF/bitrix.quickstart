<?php
/**
 * User: graymur
 * Date: 06.11.13
 * Time: 16:11
 */

namespace Geolocation;

class Result
{
    private $_city;
    private $_country;
    private $_region;
    private $_destrict;
    private $_latitude;
    private $_longtitude;

    private $error;

    public function __call($funcName, $args)
    {
        if (preg_match('#^set([a-z]+)$#i', $funcName, $m))
        {
            $varname = '_' . strtolower($m[1]);

            if (!property_exists(__CLASS__, $varname))
            {
                throw new GeoLocException("Class " . __CLASS__ . " does not have property {$varname}");
            }

            $this->{$varname} = $args[0];
        }
        else if (preg_match('#^get([a-z]+)$#i', $funcName, $m))
        {
            $varname = '_' . strtolower($m[1]);

            if (!property_exists(__CLASS__, $varname))
            {
                throw new GeoLocException("Class " . __CLASS__ . " does not have property {$varname}");
            }

            return $this->{$varname};
        }
    }

    public function setError($error)
    {
        $this->error = $error;
    }

    public function isError()
    {
        return (bool) $this->error;
    }

    public function getError()
    {
        return $this->error;
    }
}
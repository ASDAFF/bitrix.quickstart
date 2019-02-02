<?php
/**
 * Created by PhpStorm.
 * User: graymur
 * Date: 27.11.13
 * Time: 14:45
 */

namespace Cpeople\Traits;

trait MapConnection
{
    private $mcLatitude;
    private $mcLongtitude;

    private function mcFetch()
    {
        if (!isset($this->mcLatitude))
        {
            list($this->mcLatitude, $this->mcLongtitude) = preg_split('#\s*,\s*#', $this->getPropValue('COORDINATES'));
        }
    }

    public function getLatitude()
    {
        $this->mcFetch();
        return $this->mcLatitude;
    }

    public function getLongtitude()
    {
        $this->mcFetch();
        return $this->mcLongtitude;
    }

    public function getCoordinates()
    {
        return $this->getPropValue('COORDINATES');
    }
}
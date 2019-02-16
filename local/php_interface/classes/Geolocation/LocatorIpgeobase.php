<?php
/**
 * User: graymur
 * Date: 06.11.13
 * Time: 16:14
 */

namespace Geolocation;

class LocatorIpgeobase extends Locator
{
    /**
     * @return \Geolocation\Result $result
     */
    public function locate()
    {
        $url = "http://ipgeobase.ru:7020/geo/?ip=$this->ip";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, 'Content-Type:application/x-www-form-urlencoded');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $curlResult = curl_exec($ch);

        $result = new Result();

        try
        {
            if (curl_error($ch))
            {
                throw new \Exception('Error in request, ' . __METHOD__);
            }

            if (!$xml = simplexml_load_string($curlResult))
            {
                throw new \Exception('Bad response, ' . __METHOD__);
            }

            if ((string) $xml->ip->city == '')
            {
                throw new \Exception('Bad XML, ' . __METHOD__);
            }

            $result->setCity((string) $xml->ip->city);
            $result->setCountry((string) $xml->ip->country);
            $result->setRegion((string) $xml->ip->region);
            $result->setDestrict((string) $xml->ip->district);
            $result->setLatitude((string) $xml->ip->lat);
            $result->setLongtitude((string) $xml->ip->lng);
        }
        catch (\Exception $e)
        {
            $result->setError($e->getMessage());
        }

        return $result;
    }
}
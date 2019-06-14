<?php
/**
 * Created by http://bitcall.ru
 * User: Kurdikov P.S.
 * Date: 17.05.2014
 */


namespace Bitcall\Client\Settings;


class SoapSettings {
    private $wsdlUrl;
    private $wsdlCache;

    function __construct($wsdlUrl, $wsdlCache)
    {
        $this->wsdlCache = $wsdlCache;
        $this->wsdlUrl = $wsdlUrl;
    }

    /**
     * @return mixed
     */
    public function useWsdlCache()
    {
        return $this->wsdlCache;
    }

    /**
     * @return mixed
     */
    public function getWsdlUrl()
    {
        return $this->wsdlUrl;
    }
}
<?php
/**
 * Created by http://bitcall.ru
 * User: Kurdikov P.S.
 * Date: 17.05.2014
 */


namespace Bitcall\Client\Core;


use Bitcall\Client\Core\Common\ParamsFactory;
use Bitcall\Client\Core\Rest\RestBitcallClient;
use Bitcall\Client\Core\Rest\RestResponseFactory;
use Bitcall\Client\Core\Soap\SoapBitcallClient;
use Bitcall\Client\Core\Soap\SoapResponseFactory;
use Bitcall\Client\Settings\Settings;
use Exception;

class BitcallClientFactory implements IBitcallClientFactory {

    /**
     * @param string $key
     * @throws Exception
     * @return IBitcallClient
     */
    public function getClient($key)
    {
        if(!extension_loaded('openssl')){
            throw new Exception('Php extension openssl not loaded. Check your php.ini and try again');
        }
        $isCurlLoaded = extension_loaded('curl');
        $isSoapLoaded = extension_loaded('soap');
        if(!$isCurlLoaded && !$isSoapLoaded){
            throw new Exception('Php extensions curl or soap not loaded. Check your php.ini and try again');
        }

        if($isSoapLoaded){
            return new SoapBitcallClient($key,
                Settings::getInstance(),
                ParamsFactory::getInstance(),
                SoapResponseFactory::getInstance());
        } else {
            return new RestBitcallClient($key, Settings::getInstance(), ParamsFactory::getInstance(), RestResponseFactory::getInstance());
        }
    }
}
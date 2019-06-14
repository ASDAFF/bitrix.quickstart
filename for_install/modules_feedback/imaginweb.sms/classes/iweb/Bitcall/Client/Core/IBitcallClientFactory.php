<?php
/**
 * Created by http://bitcall.ru
 * User: Kurdikov P.S.
 * Date: 17.05.14
  */

namespace Bitcall\Client\Core;


/**
 * Interface IBitcallClientFactory
 * @package Bitcall\Client\Core
 */
interface IBitcallClientFactory {
    /**
     * @param string $key
     * @return IBitcallClient
     */
    public function getClient($key);
} 
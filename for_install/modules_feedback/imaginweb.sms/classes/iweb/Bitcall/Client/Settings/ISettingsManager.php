<?php
/**
 * Created by http://bitcall.ru
 * User: Kurdikov P.S.
 * Date: 17.05.14
  */

namespace Bitcall\Client\Settings;


interface ISettingsManager {

    /**
     * @return SoapSettings
     */
    public function getSoapSettings();

    /**
     * @return RestSettings
     */
    public function getRestSettings();
} 
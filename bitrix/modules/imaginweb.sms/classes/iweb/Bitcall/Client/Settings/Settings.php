<?php
/**
 * Created by http://bitcall.ru
 * User: Kurdikov P.S.
 * Date: 17.05.2014
 */


namespace Bitcall\Client\Settings;


class Settings implements ISettingsManager
{
    private static $instance;
    private $iniArray;
    private function __construct()
    {
        $this->iniArray = parse_ini_file('bitcall.ini', true);
    }
    private function __clone()
    {
    }

    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getSoapSettings()
    {
        return new SoapSettings($this->iniArray['soap']['url'], $this->iniArray['soap']['enableCache']);
    }

    public function getRestSettings()
    {
        return new RestSettings($this->iniArray['rest']['url'], array(
            'text' => $this->getRestOneSettings('textAlias'),
            'ivr' => $this->getRestOneSettings('ivrAlias'),
            'fastText' => $this->getRestOneSettings('fastTextAlias'),
            'fastIvr' => $this->getRestOneSettings('fastIvrAlias'),
            'textTask' => $this->getRestOneSettings('textTaskAlias'),
            'ivrTask' => $this->getRestOneSettings('ivrTaskAlias'),
            'status' => $this->getRestOneSettings('statusAlias'),
        ));
    }

    private function getRestOneSettings($name){
        return $this->iniArray['rest'][$name];
    }
}
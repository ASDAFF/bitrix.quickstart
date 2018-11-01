<?php
class Singleton
{
    private static $object;
    private $arParams;

    private function __construct(){}

    public static function getInstance()
    {
        if( empty(self::$object) )
        {
            return  self::$object = new Singleton();
        }

        return self::$object;
    }

    public function setParams( $key, $val )
    {
        return $this->arParams = array( $key=>$val );
    }

    public function getParams()
    {
        return $this->arParams;
    }
}

$test = Singleton::getInstance();

$set = $test->setParams('name', 'NameTest');
unset($set);


$test2 = Singleton::getInstance();

$getParms = $test2->getParams();

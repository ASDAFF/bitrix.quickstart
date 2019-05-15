<?php
/**
 * Created by http://bitcall.ru
 * User: Kurdikov P.S.
 * Date: 16.05.2014
 */


namespace Bitcall\Client\Models\Common;


abstract class Enum {
    private static $constCache = NULL;

    private static function getConstants() {
        if (self::$constCache === NULL) {
            $reflect = new ReflectionClass(get_called_class());
            self::$constCache = $reflect->getConstants();
        }

        return self::$constCache;
    }

    public static function isValidName($name, $strict = false) {
        $constants = self::getConstants();

        if ($strict) {
            return array_key_exists($name, $constants);
        }

        $keys = array_map('strtolower', array_keys($constants));
        return in_array(strtolower($name), $keys);
    }

    public static function isValidValue($value) {
        $values = array_values(self::getConstants());
        return in_array($value, $values, $strict = true);
    }
} 
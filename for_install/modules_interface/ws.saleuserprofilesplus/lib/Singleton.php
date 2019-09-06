<?php
/**
 * @author Smotrov Dmitriy <dsxack@gmail.com>
 */

namespace WS\SaleUserProfilesPlus;


class Singleton extends Object{
    private static $_instances = array();

    /**
     * @return $this
     */
    static public function get() {
        $className = get_called_class();

        if (!isset(self::$_instances[$className])) {
            self::$_instances[$className] = new $className;
        }

        return self::$_instances[$className];
    }
} 
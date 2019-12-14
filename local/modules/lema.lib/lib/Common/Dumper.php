<?php

namespace Lema\Common;

/**
 * Dumper class file.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright 2008-2013 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */
abstract class Dumper
{
    private static $_objects;
    private static $_output;
    private static $_depth;

    /**
     * Displays a variable.
     * This method achieves the similar functionality as var_dump and print_r
     * but is more robust when handling complex objects such as Yii controllers.
     *
     * @param mixed $var variable to be dumped
     * @param integer $depth maximum depth that the dumper should go into the variable. Defaults to 10.
     * @param boolean $highlight whether the result should be syntax-highlighted
     *
     * @access public
     */
    public static function dump($var, $depth = 10, $highlight = true)
    {
        echo static::dumpAsString($var, $depth, $highlight);
    }

    /**
     * Dumps a variable in terms of a string.
     * This method achieves the similar functionality as var_dump and print_r
     * but is more robust when handling complex objects such as Yii controllers.
     *
     * @param mixed $var variable to be dumped
     * @param integer $depth maximum depth that the dumper should go into the variable. Defaults to 10.
     * @param boolean $highlight whether the result should be syntax-highlighted
     *
     * @return string the string representation of the variable
     *
     * @access public
     */
    public static function dumpAsString($var, $depth = 10, $highlight = true)
    {
        static::$_output = '';
        static::$_objects = array();
        static::$_depth = $depth;

        static::dumpInternal($var, 0);
        
        if($highlight)
        {
            $result = highlight_string("<?php\n".static::$_output,true);
            static::$_output = preg_replace('/&lt;\\?php<br \\/>/', '', $result, 1);
        }

        return static::$_output;
    }

    /*
     * @param mixed $var variable to be dumped
     * @param integer $level depth level
     *
     * @access private
     */
    private static function dumpInternal($var, $level)
    {
        switch(gettype($var))
        {
            case 'boolean':
                static::$_output .= $var ? 'true' : 'false';
            break;
            case 'integer':
            case 'double':
                static::$_output .= "$var";
            break;
            case 'string':
                static::$_output.="'".addslashes($var)."'";
            break;
            case 'resource':
                static::$_output .= '{resource}';
            break;
            case 'NULL':
                static::$_output .= "null";
            break;
            case 'unknown type':
                static::$_output .= '{unknown}';
            break;
            case 'array':
                if(static::$_depth <= $level)
                    static::$_output .= 'array(...)';
                elseif(empty($var))
                    static::$_output .= 'array()';
                else
                {
                    $keys = array_keys($var);
                    $spaces = str_repeat(' ', $level * 4);
                    static::$_output .= "array\n" . $spaces . '(';
                    foreach($keys as $key)
                    {
                        static::$_output .= "\n" . $spaces . '    ';
                        static::dumpInternal($key, 0);
                        static::$_output .= ' => ';
                        static::dumpInternal($var[$key], $level + 1);
                    }
                    static::$_output .= "\n" . $spaces . ')';
                }
            break;
            case 'object':
                if(($id = array_search($var, static::$_objects, true)) !== false)
                    static::$_output .= get_class($var).'#' . ($id + 1) . '(...)';
                elseif(static::$_depth <= $level)
                    static::$_output .= get_class($var) . '(...)';
                else
                {
                    $id = array_push(static::$_objects, $var);
                    $className = get_class($var);
                    $members = (array) $var;
                    $spaces = str_repeat(' ', $level * 4);
                    static::$_output .= "$className#$id\n" . $spaces . '(';
                    foreach($members as $key => $value)
                    {
                        $keyDisplay = strtr(trim($key), array("\0" => ':'));
                        static::$_output .= "\n" . $spaces . "    [$keyDisplay] => ";
                        static::$_output .= static::dumpInternal($value, $level + 1);
                    }
                    static::$_output .= "\n" . $spaces . ')';
                }
                break;
        }
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: darkfriend <hi@darkfriend.ru>
 * Date: 04.05.2017
 * Time: 19:07
 */

namespace Dev2fun\ImageCompress;

use Bitrix\Main\Config\Option;

class Check
{
    public static function isPNGOptim() {
        $path = Option::get('dev2fun.imagecompress','path_to_optipng', '/usr/bin');
        exec($path.'/optipng -v',$s);
        return ($s?true:false);
    }

    public static function isJPEGOptim() {
        $path = Option::get('dev2fun.imagecompress','path_to_jpegoptim', '/usr/bin');
        exec($path.'/jpegoptim --version',$s);
        return ($s?true:false);
    }

    public static function isRead($path) {
        return is_readable($path);
    }

    public static function isWrite($path) {
        return is_writable($path);
    }
}
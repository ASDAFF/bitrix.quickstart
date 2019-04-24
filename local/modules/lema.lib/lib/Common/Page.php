<?php

namespace Lema\Common;

/**
 * Class Page
 * @package Lema\Common
 */
class Page
{
    /**
     * Check if $data in dir
     *
     * @param $data
     *
     * @return bool
     *
     * @access public
     */
    public static function inDir($data)
    {
        if(is_array($data))
            return (bool) preg_match('~^(?:' . join('|', array_map(function ($v) { return preg_quote($v); }, $data)) . ')', Request::get()->getRequestedPageDirectory());
        return 0 === strpos(Request::get()->getRequestedPageDirectory(), $data);
    }

    /**
     * Check if $section in root dir
     *
     * @param $section
     *
     * @return bool
     *
     * @access public
     */
    public static function inRootDir($section)
    {
        $section = is_array($section) ? join('|', array_map('preg_quote', $section)) : preg_quote($section);
        return (bool) preg_match('~^/(?:' . $section . ')~ui', Request::get()->getRequestedPageDirectory());
    }
}
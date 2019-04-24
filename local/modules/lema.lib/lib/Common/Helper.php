<?php

namespace Lema\Common;

/**
 * Class Helper
 * @package Lema\Common
 */
abstract class Helper
{
    /**
     * Return word with needed ending for given number
     *
     * @param $n
     * @param array $items
     *
     * @return bool|string
     *
     * @access public
     */
    public static function pluralize($n, array $items)
    {
        if(!isset($items[0], $items[1], $items[2]))
            return false;
        if($n % 10 === 1 && $n % 100 !== 11)
            return $items[0];
        if($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 > 20))
            return $items[1];
        return $items[2];
    }
    /**
     * Return word with needed ending for given number, with number & split symbol
     *
     * @param $n
     * @param array $items
     * @param string $splitSym
     *
     * @return bool|mixed
     *
     * @access public
     */
    public static function pluralizeN($n, array $items, $splitSym = ' ')
    {
        return $n . $splitSym . static::pluralize($n, $items);
    }

    /**
     * Wrapper of htmlspecialchars
     *
     * @param $value
     *
     * @return string
     *
     * @access public
     */
    public static function enc($value, $flags = ENT_COMPAT)
    {
        static $htmlFilterExists = null;
        if(!isset($htmlFilterExists))
            $htmlFilterExists = class_exists('\\Bitrix\\Main\\Text\\HtmlFilter');
        return $htmlFilterExists ? \Bitrix\Main\Text\HtmlFilter::encode($value, $flags) : htmlspecialcharsbx($value, $flags);
    }

    /**
     * Return json_encoded data
     *
     * @param $data
     *
     * @return bool|string
     *
     * @access public
     */
    public static function getJson($data, $options = 0, $depth = 512)
    {
        return json_encode($data, $options, $depth);
    }

    /**
     * Return encoded string or false
     *
     * @param $value
     *
     * @return bool|string
     *
     * @access public
     */
    public static function utf8ToCP1251($value)
    {
        return iconv('UTF-8', 'Windows-1251', $value);
    }

    /**
     * Return full url with https? & server name for specified path
     *
     * @param $url
     *
     * @return string
     *
     * @access public
     */
    public static function getFullUrl($url)
    {
        return 'http' . (Request::get()->isHttps() ? 's' : '') . '://' . Server::get()->getServerName() . (0 === strpos($url, '/') ? $url : '/' . $url);
    }

    /**
     * Return image with specified watermark
     *
     * @param array $element
     * @param array $watermarkParams
     * @param int   $resizeType
     *
     * @return array|bool
     *
     * @access public
     */
    public static function getImageWatermark(array $element = array(), array $watermarkParams = array(), $resizeType = BX_RESIZE_IMAGE_PROPORTIONAL)
    {
        if(!isset($element['ID'], $element['WIDTH'], $element['HEIGHT'], $watermarkParams['file']))
            return false;

        $defParams = array(
            'name' => 'watermark',
            'position' => 'center',
            'size' => 'real',
        );
        foreach($watermarkParams as $k => $v)
            $defParams[$k] = $v;

        $arWaterMark = array($defParams);

        return \CFile::ResizeImageGet(
            $element['ID'],
            array('width' => $element['WIDTH'], 'height' => $element['HEIGHT']),
            $resizeType,
            true,
            $arWaterMark
        );
    }

    /**
     * Return image path with specified watermark
     *
     * @param array $element
     * @param array $watermarkParams
     * @param int   $resizeType
     *
     * @return bool|string
     *
     * @access public
     */
    public static function getImageWatermarkSrc(array $element = array(), array $watermarkParams = array(), $resizeType = BX_RESIZE_IMAGE_PROPORTIONAL)
    {
        $data = static::getImageWatermark($element, $watermarkParams, $resizeType);
        return isset($data['src']) ? $data['src'] : false;
    }

    /**
     * Check prop empty
     *
     * @param $code
     * @param array $data
     * @param string $propArrKey
     *
     * @return bool
     *
     * @access public
     */
    public static function propEmpty($code, array $data = array(), $propArrKey = 'PROPERTIES')
    {
        return empty($data[$propArrKey][$code]['VALUE']);
    }

    /**
     * Check prop filled
     *
     * @param $code
     * @param array $data
     * @param string $propArrKey
     *
     * @return bool
     *
     * @access public
     */
    public static function propFilled($code, array $data = array(), $propArrKey = 'PROPERTIES')
    {
        return !static::propEmpty($code, $data, $propArrKey);
    }

    /**
     * Returns array of prop data (or empty array)
     *
     * @param $code
     * @param array $data
     * @param string $propArrKey
     *
     * @return array
     *
     * @access public
     */
    public static function prop($code, array $data = array(), $propArrKey = 'PROPERTIES')
    {
        return isset($data[$propArrKey][$code]) ? $data[$propArrKey][$code] : array();
    }

    /**
     * Returns value of given prop (or null)
     *
     * @param $code
     * @param array $data
     * @param string $propArrKey
     *
     * @return null|string
     *
     * @access public
     */
    public static function propValue($code, array $data = array(), $propArrKey = 'PROPERTIES')
    {
        return isset($data[$propArrKey][$code]['VALUE']) ? $data[$propArrKey][$code]['VALUE'] : null;
    }

    /**
     * Returns encoded value of given prop (or null)
     *
     * @param $code
     * @param array $data
     * @param string $propArrKey
     * @param int $encodeFlags
     *
     * @return null|string
     *
     * @access public
     */
    public static function escPropValue($code, array $data = array(), $propArrKey = 'PROPERTIES', $encodeFlags = ENT_COMPAT)
    {
        $value = static::propValue($code, $data, $propArrKey);
        return empty($value) ? null : static::enc($value, $encodeFlags);
    }
}
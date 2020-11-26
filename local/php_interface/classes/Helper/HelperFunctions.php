<?php
/**
 * Created by PhpStorm.
 * User: ASDAFF
 * Date: 02.02.2019
 * Time: 19:39
 */

namespace Helper;

/**
 * Class HelperFunctions
 * @package Helper
 */
class HelperFunctions
{

    /**
     * @return bool
     */
    function _empty()
    {
        foreach (func_get_args() as $arg) {
            if (empty($arg)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $time
     * @param string $format
     *
     * Формат времени
     * Использование HelperFunctions::dt($time)
     */

    function dt($time, $format = 'd.m.Y H:i:s')
    {
        foreach ((array)$time as $t) {
            self::dv(date($format, $t));
        }
    }

    /**
     * @param $size
     * @param int $precision
     * @return string
     *
     * Задаем формат показа размера файла
     */

    function format_file_size($size, $precision = 0)
    {
        if ($precision == 0) {
            $precision = 2;
        }
        if ($size >= 1262485504) {
            return number_format($size / 1262485504, $precision, '.', ' ') . ' Gb';
        } elseif ($size >= 1048576) {
            return number_format($size / 1048576, $precision, '.', ' ') . ' Mb';
        } elseif ($size >= 1024) {
            return number_format($size / 1024, $precision, '.', ' ') . ' kb';
        } else {
            return number_format($size, 0, '.', ' ') . ' b';
        }
    }

    /**
     * @param $_bytes
     * @param int $_dec
     * @return string
     */
    public static function FormatFilesize($_bytes, $_dec = 2){
        $_dec = intval($_dec);
        $size = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $factor = floor((strlen($_bytes) - 1) / 3);

        return sprintf("%.{$_dec}f", $_bytes / pow(1024, $factor)) ." ". @$size[$factor];
    }

    /**
     * @param int time Unix timestamp
     * @param string mode day|hour|month|year
     * @param boolean sqlReady
     */
    function time_borders($time, $mode = 'day', $sqlReady = false)
    {
        $_time = getdate($time);
        $out = array();

        switch ($mode) {
            case 'minute':
                $out[] = mktime($_time['hours'], $_time['minutes'], 0, $_time['mon'], $_time['mday'], $_time['year']);
                $out[] = $out[0] + 59;
                break;

            case 'hour':
                $out[] = mktime($_time['hours'], 0, 0, $_time['mon'], $_time['mday'], $_time['year']);
                $out[] = $out[0] + 3599;
                break;

            case 'day':
                $out[] = mktime(0, 0, 0, $_time['mon'], $_time['mday'], $_time['year']);
                $out[] = $out[0] + 86399;
                break;

            case 'week':
                $mday = date('j', $time) - date('w', $time) + 1;
                // +1 is here to make Monday the first day of week, like in all normal world :)
                $out[] = mktime(0, 0, 0, $_time['mon'], $mday, $_time['year']);
                $out[] = $out[0] + 86400 * 7 - 1;
                break;

            case 'month':
                $out[] = mktime(0, 0, 0, $_time['mon'], 1, $_time['year']);
                $out[] = mktime(23, 59, 59, $_time['mon'], date('t', $time), $_time['year']);
                break;

            case 'year':
                $out[] = mktime(0, 0, 0, 1, 1, $_time['year']);
                $out[] = mktime(23, 59, 59, 12, 31, $_time['year']);
                break;
        }

        if ($sqlReady) {
            $out[0] = date('Y-m-d H:i:s', $out[0]);
            $out[1] = date('Y-m-d H:i:s', $out[1]);
        }

        return $out;
    }

    /**
     * @param $command
     * @return string
     */
    function syscall($command)
    {
        if ($proc = popen("($command)2>&1", 'r')) {
            while (!feof($proc)) {
                @$result .= fgets($proc, 1000);
            }

            pclose($proc);
            return $result;
        }
    }

    /**
     * @param $string
     * @param $from_encoding
     * @param $to_encoding
     * @return null|string|string[]
     */
    function enconvert($string, $from_encoding, $to_encoding)
    {
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($string, $to_encoding, $from_encoding);
        } else if (function_exists('iconv')) {
            return iconv($from_encoding, $to_encoding, $string);
        } else {
            return $string;
        }
    }

    /**
     * @param $filename
     * @param $from_encoding
     * @param $to_encoding
     * @return bool|int
     */
    function enconvert_file($filename, $from_encoding, $to_encoding)
    {
        return file_put_contents($filename, enconvert(file_get_contents($filename), 'CP1251', 'UTF8'));
    }

    /**
     * @param $text
     * @param string $quote
     * @return mixed
     */
    function text4JS($text, $quote = "'")
    {
        $text = str_replace(array("\n", "\r"), '', $text);
        return str_replace($quote, "\\{$quote}", $text);
    }

    /**
     * @param $text
     * @return mixed
     */
    function text4flash($text)
    {
        $text = str_replace('strong>', 'b>', $text);
        $text = preg_replace('/<br\s*\/>/i', '<br>', $text);
        return text4JS((strip_tags(preg_replace('/<[a-z0-9]+\s+([^\>]*)(?!\/)>/isxU', '', $text), '<p><br><i><b><u><a>')), '"');
    }

    /**
     * @param $number
     * @param $length
     * @return string
     */
    function zerofill($number, $length)
    {
        return str_repeat('0', $length - strlen($number)) . (int)$number;
    }

    /**
     * @return mixed
     */
    function coalesce()
    {
        $args = func_get_args();
        for ($i = 0, $cnt = func_num_args(); $i < $cnt; $i++) {
            if (!empty($args[$i])) {
                return $args[$i];
            }
        }
        return end($args);
    }

    /**
     * @param $string
     * @param bool $allow_slash
     * @return string
     */
    function url_name($string, $allow_slash = false)
    {
        return trim(preg_replace('/[^a-z0-9\-_' . ($allow_slash ? '\/' : '') . ']/', '', $string), '/');
    }

    /**
     * @param $url
     * @return string
     */
    function url_trailing_sign($url)
    {
        return strstr($url, '?') ? '&' : '?';
    }

    /**
     * @param $url
     * @param $varnames
     * @return null|string|string[]
     */
    function url_rm_vars($url, $varnames)
    {
        if (!is_array($varnames)) {
            $varnames = preg_split('/\s*,\s*/isx', $varnames);
        }

        foreach ($varnames as $var) {
            if (empty($var)) continue;
            $url = preg_replace("/(\?|&)$var=([^\?\&$]*)/isx", '', $url);
        }

        return $url;
    }


    /**
     * @param $email
     * @return false|int
     *
     * Проверка E-mail
     */
    function checkEmail($email)
    {
        return preg_match('|^[_a-z0-9:()-]+(\.[_a-z0-9:()-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*$|i', $email);
    }

    /**
     * @param $array
     * @return bool
     */
    function empty_array($array)
    {
        foreach ((array)$array as $k => $v) {
            if (is_array($v) && !empty_array($v)) return false;
            elseif (!empty($v)) return false;
        }

        return true;
    }

    /**
     * @param $time
     * @param string $month_format
     * @return array
     */
    function get_ymd($time, $month_format = 'm')
    {
        return array(date('Y', $time), date($month_format, $time), date('d', $time));
    }

    /**
     * @param int $year
     * @param int $month
     * @param int $day
     * @return bool
     */
    function time_borders_ymd($year = 0, $month = 0, $day = 0)
    {
        $time = false;

        if (!empty($day)) {
            $time = mktime(1, 1, 1, $month, $day, $year);
            $mode = 'day';
        } else if (!empty($month)) {
            $time = mktime(1, 1, 1, $month, 1, $year);
            $mode = 'month';
        } else if (!empty($year)) {
            $time = mktime(1, 1, 1, 1, 1, $year);
            $mode = 'year';
        }

        return $time ? time_borders($time, $mode) : false;
    }

    /**
     * @param bool $show
     */
    function count_time($show = false)
    {
        static $start;

        if (!$show) {
            $start = getmicrotime();
        } else {
            self::dv(getmicrotime() - $start);
            $start = 0;
        }
    }

    /**
     * @param $val
     * @param string $separator
     * @return false|int
     */
    function dmY2time($val, $separator = '.')
    {
        list($d, $m, $Y) = explode($separator, $val);
        return mktime(0, 0, 0, $m, $d, $Y);
    }

    /**
     * @param string $stamp
     * @return float
     */
    function get_microtime($stamp = '')
    {
        if (empty($stamp)) $stamp = microtime();
        list($usec, $sec) = explode(" ", $stamp);
        return ((float)$usec + (float)$sec);
    }

    /**
     * @param $object
     */
    function dm($object)
    {
        if (!is_object($object)) {
            self::dv($object);
            return;
        }

        self::dv(get_class($object) . ':');

        $methods = get_class_methods($object);
        sort($methods);
        self::dv($methods);
    }

    /**
     * @param $array
     * @param int $chunks
     * @param bool $preserveKeys
     * @return array
     */
    function array_split($array, $chunks = 2, $preserveKeys = false)
    {
        $newArray = array_fill(0, $chunks, array());

        $chunkNum = 0;

        foreach ($array as $k => $v) {
            $key = $preserveKeys ? $k : count($newArray[$chunkNum]);
            $newArray[$chunkNum][$key] = $v;

            $chunkNum++;

            if ($chunkNum > $chunks - 1) {
                $chunkNum = 0;
            }
        }

        return $newArray;
    }

    /**
     * @param $value
     * @param $_1
     * @param $_2
     * @param $_3
     * @param bool $return_value
     * @return string
     */
    function num_to_string($value, $_1, $_2, $_3, $return_value = true)
    {
        if (($value > 10 && $value < 20) || ($value > 110 && $value < 120)) {
            $v = $_3;
        } else {
            $arr = preg_split('//', (string)$value, null, PREG_SPLIT_NO_EMPTY);
            $last = end($arr);

            if ($last == 1) {
                $v = $_1;
            } else if ($last > 1 && $last < 5) {
                $v = $_2;
            } else {
                $v = $_3;
            }
        }

        return $return_value ? (int)$value . " $v" : $v;
    }

    /**
     * @return string
     */
    function russian_date()
    {
        $translation = array(
            "am" => "дп",
            "pm" => "пп",
            "AM" => "ДП",
            "PM" => "ПП",
            "Monday" => "Понедельник",
            "Mon" => "Пн",
            "Tuesday" => "Вторник",
            "Tue" => "Вт",
            "Wednesday" => "Среда",
            "Wed" => "Ср",
            "Thursday" => "Четверг",
            "Thu" => "Чт",
            "Friday" => "Пятница",
            "Fri" => "Пт",
            "Saturday" => "Суббота",
            "Sat" => "Сб",
            "Sunday" => "Воскресенье",
            "Sun" => "Вс",
            "January" => "января",
            "Jan" => "янв",
            "February" => "февраля",
            "Feb" => "фев",
            "March" => "марта",
            "Mar" => "мар",
            "April" => "апреля",
            "Apr" => "апр",
            "May" => "мая",
            "June" => "июня",
            "Jun" => "июн",
            "July" => "июля",
            "Jul" => "июл",
            "August" => "августа",
            "Aug" => "авг",
            "September" => "сентября",
            "Sep" => "сен",
            "October" => "октября",
            "Oct" => "окт",
            "November" => "ноября",
            "Nov" => "ноя",
            "December" => "декабря",
            "Dec" => "дек",
            "st" => "ое",
            "nd" => "ое",
            "rd" => "е",
            "th" => "ое",
        );

        if (func_num_args() > 1) {
            $timestamp = func_get_arg(1);
            return strtr(date(func_get_arg(0), $timestamp), $translation);
        } else {
            return strtr(date(func_get_arg(0)), $translation);
        }
    }

    /**
     * @return string
     */
    function lang_date_nominative()
    {
        switch (func_get_arg(2)) {
            case 'EN':
                $translation = array();
                break;

            case 'PL':
                $translation = array(
                    "January" => "Styczeń",
                    "February" => "Luty",
                    "March" => "Marzec",
                    "April" => "Kwiecień",
                    "May" => "Maj",
                    "June" => "Czerwiec",
                    "July" => "Lipiec",
                    "August" => "Sierpień",
                    "September" => "Wrzesień",
                    "October" => "Październik",
                    "November" => "Listopad",
                    "December" => "Grudzień"
                );
                break;

            default:
                $translation = array(
                    "January" => "Январь",
                    "February" => "Февраль",
                    "March" => "Март",
                    "April" => "Апрель",
                    "May" => "Май",
                    "June" => "Июнь",
                    "July" => "Июль",
                    "August" => "Август",
                    "September" => "Сентябрь",
                    "October" => "Октябрь",
                    "November" => "Ноябрь",
                    "December" => "Декабрь"
                );
        }

        if (func_num_args() > 1) {
            $timestamp = func_get_arg(1);
            return strtr(date(func_get_arg(0), $timestamp), $translation);
        } else {
            return strtr(date(func_get_arg(0)), $translation);
        }
    }

    /**
     * @param $text
     * @return null|string|string[]
     */
    function space2br($text)
    {
        return preg_replace('/\s+/', '<br/>', $text);
    }

    /**
     * @return bool
     */
    function is_ajax_request()
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || isset($_REQUEST['__ajax__']);
    }

    /**
     * @param $url
     * @param bool $add_http
     * @return string
     */
    function fix_url($url, $add_http = true)
    {
        $url = str_replace('http://', '', $url);
        $url = trim(preg_replace('|/+|', '/', $url), '/');

        return $add_http ? "http://$url/" : $url;
    }

    /**
     * @param $text
     * @param int $words
     * @param string $hellip
     * @return string
     */
    function truncate_by_words($text, $words = 40, $hellip = '&hellip;')
    {
        $words_array = preg_split('/\s+/u', strip_tags($text), -1, PREG_SPLIT_NO_EMPTY);

        $slice = array_slice($words_array, 0, $words);
        $retval = join(' ', $slice);

        if (count($words_array) > count($slice)) {
            $retval .= $hellip;
        }

        return $retval;
    }

    /**
     * @param $string
     * @return bool|string
     */
    function cp_thumb_url_hash($string)
    {
        $hash = md5(sha1(md5(mb_strtoupper(trim($string, '/')))));

        $hash = substr($hash, 4, 8);

        return $hash;
    }

    /**
     * @param $object
     * @return mixed
     */
    function with($object)
    {
        return $object;
    }

    function dv_rb($die = true)
    {
        global $APPLICATION;

        $APPLICATION->RestartBuffer();

        call_user_func_array('dv', func_get_args());

        if ($die) {
            die;
        }
    }

    /**
     * @param $key
     * @param bool $special
     * @return string
     */
    function cp_get_language_message($key, $special = false)
    {
        static $MESS;

        $langFilePath = BASE_PATH . '/bitrix/templates/.default/lang/' . LANGUAGE_ID . '/phrases.php';

        if (!isset($MESS)) {
            if (file_exists($langFilePath)) {
                require $langFilePath;
            }
        }

        $retval = $key;

        if (isset($MESS[$key])) {
            $retval = $special ? htmlspecialchars($MESS[$key]) : $MESS[$key];
        } else {
            if (!file_exists($langFilePath)) {
                @mkdir(dirname($langFilePath), 0777, true);
                @touch($langFilePath, 0666);
                file_put_contents($langFilePath, "<?\n\$MESS = array(\n);");
            }

            if (file_exists($langFilePath) && ($content = file_get_contents($langFilePath))) {
                $string = "'" . addslashes($key) . "'";
                $content = str_replace('?>', '', $content);
                $content = str_replace(');', "    $string => $string,\n);", $content);
                file_put_contents($langFilePath, $content);

                require $langFilePath;
            }

            $missingFile = BASE_PATH . '/temp/missing_messages.txt';

            $missing = @file($missingFile);

            $phrases = array();

            foreach ($missing as $v) {
                list($phrase, $url) = explode("\t", $v);
                $phrases[] = trim($phrase);
            }

            if (!in_array($key, $phrases)) {
                global $APPLICATION;

                $fp = fopen($missingFile, 'a+');
                fwrite($fp, "$key\t{$APPLICATION->GetCurUri()}\r\n");
                fclose($fp);
            }
        }

        return $retval;
    }

    /**
     * @param int $site_id
     * @return mixed
     */
    function cp_fetch_site_info($site_id = 0)
    {
        static $info;

        if (!isset($info)) {
            $rsSites = CSite::GetByID(coalesce($site_id, SITE_ID));
            $info = $rsSites->Fetch();
        }

        return $info;
    }

    /**
     * @return mixed
     */
    function cp_get_site_email()
    {
        $info = self::cp_fetch_site_info();
        return $info['EMAIL'];
    }

    /**
     * @return mixed
     */
    function cp_get_site_name()
    {
        $info = self::cp_fetch_site_info();
        return $info['SITE_NAME'];
    }

    /**
     * @param $url
     * @param array $options
     * @return string
     */
    function cp_get_thumb_url($url, $options = array())
    {
        if (!empty($options)) {
            $url_part = '/';

            foreach ($options as $k => $v) {
                $url_part .= substr($k, 0, 1) . "$v-";
            }

            $url_part = trim($url_part, '-');

            $url = $url_part . '/' . trim($url, '/');

            $url = '/' . basename(IMG_CACHE_PATH) . $url . '?' . self::cp_thumb_url_hash($url);
        }

        return $url;
    }

    /**
     * @param $format
     * @param $date
     * @return false|null|string
     */
    function cp_bitrix_date($format, $date)
    {
        list ($d, $m, $y) = explode('.', $date);
        $time = mktime(1, 1, 1, $m, $d, $y);

        $retval = null;

        switch (LANGUAGE_ID) {
            case 'en':
                $retval = date($format, $time);
                break;

            default:
                $retval = self::russian_date($format, $time);
                break;
        }

        return $retval;
    }

    /**
     * @param $key
     * @param bool $aReplace
     * @return mixed|string
     */
    function CPGetMessage($key, $aReplace = false)
    {
        $retval = GetMessage($key, $aReplace);

        return empty($retval) ? $key : $retval;
    }

    /**
     * @param string $varname
     * @return null|string|string[]
     */
    function cp_bitrix_sessid_post($varname = 'sessid')
    {
        return preg_replace('/id\=".*"/isxU', '', bitrix_sessid_post($varname));
    }

    /**
     * @param $plainArray
     * @return array
     */
    function cp_menu_plain2tree($plainArray)
    {
        $tree = $pointers = array();

        $pointers[dirname($plainArray[0]['LINK'])] =& $tree;

        foreach ($plainArray as $item) {
            $item['CHILDREN'] = array();

            $pointers[rtrim($item['LINK'], '/')] =& $item['CHILDREN'];

            $pointers[dirname($item['LINK'])][] = $item;
        }

        return $tree;
    }

    /**
     * @param $resultArray
     * @param $plainArray
     * @param $parentId
     */
    function cp_categories_plain2tree(&$resultArray, $plainArray, $parentId)
    {
        foreach ($plainArray as $item) {
            if ((int)$item['IBLOCK_SECTION_ID'] == $parentId) {
                $item['CHILDREN'] = array();
                self::cp_categories_plain2tree($item['CHILDREN'], $plainArray, $item['ID']);
                $resultArray[] = $item;
            }
        }
    }

    /**
     * @param $element
     * @return mixed
     */
    function get_iblock_detail_picture_callback($element)
    {
        if ($file = CFile::GetByID($element['DETAIL_PICTURE'])->GetNext()) {
            $element['DETAIL_IMAGE_SRC'] = CFile::GetFileSRC($file);
        }

        return $element;
    }

    /**
     * @param $element
     * @return mixed
     */
    function get_iblock_preview_picture_callback($element)
    {
        if ($file = CFile::GetByID($element['PREVIEW_PICTURE'])->GetNext()) {
            $element['PREVIEW_IMAGE_SRC'] = CFile::GetFileSRC($file);
        }

        return $element;
    }

    /**
     * @param array $arFilter
     * @param null $baseURL
     * @return array
     */
    function getIBlocks($arFilter = array(), $baseURL = null)
    {
        $retval = array();

        $res = CIBlock::GetList(
            array(),
            array_merge(array(
                'SITE_ID' => SITE_ID,
                'ACTIVE' => 'Y',
                'CNT_ACTIVE' => 'Y',
                'CHECK_PERMISSIONS' => 'N'
            ), $arFilter), true
        );

        while ($iblock = $res->Fetch()) {
            if ($file = CFile::GetByID($iblock['PICTURE'])->GetNext()) {
                $iblock['IMAGE_SRC'] = CFile::GetFileSRC($file);
            }

            $retval[] = $iblock;
        }

        return $retval;
    }

    /**
     * @param $id
     * @param $baseURL
     * @return bool
     */
    function getIBlockById($id, $baseURL)
    {
        $retval = self::getIBlocks(array('ID' => $id), $baseURL);
        return self::empty_array($retval) ? false : $retval[0];
    }

    /**
     *
     */
    function bitrix_404_error()
    {
        define('ERROR_404', true);
    }

    /**
     * @param bool $removeQuery
     * @return null|string|string[]
     */
    function cp_current_url($removeQuery = false)
    {
        global $APPLICATION;

        $url = $APPLICATION->GetCurUri();

        if ($removeQuery) {
            $url = preg_replace('/\?.*$/isxU', '', $url);
        }

        return $url;
    }

    /**
     * @param $type
     * @param array $filter
     * @return array
     */
    function cp_get_iblocks_by_type($type, $filter = array())
    {
        $retval = array();

        $filter['TYPE'] = $type;

        $res = \CIBlock::GetList(null, $filter);

        while ($row = $res->Fetch()) {
            $retval[] = $row;
        }

        return $retval;
    }

    /**
     * @return bool
     */
    function cp_is_main()
    {
        return self::cp_current_url(true) == SITE_URL;
    }

    /**
     * @param $IBlockId
     * @return mixed
     */
    function cp_get_ib_properties($IBlockId)
    {
        static $result = array();
        if (!isset($result[$IBlockId])) {
            $rs = CIBlockProperty::GetList(array('sort' => 'asc'), array('IBLOCK_ID' => $IBlockId));

            while ($ar = $rs->Fetch()) {
//            $result[$IBlockId][$ar['ID']] = $ar;
                $result[$IBlockId][$ar['CODE']] = $ar;
            }
        }

        return $result[$IBlockId];
    }

    /**
     * @param $fieldName
     * @return bool
     */
    function cp_is_standard_field($fieldName)
    {
        static $arStandardFields = array('ID', 'CODE', 'EXTERNAL_ID', 'XML_ID', 'NAME',
            'IBLOCK_ID', 'IBLOCK_SECTION_ID',
            'ACTIVE', 'DATE_ACTIVE_FROM', 'DATE_ACTIVE_TO',
            'SORT', 'PREVIEW_PICTURE', 'PREVIEW_TEXT', 'PREVIEW_TEXT_TYPE',
            'DETAIL_PICTURE', 'DETAIL_TEXT', 'DETAIL_TEXT_TYPE',
            'MODIFIED_BY', 'TAGS');

        return in_array($fieldName, $arStandardFields);
    }

    /**
     * @param $month
     * @param $format
     * @return false|string
     */
    function cp_month_name($month, $format)
    {
        $timestamp = mktime(1, 1, 1, $month, 1, date('Y'));
        return date($format, $timestamp);
    }

// получить родительские разделы с пользовательскими свойствами

    /**
     * @param $id
     * @param bool $section
     * @param array $arUf
     * @return array|bool
     */
    function getStructureUserProps($id, $section = false, $arUf = array())
    {
        $id = (int)$id;
        if (!$id) {
            return false;
        }
        if ($section) {
            $res = CIBlockSection::GetByID($id);
            if ($ar = $res->GetNext()) {
                $res = CIBlockSection::GetList(array(), array('IBLOCK_ID' => $ar['IBLOCK_ID'], 'ID' => $ar['ID']), false, $arUf);
            } else {
                return false;
            }
        } else {
            $res = CIBlockElement::GetByID($id);
            if ($ar = $res->GetNext()) {
                $res = CIBlockSection::GetList(array(), array('IBLOCK_ID' => $ar['IBLOCK_ID'], 'ID' => $ar['IBLOCK_SECTION_ID']), false, $arUf);
            } else {
                return false;
            }
        }
        // херню подсунули
        if (!$res->SelectedRowsCount()) {
            return false;
        }
        $arSect = $res->GetNext();
        $arStructure = array();
        $res = CIBlockSection::GetList(array('LEFT_MARGIN' => 'ASC'), array(
            'IBLOCK_ID' => $arSect['IBLOCK_ID'],
            '<=LEFT_BORDER' => $arSect['LEFT_MARGIN'],
            '>=RIGHT_BORDER' => $arSect['RIGHT_MARGIN'],
            '<DEPTH_LEVEL' => $arSect['DEPTH_LEVEL'],
        ), false, $arUf);

        while ($ar = $res->GetNext()) {
            $arStructure[] = $ar;
        }
        $arStructure[] = $arSect;
        return $arStructure;
    }

// получить родительские разделы без пользовательских свойств

    /**
     * @param $id
     * @param bool $section
     * @return array|bool
     */
    function getStructure($id, $section = false)
    {
        $id = (int)$id;
        if (!$id) {
            return false;
        }
        if (!$section) {
            $res = CIBlockElement::GetByID($id);
            if ($ar = $res->GetNext()) {
                $id = $ar['IBLOCK_SECTION_ID'];
            } else {
                return false;
            }
        }
        $res = CIBlockSection::GetNavChain(false, $id);
        $arStructure = array();
        while ($ar = $res->GetNext()) {
            $arStructure[] = $ar;
        }
        return $arStructure;
    }

    /*
    ORDER_PRICE
    ORDER_WEIGHT
    PRICE_DELIVERY
    PERSON_TYPE_ID
    PAY_SYSTEM_ID
    DELIVERY_ID
    */
// правила работы с корзиной
    /**
     * @param array $param
     * @param bool $update
     * @return mixed
     */
    function saleCart($param = array(), $update = false)
    {
        CModule::IncludeModule('sale');
        global $USER;
        $arOrderForDiscount = array(
            'SITE_ID' => SITE_ID,
            'USER_ID' => $USER->GetID(),
            'ORDER_PRICE' => $param['ORDER_PRICE'],
            'ORDER_WEIGHT' => $param['ORDER_WEIGHT'],
            'PRICE_DELIVERY' => $param['PRICE_DELIVERY'],
            'BASKET_ITEMS' => array(),
            "PERSON_TYPE_ID" => $param['PERSON_TYPE_ID'],
            "PAY_SYSTEM_ID" => $param['PAY_SYSTEM_ID'],
            "DELIVERY_ID" => $param['DELIVERY_ID'],
        );
        $dbBasketItems = CSaleBasket::GetList(
            array("NAME" => "ASC"),
            array(
                "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                "LID" => SITE_ID,
                "ORDER_ID" => 'NULL'
            ),
            false,
            false,
            array("ID", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "DELAY", "CAN_BUY", "PRICE", "WEIGHT", "NAME", "DISCOUNT_PRICE", "CURRENCY", "PRODUCT_PROVIDER_CLASS")
        );
        while ($arOneItem = $dbBasketItems->GetNext()) {
            $arOrderForDiscount['BASKET_ITEMS'][] = $arOneItem;
            $arOrderForDiscount['ORDER_WEIGHT'] += doubleval($arOneItem['WEIGHT']);
        }
        $arDiscountOptions = array();
        $arDiscountErrors = array();
        $ORDER_PRICE = 0;
        CSaleDiscount::DoProcessOrder($arOrderForDiscount, $arDiscountOptions, $arDiscountErrors);
        foreach ($arOrderForDiscount['BASKET_ITEMS'] as &$arOneItem) {
            $ORDER_PRICE += doubleval($arOneItem['PRICE']) * doubleval($arOneItem['QUANTITY']);
            $arBasketInfo = array(
                'IGNORE_CALLBACK_FUNC' => 'Y',
                'PRICE' => $arOneItem['PRICE'],
            );
            if (array_key_exists('DISCOUNT_PRICE', $arOneItem)) {
                $arBasketInfo['DISCOUNT_PRICE'] = $arOneItem['DISCOUNT_PRICE'];
            }
            // если нужно обновить поля корзины
            if ($update) {
                CSaleBasket::Update($arOneItem['ID'], $arBasketInfo);
            }
        }
        return $arOrderForDiscount['ORDER_PRICE'];
    }

    /**
     * @param $count
     * @param string $text0
     * @param string $text1
     * @param string $text2
     * @return string
     *
     * вывод правильного окончания
     * HelperFunctions::getEndWord($count,$text0='товаров',$text1='товар',$text2='товара') - возвращает слово с нужным окончанием
     *
     * $count - кол-во (в данном случае товаров)
     * 0 товаров
     * 1 товар
     * 2 товара
     *
     */
    public static function getEndWord($count, $text0 = 'товаров', $text1 = 'товар', $text2 = 'товара')
    {
        //$count = // общее кол-во
        $drob = intval(($count / 10 - intval($count / 10)) * 10);
        if (!$drob || ($count > 4 && $count < 21) || ($drob > 4 && $drob < 10)) {
            $text = $text0;
        } elseif ($drob == 1) {
            $text = $text1;
        } elseif ($drob > 1 && $drob < 5) {
            $text = $text2;
        }
        return $text;
    }

    /**
     * @param $count
     * @param string $text0
     * @param string $text1
     * @param string $text2
     * @return string
     *
     * вывод правильного окончания дней
     *
     */
    public static function getEndDay($count, $text0 = 'дней', $text1 = 'день', $text2 = 'дня')
    {
        //$count = // общее кол-во
        $drob = intval(($count / 10 - intval($count / 10)) * 10);
        if (!$drob || ($count > 4 && $count < 21) || ($drob > 4 && $drob < 10)) {
            $text = $text0;
        } elseif ($drob == 1) {
            $text = $text1;
        } elseif ($drob > 1 && $drob < 5) {
            $text = $text2;
        }
        return $text;
    }

    /**
     * @param int $page
     * @param int $sizePage
     * @param $total
     * @return array
     *
     * HelperFunctions::paginator($page=1,$sizePage=10,$total) - выводит пагинацию в виде:
     *      <div class="counter">
     *      <a href="/url/">Назад</a>
     *      <a href="/url/">1</a>
     *      <span>2</span>
     *      <a href="?page=3">3</a>
     *      <a href="?page=4">4</a>
     *      <a href="?page=5">5</a>
     *      <a href="?page=5">Дальше</a>
     *      </div>
     * $page=1 - текущая страница
     * $sizePage=10 - элементов на странице
     * $total - всего элементов
     *
     */
    public static function paginator($page = 1, $sizePage = 10, $total)
    {
        $html = '';
        if ($page && $sizePage && $total) {
            $count = ceil($total / $sizePage);
            if ($page == $count) {
                $last = $count;
                if ($page - 4 > 0) {
                    $first = $page - 4;
                } else {
                    $first = 1;
                }
            } elseif ($page < 3) {
                $first = 1;
                $last = $count > $last ? 5 : $count;
            } elseif ($count - 2 < $page) {
                $last = $count;
                $first = $last - 5;
            } else {
                $first = $page - 2;
                $last = $page + 2;
            }
            if ($last > $count) {
                $last = $count;
            }
            $html .= '<div class="counter">';
            if ($page > 1) {
                if ($page == 2) {
                    preg_match('/^([^\?]+)/si', $_SERVER['REQUEST_URI'], $uri);
                    $uri = $uri[1];
                } else {
                    $uri = '?page=' . ($page - 1);
                }
                $html .= '<a href="' . $uri . '">Назад</a>';
            } else {
                $html .= '<span>Назад</span>';
            }
            preg_match('/^([^\?]+)/si', $_SERVER['REQUEST_URI'], $uri);
            $uri = $uri[1];
            for ($i = $first; $i <= $last; $i++) {
                if ($i == $page) {
                    $html .= '<span>' . $page . '</span>';
                } else {
                    if ($i == 1) {
                        $html .= '<a href="' . $uri . '">' . $i . '</a>';
                    } else {
                        $html .= '<a href="?page=' . $i . '">' . $i . '</a>';
                    }
                }
            }
            if ($count == $page) {
                $html .= '<span>Дальше</span>';
            } else {
                $html .= '<a href="?page=' . ($page + 1) . '">Дальше</a>';
            }
            $html .= '</div>';
        }
        return array('html' => $html);
        /*      <div class="counter">
            <a href="#">Назад</a>
            <a href="#">1</a>
            <span>2</span>
            <a href="#">3</a>
            <a href="#">4</a>
            <a href="#">5</a>
            <a href="#">Дальше</a>
          </div>*/
    }

    /**
     * @param $order_id
     * @param $prop_id
     * @param bool $or_prop_id
     * @return bool
     *
     * HelperFunctions::getPropOrderVal($order_id, $prop_id, [$or_prop_id=false]) - возвращает значение свойства заказа
     *
     * $order_id - ID заказа
     * $prop_id - ID свойства
     * $or_prop_id - ID альтернативного свойства
     *
     * возвращает значение свойства с id  $prop_id в заказе с id $order_id
     * если нет значения свойства, то происходит попытка вернуть свойство с id $or_prop_id, если задан
     * часто приходиться получить свойство в зависимости от типа плательщика
     * если значения свойство не получено, то возвращается false
     *
     */
    public static function getPropOrderVal($order_id, $prop_id, $or_prop_id = false)
    {
        $result = false;
        $itr = array($prop_id, $or_prop_id);
        foreach ($itr as $_prop_id) {
            if ($_prop_id == false) {
                continue;
            }

            $db_vals = CSaleOrderPropsValue::GetList(
                array("SORT" => "ASC"),
                array(
                    "ORDER_ID" => $order_id,
                    "ORDER_PROPS_ID" => $_prop_id
                )
            );
            if ($arVals = $db_vals->Fetch()) {
                if (!empty($arVals['VALUE'])) {
                    $result = $arVals['VALUE'];
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * @param $idImg
     * @param int $width
     * @param int $height
     * @param bool $width_size
     * @return bool
     *
     * ресайз фото. ID фото в битриксе, макс ширина, макс высота, возвр. ли размеры после ресайза
     *
     * HelperFunctions::getResizeImg($idImg,[[[$width=220],$height=220],$width_size=false]) - ресайз фото
     * $idImg - ID фото
     * $width - максимальная ширина
     * $height - максимальная высота
     * $width_size - FALSE - вернуть url, TRUE вернуть массив c url фото, с получившейся шириной, высотой и тп
     *
     */
    public static function getResizeImg($idImg, $width = 220, $height = 220, $width_size = false)
    {
        if ($width_size) {
            if ($renderImage = CFile::ResizeImageGet($idImg, array("width" => $width, "height" => $height), BX_RESIZE_IMAGE_PROPORTIONAL, true)) {
                return $renderImage;
            }
            return false;
        } else {
            if ($renderImage = CFile::ResizeImageGet($idImg, array("width" => $width, "height" => $height))) {
                return $renderImage['src'];
            }
            return false;
        }
    }

    /**
     * @param bool $group
     * @return int
     *
     * получить кол-во товара в корзине
     *
     * HelperFunctions::getCountProductsBasket(group=false) - получить общее кол-во товара в корзине
     *
     * false - общее кол-во товара в корзине
     * true - получить кол-во позиций в корзине
     *
     */
    static public function getCountProductsBasket($group = false)
    {
        CModule::IncludeModule('sale');
        $totalProduct = 0;
        $dbBasketItems = CSaleBasket::GetList(
            array(), array(
            "FUSER_ID" => CSaleBasket::GetBasketUserID(),
            "LID" => SITE_ID,
            "ORDER_ID" => "NULL"
        ), false, false, array()
        );
        if ($group) {
            return $dbBasketItems->SelectedRowsCount();
        }
        while ($arItems = $dbBasketItems->GetNext()) {
            $totalProduct += $arItems['QUANTITY'];
        }
        return $totalProduct;
    }


    /**
     * @param int $iblockID
     * @param int $sect_id
     * @param string $active
     * @param array $arSort
     * @param array $arUF
     * @return array|bool
     *
     * получить подразделы
     *
     * HelperFunctions::getSubSections($iblockID,[[[$sect_id=0],$active='Y'],$arSort=array('SORT'=>'ASC'),$arUF=array()]) - получить массив разделов (в коде массив array(...,array('id'=>...,'name'=>...,'url'=>...,'img'=>...),...) )
     *
     * $iblockID - ID инфоблока (обязательно)
     * $sect_id - в каком разделе (по-умолчанию в корне)
     * $active - активность (по-умолчанию только активные)
     * $arSort - сортировка (по-умолчанию array('SORT'=>'ASC'))
     * $arUF - массив пользовательских полей
     *
     */
    static public function getSubSections($iblockID = 0, $sect_id = 0, $active = 'Y', $arSort = array('SORT' => 'ASC'), $arUF = array())
    {
        if ($iblockID) {
            $res = CIBlockSection::GetList($arSort, array('SECTION_ID' => $sect_id, 'IBLOCK_ID' => $iblockID, 'ACTIVE' => $active), false, $arUF);
            while ($arSect = $res->GetNext()) {
                $img = '/i/noPhoto.png';// nophoto
                if ($arSect['PICTURE']) {
                    $img = CFile::GetPath($arSect['PICTURE']);
                }
                $arElems[] = array(
                    'name' => $arSect['NAME'],
                    'id' => $arSect['ID'],
                    'url' => $arSect['SECTION_PAGE_URL'],
                    'img' => $img,
                );
            }
            return $arElems;
        }
        return false;
    }


    /**
     * @param string $id
     * @param string $idUser
     * @return array
     *
     * получить содержимое корзины
     *
     * HelperFunctions::getBasket($id="NULL",$idUser='') - получить содержимое корзины. В коде массив. По-умолчанию текущая корзина.
     *
     * $id - ID заказа
     * $idUser - ID пользователя
     * array(
     * 'arBasket'=>array(
     * ...,
     * array(
     * 'id'=>...(ID товара)...,
     * 'name'=>...,
     * 'url'=>...,
     * 'img'=>...,
     * 'desc'=>...(PREVIEW_TEXT товара)...,
     * 'price'=>...(форматированная цена товара х ххх.хх)...,
     * 'num_price'=>...(неформатированная цена)...,
     * 'count'=>...,
     * 'sum'=>...(сумма)...,
     * 'idBasket'=>...(ID товара в корзине)...,
     * ),
     * ...,
     * ),
     * 'totalProduct'=>...(общее кол-во товара)...,
     * 'totalSumm'=>...(общая сумма)...
     * );
     *
     */
    public static function getBasket($id = "NULL", $idUser = '')
    {
        CModule::IncludeModule('sale');
        if (!$idUser) {
            $idUser = CSaleBasket::GetBasketUserID();
        }
        //CModule::IncludeModule('iblock');
        $dbBasketItems = CSaleBasket::GetList(
            array(), array(
            "FUSER_ID" => $idUser,
            "LID" => SITE_ID,
            "ORDER_ID" => $id
        ), false, false, array()
        );
        $basket = array();
        $totalProduct = $totalSumm = 0;
        while ($arItems = $dbBasketItems->GetNext()) {
            $res = CIBlockElement::GetByID($arItems['PRODUCT_ID']);
            $arElem = $res->GetNext();
            $img = '/i/noPhoto.png';
            if ($arElem['PREVIEW_PICTURE']) {
                $img = CFile::GetPath($arElem['PREVIEW_PICTURE']);
            }
            $basket[] = array(
                'id' => $arItems['PRODUCT_ID'],
                'name' => $arElem['NAME'],
                'url' => $arElem['DETAIL_PAGE_URL'],
                'img' => $img,
                'desc' => htmlspecialcharsBack($arElem['PREVIEW_TEXT']),
                'price' => number_format($arItems['PRICE'], 2, '.', ' '),
                'num_price' => $arItems['PRICE'],
                'count' => (int)$arItems['QUANTITY'],
                'sum' => $arItems['QUANTITY'] * $arItems['PRICE'],
                'idBasket' => $arItems['ID'],
                'delay' => $arItems['DELAY'],
            );
            $totalProduct += (int)$arItems['QUANTITY'];
            $totalSumm += $arItems['PRICE'] * $arItems['QUANTITY'];
        }
        return array('arBasket' => $basket, 'totalProduct' => $totalProduct, 'totalSumm' => $totalSumm);
    }

    /**
     * @param $id
     * @return bool
     *
     * проверить есть ли товар в корзине
     *
     * HelperFunctions::checkProductToCart($id) - проверить есть ли товар в корзине. Возвращает true|false
     *
     */
    public static function checkProductToCart($id)
    {
        CModule::IncludeModule('sale');
        if ($id) {
            $dbBasketItems = CSaleBasket::GetList(array(),
                array(
                    "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                    "LID" => SITE_ID,
                    "ORDER_ID" => "NULL",
                    "PRODUCT_ID" => $id,
                ), false, false, array()
            );
            if ($dbBasketItems->SelectedRowsCount()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $IBLOCK_ID
     * @param $name
     * @return bool|mixed|null|string|string[]
     *
     * HelperFunctions::getNewCodeSection($iblockID,$name) - создать/получить уникальный символьный код для разделов
     *
     */
    public static function getNewCodeSection($IBLOCK_ID, $name)
    {
        if ($IBLOCK_ID && $name) {
            $n = '';
            do {
                $code = Translit::UrlTranslit($name . $n);
                $res = CIBlockSection::GetList(array(), array('IBLOCK_ID' => $IBLOCK_ID, 'CODE' => $code), false);
                $n++;
            } while ($res->SelectedRowsCount());
            return $code;
        }
        return false;
    }

    /**
     * @param $iblockID - ID информационного блока
     * @param $name - имя
     * @return string
     *
     * HelperFunctions::getNewCodeElement($iblockID,$name) - создать/получить уникальный символьный код для элементов
     *
     */
    public static function getNewCodeElement($IBLOCK_ID, $name)
    {
        if ($IBLOCK_ID && $name) {
            $n = '';
            do {
                $code = Translit::UrlTranslit($name . $n);
                $res = CIBlockElement::GetList(array(), array('IBLOCK_ID' => $IBLOCK_ID, 'CODE' => $code), false, false, array());
                $n++;
            } while ($res->SelectedRowsCount());
            return $code;
        }
        return false;
    }

    /**
     * @param $iblock_id
     * @param int $page
     * @param int $count
     * @return array|mixed
     *
     * HelperFunctions::getNews($iblock_id,$page,$count) - получить массив новостей с пагинацией (необходим myCache)
     *
     * $iblock_id - ID инфоблока
     * $page - страница
     * $count - кол-во новостей на странице
     * array(
     * 'items'=>array(
     * ...,
     * array(
     * 'id'=>...,
     * 'name'=>...,
     * 'name_'=>...(для вывода в alt, title. В имени " заменены на ')...,
     * 'img'=>...(картинка детальная)...,
     * 'img_small'=>...(картинка анонса)...,
     * 'anons'=>...(текст анонса)...,
     * 'url'=>...(согласно DETAIL_PAGE_URL)...,
     * 'date'=>...(в коде в формате 13 февраля 2009, поменяйте на необходимый)...,
     * ),
     * ...,
     * ),
     * 'paginator'=>...Строка. Пагинация согласно шаблона .default компонента system.pagenavigation...
     * );
     *
     */
    public static function getNews($iblock_id, $page = 1, $count = 10)
    {
        if (!$page) {
            $page = 1;
        }
        if (!$count) {
            $count = 10;
        }
        $cache = new myCache('news_' . $iblock_id . '_' . $page . '_' . $count);
        if ($cache->check()) {
            $arSelect = array(
                'IBLOCK_ID' => $iblock_id,
                'ACTIVE' => 'Y'
            );
            $arPage = array(
                'iNumPage' => $page,
                'nPageSize' => $count
            );
            $res = CIBlockElement::GetList(array('ACTIVE_FROM' => 'DESC', 'SORT' => 'ASC'), $arSelect, false, $arPage, array());
            $arNews = array(
                'paginator' => $res->GetPageNavStringEx($navComponentObject, "", "")
            );
            while ($arItem = $res->GetNext()) {
                $img = $img_s = CFG::NOPHOTO_NEWS;
                if ((int)$arItem['DETAIL_PICTURE']) {
                    $img = CFile::GetPath($arItem['DETAIL_PICTURE']);
                    $img_s = CFile::GetPath($arItem['PREVIEW_PICTURE']);
                }
                $arNews['items'][$arItem['ID']] = array(
                    'id' => $arItem['ID'],
                    'name' => $arItem['NAME'],
                    'name_' => str_replace('"', "'", $arItem['NAME']),
                    'img' => $img,
                    'img_small' => $img_s,
                    'anons' => $arItem['PREVIEW_TEXT'],
                    'url' => $arItem['DETAIL_PAGE_URL'],
                    'date' => FormatDate('d f Y', MakeTimeStamp($arItem['ACTIVE_FROM'], CSite::GetDateFormat())),
                );
            }
            $cache->read(serialize($arNews));
            return $arNews;
        } else {
            return unserialize($cache->read());
        }
    }

    /**
     * @return bool|int
     *
     * Функция определения мобильных браузеров
     */
    public static function is_mobile()
    {
        $user_agent = strtolower(getenv('HTTP_USER_AGENT'));
        $accept = strtolower(getenv('HTTP_ACCEPT'));

        if ((strpos($accept, 'text/vnd.wap.wml') !== false) || (strpos($accept, 'application/vnd.wap.xhtml+xml') !== false)) {
            return 1; // Мобильный браузер обнаружен по HTTP-заголовкам
        }

        if (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) {
            return 2; // Мобильный браузер обнаружен по установкам сервера
        }

        if (preg_match('/(mini 9.5|vx1000|lge |m800|e860|u940|ux840|compal|' .
            'wireless| mobi|ahong|lg380|lgku|lgu900|lg210|lg47|lg920|lg840|' .
            'lg370|sam-r|mg50|s55|g83|t66|vx400|mk99|d615|d763|el370|sl900|' .
            'mp500|samu3|samu4|vx10|xda_|samu5|samu6|samu7|samu9|a615|b832|' .
            'm881|s920|n210|s700|c-810|_h797|mob-x|sk16d|848b|mowser|s580|' .
            'r800|471x|v120|rim8|c500foma:|160x|x160|480x|x640|t503|w839|' .
            'i250|sprint|w398samr810|m5252|c7100|mt126|x225|s5330|s820|' .
            'htil-g1|fly v71|s302|-x113|novarra|k610i|-three|8325rc|8352rc|' .
            'sanyo|vx54|c888|nx250|n120|mtk |c5588|s710|t880|c5005|i;458x|' .
            'p404i|s210|c5100|teleca|s940|c500|s590|foma|samsu|vx8|vx9|a1000|' .
            '_mms|myx|a700|gu1100|bc831|e300|ems100|me701|me702m-three|sd588|' .
            's800|8325rc|ac831|mw200|brew |d88|htc\/|htc_touch|355x|m50|km100|' .
            'd736|p-9521|telco|sl74|ktouch|m4u\/|me702|8325rc|kddi|phone|lg |' .
            'sonyericsson|samsung|240x|x320vx10|nokia|sony cmd|motorola|' .
            'up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|' .
            'pocket|kindle|mobile|psp|treo)/', $user_agent)) {
            return 3; // Мобильный браузер обнаружен по сигнатуре User Agent
        }

        if (in_array(substr($user_agent, 0, 4),
            Array("1207", "3gso", "4thp", "501i", "502i", "503i", "504i", "505i", "506i",
                "6310", "6590", "770s", "802s", "a wa", "abac", "acer", "acoo", "acs-",
                "aiko", "airn", "alav", "alca", "alco", "amoi", "anex", "anny", "anyw",
                "aptu", "arch", "argo", "aste", "asus", "attw", "au-m", "audi", "aur ",
                "aus ", "avan", "beck", "bell", "benq", "bilb", "bird", "blac", "blaz",
                "brew", "brvw", "bumb", "bw-n", "bw-u", "c55/", "capi", "ccwa", "cdm-",
                "cell", "chtm", "cldc", "cmd-", "cond", "craw", "dait", "dall", "dang",
                "dbte", "dc-s", "devi", "dica", "dmob", "doco", "dopo", "ds-d", "ds12",
                "el49", "elai", "eml2", "emul", "eric", "erk0", "esl8", "ez40", "ez60",
                "ez70", "ezos", "ezwa", "ezze", "fake", "fetc", "fly-", "fly_", "g-mo",
                "g1 u", "g560", "gene", "gf-5", "go.w", "good", "grad", "grun", "haie",
                "hcit", "hd-m", "hd-p", "hd-t", "hei-", "hiba", "hipt", "hita", "hp i",
                "hpip", "hs-c", "htc ", "htc-", "htc_", "htca", "htcg", "htcp", "htcs",
                "htct", "http", "huaw", "hutc", "i-20", "i-go", "i-ma", "i230", "iac",
                "iac-", "iac/", "ibro", "idea", "ig01", "ikom", "im1k", "inno", "ipaq",
                "iris", "jata", "java", "jbro", "jemu", "jigs", "kddi", "keji", "kgt",
                "kgt/", "klon", "kpt ", "kwc-", "kyoc", "kyok", "leno", "lexi", "lg g",
                "lg-a", "lg-b", "lg-c", "lg-d", "lg-f", "lg-g", "lg-k", "lg-l", "lg-m",
                "lg-o", "lg-p", "lg-s", "lg-t", "lg-u", "lg-w", "lg/k", "lg/l", "lg/u",
                "lg50", "lg54", "lge-", "lge/", "libw", "lynx", "m-cr", "m1-w", "m3ga",
                "m50/", "mate", "maui", "maxo", "mc01", "mc21", "mcca", "medi", "merc",
                "meri", "midp", "mio8", "mioa", "mits", "mmef", "mo01", "mo02", "mobi",
                "mode", "modo", "mot ", "mot-", "moto", "motv", "mozz", "mt50", "mtp1",
                "mtv ", "mwbp", "mywa", "n100", "n101", "n102", "n202", "n203", "n300",
                "n302", "n500", "n502", "n505", "n700", "n701", "n710", "nec-", "nem-",
                "neon", "netf", "newg", "newt", "nok6", "noki", "nzph", "o2 x", "o2-x",
                "o2im", "opti", "opwv", "oran", "owg1", "p800", "palm", "pana", "pand",
                "pant", "pdxg", "pg-1", "pg-2", "pg-3", "pg-6", "pg-8", "pg-c", "pg13",
                "phil", "pire", "play", "pluc", "pn-2", "pock", "port", "pose", "prox",
                "psio", "pt-g", "qa-a", "qc-2", "qc-3", "qc-5", "qc-7", "qc07", "qc12",
                "qc21", "qc32", "qc60", "qci-", "qtek", "qwap", "r380", "r600", "raks",
                "rim9", "rove", "rozo", "s55/", "sage", "sama", "samm", "sams", "sany",
                "sava", "sc01", "sch-", "scoo", "scp-", "sdk/", "se47", "sec-", "sec0",
                "sec1", "semc", "send", "seri", "sgh-", "shar", "sie-", "siem", "sk-0",
                "sl45", "slid", "smal", "smar", "smb3", "smit", "smt5", "soft", "sony",
                "sp01", "sph-", "spv ", "spv-", "sy01", "symb", "t-mo", "t218", "t250",
                "t600", "t610", "t618", "tagt", "talk", "tcl-", "tdg-", "teli", "telm",
                "tim-", "topl", "tosh", "treo", "ts70", "tsm-", "tsm3", "tsm5", "tx-9",
                "up.b", "upg1", "upsi", "utst", "v400", "v750", "veri", "virg", "vite",
                "vk-v", "vk40", "vk50", "vk52", "vk53", "vm40", "voda", "vulc", "vx52",
                "vx53", "vx60", "vx61", "vx70", "vx80", "vx81", "vx83", "vx85", "vx98",
                "w3c ", "w3c-", "wap-", "wapa", "wapi", "wapj", "wapm", "wapp", "wapr",
                "waps", "wapt", "wapu", "wapv", "wapy", "webc", "whit", "wig ", "winc",
                "winw", "wmlb", "wonu", "x700", "xda-", "xda2", "xdag", "yas-", "your",
                "zeto", "zte-"))) {
            return 4; // Мобильный браузер обнаружен по сигнатуре User Agent
        }

        return false; // Мобильный браузер не обнаружен
    }

    /**
     *  Excel 파일을 CSV 파일로 변환
     *
     * @param  $excelPath string excel 파일 경로
     * @param  $csvPath   string csv 파일 경로
     * @return void
     */
    public static function convertExcelIntoCsv(string $excelPath, string $csvPath)
    {
        echo "\nConverting Excel into CSV format...\n";
        try {
            // excel 파일을 로드하여 PHPExcel 선언
            $objPhpExcel = \PHPExcel_IOFactory::load($excelPath);
            // Excel->CSV 형식의 Object로 변환
            $objWriter = new \PHPExcel_Writer_CSV($objPhpExcel);
            // csv 경로에 같은 파일이 있으면 삭제
            if (file_exists($csvPath)) {
                echo "CSV file rewriting...\n";
                unlink($csvPath);
            }
            // 해당 경로에 csv 파일 저장
            $objWriter->save($csvPath);
            echo "Conversion success! \n";
        } catch (\PHPExcel_Reader_Exception $re) {
            die('Error loading file: ' . $e->getMessage());
        } finally {
            // 메모리 release 작업
            if ($objPhpExcel instanceof \PHPExcel_IOFactory) {
                $objPhpExcel->disconnectWorksheets();
                unset($objPhpExcel);
            }
            unset($objWriter);
        }
    }
}
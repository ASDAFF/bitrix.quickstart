<?php

function _empty()
{
    foreach (func_get_args() as $arg)
    {
        if (empty($arg))
        {
            return true;
        }
    }

    return false;
}

function dv()
{
    $args = func_get_args();
    if (!$args) return false;

    for ($i = 0; $i < count($args); $i++)
    {
        echo '<pre style="text-align: left; background-color: white; color: black; font-size: 12px">' . htmlspecialchars(print_r($args[$i], true)) . '</pre>';
    }
}

function dvv()
{
    $args = func_get_args();
    if (!$args) return false;

    for ($i = 0; $i < count($args); $i++)
    {
        echo '<pre style="text-align: left; background-color: white; color: black;">';
        var_dump($args[$i]);
        echo '</pre>';
    }
}

function dt($time, $format = 'd.m.Y H:i:s')
{
    foreach ((array) $time as $t)
    {
        dv(date($format, $t));
    }
}

function format_file_size($size,$precision=0)
{
    if ($precision==0){
        $precision=2;
    }
    if ($size >= 1262485504)
    {
        return number_format($size / 1262485504, $precision, '.', ' ') . ' Gb';
    }
    elseif ($size >= 1048576)
    {
        return number_format($size / 1048576, $precision, '.', ' ') . ' Mb';
    }
    elseif ($size >= 1024)
    {
        return number_format($size / 1024, $precision, '.', ' ') . ' kb';
    }
    else
    {
        return number_format($size, 0, '.', ' ') . ' b';
    }
}

function redirect($location, $timeout = 0)
{
    if ( ($timeout == 0) && (!headers_sent()))
    {
        header('Location: ' . $location);
        exit;
    }
    else
    {
        $timeout = $timeout * 1000;

        if ($timeout > 0)
        {
            print "<p>Click <a href=\"" . $location . "\">here</a> to continue</p>\n";
        }

        print <<< JS
<script>
window.setTimeout("window.location = '{$location}'", $timeout);
</script>

JS;
    }
}

/**
 * @param int time Unix timestamp
 * @param string mode day|hour|month|year
 * @param boolean sqlReady
 */
function time_borders($time, $mode = 'day', $sqlReady = false)
{
    $_time  = getdate($time);
    $out    = array();

    switch ($mode)
    {
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
            $out[] = mktime(23, 59, 59, $_time['mon'], date('t', $time),  $_time['year']);
            break;

        case 'year':
            $out[] = mktime(0, 0, 0, 1, 1, $_time['year']);
            $out[] = mktime(23, 59, 59, 12, 31, $_time['year']);
            break;
    }

    if ($sqlReady)
    {
        $out[0] = date('Y-m-d H:i:s', $out[0]);
        $out[1] = date('Y-m-d H:i:s', $out[1]);
    }

    return $out;
}

function syscall($command)
{
    if ($proc = popen("($command)2>&1", 'r'))
    {
        while (!feof($proc))
        {
            @$result .= fgets($proc, 1000);
        }

        pclose($proc);
        return $result;
    }
}

function enconvert($string, $from_encoding, $to_encoding)
{
    if (function_exists('mb_convert_encoding'))
    {
        return mb_convert_encoding($string, $to_encoding, $from_encoding);
    }
    else if (function_exists('iconv'))
    {
        return iconv($from_encoding, $to_encoding, $string);
    }
    else
    {
        return $string;
    }
}

function enconvert_file($filename, $from_encoding, $to_encoding)
{
    return file_put_contents($filename, enconvert(file_get_contents($filename), 'CP1251', 'UTF8'));
}

function text4JS($text, $quote = "'")
{
    $text = str_replace(array("\n", "\r"), '', $text);
    return str_replace($quote, "\\{$quote}", $text);
}

function text4flash($text)
{
    $text = str_replace('strong>', 'b>', $text);
    $text = preg_replace('/<br\s*\/>/i', '<br>', $text);
    return text4JS((strip_tags(preg_replace('/<[a-z0-9]+\s+([^\>]*)(?!\/)>/isxU', '', $text), '<p><br><i><b><u><a>')), '"');
}

function zerofill($number, $length)
{
    return str_repeat('0', $length - strlen($number)) . (int) $number;
}

function coalesce()
{
    $args = func_get_args();
    for ($i=0, $cnt = func_num_args(); $i < $cnt; $i++)
    {
        if (!empty($args[$i]))
        {
            return $args[$i];
        }
    }
    return end($args);
}

function url_name($string, $allow_slash = false)
{
    return trim(preg_replace('/[^a-z0-9\-_' . ($allow_slash ? '\/' : '') . ']/', '', $string), '/');
}

function url_trailing_sign($url)
{
    return strstr($url, '?') ? '&' : '?';
}

function url_rm_vars($url, $varnames)
{
    if (!is_array($varnames))
    {
        $varnames = preg_split('/\s*,\s*/isx', $varnames);
    }

    foreach ($varnames as $var)
    {
        if (empty($var)) continue;
        $url = preg_replace("/(\?|&)$var=([^\?\&$]*)/isx", '', $url);
    }

    return $url;
}

function checkEmail($email)
{
    return preg_match('|^[_a-z0-9:()-]+(\.[_a-z0-9:()-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*$|i', $email);
}

function empty_array($array)
{
    foreach ((array) $array as $k => $v)
    {
        if (is_array($v) && !empty_array($v)) return false;
        elseif (!empty($v)) return false;
    }

    return true;
}

function get_ymd($time, $month_format = 'm')
{
    return array(date('Y', $time), date($month_format, $time), date('d', $time));
}

function time_borders_ymd($year = 0, $month = 0, $day = 0)
{
    $time = false;

    if (!empty($day))
    {
        $time = mktime(1,1,1,$month,$day,$year);
        $mode = 'day';
    }
    else if (!empty($month))
    {
        $time = mktime(1,1,1,$month,1,$year);
        $mode = 'month';
    }
    else if (!empty($year))
    {
        $time = mktime(1,1,1,1,1,$year);
        $mode = 'year';
    }

    return $time ? time_borders($time, $mode) : false;
}

function count_time($show = false)
{
    static $start;

    if (!$show)
    {
        $start = getmicrotime();
    }
    else
    {
        dv(getmicrotime() - $start);
        $start = 0;
    }
}

function dmY2time($val, $separator = '.')
{
    list($d, $m, $Y) = explode($separator, $val);
    return mktime(0,0,0, $m, $d, $Y);
}

function get_microtime($stamp = '')
{
    if (empty($stamp)) $stamp = microtime();
    list($usec, $sec) = explode(" ", $stamp);
    return ((float)$usec + (float)$sec);
}

function dm($object)
{
    if (!is_object($object))
    {
        dv($object);
        return ;
    }

    dv(get_class($object) . ':');

    $methods = get_class_methods($object);
    sort($methods);
    dv($methods);
}

function array_split($array, $chunks = 2, $preserveKeys = false)
{
    $newArray = array_fill(0, $chunks, array());

    $chunkNum = 0;

    foreach ($array as $k => $v)
    {
        $key = $preserveKeys ? $k : count($newArray[$chunkNum]);
        $newArray[$chunkNum][$key] = $v;

        $chunkNum++;

        if ($chunkNum > $chunks - 1)
        {
            $chunkNum = 0;
        }
    }

    return $newArray;
}

function num_to_string($value, $_1, $_2, $_3, $return_value = true)
{
    if (($value > 10 && $value < 20) || ($value > 110 && $value < 120))
    {
        $v = $_3;
    }
    else
    {
        $arr = preg_split('//', (string) $value, null, PREG_SPLIT_NO_EMPTY);
        $last = end($arr);

        if ($last == 1)
        {
            $v = $_1;
        }
        else if ($last > 1 && $last < 5)
        {
            $v = $_2;
        }
        else
        {
            $v = $_3;
        }
    }

    return $return_value ? (int) $value . " $v" : $v;
}

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

    if (func_num_args() > 1)
    {
        $timestamp = func_get_arg(1);
        return strtr(date(func_get_arg(0), $timestamp), $translation);
    }
    else
    {
        return strtr(date(func_get_arg(0)), $translation);
    }
}

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

    if (func_num_args() > 1)
    {
        $timestamp = func_get_arg(1);
        return strtr(date(func_get_arg(0), $timestamp), $translation);
    }
    else
    {
        return strtr(date(func_get_arg(0)), $translation);
    }
}

function space2br($text)
{
    return preg_replace('/\s+/', '<br/>', $text);
}

function is_ajax_request()
{
    return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || isset($_REQUEST['__ajax__']);
}

function fix_url($url, $add_http = true)
{
    $url = str_replace('http://', '', $url);
    $url = trim(preg_replace('|/+|', '/', $url), '/');

    return $add_http ? "http://$url/" : $url;
}

function truncate_by_words($text, $words = 40, $hellip = '&hellip;')
{
    $words_array = preg_split('/\s+/u', strip_tags($text), -1, PREG_SPLIT_NO_EMPTY);

    $slice = array_slice($words_array, 0, $words);
    $retval = join(' ', $slice);

    if (count($words_array) > count($slice))
    {
        $retval .= $hellip;
    }

    return $retval;
}

function cp_thumb_url_hash($string)
{
    $hash = md5(sha1(md5(mb_strtoupper(trim($string, '/')))));

    $hash = substr($hash, 4, 8);

    return $hash;
}

function with($object)
{
    return $object;
}

function dv_rb($die = true)
{
    global $APPLICATION;

    $APPLICATION->RestartBuffer();

    call_user_func_array('dv', func_get_args());

    if ($die)
    {
        die;
    }
}

class Exception404 extends Exception {} ;

function __($key, $special = false)
{
    return  cp_get_language_message($key, $special);
}

function cp_get_language_message($key, $special = false)
{
    static $MESS;

    $langFilePath = BASE_PATH . '/bitrix/templates/.default/lang/'.LANGUAGE_ID.'/phrases.php';

    if (!isset($MESS))
    {
        if (file_exists($langFilePath))
        {
            require $langFilePath;
        }
    }

    $retval = $key;

    if (isset($MESS[$key]))
    {
        $retval = $special ? htmlspecialchars($MESS[$key]) : $MESS[$key];
    }
    else
    {
        if (!file_exists($langFilePath))
        {
            @mkdir(dirname($langFilePath), 0777, true);
            @touch($langFilePath, 0666);
            file_put_contents($langFilePath, "<?\n\$MESS = array(\n);");
        }

        if (file_exists($langFilePath) && ($content = file_get_contents($langFilePath)))
        {
            $string = "'" . addslashes($key) . "'";
            $content = str_replace('?>', '', $content);
            $content = str_replace(');', "    $string => $string,\n);", $content);
            file_put_contents($langFilePath, $content);

            require $langFilePath;
        }

        $missingFile = BASE_PATH . '/temp/missing_messages.txt';

        $missing = @file($missingFile);

        $phrases = array();

        foreach ($missing as $v)
        {
            list($phrase, $url) = explode("\t", $v);
            $phrases[] = trim($phrase);
        }

        if (!in_array($key, $phrases))
        {
            global $APPLICATION;

            $fp = fopen($missingFile, 'a+');
            fwrite($fp, "$key\t{$APPLICATION->GetCurUri()}\r\n");
            fclose($fp);
        }
    }

    return $retval;
}

function cp_fetch_site_info($site_id = 0)
{
    static $info;

    if (!isset($info))
    {
        $rsSites = CSite::GetByID(coalesce($site_id, SITE_ID));
        $info = $rsSites->Fetch();
    }

    return $info;
}

function cp_get_site_email()
{
    $info = cp_fetch_site_info();
    return $info['EMAIL'];
}

function cp_get_site_name()
{
    $info = cp_fetch_site_info();
    return $info['SITE_NAME'];
}

function cp_get_thumb_url($url, $options = array())
{
    if (!empty($options))
    {
        $url_part = '/';

        foreach ($options as $k => $v)
        {
            $url_part .= substr($k, 0, 1) . "$v-";
        }

        $url_part = trim($url_part, '-');

        $url = $url_part . '/' . trim($url, '/');

        $url = '/' . basename(IMG_CACHE_PATH) . $url . '?' . cp_thumb_url_hash($url);
    }

    return $url;
}

function cp_bitrix_date($format, $date)
{
    list ($d, $m, $y) = explode('.', $date);
    $time = mktime(1,1,1,$m,$d,$y);

    $retval = null;

    switch (LANGUAGE_ID)
    {
        case 'en':
            $retval = date($format, $time);
            break;

        default:
            $retval = russian_date($format, $time);
            break;
    }

    return $retval;
}

function CPGetMessage($key, $aReplace = false)
{
    $retval = GetMessage($key, $aReplace);

    return empty($retval) ? $key : $retval;
}

function cp_language_text($key)
{
    static $list;

    if (!isset($list))
    {
        $list = \Cpeople\Classes\Block\Getter::instance()
            ->addFilter('IBLOCK_TYPE', 'texts')
            ->setClassName('MultilangDummy')
            ->get();
    }

    /** @var $text  MultilangDummy */
    $text = false;

    foreach ($list as $item)
    {
        if ($item->code == $key)
        {
            $text = $item;
            break;
        }
    }

    if (!$text)
    {
        throw new Exception("Text with key '$key' not found");
    }

    return $text->getLangPropText('TEXT');
}

function cp_bitrix_sessid_post($varname='sessid')
{
    return preg_replace('/id\=".*"/isxU', '', bitrix_sessid_post($varname));
}

function cp_menu_plain2tree($plainArray)
{
    $tree = $pointers = array();

    $pointers[dirname($plainArray[0]['LINK'])] =& $tree;

    foreach ($plainArray as $item)
    {
        $item['CHILDREN'] = array();

        $pointers[rtrim($item['LINK'], '/')] =& $item['CHILDREN'];

        $pointers[dirname($item['LINK'])][] = $item;
    }

    return $tree;
}

function cp_categories_plain2tree(&$resultArray, $plainArray, $parentId)
{
    foreach($plainArray as $item)
    {
        if ((int) $item['IBLOCK_SECTION_ID'] == $parentId)
        {
            $item['CHILDREN'] = array();
            cp_categories_plain2tree($item['CHILDREN'], $plainArray, $item['ID']);
            $resultArray[] = $item;
        }
    }
}

function get_iblock_detail_picture_callback($element)
{
    if ($file = CFile::GetByID($element['DETAIL_PICTURE'])->GetNext())
    {
        $element['DETAIL_IMAGE_SRC'] = CFile::GetFileSRC($file);
    }

    return $element;
}

function get_iblock_preview_picture_callback($element)
{
    if ($file = CFile::GetByID($element['PREVIEW_PICTURE'])->GetNext())
    {
        $element['PREVIEW_IMAGE_SRC'] = CFile::GetFileSRC($file);
    }

    return $element;
}

function getIBlocks($arFilter = array(), $baseURL = null)
{
    $retval = array();

    $res = CIBlock::GetList(
        array(),
        array_merge(array(
            'SITE_ID' => SITE_ID,
            'ACTIVE' => 'Y',
            'CNT_ACTIVE'=>'Y',
            'CHECK_PERMISSIONS' => 'N'
        ), $arFilter), true
    );

    while($iblock = $res->Fetch())
    {
        if ($file = CFile::GetByID($iblock['PICTURE'])->GetNext())
        {
            $iblock['IMAGE_SRC'] = CFile::GetFileSRC($file);
        }

        $retval[] = $iblock;
    }

    return $retval;
}

function getIBlockById($id, $baseURL)
{
    $retval = getIBlocks(array('ID' => $id), $baseURL);
    return empty_array($retval) ? false : $retval[0];
}

function bitrix_404_error()
{
    define('ERROR_404', true);
}

function cp_current_url($removeQuery = false)
{
    global $APPLICATION;

    $url = $APPLICATION->GetCurUri();

    if ($removeQuery)
    {
        $url = preg_replace('/\?.*$/isxU', '', $url);
    }

    return $url;
}

function cp_get_iblocks_by_type($type, $filter = array())
{
    $retval = array();

    $filter['TYPE'] = $type;

    $res = \CIBlock::GetList(null, $filter);

    while($row = $res->Fetch())
    {
        $retval[] = $row;
    }

    return $retval;
}

function cp_is_main()
{
    return cp_current_url(true) == SITE_URL;
}

function cp_get_ib_properties($IBlockId)
{
    static $result = array();
    if(!isset($result[$IBlockId]))
    {
        $rs = CIBlockProperty::GetList(array('sort'=>'asc'), array('IBLOCK_ID'=>$IBlockId));

        while($ar = $rs->Fetch())
        {
//            $result[$IBlockId][$ar['ID']] = $ar;
            $result[$IBlockId][$ar['CODE']] = $ar;
        }
    }

    return $result[$IBlockId];
}

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
 * Фунцкция принимает список инфоблоков \Cpeople\Classes\Block\Object,
 * делает выборку разделов и возвращает список разделов \Cpeople\Classes\Section\Object
 * с полем elements, содержащим инфорблоки
 *
 * @param $iblocks
 * @return array
 */
function cp_group_by_section($iblocks, $level = 0)
{
    if (empty($iblocks) || !is_array($iblocks)) return false;

    $sectionsIds = array();

    $list = new \Cpeople\Classes\Block\Collection($iblocks);

    foreach ($list as $item)
    {
        $sectionsIds[] = $item->iblock_section_id;
    }

    if (empty_array($sectionsIds)) return false;

    $sections = \Cpeople\Classes\Section\Getter::instance()->setFilter(array(
        'ID' => $sectionsIds
    ))->checkPermissions(false)->get();

    foreach ($sections as $section)
    {
        $section->elements = $list->getBy('iblock_section_id', $section->id);
    }

    return $sections;
}

function cp_get_iblock_dates($filter = array())
{
    $dates = array();

    \Cpeople\Classes\Block\Getter::instance()
        ->setOrder(array('DATE_ACTIVE_FROM' => 'DESC'))
        ->setFilter($filter)
        ->setHydrationMode(\Cpeople\Classes\Block\Getter::HYDRATION_MODE_ARRAY)
        ->setSelectFields(array('DATE_ACTIVE_FROM'))
        ->addCallback(function ($element) use(&$dates) {
            $timestamp = strtotime($element['ACTIVE_FROM']);
            $dates[date('Y', $timestamp)][date('n', $timestamp)]++;
        })
        ->get();

    return $dates;
}

function cp_month_name($month, $format)
{
    $timestamp = mktime(1,1,1, $month, 1, date('Y'));
    return date($format, $timestamp);
}

// получить родительские разделы с пользовательскими свойствами
function getStructureUserProps($id, $section=false, $arUf=array()) {
    $id = (int)$id;
    if (!$id) { return false; }
    if ($section) {
        $res = CIBlockSection::GetByID($id);
        if ( $ar = $res->GetNext() ) {
            $res = CIBlockSection::GetList(array(), array('IBLOCK_ID'=>$ar['IBLOCK_ID'], 'ID'=>$ar['ID']), false, $arUf);
        } else { return false; }
    } else {
        $res = CIBlockElement::GetByID($id);
        if ( $ar = $res->GetNext() ) {
            $res = CIBlockSection::GetList(array(), array('IBLOCK_ID'=>$ar['IBLOCK_ID'], 'ID'=>$ar['IBLOCK_SECTION_ID']), false, $arUf);
        } else { return false; }
    }
    // херню подсунули
    if ( !$res->SelectedRowsCount() ) { return false; }
    $arSect = $res->GetNext();
    $arStructure = array();
    $res = CIBlockSection::GetList(array('LEFT_MARGIN'=>'ASC'), array(
        'IBLOCK_ID' => $arSect['IBLOCK_ID'],
        '<=LEFT_BORDER' => $arSect['LEFT_MARGIN'],
        '>=RIGHT_BORDER' => $arSect['RIGHT_MARGIN'],
        '<DEPTH_LEVEL' => $arSect['DEPTH_LEVEL'],
    ), false, $arUf);

    while($ar = $res->GetNext()){
        $arStructure[] = $ar;
    }
    $arStructure[] = $arSect;
    return $arStructure;
}

// получить родительские разделы без пользовательских свойств
function getStructure($id, $section=false) {
    $id = (int)$id;
    if (!$id) { return false; }
    if (!$section) {
        $res = CIBlockElement::GetByID($id);
        if ( $ar = $res->GetNext() ){
            $id = $ar['IBLOCK_SECTION_ID'];
        } else { return false; }
    }
    $res = CIBlockSection::GetNavChain(false, $id);
    $arStructure = array();
    while($ar = $res->GetNext()){
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
function saleCart($param = array(), $update=false) {
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
        $ORDER_PRICE += doubleval($arOneItem['PRICE'])*doubleval($arOneItem['QUANTITY']);
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
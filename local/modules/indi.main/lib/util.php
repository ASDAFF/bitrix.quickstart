<?php
/**
 * Individ module
 *
 * @category    Individ
 * @link        http://individ.ru
 * @revision    $Revision$
 * @date        $Date$
 
 */

namespace Indi\Main;

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
	 * Логгирование в файл папки по датам
	 *
	 * @param        $data - данные для логгирования
	 * @param string $file - название файла без расширения
	 * @param bool   $backtrace
	 */
	public static function saveDateLog($data, $file = '', $backtrace = false)
	{
		if (!$file) {
			$file = 'log';
		}
		//проверяем есть ли раздел logs в корне
		if (!is_dir($_SERVER['DOCUMENT_ROOT'] . (substr($_SERVER['DOCUMENT_ROOT'], -1) == '/' ? '' : '/') . 'logs')) {
			mkdir($_SERVER['DOCUMENT_ROOT'] . (substr($_SERVER['DOCUMENT_ROOT'], -1) == '/' ? '' : '/') . 'logs');
		}

		//проверяем есть ли раздел на текущую дату
		if (!is_dir($_SERVER['DOCUMENT_ROOT'] . (substr($_SERVER['DOCUMENT_ROOT'], -1) == '/' ? '' : '/') . 'logs/' . date('Y-m-d'))) {
			mkdir($_SERVER['DOCUMENT_ROOT'] . (substr($_SERVER['DOCUMENT_ROOT'], -1) == '/' ? '' : '/') . 'logs/' . date('Y-m-d'));
		}

		$file = $_SERVER['DOCUMENT_ROOT'] . (substr($_SERVER['DOCUMENT_ROOT'], -1) == '/' ? '' : '/') . 'logs/' . date("Y-m-d") . '/' . $file . '.log';
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
	 * Метод-агент, удаляющий устаревшие логи
	 * повешать на cron /local/cron/logs.php
	 *
	 * @static cleanExpireLogs
	 */

	public static function cleanExpireLogs()
	{
		// получаем дату за 7 дней до текущей
		$prevWeek = date("Y-m-d", AddToTimeStamp(array("DD" => -7), MakeTimeStamp(date('Y-m-d'), "YYYY-MM-DD")));

		// получаем путь логов за эту дату
		$path = $_SERVER['DOCUMENT_ROOT'] . (substr($_SERVER['DOCUMENT_ROOT'], -1) == '/' ? '' : '/') . 'logs/' . (string)$prevWeek;

		if (file_exists($path) && is_dir($path)) {
			$dirHandle = opendir($path);
			while (false !== ($file = readdir($dirHandle))) {
				if ($file != '.' && $file != '..') {
					$tmpPath = $path . '/' . $file;
					if (file_exists($tmpPath)) {
						unlink($tmpPath);
					}
				}
			}
			closedir($dirHandle);
			rmdir($path);

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
     * Преобразует результат работы компонента bitrix:menu в многоуровневое дерево
     *
     * @param array $items Результат работы компонента bitrix:menu
     * @return array
     */
    public static function menuToTree1($items)
    {
        $tree = array(
            'TEXT' => '[root]',
            'DEPTH_LEVEL' => 0,
            'CHILDREN' => array(),
        );

        self::menuToTreeLevel1($tree, $items);
        return $tree;
    }

    /**
     * Обрабатывает один уровень дерева меню
     *
     * @param array $parent Родительский пункт меню
     * @param array $items Результат работы компонента bitrix:menu
     * @return array
     */
    protected static function menuToTreeLevel1(&$parent, &$items)
    {
        while ($items) {
            $item = array_shift($items);
            $item['CHILDREN'] = array();

            if ($item['DEPTH_LEVEL'] > 1 + $parent['DEPTH_LEVEL']) {
                if ($parent['CHILDREN']) {
                    array_unshift($items, $item);
                    self::menuToTreeLevel1($parent['CHILDREN'][count($parent['CHILDREN']) - 1], $items);
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
     * Вычисляем тип файла
     *
     * @param string $file - абсолютный путь к файлу
     */
    public function typeFile($file)
    {
        $arPath = explode('/', $file);
        $fileName = $arPath[count($arPath) - 1];
        $typeFile = explode('.', $fileName);

        return $typeFile;
    }

    /**
     * excel документ в массив
     *
     * @param string $filename - абсолютный путь к файлу
     */
    public function excelToArray( $filename ){
        // путь к библиотеки от корня сайта
        require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/include/PHPExcel/Classes/PHPExcel.php';
        $result = array();
        // получаем тип файла (xls, xlsx), чтобы правильно его обработать
        $file_type = PHPExcel_IOFactory::identify( $filename );
        // создаем объект для чтения
        $objReader = PHPExcel_IOFactory::createReader( $file_type );
        $objPHPExcel = $objReader->load( $filename ); // загружаем данные файла
        $result = $objPHPExcel->getActiveSheet()->toArray(); // выгружаем данные

        return $result;
    }

    /**
     * каждый день добавление нового курса
     *
     * @param string $CURRENCY - наименование валюты
     */
    public function GetRateFromCBR($CURRENCY)
    {
        global $DB;
        global $APPLICATION;

        \CModule::IncludeModule('currency');
        if(!\CCurrency::GetByID($CURRENCY))
            //такой валюты нет на сайте, агент в этом случае удаляется
            return false;

        $DATE_RATE=date("d.m.Y");//сегодня
        $QUERY_STR = "date_req=".$DB->FormatDate($DATE_RATE, \CLang::GetDateFormat("SHORT", $lang), "D.M.Y");

        //делаем запрос к www.cbr.ru с просьбой отдать курс на нынешнюю дату
        $strQueryText = QueryGetData("www.cbr.ru", 80, "/scripts/XML_daily.asp", $QUERY_STR, $errno, $errstr);

        //получаем XML и конвертируем в кодировку сайта
        $charset = "utf-8";
        if (preg_match("/<"."\?XML[^>]{1,}encoding=[\"']([^>\"']{1,})[\"'][^>]{0,}\?".">/i", $strQueryText, $matches))
        {
            $charset = Trim($matches[1]);
        }
        $strQueryText = eregi_replace("<!DOCTYPE[^>]{1,}>", "", $strQueryText);
        $strQueryText = eregi_replace("<"."\?XML[^>]{1,}\?".">", "", $strQueryText);
        $strQueryText = $APPLICATION->ConvertCharset($strQueryText, $charset, SITE_CHARSET);

        require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/xml.php");

        //парсим XML
        $objXML = new \CDataXML();
        $res = $objXML->LoadString($strQueryText);
        if($res !== false)
            $arData = $objXML->GetArray();
        else
            $arData = false;

        $NEW_RATE=Array();

        //получаем курс нужной валюты $CURRENCY
        if (is_array($arData) && count($arData["ValCurs"]["#"]["Valute"])>0)
        {
            for ($j1 = 0; $j1<count($arData["ValCurs"]["#"]["Valute"]); $j1++)
            {
                if ($arData["ValCurs"]["#"]["Valute"][$j1]["#"]["CharCode"][0]["#"]==$CURRENCY)
                {
                    $NEW_RATE['CURRENCY']=$CURRENCY;
                    $NEW_RATE['RATE_CNT'] = IntVal($arData["ValCurs"]["#"]["Valute"][$j1]["#"]["Nominal"][0]["#"]);
                    $NEW_RATE['RATE'] = DoubleVal(str_replace(",", ".", $arData["ValCurs"]["#"]["Valute"][$j1]["#"]["Value"][0]["#"]));
                    $NEW_RATE['DATE_RATE']=$DATE_RATE;
                    break;
                }
            }
        }

        if ((isset($NEW_RATE['RATE']))&&(isset($NEW_RATE['RATE_CNT'])))
        {

            //курс получили, возможно, курс на нынешнюю дату уже есть на сайте, проверяем
            \CModule::IncludeModule('currency');
            $arFilter = array(
                "CURRENCY" => $NEW_RATE['CURRENCY'],
                "DATE_RATE"=>$NEW_RATE['DATE_RATE']
            );
            $by = "date";
            $order = "desc";

            $db_rate = \CCurrencyRates::GetList($by, $order, $arFilter);
            if(!$ar_rate = $db_rate->Fetch())
                //такого курса нет, создаём курс на нынешнюю дату
                \CCurrencyRates::Add($NEW_RATE);

        }
    }

    public function testAgent($CURRENCY){
        self::log($CURRENCY);
        return '\Indi\Main\Util::testAgent("'.$CURRENCY.'")';
    }


    // Транслитерация строк.
    /**
     * Транслитерация строк.
     *
     * @param string $st - строка
     */
    public function transliterate($string) {
        $converter = array(
            'а' => 'a',   'б' => 'b',   'в' => 'v',
            'г' => 'g',   'д' => 'd',   'е' => 'e',
            'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
            'и' => 'i',   'й' => 'y',   'к' => 'k',
            'л' => 'l',   'м' => 'm',   'н' => 'n',
            'о' => 'o',   'п' => 'p',   'р' => 'r',
            'с' => 's',   'т' => 't',   'у' => 'u',
            'ф' => 'f',   'х' => 'h',   'ц' => 'c',
            'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
            'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
            'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

            'А' => 'A',   'Б' => 'B',   'В' => 'V',
            'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
            'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
            'И' => 'I',   'Й' => 'Y',   'К' => 'K',
            'Л' => 'L',   'М' => 'M',   'Н' => 'N',
            'О' => 'O',   'П' => 'P',   'Р' => 'R',
            'С' => 'S',   'Т' => 'T',   'У' => 'U',
            'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
            'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
            'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
            'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
        );
        return trim(strtr($string, $converter));
    }

    /**
     * Транслитерация строк c пробелами для символьных кодов.
     *
     * @param string $st - строка
     */
    public function transliterateCode($string) {
        $converter = array(
            'а' => 'a',   'б' => 'b',   'в' => 'v',
            'г' => 'g',   'д' => 'd',   'е' => 'e',
            'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
            'и' => 'i',   'й' => 'y',   'к' => 'k',
            'л' => 'l',   'м' => 'm',   'н' => 'n',
            'о' => 'o',   'п' => 'p',   'р' => 'r',
            'с' => 's',   'т' => 't',   'у' => 'u',
            'ф' => 'f',   'х' => 'h',   'ц' => 'c',
            'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
            'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
            'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
            ' ' => '-',

            'А' => 'A',   'Б' => 'B',   'В' => 'V',
            'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
            'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
            'И' => 'I',   'Й' => 'Y',   'К' => 'K',
            'Л' => 'L',   'М' => 'M',   'Н' => 'N',
            'О' => 'O',   'П' => 'P',   'Р' => 'R',
            'С' => 'S',   'Т' => 'T',   'У' => 'U',
            'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
            'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
            'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
            'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
        );
        return trim(strtr($string, $converter));
    }

    /**
     * Удаление не нужных символов
     *
     * @param string $string - строка
     */
    public function cleanSymb($string) {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }

    /**
     * Сортировка категорий в виде дерева
     *
     * @param mixed[] $arCategories
     * 
     * @return $tree
     */
    public function getTree($data) {
        $tree = array();

        foreach ($data as $id => &$node) {
            //Если нет вложений
            if ($node['IBLOCK_SECTION_ID'] == ''){
                $tree[$node['ID']] = &$node;
            }else{
                //Если есть потомки то перебераем массив
                $data[$node['IBLOCK_SECTION_ID']]['childs'][$node['ID']] = &$node;
            }
        }
        return $tree;
    }

    /**
     * Возвращает картинку no-photo
     *
     * @return array
     */
    public static function getNoPhoto()
    {
        return 'images/no_photo.png';
    }
    
    /**
     * Возвращает ссылку на catalog
     *
     * @return array
     */
    public static function getUrlCatalog($url)
    {
        return str_replace('/shop', '/catalog', $url);
    }
}
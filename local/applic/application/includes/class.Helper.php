<?php

class Helper
{
    /**
     * getMailTo
     * Отправитель по-умолчанию
     *
     * @return str
     */
    public static function getMailTo() 
    {
        static $mailTo;
        if (empty($mailTo)) {
            $rsSites = CSite::GetByID(SITE_ID);
            $arSite = $rsSites->Fetch();
            $mailTo = (empty($arSite['EMAIL']) ? DEFAULT_EMAIL_TO : $arSite['EMAIL']);
        }
        return $mailTo;
    }

    /**
     * Проверяет, находимся ли мы на главной странице
     *
     * @return bool
     */
    public static function isMain() 
    {
        return ($_SERVER['PHP_SELF'] == "/index.php");
    }

    /**
     * Возвращает информацию о файле
     *
     * @param int|array $fid ID файла, либо массив ID файлов
     * @return array - данные информация о файле
     */
    public static function getFileData($fid) 
    {
        if (!isset($fid)) return;

        if (is_array($fid)) {
            $rsFile = CFile::GetList(array(), array("@ID" => implode(",", $fid)));
        } else {
            $rsFile = CFile::GetByID($fid);
        }

        $ret = array();

        while ($ifile = $rsFile->Fetch()) {
            $ret[$ifile['ID']] = array(
                "SRC"   => P_UPLOAD . $ifile["SUBDIR"] . "/" . $ifile['FILE_NAME'],
                "WIDTH" => $ifile["WIDTH"],
                "HEIGHT"=> $ifile["HEIGHT"],
                "DATA"  => $ifile
            );
        }

        if (is_array($fid)) {
            return $ret;
        } else {
            return $ret[$fid];
        }
    }

    /**
     * Логирование в файл
     *
     * @param $str
     * @param string $fileName
     */
    public static function logToFile($str, $fileName = "") 
    {
        if (empty($fileName)) {
            $f = fopen(P_LOG_FILE, "a");
        } else {
            $f = fopen(P_LOG_DIR . $fileName, "a");
        }
        fwrite($f, "[" . date("Y.m.d H:i:s") . "] " . $str . "\n");
        fclose($f);
    }

    /**
     *  Возвращает строку, разделенную пробелами по 3 символа, начиная с конца. 
     *
     *  @param string $str
     *  @retrun string
     */
    public static function priceFormat($str) 
    {
        return number_format(floatval($price), 0, '', ' ');
    }

    /**
     * Возвращает транслитерированную строку
     */
    public static function translitIt($str) 
    {
        $tr = array(
            "А"=>"A","Б"=>"B","В"=>"V","Г"=>"G",
            "Д"=>"D","Е"=>"E","Ж"=>"J","З"=>"Z","И"=>"I",
            "Й"=>"Y","К"=>"K","Л"=>"L","М"=>"M","Н"=>"N",
            "О"=>"O","П"=>"P","Р"=>"R","С"=>"S","Т"=>"T",
            "У"=>"U","Ф"=>"F","Х"=>"H","Ц"=>"TS","Ч"=>"CH",
            "Ш"=>"SH","Щ"=>"SCH","Ъ"=>"","Ы"=>"YI","Ь"=>"",
            "Э"=>"E","Ю"=>"YU","Я"=>"YA","а"=>"a","б"=>"b",
            "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"j",
            "з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
            "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
            "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
            "ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y",
            "ы"=>"yi","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya"
        );
        return strtr($str,$tr);
    }

    /**
     * Возвращает true, если идет ajax-запрос
     */
    function isAjax()
    {
        static $isAjax = null;
        
        if ($isAjax === null) {
            $headers = getallheaders();
            
            if ((isset($headers['X-Requested-With']) && $headers['X-Requested-With'] == 'XMLHttpRequest') 
            ||(isset($headers['x-requested-with']) && $headers['x-requested-with'] == 'XMLHttpRequest')) {
                $isAjax = true;
            } else {
                $isAjax = false;
            }
        }
        
        return $isAjax;
    }

    /**
     * Функция, аналогичная CMain::GetCurPageParam, только умеет работать с любой переданной ссылкой и умеет удалять массивы параметров.
     */
    public static function getPageParam($strParam = '', $arParamKill = array(), $get_index_page = NULL, $uri = FALSE)
    {
        if (NULL === $get_index_page) {
            if (defined( 'BX_DISABLE_INDEX_PAGE'))
                $get_index_page = !BX_DISABLE_INDEX_PAGE;
            else
                $get_index_page = TRUE;
        }

        $sUrlPath = GetPagePath( $uri, $get_index_page );
        $strNavQueryString = deleteParam( $arParamKill, $uri );

        if (($strNavQueryString != '') && ($strParam != ''))
            $strNavQueryString = '&'.$strNavQueryString;

        if (($strNavQueryString == '') && ($strParam == ''))
            return $sUrlPath;
        else
            return $sUrlPath.'?'.$strParam.$strNavQueryString;
    }

    /**
     * Вспомогательная функция для удаления массива параметров из ссылки
     * 
     * @param array $arParam
     * @param string|boolean $uri
     * 
     * @return string
     */
    public static function deleteParam($arParam, $uri = FALSE)
    {
       $get = array();
       if ($uri && ($qPos = strpos($uri, '?')) !== FALSE) {
          $queryString = substr( $uri, $qPos + 1 );
          parse_str( $queryString, $get );
          unset( $queryString );
       }

       if (sizeof($get) < 1)
          $get = $_GET;

       if (sizeof($get) < 1)
          return '';

       if (sizeof($arParam) > 0) {
          foreach ($arParam as $param) {
             $search    = &$get;
             $param     = (array)$param;
             $lastIndex = sizeof($param) - 1;
             foreach ($param as $c => $key) {
                if (array_key_exists($key, $search)) {
                   if($c == $lastIndex)
                      unset($search[$key]);
                   else
                      $search = &$search[$key];
                }
             }
          }
       }

       return str_replace(
          array('%5B', '%5D'),
          array('[', ']'),
          http_build_query($get)
       );
    }

    /**
     * Возвращает отформатированную строку с размером файла для загрузки
     *
     * @param int $size
     * @param int $round
     * 
     * @return float
     */
    public static function getStrFileSize($size, $round = 2) 
    {
        $sizes = array('B', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb', 'Eb', 'Zb', 'Yb');
        for ($i=0; $size > 1024 && $i < count($sizes) - 1; $i++) $size /= 1024;
        return round($size,$round)." ".$sizes[$i];
    }

    /**
     * Возвращает правильное окончание слова в зависимости от числа, которому оно сопоставлено
     * 
     * @param int $count
     * @param string $form1
     * @param string $form2_4
     * @param string $form5_0
     * 
     * @return string
     */
    public static function wordEnding($count, $form1 = "", $form2_4 = "а", $form5_0 = "ов") 
    {
        $n100 = $count % 100;
        $n10  = $count % 10;

        if (($n100 > 10) && ($n100 < 21)) {
            return $form5_0;
        } else if ((!$n10) || ($n10 >= 5)) {
            return $form5_0;
        } else if ($n10 == 1) {
            return $form1;
        }

        return $form2_4;
    }

    /**
     * Возвращает массив настроек сайта
     * 
     * @return array
     */
    public static function getSettings()
    {
        
    }

    /**
     * Возвращает настройку сайта по ее символьному коду
     * 
     * @param string $code
     * 
     * @return string|boolean
     */
    public static function getSetting($code)
    {
        
    }

    /**
     * Изменяет размеры картинки и возвращает путь к ней (к измененной картинке)
     * 
     * @param int $id
     * @param int $w
     * @param int $h
     * 
     * @return string|boolean
     */
    public static function getResizeImageSrc($id, $w, $h, $method = BX_RESIZE_IMAGE_PROPORTIONAL)
    {
        $arImg = CFile::ResizeImageGet($id, array('width' => $w, 'height' => $h), $method);
        if ($arImg["src"])
            return $arImg["src"];
        else
            return false;
    }

    /**
     * Зачищает и экранирует строку перед сохранением в бд
     * 
     * @param string $str
     * 
     * @return string
     */
    public static function filterString($str)
    {
        $str = strip_tags($str);
        $str = htmlspecialchars($str);
        $str = mysql_escape_string($str);

        return $str;
    }
}
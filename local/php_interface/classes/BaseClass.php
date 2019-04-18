<?php
/**
 * Created by PhpStorm.
 * User: ASDAFF
 * Date: 02.02.2019
 * Time: 5:40
 */

/**
 * Class BaseClass
 *
 * BaseClass::paginator($page=1,$sizePage=10,$total) - выводит пагинацию в виде:
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
 * BaseClass::getEndWord($count,$text0='товаров',$text1='товар',$text2='товара') - возвращает слово с нужным окончанием
 *
 * $count - кол-во (в данном случае товаров)
 * 0 товаров
 * 1 товар
 * 2 товара
 * BaseClass::getPropOrderVal($order_id, $prop_id, [$or_prop_id=false]) - возвращает значение свойства заказа
 *
 * $order_id - ID заказа
 * $prop_id - ID свойства
 * $or_prop_id - ID альтернативного свойства
 * BaseClass::getResizeImg($idImg,[[[$width=220],$height=220],$width_size=false]) - ресайз фото
 * $idImg - ID фото
 * $width - максимальная ширина
 * $height - максимальная высота
 * $width_size - FALSE - вернуть url, TRUE вернуть массив c url фото, с получившейся шириной, высотой и тп
 * BaseClass::getCountProductsBasket(group=false) - получить общее кол-во товара в корзине
 *
 * false - общее кол-во товара в корзине
 * true - получить кол-во позиций в корзине
 * BaseClass::getSubSections($iblockID,[[[$sect_id=0],$active='Y'],$arSort=array('SORT'=>'ASC'),$arUF=array()]) - получить массив разделов (в коде массив array(...,array('id'=>...,'name'=>...,'url'=>...,'img'=>...),...) )
 *
 * $iblockID - ID инфоблока (обязательно)
 * $sect_id - в каком разделе (по-умолчанию в корне)
 * $active - активность (по-умолчанию только активные)
 * $arSort - сортировка (по-умолчанию array('SORT'=>'ASC'))
 * $arUF - массив пользовательских полей
 * BaseClass::getBasket($id="NULL",$idUser='') - получить содержимое корзины. В коде массив. По-умолчанию текущая корзина.
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
 * BaseClass::checkProductToCart($id) - проверить есть ли товар в корзине. Возвращает true|false
 *
 * BaseClass::getNewCodeSection($iblockID,$name) - создать уникальный символьный код для разделов
 *
 * $iblockID - ID информационного блока
 * $name - имя
 * BaseClass::getNewCodeElement($iblockID,$name) - создать уникальный символьный код для элементов
 *
 * $iblockID - ID информационного блока
 * $name - имя
 * BaseClass::getNews($iblock_id,$page,$count) - получить массив новостей с пагинацией (необходим myCache)
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
 * BaseClass::is_mobile() - проверка на мобильный браузер
 *
 */
class BaseClass
{


    // вывод правильного окончания
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

    // paginator
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

    /****
     * возвращает значение свойства с id  $prop_id в заказе с id $order_id
     * если нет значения свойства, то происходит попытка вернуть свойство с id $or_prop_id, если задан
     * часто приходиться получить свойство в зависимости от типа плательщика
     * если значения свойство не получено, то возвращается false
     *
     * @author Rmld.
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


    // ресайз фото. ID фото в битриксе, макс ширина, макс высота, возвр. ли размеры после ресайза
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

    // получить кол-во товара в корзине
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

    // получить подразделы
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

    // получить содержимое корзины
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

    // проверить есть ли товар в корзине
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

    // получить уникальный символьный код для разделов
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

    // получить уникальный символьный код для элементов
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

    // получить список новостей с пагинацией
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

    // Функция определения мобильных браузеров
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
     *  @param  $excelPath string excel 파일 경로
     *  @param  $csvPath   string csv 파일 경로
     *  @return void
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
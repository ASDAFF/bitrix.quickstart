<?php

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

define('SEARCH_URL', 'https://api.detectum.ru/moshoztorg/search/search_plain');
define('SEARCH_WITH_FILTER', 'https://api.detectum.ru/moshoztorg/search/search');

define('SEARCH_SUGGEST_URL', 'https://api.detectum.ru/moshoztorg/suggest/prefix');

define('SEARCH_ITEMS_COUNT', 16);

error_reporting(E_ALL);
ini_set('display_errors', 1);


define('COOKIE_NAME', "brand_request");


/*

    q      запрос<br>
    offset номер товара, до которого товары в выдаче надо пропустить <br>
    limit  количество товаров в выдаче, 0 - все товары<br>
    order  price_asc, price_desc, id_asc, id_desc - по цене и id, вверх и вниз<br>

*/


if (check_bitrix_sessid()) {

    //восстанавливаем запрос из кук
    if (isset($_POST['autoload']) && isset($_COOKIE[COOKIE_NAME])) {
        $_POST = \Bitrix\Main\Web\Json::decode($_COOKIE[COOKIE_NAME]);
    }

    if (isset($_POST['q'])) {


        $arGetArgs = [
            'limit' => SEARCH_ITEMS_COUNT,
        ];


        if (intval($_POST['offset']) > 0) {
            $arGetArgs['offset'] = intval($_POST['offset']);
        }

        //добавляем сортировку по цене
        if (isset($_POST['order'])) {
            $arGetArgs['order'] = $_POST['order'];
        }

        //удаляем пустую скидку
        if (isset($_POST['discount']) && ($_POST['discount'] == '0')) {
            unset($_POST['discount']);
        }


        //if (isset($_POST['set_filter']) || isset($_POST['discount'])) {

            $arFilterParams = [];

            $iParamsCount = 0;
            foreach ($_POST as $name => $value) {

                if ( ! in_array($name, ['q', 'sessid', 'set_filter', 'category', 'offset', 'order', 'autoload'])) {

                    $value = str_replace('_', ' ', $value);
                    $value = str_replace('+', ' ', $value);

                    $arFilterParams[$name] = $value;
                    $iParamsCount          += count($arFilterParams[$name]);
                }
            }

            //тут забиваем бренд в параметры запроса
            $arFilterParams['vendor'] = $_POST['q'];
            $iParamsCount += 1;

            $arGetArgs['params'] = $iParamsCount;
            $iParamsCounter      = 0;
            foreach ($arFilterParams as $sParamName => $arParamValue) {


                $sParamName = str_replace('_', ' ', $sParamName);

                if (is_array($arParamValue)) {

                    foreach ($arParamValue as $sValue) {
                        $iParamsCounter++;
                        $arGetArgs['param_' . $iParamsCounter] = $sParamName;
                        $arGetArgs['value_' . $iParamsCounter] = $sValue;


                    }

                } else {
                    $iParamsCounter++;
                    $arGetArgs['param_' . $iParamsCounter] = $sParamName;
                    $arGetArgs['value_' . $iParamsCounter] = $arParamValue;

                }

            }

            if (isset($_POST['category'][0])) {
                $arGetArgs['category_id'] = implode(',', $_POST['category']);
            } else {
                $arGetArgs['category_id'] = 'root';
            }


        $resJson = file_get_contents(SEARCH_URL . '?' . http_build_query($arGetArgs));

        if ($resJson && $res = \Bitrix\Main\Web\Json::decode($resJson)) {

            //сохраняяем запрос в куки
            if ( ! isset($_POST['autoload']) || (isset($_POST['form_type']))) {
                setcookie(COOKIE_NAME, \Bitrix\Main\Web\Json::encode($_POST), 0, '/');
            }

            if (($res['type'] == 'redirect') || (( ! isset($res['results']['items']) || empty($res['results']['items'])))) {
                unset($_COOKIE[COOKIE_NAME]);
                setcookie(COOKIE_NAME, "", time() - 3600, "/");
            }


        }


        echo $resJson;


    } else if (isset($_GET['term'])) {

        $arGetArgs = [
            'term' => $_GET['term'],
            //'limit' => SEARCH_ITEMS_COUNT
        ];
        echo file_get_contents(SEARCH_SUGGEST_URL . '?' . http_build_query($arGetArgs));

        //echo SEARCH_SUGGEST_URL . '?' . http_build_query($arGetArgs);

        unset($_COOKIE[COOKIE_NAME]);
        setcookie(COOKIE_NAME, "", time() - 3600, "/");
    }


} else {
    echo 'session error';
}
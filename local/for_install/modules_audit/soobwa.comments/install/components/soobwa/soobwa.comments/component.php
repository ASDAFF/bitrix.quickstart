<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/*
 * Подключаем api модуля
 * */
use Bitrix\Main\Loader;
use \Bitrix\Main\Localization\Loc;

Loc::loadLanguageFile(__FILE__);

global $USER;

if(Loader::includeModule("soobwa.comments")){
    /*
     * Удалить
     * */
    if(htmlspecialchars($_REQUEST['DELETE_COMMENT']) == 'Y' and $USER->IsAdmin()){
        $GLOBALS['APPLICATION']->RestartBuffer();

        \Soobwa\Comments\Api::statusDel(htmlspecialchars($_REQUEST['DELETE_COMMENT_ID']), true);
        echo json_encode(array('SEND'=>'Y', 'RESULT' => 'DELETE'));

        die();
    }

    /*
     * Опрувим
     * */
    if(htmlspecialchars($_REQUEST['ACTIVE_COMMENT']) == 'Y' and $USER->IsAdmin()){
        $GLOBALS['APPLICATION']->RestartBuffer();

        \Soobwa\Comments\Api::statusActive(htmlspecialchars($_REQUEST['ACTIVE_COMMENT_ID']), true);
        echo json_encode(array('SEND'=>'Y', 'RESULT' => 'ACTIVE'));

        die();
    }

    /*
     * Проверка на добовление комментария
     * */
    if(htmlspecialchars($_REQUEST['ADD_COMMENT']) == 'Y') {
        /*
         * TODO: сделать обработку добовления комментария
         * */

        $GLOBALS['APPLICATION']->RestartBuffer();

        /*
         * Определяем ACTIVE
         * */
        if(htmlspecialchars($arParams['MODERATION']) == 'Y'){
            $valActive = false;
        }else{
            $valActive = true;
        }

        /*
         * Формирование массив для добовления комментария
         * */
        $arFields = array(
            'ID_CHAT' => htmlspecialchars($_REQUEST['ID_COMMENTS']),
            'ACTIVE' => $valActive,
            'ID_USER' => htmlspecialchars($_REQUEST['ID_USER']),
            'DATA' => getdate()[0],
            'TEXT' => htmlspecialchars($_REQUEST['TEXT']),
            'DELETE' => false,
        );
        $_REQUEST['addResult'] = \Soobwa\Comments\Api::addElement($arFields);

        if(is_array(htmlspecialchars($_REQUEST['addResult']))){
            echo json_encode(array('SEND'=>'N', 'ERRORS' => htmlspecialchars($_REQUEST['addResult']), 'RESULT' => htmlspecialchars($_REQUEST)));
        }else{
            echo json_encode(array('SEND'=>'Y', 'RESULT' => htmlspecialchars($_REQUEST)));
        }

        die();

    }else{
        /*
         * TODO: сделать кеш!
         * */

        /*
         * Фильтр
         * */
        $filterParam =array(
            'ID_CHAT' => $arParams['ID_CHAT'],
            '!DELETE' => '1'
        );

        /*
         * Переменные
         * */
        $arResult = array();
        $arResult['USERS'] = array();
        $arResult['ITEMS'] = array();

        /*
         * Проверка является ли пользователь адмнистратором
         * */
        if ($USER->IsAdmin()){
            $arResult['USER']['IS_ADMIN'] = 'Y';
        }else{
            $arResult['USER']['IS_ADMIN'] = 'N';
            $filterParam = array_merge($filterParam, array('ACTIVE' => '1'));
        }

        /*
         * Проверка зарегистрирован человек или нет, добовление ID пользователя
         * */
        if($USER->IsAuthorized()){
            $arResult['USER']['IS_AUTHORIZED'] = 'Y';

            /*
             * Получаем ID пользователя
             * */
            $arResult['USER']['ID'] = $USER->GetID();
        }else{
            $arResult['USER']['IS_AUTHORIZED'] = 'N';

            /*
             * Присваиваем ID пользователя
             * */
            $arResult['USER']['ID'] = 0;
        }

        /*
         * ID группы сообщений
         * */
        $arResult['ID_COMMENTS'] = $arParams['ID_CHAT'];

        /*
         * Колличество коментариев
         * */
        $arResult['COUNT_MASSAGE'] = \Soobwa\Comments\Api::getCount($filterParam);

        /*
         * Колличество странниц
         * */
        $arResult['COUNT_PAGES'] = ceil($arResult['COUNT_MASSAGE'] / $arParams['COUNT']);

        /*
         * Собираем все комментарии
         * */

        /*
         * Пагинация
         * */
        $offset = 0;

        if(htmlspecialchars($_REQUEST['GET_COMMENT']) == 'Y'){
            if(htmlspecialchars($_REQUEST['PAGEN']) > 1){
                $offset = $arParams['COUNT'] * (htmlspecialchars($_REQUEST['PAGEN']) - 1);
            }
        }

        $result = \Soobwa\Comments\Api::getList(array('ID', 'ID_CHAT', 'ACTIVE', 'ID_USER', 'DATA', 'TEXT'), $filterParam, array('ID'=>'DESC'), $arParams['COUNT'], $offset);
        while ($res = $result->fetch()) {
            /*
             * Получаем дату
             * */
            $res['FORMAT_DATA'] = date('d.m.Y', $res['DATA']);
            /*
             * Переводим активность в понятный вид (Y/N)
             * */
            if($res['ACTIVE'] == '1'){
                $res['ACTIVE'] = 'Y';
            }else{
                $res['ACTIVE'] = 'N';
            }
            /*
             * Сбор пользователей
             * */
            $arResult['USERS'][$res['ID_USER']]= $res['ID_USER'];

            /*
             * не показываем если не админ не активные элементы
             * */
            if($USER->IsAdmin()){
                $arResult['ITEMS'][] = $res;
            }else{
                if($res['ACTIVE'] == 'Y'){
                    $arResult['ITEMS'][] = $res;
                }
            }
            //$arResult['ITEMS'][] = $res;
        }
        /*
         * Получаем данные о пользователях
         * */
        foreach ($arResult['USERS'] as $keyUser => $valUser){
            $arResult['USERS'][$keyUser] = CUser::GetByID($valUser)->Fetch();
        }
    }
}else{
    echo Loc::getMessage("SOOBWA_COMMENTS_COMPONENT_ERROR_NO_MODULE");
}

$this->IncludeComponentTemplate();
?>

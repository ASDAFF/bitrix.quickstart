<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php';

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if(!CModule::IncludeModule("soobwa.comments")){
    /*
    * Сообщение при отсутствии модуля
    * */
    CAdminMessage::ShowMessage(array(
        "MESSAGE"   =>  Loc::getMessage('SOOBWA_COMMENTS_ADMIN_LIST_ERROR_MODULE'),
        "TYPE"      =>  "ERROR",
    ));
    die();
}
/*
 * Обработка событий
 * */
if(!empty($_REQUEST['event']) and !empty($_REQUEST['id_message'])){
    switch ($_REQUEST['event']){
        case 'delete':
            \Soobwa\Comments\Api::statusDel(htmlspecialchars($_REQUEST['id_message']), true);
            break;
        case 'active':
            \Soobwa\Comments\Api::statusActive(htmlspecialchars($_REQUEST['id_message']), true);
            break;
        case 'unactive':
            \Soobwa\Comments\Api::statusActive(htmlspecialchars($_REQUEST['id_message']), false);
            break;
    }

    /*
     * После выполнения возврашаем пользователя обратно
     * */
    LocalRedirect('soobwa_comments_list.php?lang='.LANGUAGE_ID.'&id_group='.htmlspecialchars($_REQUEST['id_group']));
}

/*
 * Массив с меню
 * */
$aContext = array();
if(!empty($_REQUEST['id_group'])) {
    $aContext[] = array(
        "TEXT" => Loc::getMessage('SOOBWA_COMMENTS_ADMIN_LIST_MENU_BACK_TEXT'),
        "TITLE" => Loc::getMessage('SOOBWA_COMMENTS_ADMIN_LIST_MENU_BACK_TITLE'),
        "LINK" => "soobwa_comments_list.php?lang=" . LANGUAGE_ID,
        "ICON" => "btn_list",
    );
}

/*
 * Показываем меню
 * */
$oMenu = new CAdminContextMenu($aContext);
$oMenu->Show();
 
/*
 * разделяем вывод если выбрана группа
 * */
if(empty($_REQUEST['id_group'])){

    /*
     * Собираем все группы и показываем в списке
     * */
    $sTableID = "groups_list";
    $lAdmin = new CAdminList($sTableID);

    /*
     * Добовляем заголовки
     * */
    $lAdmin->AddHeaders(array(
        array(
            "id"        =>  "ID",
            "content"   =>  Loc::getMessage('SOOBWA_COMMENTS_ADMIN_LIST_TABLE_HEAD_ID'),
            "sort"      =>  "id",
            "default"   =>  true,
        ),
        array(
            "id"        =>  "COUNT_MASSAGE",
            "content"   =>  Loc::getMessage('SOOBWA_COMMENTS_ADMIN_LIST_TABLE_HEAD_COUNT_MASSAGE'),
            "sort"      =>  "count_massage",
            "default"   =>  true,
        ),
        array(
            "id"        =>  "COUNT_MODERATION",
            "content"   =>  Loc::getMessage('SOOBWA_COMMENTS_ADMIN_LIST_TABLE_HEAD_COUNT_MODERATION'),
            "sort"      =>  "count_moderation",
            "default"   =>  true,
        ),
    ));

    /*
     * Получаем группы и колличество комментариев в них
     * */
    $arGroups = array();

    $result = \Soobwa\Comments\Api::getList(array('ID', 'ID_CHAT', 'ACTIVE'), array('!DELETE' => '1'), array('ID_CHAT'=>'ASC'));
    while ($res = $result->fetch()) {

        /*
         * Получаем колличество комментариев
         * */
        if(empty($arGroups[$res['ID_CHAT']])){
            $countMessage = 1;
        }else{
            $countMessage += 1;
        }

        /*
         * Получаем количество не отмодерированных комментариев
         * */
        if(empty($arGroups[$res['ID_CHAT']])){
            if($res['ACTIVE'] != '1'){
                $countModeration = 1;
            }else{
                $countModeration = 0;
            }
        }else{
            if($res['ACTIVE'] != '1'){
                $countModeration = $arGroups[$res['ID_CHAT']]['COUNT_MODERATION'] += 1;
            }
        }

        /*
         * Собираем массив
         * */
        $arGroups[$res['ID_CHAT']] = array(
            'ID' => $res['ID_CHAT'],
            'COUNT_MASSAGE' => $countMessage,
            'COUNT_MODERATION' => $countModeration,
        );
    }

    /*
     * Формируем строки таблицы
     * */
    foreach ($arGroups as $keyGroup => $arGroup) {

        $row =& $lAdmin->AddRow($keyGroup);

        /*
         * Добовляем сам текст
         * */
        foreach ($arGroup as $keyField => $valField){
            if($keyField == 'ID'){
                $row->AddViewField($keyField, '<a href="?lang='.LANGUAGE_ID.'&id_group='.htmlspecialchars($valField).'">'.$valField.'</a>');
            }else{
                $row->AddViewField($keyField, $valField);
            }
        }

        /*
         * Добовляем контекстное меню
         * */
        $arActions = Array();

        $arActions[] = array(
            "ICON"      =>  "edit",
            "DEFAULT"   =>  true,
            "TEXT"      =>  Loc::getMessage('SOOBWA_COMMENTS_ADMIN_LIST_ACTION_EDIT_TEXT'),
            "ACTION"    =>  $lAdmin->ActionRedirect('?lang='.LANGUAGE_ID.'&id_group='.htmlspecialchars($keyGroup))
        );

        $row->AddActions($arActions);
    }

    /*
     * Выводим группы
     * */
    $lAdmin->Display();
}else{

    /*
     * Собираем все группы и показываем в списке
     * */
    $sTableID = "groups_list";
    $lAdmin = new CAdminList($sTableID);

    /*
     * Добовляем заголовки
     * */
    $lAdmin->AddHeaders(array(
        array(
            "id"        =>  "ID",
            "content"   =>  Loc::getMessage('SOOBWA_COMMENTS_ADMIN_LIST_TABLE_HEAD_ID'),
            "sort"      =>  "id",
            "default"   =>  true,
        ),
        array(
            "id"        =>  "ID_CHAT",
            "content"   =>  Loc::getMessage('SOOBWA_COMMENTS_ADMIN_LIST_TABLE_HEAD_ID_CHAT'),
            "sort"      =>  "id_chat",
            "align"     =>  "center",
            "default"   =>  true,
        ),
        array(
            "id"        =>  "ACTIVE",
            "content"   =>  Loc::getMessage('SOOBWA_COMMENTS_ADMIN_LIST_TABLE_HEAD_ACTIVE'),
            "sort"      =>  "active",
            "default"   =>  true,
        ),
        array(
            "id"        =>  "ID_USER",
            "content"   =>  Loc::getMessage('SOOBWA_COMMENTS_ADMIN_LIST_TABLE_HEAD_ID_USER'),
            "sort"      =>  "id_user",
            "default"   =>  true,
        ),
        array(
            "id"        =>  "DATA",
            "content"   =>  Loc::getMessage('SOOBWA_COMMENTS_ADMIN_LIST_TABLE_HEAD_DATA'),
            "sort"      =>  "data",
            "align"     =>  "center",
            "default"   =>  true,
        ),
        array(
            "id"        =>  "TEXT",
            "content"   =>  Loc::getMessage('SOOBWA_COMMENTS_ADMIN_LIST_TABLE_HEAD_TEXT'),
            "sort"      =>  "text",
            "default"   =>  true,
        ),
    ));

    /*
     * Получаем сообщения группы
     * */
    $arMessages = array();

    $result = \Soobwa\Comments\Api::getList(
        array('ID', 'ID_CHAT', 'ACTIVE', 'ID_USER', 'DATA', 'TEXT'),
        array('ID_CHAT'=>htmlspecialchars($_REQUEST['id_group']), '!DELETE' => '1'),
        array('ID'=>'ASC')
    );

    while ($res = $result->fetch()) {
        $arMessages[$res['ID']] = $res;
    }

    /*
     * Формируем строки таблицы
     * */
    foreach ($arMessages as $keyMessage => $arMessage) {

        $row =& $lAdmin->AddRow($keyGMessage);

        /*
         * Добовляем сам текст
         * */
        foreach ($arMessage as $keyField => $valField){

            switch ($keyField){
                case 'ID':
                    $row->AddViewField($keyField, $valField);
                    break;
                case 'ACTIVE':
                    $active = '';
                    if($valField == '1'){
                        $active = '<sapn style="color: green;">'.Loc::getMessage('SOOBWA_COMMENTS_ADMIN_LIST_TABLE_HEAD_ACTIVE_ACTIVE').'</sapn>';
                    }else{
                        $active = '<sapn style="color: red;">'.Loc::getMessage('SOOBWA_COMMENTS_ADMIN_LIST_TABLE_HEAD_ACTIVE_NO_ACTIVE').'</sapn>';
                    }
                    $row->AddViewField($keyField, $active);
                    break;
                case 'ID_USER':
                    /*
                     * Получаем данные пользовател
                     * */
                    if($valField == 0){
                        $html = Loc::getMessage('SOOBWA_COMMENTS_ADMIN_LIST_MASSAGE_USER_NO_AUTH');
                    }else {
                        $rsUser = CUser::GetByID($valField);
                        $arUser = $rsUser->Fetch();

                        if ($arUser['PERSONAL_PHOTO'] > 0) {
                            $file = array();
                            $file = CFile::ResizeImageGet($arUser['PERSONAL_PHOTO'], array('width' => 50, 'height' => 50), BX_RESIZE_IMAGE_PROPORTIONAL, true);
                            $html = '<a href="user_edit.php?lang=' . LANGUAGE_ID . '&ID=' . $valField . '"><img style="border-radius: 50%; border: rgb(5, 181, 255) 2px solid;" src="' . $file['src'] . '" alt="' . $arUser['LAST_NAME'] . $arUser['NAME'] . '"></a>';
                        } else {
                            $html = '<a href="user_edit.php?lang=' . LANGUAGE_ID . '&ID=' . $valField . '">' . $arUser['LAST_NAME'] .' '. $arUser['NAME'] . '</a>';
                        }
                    }
                    $row->AddViewField($keyField, $html);
                    break;
                case 'DATA':
                    $row->AddViewField($keyField, date('d.m.Y', $valField));
                    break;
                default:
                    $row->AddViewField($keyField, $valField);
            }
        }

        /*
         * Добовляем контекстное меню
         * */
        $arActions = Array();

        if($arMessage['ACTIVE'] == '1'){
            $arActions[] = array(
                "ICON"      =>  "",
                "DEFAULT"   =>  false,
                "TEXT"      =>  Loc::getMessage('SOOBWA_COMMENTS_ADMIN_LIST_ACTION_UNACTIVE_TEXT'),
                "ACTION"    =>  $lAdmin->ActionRedirect('?lang='.LANGUAGE_ID.'&id_group='.htmlspecialchars($_REQUEST['id_group']).'&id_message='.$keyMessage.'&event=unactive')
            );
        }else {
            $arActions[] = array(
                "ICON" => "",
                "DEFAULT" => false,
                "TEXT" => Loc::getMessage('SOOBWA_COMMENTS_ADMIN_LIST_ACTION_ACTIVE_TEXT'),
                "ACTION"    =>  $lAdmin->ActionRedirect('?lang='.LANGUAGE_ID.'&id_group='.htmlspecialchars($_REQUEST['id_group']).'&id_message='.$keyMessage.'&event=active')
            );
        }

        $arActions[] = array(
            "ICON"      =>  "delete",
            "DEFAULT"   =>  false,
            "TEXT" => Loc::getMessage('SOOBWA_COMMENTS_ADMIN_LIST_ACTION_DELETE_TEXT'),
            "ACTION"    =>  $lAdmin->ActionRedirect('?lang='.LANGUAGE_ID.'&id_group='.htmlspecialchars($_REQUEST['id_group']).'&id_message='.$keyMessage.'&event=delete')
        );

        $row->AddActions($arActions);
    }

    /*
     * Выводим сообщения
     * */
    $lAdmin->Display();
}

require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php';

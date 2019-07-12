<?php
/**
 * аjax файл который обрабатываем данные для сео
 */
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
__IncludeLang($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/include/lang/ru/seo_element_edit.php");


global $USER;
$userID = $USER->GetID();

$validGroup = array(1);
$filter = Array("STRING_ID" => "sale_administrator");
$rsGroups = CGroup::GetList(($by = "c_sort"), ($order = "desc"), $filter); // выбираем группы

if ($arGroup = $rsGroups->Fetch()) {
    $validGroup[] = $arGroup["ID"];
}

$arGroups = CUser::GetUserGroup($userID);

$accessDenied = true;
foreach ($arGroups as $groupID) {
    if (in_array($groupID, $validGroup)) {
        $accessDenied = false;
        break;
    }
}
//доступ разрешен только для админов и админов интернет магазина
if ($accessDenied == true) {
    exit('ACCESS DENIED');
}

if (!empty($_REQUEST['action'])) {
    CModule::IncludeModule("iblock");
    if (!CModule::IncludeModule("highloadblock")) {
        exit('Module highloadblock not installed.');
    }
    if (strtolower(LANG_CHARSET) == "windows-1251") {
        //$siteUTF8 = false;
        // конвертим реквест чтоб не было кракозябр
        foreach ($_REQUEST as $key => $item) {

            if (!empty($_REQUEST[$key])) $_REQUEST[$key] = iconv('UTF-8', 'windows-1251', $_REQUEST[$key]);

        }
    }
    //deb($_REQUEST);
}


if ($_REQUEST['action'] == 'add' && !empty($_REQUEST['old_url'])) {
    // add seo url
    if (!check_bitrix_sessid('sessid')) die("session error");

    $curUri = $_REQUEST['old_url'];
    // add seo record

    $parameters = array("filter" => array("=NAME" => "SeoReference"));
    $hlblock = Bitrix\Highloadblock\HighloadBlockTable::getList($parameters)->fetch();

    if (!empty($hlblock)) {
        $entity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();
        // pagination
        $limit = array('nPageSize' => 1, 'iNumPage' => 1);

        // sort
        $sort_id = 'ID';
        $sort_type = 'DESC';

        // execute query
        $main_query = new Bitrix\Main\Entity\Query($entity);
        $main_query->setSelect(array('ID', 'UF_NAME'));
        $main_query->setFilter(array('UF_NAME' => $curUri));
        $main_query->setOrder(array($sort_id => $sort_type));
        $main_query->setLimit($limit['nPageSize']);

        $result = $main_query->exec();
        $result = new CDBResult($result);

        if ($arElement = $result->Fetch()) {
            // record exists
            //deb($arElement);
        } else {
            //add record
            // у результата добавления тип Bitrix\Main\Entity\AddResult

            $fields = array(
                'UF_NAME' => $curUri,
                'UF_NEW_URL' => $_REQUEST["new_url"],
                'UF_TITLE' => $_REQUEST["title"],
                'UF_KEYWORDS' => $_REQUEST["keywords"],
                'UF_DESCRIPTION' => $_REQUEST["description"],
                'UF_SITE_ID' => SITE_ID,
                'UF_SEO_TEXT' => $_REQUEST["seo_text"]
            );

            if (!$fields["UF_SITE_ID"]) exit("undefined SITE_ID");

            $result = $entity_data_class::add($fields);

            if ($result->isSuccess()) {

                echo $result->getId();

            } else {
                //echo 'ERROR ADDED ' . implode(', ', $result->getErrors()) . "<br />";
            }
        }
    }
}

if ($_REQUEST['action'] == 'updateUrlsFile') {
    // обновляем урлы
    Novagroup_Classes_General_Main::updateSeoUrls();
    die('1');
}

if ($_REQUEST['action'] == 'update' && !empty($_REQUEST['old_url'])) {

    if (!check_bitrix_sessid('sessid')) die("session error");

    $curUri = $_REQUEST['old_url'];
    // update seo record
    $parameters = array("filter" => array("=NAME" => "SeoReference"));
    $hlblock = Bitrix\Highloadblock\HighloadBlockTable::getList($parameters)->fetch();

    if (!empty($hlblock)) {
        $entity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();
        // pagination
        $limit = array('nPageSize' => 1, 'iNumPage' => 1);

        // sort
        $sort_id = 'ID';
        $sort_type = 'DESC';

        // execute query
        $main_query = new Bitrix\Main\Entity\Query($entity);
        $main_query->setSelect(array('ID', 'UF_NAME'));
        $main_query->setFilter(array('UF_NAME' => $curUri));
        $main_query->setOrder(array($sort_id => $sort_type));
        $main_query->setLimit($limit['nPageSize']);

        $result = $main_query->exec();
        $result = new CDBResult($result);

        if ($arElement = $result->Fetch()) {
            
            $fields = array(
                'UF_NAME' => $curUri,
                'UF_NEW_URL' => $_REQUEST["new_url"],
                'UF_TITLE' => $_REQUEST["title"],
                'UF_KEYWORDS' => $_REQUEST["keywords"],
                'UF_DESCRIPTION' => $_REQUEST["description"],
                //'UF_SITE_ID' => SITE_ID,
                'UF_SEO_TEXT' => $_REQUEST["seo_text"]
            );

            //if (!$fields["UF_SITE_ID"]) exit("undefined SITE_ID");

            $result = $entity_data_class::update($arElement["ID"], $fields);

            if ($result->isSuccess()) {
                echo $arElement["ID"];

            } else {
                echo 'ERROR UPDATED ' . implode(', ', $result->getErrors()) . "<br />";
            }
        }
    }
}

if ($_REQUEST['action'] == 'content' && !empty($_REQUEST['uri'])) {
    // show new form
    $rewriteMessageFlag = false;

    $curUri = $_REQUEST['uri'];

    $parameters = array("filter" => array("=NAME" => "SeoReference"));
    $hlblock = Bitrix\Highloadblock\HighloadBlockTable::getList($parameters)->fetch();
    $entity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
    $entity_data_class = $entity->getDataClass();
    // pagination
    $limit = array('nPageSize' => 1, 'iNumPage' => 1);

    // sort
    $sort_id = 'ID';
    $sort_type = 'DESC';

    // execute query
    $main_query = new Bitrix\Main\Entity\Query($entity);
    $main_query->setSelect(
        array('ID', 'UF_NAME', 'UF_NEW_URL', 'UF_TITLE', 'UF_KEYWORDS', 'UF_DESCRIPTION', 'UF_SITE_ID', 'UF_SEO_TEXT')
    );
    $arFilter = array('IBLOCK_ID' => $iblockId, array("NAME" => $curUri, "PROPERTY_NEW_URL" => $curUri, "LOGIC" => "OR"));

    $main_query->setFilter(
        array('UF_NAME' => $curUri, "UF_NEW_URL" => $curUri, "LOGIC" => "OR")
    );
    $main_query->setOrder(array($sort_id => $sort_type));
    $main_query->setLimit($limit['nPageSize']);

    $result = $main_query->exec();
    $result = new CDBResult($result);
    $resArr = array();
    if ($arElement = $result->Fetch()) {

        $resArr['TITLE'] = $arElement['UF_TITLE'];
        $resArr['KEYWORDS'] = $arElement['UF_KEYWORDS'];
        $resArr['DESCRIPTION'] = $arElement['UF_DESCRIPTION'];
        $resArr['DETAIL_TEXT'] = $arElement['UF_SEO_TEXT'];
        $resArr['NEW_URL'] = $arElement['UF_NEW_URL'];
        $resArr['OLD_URL'] = $arElement['UF_NAME'];
        $resArr['ID'] = $arElement['ID'];
        $resArr['SITE_ID'] = $arElement['UF_SITE_ID'];
        $adminLink = "/bitrix/admin/highloadblock_row_edit.php?ENTITY_ID=". $hlblock['ID'] . "&ID=" . $arElement['ID'] ;
        $action = "update";
    } else {
        $adminLink = "/bitrix/admin/highloadblock_rows_list.php?ENTITY_ID=". $hlblock['ID'] . "&lang=ru";
        $action = "add";
        $resArr['OLD_URL'] = $curUri;
    }

    ?>
    <style>
        .seo-table { margin-bottom: 15px; }
        .seo-table td { padding: 5px; }
        .suc { color: green !important; padding-top: 15px; }
        .err { color: red !important; padding-top: 15px; }
    </style>
    <div class="seo_ajax">
        <form id="formSeo">
            <?= bitrix_sessid_post('sessid') ?>
            <input type="hidden" name="action" value="<?= $action ?>" id="action">
            <input type="hidden" name="recordId" value="<?= $resArr['ID'] ?>">
            <input type="hidden" name="siteId" value="<?= $resArr['SITE_ID'] ?>">
            <table class="seo-table" cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td><?= GetMessage("SEO_URLS_ODL_URL") ?></td>
                    <td>
                        <input type="text" name="old_url" id="old_url" value="<?= $resArr['OLD_URL'] ?>">
                    </td>
                </tr>
                <tr>
                    <td><?= GetMessage("SEO_URLS_NEW_URL") ?></td>
                    <td>
                        <input type="text" name="new_url" id="new_url" value="<?= $resArr['NEW_URL'] ?>">
                    </td>
                </tr>

            </table>

            <table class="seo-table" cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td>Meta title</td>
                    <td>
                        <input type="text" name="title" value="<?= $resArr['TITLE'] ?>">
                    </td>
                </tr>
                <tr>
                    <td>Meta keywords</td>
                    <td>
                        <input type="text" name="keywords" value="<?= $resArr['KEYWORDS'] ?>">
                    </td>
                </tr>
                <tr>
                    <td>Meta description</td>
                    <td>
                        <input type="text" name="description" value="<?= $resArr['DESCRIPTION'] ?>">
                    </td>
                </tr>
                <tr>
                    <td><?= GetMessage("SEO_URLS_SEO_TEXT") ?></td>
                    <td>
                        <textarea name="seo_text" cols="40" rows="5"><?= $resArr['DETAIL_TEXT'] ?></textarea>
                    </td>
                </tr>
            </table>
            <div>
                <a target="_blank" href="<?= $adminLink ?>"><?= GetMessage("SEO_URLS_ADMIN_LINK") ?></a>
            </div>
        </form>
    </div>
    <div id="okDiv" style="display:none" class="suc">
        <?= GetMessage("SEO_URLS_UPDATE_OK_LABEL") ?>
    </div>
    <?php
}
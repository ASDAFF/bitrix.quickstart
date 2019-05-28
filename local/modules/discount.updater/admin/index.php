<?php
set_time_limit(0);
ob_implicit_flush(1);

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');

global $APPLICATION;

$DiscountFileName = $_SERVER["DOCUMENT_ROOT"] . '/discount_cards.xml';

$arError = [];

$APPLICATION->SetTitle("Обновление дисконтных карт");

require_once($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/main/include/prolog_admin_after.php');

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/lang/ru/admin/event_log.php");

CModule::IncludeModule("main");


if ($REQUEST_METHOD == "POST" && check_bitrix_sessid()) {

    if ( ! move_uploaded_file($_FILES['discount_xml']['tmp_name'], $DiscountFileName)) {
        $arError[] = 'Не удалось скопировать загруженный файл';
    } else {
        \CEventLog::Add(array(
            "SEVERITY"      => "INFO",
            "AUDIT_TYPE_ID" => "DEBUG",
            "MODULE_ID"     => "discount.updater",
            "ITEM_ID"       => "UPDATE",
            "DESCRIPTION"   => "Файл загружен",
        ));
    }
}


if ($arError) {

    CAdminMessage::ShowMessage(array(
        "MESSAGE" => "Ошибка",
        "DETAILS" => implode('<br>', $arError),
        "HTML"    => true,
        "TYPE"    => "ERROR",
    ));
}

if (file_exists($DiscountFileName . '.lock')) {

    CAdminMessage::ShowMessage(array(
        "MESSAGE" => "Внимание",
        "DETAILS" => "Идет обновление данных дисконтных карт. Дождитесь окончания процесса.",
        "HTML"    => true,
        "TYPE"    => "OK",
    ));


} else {


    if (file_exists($DiscountFileName)) {

        CAdminMessage::ShowMessage(array(
            "MESSAGE" => "Внимание",
            "DETAILS" => "Найден файл <a href='../../discount_cards.xml' target='_blank'>discount_cards.xml</a>.<br> Обновление начнется автоматически в течении 5 минут.",
            "HTML"    => true,
            "TYPE"    => "OK",
        ));

    }

    ?>

    <form method="post" enctype="multipart/form-data" action="<? echo $APPLICATION->GetCurPage() ?>">

        <?
        $name = 'discount_xml';
        $val  = '';

        ?>

        <fieldset>
            <legend>Загрузить файл</legend>
            <br>
            <table>
                <tr>
                    <td width="40%" nowrap>
                        <label for="<?= $name ?>">Дисконтные карты (xml файл):</label>
                    <td width="60%">

                        <? echo CFile::InputFile($name, 20, $val); ?>

                    </td>
                </tr>
            </table>

            <br>
            <input type="submit" name="Update" value="<?= GetMessage("MAIN_SAVE") ?>"
                   title="<?= GetMessage("MAIN_OPT_SAVE_TITLE") ?>" class="adm-btn-save">

            <?= bitrix_sessid_post(); ?>

        </fieldset>

    </form>

    <br><br>

    <?

}

?>

    <h1 class="adm-title">Журнал событий</h1>

<?

$arFilter    = array(
    'MODULE_ID' => "discount.updater",
);
$arNavParams = array("nPageSize" => 30);

$rsData = CEventLog::GetList(array('ID' => 'DESC'), $arFilter, []);

$sTableID = "tbl_event_log";
$oSort    = new CAdminSorting($sTableID, "ID", "DESC");
$lAdmin   = new CAdminList($sTableID, $oSort);
$rsData   = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();

$arHeaders = array(
    array(
        "id"      => "TIMESTAMP_X",
        "content" => "Время",
        "sort"    => "TIMESTAMP_X",
        "default" => true,
        "align"   => "right",
    ),
    array(
        "id"      => "DESCRIPTION",
        "content" => "Описание",
        "default" => true,
    ),
);
$lAdmin->AddHeaders($arHeaders);
while ($db_res = $rsData->NavNext(true, "a_")) {
    $row =& $lAdmin->AddRow($a_ID, $db_res);
}

$lAdmin->DisplayList();

require_once($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/include/epilog_admin.php");
?>
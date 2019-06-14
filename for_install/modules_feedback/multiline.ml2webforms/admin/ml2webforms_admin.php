<?php
define("ADMIN_MODULE_NAME", "multiline.ml2webforms");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile(__DIR__.'/menu.php');

/** @var CMain $APPLICATION */
$module_right = $APPLICATION->GetGroupRight(ADMIN_MODULE_NAME);
if ($module_right == "D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

if (!\CModule::IncludeModule(ADMIN_MODULE_NAME))
{
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

use \Bitrix\Main\Localization\Loc;
use \Ml2WebForms\Ml2WebFormsEntity;
use \Ml2WebForms\MlAdminPanelBuilder;

$APPLICATION->SetTitle(Loc::getMessage('ml2webforms_list_title'));

$sTableID = 'ml2webforms_list';
$oSort = new \CAdminSorting($sTableID, "id", "asc" );
$lAdmin = new \CAdminList($sTableID, $oSort);

$by = $_REQUEST['by'] ? $_REQUEST['by'] : 'id';
$order = in_array($_REQUEST['order'], array('asc', 'desc')) ? $_REQUEST['order'] : 'asc';

$arForms = Ml2WebFormsEntity::getFormsList();

$arHeaders = Array(
    Array(
        "id" => "NAME",
        "content" => Loc::getMessage("ml2webforms_field_NAME"),
        "sort" => false,
        "default" => true
    ),
    Array(
        "id" => "ID",
        "content" => Loc::getMessage("ml2webforms_field_ID"),
        "sort" => false,
        "default" => true
    ),
);

$lAdmin->AddHeaders($arHeaders);

foreach ($arForms as &$form) {
    $row = &$lAdmin->AddRow($form['ID'], $form);
    $row->AddViewField(
        'NAME',
        MlAdminPanelBuilder::PrepareListHtml(
            $form['NAME'],
            'input_text',
            "ml2webforms_results.php?id={$form['ID']}&lang=" . LANG
        )
    );
    $row->AddViewField(
        'ID',
        MlAdminPanelBuilder::PrepareListHtml(
            $form['ID'],
            'input_text'
        )
    );
    $arActions = Array();

    $arActions[] = array(
        "ICON" => "view",
        "DEFAULT" => "Y",
        "TEXT" => Loc::getMessage("ml2webforms_results"),
        "ACTION" => $lAdmin->ActionRedirect("ml2webforms_results.php?id={$form['ID']}&lang=" . LANG),
    );

    $arActions[] = array( "SEPARATOR" => TRUE );

    $arActions[] = array(
        "ICON" => "edit",
        "DEFAULT" => "Y",
        "TEXT" => Loc::getMessage("ml2webforms_edit"),
        "ACTION" => $lAdmin->ActionRedirect("ml2webforms_edit.php?id={$form['ID']}&lang=" . LANG),
    );

    $row->AddActions($arActions);
}

$lAdmin->AddFooter(
    array(
        array(
            "title" => Loc::getMessage("MAIN_ADMIN_LIST_SELECTED"),
            "value" => count($arForms)
        ),
        array(
            "counter" => true,
            "title" => Loc::getMessage("MAIN_ADMIN_LIST_CHECKED"),
            "value" => "0"
        ),
    )
);

$aContext = array(
    array(
        "ICON" => "btn_new",
        "TEXT" => Loc::getMessage("ml2webforms_add"),
        "LINK" => "ml2webforms_edit.php?lang=" . LANG,
        "TITLE" => Loc::getMessage("ml2webforms_add"),
    ),
);

$lAdmin->AddAdminContextMenu($aContext);

$lAdmin->CheckListMode();

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

$lAdmin->DisplayList();

if ($note = Loc::getMessage("ml2webforms_list_note")) {
    echo BeginNote();
    echo $note;
    echo EndNote();
}


require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");

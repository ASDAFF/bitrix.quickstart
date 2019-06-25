<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?if (!CModule::IncludeModule('intec.startshop')) return;?>
<?
    $arForms = array();
    $dbForms = CStartShopForm::GetList();

    while ($arForm = $dbForms->Fetch())
        $arForms[$arForm['ID']] = '['.$arForm['ID'].'] '.(!empty($arForm['LANG'][LANGUAGE_ID]['NAME']) ? $arForm['LANG'][LANGUAGE_ID]['NAME'] : $arForm['CODE']);

    $arComponentParameters = array(
        "PARAMETERS" => array(
            "FORM_ID" => array(
                "PARENT" => "BASE",
                "NAME" => GetMessage("SFRN_FORM_ID"),
                "TYPE" => "LIST",
                "VALUES" => $arForms,
                "ADDITIONAL_VALUES" => "Y"
            ),
            "REQUEST_VARIABLE_ACTION" => array(
                "NAME" => GetMessage("SFRN_REQUEST_VARIABLE_ACTION"),
                "TYPE" => "STRING",
                "DEFAULT" => "action"
            ),
            "FORM_VARIABLE_CAPTCHA_SID" => array(
                "NAME" => GetMessage("SFRN_FORM_VARIABLE_CAPTCHA_SID"),
                "TYPE" => "STRING",
                "DEFAULT" => "CAPTCHA_SID"
            ),
            "FORM_VARIABLE_CAPTCHA_CODE" => array(
                "NAME" => GetMessage("SFRN_FORM_VARIABLE_CAPTCHA_CODE"),
                "TYPE" => "STRING",
                "DEFAULT" => "CAPTCHA_CODE"
            ),
            "AJAX_MODE" => array()
        )
    );
?>
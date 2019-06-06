<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if(!\CModule::IncludeModule("multiline.ml2webforms")) {
	return; 
}
$forms_list = \Ml2WebForms\Ml2WebFormsEntity::getFormsList();
$forms_list_short = array ();
foreach ($forms_list as $form) {
	$forms_list_short[$form["ID"]] = $form["NAME"];
}

$arComponentParameters = array(
    "PARAMETERS" => array(
        "ID" => array(
            "NAME" => GetMessage("ML2WEBFORMS_ID_MESSAGE"),
            "TYPE" => "LIST",
            "MULTIPLE" => "N",
            "VALUES" => $forms_list_short,
            "PARENT" => "BASE",
            "DEFAULT" => ""
        )
    ),
);
?>

<?
global $MESS, $APPLICATION;

IncludeModuleLangFile(__FILE__);

$arClassesList = array(

);

if (method_exists(CModule, "AddAutoloadClasses")) {
        CModule::AddAutoloadClasses(
                "altasib.qrcode",
                $arClassesList
        );
} else {
        foreach ($arClassesList as $sClassName => $sClassFile) {
                require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.qrcode/".$sClassFile);
        }
}
?>
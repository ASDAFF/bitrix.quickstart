<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/ws.projectsettings/include.php");
__IncludeLang(dirname(__FILE__).'/lang/'.LANGUAGE_ID.'.php');
$moduleName = "ws.projectsettings";
define("ADMIN_MODULE_NAME", $moduleName);
define("ADMIN_MODULE_ICON", "");

$pathAdditionalLang = BX_ROOT.'/modules/'.$moduleName.'/js'; //for bitrix 16.0 or higher
$pathLang = BX_ROOT.'/modules/'.$moduleName.'/lang/'.LANGUAGE_ID.'/js'; //for bitrix < 16.0
$pathModuleJsFolder = '/bitrix/js/'.$moduleName.'/';
foreach (array(
    'ws_progectssettings_main' => array(
        'js' => $pathModuleJsFolder.'main.js',
        'lang' => $pathLang.'.php',
        'lang_additional' => $pathAdditionalLang.'.php',
        'skip_core' => true,
    ),
    'ws_progectssettings_fields' => array(
        'js' => $pathModuleJsFolder . 'fields.js',
        'skip_core' => false,
        'css' => $pathModuleJsFolder . 'fields.css',
        'rel' => array('ws_progectssettings_main', 'jquery')
    )
) as $ext => $arExt) {
    CJSCore::RegisterExt($ext, $arExt);
}
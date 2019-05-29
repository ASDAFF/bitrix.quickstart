<?php

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

global $ID;
$ID = intval($ID);
$moduleId =  'acrit.exportpro';
$POST_RIGHT = $APPLICATION->GetGroupRight($moduleId);

if($POST_RIGHT >= 'R')
{
    CModule::IncludeModule($moduleId);
    $acritExport = new CAcritExportproExport($ID);
    $acritExport->Export();
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
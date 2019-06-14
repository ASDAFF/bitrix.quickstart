<?php
require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php" );

global $ID;
$ID = intval( $ID );
$moduleId =  "acrit.exportpro";
$POST_RIGHT = $APPLICATION->GetGroupRight( $moduleId );

if( $POST_RIGHT >= "R" ){
    CModule::IncludeModule( $moduleId );
    $acritExport = new CAcritExportproExport( $ID );
    $acritExport->Export();
}

require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php" );
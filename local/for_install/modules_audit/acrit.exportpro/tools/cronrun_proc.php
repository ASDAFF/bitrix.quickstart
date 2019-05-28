<?
require_once( dirname(__DIR__)."/classes/general/threads.php" );
set_time_limit(0);

if( $params = Threads::getParams() ){
    $_SERVER["DOCUMENT_ROOT"] = $DOCUMENT_ROOT = $params["documentRoot"];
    require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php" );
    
    Cmodule::IncludeModule( "acrit.exportpro" );
    $export = new CAcritExportproExport( intval( $params["profileId"] ) );
    $export->Export( "cron", $params["cronPage"] );
}
?>
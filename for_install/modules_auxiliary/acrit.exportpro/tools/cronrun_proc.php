<?
if( defined( "NO_KEEP_STATISTIC" ) )
    define( "NO_KEEP_STATISTIC", true );

if( !defined( "NOT_CHECK_PERMISSIONS" ) )
    define( "NOT_CHECK_PERMISSIONS", true );

@set_time_limit(0);

require_once( dirname(__DIR__)."/classes/general/threads.php" );

if( $params = Threads::getParams() ){    
    $_SERVER["DOCUMENT_ROOT"] = $DOCUMENT_ROOT = $params["documentRoot"];
    require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php" );
    Cmodule::IncludeModule( "acrit.exportpro" );
    $export = new CAcritExportproExport( intval( $params["profileId"] ) );
    $export->Export( "cron", $params["cronPage"] );
}
?>
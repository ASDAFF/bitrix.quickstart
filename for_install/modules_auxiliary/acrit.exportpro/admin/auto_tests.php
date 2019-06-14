<?
if( !defined( "B_PROLOG_INCLUDED" ) || B_PROLOG_INCLUDED !== true ) die();

IncludeModuleLangFile( __FILE__ );

$arAutoProblems = $arAutoProblemsToSupportMessage = array();

if( !function_exists( "curl_setopt" ) ){
    $arAutoProblems[] = GetMessage( "AS_EXPORTPRO_NO_CURL_WARNING" );
    $arAutoProblemsToSupportMessage[] = GetMessage( "AS_EXPORTPRO_NO_CURL_WARNING_TP" );
}

if( !function_exists( "mb_convert_encoding" ) ){
    $arAutoProblems[] = GetMessage( "AS_EXPORTPRO_NO_MBSTRING_WARNING" );
    $arAutoProblemsToSupportMessage[] = GetMessage( "AS_EXPORTPRO_NO_MBSTRING_WARNING_TP" );
}

if( !function_exists( "simplexml_load_string" ) ){
    $arAutoProblems[] = GetMessage( "AS_EXPORTPRO_NO_SIMPLEXML_WARNING" );
    $arAutoProblemsToSupportMessage[] = GetMessage( "AS_EXPORTPRO_NO_SIMPLEXML_WARNING_TP" );
}

if( !function_exists( "json_decode" ) ){
	$arAutoProblems[] = GetMessage( "AS_EXPORTPRO_NO_JSON_WARNING" );
	$arAutoProblemsToSupportMessage[] = GetMessage( "AS_EXPORTPRO_NO_JSON_WARNING_TP" );
}

if( count( $arAutoProblems ) > 0 ){
	echo BeginNote();
	echo implode('<br />', $arAutoProblems);
	echo EndNote();
}
?>
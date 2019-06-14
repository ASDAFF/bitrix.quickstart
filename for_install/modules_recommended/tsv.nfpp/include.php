<?
global $DBType; 
IncludeModuleLangFile(__FILE__); 
	global $USER;
$mid = 'tsv.nfpp';	
	
$bShowOnlyAdmin = COption::GetOptionString($mid, "ONLY_FOR_ADMIN") == 'Y';	
if( ($bShowOnlyAdmin && $USER->IsAdmin()) || !$bShowOnlyAdmin){
	require_once($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/'.$mid.'/classes/nf_pp.php');	
}else{
	function pp(){
		return false;
	}
}		

?>
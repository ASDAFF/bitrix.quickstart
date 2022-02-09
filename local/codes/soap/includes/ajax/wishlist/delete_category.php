<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?
if(CModule::IncludeModule("iblock")){
	GLOBAL $USER;
	$user_id = $USER->GetParam("USER_ID");
	$res = CIBlockSection::GetByID($_REQUEST['cat']);
	if($ar_res = $res->GetNext()){
		if($ar_res['CREATED_BY']==$user_id){
			CIBlockSection::Delete($ar_res['ID']);
		}
	}
}
?>
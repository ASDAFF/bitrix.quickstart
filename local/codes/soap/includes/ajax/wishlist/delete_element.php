<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?
if(CModule::IncludeModule("iblock")){
	GLOBAL $USER;
	$user_id = $USER->GetParam("USER_ID");
	$arFilter = Array(
		"IBLOCK_ID" => 2,
		"ID" => IntVal($_REQUEST['cat']),	
		"CREATED_BY" => $user_id
	);
	$res = CIBlockSection::GetList(Array($by=>$order), $arFilter, true, array("ID"));
	if($ar_res = $res->GetNext()){
		$arFilter = Array(
			"IBLOCK_ID" => 2, 
			"SECTION_ID" => $ar_res['ID'],
			"PROPERTY_WISH" => $_REQUEST['element']
		);
		$res = CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilter, Array("ID", "PROPERTY_WISH"));
		if($ar_fields = $res->GetNext())
		{
			CIBlockElement::Delete($ar_fields['ID']);
		}
	}
}
?>
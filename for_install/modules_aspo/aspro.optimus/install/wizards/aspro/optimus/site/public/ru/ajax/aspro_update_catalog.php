<?
define("NOT_CHECK_PERMISSIONS",true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$catalog_id=(int)$_GET["catalog_id"];
if($catalog_id){
	\Bitrix\Main\Loader::includeModule('iblock');
	$rsItems=CIBlockElement::GetList(array(), array("IBLOCK_ID"=>$catalog_id, "ACTIVE"=>"Y"), false, false, array("ID", "ACTIVE"));
	$el = new CIBlockElement;	
	while($arItem=$rsItems->Fetch()){
		$res = $el->Update($arItem["ID"], array("ACTIVE"=>$arItem["ACTIVE"]));
	}
}else{
	echo "Select catalog";
}
?>
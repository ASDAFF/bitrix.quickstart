<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule("iblock"))
	return;

//инфоблок
$arTypesEx = CIBlockParameters::GetIBlockTypes(Array("-"=>" "));
$arIBlocks = Array("-"=>" ");
$db_iblock = CIBlock::GetList(Array("SORT"=>"ASC"), Array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
while($arRes = $db_iblock->Fetch())
	$arIBlocks[$arRes["ID"]] = '['.$arRes["CODE"].'] '.$arRes["NAME"];

//категории
$arCategories = array();
if(is_numeric($arCurrentValues["IBLOCK_ID"])) {
	$arFilter = Array('IBLOCK_ID'=>$arCurrentValues["IBLOCK_ID"], 'GLOBAL_ACTIVE'=>'Y');
	$rsSect = CIBlockSection::GetList(array('left_margin' => 'asc'),$arFilter);
	while ($arSect = $rsSect->GetNext())
		$arCategories[$arSect['ID']] = $arSect["NAME"];
}

$arTemplateParameters["GETLIST"] = array(
			'NAME' => GetMessage("MLIFE_SHARECOUNT_GETLIST"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
			"PARENT" => "",
);

if($arCurrentValues["GETLIST"]=='Y') {

$arTemplateParameters["IBLOCK_TYPE"] = Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MLIFE_SHARECOUNT_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arTypesEx,
			"DEFAULT" => "-",
			"REFRESH" => "Y",
		);
$arTemplateParameters["IBLOCK_ID"] = Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MLIFE_SHARECOUNT_IBLOCK_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"DEFAULT" => '-',
			"ADDITIONAL_VALUES" => "N",
			"REFRESH" => "Y",
		);
$arTemplateParameters["CATEGORY_ID"] = Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("MLIFE_SHARECOUNT_CATEGORY_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arCategories,
			"DEFAULT" => '',
			"ADDITIONAL_VALUES" => "N",
			"REFRESH" => "Y",
		);
		
$arTemplateParameters["COUNT"] = array(
			'NAME' => GetMessage("MLIFE_SHARECOUNT_COUNT"),
			"TYPE" => "TEXT",
			"DEFAULT" => "",
		);	
	
}

$arTemplateParameters["SHARE_DESC"] = array(
			'NAME' => GetMessage("MLIFE_SHARECOUNT_SHARE_DESC"),
			"TYPE" => "TEXT",
			"DEFAULT" => "",
		);
$arTemplateParameters["IMG_DESC"] = array(
			'NAME' => GetMessage("MLIFE_SHARECOUNT_IMG_DESC"),
			"TYPE" => "TEXT",
			"DEFAULT" => "",
		);

?>
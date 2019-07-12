<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION;
$aMenuLinksExt = array();

if(file_exists($_SERVER["DOCUMENT_ROOT"]."/include/telephone.php")){
	$phone = file_get_contents($_SERVER["DOCUMENT_ROOT"]."/include/telephone.php");
	if(strlen($phone)){
		$phone = strip_tags($phone);
		$aMenuLinksExt[] = array(
			$phone,
			"",
			array(),
			array("PHONE" => true),
			""
		);
	}
}

if(CModule::IncludeModule('iblock'))
{
	$arFilter = array(
		"TYPE" => "menu",
		"SITE_ID" => SITE_ID,
		"CODE" => "social_menu"
	);

	$dbIBlock = CIBlock::GetList(array('SORT' => 'ASC', 'ID' => 'ASC'), $arFilter);
	$dbIBlock = new CIBlockResult($dbIBlock);

	if ($arIBlock = $dbIBlock->GetNext())
	{
		if(defined("BX_COMP_MANAGED_CACHE"))
			$GLOBALS["CACHE_MANAGER"]->RegisterTag("iblock_id_".$arIBlock["ID"]);

		if($arIBlock["ACTIVE"] == "Y")
		{
			$rsElements = CIBlockElement::GetList(array("sort" => "asc", "name" => "asc"), array("IBLOCK_ID" => $arIBlock["ID"], "ACTIVE" => "Y"), false, array("nTopCount" => 5), array("ID", "NAME", "PREVIEW_PICTURE", "PROPERTY_link"));
			while($arElement = $rsElements -> GetNext()){
				if(intval($arElement["PREVIEW_PICTURE"])){
					$rsFile = CFile::GetByID($arElement["PREVIEW_PICTURE"]);
					$arFile = $rsFile->Fetch();
					$arElement["IMG"] = $arFile;
				}else{
					$arElement["IMG"] = array();
				}
				$aMenuLinksExt[] = array(
					$arElement["NAME"],
					$arElement["PROPERTY_LINK_VALUE"],
					array(),
					array("IMG" => $arElement["IMG"]),
					""
				);
			}
		}
	}

	if(defined("BX_COMP_MANAGED_CACHE"))
		$GLOBALS["CACHE_MANAGER"]->RegisterTag("iblock_id_new");
}

$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt);
?>
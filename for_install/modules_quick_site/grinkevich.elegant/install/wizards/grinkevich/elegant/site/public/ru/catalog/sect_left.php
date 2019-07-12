<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
$arParams["IBLOCK_ID"] = '#CATALOG_IBLOCK_ID#';
$arParams["IBLOCK_TYPE"] = "catalog";
$arParams["PRICE_CODE"] = array(0 => "BASE");

if( is_int($url[2]) )
	$arResult["VARIABLES"]["SECTION_ID"] = $url[2];
elseif( strlen($url[2]) > 0 )
	$arResult["VARIABLES"]["SECTION_CODE"] = $url[2];
?>

<?if (CModule::IncludeModule("iblock") && COption::GetOptionString("eshop", "catalogSmartFilter", "Y", SITE_ID)=="Y")
{
    $arFilter = array(
        "ACTIVE" => "Y",
        "GLOBAL_ACTIVE" => "Y",
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
    );
    if(strlen($arResult["VARIABLES"]["SECTION_CODE"])>0)
    {
        $arFilter["=CODE"] = $arResult["VARIABLES"]["SECTION_CODE"];
    }
    elseif($arResult["VARIABLES"]["SECTION_ID"]>0)
    {
        $arFilter["ID"] = $arResult["VARIABLES"]["SECTION_ID"];
    }

	$obCache = new CPHPCache;
	if($obCache->InitCache(36000, serialize($arFilter), "/iblock/catalog"))
	{
		$arCurSection = $obCache->GetVars();
	}
	else
	{
		$arCurSection = array();
		$dbRes = CIBlockSection::GetList(array(), $arFilter, false, array("ID"));
		$dbRes = new CIBlockResult($dbRes);

		if(defined("BX_COMP_MANAGED_CACHE"))
		{
			global $CACHE_MANAGER;
			$CACHE_MANAGER->StartTagCache("/iblock/catalog");

			if ($arCurSection = $dbRes->GetNext())
			{
				$CACHE_MANAGER->RegisterTag("iblock_id_".$arParams["IBLOCK_ID"]);
			}
			$CACHE_MANAGER->EndTagCache();
		}
		else
		{
			if(!$arCurSection = $dbRes->GetNext())
				$arCurSection = array();
		}

		$obCache->EndDataCache($arCurSection);
	}



    ?>


    <?$APPLICATION->IncludeComponent(
		"bitrix:catalog.smart.filter",
		"",
		Array(
		    "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
		    "SECTION_ID" => $arCurSection["ID"],
		    "FILTER_NAME" => "arrFilter",
		    "PRICE_CODE" => $arParams["PRICE_CODE"],
		    "CACHE_TYPE" => "A",
		    "CACHE_TIME" => "36000000",
		    "CACHE_NOTES" => "",
		    "CACHE_GROUPS" => "Y",
		    "SAVE_IN_SESSION" => "N"
		),
		false
    );?>




<?
}
?>


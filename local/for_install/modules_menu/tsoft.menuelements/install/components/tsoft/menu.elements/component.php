<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

$arParams["ID"] = intval($arParams["ID"]);
$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);

$arParams["DEPTH_LEVEL"] = intval($arParams["DEPTH_LEVEL"]);
if($arParams["DEPTH_LEVEL"]<=0)
	$arParams["DEPTH_LEVEL"]=1;

$arResult["SECTIONS"] = array();
$arResult["ELEMENT_LINKS"] = array();

$CacheKeys = $APPLICATION->GetCurPage();

if($this->StartResultCache(false, $CacheKeys))
{
	if(!CModule::IncludeModule("iblock"))
	{
		$this->AbortResultCache();
	}
	else
	{
		$arFilter = array(
			"IBLOCK_ID"=>$arParams["IBLOCK_ID"],
			"GLOBAL_ACTIVE"=>"Y",
			"IBLOCK_ACTIVE"=>"Y",

		);
		$arOrder = array(
			"sort"=>"asc",
		);


        $aMenuLinksNew = array();
        $menuIndex = 0;
        $previousDepthLevel = 1;

        $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>50), $arSelect);
        while($ob = $res->GetNextElement())
        {
            $arElements= $ob->GetFields();
            $aMenuLinksNew[$menuIndex++] = array(
                htmlspecialchars($arElements["~NAME"]),
                $arElements["DETAIL_PAGE_URL"],
                array(),
                array(
                    "FROM_IBLOCK" => true,
                    "IS_PARENT" => false,
                    "DEPTH_LEVEL" => 1,
                ),
            );
//            echo '<pre style="background-color: #FFFFFF; position: relative;z-index: 10;">'; print_r($arElements); echo '</pre>';
        }

        $arResult = $aMenuLinksNew;
		$this->EndResultCache();
	}
}

return $arResult;
?>

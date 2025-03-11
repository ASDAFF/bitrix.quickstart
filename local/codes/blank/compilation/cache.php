<?
	$obCache 		= Bitrix\Main\Data\Cache::createInstance();
	$cacheLifetime  = 86400; 
	$cacheID 		= 'FEEDBACKS'; 
	$cachePath 		= '/'.$cacheID;
	
	if(isset($_REQUEST['PAGEN_1']))
		$obCache->CleanDir();
	
	if( $obCache->InitCache($cacheLifetime, $cacheID, $cachePath) )
	{
		$vars = $obCache->GetVars();
		$arResult = $vars['FEEDBACKS'];
	}
	elseif( $obCache->StartDataCache() )
	{
		$arResult['DATA'] = GetOniksFeedbacks( $IBLOCK_ID );	
		$arResult['PAGINATION_OBJECT'] = SetOniksFeedbacksPagination( $PAGE_ELEMENT_COUNT,GetOniksFeedbacks( $IBLOCK_ID ) );
		$obCache->EndDataCache(array('FEEDBACKS' => $arResult ));
	}

////////////////////////////////////////////////////////////////////////////////////////////
// тегированый кеш 
//http://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&LESSON_ID=2978&LESSON_PATH=3913.4565.4780.2978
if (!isset($arParams['FILTER_VIEW_MODE']) || (string)$arParams['FILTER_VIEW_MODE'] == '')
	$arParams['FILTER_VIEW_MODE'] = 'VERTICAL';
$arParams['USE_FILTER'] = (isset($arParams['USE_FILTER']) && $arParams['USE_FILTER'] == 'Y' ? 'Y' : 'N');

$isVerticalFilter = ('Y' == $arParams['USE_FILTER'] && $arParams["FILTER_VIEW_MODE"] == "VERTICAL");
$isSidebar = ($arParams["SIDEBAR_SECTION_SHOW"] == "Y" && isset($arParams["SIDEBAR_PATH"]) && !empty($arParams["SIDEBAR_PATH"]));
$isFilter = ($arParams['USE_FILTER'] == 'Y');

if ($isFilter)
{
	$arFilter = array(
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"ACTIVE" => "Y",
		"GLOBAL_ACTIVE" => "Y",
	);
	if (0 < intval($arResult["VARIABLES"]["SECTION_ID"]))
		$arFilter["ID"] = $arResult["VARIABLES"]["SECTION_ID"];
	elseif ('' != $arResult["VARIABLES"]["SECTION_CODE"])
		$arFilter["=CODE"] = $arResult["VARIABLES"]["SECTION_CODE"];

	$obCache = new CPHPCache();
	if ($obCache->InitCache(36000, serialize($arFilter), "/iblock/catalog"))
	{
		$arCurSection = $obCache->GetVars();
	}
	elseif ($obCache->StartDataCache())
	{
		$arCurSection = array();
		if (Loader::includeModule("iblock"))
		{
			$dbRes = CIBlockSection::GetList(array(), $arFilter, false, array("ID"));

			if(defined("BX_COMP_MANAGED_CACHE"))
			{
				global $CACHE_MANAGER;
				$CACHE_MANAGER->StartTagCache("/iblock/catalog");

				if ($arCurSection = $dbRes->Fetch())
					$CACHE_MANAGER->RegisterTag("iblock_id_".$arParams["IBLOCK_ID"]);

				$CACHE_MANAGER->EndTagCache();
			}
			else
			{
				if(!$arCurSection = $dbRes->Fetch())
					$arCurSection = array();
			}
		}
		$obCache->EndDataCache($arCurSection);
	}
	if (!isset($arCurSection))
		$arCurSection = array();
}










///////////////////////////
$сacheObj = Bitrix\Main\Data\Cache::createInstance();
        $cacheTimeSm = 3600;
        $cacheIdSm = 'arrFilterrr';
        $cacheDirSm = '/'.$cacheIdSm;
        $arrFilterrr = [];
        global $arSimilarItems;
        if ($сacheObj->InitCache($cacheTimeSm, $cacheIdSm, $cacheDirSm))
        {
            $arrFilterrr = $сacheObj->GetVars();
        }
        elseif ($сacheObj->StartDataCache())
        {
            function GetPreLinkProducts( $iblockId, $elementId, $showCountElBySides, $sectionId )
            {
                if(CModule::IncludeModule("iblock"))
                {
                    $resdb = CIBlockElement::GetList(array('ID' => 'DESC'), array(
                        'IBLOCK_ID' => $iblockId,
                        'SECTION_ID'=>$sectionId,
                        'ACTIVE' => 'Y',
                        'SECTION_GLOBAL_ACTIVE' => 'Y'),
                        false, array('nPageSize' => $showCountElBySides, 'nElementID' => $elementId),
                        array());

                    $linkProds = [];
                    while ( $res = $resdb->fetch() )
                    {
                        if( $res['ID'] !== $elementId )
                        {
                            $linkProds[] = (int)$res['ID'];
                        }
                    }

                    if( is_array($linkProds) )
                        return $linkProds;
                }
            }

            $arSectionEl = CIBlockElement::GetByID($ElementID)->fetch();
            $arrFilterrr   = GetPreLinkProducts( 2, (int)$ElementID, 4, (int)$arSectionEl['IBLOCK_SECTION_ID'] );

            if ($isInvalid)
            {
                $сacheObj->abortDataCache();
            }
            $сacheObj->EndDataCache($arrFilterrr);
        }

<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/** @var $this CBitrixComponent */

$iblockId = intval($arParams["IBLOCK_ID"]);

if($this->startResultCache(3600000, array($iblockId)))
{
	if (!\Bitrix\Main\Loader::includeModule("iblock"))
	{
		$this->abortResultCache();
		return;
	}
	if (!\Bitrix\Main\Loader::includeModule("citrus.realty"))
	{
		$this->abortResultCache();
		return;
	}

	if (is_numeric($arParams["IBLOCK_ID"]))
	{
		$rsIBlock = CIBlock::GetList(array(), array(
			"ACTIVE" => "Y",
			"ID" => $arParams["IBLOCK_ID"],
		));
	} else
	{
		$rsIBlock = CIBlock::GetList(array(), array(
			"ACTIVE" => "Y",
			"CODE" => $arParams["IBLOCK_ID"],
			"SITE_ID" => SITE_ID,
		));
	}
	if ($arResult = $rsIBlock->GetNext())
	{
		$sections = CIBlockSection::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_ID" => $arResult["ID"], "ACTIVE" => "Y", "DEPTH_LEVEL" => 1));
		$aNewMenuLinks = $elements = Array();
		$prev_level = 1;

		$rsElement = CIBlockElement::GetList(
			Array("SORT" => "ASC"),
			Array("IBLOCK_ID" => $arResult["ID"], "ACTIVE" => "Y", "GLOBAL_ACTIVE" => "Y"),
			$arGroupBy = false,
			$arNavStartParams = false,
			$arSelectFields = Array("ID", "NAME", "DETAIL_PAGE_URL", "PROPERTY_title", "SECTION_ID")
		);
		$rsElement->SetUrlTemplates();
		while ($arElement = $rsElement->GetNext())
		{
			$elements[$arElement['IBLOCK_SECTION_ID']][] = $arElement;
		}

		while ($section = $sections->GetNext())
		{
			if ($prev_level < $section['DEPTH_LEVEL'])
				$aNewMenuLinks[count($aNewMenuLinks)-1][3]['IS_PARENT'] = true;
			$prev_level = $section['DEPTH_LEVEL'];
			//print_r($section);
			$aNewMenuLinks[] = Array(
				$section['NAME'],
				"#",
				Array(),
				Array(
					"FROM_IBLOCK" => true,
					"IS_PARENT" => false,
					"DEPTH_LEVEL" => $section['DEPTH_LEVEL'],
					"LEVEL" => $section['DEPTH_LEVEL']
				)
			);
			if (is_array($elements[$section['ID']]))
				foreach ($elements[$section['ID']] as $element)
				{
					$aNewMenuLinks[] = Array(
						$element["PROPERTY_TITLE_VALUE"] ? $element["PROPERTY_TITLE_VALUE"] : $element["NAME"],
						$element["DETAIL_PAGE_URL"],
						Array(),
						Array(
							"FROM_IBLOCK" => true,
							"IS_PARENT" => false,
							"DEPTH_LEVEL" => $section['DEPTH_LEVEL'],
							"LEVEL" => $section['DEPTH_LEVEL']
						)
					);
				}
		}
		$arResult["LINKS"] = $aNewMenuLinks;

		$this->endResultCache();
	}
	else
	{
		$this->abortResultCache();
		ShowError("Iblock {$arParams['IBLOCK_ID']} not found!");
		@define("ERROR_404", "Y");
		CHTTP::SetStatus("404 Not Found");
	}
}

return $arResult["LINKS"];
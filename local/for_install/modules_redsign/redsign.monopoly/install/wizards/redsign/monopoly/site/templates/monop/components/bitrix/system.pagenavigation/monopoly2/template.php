<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

$ClientID = 'navigation_'.$arResult['NavNum'];

if(!$arResult["NavShowAlways"]) {
	if ($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false))
		return;
}

$arResult['NavQueryString'] = preg_replace('/(\&)?(AJAX_CALL|action)\=[^\&]*/', '', $arResult['NavQueryString']);

?><nav><?
	?><ul class="pagination list-unstyled"><?

	$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
	$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");
	if($arResult["bDescPageNumbering"] === true) {
		// to show always first and last pages
		$arResult["nStartPage"] = $arResult["NavPageCount"];
		$arResult["nEndPage"] = 1;

		$sPrevHref = '';
		if ($arResult["NavPageNomer"] < $arResult["NavPageCount"]) {
			$bPrevDisabled = false;
			if ($arResult["bSavePage"]) {
				$sPrevHref = $arResult["sUrlPath"].'?'.$strNavQueryString.'PAGEN_'.$arResult["NavNum"].'='.($arResult["NavPageNomer"]+1);
			} else {
				if ($arResult["NavPageCount"] == ($arResult["NavPageNomer"]+1)) {
					$sPrevHref = $arResult["sUrlPath"].$strNavQueryStringFull;
				} else{
					$sPrevHref = $arResult["sUrlPath"].'?'.$strNavQueryString.'PAGEN_'.$arResult["NavNum"].'='.($arResult["NavPageNomer"]+1);
				}
			}
		} else {
			$bPrevDisabled = true;
		}
		
		$sNextHref = '';
		if ($arResult["NavPageNomer"] > 1) {
			$bNextDisabled = false;
			$sNextHref = $arResult["sUrlPath"].'?'.$strNavQueryString.'PAGEN_'.$arResult["NavNum"].'='.($arResult["NavPageNomer"]-1);
		} else {
			$bNextDisabled = true;
		}
		if ($bPrevDisabled) {
			?><li class="disabled"><a href="#">&laquo;</a></li><?
		} else {
			?><li><a href="<?=$sPrevHref;?>" id="<?=$ClientID?>_previous_page">&laquo;</a></li><?
		}

		$bFirst = true;
		$bPoints = false;
		do {
			$NavRecordGroupPrint = $arResult["NavPageCount"] - $arResult["nStartPage"] + 1;
			if ($arResult["nStartPage"] <= 2 || $arResult["NavPageCount"]-$arResult["nStartPage"] <= 1 || abs($arResult['nStartPage']-$arResult["NavPageNomer"])<=2) {
				if ($arResult["nStartPage"] == $arResult["NavPageNomer"]) {
					?><li class="active"><a href="#"><?=$NavRecordGroupPrint?></a></li><?
				} elseif($arResult["nStartPage"] == $arResult["NavPageCount"] && $arResult["bSavePage"] == false) {
					?><li><a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"><?=$NavRecordGroupPrint?></a></li><?
				} else {
					?><li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["nStartPage"]?>"><?=$NavRecordGroupPrint?></a></li><?
				}
				$bFirst = false;
				$bPoints = true;
			} else {
				if ($bPoints) {
					?><li><a href="#">...</a></li><?
					$bPoints = false;
				}
			}
			$arResult["nStartPage"]--;
		} while($arResult["nStartPage"] >= $arResult["nEndPage"]);

		if ($bNextDisabled) {
			?><li class="disabled"><a href="#">&raquo;</a></li><?
		} else {
			?><li><a href="<?=$sNextHref;?>" id="<?=$ClientID?>_next_page">&raquo;</a></li><?
		}
	} else {
		// to show always first and last pages
		$arResult["nStartPage"] = 1;
		$arResult["nEndPage"] = $arResult["NavPageCount"];

		$sPrevHref = '';
		if ($arResult["NavPageNomer"] > 1) {
			$bPrevDisabled = false;
			if ($arResult["bSavePage"] || $arResult["NavPageNomer"] > 2) {
				$sPrevHref = $arResult["sUrlPath"].'?'.$strNavQueryString.'PAGEN_'.$arResult["NavNum"].'='.($arResult["NavPageNomer"]-1);
			} else {
				$sPrevHref = $arResult["sUrlPath"].$strNavQueryStringFull;
			}
		} else {
			$bPrevDisabled = true;
		}

		$sNextHref = '';
		if ($arResult["NavPageNomer"] < $arResult["NavPageCount"]) {
			$bNextDisabled = false;
			$sNextHref = $arResult["sUrlPath"].'?'.$strNavQueryString.'PAGEN_'.$arResult["NavNum"].'='.($arResult["NavPageNomer"]+1);
		} else {
			$bNextDisabled = true;
		}
		if ($bPrevDisabled) {
			?><li class="disabled"><a href="#">&laquo;</a></li><?
		} else {
			?><li><a href="<?=$sPrevHref;?>" id="<?=$ClientID?>_previous_page">&laquo;</a></li><?
		}

		$bFirst = true;
		$bPoints = false;
		do {
			if ($arResult["nStartPage"] <= 2 || $arResult["nEndPage"]-$arResult["nStartPage"] <= 1 || abs($arResult['nStartPage']-$arResult["NavPageNomer"])<=2) {

				if ($arResult["nStartPage"] == $arResult["NavPageNomer"]) {
					?><li class="active"><a href="#"><?=$arResult["nStartPage"]?></a></li><?
				} elseif($arResult["nStartPage"] == 1 && $arResult["bSavePage"] == false) {
					?><li><a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"><?=$arResult["nStartPage"]?></a></li><?
				} else {
					?><li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["nStartPage"]?>"><?=$arResult["nStartPage"]?></a></li><?
				}
				$bFirst = false;
				$bPoints = true;
			} else {
				if ($bPoints) {
					?><li><a href="#">...</a></li><?
					$bPoints = false;
				}
			}
			$arResult["nStartPage"]++;
		} while($arResult["nStartPage"] <= $arResult["nEndPage"]);

		if ($bNextDisabled) {
			?><li class="disabled"><a href="#">&raquo;</a></li><?
		} else {
			?><li><a href="<?=$sNextHref;?>" id="<?=$ClientID?>_next_page">&raquo;</a></li><?
		}
	}

	if ($arResult["bShowAll"]) {
		if ($arResult["NavShowAll"]) {
			?><li class="allShower"><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>SHOWALL_<?=$arResult["NavNum"]?>=0"><?=GetMessage("nav_paged")?></a></li><?
		} else {
			?><li class="allShower"><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>SHOWALL_<?=$arResult["NavNum"]?>=1"><?=GetMessage("nav_show_all_custom")?></a></li><?
		}
	}
	?></ul><?
?></nav><?
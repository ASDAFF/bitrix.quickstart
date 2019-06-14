<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

if(!$arResult["NavShowAlways"]) {
	if ($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false))
		return;
}

$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");

?><nav><?
	?><ul class="pagination list-unstyled"><?

		if($arResult["bDescPageNumbering"] === true) {

			if ($arResult["NavPageNomer"] < $arResult["NavPageCount"]) {
				if($arResult["bSavePage"]) {
					?><li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>">&laquo;</a></li><?
				} else {
					if ($arResult["NavPageCount"] == ($arResult["NavPageNomer"]+1) ) {
						?><li><a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>">&laquo;</a></li><?
					} else {
						?><li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>">&laquo;</a></li><?
					}
				}
			} else {
				?><li class="disabled"><a href="#">&laquo;</a></li><?
			}

			while($arResult["nStartPage"] >= $arResult["nEndPage"]) {
				$NavRecordGroupPrint = $arResult["NavPageCount"] - $arResult["nStartPage"] + 1;
				if ($arResult["nStartPage"] == $arResult["NavPageNomer"]) {
					?><li class="active"><a href="#"><?=$NavRecordGroupPrint?></a></li><?
				} elseif($arResult["nStartPage"] == $arResult["NavPageCount"] && $arResult["bSavePage"] == false) {
					?><li><a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"><?=$NavRecordGroupPrint?></a></li><?
				} else {
					?><li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["nStartPage"]?>"><?=$NavRecordGroupPrint?></a></li><?
				}
				$arResult["nStartPage"]--;
			}

			if ($arResult["NavPageNomer"] > 1) {
				?><li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>">&raquo;</a></li><?
			} else {
				?><li class="disabled"><a href="#">&raquo;</a></li><?
			}

		} else {

			if ($arResult["NavPageNomer"] > 1) {
				if($arResult["bSavePage"]) {
					?><li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>">&laquo;</a></li><?
				} else {
					if ($arResult["NavPageNomer"] > 2) {
						?><li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>">&laquo;</a></li><?
					} else {
						?><li><a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>">&laquo;</a></li><?
					}
				}
			} else {
				?><li class="disabled"><a href="#">&laquo;</a></li><?
			}

			while($arResult["nStartPage"] <= $arResult["nEndPage"]) {
				if ($arResult["nStartPage"] == $arResult["NavPageNomer"]) {
					?><li class="active"><a href="#"><?=$arResult["nStartPage"]?></a></li><?
				} elseif($arResult["nStartPage"] == 1 && $arResult["bSavePage"] == false) {
					?><li><a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"><?=$arResult["nStartPage"]?></a></li><?
				} else {
					?><li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["nStartPage"]?>"><?=$arResult["nStartPage"]?></a></li><?
				}
				$arResult["nStartPage"]++;
			}

			if($arResult["NavPageNomer"] < $arResult["NavPageCount"]) {
				?><li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>">&raquo;</a></li><?
			} else {
				?><li class="disabled"><a href="#">&raquo;</a></li><?
			}

		}

		if($arResult["bShowAll"]) {
			if ($arResult["NavShowAll"]) {
				?><li class="allShower"><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>SHOWALL_<?=$arResult["NavNum"]?>=0" rel="nofollow"><?=GetMessage("nav_paged")?></a></li><?
			} else {
				?><li class="allShower"><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>SHOWALL_<?=$arResult["NavNum"]?>=1" rel="nofollow"><?=GetMessage("nav_show_all")?></a></li><?
			}
		}

	?></ul><?

?></nav><?
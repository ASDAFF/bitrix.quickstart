<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);

if (!$arResult["NavShowAlways"]) {
	if ($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false))
		return;
}

$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");
?>

<div class="pagenavigator">
	<? if ($arResult["bDescPageNumbering"] === true) {
		if ($arResult["NavPageNomer"] < $arResult["NavPageCount"]) {
			if ($arResult["bSavePage"]) {
				?><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>"><?=GetMessage("sfl_nav_prev")?></a><?
			} else {
				if ($arResult["NavPageCount"] == ($arResult["NavPageNomer"]+1)) {
					?><a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"><?=GetMessage("sfl_nav_prev")?></a><?
				} else {
					?><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>"><?=GetMessage("sfl_nav_prev")?></a><?
				}
			}
		} else {
			echo '<span>'.GetMessage("sfl_nav_prev").'</span>';
		}

		while ($arResult["nStartPage"] >= $arResult["nEndPage"]) {
			$NavRecordGroupPrint = $arResult["NavPageCount"] - $arResult["nStartPage"] + 1;
			if ($arResult["nStartPage"] == $arResult["NavPageNomer"]) {
				echo '<span class="active">'.$NavRecordGroupPrint.'</span>';
			} else if ($arResult["nStartPage"] == $arResult["NavPageCount"] && $arResult["bSavePage"] == false) {
				?><a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"><?=$NavRecordGroupPrint?></a><?
			} else {
				?><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["nStartPage"]?>"><?=$NavRecordGroupPrint?></a><?
			}
			$arResult["nStartPage"]--;
		}

		if ($arResult["NavPageNomer"] > 1) {
			?><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>"><?=GetMessage("sfl_nav_next")?></a><?
		} else {
			echo '<span>'.GetMessage("sfl_nav_next").'</span>';
		}
	} else {
		if ($arResult["NavPageNomer"] > 1) {
			if ($arResult["bSavePage"]) {
				?><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>"><?=GetMessage("sfl_nav_prev")?></a><?
			} else {
				if ($arResult["NavPageNomer"] > 2) {
					?><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>"><?=GetMessage("sfl_nav_prev")?></a><?
				} else {
					?><a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"><?=GetMessage("sfl_nav_prev")?></a><?
				}
			}
		} else {
			echo '<span>'.GetMessage("sfl_nav_prev").'</span>';
		}

		while ($arResult["nStartPage"] <= $arResult["nEndPage"]) {
			if ($arResult["nStartPage"] == $arResult["NavPageNomer"]) {
				echo '<span class="active">'.$arResult["nStartPage"].'</span>';
			} else if ($arResult["nStartPage"] == 1 && $arResult["bSavePage"] == false) {
				?><a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"><?=$arResult["nStartPage"]?></a><?
			} else {
				?><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["nStartPage"]?>"><?=$arResult["nStartPage"]?></a><?
			}
			$arResult["nStartPage"]++;
		}

		if ($arResult["NavPageNomer"] < $arResult["NavPageCount"]) {
			?><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>"><?=GetMessage("sfl_nav_next")?></a><?
		} else {
			echo '<span>'.GetMessage("sfl_nav_next").'</span>';
		}
	} ?>

	<? if ($arResult["bShowAll"]): ?>
		<noindex>
			<?if ($arResult["NavShowAll"]):?>
				<br /><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>SHOWALL_<?=$arResult["NavNum"]?>=0" rel="nofollow"><?=GetMessage("sfl_nav_paged")?></a>
			<?else:?>
				<br /><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>SHOWALL_<?=$arResult["NavNum"]?>=1" rel="nofollow"><?=GetMessage("sfl_nav_all")?></a>
			<?endif?>
		</noindex>
	<? endif; ?>
</div>
<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>


<select id="gr-sections-list">
	<?foreach($arResult["SECTIONS"] as $arSection){?>
		<option value="<?=$arSection["SECTION_PAGE_URL"]?>?<?=htmlspecialcharsbx("arrFilter_".$arParams["FILTER_ID"]."_".abs(crc32($arParams["FILTER_ELEMENT"])));?>=Y&set_filter=y"><?=$arSection["NAME"]?></option>
	<?}?>
</select>



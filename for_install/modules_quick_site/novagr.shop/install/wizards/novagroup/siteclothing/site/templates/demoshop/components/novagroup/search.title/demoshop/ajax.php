<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?
if (count($arResult["ITEMS"]) > 0)
{
?>
    <table class="title-search-result">
            <tr class="title-search-line">
                <?php /*<th class="title-search-separator">&nbsp;</th>*/ ?>
                <td class="title-search-separator">&nbsp;</td>
            </tr>
            <?foreach ($arResult["ITEMS"] as $i => $arItem):
                ?>
                <tr>
                    <td width="75">
                        <a href="<? echo $arItem["DETAIL_PAGE_URL"] ?>?ID=<?=(int)$arResult["PHOTO_ID"]?>#color-<?=(int)$arResult["PHOTO_ID"]?>-<?=(int)$arItem['ID']?>"><?$APPLICATION->IncludeComponent(
                                "novagroup:{$arParams['SEARCH_WHERE']}.element.photo",
                                "search",
                                Array(
                                    "CATALOG_ELEMENT_ID" => (int)$arItem['ID'],
                                    "CATALOG_IBLOCK_ID" => (int)$arItem['IBLOCK_ID'],
                                    "PHOTO_WIDTH" => "75",
                                    "PHOTO_HEIGHT" => "100",
                                    "PHOTO_ID"=>$arResult["PHOTO_ID"]
                                ),
                                false,
                                Array(
                                    'ACTIVE_COMPONENT' => 'Y',
                                    "HIDE_ICONS" => "Y"
                                )
                            );?></a>
                    </td>
                    <td class="title-search-item">
                        <a href="<? echo $arItem["DETAIL_PAGE_URL"] ?>?ID=<?=(int)$arResult["PHOTO_ID"]?>#color-<?=(int)$arResult["PHOTO_ID"]?>-<?=(int)$arItem['ID']?>"><? echo $arItem["NAME"] ?></a>
                        <?
                        if($arParams['SEARCH_WHERE']=='catalog')
                        {
                            $catalogPrice = new Novagroup_Classes_General_CatalogPrice($arItem['ID'], $arItem['IBLOCK_ID'], 0);
                            $prices = $catalogPrice->getPrice();
                        }
                        if($arParams['SEARCH_WHERE']=='fashion')
                        {
                            $fashion = new Novagroup_Classes_General_Fashion($arItem['IBLOCK_ID']);
                            $prices = $fashion->getPriceByElement($arItem['ID']);
                        }
                        ?>
                        <div class="price">
                            <? if (isset($prices['OLD_PRICE'])): ?>
                                <span class="actual discount"><?= $prices['FROM'] . $prices['PRINT_PRICE']; ?></span>
                                <span
                                    class="actual old-price block"><?= $prices['FROM'] . $prices['PRINT_OLD_PRICE']; ?></span>
                            <? else: ?>
                                <span
                                    class="actual default-value"><?= $prices['FROM'] . $prices['PRINT_PRICE']; ?></span>
                            <?endif; ?>
                        </div>
                    </td>
                </tr>
            <? endforeach; ?>
            <? if(isset($arResult["QUERY_RESULT"]->NavRecordCount) and $arResult["QUERY_RESULT"]->NavRecordCount>3): ?>
                <tr>
                    <td>&nbsp;
                        
                    </td>
                    <td class="title-search-all">
                        <a href="<? echo $arResult["GET_ALL_RESULTS"]["URL"] ?>"><? echo $arResult["GET_ALL_RESULTS"]["NAME"]?></a>
                    </td>
                </tr>
            <? endif; ?>
    </table>
<?
}else{
?>
<table class="title-search-result">
	<tr class="title-search-line">
		<td class="title-search-separator">&nbsp;</td>
	</tr>
	<tr>
		<td><?=$arResult["NOT_FOUND"];?></td>
	</tr>
</table>
<?
}
?>
<script type="text/javascript">
    var offsetSearch = $("#search").offset();
    var widthSearch = $("#search").width();
    $(".searchtd").css({"z-index": "300", "position": "relative"})
    $(".title-search-result").css({"left": 3 + offsetSearch.left + "px", "top": 40 + offsetSearch.top + "px", "width": widthSearch - 8 + "px"});
</script>
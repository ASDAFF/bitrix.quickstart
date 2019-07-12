<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
if(CModule::IncludeModuleEx('bitrix.fashion')==3){
	echo GetMessage("TEST_END");
	return;
}
?>

<?//if (count($arResult["ITEMS"]) > 1) {
$arAvailableSort = array(
    "name" => Array("name", "asc"),
    "brand" => Array("PROPERTY_fil_models_brand", "asc"),
    "popularity" => Array("PROPERTY_models_rating", "asc"),

);
$sort = $arParams["ELEMENT_SORT_FIELD"];
$sort_order = $arParams["ELEMENT_SORT_ORDER"];

$arPerPage = array(12, 24, 36);
/*
print '<pre>';
print_r($arParams);
print '</pre>';
*/
?>

<div class="options">
    <ul class="sorting">
        <?foreach ($arAvailableSort as $key => $val):
            $className = $sort == $val[0] ? ' active' : '';
            if ($className)
                $className .= $sort_order == 'asc' ? ' asc' : ' desc';
            $newSort = $sort == $val[0] ? $sort_order == 'desc' ? 'asc' : 'desc' : $arAvailableSort[$key][1];
        ?>
        <li class="<?=$className?>"><a href="<?=$APPLICATION->GetCurPageParam('sort='.$key.'&order='.$newSort, array('sort', 'order'))?>" rel="nofollow"><span><?=GetMessage('SORT_'.$key)?></span></a></li>
        <?endforeach;?>
    </ul>
    <?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
        <?=$arResult["NAV_STRING"]?>
	<?endif;?>

	<form id="page-counter-top" method="get">
	
	<?if($arParams['SHOW_INSTOCK']=='Y'):?>
	<div style="float: right; margin-right: 10px;">		
		<span style="margin-left: 7px;">
		<input style="margin-top: -3px;" type="checkbox" name="instock" value="Y" id="instock"<?=(isset($_REQUEST['instock'])&&$_REQUEST['instock']=='Y'?' checked':'')?>>&nbsp;<label for="instock"><?=GetMessage("G_IN_STOCK")?></label>
		</span>
		<script>$('#instock').click(function(){$('#page-counter-top').submit();});</script>
	</div>	
	<?endif;?>

    <span class="page-limit">
        <label for="page-limit-top"><?=GetMessage("PER_PAGE")?></label>
        <select name="count" id="page-limit-top" class="page-limit-select">
            <?foreach ($arPerPage as $val) {?>
            <option value="<?=$val?>"<?=(intval($_REQUEST["count"]) == $val ? " selected" : "")?>><?=$val?></option>
            <?}?>
        </select>
        <script>$('#page-limit-top').change(function(){$('#page-counter-top').submit();});</script>
    </span>
    </form>
</div>
<?//}?>


<?if (!empty($arResult["ITEMS"])) {?>
<div class="catalog">
    <ul>
        <?foreach($arResult["ITEMS"] as $cell => $arElement):?>
        <?
        $this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
        $this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
        ?>

        <?$minPrice = $minDiscountPrice = $isSale = 0;
        $arSizes = $arColors = array();
        $img = '';

        $minPrice = $arElement["OFFERS"][0]["PRICES"][$arResult["BASE_PRICES"]]["VALUE"];
        $minDiscountPrice = $arElement["OFFERS"][0]["PRICES"][$arResult["BASE_PRICES"]]["DISCOUNT_VALUE"];

        foreach ($arElement["OFFERS"] as $offer) {
            if ($offer["PRICES"][$arResult["BASE_PRICES"]]["DISCOUNT_VALUE"] < $offer["PRICES"][$arResult["BASE_PRICES"]]["VALUE"]) {
                $isSale = 1;
            }

            if ($minPrice > $offer["PRICES"][$arResult["BASE_PRICES"]]["VALUE"]) {
                $minPrice = $offer["PRICES"][$arResult["BASE_PRICES"]]["VALUE"];
            }

            if ($minDiscountPrice > $offer["PRICES"][$arResult["BASE_PRICES"]]["DISCOUNT_VALUE"]) {
                $minDiscountPrice = $offer["PRICES"][$arResult["BASE_PRICES"]]["DISCOUNT_VALUE"];
            }

            if (empty($img)) {
                $img = CFile::ResizeImageGet($offer["PROPERTIES"]["item_more_photo"]["VALUE"][0], array('width'=>250, 'height'=>320), BX_RESIZE_IMAGE_PROPORTIONAL, true);
            }

            //sizes
            $arSizes[$offer["PROPERTIES"]["item_size"]["SORT_VALUE"]] = $offer["PROPERTIES"]["item_size"]["VALUE"];

            //colors
            if (intval($offer["PROPERTIES"]["item_color"]["VALUE"]["DETAIL_PICTURE"]) > 0) {
                $colorImg = CFile::ResizeImageGet($offer["PROPERTIES"]["item_color"]["VALUE"]["DETAIL_PICTURE"], array('width'=>20, 'height'=>20), BX_RESIZE_IMAGE_PROPORTIONAL, true);
                $arColors[] = 'img|'.$offer["PROPERTIES"]["item_color"]["VALUE"]["CODE"] .'|<img src="'.$colorImg['src'].'" title="'.$offer["PROPERTIES"]["item_color"]["VALUE"]["NAME"].'" alt="'.$offer["PROPERTIES"]["item_color"]["VALUE"]["NAME"].'" width="'.$colorImg['width'].'" height="'.$colorImg['height'].'" />';
            } else {
                $arColors[] = 'hex|'.$offer["PROPERTIES"]["item_color"]["VALUE"]["CODE"] .'|#'.$offer["PROPERTIES"]["item_color"]["VALUE"]["PROPERTY_HEX_VALUE"].'|'.$offer["PROPERTIES"]["item_color"]["VALUE"]["NAME"];
            }
        }

        ksort($arSizes);
        $arSizes = array_unique($arSizes);
        $arSizes = array_values($arSizes);

        $arColors = array_unique($arColors);
        ?>

        <li id="<?=$this->GetEditAreaId($arElement['ID']);?>" class="item" itemscope itemtype="http://schema.org/Product">
		<?
		/*
		print '<pre>';
		print_r($arElement["PROPERTIES"]["models_hit"]);
		print '</pre>';
		*/
		?>
            <?if ($arElement["PROPERTIES"]["models_hit"]["VALUE_XML_ID"]=='yes' || $arElement["PROPERTIES"]["models_new"]["VALUE_XML_ID"]=='yes' || $isSale) {?>
            <ul class="shortcuts show">
                <?if ($arElement["PROPERTIES"]["models_hit"]["VALUE_XML_ID"]=='yes') {?><li class="hit show"><?=GetMessage("HIT")?></li><?}?>
                <?if ($arElement["PROPERTIES"]["models_new"]["VALUE_XML_ID"]=='yes') {?><li class="new show"><?=GetMessage("NEW")?></li><?}?>
                <?if ($isSale) {?><li class="discount show"><?=GetMessage("SALE")?></li><?}?>
            </ul>
            <?}?>
            <div class="hover">
                <?if (!empty($arSizes)) {?>
                <h5><?=GetMessage("IN_STOCK")?></h5>
                <p>
                    <span class="sizes"><?foreach ($arSizes as $key => $size) {
                    echo ($key + 1 == count($arSizes) ? $size : $size . ', ');
                    }?></span>
                    <a href="<?=SITE_DIR?>include/table-sizes.jpg" target="_blank" alt="<?=GetMessage("SIZING_CHART")?>" title="<?=GetMessage("SIZING_CHART")?>" class="tip"><?=GetMessage("HOW_TO_CHOOSE")?></a>
                </p>
                <?}?>

                <?if (!empty($arColors)) {?>
                <h5><?=GetMessage("AVAILABLE_COLORS")?></h5>
                <ul class="colors">
                    <?foreach ($arColors as $key => $color) {
                        $arColorData = explode("|", $color);
                        if ($arColorData[0] == "img") {
                            ?><li><a href="<?=$arElement["DETAIL_PAGE_URL"]?><?if($key>0){?><?=$arColorData[1]?>/<?}?>"><?=$arColorData[2]?></a></li><?
                        } else {
                            ?><li><a href="<?=$arElement["DETAIL_PAGE_URL"]?><?if($key>0){?><?=$arColorData[1]?>/<?}?>" title="<?=$arColorData[3]?>"><span style="background-color:<?=$arColorData[2]?>"></span></a></li><?
                        }
                    }?>
                </ul>
                <?}?>
            </div>
            <div class="image"><img src="<?=$img['src']?>" width="<?=$img['width']?>" height="<?=$img['height']?>" alt="<?=$arElement["NAME"]?>" /></div>
            <div class="link-overflow"><a href="<?=$arElement["DETAIL_PAGE_URL"]?>" title="<?=$arElement["NAME"]?>"></a></div>
            <div class="info">
                <div class="rating">
                    <div class="current level-<?=intval($arElement["PROPERTIES"]["models_rating"]["VALUE"])?>"></div>
                </div>

                <h4 itemprop="name"><a href="<?=$arElement["DETAIL_PAGE_URL"]?>" title="<?=$arElement["NAME"]?>"><?=$arElement["NAME"]?></a></h4>
                <p class="price" itemprop="offerDetails" itemscope itemtype="http://schema.org/Offer">
                    <?if ($isSale) {?>
                    <span class="oldprice"><?=CSiteFashionStore::formatMoney($minPrice)?> <span class="rub"><?=GetMessage("RUB")?></span></span>
                    <span class="newprice"><span itemprop="price"><?=CSiteFashionStore::formatMoney($minDiscountPrice)?></span> <span class="rub"><?=GetMessage("RUB")?></span></span>
                    <?} else {?>
                    <span itemprop="price"><?=CSiteFashionStore::formatMoney($minPrice)?></span> <span class="rub"><?=GetMessage("RUB")?></span>
                    <?}?>
                    <meta itemprop="currency" content="RUB" />
                </p>
            </div>
        </li>

        <?endforeach; // foreach($arResult["ITEMS"] as $arElement):?>

    </ul>
</div>
<script>$('.tip').click(function(e){window.open('<?=SITE_DIR?>include/table-sizes.php', '', 'width=565,scrollbars=yes'); e.preventDefault(); return false;});</script>

<?if (count($arResult["ITEMS"]) > 1) {?>
<div class="options">
    <ul class="sorting">
        <?foreach ($arAvailableSort as $key => $val):
            $className = $sort == $val[0] ? ' active' : '';
            if ($className)
                $className .= $sort_order == 'asc' ? ' asc' : ' desc';
            $newSort = $sort == $val[0] ? $sort_order == 'desc' ? 'asc' : 'desc' : $arAvailableSort[$key][1];
        ?>
        <li class="<?=$className?>"><a href="<?=$APPLICATION->GetCurPageParam('sort='.$key.'&order='.$newSort, array('sort', 'order'))?>" rel="nofollow"><span><?=GetMessage('SORT_'.$key)?></span></a></li>
        <?endforeach;?>
    </ul>
    <?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
        <?=$arResult["NAV_STRING"]?>
    <?endif;?>
    <form id="page-counter-bottom" method="get">
    <span class="page-limit">
        <label for="page-limit-bottom"><?=GetMessage("PER_PAGE")?></label>
        <select name="count" id="page-limit-bottom" class="page-limit-select">
            <?foreach ($arPerPage as $val) {?>
            <option value="<?=$val?>"<?=(intval($_REQUEST["count"]) == $val ? " selected" : "")?>><?=$val?></option>
            <?}?>
        </select>
        <script>$('#page-limit-bottom').change(function(){$('#page-counter-bottom').submit();});</script>
    </span>
    </form>
</div>
<?}?>

<?if (strlen($arResult["DESCRIPTION"]) > 0) {?>
<div class="category-description">
    <?=$arResult["DESCRIPTION"]?>
</div>
<?}?>

<?} else {?>
<br /><br /><?=GetMessage("RESULT_EMPTY")?>
<?}?>
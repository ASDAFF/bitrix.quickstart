<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (count($arResult["RECOMMEND_ELEMENTS"]) > 0) {
    ?>
    <div class="col-right col-right-recommend-products">
        <p class="title"><?=GetMessage("RECOMMEND_PRODUCTS")?></p>
        <div class="min-photo det-min">
            <?php
            //$arParams['priceID'] = 1;
            if (!empty($arParams['priceID'])) {
                $pr = CCatalogGroup::GetByID($arParams['priceID']);
                $priceCode = $pr["NAME"];
            } else {
                $getBase = Novagroup_Classes_General_CatalogPrice::getBaseGroup();

                $priceCode =  $getBase[0];
            }

/*
            if ($catalogPrices->priceID != false) {
                $pr = CCatalogGroup::GetByID($catalogPrices->priceID);
                $priceCode = $pr["NAME"];

            } else {
                $getBase = $catalogPrices->getBaseGroup();
                $priceCode =  $getBase[0];
            }
*/

            foreach($arResult["RECOMMEND_ELEMENTS"] as $val) {
                ?>
                <div class="depiction">
			<a class="detail-card" href="<?= $val['DETAIL_PAGE_URL'] ?>">
<?
$APPLICATION->IncludeComponent(
	"novagroup:ajaximgload",
	"",
	Array(
		"CATALOG_IBLOCK_ID"		=> $val['IBLOCK_ID'],
		"CALL_FROM_CATALOG"		=> "Y",
		"ATTRIBUTES"	=> array(
			'width'		=> 98,
			'height'	=> 130
		),
		"MICRODATA"		=> array(
			'elmid'	=> $val['ID']
		),
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "2592000",
	),
	false,
	Array(
		'ACTIVE_COMPONENT' => 'Y',
		//"HIDE_ICONS"=>"Y"
	)
);
?>
            <div class="wrapped-depiction">
                <span class="name-mini"><?=$val['NAME']?></span>
                <span><?= $val["PROPERTY_VENDOR_NAME"] ?></span>
                <?
                //deb($arParams['OFFERS_IBLOCK_ID']);
                $catalogPrices = new Novagroup_Classes_General_CatalogPrice(
                    $val['ID'], $val['IBLOCK_ID'], false, $arParams['OFFERS_IBLOCK_ID']
                );

                //$catalogPrices->setPricesWithDiscount(false);
                $prices = $catalogPrices->getPrice();
                ?>
                <span class="discount"><?=$prices['FROM'] . $prices['PRINT_PRICE'] ?></span>
                <?php
                if (isset($prices['PRINT_OLD_PRICE'])) {
                    ?>
                    <span class="old-price"><?= $prices['FROM'] . $prices['PRINT_OLD_PRICE'] ?></span>
                    <?
                }
                ?>
            </div>
</a>
			</div>
            <?php
            }
            ?>
        </div>
    </div>
<?php
}
?>

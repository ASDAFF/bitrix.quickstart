<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (count($arResult["RECOMMEND_ELEMENTS"]) > 0) {
    ?>
    <div class="col-right col-right-recommend-products">
        <p class="title"><?=GetMessage("RECOMMEND_PRODUCTS")?></p>
        <div class="min-photo det-min">
            <?php
            foreach($arResult["RECOMMEND_ELEMENTS"] as $val) {
                ?>
                <div class="depiction">
			<a class="detail-card" href="<?= $val['DETAIL_PAGE_URL'] ?>">
                <img width="98" height="130"
                     alt="<?=htmlspecialcharsEx($val["NAME"])?>"
                     src="<?$APPLICATION->IncludeComponent(
                         "novagroup:catalog.element.photo",
                         "common",
                         Array(
                             "CATALOG_IBLOCK_ID" => $val['IBLOCK_ID'],
                             "CATALOG_ELEMENT_ID" => $val['ID'],
                             "PHOTO_WIDTH" => "98",
                             "PHOTO_HEIGHT" => "128"
                         ),
                         false,
                         Array(
                             'ACTIVE_COMPONENT' => 'Y',
                             "HIDE_ICONS" => "Y"
                         )
                     );?>">
            </a>
			<div class="wrapped-depiction">
                <span class="name-mini"><?=$val['NAME']?></span>
                <span><?= $val["PROPERTY_VENDOR_NAME"] ?></span>
                <?
                $catalogPrices = new Novagroup_Classes_General_CatalogPrice($val['ID'],$val['IBLOCK_ID']);
                //$catalogPrices->setPricesWithDiscount(false);
                $prices = $catalogPrices->getPrice();
                ?>
                <span class="discount"><?= $prices['PRINT_PRICE'] ?></span>
                <?php
                if (isset($prices['PRINT_OLD_PRICE'])) {
                    ?>
                    <span class="old-price"><?= $prices['PRINT_OLD_PRICE'] ?></span>
                <?
                }
                ?>
            </div>
			</div>
            <?php
            }
            ?>
        </div>
    </div>
<?php
}
?>
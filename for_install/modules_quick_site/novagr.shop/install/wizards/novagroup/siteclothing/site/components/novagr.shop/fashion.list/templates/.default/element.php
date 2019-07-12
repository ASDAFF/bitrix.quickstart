<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();$this->setFrameMode(true);
// get detail card settings


$detail = $arParams['FASHION_ROOT_PATH'].$val['CODE']."/";

$arButtons = CIBlock::GetPanelButtons(
    $arParams['FASHION_IBLOCK_ID'],
    $arResult["ID"],
    0,
    array("SECTION_BUTTONS"=>false, "SESSID"=>false)
);
$val["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];

$this->AddEditAction(
    $arResult["ID"], $val["EDIT_LINK"],
    CIBlock::GetArrayByID($arParams['FASHION_IBLOCK_ID'], "ELEMENT_EDIT")
);
$PicPath = "/local/templates/demoshop/images/nophoto.png";

?>
<div class="col-left" id="<?=$this->GetEditAreaId($arResult['ID'])?>">
	<div class="col-left">
		<div class="min-photo">

			<div class="inter-block" id="thumbsCollection">
                <?

                    if ( isset($arResult['LIST']['PROPERTY_PHOTOS_VALUE']) ) {
                        foreach ($arResult['LIST']['PROPERTY_PHOTOS_VALUE'] as $key=>$val)
                        {
			$APPLICATION->IncludeComponent(
				"novagroup:ajaximgload",
				"",
				Array(
					//"CATALOG_IBLOCK_ID"	=> "",	// if it is established then IMG_ID as COLOR_ID and photos will be selected by CATALOG_ELEMENT_ID
					"IS_IMAGERY"			=> "Y",
					"CALL_FROM_CATALOG"		=> "Y",	// if call from catalog set Y to show normal after clear cache and edit mode for admin
					"ATTRIBUTES"			=> array(
						"id"				=> "",
						"class"				=> "previewImg",
						"width"				=> 97,	// if not defined then set default as 240
						"height"			=> 130, // if not defined then set default as 180
					),
					"MICRODATA"	=> array(
						"elmid"		=> "",		// CATALOG BLOCK_ID, CATALOG ELEMENT_ID and IMG_ID required fields
						"imgid"		=> $val,	// if need select photo by ID then set IMG_ID as PHOTO_ID and CATALOG_IBLOCK_ID and CATALOG_ELEMENT_ID was not be filled
						
						"big-pic"		=> $arResult['ORIGINAL']['PROPERTY_PHOTOS_VALUE'][$key],
						"middle-pic"	=> $arResult['ELEMENT']['PROPERTY_PHOTOS_VALUE'][$key]
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
<?
/*
?>
                            <img width="97" height="130" data-big-pic="<?=$arResult['ORIGINAL']['PROPERTY_PHOTOS_VALUE'][$key]?>" data-middle-pic="<?=$arResult['ELEMENT']['PROPERTY_PHOTOS_VALUE'][$key]?>"
                                 alt="" src="<?=$val?>" class="previewImg">
                        <?
*/
                        }
                    } else {
                        ?>
                        <img width="97" height="130" data-big-pic="<?=$PicPath?>" data-middle-pic="<?=$PicPath?>"
                             alt="" src="<?=$PicPath?>" class="previewImg">
                    <?
                    }
                ?>
            </div>

		</div>
	</div>
	<div class="col-right">
		<div class="big-demo" id="photosIm">
            <?
            // picture makes by js
            ?>
        </div>
	</div>
	<div class="clear"></div>
    <?
    $val = array("NAME" => $arResult['ELEMENT']['NAME']);
    $arResult['SOC_PHOTO'] = $arResult['ORIGINAL']['PROPERTY_PHOTOS_VALUE'][key($arResult['ORIGINAL']['PROPERTY_PHOTOS_VALUE'])];
    Novagroup_Classes_General_Main::getView('catalog.element','yashare',array("arResult"=>$arResult, "val"=>$val));
    ?>
    <script type="text/javascript">$('div.info-bar').removeClass('soc')</script>
</div>
<div class="col-right">
	<div class="composite-pr">
		<div class="header-title-demo">
			<div class="more-pr tooltip-demo" data-toggle="buttons-radio">

              <h1><?=$arResult['ELEMENT']['NAME'];?></h1>

            </div>
		</div>
        <?php
            // если пользователь входит в группу Администраторы интернет-магазина [5]
            // то показываем карандашик для редакрирования
            if ($arParams['SHOW_EDIT_BUTTON'] == "Y") {
            ?>
            <div class="tooltip-demo card-tool">
			<div class="bs-docs-tooltip-examples admin">
				<a target="_blank"
					href="/bitrix/admin/iblock_element_edit.php?ID=<?=$arResult['ELEMENT']['ID']?>&type=catalog&IBLOCK_ID=<?=$arParams["FASHION_IBLOCK_ID"]?>"
					data-original-title="<?=GetMessage("EDIT_LABEL")?>" rel="tooltip"
					data-placement="top"><i class="icon-admin"></i></a>
			</div>
		</div>
            <?php
            }
        ?>
        <hr class="composite-hr">
		<div class="">
		<?php $ITEMS_IN_IMAGERY = (count($arResult['ITEMS'])>0) ? GetMessage("ITEMS_IN_IMAGERY") : GetMessage("ITEMS_IN_IMAGERY_NOT_EXISTS"); ?>
			<p class="title"><?=$ITEMS_IN_IMAGERY; ?></p>
            <?php
                $SUM_FULL_PRICE = array();
                $SUM_OLD_PRICE = array();
                $SUM_CURRENCY = null;
                foreach ($arResult['ITEMS'] as $item) {
                    
                ?>
                <div class="pic-image">
				<div class="block-pr">
                        <div class="depiction">
					<a href="<?=$item['DETAIL_PAGE_URL']?>">
<?
$APPLICATION->IncludeComponent(
	"novagroup:ajaximgload",
	"",
	Array(
		"CATALOG_IBLOCK_ID"		=> $item['IBLOCK_ID'],	// if it is established then IMG_ID as COLOR_ID and photos will be selected by CATALOG_ELEMENT_ID
		//"CATALOG_ELEMENT_ID"	=> $item['ID'],			// CATALOG BLOCK_ID, CATALOG ELEMENT_ID and IMG_ID required fields
		//"IMG_ID"				=>  $color['ID'] ,		// if need select photo by ID then set IMG_ID as PHOTO_ID and CATALOG_IBLOCK_ID and CATALOG_ELEMENT_ID was not be filled
		//"IMG_WIDTH"				=> "93",				// if not defined then set default as 240
		//"IMG_HEIGHT"			=> "119",				// if not defined then set default as 180
		//"IMG_TITLE"				=> "",
		//"IMG_ALT"				=> "",
		"CALL_FROM_CATALOG"		=> "Y",					// if call from catalog set Y to show normal after clear cache and edit mode for admin
		"ATTRIBUTES" => array(
			'width'		=> 93,
			'height'	=> 119
		),
		"MICRODATA" => array(
			"elmid"	=> $item['ID'],
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
						</a>
                            <div class="wrapped-depiction">
                                <span style="cursor:pointer;" class="link-popover-card catalog-preview-color-element-498 catalog-preview-color-element-498-14">
                                    <a href="<?=$item['DETAIL_PAGE_URL']?>" class="quickViewLink" data-toggle="modal"><?=GetMessage("NOVAGR_JWSHOP_PROSMOTR")?>
                                        <img width="21" height="18" alt="" src="/local/templates/demoshop/images/lupa.png">
                                    </a>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="block-pr">

					<p><?=$item['NAME']?></p>
					<div class="wrapper-l">
                    <?php
                    if ($arResult['OPT_USER'] == 1) {
                        $priceID = $arParams['OPT_PRICE_ID'];
                    } else {
                        $priceID = false;
                    }

                    $catalogPrice = new Novagroup_Classes_General_CatalogPrice(
                        $item['ID'], $item['IBLOCK_ID'], $priceID, $arParams["OFFERS_IBLOCK_ID"]
                    );
                    $prices = $catalogPrice->getPrice();

                    if (isset($prices['OLD_PRICE'])) {
                        ?>
                        <span class="discount"><?=$prices['FROM'].$prices['PRINT_PRICE'];?></span>
                        <span class="old-price"><?=$prices['FROM'].$prices['PRINT_OLD_PRICE'];?></span>
                        <?php

                        } else {
                        ?><span class="default-value"><?=$prices['FROM'].$prices['PRINT_PRICE'];?></span><?php
                        }
                    ?>
                        <hr class="composite-hr">
						<div class="wrapper-l">
							<span class="brand-l"><?=GetMessage("BRAND_LABEL")?>:</span> <span><?=$item["PROPERTY_VENDOR_NAME"]?></span>
						</div>
					</div>
				</div>
			</div>
                <?php
                }
            ?>
            <div class="pic-image"></div>
			<div class="total-price">
                <p>
                    <?
                    if ($arResult['OPT_USER'] == 1) {
                        $priceID = $arParams['OPT_PRICE_ID'];
                    } else {
                        $priceID = false;
                    }
                    $fashion = new Novagroup_Classes_General_Fashion($arParams['FASHION_IBLOCK_ID'], $priceID);
                    $prices = $fashion->getPriceByElement($arResult['ID']);

                    if (isset($prices['OLD_PRICE'])) {
                        ?>
                        <?=GetMessage("TOTAL_COST_IMAGERY")?>:
                        <span class="discount"><?= $prices['FROM'] . $prices['PRINT_PRICE']; ?></span>
                        <span class="old-price"><?= $prices['FROM'] . $prices['PRINT_OLD_PRICE']; ?></span>
                    <?
                    } elseif(isset($prices['PRICE']) && $prices['PRICE']>0) {

                        ?>
                        <?=GetMessage("TOTAL_COST_IMAGERY")?>:
                        <span><?= $prices['FROM'] . $prices['PRINT_PRICE']; ?></span>
                    <?
                    }
                    ?>
                </p>
            </div>
		</div>
	</div>
</div>
<div class="clear"></div>

<div id="myModalCollection" class="modal fade card-img modal-overflow"
     tabindex="-1" aria-hidden="false" style="display: none;">
    <div id="myCarouselCollection" class="carousel">
        <div class="carousel-inner" id="carousel-inner-collection"></div>
        <!-- Carousel nav -->
        <a id="collection-left-arr" class="carousel-control left" href="#myCarouselCollection"
           data-slide="prev"></a> <a id="collection-right-arr"
                                     class="carousel-control right" href="#myCarouselCollection" data-slide="next"></a>
    </div>
</div>

<script>
    $(document).ready(function() {
        var detailCardView = <?=$arResult["DETAIL_CARD_VIEW"]?>;
        var photos = {};
        photos.BIG = [];
        photos.MIDDLE = [];
        photos.SMALL = [];
        <?php

        if (!empty($arResult['ELEMENT']['PROPERTY_PHOTOS_VALUE'])) {

            foreach ($arResult['ELEMENT']['PROPERTY_PHOTOS_VALUE'] as $key => $val) {
                ?>
        photos.BIG.push('<?=$arResult['ORIGINAL']['PROPERTY_PHOTOS_VALUE'][$key]?>');
        photos.MIDDLE.push('<?=$val?>');
        photos.SMALL.push('<?=$arResult['LIST']['PROPERTY_PHOTOS_VALUE'][$key]?>');
        <?php
    }
}
?>
        var messages = {
            "CAROUSEL_LABEL1" : "<?=GetMessage("CAROUSEL_LABEL1")?>",
            "CAROUSEL_LABEL2" : "<?=GetMessage("CAROUSEL_LABEL2")?>"
        }

        collection.init(detailCardView, messages, photos, "<?=$PicPath?>");
    });
</script>
<!-- >>> ajax image load scripts temporary decision -->
<script>
	$(document).ready(function(e) {
		ajaxImgLoad();
	});
</script>
<!-- <<< ajax image load scripts temporary decision -->

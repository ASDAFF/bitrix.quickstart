<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$ajaxUrl = $templateFolder . '/ajax.php';

if (!empty($arResult['ELEMENT']["ID"])) {
	$val = $arResult['ELEMENT'];
?>
    <section>
        <?
        $productID = $APPLICATION->IncludeComponent(
            "novagroup:catalog.timetobuy",
            "landing",
            Array(
                "IBLOCK_ID"=> $arParams["CATALOG_IBLOCK_ID"],
                "ID"=> $val['ID'],
                "PRODUCT_NAME" => $val['NAME']
            )
        );
        if (!$productID) {
            ?>
            <h1 id="product-name"><?=$val["NAME"]?></h1>
            <?
        }
        ?>
    </section>
    <section class="block-content-user edited">
        <?//=$arResult["LANDING_ELEMENT"]["PREVIEW_TEXT"]?>
        <?=htmlspecialchars_decode($arParams["LANDING_PREVIEW_TEXT"])?>
    </section>
<section>

<div class="row">
    <div class="detalet-cart card-land">

        <div class="img-photos-demo">
            <div class="big-demo" id="photos">
                <?php /* fotos prints by js */ ?>
            </div>
            <?
            Novagroup_Classes_General_Main::getView('catalog.element','actions',array("val"=>$val));
            ?>
            <div class="zoom-land"><a id="zoom" href="#" role="button"><img width="29" height="25" src="<?= SITE_TEMPLATE_PATH ?>/images/lupa-land.png" title="<?=GetMessage("LUPA_TITLE")?>" alt="zoom"></a></div>
        </div>
        <div class="thumbs" id="thumbs">
            <?php /* fotos prints by js */ ?>
        </div>
        <div class="clear"></div>
    </div>
</div>
</section>

<section class="block">
<div >
    <div class="span5">
        <div>
            <?
            if (is_array($arResult["CURRENT_ELEMENT"]["COLORS"])) {
                $params = array();
                foreach ($arResult["CURRENT_ELEMENT"]["COLORS"] as $color) {
                    $params[] = array(
                        "button"=>array(
                            "data-original-title"=>$arResult['mixData'][$color]['NAME'],
                            "id"=>"color-".$arResult['mixData'][$color]['ID']."-".$val['ID']."-set-by-hash",
                            "class"=>"btn",
                            "data-placement"=>"top",
                            "rel"=>"tooltip",
                            "data-color"=>$arResult['mixData'][$color]['ID'],
                            "type"=>"button",
                        ),
                        "img"=>array(
                            "src"=>(!empty($arResult['PREVIEW_PICTURE'][$color])) ? $arResult['PREVIEW_PICTURE'][$color] : "/local/templates/demoshop/images/not-f.jpg",
                            "width"=>"35",
                            "height"=>"33",
                            "border"=>"0",
                            "alt"=>$arResult['mixData'][$color]['NAME']
                        )
                    );
                }
                Novagroup_Classes_General_Main::getView('catalog.element','colors_button', array("params"=>$params) );
            }

            ?>
        </div>
        <hr class="composite-hr">
        <div>
            <div class="wrap-size">
                <div class="choice-size "><?=GetMessage("CHOOSE_SIZE_LABEL")?>:</div>
                <div id="size-table" >
                    &nbsp;<a href="#myModal8" ><?=GetMessage("SIZE_TABLE")?></a>
                </div>
                <div class="clear"></div>
                <div class="tab-choice tooltip-demo">
                    <?php

                    Novagroup_Classes_General_Main::includeView( SITE_DIR.'include/catalog/element/size_table.php', array("arResult"=>$arResult, "Params"=>$arParams) );

                    ?>
                </div>
             </div>

            <?php
            if (!empty($val['DETAIL_TEXT'])) {
                ?>
                <hr class="composite-hr">
                <div class="descript">
                    <p class="head-p"><?=GetMessage("DESCTIPTION")?>:</p>
                    <?=$val['DETAIL_TEXT']?>
                </div>

            <?
            }
            ?>
        </div>
    </div>
    <div class="span5 tab-choice right-block-land ">

        <?
        Novagroup_Classes_General_Main::includeView(SITE_DIR.'include/catalog/element/quick-buy-landing.php');

        Novagroup_Classes_General_Main::includeView(SITE_DIR.'include/catalog/element/actual-price-landing.php', array("arResult"=>$arResult) );
        ?>
        <?
        //include(SITE_DIR.'include/pSubscribe.php');
        //Novagroup_Classes_General_Main::includeView(SITE_DIR.'include/catalog/element/quick-buy-landing.php');

        ?>
    </div>

</div>
</section>
<section class="block-content-user edited block">
    <?=htmlspecialchars_decode($arParams["LANDING_DETAIL_TEXT"])?>
</section>
    <p class="more-link"><a href="/catalog/<?= $arResult["PRODUCT_SECTION"]["CODE"]?>/"><span><?=GetMessage("SEE_MORE_PRODUCTS")?></span> <span class="land"></span></a></p>
<div id="myModal8" class="modal hide fade size-tab-my mod-size" tabindex="-1" role="dialog" aria-labelledby="myModalLabel8" aria-hidden="true">
        <div class="modal-header" id="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h3 id="myModalLabel8"><?=GetMessage("SIZE_TABLE2")?></h3>
        </div>
	    <div class="modal-body" id="modal-body">
	    <?=$arResult["tablitsa-razmerov"]?>
		</div>
</div>
<?php 
}
?>

<div id="myModal" class="modal fade card-img" tabindex="-1" aria-hidden="false"  style="display: none;">
    <div id="myCarousel" class="carousel">
        <div class="carousel-inner" id="carousel-inner">
        
        </div>
       <!-- Carousel nav -->
        <a id="left-arr" class="carousel-control left" href="#myCarousel" data-slide="prev"></a>
        <a id="right-arr" class="carousel-control right" href="#myCarousel" data-slide="next"></a>
    </div>
</div>


<script>
$(document).ready(function(){
    // click handler on the size
    $("#myTab a").live("click", function(){
        var sizeId = $(this).data("size");
        product.changeSize(sizeId);

        return false;
    });
    
    // handler click the button to buy
    $(".addToBasket").unbind('click');
    $(".addToBasket").click(function(){
        product.buyClick(this);
        return false;
    });

    $("#size-table a").bind('click', function(){

        $("#myModal8").modal('show');
        $("#modal-body").css({'max-height':($(window).height() -68) + 'px' });
        top_proc = 0;
        $("#myModal8").css({'top':top_proc+'px', 'margin-top': top_proc+'px'});
        return false;
    });

    <?php

    $i = 0;
    foreach ($arResult["OFFERS"] as $item) {

        $quantity = intval($item["CATALOG_QUANTITY"]);
        $colorId = $item["DISPLAY_PROPERTIES"]["COLOR"]["VALUE"];
        if ($i == 0) {

            ?>
    var curPhotosSmall = [];
    var curPhotosBig = [];
    var curPhotosMiddle = [];
    var curPhotosBigHeight = [];
    <?php
    $photos_properties = array('curPhotosSmall','curPhotosBig','curPhotosBigHeight','curPhotosMiddle');
    foreach($photos_properties as $photo_property)
    {
        if(is_array($arResult['DETAIL_IMAGES'][$item['ID']][$photo_property])){
            foreach($arResult['DETAIL_IMAGES'][$item['ID']][$photo_property] as $photo)
            {
            ?>
            <?=$photo_property?>.push('<?=$photo?>');
            <?php
            }
        }
    }
// show the first offer with min price
?>
    var messages = {
        "NOT_IN_OPT_STOCK" : "<?=GetMessage("NOT_IN_OPT_STOCK")?>",
        "ALERT_NAME" : "<?=GetMessage("ALERT_NAME")?>",
        "NO_IN_STOCK" : "<?=GetMessage("NO_IN_STOCK")?>",
        "NO_SIZE_LABEL" : "<?=GetMessage("NO_SIZE_LABEL")?>",
        "PRODUCT_ADDED_TO_CART" : "<?=GetMessage("PRODUCT_ADDED_TO_CART")?>",
        "PRODUCT_ALREADY_IN_CART" : "<?=GetMessage("PRODUCT_ALREADY_IN_CART")?>",
        "ADDED_TO_SHELVES" : "<?=GetMessage("ADDED_TO_SHELVES")?>",
        "ALERT_MESSAGE" : "<?=GetMessage("ALERT_MESSAGE")?>",
        "SUBSCR_MESSAGE" : "<?=GetMessage("SUBSCR_MESSAGE")?>",
        "CAROUSEL_LABEL1" : "<?=GetMessage("CAROUSEL_LABEL1")?>",
        "CAROUSEL_LABEL2" : "<?=GetMessage("CAROUSEL_LABEL2")?>",
        "PRODUCT_NAME" : "<?=$val['NAME']?>"
    }
    product.setSiteID('<?=SITE_ID?>');
    var userArr = [];
    userArr["is_opt"] = <?=$arResult['OPT_USER']?>;
    product.init(
        <?=$item["ID"]?>,
        '<?=$item["PRICES"][$arResult["CUR_PRICE_CODE"]]["PRINT_DISCOUNT_VALUE_VAT"]?>',
        '<?=$item["DISPLAY_PROPERTIES"]["COLOR"]["VALUE"]?>',
        '<?=$item["DISPLAY_PROPERTIES"]["STD_SIZE"]["VALUE"]?>',
        '<?=$arResult['mixData'][$item["DISPLAY_PROPERTIES"]["COLOR"]["VALUE"]]['NAME']?>',
        '<?=$arResult['PREVIEW_PICTURE'][$item["DISPLAY_PROPERTIES"]["COLOR"]["VALUE"]]?>',
        curPhotosSmall,
        curPhotosBig,
        curPhotosBigHeight,
        '<?=$arResult['mixData'][$item["DISPLAY_PROPERTIES"]["STD_SIZE"]["VALUE"]]['NAME']?>','<?=$arResult['mixData'][$item["DISPLAY_PROPERTIES"]["STD_SIZE"]["VALUE"]]['SORT']?>',
        <?=$quantity?>,
        '<?=$item["PRICES"][$arResult["CUR_PRICE_CODE"]]["PRINT_VALUE_VAT"]?>',
        '<?=$ajaxUrl?>',
        <?=$val["ID"]?>,
        messages,
        0,
        '<?=$arResult['DETAIL_PAGE_URL']?>',
        <?=($arParams["CATALOG_SUBSCRIBE_ENABLE"] == "Y" ? 'true' : 'false' )?>,
        curPhotosMiddle,
        1,
        userArr
    );
    <?

        } else {

            ?>
    var curPhotosSmall = []
    var curPhotosMiddle = [];
    var curPhotosBig = [];
    var curPhotosBigHeight = [];
    <?php
    $photos_properties = array('curPhotosSmall','curPhotosBig','curPhotosBigHeight','curPhotosMiddle');
    foreach($photos_properties as $photo_property)
    {
        if(is_array($arResult['DETAIL_IMAGES'][$item['ID']][$photo_property])){
            foreach($arResult['DETAIL_IMAGES'][$item['ID']][$photo_property] as $photo)
            {
            ?>
            <?=$photo_property?>.push('<?=$photo?>');
            <?php
            }
        }
    }
?>

    product.addToSet(
        <?=$item["ID"]?>,
        '<?=$item["PRICES"][$arResult["CUR_PRICE_CODE"]]["PRINT_DISCOUNT_VALUE_VAT"]?>',
        '<?=$item["DISPLAY_PROPERTIES"]["COLOR"]["VALUE"]?>',
        '<?=$item["DISPLAY_PROPERTIES"]["STD_SIZE"]["VALUE"]?>',
        '<?=$arResult['mixData'][$item["DISPLAY_PROPERTIES"]["COLOR"]["VALUE"]]['NAME']?>',
        '<?=$arResult['PREVIEW_PICTURE'][$item["DISPLAY_PROPERTIES"]["COLOR"]["VALUE"]]?>',
        curPhotosSmall,
        curPhotosBig,
        curPhotosBigHeight,
        '<?=$arResult['mixData'][$item["DISPLAY_PROPERTIES"]["STD_SIZE"]["VALUE"]]['NAME']?>',
        '<?=$arResult['mixData'][$item["DISPLAY_PROPERTIES"]["STD_SIZE"]["VALUE"]]['SORT']?>',
        <?=$quantity?>,
        '<?=$item["PRICES"][$arResult["CUR_PRICE_CODE"]]["PRINT_VALUE_VAT"]?>',
        curPhotosMiddle
    );
    <?php
    }
    $i++;
}
if ($arParams["CATALOG_SUBSCRIBE_ENABLE"] == "Y") {
    ?>
    product.getSubscribed();
    
    <?php
} 
?>
    product.chooseFirstColor();
    var sizeIds = [];
    <?
	if (isset($arParams['cs']))
	{
		$cs = explode("-",$arParams['cs']);
		if (is_array($cs))
		{
			foreach($cs as $item)
			{
				?>
				sizeIds[sizeIds.length] = '<?=$item?>';
				<?
			}
		}
	}
	?>
    $(window).ready(function(){
        if ($.isArray(sizeIds)) {
            for (var i = 0; i < sizeIds.length; i++) {
                if (product.checkSize(sizeIds[i])) {
                    product.changeSize(sizeIds[i]);
                    break;
                }
            }
        }
    });
    <?php 
    if ($arResult["MAX_COUNT_SIZE"] > 0 && $arResult["MAX_COUNT_COLOR"] > 0) {
    	// choose size and color like smart site
		?>
		product.currentSizeId = <?=$arResult["MAX_COUNT_SIZE"]?>;
		$( "button[data-color='<?=$arResult["MAX_COUNT_COLOR"]?>']" ).click();
		<?php 
    }
    ?>
});
$(document).ready(function () {
        var messages = [];
        messages['NOTIFY_ERR_LOGIN'] = '<?= GetMessageJS("NOTIFY_ERR_LOGIN") ?>';
        messages['NOTIFY_ERR_MAIL'] = '<?= GetMessageJS("NOTIFY_ERR_MAIL") ?>';
        messages['NOTIFY_ERR_CAPTHA'] = '<?= GetMessageJS("NOTIFY_ERR_CAPTHA") ?>';
        messages['NOTIFY_ERR_MAIL_EXIST'] = '<?= GetMessageJS("NOTIFY_ERR_MAIL_EXIST") ?>';
        messages['NOTIFY_ERR_REG'] = '<?= GetMessageJS("NOTIFY_ERR_REG") ?>';
        messages['NOTIFY_SUBSCRIBED'] = '<?= GetMessageJS("NOTIFY_SUBSCRIBED") ?>';
        messages['NOTIFY_EMAIL_WRING1'] = '<?= GetMessageJS("NOTIFY_EMAIL_WRING1") ?>';
        messages['NOTIFY_EMAIL_WRING2'] = '<?= GetMessageJS("NOTIFY_EMAIL_WRING2") ?>';
        messages['NOTIFY_ALREADY_SUBSCRIBED'] = '<?= GetMessageJS("NOTIFY_ALREADY_SUBSCRIBED") ?>';
        messages['NOTIFY_YOU_ARE_SUBSCRIBE'] =  '<?= GetMessageJS("NOTIFY_YOU_ARE_SUBSCRIBE") ?>';
        pSubscribe.init(messages);
    }
);
</script>
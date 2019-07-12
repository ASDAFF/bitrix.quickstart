<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$ajaxUrl = $templateFolder . '/ajax.php';

if (!empty($arResult['ELEMENT']["ID"])) {
	$val = $arResult['ELEMENT'];

?>
<div class="col-left">
	<div class="col-left">
		<div class="detalet-cart">
			<div class="img-photos-demo">
				<div class="big-demo" id="photos">
				<?php /* fotos prints by js */ ?>
				</div>
                <?
                Novagroup_Classes_General_Main::getView('catalog.element','actions',array("val"=>$val));
                ?>
			</div>
			<div class="thumbs" id="thumbs">
			<?php /* fotos prints by js */ ?>
			</div>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>

	</div>
	<div class="col-right">
		<div class="card-tab-det">

        <div class="bs-tab">


        <?php

        if ($arParams['SHOW_EDIT_BUTTON'] == "Y") {
            Novagroup_Classes_General_Main::getView('catalog.element','edit_button',array("val"=>$val));
        }
        ?>

        <!-- Детальное описание -->
        <div class="detail">

            <div class="head-title"><h1><?=$val['NAME']?></h1></div>
            <?$APPLICATION->IncludeComponent(
                "novagroup:catalog.timetobuy",
                "",
                Array(
                    "IBLOCK_ID"=>$val['IBLOCK_ID'],
                    "ID"=>$val['ID']
                )
            );?>
            <?php

            $showProperties = array();

            $showProperties["VENDOR"] = array(
                "title"=>GetMessage("BRAND_LABEL"),
                "value"=>$arResult['mixData'][$val["PROPERTIES"]['VENDOR']["VALUE"] ]['NAME']
            );

            $showProperties["CML2_ARTICLE"] = array(
                "title"=>GetMessage("ARTICUL_LABEL"),
                "value"=>$val["PROPERTIES"]['CML2_ARTICLE']["VALUE"]
            );

            if (!empty($val["PROPERTIES"]['MATERIAL_DESC']["VALUE"]["TEXT"])) {

                $showProperties["MATERIAL_DESC"] = array(
                    "title" => GetMessage("MATERIAL_DESC_LABEL"),
                    "value" => $val["PROPERTIES"]['MATERIAL_DESC']["VALUE"]["TEXT"]
                );

            } else {

                $showProperties["MATERIAL_DESC"] = array(
                    "title" => GetMessage("MATERIAL_LABEL"),
                    "value" => $arResult['mixData'][$val["PROPERTIES"]['MATERIAL_LIST']["VALUE"] ]['NAME']
                );
            }

            $html = array();

            if (is_array($val["PROPERTIES"]['SAMPLES']["VALUE"]))
            foreach($val["PROPERTIES"]['SAMPLES']["VALUE"] as $subval) {
                if(isset($arResult['mixData'][$subval]['NAME']))
                    $html[] = $arResult['mixData'][$subval]['NAME'];
            }
            $showProperties["SAMPLES"] = array(
                "title"=>GetMessage("SAMPLES_LABEL"),
                "value"=>implode(", ",$html)
            );

            foreach($showProperties as $data)
            {
                Novagroup_Classes_General_Main::getView('catalog.element','properties', $data);
            }

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
                            //"border"=>"0",
                            "alt"=>$arResult['mixData'][$color]['NAME']
                        )
                    );
                }
                Novagroup_Classes_General_Main::getView('catalog.element','colors_button', array("params"=>$params) );
            }

            ?>

            <div class="choice-size "><?=GetMessage("CHOOSE_SIZE_LABEL")?>:</div>
            <div id="size-table" >
                &nbsp;<a href="#myModal8" ><?=GetMessage("SIZE_TABLE")?></a>
            </div>

            <div class="tab-choice tooltip-demo">
            <?php

            Novagroup_Classes_General_Main::includeView( SITE_DIR.'include/catalog/element/size_table.php', array("arResult"=>$arResult, "Params"=>$arParams) );
            Novagroup_Classes_General_Main::includeView(SITE_DIR.'include/catalog/element/actual-price.php', array("arResult"=>$arResult) );
            ?>
            <div class="aside addToBasket">
                <div class="set">
                    <div id="box-shelve" style="display: none;">
                        <div class="message-demo set-tool"><?=GetMessage("ADDED_TO_SHELVES")?></div>
                    </div>
                    <a href="#" id="shelve-product" data-action="addToShelve" data-elem-id="" ><?=GetMessage("ADD_TO_SHELVES")?></a>
                </div>

            </div><?
            /*
            include(SITE_DIR.'include/pSubscribe.php');
            */
            Novagroup_Classes_General_Main::includeView(SITE_DIR.'include/catalog/element/quick-buy.php');
            Novagroup_Classes_General_Main::includeView(SITE_DIR.'include/catalog/element/basket.php');
            ?>
            <div class="clear"></div>
            </div>
        </div>

        <ul class="nav nav-tabs" id="myTab1">
            <?php
            $emptyDetailTextFlag = true;

            if (!empty($val['DETAIL_TEXT'])) {
                ?>
                <li class="active"><a data-toggle="tab" href="#description"><?=GetMessage("DESCR_LABEL")?></a></li>
                <?
                $emptyDetailTextFlag = false;
            }
            ?>
            <li <?=( $emptyDetailTextFlag == true ? 'class="active"' : '')?>><a data-toggle="tab" href="#delivery"><?=GetMessage("DELIVERY_LABEL")?></a></li>
            <?php
            if ($arResult["COMMENTS_ON"] == 1) {
                ?>
                <li><a data-toggle="tab" onclick="product.getComments();" href="#comment"><?=GetMessage("COMMENTS_LABEL")?></a></li>
            <?
            }
            ?>
        </ul>

        <div class="tab-content" id="myTabContent1">

            <?php
            if ($emptyDetailTextFlag == false) {
                ?>
                <div id="description" class="tab-pane in active">
                    <h2><?=GetMessage("ABOUT_PRODUCT")?>:</h2>
                    <?=$val['DETAIL_TEXT']?>
                </div>
            <?
            }
            ?>
            <div id="delivery" class="tab-pane <?=( $emptyDetailTextFlag == true ? 'in active' : 'fade')?>">

                <?=$arResult["delivery"]?>

            </div>
            <?
            Novagroup_Classes_General_Main::includeView(SITE_DIR.'include/catalog/element/comments.php',array("arResult"=>$arResult,"ajaxUrl"=>$ajaxUrl));
            ?>
        </div>

        </div>

        <div class="clear"></div>
        <?
        if($_REQUEST['CAJAX']!=="1")
		{
            Novagroup_Classes_General_Main::getView('catalog.element','yashare',array("arResult"=>$arResult, "val"=>$val));
        }
        ?>

		</div>   
	</div>
</div>

    <?
    if(($arParams['CAJAX']!=="1") || ($_REQUEST['FULL'] == 1)){
        $APPLICATION->IncludeComponent("novagroup:catalog.element.recommend", ".default", array(
                "ELEMENT_ID" => $arResult["ID"],
                "CATALOG_IBLOCK_ID" => $arParams['CATALOG_IBLOCK_ID'],
                "OFFERS_IBLOCK_ID" => $arParams['CATALOG_OFFERS_IBLOCK_ID'],
                "CACHE_TYPE" => $arParams['CACHE_TYPE'],
                "CACHE_TIME" => $arParams['CACHE_TIME']*2,
            ), false,
            Array(
                'ACTIVE_COMPONENT' => 'Y',
            ));
    }
    ?>

	<div class="clear"></div>

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
//$detailCardView = COption::GetOptionString("main", "detail_card", "1"); $arResult['DETAIL_CARD_VIEW']

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
$rsSeoData = new \Bitrix\Iblock\InheritedProperty\ElementValues($arResult["IBLOCK_ID"], $arResult['ID']);
$arSeoData = $rsSeoData->getValues();
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
        "PRODUCT_NAME" : "<?=$arSeoData['ELEMENT_PREVIEW_PICTURE_FILE_ALT'];?>"
    }
    product.setSiteID('<?=SITE_ID?>');
    product.photoFirstTime = false;
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
        <?=$arResult["COMMENTS_ON"]?>,
        '<?=$arResult['DETAIL_PAGE_URL']?>',
        <?=($arParams["CATALOG_SUBSCRIBE_ENABLE"] == "Y" ? 'true' : 'false' )?>,
        curPhotosMiddle,
        <?=$arResult["DETAIL_CARD_VIEW"]?>,
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
    var optionsComments = {
        dataType:  'json',
        beforeSubmit:  product.checkCommentForm,
        success: function(json) {

            hideAjaxLoader();
            if (json.result == "ERROR") {

                $("#alert").attr("class", "alert alert-error").html(json.message);

            } else if (json.result == "OK") {

                product.getComments(1);
                $("#alert").attr("class", "alert alert-success").html(json.message);
                $("#commenForm" )[0].reset();
                $("#controlGroupName").attr("class", "control-group");
                $("#controlGroupText").attr("class", "control-group");
            }
        }
    };
    $('#commenForm').ajaxForm(optionsComments);
    //update comments
    $('.refreshComments').live('click', function() {

        product.getComments(1);
        return false;
    });
    $('.pagination ul li a').live('click', function(){

        product.getComments($(this).attr('data-inumpage'));

        return false;
    });
    var sizeIds = [];
    var getAnchor = location.hash;

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
    // fix for ie
    if (sizeIds.length == 0) {
        var getAnchor = location.hash;
        sizeIds = product.getSizeFromAnchorIE(getAnchor);
    }

    <?
    if ($arResult["MAX_COUNT_SIZE"] > 0 && $arResult["MAX_COUNT_COLOR"] > 0) {
    	// choose size and color like smart site
		?>
		product.currentSizeId = <?=$arResult["MAX_COUNT_SIZE"]?>;

		if (product.colorFromUrl == false) {

           // product.changeColor(<?=$arResult["MAX_COUNT_COLOR"]?>);
            $( "button[data-color='<?=$arResult["MAX_COUNT_COLOR"]?>']" ).click();
        }
		<?php 
    }
    ?>

    if ($.isArray(sizeIds)) {
        for (var i = 0; i < sizeIds.length; i++) {
            if (product.checkSize(sizeIds[i])) {
                product.changeSize(sizeIds[i]);
                break;
            }
        }
    }

    // show final photos
    product.photoFirstTime = true;
    product.changePhotos(product.photoBuffer);
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
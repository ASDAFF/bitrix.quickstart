<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<script>
var left_value;
function move_right(){
  var ind = $('#slider li:first').width()+20;
  leftInd = parseInt($('#slider ul').css('left')) - ind;
    $('#slider ul').animate({
        left: leftInd
        },500,function(){
            $('#slider li:last').after($('#slider li:first'));
            $('#slider ul').css({'left' : 0});
          }
    );
}
function move_left(){
  $('#slider li:first').before($('#slider li:last'));
  var ind = $('#slider li:first').width()+20;
  $('#slider ul').css({'left' : -ind});
  leftInd = parseInt($('#slider ul').css('left')) + ind;
    $('#slider ul').animate({
        left: leftInd
        },500
    );
}

$(document).ready(function(){
	$("a.element-add-btn").click(function(){
		$.ajax({
			type: "POST",
			async: false,
			url: "<?=$templateFolder?>/ajax.php",
			data: "elementid="+$(this).attr('elementid')+"&elementname="+$(this).attr('elementname')+"&elementprice="+$(this).attr('elementprice')+"&elementdpu="+$(this).attr('elementdpu'),
			success: function(){
				$("div.basket-add-success").slideToggle("1000").delay("3000").slideToggle("1000");
			}
		});
		return false;
	});
	$("img.colorscheme").click(function(){
		$("div.element-price center").html($(this).attr("splitprice")+" <span class='rouble'>c</span>");
		$("a.mainpic").attr("href",$(this).attr("detailpicture"));
		$("img.mainpic").attr("src",$(this).attr("detailpicture"));
		$("a.element-add-btn").attr("elementid",$(this).attr("id"));
		$("a.element-add-btn").attr("elementname",$(this).attr("offername"));
		$("a.element-add-btn").attr("elementprice",$(this).attr("price"));
		$("a.element-buy-btn").attr("href",$(this).attr("buyhref"));
		$("div.colorsselector").css("background","#ccc");
		$("#div"+$(this).attr('id')).css("background","#d01616");
	});
	$('#slider ul').css('width',3000+'px');
	$('#slider ul').css('left','0px');
	left_value = slide_width * (-1);
	var wrapW = $('#slider-wrap').width()-57;
	$('#slider').css('width',wrapW);
});
</script>

<?if($_COOKIE["pageelementcount"])
	$pageElementCount = $_COOKIE["pageelementcount"];
else
	$pageElementCount = "4";
?>

<div class="basket-add-success">
	<center><?=GetMessage("CATALOG_ELEMENT_ADD_SUCCESS")?></center>
</div>

<h1 style="font-size:30px; color:#4b423c;"><?=$arResult["NAME"]?></h1>
<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr>
	<td rowspan="2" width="280px">
		<?$elpic = CFile::ResizeImageGet($arResult["DETAIL_PICTURE"],array("width"=>"240px","height"=>"180px"),"BX_RESIZE_IMAGE_EXACT",true);?>
		<div class="element">
			<div class="element-pic">
				<center><a href="<?=$arResult['DETAIL_PAGE_URL']?>"><img src="<?=$elpic['src']?>" border="0" class="mainpic" style="max-height:180px; max-width:240px;"></a></center>
				<div class="element-zoom"><a href="<?=$arResult['DETAIL_PICTURE']['SRC']?>" class="mainpic" id="link"><img src="<?=$templateFolder?>/images/zoom.png" border="0" title="<?=GetMessage('CATALOG_ELEMENT_PICTURE_ZOOM')?>"></a></div>
			</div>
			<div class="element-price">
				<div class="element-price-left"></div>
				<center><?=strrev(chunk_split(strrev($arResult["PRICES"]["BASE"]["DISCOUNT_VALUE"]),3))?> <span class="rouble">c</span></center>
				<div class="element-price-right"></div>
			</div>
			<div class="element-buy"><a href="<?=$APPLICATION->GetCurPageParam("action=BUY&id=".$arResult['ID'],array("clear_cache","sectioncode","other"))?>" class="element-buy-btn"><?=GetMessage("CATALOG_ELEMENT_BUY")?></a></div>
			<div class="element-add"><a href="" class="element-add-btn" elementid="<?=$arResult["ID"]?>" elementname="<?=$arResult["NAME"]?>" elementprice="<?=$arResult["PRICES"]["BASE"]["DISCOUNT_VALUE"]?>" elementdpu="<?=$arResult["DETAIL_PAGE_URL"]?>"><?=GetMessage("CATALOG_ELEMENT_ADD")?></a></div>
		</div>
	</td>
	<td height='110px' valign='top'><h3 style="margin:10px 0 10px 0; font-size:18px;"><?=GetMessage("CATALOG_ELEMENT_COLORSCHEME")?></h3>
		<?foreach($arResult["OFFERS"] as $key=>$ColorScheme):
			$Color = CIBlockElement::GetList(array(),array("ID"=>$ColorScheme["DISPLAY_PROPERTIES"]["COLOR_SCHEME"]["VALUE"]),false,array("nTopCount"=>"1"),array("DETAIL_PICTURE","NAME"))->Fetch();
			$img = CFile::ResizeImageGet($Color["DETAIL_PICTURE"],array("width"=>"30px","height"=>"30px"),"BX_RESIZE_IMAGE_PROPORTIONAL_ALT",true);
			$bigImg = CFile::ResizeImageGet($Color["DETAIL_PICTURE"],array("width"=>"90px","height"=>"90px"),"BX_RESIZE_IMAGE_PROPORTIONAL_ATL",true);?>
			<div class="colorsselector" id="div<?=$ColorScheme['ID']?>">
				<img src="<?=$img['src']?>" width="30px" height="30px" border="0" class="colorscheme" id="<?=$ColorScheme['ID']?>" detailpicture="<?=CFile::GetPath($ColorScheme['DETAIL_PICTURE'])?>" price="<?=$ColorScheme["PRICES"]["BASE"]["DISCOUNT_VALUE"]?>" splitprice="<?=strrev(chunk_split(strrev($ColorScheme["PRICES"]["BASE"]["DISCOUNT_VALUE"]),3))?>" offername="<?=$ColorScheme['NAME']?>" buyhref="<?=$APPLICATION->GetCurPageParam("action=BUY&id=".$ColorScheme['ID'],array("clear_cache","sectioncode","other"))?>">
				<div style="position:relative;">
					<div class="colorDetail">
						<img src="<?=$bigImg['src']?>" width="90px" height="90px" border="0">
						<center style='padding-top:5px;'><?=$Color["NAME"]?></center>
						<div class="colorDetail-arrow"></div>
					</div>
				</div>
			</div>
		<?endforeach;?>
	</td>
</tr><tr>
	<td><h3 style="margin:10px 0 10px 0; font-size:18px;"><?=GetMessage("CATALOG_ELEMENT_PHOTOS")?></h3>
	<!--photos slider-->
		<div class="slider-wrapper">
<table cellpadding="0" cellspacing="0" border="0" width="100%" id="slider-wrap">
    <tr>
        <td width="4px" align="center"><div class="left-but-s" onclick="move_left();"></div></td>
        <td valign="top" id="td-slider">
<div class="slider-wrap" style="height:80px; width:100%;">

<div id="slider" style="height:80px; width:100%;">
<ul>
<?foreach($arResult["PROPERTIES"]["PHOTOS"]["VALUE"] as $key=>$photo){?>
    <?$file = CFile::ResizeImageGet($photo, array('height'=>80, 'width'=>106), BX_RESIZE_IMAGE_PROPORTIONAL, true);?>
    <li>
        <a href="<?=CFile::GetPath($photo)?>" id="link"><img src="<?=$file["src"]?>" height="<?=$file["height"]?>" width="<?=$file["width"]?>" align="center">
    </li>
<?}?>
</ul>
</div>

</div>
</td>
        <td width="4px" align="center"><div class="right-but-s" onclick="move_right();"></div></td>
    </tr>
</table>
</div>
	<!--end photos slider-->
	</td>
</tr></table>
		<h3 style="margin:10px 0 10px 0;"><?=GetMessage("CATALOG_ELEMENT_DESCRIPTION")?></a></h3>
		<p style='font-size:14px;'><?=$arResult["PREVIEW_TEXT"]?></p>
		<h3 style="margin:10px 0 10px 0;"><?=GetMessage("CATALOG_ELEMENT_FEATURES")?></a></h3>
		<p style='font-size:14px;'><?=$arResult["DETAIL_TEXT"]?></p>
		<h3 style="margin:10px 0 10px 0;"><?=GetMessage("CATALOG_ELEMENT_SHAREBTNS")?></a></h3>
		<?$APPLICATION->IncludeComponent("bitrix:asd.share.buttons", ".default", array(
	"ASD_ID" => $arResult["ID"],
	"ASD_TITLE" => $arResult["NAME"],
	"ASD_URL" => $arResult["DETAIL_PAGE_URL"],
	"ASD_PICTURE" => $arResult["DETAIL_PUCTURE"]["SRC"],
	"ASD_TEXT" => $arResult["PREVIEW_TEXT"],
	"ASD_LINK_TITLE" => "Расшарить в #SERVICE#",
	"ASD_INCLUDE_SCRIPTS" => array(
		0 => "FB_LIKE",
		1 => "TWITTER",
		2 => "GOOGLE",
	),
	"LIKE_TYPE" => "LIKE",
	"TW_DATA_VIA" => "",
	"SCRIPT_IN_HEAD" => "N"
	),
	false
);?>
		<h3 style="margin:30px 0 10px 0;"><?=GetMessage("CATALOG_ELEMENT_SEEALSO")?> "<?=$arResult["SECTION"]["NAME"]?>":</a></h3>
		<?global $arrAlso;
		$minAlsoPrice = $arResult["PRICES"]["BASE"]["VALUE"] - $arResult["PRICES"]["BASE"]["VALUE"]*0.20;
		$maxAlsoPrice = $arResult["PRICES"]["BASE"]["VALUE"] + $arResult["PRICES"]["BASE"]["VALUE"]*0.20;
		$arrAlso = array("SECTION_ID"=>$arResult["SECTION"]["ID"],"!ID"=>$arResult["ID"],"<=CATALOG_PRICE_1"=>$maxAlsoPrice,">=CATALOG_PRICE_1"=>$minAlsoPrice);
		$APPLICATION->IncludeComponent("bitrix:catalog.section", ".default", array(
	"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
	"IBLOCK_ID" => $arParams["IBLOCK_ID"],
	"SECTION_ID" => "",
	"SECTION_CODE" => "",
	"SECTION_USER_FIELDS" => array(
		0 => "",
		1 => "",
	),
	"ELEMENT_SORT_FIELD" => "sort",
	"ELEMENT_SORT_ORDER" => "asc",
	"FILTER_NAME" => "arrAlso",
	"INCLUDE_SUBSECTIONS" => "Y",
	"SHOW_ALL_WO_SECTION" => "Y",
	"PAGE_ELEMENT_COUNT" => "2",
	"LINE_ELEMENT_COUNT" => "2",
	"PROPERTY_CODE" => array(
		0 => "",
		1 => "",
	),
	"SECTION_URL" => "",
	"DETAIL_URL" => "",
	"BASKET_URL" => "/personal/basket.php",
	"ACTION_VARIABLE" => "action",
	"PRODUCT_ID_VARIABLE" => "id",
	"PRODUCT_QUANTITY_VARIABLE" => "quantity",
	"PRODUCT_PROPS_VARIABLE" => "prop",
	"SECTION_ID_VARIABLE" => "SECTION_ID",
	"AJAX_MODE" => "N",
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "Y",
	"AJAX_OPTION_HISTORY" => "N",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "36000000",
	"CACHE_GROUPS" => "Y",
	"META_KEYWORDS" => "-",
	"META_DESCRIPTION" => "-",
	"BROWSER_TITLE" => "-",
	"ADD_SECTIONS_CHAIN" => "N",
	"DISPLAY_COMPARE" => "N",
	"SET_TITLE" => "Y",
	"SET_STATUS_404" => "N",
	"CACHE_FILTER" => "N",
	"PRICE_CODE" => array(
		0 => "BASE",
	),
	"USE_PRICE_COUNT" => "N",
	"SHOW_PRICE_COUNT" => "1",
	"PRICE_VAT_INCLUDE" => "Y",
	"PRODUCT_PROPERTIES" => array(
	),
	"USE_PRODUCT_QUANTITY" => "N",
	"DISPLAY_TOP_PAGER" => "N",
	"DISPLAY_BOTTOM_PAGER" => "N",
	"PAGER_TITLE" => "Товары",
	"PAGER_SHOW_ALWAYS" => "N",
	"PAGER_TEMPLATE" => "",
	"PAGER_DESC_NUMBERING" => "N",
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
	"PAGER_SHOW_ALL" => "N",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>
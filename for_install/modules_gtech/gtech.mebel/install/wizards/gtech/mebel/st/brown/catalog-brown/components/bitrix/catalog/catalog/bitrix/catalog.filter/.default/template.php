<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
$rsElements = CIBlockElement::GetList(array("CATALOG_PRICE_1"=>"asc"),array("IBLOCK_ID"=>$arParams["IBLOCK_ID"],"SECTION_CODE"=>$_GET["sectioncode"]),false,false,array("ID"));
$arElement = $rsElements->Fetch();
$iblockCatalog = CCatalog::GetByIDExt($arParams["IBLOCK_ID"]);
$rsOffers = CIBlockElement::GetList(array(),array("IBLOCK_ID"=>$iblockCatalog["OFFERS_IBLOCK_ID"],"PROPERTY_".$iblockCatalog["OFFERS_PROPERTY_ID"]=>$arElement["ID"],"ACTIVE"=>"Y"),false,false,array("PROPERTY_COLOR_SCHEME"));
while($arOffer = $rsOffers->Fetch()){$arOffersColor[]=$arOffer["PROPERTY_COLOR_SCHEME_VALUE"];}
$minPriceID = $arElement["ID"];
$maxPriceID = $arElement["ID"];
while($arElement = $rsElements->Fetch()){
	$maxPriceID = $arElement["ID"];
	$rsOffers = CIBlockElement::GetList(array(),array("IBLOCK_ID"=>$iblockCatalog["OFFERS_IBLOCK_ID"],"PROPERTY_".$iblockCatalog["OFFERS_PROPERTY_ID"]=>$arElement["ID"],"ACTIVE"=>"Y"),false,false,array("PROPERTY_COLOR_SCHEME"));
	while($arOffer = $rsOffers->Fetch()){if(!in_array($arOffer["PROPERTY_COLOR_SCHEME_VALUE"],$arOffersColor)){$arOffersColor[]=$arOffer["PROPERTY_COLOR_SCHEME_VALUE"];}}
}
$minPrice = CCatalogProduct::GetOptimalPrice($minPriceID,"1",$USER->GetUserGroupArray());
$minPrice = $minPrice["DISCOUNT_PRICE"];
$maxPrice = CCatalogProduct::GetOptimalPrice($maxPriceID,"1",$USER->GetUserGroupArray());
$maxPrice = $maxPrice["DISCOUNT_PRICE"];
if($_COOKIE["curminPrice_".$_GET['sectioncode']]){$curminPrice=$_COOKIE["curminPrice_".$_GET['sectioncode']];}else{$curminPrice=$minPrice;}
if($_COOKIE["curmaxPrice_".$_GET['sectioncode']]){$curmaxPrice=$_COOKIE["curmaxPrice_".$_GET['sectioncode']];}else{$curmaxPrice=$maxPrice;}

$rsColors = CIBlockElement::GetList(array("ID"=>"ASC"),array("IBLOCK_CODE"=>"colorscheme", "ID"=>$arOffersColor),false,array("nTopCount"=>count($arOffersColor)),array("ID","DETAIL_PICTURE","NAME"));
while($arColor = $rsColors->Fetch()){$arColors[$arColor["ID"]] = $arColor["DETAIL_PICTURE"]; $arColorsName[$arColor["ID"]] = $arColor["NAME"];}
?>

<div class="aerofilter">
	<div class="aerofilterbookmark">&nbsp;</div>
	<div class="aerofilterinner">
		<table cellpadding="0" cellspacing="7px" border="0" width="94%" align="center">
			<tr><td colspan="2"><?=GetMessage("IBLOCK_FILTER_FORPRICE")?>
				<div id="aerofilter-priceranger"></div>
			</td></tr>
			<tr>
				<td id="amountmin" align="left"><?=$curminPrice?> <span class="rouble">c</span></td>
				<td id="amountmax" align="right"><?=$curmaxPrice?> <span class="rouble">c</span></td>
			</tr>
			<tr><td colspan="2"><?=GetMessage("IBLOCK_FILTER_FORCOLOR")?>
				<div id="aerofilter-colors">
					<?foreach($arColors as $key=>$Color):
						$img = CFile::ResizeImageGet($Color,array("width"=>"30px","height"=>"30px"),"BX_RESIZE_IMAGE_PROPORTIONAL_ALT",true);
						$bigImg = CFile::ResizeImageGet($Color,array("width"=>"90px","height"=>"90px"),"BX_RESIZE_IMAGE_PROPORTIONAL_ATL",true);?>
						<div class="colorsselector" id="color<?=$key?>">
							<img src="<?=$img['src']?>" width="30px" height="30px" border="0">
							<div style="position:relative;">
								<div class="colorDetail">
									<img src="<?=$bigImg['src']?>" width="90px" height="90px" border="0">
									<center style='padding-top:5px;'><?=$arColorsName[$key]?></center>
									<div class="colorDetail-arrow"></div>
								</div>
							</div>
						</div>
					<?endforeach;?>
				</div>
			</td></tr>
		</table>
	</div>
	<div class="aerofilter-submit"><?=GetMessage("IBLOCK_SET_FILTER")?></div>
</div>

<script>
$(document).ready(function(){
	$("div.aerofilterbookmark").click(function(){
		if ($("div.aerofilter").css("right")=="-219px"){
			$("div.aerofilter").animate({"right":"0px"},"1000");
		}else{
			$("div.aerofilter").animate({"right":"-219px"},"1000");
		}
	});
	$("#aerofilter-priceranger").slider({
			range: true,
			min: <?=$minPrice?>,
			max: <?=$maxPrice?>,
			values: [<?=$curminPrice?>,<?=$curmaxPrice?>],
			slide: function(event,ui){
				$.cookie("curminPrice_<?=htmlspecialchars($_GET['sectioncode'])?>",ui.values[0],{expires:"86400", path:"/"});
				$.cookie("curmaxPrice_<?=htmlspecialchars($_GET['sectioncode'])?>",ui.values[1],{expires:"86400", path:"/"});
				$("#amountmin").html(ui.values[0] + " <span class='rouble'>c</span>");
				$("#amountmax").html(ui.values[1] + " <span class='rouble'>c</span>");
			}
		});
		$("div.aerofilter-submit").click(function(){window.location.href="";});
		$("div.colorsselector").click(function(){
			if($.cookie("color_<?=htmlspecialchars($_GET['sectioncode'])?>_"+$(this).attr("id"))){
				$.cookie("color_<?=htmlspecialchars($_GET['sectioncode'])?>_"+$(this).attr("id"),null);
			}else{$.cookie("color_<?=htmlspecialchars($_GET['sectioncode'])?>_"+$(this).attr("id"),"value");}
			$(this).toggleClass("colorselected");
		});
		<?foreach($arColors as $key=>$Color){
			if($_COOKIE["color_".$_GET['sectioncode']."_color".$key]){?>
				$("#color"+<?=$key?>).addClass("colorselected");
			<?}
		}?>
});
</script>
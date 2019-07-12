<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<script>
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
	$("td.pageelementcount").click(function(){
		$.cookie("pageelementcount",$(this).text(),{expires:"86400",path:"/"});
		window.location.href="";
	});
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
<? 
$cols="2"; //cols count 
$k="0"; 
?>
<p style="font-size:16px;" align="justify"><?=$arResult["DESCRIPTION"]?></p>
<table cellpadding="3" cellspacing="0" border="0" width="100%"><tr> 
<?foreach($arResult["ITEMS"] as $arItem): $k++;
$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_EDIT"));
$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_DELETE"), array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM')));?>
    <?if($k>$cols){$k=1;?></tr><tr><?}?> 
    <td id="<?=$this->GetEditAreaId($arSection['ID']);?>" align="center" valign="top" width="<?=count(100/$cols)?>%">
		<?$elpic = CFile::ResizeImageGet($arItem["DETAIL_PICTURE"],array("width"=>"240px","height"=>"180px"),"BX_RESIZE_IMAGE_EXACT",true);?>
		<div class="element">
			<div class="element-pic">
				<center><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><img src="<?=$elpic['src']?>" border="0" style="max-height:180px; max-width:240px;"></a></center>
				<div class="element-zoom"><a href="<?=$arItem['DETAIL_PICTURE']['SRC']?>" id="link"><img src="<?=$templateFolder?>/images/zoom.png" border="0" title="<?=GetMessage('CATALOG_ELEMENT_PICTURE_ZOOM')?>"></a></div>
			</div>
			<div class="element-price">
				<div class="element-price-left"></div>
				<?=strrev(chunk_split(strrev($arItem["PRICES"]["BASE"]["DISCOUNT_VALUE"]),3))?> <span class="rouble">c</span>
				<div class="element-price-right"></div>
			</div>
			<div class="element-buy"><a href="<?=$APPLICATION->GetCurPageParam("action=BUY&id=".$arItem['ID'],array("clear_cache","sectioncode","other"))?>"><?=GetMessage("CATALOG_ELEMENT_BUY")?></a></div>
			<div class="element-add"><a href="" class="element-add-btn" elementid="<?=$arItem["ID"]?>" elementname="<?=$arItem["NAME"]?>" elementprice="<?=$arItem["PRICES"]["BASE"]["DISCOUNT_VALUE"]?>" elementdpu="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=GetMessage("CATALOG_ELEMENT_ADD")?></a></div>
		</div>
		<h3 style="margin:10px 0 10px 0;"><a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="detail-link"><?=$arItem["NAME"]?></a></h3>
    </td>
<?endforeach;?>
 
<?if($k!=$cols){for($i=1; $i<=$cols-$k; $i++){?> 
<td>&nbsp;</td> 
<?}}?>
</tr></table>

<table cellpadding="0" cellspacing="10" border="0" width="100%" style="margin-top:20px;"><tr>
	<td>
		<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
			<br /><?=$arResult["NAV_STRING"]?>
		<?endif;?>
	</td>
	<td width="260px">
		<table cellpadding="0" cellspacing="7" border="0" width="100%"><tr>
			<td><span style="font-size:20px; color:#4b423c;">Показывать по: </span></td>
			<td width="24px" height="26px" valign="middle" align="center" <?if($pageElementCount==4){?>class="curpageelementcount"<?}else{?>class="pageelementcount"<?}?>>4</td>
			<td width="24px" valign="middle" align="center" <?if($pageElementCount==8){?>class="curpageelementcount"<?}else{?>class="pageelementcount"<?}?>>8</td>
			<td width="24px" valign="middle" align="center" <?if($pageElementCount==12){?>class="curpageelementcount"<?}else{?>class="pageelementcount"<?}?>>12</td>
		</tr></table>
	</td>
</tr></table>

























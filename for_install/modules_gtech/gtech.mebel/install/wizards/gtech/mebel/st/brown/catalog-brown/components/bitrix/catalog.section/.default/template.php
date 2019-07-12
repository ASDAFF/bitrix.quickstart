<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<h1 style="font-size:30px; color:#4b423c;"><?=$arResult["NAME"]?></h1>
<? 
$cols="2"; //cols count 
$k="0"; 
?>
<table cellpadding="3" cellspacing="0" border="0" width="100%"><tr> 
<?foreach($arResult["ITEMS"] as $arItem): $k++;
$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_EDIT"));
$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_DELETE"), array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM')));?>
    <?if($k>$cols){$k=1;?></tr><tr><?}?> 
    <td id="<?=$this->GetEditAreaId($arSection['ID']);?>" align="center" valign="top" width="<?=count(100/$cols)?>%">
		<?$elpic = CFile::ResizeImageGet($arItem["DETAIL_PICTURE"],array("width"=>"240px","height"=>"180px"),"BX_RESIZE_IMAGE_EXACT",true);?>
		<div class="element2">
			<div class="element-pic2">
				<center><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><img src="<?=$elpic['src']?>" border="0" style="max-height:180px; max-width:240px;"></a></center>
				<div class="element-zoom2"><a href="<?=$arItem['DETAIL_PICTURE']['SRC']?>" id="link"><img src="<?=$templateFolder?>/images/zoom.png" border="0" title="<?=GetMessage('CATALOG_ELEMENT_PICTURE_ZOOM');?>"></a></div>
			</div>
			<div class="element-price2">
				<div class="element-price-left2"></div>
				<?=strrev(chunk_split(strrev($arItem["PRICES"]["BASE"]["DISCOUNT_VALUE"]),3))?> <span class="rouble">c</span>
				<div class="element-price-right2"></div>
			</div>
		</div>
		<h3 style="margin:10px 0 10px 0;"><a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="detail-link2"><?=$arItem["NAME"]?></a></h3>
    </td>
<?endforeach;?>
 
<?if($k!=$cols){for($i=1; $i<$cols-$k; $i++){?> 
<td>&nbsp;</td> 
<?}}?>
</tr></table>
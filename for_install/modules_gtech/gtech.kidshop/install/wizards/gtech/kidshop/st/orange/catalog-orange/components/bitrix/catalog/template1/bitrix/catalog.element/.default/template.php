<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<script type="text/javascript">
$(function() {
	$('a.zoom_pic').fancybox({
		'transitionIn': 'elastic',
		'transitionOut': 'elastic',
		'speedIn': 600,
		'speedOut': 400,
		'overlayShow': false,
		'cyclic' : true,
		'padding': 20,
		'titlePosition': 'over',
		'onComplete': function() {
			$("#fancybox-title").css({ 'top': '100%', 'bottom': 'auto' });
		}
	});
});
</script>

<table cellpadding="0" cellspacing="0" border="0" width="98%">
<tr>
	<td width="221px" valign="top">
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr><td class="detail_img_border" valign="middle" align="center">
			<?if(is_array($arResult["PREVIEW_PICTURE"]) || is_array($arResult["DETAIL_PICTURE"])):?>
                <?$arPrevImg = CFile::ResizeImageGet($arResult["PREVIEW_PICTURE"]["ID"],array("width"=>"200","height"=>"200"));?>
                <?$arDetImg = CFile::ResizeImageGet($arResult["DETAIL_PICTURE"]["ID"],array("width"=>"200","height"=>"200"));?>
				<?if(is_array($arResult["PREVIEW_PICTURE"]) && is_array($arResult["DETAIL_PICTURE"])):?>
					<img border="0" src="<?=$arPrevImg["src"]?>" alt="<?=$arResult["NAME"]?>" title="<?=$arResult["NAME"]?>" id="image_<?=$arResult["PREVIEW_PICTURE"]["ID"]?>" style="max-width:200px; max-height:200px; display:block;cursor:pointer;cursor: hand;" OnClick="document.getElementById('image_<?=$arResult["PREVIEW_PICTURE"]["ID"]?>').style.display='none';document.getElementById('image_<?=$arResult["DETAIL_PICTURE"]["ID"]?>').style.display='block'" />
					<img border="0" src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" alt="<?=$arResult["NAME"]?>" title="<?=$arResult["NAME"]?>" id="image_<?=$arResult["DETAIL_PICTURE"]["ID"]?>" style="max-width:200px; max-height:200px; display:none;cursor:pointer; cursor: hand;" OnClick="document.getElementById('image_<?=$arResult["DETAIL_PICTURE"]["ID"]?>').style.display='none';document.getElementById('image_<?=$arResult["PREVIEW_PICTURE"]["ID"]?>').style.display='block'" />
				<?elseif(is_array($arResult["DETAIL_PICTURE"])):?>
					<img border="0" src="<?=$arDetImg["src"]?>" alt="<?=$arResult["NAME"]?>" title="<?=$arResult["NAME"]?>" style="max-width:200px; max-height:200px;" />
				<?elseif(is_array($arResult["PREVIEW_PICTURE"])):?>
					<img border="0" src="<?=$arPrevImg["src"]?>" alt="<?=$arResult["NAME"]?>" title="<?=$arResult["NAME"]?>" style="max-width:200px; max-height:200px;" />
				<?endif?>
			<?endif;?>
		</td></tr></table>
		<br/>
		<?foreach($arResult["PRICES"] as $code=>$arPrice):?>
			<?if($arPrice["CAN_ACCESS"]):?>
				<div style="position:relative; height:50px;">
				<div class="price-sticker">
				<div class="price-sticker-left">&nbsp;</div>
				<p style="margin:10px 5px 0 20px; padding:0;">
					<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
						<?=GetMessage("CATALOG_PRICE")?> <span class="catalog-price"><?=$arPrice["DISCOUNT_VALUE"]?> <span style="font-family:rouble;">c</span> (<s><?=$arPrice["VALUE"]?> <span style="font-family:rouble;">c</span></s>)</span>
					<?else:?><?=GetMessage("CATALOG_PRICE")?> <span class="catalog-price"><?=$arPrice["VALUE"]?> <span style="font-family:rouble;">c</span></span><?endif;?>
				</p>
				<div class="price-sticker-right">&nbsp;</div>
				</div>
				</div>
			<?endif;?>
		<?endforeach;?>
					<form action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">
					<table border="0" cellspacing="0" cellpadding="0">
					<?if($arParams["USE_PRODUCT_QUANTITY"]):?>
						<tr>
<td colspan="1" style="padding-top: 5px;" valign="bottom"><input type="submit" name="<?echo $arParams["ACTION_VARIABLE"]."BUY"?>" value="" class="buy" title="<?=GetMessage("CATALOG_BUY")?>" >&nbsp;
</td><td valign="bottom"><a href="<?echo $arResult["ADD_URL"]?>" class="tobasket-list" rel="nofollow"  onclick="return addToCart(this, 'catalog_list_image_<?=$arResult['ID']?>', 'list','','<?=$count?>');" id="catalog_add2cart_link_<?=$arResult['ID']?>"><img src="<?=$templateFolder?>/images/basket.png" title="<?=GetMessage("CATALOG_ADD")?>" /></a>
<input type="hidden" name="<?echo $arParams["ACTION_VARIABLE"];?>" value="BUY">
					<input type="hidden" name="<?echo $arParams["PRODUCT_ID_VARIABLE"]?>" value="<?echo $arResult["ID"]?>">
</td>
						</tr>
					<?endif;?>
					</table>
					</form>
	</td>
	<td style="padding-left:20px;" valign="top">
		<p style="margin-top:5px;"><span class="detail_name"><?=$arResult["NAME"]?></span></p>
		<?if($arResult["DISPLAY_PROPERTIES"]){?>
		<p>
			<span class="detail_features"><?=GetMessage("CATALOG_ITEM_CHARACTERISTICS")?></span><br/>
			<div id="detail_features">
            <?foreach($arResult["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
            <?if($arProperty["ID"]!=10){?>
				<?=$arProperty["NAME"]?>:<b>&nbsp;<?
				if(is_array($arProperty["DISPLAY_VALUE"])):
					echo implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);
				elseif($pid=="MANUAL"):
					?><a href="<?=$arProperty["VALUE"]?>"><?=GetMessage("CATALOG_DOWNLOAD")?></a><?
				else:
					echo $arProperty["DISPLAY_VALUE"];?>
				<?endif?></b><br />
			<?}?>
			<?endforeach?>
			</div>
		</p>
		<?}?>
		<?if($arResult["DETAIL_TEXT"]){?>
		<p>
			<span class="detail_description"><?=GetMessage("CATALOG_ITEM_DESCRIPTION")?></span><br/>
			<div id="detail_description"><?=$arResult["DETAIL_TEXT"]?></div>
		</p>
		<?}?>
		<?if(is_array($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"])){?>
		<p>
			<span class="detail_morephotos"><?=GetMessage("CATALOG_ITEM_ADDPHOTOS")?></span><br/>
			<div id="detail_morephotos">
			<?$cols=5; $key=0;?>
			<table cellpadding="0" cellspacing="5" border="0"><tr>
				<?foreach($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"] as $Photo): $key++;?>
				<?if($key>$cols){$key=1;?></tr><tr><?}?>
					<?$sPhoto = CFile::ResizeImageGet($Photo,Array("width"=>"100","height"=>"100"));?>
					<td class="zoom_pic"><a href="<?=CFile::GetPath($Photo)?>" rel="zoom_pic" class="zoom_pic"><img src="<?=$sPhoto['src']?>" border="0"></a></td>
				<?endforeach;?>
			</tr></table>
			</div>
		</p>
		<?}?>
		<?$sharePic = $_SERVER["HTTP_HOST"].$arResult["DETAIL_PICTURE"]["SRC"];?>
		<p><span class="detail_share"><?=GetMessage("CATALOG_ITEM_SOCIALBUTTONS")?></span><br/></p>
		<?$APPLICATION->IncludeComponent("bitrix:asd.share.buttons", ".default", array(
	"ASD_ID" => $_REQUEST["id"],
	"ASD_TITLE" => $arResult["NAME"],
	"ASD_URL" => $_SERVER["HTTP_REFERER"],
	"ASD_PICTURE" => $sharePic,
	"ASD_TEXT" => $arResult["DETAIL_TEXT"],
	"ASD_LINK_TITLE" => "Расшарить в #SERVICE#",
	"ASD_INCLUDE_SCRIPTS" => array(
		0 => "FB_LIKE",
		1 => "TWITTER",
		2 => "GOOGLE",
	),
	"LIKE_TYPE" => "RECOMMEND",
	"TW_DATA_VIA" => "",
	"SCRIPT_IN_HEAD" => "N"
	),
	false
);?>
	</td>
</tr>
</table>
<br/>

<?global $assignFilter; $assignFilter = Array("IBLOCK_ID"=>$arParams["IBLOCK_ID"], "ID"=>$arResult["PROPERTIES"]["RECOMMEND"]["VALUE"]);?>
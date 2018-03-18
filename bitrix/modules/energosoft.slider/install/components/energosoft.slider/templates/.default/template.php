<?
######################################################
# Name: energosoft.slider                            #
# File: template.php                                 #
# (c) 2005-2012 Energosoft, Maksimov M.A.            #
# Dual licensed under the MIT and GPL                #
# http://energo-soft.ru/                             #
# mailto:support@energo-soft.ru                      #
######################################################
?>
<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<?if($arParams["ES_ORIENTATION"]=="true"):?>
	<?if($arParams["ES_SHOW_BUTTONS"]=="Y"):?>
		<div style="width:<?=$arParams["ES_BLOCK_WITDH"]+40?>px;height:<?=($arParams["ES_BLOCK_HEIGHT"]+$arParams["ES_BLOCK_MARGIN"])*$arParams["ES_COUNT"]+80?>px;">
	<?else:?>
		<div style="width:<?=$arParams["ES_BLOCK_WITDH"]?>px;height:<?=($arParams["ES_BLOCK_HEIGHT"]+$arParams["ES_BLOCK_MARGIN"])*$arParams["ES_COUNT"]?>px;">
	<?endif;?>
<?else:?>
	<?if($arParams["ES_SHOW_BUTTONS"]=="Y"):?>
		<div style="width:<?=($arParams["ES_BLOCK_WITDH"]+$arParams["ES_BLOCK_MARGIN"])*$arParams["ES_COUNT"]+80?>px;height:<?=$arParams["ES_BLOCK_HEIGHT"]+40?>px;">
	<?else:?>
		<div style="width:<?=($arParams["ES_BLOCK_WITDH"]+$arParams["ES_BLOCK_MARGIN"])*$arParams["ES_COUNT"]?>px;height:<?=$arParams["ES_BLOCK_HEIGHT"]?>px;">
	<?endif;?>
<?endif;?>
	<ul id="escarousel<?=$arResult["ID"]?>" style="display:none;" class="jcarousel-skin-energosoft<?=$arResult["ES_HASH"]?>">
		<?if($arParams["ES_USEPRELOADER"]!="Y"):?>
			<?foreach($arResult["ITEMS"] as $arElement):?>
				<?if(is_array($arElement["PREVIEW_PICTURE"])):?>
					<li>
						<div style="overflow:hidden;">
							<?if(isset($arElement["URL"])):?>
								<a href="<?=$arElement["URL"]?>" target="<?=$arParams["ES_PROPERTY_URL_TARGET"]?>"><img src="<?=$arElement["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arElement["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arElement["PREVIEW_PICTURE"]["HEIGHT"]?>" border="0"/></a>
							<?else:?>
								<img src="<?=$arElement["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arElement["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arElement["PREVIEW_PICTURE"]["HEIGHT"]?>" border="0" />
							<?endif;?>
							<?foreach($arElement["DISPLAY_PROPERTIES"] as $prop):?>
								<br/><b><?=$prop["NAME"]?>:</b>&nbsp;<?=$prop["VALUE"]?>
							<?endforeach;?>
						</div>
					</li>
				<?endif;?>
			<?endforeach;?>
		<?endif;?>
	</ul>
</div>

<script type="text/javascript">
<?
	if($arParams["ES_USEPRELOADER"]=="Y")
	{
		$js_array="";
		foreach($arResult["ITEMS"] as $arElement)
		{
			if(is_array($arElement["PREVIEW_PICTURE"]))
			{
				if(isset($arElement["URL"])) $js_array=$js_array."{hr:'".$arElement["URL"]."',tar:'".$arParams["ES_PROPERTY_URL_TARGET"]."',url:'".$arElement["PREVIEW_PICTURE"]["SRC"]."',width:".$arElement["PREVIEW_PICTURE"]["WIDTH"].",height:".$arElement["PREVIEW_PICTURE"]["HEIGHT"]."},";
				else $js_array=$js_array."{hr:'',tar:'',url:'".$arElement["PREVIEW_PICTURE"]["SRC"]."',width:".$arElement["PREVIEW_PICTURE"]["WIDTH"].",height:".$arElement["PREVIEW_PICTURE"]["HEIGHT"]."},";
			}
		}
	}
?>
<?if($arParams["ES_USEPRELOADER"]=="Y"):?>
<?if($js_array==""):?>
var escarousel<?=$arResult["ID"]?>_itemList = [];
<?else:?>
var escarousel<?=$arResult["ID"]?>_itemList = [<?=substr($js_array,0,strlen($js_array)-1)?>];
<?endif;?>
function escarousel<?=$arResult["ID"]?>_itemLoadCallback(carousel, state)
{
	for(var i = carousel.first; i <= carousel.last; i++)
	{
		if (carousel.has(i)) continue;
		var idx = carousel.index(i, escarousel<?=$arResult["ID"]?>_itemList.length); 
		var item = escarousel<?=$arResult["ID"]?>_itemList[idx-1];
		var img = "";
		if(item.hr != "" && item.tar !="") img = '<a href="' + item.hr + '" target="' + item.tar + '"><img src="' + item.url + '" width="' + item.width + '" height="' + item.height + '" border="0"/></a>';
		else img = '<img src="' + item.url + '" width="' + item.width + '" height="' + item.height + '" border="0"/>';
		carousel.add(i, img);
	}
};
<?endif;?>
jQuery(document).ready(function()
{
	<?if($arParams["ES_USEPRELOADER"]=="Y"):?>
	jQuery("#escarousel<?=$arResult["ID"]?>").jcarousel({
		size: escarousel<?=$arResult["ID"]?>_itemList.length,
		rtl: <?=$arParams["ES_RTL"]?>,
		auto: <?=$arParams["ES_AUTO"]?>,
		wrap: <?=$arParams["ES_WRAP"]=="null"?$arParams["ES_WRAP"]:"\"".$arParams["ES_WRAP"]."\""?>,
		scroll: <?=$arParams["ES_STEP"]?>,
		vertical: <?=$arParams["ES_ORIENTATION"]?>,
		animation: <?=is_string($arParams["ES_ANIMATION"])?"\"".$arParams["ES_ANIMATION"]."\"":$arParams["ES_ANIMATION"]?>,
		itemLoadCallback: {onBeforeAnimation: escarousel<?=$arResult["ID"]?>_itemLoadCallback},
		easing: <?="\"".$arParams["ES_EFFECT"]."\""?><?=$arParams["ES_SHOW_BUTTONS"]!="Y"?",buttonNextHTML: \"\", buttonPrevHTML: \"\"":""?>
	});
	<?else:?>
	jQuery("#escarousel<?=$arResult["ID"]?>").jcarousel({
		rtl: <?=$arParams["ES_RTL"]?>,
		auto: <?=$arParams["ES_AUTO"]?>,
		wrap: <?=$arParams["ES_WRAP"]=="null"?$arParams["ES_WRAP"]:"\"".$arParams["ES_WRAP"]."\""?>,
		scroll: <?=$arParams["ES_STEP"]?>,
		vertical: <?=$arParams["ES_ORIENTATION"]?>,
		animation: <?=is_string($arParams["ES_ANIMATION"])?"\"".$arParams["ES_ANIMATION"]."\"":$arParams["ES_ANIMATION"]?>,
		easing: <?="\"".$arParams["ES_EFFECT"]."\""?><?=$arParams["ES_SHOW_BUTTONS"]!="Y"?",buttonNextHTML: \"\", buttonPrevHTML: \"\"":""?>
	});
	<?endif;?>
	jQuery("#escarousel<?=$arResult["ID"]?>").show();
});
</script>
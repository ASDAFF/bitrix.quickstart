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
		<?foreach($arResult["ITEMS"] as $arElement):?>
			<li>
				<div><?=$arElement["PREVIEW_TEXT"]?></div>
			</li>
		<?endforeach;?>
	</ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function()
{
	jQuery("#escarousel<?=$arResult["ID"]?>").jcarousel({
		rtl: <?=$arParams["ES_RTL"]?>,
		auto: <?=$arParams["ES_AUTO"]?>,
		wrap: <?=$arParams["ES_WRAP"]=="null"?$arParams["ES_WRAP"]:"\"".$arParams["ES_WRAP"]."\""?>,
		scroll: <?=$arParams["ES_STEP"]?>,
		vertical: <?=$arParams["ES_ORIENTATION"]?>,
		animation: <?=is_string($arParams["ES_ANIMATION"])?"\"".$arParams["ES_ANIMATION"]."\"":$arParams["ES_ANIMATION"]?>,
		easing: <?="\"".$arParams["ES_EFFECT"]."\""?><?=$arParams["ES_SHOW_BUTTONS"]!="Y"?",buttonNextHTML: \"\", buttonPrevHTML: \"\"":""?>
	});
	jQuery("#escarousel<?=$arResult["ID"]?>").show();
});
</script>
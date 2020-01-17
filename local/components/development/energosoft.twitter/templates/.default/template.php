<?
######################################################
# Name: energosoft.twitter                           #
# File: template.php                                 #
# (c) 2005-2011 Energosoft, Maksimov M.A.            #
# Dual licensed under the MIT and GPL                #
# http://energo-soft.ru/                             #
# mailto:support@energo-soft.ru                      #
######################################################
?>
<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<div class="es-twitter-header" style="width:<?=$arParams["ES_WITDH"]?>px;">
	<div class="es-twitter-headerpanel">
		<div class="es-twitter-headertext"><?=GetMessage("ES_HEADER")?></div>
	</div>

	<?if($arParams["ES_FOLLOWBUTTON_USE"]=="Y"):?>
	<div class="es-twitter-top">
		<a href="https://twitter.com/<?=$arParams["ES_TWITTER"]?>" class="twitter-follow-button" data-show-count="<?=$arParams["ES_DATA_SHOW_COUNT"]?>" data-button="<?=$arParams["ES_DATA_BUTTON"]?>" data-text-color="<?=$arParams["ES_DATA_TEXT_COLOR"]?>" data-link-color="<?=$arParams["ES_DATA_LINK_COLOR"]?>" data-width="<?=$arParams["ES_DATA_WIDTH"]?>" data-align="<?=$arParams["ES_DATA_ALIGN"]?>" data-lang="<?=$arParams["ES_DATA_LANG"]?>"></a>
	</div>
	<?endif;?>

	<div id="esContainer<?=$arResult["ID"]?>" class="es-twitter-preloader" style="width:<?=$arParams["ES_WITDH"]?>px;height:<?=$arParams["ES_HEIGHT"]?>px;overflow:auto;">
		<div id="esTwitter<?=$arResult["ID"]?>"></div>
	</div>
	<div class="es-twitter-bottom">
		<a href="http://www.energo-soft.ru/" id="es-link" target="_blank">Energosoft</a>
	</div>
</div>

<?if($arParams["ES_TWITTER"]!=""):?>
<script type="text/javascript">
jQuery(document).ready(function()
{
	// %img%
	// %text%
	// %timeago%
	// %localedate%
	// %localetime%
	var template = "";
	template += '<div class="es-twitter-panelfull">';
	  template += '<div class="es-twitter-imagepanel">';
		template += '<img src="%img%" width="32" height="32">';
	  template += '</div>';
	  template += '<div class="es-twitter-textpanel">';
		template += '<div class="es-twitter-titlepanel">@<?=$arParams["ES_TWITTER"]?></div>';
		template += '<div class="es-twitter-timepanel">%localedate% %localetime%</div>';
		template += '<div class="es-twitter-timeagopanel">%timeago%</div>';
	  template += '</div>';
	  template += '<br/>';
	  template += '<div>%text%</div>';
	template += '</div>';

<?if($arParams["ES_TWITTER_AUTOREFRESH"]>0):?>
	window.setInterval(function(){
		jQuery.esTwitter("<?=$arResult["ID"]?>", "<?=$arParams["ES_TWITTER"]?>", <?=$arParams["ES_TWITTER_COUNT"]?>, template);
	}, <?=$arParams["ES_TWITTER_AUTOREFRESH"]?>);
	jQuery.esTwitter("<?=$arResult["ID"]?>", "<?=$arParams["ES_TWITTER"]?>", <?=$arParams["ES_TWITTER_COUNT"]?>, template);
<?else:?>
	jQuery.esTwitter("<?=$arResult["ID"]?>", "<?=$arParams["ES_TWITTER"]?>", <?=$arParams["ES_TWITTER_COUNT"]?>, template);
<?endif;?>
});
</script>
<?endif;?>
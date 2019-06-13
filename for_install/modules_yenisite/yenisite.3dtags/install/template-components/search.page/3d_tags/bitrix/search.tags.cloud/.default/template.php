<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//print_r($arParams);

if($arParams["SHOW_CHAIN"] != "N" && !empty($arResult["TAGS_CHAIN"])):
?>
<noindex>
	<div class="search-tags-chain" <?=$arParams["WIDTH"]?>><?
		foreach ($arResult["TAGS_CHAIN"] as $tags):
			?><a href="<?=$tags["TAG_PATH"]?>" rel="nofollow"><?=$tags["TAG_NAME"]?></a> <?
			?>[<a href="<?=$tags["TAG_WITHOUT"]?>" class="search-tags-link" rel="nofollow">x</a>]  <?
		endforeach;?>
	</div>
</noindex>
<?
endif;

if(is_array($arResult["SEARCH"]) && !empty($arResult["SEARCH"])):
?>
<noindex>
	<div class="search-tags-cloud" <?=$arParams["WIDTH"]?>><?
	$tags = '<tags>';

		foreach ($arResult["SEARCH"] as $key => $res)
		{

		$tags .= '<a href="'. $res["URL"]. '" style="font-size:'. $res["FONT_SIZE"]. 'pt;">'. $res["NAME"]. '</a><br />';


/*		?><a href="<?=$res["URL"]?>" style="font-size: <?=$res["FONT_SIZE"]?>px; color: #<?=$res["COLOR"]?>;px" rel="nofollow"><?=$res["NAME"]?></a> <?
*/
		}

	$tags .= '</tags>';

	if(LANG_CHARSET=="windows-1251")
		$tags = iconv ('CP1251','UTF-8',$tags);
	?></div>
</noindex>


<div id="tags">
To correctly display this element you must install FlashPlayer to include the browser Java Script.
<script type="text/javascript">
var rnumber = Math.floor(Math.random()*9999999);
var widget_so = new SWFObject("<?=$templateFolder?>/tagcloud.swf?r="+rnumber, "tagcloudflash", "<?=$arParams["YS_WIDTH"];?>", "<?=$arParams["YS_HEIGHT"];?>", "9", "#<?=$arParams["YS_BGCOLOR"];?>");
widget_so.addParam("wmode", "transparent");
widget_so.addParam("allowScriptAccess", "always");
widget_so.addVariable("tcolor", "0x<?=$arParams["YS_TEXTCOLOR"];?>"); 
widget_so.addVariable("tspeed", "<?=$arParams["YS_SPEED"];?>");
widget_so.addVariable("distr", "true"); 
widget_so.addVariable("mode", "tags");
widget_so.addVariable("tagcloud", "<?php echo urlencode($tags); ?>");
widget_so.write("tags");</script> 
</div>

<?
endif;
?>
<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

CJSCore::Init(array("ajax"));
//Let's determine what value to display: rating or average ?

if($arParams["DISPLAY_AS_RATING"] == "vote_avg")
{
	if($arResult["PROPERTIES"]["vote_count"]["VALUE"])
		$DISPLAY_VALUE = round($arResult["PROPERTIES"]["vote_sum"]["VALUE"]/$arResult["PROPERTIES"]["vote_count"]["VALUE"], 2);
	else
		$DISPLAY_VALUE = 0;
}
else
	$DISPLAY_VALUE = $arResult["PROPERTIES"]["rating"]["VALUE"];

?><div class="iblock-vote" id="vote_<?echo $arResult["ID"]?>">

<script type="text/javascript">
if(!window.voteScript) window.voteScript =
{
	do_vote: function(div, parent_id, arParams)
	{
		var r = div.id.match(/^vote_(\d+)_(\d+)$/);
		var vote_id = r[1];
		var vote_value = r[2];

		function __handler(data)
		{
			var obContainer = document.getElementById(parent_id);
			if (obContainer)
			{
				//16a Мы предполагаем, что шаблон содержит только один элемент (например div или table)
				var obResult = document.createElement("DIV");
				obResult.innerHTML = data;
				obContainer.parentNode.replaceChild(obResult.firstChild, obContainer);
			}
		}

		BX('wait_' + parent_id).innerHTML = BX.message('JS_CORE_LOADING');

		arParams['vote'] = 'Y';
		arParams['vote_id'] = vote_id;
		arParams['rating'] = vote_value;

		BX.ajax.post(
			'/bitrix/components/bitrix/iblock.vote/component.php',
			arParams,
			__handler
		);
	}
}
</script><?
?><ul class="rating JS-RatingDecor" data-rating-decor="{'classActive':'rating__item_active'}"><?
	if($arResult["VOTED"] || $arParams["READ_ONLY"]==="Y")
	{
		if($DISPLAY_VALUE)
		{
			foreach($arResult["VOTE_NAMES"] as $i=>$name)
			{
				if(round($DISPLAY_VALUE) > $i)
				{
					?><li id="vote_<?echo $arResult["ID"]?>_<?echo $i?>" class="rating__item JS-RatingDecor-Item"><a class="rating__label" href="javascript:;"><img class="rating__img" src="<?=SITE_TEMPLATE_PATH?>/images/1x1.gif" alt="" /></a></li><?
				}
				else
				{
					?><li id="vote_<?echo $arResult["ID"]?>_<?echo $i?>" class="rating__item JS-RatingDecor-Item"><a class="rating__label rating__label_empty" href="javascript:;"><img class="rating__img" src="<?=SITE_TEMPLATE_PATH?>/images/1x1.gif" alt="" /></a></li><?
				}
			}
		}
		else 
		{
			foreach($arResult["VOTE_NAMES"] as $i=>$name)
			{
				?><li id="vote_<?echo $arResult["ID"]?>_<?echo $i?>" class="rating__item JS-RatingDecor-Item"><a class="rating__label rating__label_empty" href="javascript:;"><img class="rating__img" src="<?=SITE_TEMPLATE_PATH?>/images/1x1.gif" alt="" /></a></li><?
			}
		}
	}
	else
	{
		$onclick = "voteScript.do_vote(this, 'vote_".$arResult["ID"]."', ".$arResult["AJAX_PARAMS"].")";
		if($DISPLAY_VALUE)
		{
			foreach($arResult["VOTE_NAMES"] as $i=>$name)
			{
				if(round($DISPLAY_VALUE) > $i)
				{
					?><li id="vote_<?echo $arResult["ID"]?>_<?echo $i?>" onclick="<?echo htmlspecialcharsbx($onclick)?>"  class="rating__item JS-RatingDecor-Item"><a class="rating__label" href="javascript:;"><img class="rating__img" src="<?=SITE_TEMPLATE_PATH?>/images/1x1.gif" alt="" /></a></li><?
				}
				else
				{
					?><li id="vote_<?echo $arResult["ID"]?>_<?echo $i?>" onclick="<?echo htmlspecialcharsbx($onclick)?>" class="rating__item JS-RatingDecor-Item"><a class="rating__label rating__label_empty" href="javascript:;"><img class="rating__img" src="<?=SITE_TEMPLATE_PATH?>/images/1x1.gif" alt="" /></a></li><?
				}
			}
		}
		else 
		{
			foreach($arResult["VOTE_NAMES"] as $i=>$name)
			{
				?><li id="vote_<?echo $arResult["ID"]?>_<?echo $i?>" class="rating__item JS-RatingDecor-Item"><a class="rating__label rating__label_empty" href="javascript:;"><img class="rating__img" src="<?=SITE_TEMPLATE_PATH?>/images/1x1.gif" alt="" onclick="<?echo htmlspecialcharsbx($onclick)?>" /></a></li><?
			}
		}
	}
	if($arResult["PROPERTIES"]["vote_count"]["VALUE"])
	{
		?><span id="wait_vote_<?echo $arResult["ID"]?>" class="rating-counter"><?echo GetMessage("T_IBLOCK_VOTE_RESULTS", array("#VOTES#"=>$arResult["PROPERTIES"]["vote_count"]["VALUE"] , "#RATING#"=>$DISPLAY_VALUE))?></span><?
	}
	else 
	{
		?><span id="wait_vote_<?echo $arResult["ID"]?>" class="rating-counter"><?echo GetMessage("T_IBLOCK_VOTE_NO_RESULTS")?></span><?
	}
?></ul><?
?></div><?

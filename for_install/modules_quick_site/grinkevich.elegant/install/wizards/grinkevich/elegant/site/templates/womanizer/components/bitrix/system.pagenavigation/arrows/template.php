<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$ClientID = 'navigation_'.$arResult['NavNum'];

if(!$arResult["NavShowAlways"])
{
	if ($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false))
		return;
}
?>
<div id="pager-block">
	<div class="color-sep">
		<img src="<?=SITE_TEMPLATE_PATH?>/images/grad_line.png" alt="" />
		<div></div>
	</div>


<?
$arAvailablePager = array("20", "50", "100");
$show_no = array_search($_REQUEST["show_no"], $arAvailablePager) ? $_REQUEST["show_no"] : $arAvailablePager[0];
?>
<div id="p-ddl">
								<?=GetMessage('SECT_SHOW')?>
								<select class="goto-select">
									<?foreach ($arAvailablePager as $val){?>
										<?$className = ($show_no == $val) ? ' current' : '';?>
										<option value="<?=$APPLICATION->GetCurPageParam('show_no='.$val, array('show_no'))?>"><?=$val?></option>
									<?}?>
								</select>
</div>


<?
$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");
if($arResult["bDescPageNumbering"] === true)
{
	// to show always first and last pages
	$arResult["nStartPage"] = $arResult["NavPageCount"];
	$arResult["nEndPage"] = 1;

	$sPrevHref = '';
	if ($arResult["NavPageNomer"] < $arResult["NavPageCount"])
	{
		$bPrevDisabled = false;
		if ($arResult["bSavePage"])
		{
			$sPrevHref = $arResult["sUrlPath"].'?'.$strNavQueryString.'PAGEN_'.$arResult["NavNum"].'='.($arResult["NavPageNomer"]+1);
		}
		else
		{
			if ($arResult["NavPageCount"] == ($arResult["NavPageNomer"]+1))
			{
				$sPrevHref = $arResult["sUrlPath"].$strNavQueryStringFull;
			}
			else
			{
				$sPrevHref = $arResult["sUrlPath"].'?'.$strNavQueryString.'PAGEN_'.$arResult["NavNum"].'='.($arResult["NavPageNomer"]+1);
			}
		}
	}
	else
	{
		$bPrevDisabled = true;
	}

	$sNextHref = '';
	if ($arResult["NavPageNomer"] > 1)
	{
		$bNextDisabled = false;
		$sNextHref = $arResult["sUrlPath"].'?'.$strNavQueryString.'PAGEN_'.$arResult["NavNum"].'='.($arResult["NavPageNomer"]-1);
	}
	else
	{
		$bNextDisabled = true;
	}
	?>

	<div id="p-links" class="navigation-arrows">
		Страницы: <span class="arrow">&larr;</span><span class="ctrl"> ctrl</span>&nbsp;<?if ($bPrevDisabled):?><span class="disabled"><?=GetMessage("nav_prev")?></span><?else:?><a href="<?=$sPrevHref;?>" id="<?=$ClientID?>_previous_page"><?=GetMessage("nav_prev")?></a><?endif;?>&nbsp;<?if ($bNextDisabled):?><span class="disabled"><?=GetMessage("nav_next")?></span><?else:?><a href="<?=$sNextHref;?>" id="<?=$ClientID?>_next_page"><?=GetMessage("nav_next")?></a><?endif;?>&nbsp;<span class="ctrl">ctrl </span><span class="arrow">&rarr;</span>
	</div>


		<ul id="pager" class="navigation-pages">
	<?
	$bFirst = true;
	$bPoints = false;
	do
	{
		$NavRecordGroupPrint = $arResult["NavPageCount"] - $arResult["nStartPage"] + 1;
		if ($arResult["nStartPage"] <= 2 || $arResult["NavPageCount"]-$arResult["nStartPage"] <= 1 || abs($arResult['nStartPage']-$arResult["NavPageNomer"])<=2)
		{

			if ($arResult["nStartPage"] == $arResult["NavPageNomer"]):
	?>
			<li><span class="nav-current-page"><?=$NavRecordGroupPrint?></span></li>
	<?
			elseif($arResult["nStartPage"] == $arResult["NavPageCount"] && $arResult["bSavePage"] == false):
	?>
			<li><a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"><?=$NavRecordGroupPrint?></a></li>
	<?
			else:
	?>
			<li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["nStartPage"]?>"><?=$NavRecordGroupPrint?></a></li>
	<?
			endif;
			$bFirst = false;
			$bPoints = true;
		}
		else
		{
			if ($bPoints)
			{
				?><li>...</li><?
				$bPoints = false;
			}
		}
		$arResult["nStartPage"]--;
	} while($arResult["nStartPage"] >= $arResult["nEndPage"]);

	?></ul><?
}
else
{
	// to show always first and last pages
	$arResult["nStartPage"] = 1;
	$arResult["nEndPage"] = $arResult["NavPageCount"];

	$sPrevHref = '';
	if ($arResult["NavPageNomer"] > 1)
	{
		$bPrevDisabled = false;

		if ($arResult["bSavePage"] || $arResult["NavPageNomer"] > 2)
		{
			$sPrevHref = $arResult["sUrlPath"].'?'.$strNavQueryString.'PAGEN_'.$arResult["NavNum"].'='.($arResult["NavPageNomer"]-1);
		}
		else
		{
			$sPrevHref = $arResult["sUrlPath"].$strNavQueryStringFull;
		}
	}
	else
	{
		$bPrevDisabled = true;
	}

	$sNextHref = '';
	if ($arResult["NavPageNomer"] < $arResult["NavPageCount"])
	{
		$bNextDisabled = false;
		$sNextHref = $arResult["sUrlPath"].'?'.$strNavQueryString.'PAGEN_'.$arResult["NavNum"].'='.($arResult["NavPageNomer"]+1);
	}
	else
	{
		$bNextDisabled = true;
	}
	?>
		<div id="p-links" class="navigation-arrows">
			<span class="arrow">&larr;</span><span class="ctrl"> ctrl</span>&nbsp;<?if ($bPrevDisabled):?><span class="disabled"><?=GetMessage("nav_prev")?></span><?else:?><a href="<?=$sPrevHref;?>" id="<?=$ClientID?>_previous_page"><?=GetMessage("nav_prev")?></a><?endif;?>&nbsp;<?if ($bNextDisabled):?><span class="disabled"><?=GetMessage("nav_next")?></span><?else:?><a href="<?=$sNextHref;?>" id="<?=$ClientID?>_next_page"><?=GetMessage("nav_next")?></a><?endif;?>&nbsp;<span class="ctrl">ctrl </span><span class="arrow">&rarr;</span>
		</div>

		<ul id="pager" class="navigation-pages">

	<?
	$bFirst = true;
	$bPoints = false;
	do
	{
		if ($arResult["nStartPage"] <= 2 || $arResult["nEndPage"]-$arResult["nStartPage"] <= 1 || abs($arResult['nStartPage']-$arResult["NavPageNomer"])<=2)
		{

			if ($arResult["nStartPage"] == $arResult["NavPageNomer"]):?>
				<li><a class="act"><span><?=$arResult["nStartPage"]?></span></a></li>
			<? elseif($arResult["nStartPage"] == 1 && $arResult["bSavePage"] == false): ?>
				<li><a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"><span><?=$arResult["nStartPage"]?></span></a></li>
			<? else: ?>
				<li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["nStartPage"]?>"><span><?=$arResult["nStartPage"]?></span></a></li>
			<? endif;
			$bFirst = false;
			$bPoints = true;
		}

		$arResult["nStartPage"]++;
	} while($arResult["nStartPage"] <= $arResult["nEndPage"]);

}


	?></ul><?


?>






</div>


<?CJSCore::Init();?>
<script type="text/javascript">
	BX.bind(document, "keydown", function (event) {

		event = event || window.event;
		if (!event.ctrlKey)
			return;

		var target = event.target || event.srcElement;
		if (target && target.nodeName && (target.nodeName.toUpperCase() == "INPUT" || target.nodeName.toUpperCase() == "TEXTAREA"))
			return;

		var key = (event.keyCode ? event.keyCode : (event.which ? event.which : null));
		if (!key)
			return;

		var link = null;
		if (key == 39)
			link = BX('<?=$ClientID?>_next_page');
		else if (key == 37)
			link = BX('<?=$ClientID?>_previous_page');

		if (link && link.href)
			document.location = link.href;
	});
</script>
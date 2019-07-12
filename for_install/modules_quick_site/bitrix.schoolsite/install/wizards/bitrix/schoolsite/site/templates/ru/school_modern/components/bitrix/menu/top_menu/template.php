<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if (empty($arResult))return;
?><div class="nav"><?
$prevLevel = 1;
$deniedMenuCnt = 0;
foreach($arResult as $i => $m)
{
	if($prevLevel > $m["DEPTH_LEVEL"])
	{
		$k = $prevLevel;
		for(;$k>$m["DEPTH_LEVEL"];$k--)
		{
			?></span>
					<span class="bb"><span class="cn l"></span><span class="cn r"></span></span>
				</span>
			</span><?
		}
	}
	
	if($m["IS_PARENT"])
	{
		?><span class="dropdown <?=$m["SELECTED"]?" current":""?>"><?
			if($m["PERMISSION"]>"D")
			{
					?><a href="<?=$m["LINK"]?>"><?=$m["TEXT"]?></a><?
			}
			else
			{
				?><a href="javascript:return false;" title="<?=GetMessage("MENU_ITEM_ACCESS_DENIED")?>"><?=$m["TEXT"]?></a><?
			}
			?><span class="r-border-shape">
				<span class="tb"><span class="cn l"></span><span class="cn r"></span></span>
				<span class="secondLevel"><?
	}
	else
	{
		if($m["PERMISSION"]>"D")
		{
			?><span <?=$m["SELECTED"]?" class='current'":""?>><a href="<?=$m["LINK"]?>"><?=$m["TEXT"]?></a></span><?
		}
		else
		{
			?><span><a name="javascript:return false;" class="lock" title="<?=GetMessage("MENU_ITEM_ACCESS_DENIED")?>"><?=$m["TEXT"]?></a></span><?
		}
	}
	
	$prevLevel = $m["DEPTH_LEVEL"];
}
?></div><?
?>
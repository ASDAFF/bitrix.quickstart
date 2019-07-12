<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if (empty($arResult))return;
?>
<div class="nav">
 <div class="navInner"><?
$prevLevel = 1;
$deniedMenuCnt = 0;
foreach($arResult as $i => $m)
{
	if($prevLevel > $m["DEPTH_LEVEL"])
	{
		for($k = $prevLevel;$k>$m["DEPTH_LEVEL"];$k--)
		{
			?></ul> </div> <?
		}
	}
	
	if($m["IS_PARENT"])
	{
		?>
   <div class="navItem<?=$m["SELECTED"]?" current":""?>"><a href="<?=$m["LINK"]?>"><?=$m["TEXT"]?></a>
    <ul class="level level1">
  <?
	}
	else
	{
		if($m["DEPTH_LEVEL"] == 1)
  {
   ?><div class="navItem<?=$m["SELECTED"]?" current":""?>"><a href="<?=$m["LINK"]?>"><?=$m["TEXT"]?></a></div> <?
  }
  else
  {
   ?><li<?=$m["SELECTED"]?' class="current"':""?>><a <?if($m["PERMISSION"]>"D"):?>href="<?=$m["LINK"]?>"<?else:?>name="javascript:return false;" class="lock" title="<?=GetMessage("MENU_ITEM_ACCESS_DENIED")?>"<?endif?>><?=$m["TEXT"]?></a></li><?
  }
	}
	
	$prevLevel = $m["DEPTH_LEVEL"];
}

if($prevLevel > 1)
{
 ?></ul> </div> <?
}
?>
  <span class="under"></span>
 </div>
</div><?
?>
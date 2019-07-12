<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
foreach($arResult["ITEMS"] as $v)
	if(strlen($v["ACTIVE_FROM"]) > 0)
		$arResult["byDate"][$v["ACTIVE_FROM"]][] = $v;
	else
	{
		echo "<pre>";
		print_r($v);
		echo "</pre>";
	}
?>
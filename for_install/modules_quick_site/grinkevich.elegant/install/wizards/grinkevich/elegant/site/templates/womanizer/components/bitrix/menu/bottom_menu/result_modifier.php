<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
foreach($arResult as $k => $v){
	if($v["DEPTH_LEVEL"] == "1"){
		$key = $k;
	}elseif($v["DEPTH_LEVEL"] == "2"){
		$arResult[$key]["MENUS"][] = $v;
	}
}
?>

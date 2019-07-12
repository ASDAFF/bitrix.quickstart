<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if ($USER->IsAuthorized()) {
	$text = GetMessage('LOGOUT');
	$link = $APPLICATION->GetCurDir().'?logout=yes';
	$reg_name=GetMessage('PERSONAL');
	//$reg_name=$USER->GetFullName();
	$reg_link=SITE_DIR."personal/";
	
} else {
	$text = GetMessage('LOGIN');
	$link = SITE_DIR.'personal/?logout=yes';
	$reg_name=GetMessage('REGISTRATION');
	$reg_link=SITE_DIR."personal/?register=yes";
	
}

$arResult[]=array(
	'TEXT' => $text,
	'LINK' => $link,
	'REG_NAME' => $reg_name,
	'REG_LINK' => $reg_link,
	'SELECTED' => FALSE,
	'PERMISSION' => 'X',
	'ADDITIONAL_LINKS' => array(),
	'ITEM_TYPE' => 'D',
	'ITEM_INDEX' => count($arResult)-1,
	'PARAMS' => array(),
	'DEPTH_LEVEL' => '1',
	'IS_PARENT' => FALSE
);
//var_dump($arResult);
//["TEXT"]=> string(8) "Контакты" ["LINK"]=> string(10) "/contacts/" ["SELECTED"]=> bool(false) ["PERMISSION"]=> string(1) "X" ["ADDITIONAL_LINKS"]=> array(0) { } ["ITEM_TYPE"]=> string(1) "D" ["ITEM_INDEX"]=> int(0) ["PARAMS"]=> array(0) { } ["DEPTH_LEVEL"]=> int(1) ["IS_PARENT"]=> bool(false)
?>
<? if (!empty($arResult)) { ?>
	<div id="navtop" class="noprint"><div class="links">
		<? foreach($arResult as $key=>$arItem) { ?>
			<? if ($key!=0) { ?>&nbsp;|&nbsp;<? } ?>
			<? if ($arItem['REG_LINK']) { ?><a href="<?=$arItem["REG_LINK"]?>"><?=$arItem["REG_NAME"]?></a> <? } ?>
			<? if ($key!=0) { ?>&nbsp;|&nbsp;<? } ?>
			<a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
		<? } ?>
		
	</div></div>
	
<? } ?>
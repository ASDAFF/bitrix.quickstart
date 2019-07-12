<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/**
 * @var CBitrixComponentTemplate $this
 */
$this->setFrameMode(true);
if(!$arResult["NavShowAlways"])
{
	if ($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false))
		return;
}

if ($_REQUEST["iblid"]) $iblid = (int)$_REQUEST["iblid"];
else $iblid = false;

if ($_REQUEST["secid"]) $secid = (int)$_REQUEST["secid"];
else $secid = false;

$addParams = "";


//$pageNumParam = 'iNumPage';
$pageNumParam = 'PAGEN_'.$arResult["NavNum"];
global $tab;

if ($_REQUEST["tab"])  {
	$tab = $_REQUEST["tab"];
	
}

if (!empty($tab)) {
	$addParams .= "tab=".$tab."&amp;";
	$pageNumParam = 'PAGEN_'.$arResult["NavNum"];
}


if ($_REQUEST["subtab"])  $addParams .= "subtab=".$_REQUEST["subtab"]."&amp;";

if ($_REQUEST["q"])  $addParams .= "q=".$_REQUEST["q"]."&amp;";
if ($_REQUEST["SEARCH_WHERE"])  $addParams .= "SEARCH_WHERE=".$_REQUEST["SEARCH_WHERE"]."&amp;";

if ($_REQUEST["filter_history"])  $addParams .= "filter_history=".$_REQUEST["filter_history"]."&amp;";
if ($_REQUEST["filter_canceled"])  $addParams .= "filter_canceled=".$_REQUEST["filter_canceled"]."&amp;";
if ($_REQUEST["filter_status"])  $addParams .= "filter_status=".$_REQUEST["filter_status"]."&amp;";

if ($_REQUEST["let"])  $addParams .= "let=".$_REQUEST["let"]."&amp;";


$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");

?>
<?//=$arResult["NavTitle"]?>

<?

if($arResult["bDescPageNumbering"] === true):

else:
	
	?>
	<div class="pagination pagination-right ">
	<ul>
	<?if ($arResult["NavPageNomer"] > 1):?>

		<?if($arResult["bSavePage"]):?>
			
			<li class="previous"><a href="./?<?=$addParams?><?=$pageNumParam?>=<?=($arResult["NavPageNomer"]-1)?>" data-inumpage="<?=($arResult["NavPageNomer"]-1)?>" nPageSize="<?=$arResult["NavPageSize"]?>"><?=GetMessage("nav_prev")?></a></li>

		<?else:
			/*
			?>
			<a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"><?=GetMessage("nav_begin")?></a>
			|
			<?
			*/
			if ($arResult["NavPageNomer"] > 2):?>
			<li class="previous"><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>"><?=GetMessage("nav_prev")?></a></li>
			<?else:?>
			<li class="previous"><a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"><?=GetMessage("nav_prev")?></a></li>
			<?endif?>
			
		<?endif?>

	<?else:?>
		
 		<li class="previous"><span><?=GetMessage("nav_prev")?></span></li>
	<?endif?>

	<?while($arResult["nStartPage"] <= $arResult["nEndPage"]):?>

		<?if ($arResult["nStartPage"] == $arResult["NavPageNomer"]):?>
			
			<li  class="selected"><span><?=$arResult["nStartPage"]?></span></li>
		<?
		/*elseif($arResult["nStartPage"] == 1 && $arResult["bSavePage"] == false):?>
			<a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"><?=$arResult["nStartPage"]?></a>
		<?*/
		else:?>
			<li><a data-inumpage="<?=$arResult["nStartPage"]?>" nPageSize="<?=$arResult["NavPageSize"]?>" href="./?<?=$addParams?><?=$pageNumParam?>=<?=$arResult["nStartPage"]?>"><?=$arResult["nStartPage"]?></a></li>
		<?endif?>
		<?$arResult["nStartPage"]++?>
	<?endwhile?>

	<?if($arResult["NavPageNomer"] < $arResult["NavPageCount"]):?>
		<li class="next"><a data-inumpage="<?=($arResult["NavPageNomer"]+1)?>" nPageSize="<?=$arResult["NavPageSize"]?>" href="./?<?=$addParams?><?=$pageNumParam?>=<?=($arResult["NavPageNomer"]+1)?>"><?=GetMessage("nav_next")?></a></li>
 		
	<?else:?>

		<li class="next"><span><?=GetMessage("nav_next")?></span></li>
 		
	<?endif?>
	</ul>
	</div>
	<div class="clear"></div>
<?endif;

?>

<??>
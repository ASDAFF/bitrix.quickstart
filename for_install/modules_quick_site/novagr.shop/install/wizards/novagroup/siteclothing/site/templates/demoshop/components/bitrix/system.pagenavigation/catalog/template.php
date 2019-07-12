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
if ( $iblid ) $addParams .= "iblid=".$iblid."&amp;";
if ( $iblid ) $addParams .= "secid=".$secid."&amp;";
if ($_REQUEST["tab"])  $addParams .= "tab=".$_REQUEST["tab"]."&amp;";

// if fixed changing SEARCH_WHERE = `catalog` then remove this row
if($_REQUEST['SEARCH_WHERE'] == "catalog") $_REQUEST['SEARCH_WHERE'] = "products";

// protect for double query encoding
if(isset($_REQUEST['q']) && !empty($_REQUEST['q']))
	if(!mb_strpos($_REQUEST['q'],"%",1))
		$_REQUEST['q'] = rawurlencode($_REQUEST['q']);

if( !empty($_REQUEST['q']) )
	$addParams .= "&q=".$_REQUEST['q']."&SEARCH_WHERE=".$_REQUEST['SEARCH_WHERE'];

$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");
?>

<? if($arResult["bDescPageNumbering"] === true):/*
?>

	<?=$arResult["NavFirstRecordShow"]?> <?=GetMessage("nav_to")?> <?=$arResult["NavLastRecordShow"]?> <?=GetMessage("nav_of")?> <?=$arResult["NavRecordCount"]?><br /></font>

	

	<?if ($arResult["NavPageNomer"] < $arResult["NavPageCount"]):?>
		<?if($arResult["bSavePage"]):?>
			<a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["NavPageCount"]?>" data-q="<?=($_REQUEST['q']);?>" data-where="<?=$_REQUEST['SEARCH_WHERE']?>"><?=GetMessage("nav_begin")?></a>
			|
			<a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>" data-q="<?=($_REQUEST['q']);?>" data-where="<?=$_REQUEST['SEARCH_WHERE']?>"><?=GetMessage("nav_prev")?></a>
			|
		<?else:?>
			<a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>" data-q="<?=($_REQUEST['q']);?>" data-where="<?=$_REQUEST['SEARCH_WHERE']?>"><?=GetMessage("nav_begin")?></a>
			|
			<?if ($arResult["NavPageCount"] == ($arResult["NavPageNomer"]+1) ):?>
				<a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>" data-q="<?=($_REQUEST['q']);?>" data-where="<?=$_REQUEST['SEARCH_WHERE']?>"><?=GetMessage("nav_prev")?></a>
				|
			<?else:?>
				<a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>" data-q="<?=($_REQUEST['q']);?>" data-where="<?=$_REQUEST['SEARCH_WHERE']?>"><?=GetMessage("nav_prev")?></a>
				|
			<?endif?>
		<?endif?>
	<?else:?>
		<?=GetMessage("nav_begin")?>&nbsp;|&nbsp;<?=GetMessage("nav_prev")?>&nbsp;|
	<?endif?>

	<?while($arResult["nStartPage"] >= $arResult["nEndPage"]):?>
		<?$NavRecordGroupPrint = $arResult["NavPageCount"] - $arResult["nStartPage"] + 1;?>

		<?if ($arResult["nStartPage"] == $arResult["NavPageNomer"]):?>
			<b><?=$NavRecordGroupPrint?></b>
		<?elseif($arResult["nStartPage"] == $arResult["NavPageCount"] && $arResult["bSavePage"] == false):?>
			<a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>" data-q="<?=($_REQUEST['q']);?>" data-where="<?=$_REQUEST['SEARCH_WHERE']?>"><?=$NavRecordGroupPrint?></a>
		<?else:?>
			<a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["nStartPage"]?>" data-q="<?=($_REQUEST['q']);?>" data-where="<?=$_REQUEST['SEARCH_WHERE']?>"><?=$NavRecordGroupPrint?></a>
		<?endif?>

		<?$arResult["nStartPage"]--?>
	<?endwhile?>

	|

	<?if ($arResult["NavPageNomer"] > 1):?>
		<a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>" data-q="<?=($_REQUEST['q']);?>" data-where="<?=$_REQUEST['SEARCH_WHERE']?>"><?=GetMessage("nav_next")?></a>
		|
		<a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=1" data-q="<?=($_REQUEST['q']);?>" data-where="<?=$_REQUEST['SEARCH_WHERE']?>"><?=GetMessage("nav_end")?></a>
	<?else:?>
		<?=GetMessage("nav_next")?>&nbsp;|&nbsp;<?=GetMessage("nav_end")?>
	<?endif?>

<?
*/
else:
?>
	<div class="pagination pagination-right ">
	<ul class="navig">
	<?if ($arResult["NavPageNomer"] > 1):?>
		<?if($arResult["bSavePage"]):?>
			<li class="previous"><a href="./?iNumPage=<?=($arResult["NavPageNomer"]-1)?><?=$addParams?>" data-inumpage="<?=($arResult["NavPageNomer"]-1)?>" data-q="<?=($_REQUEST['q']);?>" data-where="<?=$_REQUEST['SEARCH_WHERE']?>"><?=GetMessage("nav_prev")?></a></li>
		<?else:/*?>
			<a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>" data-q="<?=($_REQUEST['q']);?>" data-where="<?=$_REQUEST['SEARCH_WHERE']?>"><?=GetMessage("nav_begin")?></a>
			|
			<?
			*/
			//if ($arResult["NavPageNomer"] > 2):
			/*
			?>
			<a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>"><?=GetMessage("nav_prev")?></a>
			*/
			/*?>
			<li class="previous"><a href="./?<?=$addParams?>iNumPage=<?=($arResult["NavPageNomer"]-1)?>" inumpage="<?=($arResult["NavPageNomer"]-1)?>">Назад</a></li>
			<?else:*/?>
			<li class="previous"><a href="./?iNumPage=<?=($arResult["NavPageNomer"]-1)?><?=$addParams?>" data-inumpage="<?=($arResult["NavPageNomer"]-1)?>" data-q="<?=($_REQUEST['q']);?>" data-where="<?=$_REQUEST['SEARCH_WHERE']?>"><?=GetMessage("nav_prev")?></a></li>
			
			<?//endif;
		endif;
		?>
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
			<li><a data-inumpage="<?=$arResult["nStartPage"]?>" href="./?iNumPage=<?=$arResult["nStartPage"]?><?=$addParams?>" data-q="<?=($_REQUEST['q']);?>" data-where="<?=$_REQUEST['SEARCH_WHERE']?>"><?=$arResult["nStartPage"]?></a></li>
		<?endif?>
		<?$arResult["nStartPage"]++?>
	<?endwhile?>
	<?if($arResult["NavPageNomer"] < $arResult["NavPageCount"]):?>
		<li class="next"><a data-inumpage="<?=($arResult["NavPageNomer"]+1)?>" href="./?iNumPage=<?=($arResult["NavPageNomer"]+1)?><?=$addParams?>" data-q="<?=($_REQUEST['q']);?>" data-where="<?=$_REQUEST['SEARCH_WHERE']?>"><?=GetMessage("nav_next")?></a></li>
 	<?else:?>
		<li class="next"><span><?=GetMessage("nav_next")?></span></li>
	<?endif?>
	</ul>
	</div>
	<div class="clear"></div>
<?endif?>


<??>
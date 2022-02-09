<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//if($arResult["NavPageCount"] == 1)
  //  return;

$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");

?>

<div class="b-page-nav">
<span class="b-page-nav__text">Страницы:</span>

<?if($arResult["NavPageNomer"] > 1){?>
    <a class="b-page-nav__link" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["NavPageNomer"] - 1;?>">« предыдущая</a>
<?}?>

<?for($i = 1; $i <= $arResult['NavPageCount']; $i++){?>
    <?if($arResult["NavPageNomer"] == $i){?>
        <span class="b-page-nav__current"><?=$i;?></span>
    <?} else {?>
        <a class="b-page-nav__link" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$i;?>"><?=$i;?></a>
    <?}?>
<?}?>
     
<?if($arResult["NavPageNomer"] < $arResult['NavPageCount']){ ?>
    <a class="b-page-nav__link" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["NavPageNomer"] + 1;?>">следующая »</a>
<?}?>

<a class="b-page-nav__link b-page-nav__all" href="#">Показать все</a>
</div>
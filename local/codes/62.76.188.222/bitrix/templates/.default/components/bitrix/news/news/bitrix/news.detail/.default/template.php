<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="b-catalog-list_table">
<div class="b-news_detail">
	
	<?if($arParams["DISPLAY_NAME"]!="N" && $arResult["NAME"]):?>
		<h2 class="b-h2 m-no_margin"><?=$arResult["NAME"]?></h2>
	<?endif;?>
	<?if($arParams["DISPLAY_DATE"]!="N" && $arResult["DISPLAY_ACTIVE_FROM"]):?>
		<div class="b-news_list__date"><?=$arResult["DISPLAY_ACTIVE_FROM"]?></div>
	<?endif;?>
	<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arResult["DETAIL_PICTURE"])):?>
		<img src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" width="<?=$arResult["DETAIL_PICTURE"]["WIDTH"]?>" height="<?=$arResult["DETAIL_PICTURE"]["HEIGHT"]?>" alt="<?=$arResult["NAME"]?>"  title="<?=$arResult["NAME"]?>" />
	<?endif?>
	<?if(strlen($arResult["DETAIL_TEXT"])>0):?>
		<article class="b-news_detail__text">
			<?echo $arResult["DETAIL_TEXT"];?>
		</article>
	<?else:?>
		<article class="b-news_detail__text">
			<?echo $arResult["PREVIEW_TEXT"];?>
		</article>
	<?endif?>
	
	<?
	if(array_key_exists("USE_SHARE", $arParams) && $arParams["USE_SHARE"] == "Y")
	{
		?>
		<div class="news-detail-share">
			<noindex>
			<?
			$APPLICATION->IncludeComponent("bitrix:main.share", "", array(
					"HANDLERS" => $arParams["SHARE_HANDLERS"],
					"PAGE_URL" => $arResult["~DETAIL_PAGE_URL"],
					"PAGE_TITLE" => $arResult["~NAME"],
					"SHORTEN_URL_LOGIN" => $arParams["SHARE_SHORTEN_URL_LOGIN"],
					"SHORTEN_URL_KEY" => $arParams["SHARE_SHORTEN_URL_KEY"],
					"HIDE" => $arParams["SHARE_HIDE"],
				),
				$component,
				array("HIDE_ICONS" => "Y")
			);
			?>
			</noindex>
		</div>
		<?
	}
	?>
</div>
</div>

<?
$arFilter = array("IBLOCK_ID" => $arResult['IBLOCK_ID']);
// Выбиреам записи
$rs = CIBlockElement::GetList(array("DATE_ACTIVE_FROM"=>"DESC"),$arFilter,false,false,array('ID','NAME','DETAIL_PAGE_URL'));
$arNavi = array();
$i=0;
while ($ar = $rs -> GetNext()) {
   $arNavi[$i] = $ar;
   if ($ar['ID'] == $arResult['ID']) $iCurPos = $i;
   $i++;
}
// Заполняем массив информацией о следующей и предыдущей записи
// Ключ предыдущего элемента будет [$iCurPos - 1]
// Ключ следующего элемента будет [$iCurPos + 1]
// Если элементы массива с этими ключами существуют то сохраняем их, иначе осталяем пустыми
// $arLink - массив со ссылками на след и предыд новости
$arLink = array();
$arLink['PREVIOUS'] = isset($arNavi[$iCurPos - 1]) ? $arNavi[$iCurPos - 1] : '';
$arLink['NEXT'] = isset($arNavi[$iCurPos+1]) ? $arNavi[$iCurPos+1] : '';
$id_next = $arLink['NEXT']['ID'] ? $arLink['NEXT']['ID']."/" : "";
?>
<div class="b-page-nav">
	<a href="/about/news/" class="b-page-nav__link">Вернуться к списку новостей</a>
	<?if ($id_next):?>
	<a href="/about/news/<?=$id_next?>" class="b-page-nav__link b-page-nav__all">Следующая новость »</a>
	<?endif?>
</div>
<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>
<?//print_R($arResult)?>
<link  href="http://cdnjs.cloudflare.com/ajax/libs/fotorama/4.6.4/fotorama.css" rel="stylesheet">
<script src="http://cdnjs.cloudflare.com/ajax/libs/fotorama/4.6.4/fotorama.js"></script>

<div class="bj-look">

  <div class="row">
    
    <div class="col-sm-7">
	<?$fotoramaParams='data-width="100%" data-ratio="3/4"'; 
	if($arParams["GALLERY_STYLE"]=='center')
	{$fotoramaParams='data-width="100%" data-ratio="3/4"';} 
	else if($arParams["GALLERY_STYLE"]=='left')
	{$fotoramaParams='data-ratio="3/4" data-fit="contain"';}?>
      <div class="fotorama" <?=$fotoramaParams?> >
			<?$flagHasImages=false;?>
		  <?if($arResult['DETAIL_PICTURE']['SRC']):?>
			<?$flagHasImages=true;?>
			<?{$imgSrc=CFile::ResizeImageGet($arResult['DETAIL_PICTURE']['ID'], array('width'=>680, 'height'=>1200), BX_RESIZE_IMAGE_PROPORTIONAL, true);}?>
			<img src="<?=$imgSrc['src']?>">
		  <?endif?>
		  <?if(count($arResult['PROPERTIES']['MORE_PHOTO']['VALUE'])>0):?>
			<?$flagHasImages=true;?>
			<?foreach($arResult['PROPERTIES']['MORE_PHOTO']['VALUE'] as $imgId):?>
				<?{$imgSrc=CFile::ResizeImageGet($imgId, array('width'=>680, 'height'=>1200), BX_RESIZE_IMAGE_PROPORTIONAL, true);}?>
				<img src="<?=$imgSrc['src']?>">
			<?endforeach?>
		  <?endif?>
			<?if(!$flagHasImages):?>
				<img src="/upload/default.gif">
			<?endif?>
      </div>
    </div>
    
    <hr class="visible-xs i-size-M">
    
    <div class="col-sm-5">
	<?if($arResult['DETAIL_TEXT']):?>
      <h3><?=GetMessage("DESCR")?></h3>

      <p><?=$arResult['DETAIL_TEXT']?></p>
      <hr>
	<?endif?>
	
      <table class="bj-look__table">
	  <?$lotPriceWithoutDiscount=0; $lotPrice=0; $byLinkParams=''?>
	  <?foreach($arResult['PROPERTIES']['ELEMENTS_DATA'] as $k=>$elem):?>
	  <?
	  	$lotPrice+=$elem['ConvertPrice'];
		$lotPriceWithoutDiscount+=$elem['price'];
		$byLinkParams.='order[]='.$elem['id'].'&';
		//print_R($elem);
		?>
        <tr>
			<td>
				<?if($elem['DETAIL_PAGE_URL']):?>
					<a href="<?=$elem['DETAIL_PAGE_URL']?>" target="_blank"><?=$elem['NAME']?></a>
				<?else:?>
					<?=$elem['NAME']?>
				<?endif;?>
			</td>
          <td><?if($arResult['PROPERTIES']['ELEMENTS']['VALUE'][0]):?><?=CurrencyFormat($elem['ConvertPrice'], $elem["CURRENCY"]);?><?endif?></td>
        </tr>
	 <?endforeach?>
	
	<?if($arResult['PROPERTIES']['CONDITIONS_OF_PURCHASE']['~VALUE']['TEXT']):?>
        <tr>
          <td colspan="2">
			<h3 class=""><?=GetMessage("BYE_USL")?></h3>
			<?=($arResult['PROPERTIES']['CONDITIONS_OF_PURCHASE']['~VALUE']['TEXT']);?>
          </td>
        </tr>
	<?endif;?>
      </table>
	  <hr>
	 <?if($arResult['PROPERTIES']['ELEMENTS']['VALUE'][0]):?> <a href="/personal/cart/add2backet.php?<?=$byLinkParams?>" class="btn btn-default btn-100 btn-size-L"><?=GetMessage("BYE")?></a><?endif?>
    </div>
    
  </div>
  
  <hr class="hidden-xs i-size-L">
    
    <hr class="visible-xs i-size-M">
  
  <div class="bj-look__items">
    <?if($arResult['PROPERTIES']['ELEMENTS']['VALUE'][0]):?><h2><?=GetMessage("V_SOSTAVE")?></h2><?endif?>
	<?global $arrFilter;
	$arrFilter['ID']=$arResult['PROPERTIES']['ELEMENTS']['VALUE'];
	?>

	<?$APPLICATION->IncludeComponent(
	"bitrix:catalog.section", 
	"tabs", 
	array(
    'TEMPLATE_THEME' => 'green',
    'PRODUCT_DISPLAY_MODE' => 'Y',
    'ADD_PICT_PROP' => 'MORE_PHOTO',
    'LABEL_PROP' => 'NEWPRODUCT',
    'OFFER_ADD_PICT_PROP' => 'MORE_PHOTO',
    'OFFER_TREE_PROPS' => $arParams["OFFER_TREE_PROPS"],

    'PRODUCT_SUBSCRIPTION' => 'N',
    'SHOW_DISCOUNT_PERCENT' => 'Y',
    'SHOW_OLD_PRICE' => 'Y',
    'MESS_BTN_BUY' => GetMessage("BYE"),
    'MESS_BTN_ADD_TO_BASKET' => GetMessage("CT_BCE_CATALOG_ADD"),
    'MESS_BTN_SUBSCRIBE' => '',
    'MESS_BTN_DETAIL' => GetMessage("MESS_BTN_DETAIL"),
    'MESS_NOT_AVAILABLE' => GetMessage("CT_BCE_CATALOG_NOT_AVAILABLE"),
    'IBLOCK_TYPE' => $arParams["IBLOCK_TYPE"],
    'IBLOCK_ID' => $arParams["PRODUCTS_BLOCK"],
    'ELEMENT_SORT_FIELD' => $arParams["ELEMENT_SORT_FIELD"],
    'ELEMENT_SORT_ORDER' => $arParams["ELEMENT_SORT_ORDER"],
    'ELEMENT_SORT_FIELD2' => $arParams["ELEMENT_SORT_FIELD2"],
    'ELEMENT_SORT_ORDER2' => $arParams["ELEMENT_SORT_ORDER2"],
    'PROPERTY_CODE' => $arParams["PROPERTY_CODE"],

    'META_KEYWORDS' => 'UF_KEYWORDS',
    'META_DESCRIPTION' => 'UF_META_DESCRIPTION',
    'BROWSER_TITLE' => 'UF_BROWSER_TITLE',
    'INCLUDE_SUBSECTIONS' => 'Y',
    'BASKET_URL' => '/personal/cart/',
    'ACTION_VARIABLE' => 'action',
    'PRODUCT_ID_VARIABLE' => 'id',
    'SECTION_ID_VARIABLE' => 'SECTION_ID',
    'PRODUCT_QUANTITY_VARIABLE' => 'quantity',
    'PRODUCT_PROPS_VARIABLE' => 'prop',
    'FILTER_NAME' => 'arrFilter',
    'CACHE_TYPE' => 'A',
    'CACHE_TIME' => '36000000',
    'CACHE_FILTER' => '',
    'CACHE_GROUPS' => 'Y',
    'SET_TITLE' => 'N',
    'SET_STATUS_404' => 'Y',
    'DISPLAY_COMPARE' => '',
    'PAGE_ELEMENT_COUNT' => '999',
    'LINE_ELEMENT_COUNT' => '3',
    'PRICE_CODE' => $arParams["PRICE_CODE"],

    'USE_PRICE_COUNT' => '',
    'SHOW_PRICE_COUNT' => '1',
    'PRICE_VAT_INCLUDE' => '1',
    'USE_PRODUCT_QUANTITY' => '1',
    'ADD_PROPERTIES_TO_BASKET' => 'Y',
    'PARTIAL_PRODUCT_PROPERTIES' => 'N',
    'PRODUCT_PROPERTIES' => Array
        (
		'0' => 'MORE_PHOTO',
        ),

    'DISPLAY_TOP_PAGER' => '1',
    'DISPLAY_BOTTOM_PAGER' => '1',
    'PAGER_TITLE' => 'Товары',
    'PAGER_SHOW_ALWAYS' => '',
    'PAGER_TEMPLATE' => 'bejetstore',
    'PAGER_DESC_NUMBERING' => '',
    'PAGER_DESC_NUMBERING_CACHE_TIME' => '36000000',
    'PAGER_SHOW_ALL' => '',
    'OFFERS_CART_PROPERTIES' => Array
        (
            '0' => 'ARTNUMBER',
            '1' => 'COLOR_REF',
            '2' => 'SIZES_SHOES',
            '3' => 'SIZES_CLOTHES',
        ),

    'OFFERS_FIELD_CODE' => Array
        (
            '0' => 'NAME',
            '1' => 'PREVIEW_PICTURE',
            '2' => 'DETAIL_PICTURE',
        ),

    'OFFERS_PROPERTY_CODE' => Array
        (
            '0' => 'ARTNUMBER',
            '1' => 'COLOR_REF',
            '2' => 'SIZES_SHOES',
            '3' => 'SIZES_CLOTHES',
            '4' => 'MORE_PHOTO',
        ),

    'OFFERS_SORT_FIELD' => 'sort',
    'OFFERS_SORT_ORDER' => 'asc',
    'OFFERS_SORT_FIELD2' => 'id',
    'OFFERS_SORT_ORDER2' => 'desc',
    'OFFERS_LIMIT' => '0',
    'SECTION_ID' => '0',
    'SECTION_CODE' => '',
    'SECTION_URL' => '/catalog/#SECTION_CODE#/',
    'DETAIL_URL' => '/catalog/#SECTION_CODE#/#ELEMENT_CODE#/',
    'CONVERT_CURRENCY' => 'Y',
    'CURRENCY_ID' => 'RUB',
    'HIDE_NOT_AVAILABLE' => 'N',
    'ADD_SECTIONS_CHAIN' => '',

    'SHOW_ALL_WO_SECTION' => 'Y',
    'SET_LAST_MODIFIED' => '',
    'USE_MAIN_ELEMENT_SECTION' => '',
    'SET_BROWSER_TITLE' => 'Y',
    'SET_META_KEYWORDS' => 'Y',
    'SET_META_DESCRIPTION' => 'Y',
    'COMPARE_PATH' => ''
	),
	false
);?>
  </div>

</div>
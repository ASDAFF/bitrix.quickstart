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
function NumberEnd($number, $titles) {
    $cases = array (2, 0, 1, 1, 1, 2);
    return $titles[ ($number%100>4 && $number%100<20)? 2 : $cases[min($number%10, 5)] ];
}
?>

	<?
	$cols=array();
	$c=count($arResult['ITEMS']);
	for($i=0;$i<$c;$i+=5)
	{
		if($arResult['ITEMS'][$i])$cols[1][]=$arResult['ITEMS'][$i];
		if($arResult['ITEMS'][$i+1])$cols[2][]=$arResult['ITEMS'][$i+1];
		if($arResult['ITEMS'][$i+2])$cols[2][]=$arResult['ITEMS'][$i+2];
		if($arResult['ITEMS'][$i+3])$cols[3][]=$arResult['ITEMS'][$i+3];
		if($arResult['ITEMS'][$i+4])$cols[3][]=$arResult['ITEMS'][$i+4];
	}
	?>
	<!--link rel="stylesheet" type="text/css" href="/bitrix/templates/bejetstore_purple_white/components/bitrix/catalog.section/lookbook/style.css" /-->
<div class="bj-lookbook">
  <div class="row">
    
    <div class="col-sm-5 bj-lookbook__col">
	<?$descDisplayed=false;?>
	<?foreach($cols[1] as $elem):?>
      <div class="bj-lookbook__i">
	  <?if(!$descDisplayed):?>
        <div class="bj-lookbook__desc">
		
			<?=$arResult['DESCRIPTION']?>
        </div>
        <hr class="i-size-M">
		<?$descDisplayed=true;?>
	  <?endif?>
        <a href="<?=$section['SECTION_PAGE_URL']?><?=$elem['CODE']?>/" class="bj-lookbook__img">
		<?
			//////Вывод фоторгафии - анонс-детальная-первая из галараи
			if($elem['PREVIEW_PICTURE'])
			{$imgSrc=CFile::ResizeImageGet($elem['PREVIEW_PICTURE'], array('width'=>800, 'height'=>1200), BX_RESIZE_IMAGE_PROPORTIONAL, true);}
			elseif($elem['DETAIL_PICTURE'])
			{$imgSrc=CFile::ResizeImageGet($elem['DETAIL_PICTURE'], array('width'=>800, 'height'=>1200), BX_RESIZE_IMAGE_PROPORTIONAL, true);}
			elseif($elem['PROPERTIES']['MORE_PHOTO']['VALUE'][0])
			{$imgSrc=CFile::ResizeImageGet($elem['PROPERTIES']['MORE_PHOTO']['VALUE'][0], array('width'=>800, 'height'=>1200), BX_RESIZE_IMAGE_PROPORTIONAL, true);}
			else{$imgSrc='/upload/nofoto.jpg';}
		?>
         <img src="<?=$imgSrc['src']?>">
          <span class="bj-lookbook__cover">
            <h2><?=$elem['NAME']?></h2>
            
            <span class="bj-lookbook__info">
			  <?
				////Подсчет цены и вывод
				$lotPriceWithoutDiscount=0;
				$lotPrice=0;
				foreach($elem['PROPERTIES']['ELEMENTS']['VALUE'] as $thing)
				{
					$lotPrice+=$arResult['ELEMENTS'][$thing]['ConvertPrice'];
					$lotPriceWithoutDiscount+=$arResult['ELEMENTS'][$thing]['price'];
				}
				$lotPriceFormat=CurrencyFormat($lotPrice, $arParams["CURRENCY_ID"]);
				
			  ?>
			  <?if($lotPrice>0):?>
			  <span><?=count($elem['PROPERTIES']['ELEMENTS']['VALUE'])?>  <?=NumberEnd(count($elem['PROPERTIES']['ELEMENTS']['VALUE']), array(GetMessage("V1"), GetMessage("V2"), GetMessage("V3")))?></span>
              <?if($lotPriceWithoutDiscount!=$lotPrice):?>
				<span class="text-large i-new-price"><?=$lotPriceFormat?></span>
			  <?else:?>
				<span class="text-large"><?=$lotPriceFormat?></span>
			  <?endif?>
              <?if($lotPriceWithoutDiscount!=$lotPrice):?><span class="small"><s><?=CurrencyFormat($lotPriceWithoutDiscount, $arParams["CURRENCY_ID"]);?></s></span><?endif?>
              <hr>
			  <?endif?>
              <span class="btn btn-default"><?=GetMessage("SEE")?></span>
            </span>
          </span>
        </a>
      </div>
    <hr class="i-size-M visible-xs">
	<?endforeach?>
	
    </div>
    
    <div class="col-sm-3 bj-lookbook__col">
    
	<?foreach($cols[2] as $elem):?>
      <div class="bj-lookbook__i">

        <a href="<?=$section['SECTION_PAGE_URL']?><?=$elem['CODE']?>/" class="bj-lookbook__img">
		<?
			//////Вывод фоторгафии - анонс-детальная-первая из галараи
			if($elem['PREVIEW_PICTURE'])
			{$imgSrc=CFile::ResizeImageGet($elem['PREVIEW_PICTURE'], array('width'=>800, 'height'=>1200), BX_RESIZE_IMAGE_PROPORTIONAL, true);}
			elseif($elem['DETAIL_PICTURE'])
			{$imgSrc=CFile::ResizeImageGet($elem['DETAIL_PICTURE'], array('width'=>800, 'height'=>1200), BX_RESIZE_IMAGE_PROPORTIONAL, true);}
			elseif($elem['PROPERTIES']['MORE_PHOTO']['VALUE'][0])
			{$imgSrc=CFile::ResizeImageGet($elem['PROPERTIES']['MORE_PHOTO']['VALUE'][0], array('width'=>800, 'height'=>1200), BX_RESIZE_IMAGE_PROPORTIONAL, true);}
			else{$imgSrc='/upload/nofoto.jpg';}
		?>
         <img src="<?=$imgSrc['src']?>">
          <span class="bj-lookbook__cover">
            <h2><?=$elem['NAME']?></h2>
            
            <span class="bj-lookbook__info">
			  <?
				////Подсчет цены и вывод
				$lotPriceWithoutDiscount=0;
				$lotPrice=0;
				foreach($elem['PROPERTIES']['ELEMENTS']['VALUE'] as $thing)
				{
					$lotPrice+=$arResult['ELEMENTS'][$thing]['ConvertPrice'];
					$lotPriceWithoutDiscount+=$arResult['ELEMENTS'][$thing]['price'];
				}
				$lotPriceFormat=CurrencyFormat($lotPrice, $arParams["CURRENCY_ID"]);
				
			  ?>
			  <?if($lotPrice>0):?>
			  <span><?=count($elem['PROPERTIES']['ELEMENTS']['VALUE'])?>  <?=NumberEnd(count($elem['PROPERTIES']['ELEMENTS']['VALUE']), array(GetMessage("V1"), GetMessage("V2"), GetMessage("V3")))?></span>
              <?if($lotPriceWithoutDiscount!=$lotPrice):?>
				<span class="text-large i-new-price"><?=$lotPriceFormat?></span>
			  <?else:?>
				<span class="text-large"><?=$lotPriceFormat?></span>
			  <?endif?>
              <?if($lotPriceWithoutDiscount!=$lotPrice):?><span class="small"><s><?=CurrencyFormat($lotPriceWithoutDiscount, $arParams["CURRENCY_ID"]);?></s></span><?endif?>
              <hr>
			  <?endif?>
              <span class="btn btn-default"><?=GetMessage("SEE")?></span>
            </span>
          </span>
        </a>
      </div>
    <hr class="i-size-M visible-xs">
	<?endforeach?>
    
    </div>
    
    <div class="col-sm-4 bj-lookbook__col">
    
	<?foreach($cols[3] as $elem):?>
      <div class="bj-lookbook__i">

        <a href="<?=$section['SECTION_PAGE_URL']?><?=$elem['CODE']?>/" class="bj-lookbook__img">
		<?
			//////Вывод фоторгафии - анонс-детальная-первая из галараи
			if($elem['PREVIEW_PICTURE'])
			{$imgSrc=CFile::ResizeImageGet($elem['PREVIEW_PICTURE'], array('width'=>800, 'height'=>1200), BX_RESIZE_IMAGE_PROPORTIONAL, true);}
			elseif($elem['DETAIL_PICTURE'])
			{$imgSrc=CFile::ResizeImageGet($elem['DETAIL_PICTURE'], array('width'=>800, 'height'=>1200), BX_RESIZE_IMAGE_PROPORTIONAL, true);}
			elseif($elem['PROPERTIES']['MORE_PHOTO']['VALUE'][0])
			{$imgSrc=CFile::ResizeImageGet($elem['PROPERTIES']['MORE_PHOTO']['VALUE'][0], array('width'=>800, 'height'=>1200), BX_RESIZE_IMAGE_PROPORTIONAL, true);}
			else{$imgSrc='/upload/nofoto.jpg';}
		?>
         <img src="<?=$imgSrc['src']?>">
          <span class="bj-lookbook__cover">
            <h2><?=$elem['NAME']?></h2>
            
            <span class="bj-lookbook__info">
			  <?
				////Подсчет цены и вывод
				$lotPriceWithoutDiscount=0;
				$lotPrice=0;
				foreach($elem['PROPERTIES']['ELEMENTS']['VALUE'] as $thing)
				{
					$lotPrice+=$arResult['ELEMENTS'][$thing]['ConvertPrice'];
					$lotPriceWithoutDiscount+=$arResult['ELEMENTS'][$thing]['price'];
				}
				$lotPriceFormat=CurrencyFormat($lotPrice, $arParams["CURRENCY_ID"]);
				
			  ?>
			  <?if($lotPrice>0):?>
			  <span><?=count($elem['PROPERTIES']['ELEMENTS']['VALUE'])?>  <?=NumberEnd(count($elem['PROPERTIES']['ELEMENTS']['VALUE']), array(GetMessage("V1"), GetMessage("V2"), GetMessage("V3")))?></span>
              <?if($lotPriceWithoutDiscount!=$lotPrice):?>
				<span class="text-large i-new-price"><?=$lotPriceFormat?></span>
			  <?else:?>
				<span class="text-large"><?=$lotPriceFormat?></span>
			  <?endif?>
              <?if($lotPriceWithoutDiscount!=$lotPrice):?><span class="small"><s><?=CurrencyFormat($lotPriceWithoutDiscount, $arParams["CURRENCY_ID"]);?></s></span><?endif?>
              <hr>
			  <?endif?>
              <span class="btn btn-default"><?=GetMessage("SEE")?></span>
            </span>
          </span>
        </a>
      </div>
    <hr class="i-size-M visible-xs">
	<?endforeach?>
    
    </div>
    
  </div>
  </div>

  <?=$arResult["NAV_STRING"];?>
  <hr class=" i-size-L">
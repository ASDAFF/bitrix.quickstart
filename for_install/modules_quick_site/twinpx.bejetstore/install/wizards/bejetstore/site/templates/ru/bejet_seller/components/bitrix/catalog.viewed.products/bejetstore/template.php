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
<?//print_R($arResult['ITEMS'])?>
<?if($arResult['ITEMS'] && count($arResult['ITEMS'])>0):?>
<hr class="i-size-L">
<div class="bj-hr-heading">
	<div class="bj-hr-heading__content"><span><?=GetMessage("YOU_LOOK");?></span></div>
</div>

<div class="row">
<?foreach($arResult['ITEMS'] as $it):?>
<?
	$fotosArr=array();
	$fotosArr[]=$it['PREVIEW_PICTURE']['ID'];
	$fotosArr[]=$it['DETAIL_PICTURE']['ID'];
	$fotosArr[]=$it['PROPERTIES']['MORE_PHOTO']['VALUE'][0];
	$fotosArr[]=$it['PROPERTIES']['MORE_PHOTO']['VALUE'][1];
	$i=0;
	$fotosResArr=array();
	$fotoId='';
	foreach($fotosArr as $fotoId)
	{		
		if($fotoId)
		{
			$fotosResArr[]=$fotoId;
			$i++;
		}
		if($i==2)break;
	}
	
	$price=array_shift($it['PRICES']);
	//print_r($price);
	//print_r($fotosResArr);
	if(!$price['PRINT_VALUE'])
	{
		if($it['OFFERS'][0]['NAME'])
		{
			$price=array_shift($it['OFFERS'][0]['PRICES']);
			echo '<!----price цена взята из первого торгового предложения---->';
		}
	
	}
	
?>
	<div class="col-sm-2">
		<div class="bj-product-card">
			<a href="<?=$it['DETAIL_PAGE_URL']?>" class="bj-product-card__img i-changable">
				<?if($fotosResArr[1]):?><span style="background-image: url('<?=cfile::GetPath($fotosResArr[1])?>');"></span><?endif?>
				<?if($fotosResArr[0]):?><span style="background-image: url('<?=cfile::GetPath($fotosResArr[0])?>');"></span><?endif?>
			</a>
			<div class="bj-product-card__title bj-table">
				<div class="bj-table-row">
				<div class="bj-table-cell">
					<span class="bj-product-card__title__wrapper">
					<a href="<?=$it['DETAIL_PAGE_URL']?>"><?=$it['NAME']?></a>
					</span>
				</div>
				</div>
			</div>
			<?if($price['DISCOUNT_DIFF_PERCENT']>0):?>
				<div class="bj-product-card__price bj-price bx_catalog_item_price text-center">
					<div class="text-large text-info bx_price"><?=$price['PRINT_DISCOUNT_VALUE']?></div>
					<div class="text-small"><s><?=$price['PRINT_VALUE']?></s></div>
				</div>
			<?else:?>
				<div class="bj-product-card__price bj-price bx_catalog_item_price text-center">
					<div class="text-large bx_price"><?=$price['PRINT_VALUE']?></div>
				</div>
			<?endif?>
		</div>
	</div>
 
<?endforeach?>

</div>
<?endif?>
<?//print_R($arResult);?>
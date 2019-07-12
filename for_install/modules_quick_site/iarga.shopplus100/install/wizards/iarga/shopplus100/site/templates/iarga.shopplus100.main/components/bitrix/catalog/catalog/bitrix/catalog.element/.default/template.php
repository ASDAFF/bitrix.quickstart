<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
global $templateFolder;
$price = iarga::getprice($arResult['ID']);
$oldprice = $arResult['PROPERTIES']['oldprice']['VALUE'];
$action = CIBlockElement::GetById($arResult['PROPERTIES']['action']['VALUE'])->GetNext();
if(!$arResult['PREVIEW_PICTURE']> 0) $arResult['PREVIEW_PICTURE'] = $arResult['DETAIL_PICTURE'];
?>

<div class="item detail">
	<div class="title-container">
		<p class="title"><?=$arResult['NAME']?></p>
		<p class="price">
			<?if($oldprice):?>
				<p class="price"><span class="old"><?=iarga::prep($oldprice)?> <?=GetMessage("VALUTE_MEDIUM")?></span> <span class="new"><?=iarga::prep($price)?> <?=GetMessage("VALUTE_MEDIUM")?></span></p>
			<?else:?>
				<?=iarga::prep($price)?> <?=GetMessage("VALUTE_MEDIUM")?>
			<?endif;?>
		</p>
	</div><!--.title-container-end-->

	<?if($arResult['PREVIEW_PICTURE']>0 || $arResult['PROPERTIES']['photo']['VALUE'][0]>0):?>
		<div class="img open">
			<div class="preview"></div>
			<?if($action):?>
				<strong class="type action question"><a href="<?=$action['DETAIL_PAGE_URL']?>" title="<?=$action['NAME']?>"><?=GetMessage("STOCK")?> <img src="<?=$templateFolder?>/images/icon-question.png" alt="?" style="max-width: 18px;"></a><i></i></strong>
			<?endif;?>
			<div class="royalSlider rsDefault">
				<?if($arResult['PREVIEW_PICTURE']>0):?>
					<a class="rsImg" data-rsBigImg="<?=iarga::res($arResult['PREVIEW_PICTURE'],900,900,1)?>" href="<?=iarga::res($arResult['PREVIEW_PICTURE'],400,400,1)?>"></a>
				<?endif;?>
				<?foreach($arResult['PROPERTIES']['photo']['VALUE'] as $i=>$photo):?>
					<a class="rsImg" data-rsBigImg="<?=iarga::res($photo,900,900,1)?>" href="<?=iarga::res($photo,400,400,1)?>"></a>
				<?endforeach;?>				
			</div><!--.royalSlider-end-->
		</div><!--.img-end-->
	<?endif;?>
	
	<div class="summary">
		<div class="description-preview">
			<h2><a href="#!<?=$arResult['DETAIL_PAGE_URL']?>" data-rel="<?=$arResult['ID']?>"><?=$arResult['NAME']?></a></h2>
		</div><!--.description-preview-end-->
		<a href="#" class="bt_card to_cart" data-rel="<?=$arResult['ID']?>"><?=GetMessage('TO_BASKET')?></a>
		<div class="amount-card" data-rel="<?=$arResult['ID']?>">
			<span><?=GetMessage('NUM')?>:</span>
			<input type="text" class="inp-text" value="1">
			<span class="remove-card"><a href="#"><?=GetMessage('DELETE')?></a></span>
		</div><!--.amount-card-end-->
		<a href="#" class="add-favorites to_fav" data-rel="<?=$arResult['ID']?>"><?=GetMessage('TO_FAV')?></a>
		<div class="description-extended">
			<p><?//=$arResult['PREVIEW_TEXT']?></p>
			<div class="features">

				<?$n = 0;
				$maxn = 6;
				if(sizeof($arResult['PROPERTIES']['vars']['VALUE']) + sizeof($arResult['DISPLAY_PROPERTIES']) + sizeof($arResult['PROPERTIES']['props']['VALUE']) < 9) $maxn = 9;?>
				<?foreach($arResult['DISPLAY_PROPERTIES'] as $code=>$prop):
					$n++;?>
						<?if($n == $maxn+1):?>
							<div class="hide">
								<span class="ellipsis">...</span>
									<div>
						<?endif;?>
					<p>
						<span><?=$prop['NAME']?> -</span> 
						<?if(is_array($prop['DISPLAY_VALUE'])):?>
							<?foreach($prop['DISPLAY_VALUE'] as $i=>$val){
								if($i>0) print ', '; print $val;
							}?>
						<?else:?>
							<?=$prop['DISPLAY_VALUE']?>
						<?endif?>
					</p>
				<?endforeach;?>
				<?foreach($arResult['PROPERTIES']['vars']['VALUE'] as $i=>$var):
					$n++;?>
						<?if($n == $maxn+1):?>
							<div class="hide">
								<span class="ellipsis">...</span>
									<div>
						<?endif;?>
					<p><span><?=$var?></span></p>
				<?endforeach;?>

				<?foreach($arResult['PROPERTIES']['props']['VALUE'] as $i=>$prop):
					$n++;?>
						<?if($n == $maxn+1):?>
							<div class="hide">
								<span class="ellipsis">...</span>
									<div>
						<?endif;?>
					<p><span><?=$prop?> -</span> <?=$arResult['PROPERTIES']['props']['DESCRIPTION'][$i]?></p>
				<?endforeach;?>

				<?if($n >= $maxn+1):?>
						</div>
					</div><!--.hide-end-->
					<a href="#" class="dashed show-link" data-show="<?=GetMessage('SHOW_ALL')?>" data-hide="<?=GetMessage('HIDE_ALL')?>"><?=GetMessage('SHOW_ALL')?></a>
				<?endif;?>
			</div><!--.features-end-->
		</div><!--.description-extended-end-->
	</div><!--.summary-end-->

	<?if($arResult['DETAIL_TEXT']!=""):?>
		<div class="clr"></div>
		<br><br>
		<h2><?=GetMessage("DESCRIPTION")?></h2>
		<p><?=$arResult['DETAIL_TEXT']?></p>
	<?endif;?>
	
	<div class="clr"></div>
	
</div><!--.item-end-->
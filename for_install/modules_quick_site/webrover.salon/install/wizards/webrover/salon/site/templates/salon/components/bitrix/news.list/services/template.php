<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div id="collage">
	<a href="javascript:void(0);" class="navigation prev"></a>
	<a href="javascript:void(0);" class="navigation next"></a>
	<div class="round">
		<div class="round-top-left"></div>
		<div class="round-top-right"></div>
		<div class="round-bottom-left"></div>
		<div class="round-bottom-right"></div>
		<div class="carousel">
			<ul>
				<? foreach ($arResult['ITEMS'] as $arItem): ?>
					<li>
						<a href="<?=$arItem['PROPERTIES']['LINK']['VALUE']?>" title="<?=$arItem['NAME']?>">
							<img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" alt="<?=$arItem['NAME']?>">
							<span class="description">
								<span class="round round-top">
									<span class="round-left"><span class="round-right"><span class="round-repeat"></span></span></span>
								</span>
								<span class="bg"></span>
								<span class="text"><?=$arItem['NAME']?></span>
								<span class="round round-bottom">
									<span class="round-left"><span class="round-right"><span class="round-repeat"></span></span></span>
								</span>											
							</span>
						</a>
					</li>
				<? endforeach ?>
			</ul>
		</div>
	</div>
</div>
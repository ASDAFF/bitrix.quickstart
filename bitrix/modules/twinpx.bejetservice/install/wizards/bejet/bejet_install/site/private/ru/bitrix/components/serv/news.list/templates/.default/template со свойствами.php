<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<pre>
<?
//print_r($arResult["ITEMS"]);

$c=ceil(count($arResult["ITEMS"])/3);

$nElem=0;
?>
</pre>


<section id="services-block" class="i-page-block i-wide-content">
	<a name="services" class="b-anchor"></a>
	<div class="i-page-content">
		<div class="i-page-content-pad">
			<div class="b-icon i-list"></div>
			<?$j=0; for($j;$j<$c; $j++):?>
			<div class="b-grid">
			<?$i=0; for($i;$i<3; $i++):?>
			<?if($arResult["ITEMS"][$nElem]['ID']):?>
				<div class="b-grid__column">
					<h2><?=$arResult["ITEMS"][$nElem]['NAME']?></h2>
					<p><?=$arResult["ITEMS"][$nElem]['PREVIEW_TEXT']?></p>
					<div class="b-price-block">
					<?foreach($arResult["ITEMS"][$nElem]['PROPERTIES'] as $prop):?>
						<?if($prop['VALUE']):?><div class="b-price-block__item"><?=$prop['NAME']?> – <?=$prop['VALUE']?></div><?endif?>
					<?endforeach;?>
					</div>
				</div>
			<?endif;?>
				<?$nElem++; endfor; ?>
			</div>
			<?endfor?>

		</div>
	</div>
</section>
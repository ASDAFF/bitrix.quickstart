<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<ul class="sidenav">
	<? foreach($arResult['SECTIONS'] as $value) { ?> 	 
		<li><a href="<?=$arResult['CATEGORIES_URL']?><?=$value['CODE']?>/"><?=htmlspecialcharsback($value['NAME'])?></a></li>
	<? } ?>
</ul>
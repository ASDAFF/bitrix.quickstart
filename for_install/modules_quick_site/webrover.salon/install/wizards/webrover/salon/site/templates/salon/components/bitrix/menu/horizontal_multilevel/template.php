<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
	$arItems = array();
	$lastID = 0;
	foreach ($arResult as $key => $arItem){
		if ($arItem['DEPTH_LEVEL'] == 1){
			$collect = $arItem['IS_PARENT'];
			$arItems[$key] = $arItem;
			$lastID = $key;
		} else 
			$arItems[$lastID]['ITEMS'][] = $arItem;
	}
?>
<div id="mainmenu">
	<ul class="horizontal">
		<? foreach ($arItems as $arItem): ?>
			<li class="<?=(count($arItem['ITEMS']) > 0 ? 'multilevel' : '')?>">
				<div class="round<?=($arItem['SELECTED'] ? ' selected' : '')?>">
					<div class="round-left">
						<div class="round-right">
							<div class="round-repeat">
								<a href="<?=$arItem['LINK']?>"><?=$arItem['TEXT']?></a>
							</div>
						</div>
					</div>
				</div>
				<? if (count($arItem['ITEMS']) > 0): ?>
					<div class="sublevel">
						<div class="round round-top">
							<div class="round-left"><div class="round-right"><div class="round-repeat"></div></div></div>
						</div>
						<div class="bg">
							<ul>
								<? foreach ($arItem['ITEMS'] as $subItem): ?>
									<li><a href="<?=$subItem['LINK']?>"><?=$subItem['TEXT']?></a></li>
								<? endforeach ?>
							</ul>
						</div>		
						<div class="round round-bottom">
							<div class="round-left"><div class="round-right"><div class="round-repeat"></div></div></div>
						</div>
					</div>
				<? endif ?>
			</li>
		<? endforeach ?>
	</ul>
</div>
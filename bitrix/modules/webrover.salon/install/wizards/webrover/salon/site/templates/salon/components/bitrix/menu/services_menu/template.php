<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if (!empty($arResult)):?>
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
	<h2 class="magenta"><?=GetMessage("SERVISES");?></h2>
	<div id="leftmenu">
		<ul>
			<? foreach ($arItems as $arItem): ?>
				<li>
					<a href="<?=$arItem['LINK']?>" title="<?=GetMessage("GO_TO_SECTION");?> <?=$arItem['TEXT']?>" class="<?=($arItem['SELECTED'] ? 'selected' : '')?>"><?=$arItem['TEXT']?></a>
					<? if ($arItem['SELECTED'] && count($arItem['ITEMS']) > 0): ?>
						<ul>
							<? foreach ($arItem['ITEMS'] as $subItem): ?>
								<li><a href="<?=$subItem['LINK']?>" title="<?=GetMessage("GO_TO_DESCRIPTION");?>" class="<?=($subItem['SELECTED'] ? 'selected' : '')?>"><?=$subItem['TEXT']?></a></li>
							<? endforeach ?>
						</ul>
					<? endif ?>
				</li>
			<? endforeach ?>
		</ul>
	</div>
<? endif ?>
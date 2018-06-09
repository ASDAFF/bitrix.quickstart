<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
?>
<? if (sizeof($arResult['ITEMS']) > 0):?>
	<ul class="object_menu__list">
		<? foreach ($arResult['ITEMS'] as $item):?>
			<? if ($item['SELECTED']):?>
				<li class="active">
					<a href="<?=$item['LINK'];?>"<?=($item['PARAMS']['target'] ? ' target="' . $item['PARAMS']['target'] . '"' : '');?>><?=$item['TEXT'];?></a>
					<? if (sizeof($item['CHILD'])):?>
						<ul class="submenu">
						<? foreach ($item['CHILD'] as $subitem):?>
							<li<?=($subitem['SELECTED'] ? ' class="active"' : '');?>> <a href="<?=$subitem['LINK'];?>"<?=($subitem['PARAMS']['target'] ? ' target="' . $subitem['PARAMS']['target'] . '"' : '');?>><?=$subitem['TEXT'];?></a></li>
						<? endforeach;?>
						</ul>
					<? endif;?>
				</li>
			<? else:?>
				<li><a href="<?=$item['LINK'];?>"<?=($item['PARAMS']['target'] ? ' target="' . $item['PARAMS']['target'] . '"' : '');?>><?=$item['TEXT'];?></a></li>
			<? endif;?>
		<? endforeach;?>
	</ul>
<? endif; ?>
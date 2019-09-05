<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

if (empty($arResult)) {
	return;
}

use Site\Main\Util;

$showLevel = function($node) use ($arParams, &$showLevel) {
	if ($node['DEPTH_LEVEL'] > $arParams['MAX_LEVEL']) {
		return;
	}
	?>
	<ul>
		<?
		foreach($node['CHILDREN'] as $item) {
			$isBlank = strpos($item['LINK'], '//') !== false;
			?>
			<li>
				<?if ($item['PERMISSION'] > 'D') {
					?>
					<a class="<?=$item['SELECTED'] ? 'active ' : ''?>" href="<?=$item['LINK']?>">
						<?=$item['TEXT']?>
					</a>
					<?
				} else {
					?>
					<a class="disabled <?=$item['SELECTED'] ? 'active ' : ''?>" title="<?=GetMessage('MENU_ITEM_ACCESS_DENIED')?>">
						<?=$item['TEXT']?>
					</a>
					<?
				}?>
				<?
				$showLevel($item);
				?>
			</li>
			<?
		}
		?>
	</ul>
	<?
};
?>

<div class="menu menu-ul">
	<?$showLevel(Util::menuToTree($arResult))?>
</div>
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
	
	foreach($node['CHILDREN'] as $item) {
		$isBlank = strpos($item['LINK'], '//') !== false;
		?>
		<div class="col-xs-6 col-sm-4 col-md-2">
			<a class="<?=$item['SELECTED'] ? 'active ' : ''?>" href="<?=$item['LINK']?>">
				<?=$item['TEXT']?>
			</a>
		</div>
		<?
		$showLevel($item);
	}
};
?>

<div class="menu menu-default">
	<div class="row">
		<?$showLevel(Util::menuToTree($arResult))?>
	</div>
</div>
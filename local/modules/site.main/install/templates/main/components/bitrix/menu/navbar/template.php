<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

if (empty($arResult)) {
	return;
}

use Site\Main\Util;

$showLevel = function($node) use ($arParams, &$showLevel) {
	if ($node['DEPTH_LEVEL'] > $arParams['MAX_LEVEL']
		|| !$node['CHILDREN']
	) {
		return;
	}
	
	$attrs = '';
	if ($node['DEPTH_LEVEL'] == 0) {
		$attrs = ' class="nav navbar-nav navbar-nav-menu"';
	} elseif($node['DEPTH_LEVEL'] == 1) {
		$attrs = ' class="dropdown-menu" role="menu"';
	}
	?>
	<ul<?=$attrs?>>
		<?foreach($node['CHILDREN'] as $item) {
			$isBlank = strpos($item['LINK'], '//') !== false;
			$isDropDown = $item['DEPTH_LEVEL'] == 1 && $item['CHILDREN'];
			?>
			<li class="<?=$item['SELECTED'] ? 'active ' : ''?><?=$isDropDown ? 'dropdown ' : ''?>">
				<?if ($isDropDown) {
					?>
					<a class="dropdown-toggle" href="#" data-toggle="dropdown">
						<?=$item['TEXT']?>
						<span class="caret"></span>
					</a>
					<?
				} else {
					?>
					<a href="<?=$item['LINK']?>"<?=$isBlank ? ' target="_blank"' : ''?>>
						<?=$item['TEXT']?>
					</a>
					<?
				}?>
				<?$showLevel($item)?>
			</li>
			<?
		}?>
	</ul>
	<?
};

$showLevel(Util::menuToTree($arResult));
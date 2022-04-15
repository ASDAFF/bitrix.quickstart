<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
?>
<? if (sizeof($arResult['ITEMS']) > 0):?>
<div class="nav-layout">
	<nav class="nav clear">
		<ul class="nav__list">
			<? foreach ($arResult['ITEMS'] as $i => $arLevel1):?>
			<li class="nav__item nav__item_<?=($i + 1); ?>">
				<a class="nav__link nav__link<?=($arLevel1['SELECTED'] ? '_active' : ''); ?>" href="http://<?=$_SERVER['HTTP_HOST']?><?=($arLevel1['LINK'] == '/ru/news/' ? '/ru/news/press_releases/2015/' : $arLevel1['LINK']); ?>"><?=$arLevel1['TEXT']; ?></a>
				<? if (!$arLevel1['SELECTED']):?>
					<div class="nav-sub-layout">
						<? if (sizeof($arLevel1['CHILD'])):?>
							<div class="nav-sub nav-sub_<?=($i + 1); ?> nav-sub_aside_left clear">
								<nav class="menu-inner-layout">
									<ul class="menu-inner">
										<? foreach ($arLevel1['CHILD'] as $j => $arLevel2):?>
											<li class="menu-inner__item menu-inner__item_<?=($j + 1); ?>">
												<a class="menu-inner__link" href="http://<?=$_SERVER['HTTP_HOST']?><?=$arLevel2['LINK']; ?>"><?=$arLevel2['TEXT']; ?></a>
											</li>
										<? endforeach; ?>
									</ul>
								</nav>
								<div class="nav-sub__content clear">
									<?$APPLICATION -> IncludeComponent("bitrix:main.include", "", Array("AREA_FILE_SHOW" => "file", "PATH" => GetDirPath($arLevel1['LINK']) . '/sect_menu.php', "EDIT_TEMPLATE" => ""), false); ?>
								</div>
							</div>
						<? else: ?>
							<div class="nav-sub nav-sub_<?=($i + 1); ?> nav-sub_aside_no clear">
								<?$APPLICATION -> IncludeComponent("bitrix:main.include", "", Array("AREA_FILE_SHOW" => "file", "PATH" => GetDirPath($arLevel1['LINK']) . '/sect_menu.php', "EDIT_TEMPLATE" => ""), false); ?>
							</div>
						<? endif; ?>
					</div>
				<? endif; ?>
			</li>
			<? endforeach; ?>
		</ul>
	</nav>
</div>

<? endif; ?>
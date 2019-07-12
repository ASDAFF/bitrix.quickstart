<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<div id="menu">
	<nav class="clearfix">
		<?php if ($arParams['SHOW_SEARCH'] === 'Y'): ?>
			<?php
			$APPLICATION->IncludeComponent("bitrix:search.form", "header", array(
	"PAGE" => "#SITE_DIR#search/index.php",
	"PLACEHOLDER_TEXT" => GetMessage('PRMEDIA_MM_MH_PLACEHOLDER_TEXT')
	),
	false
);
			?>
		<?php endif; ?>
		<ul class="clearfix">
			<?php foreach ($arResult as $menuItem): ?>
				<?php if (!empty($menuItem['IS_CATALOG'])): ?>
					<li class="mob-catalog-menu">
						<div class="smenu">
							<?php echo PrmediaMinimarketMenuHeaderShowLink($menuItem); ?>
							<div class="trg"></div>
						</div>
						<?php
						$APPLICATION->IncludeComponent("prmedia:minimarket.section.list", "menu", array(
	"IBLOCK_TYPE_ID" => "prmedia_minimarket",
	"IBLOCK_ID" => PRMEDIA_MINIMARKET_CATALOG_IBLOCK_ID,
	"TOP_DEPTH" => "2",
	"SECTION_URL" => "",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "36000000",
	"CACHE_GROUPS" => "N"
	),
	false
);
						?>
					</li>
				<?php else: ?>
					<li>
						<? if (empty($menuItem['ITEMS'])): ?>
							<?php echo PrmediaMinimarketMenuHeaderShowLink($menuItem); ?>
						<? else: ?>
							<div class="smenu">
								<?php echo PrmediaMinimarketMenuHeaderShowLink($menuItem); ?>
								<div class="trg"></div>
							</div>
							<ul class="clearfix">
								<?php foreach ($menuItem['ITEMS'] as $menuSubItem): ?>
									<li>
										<div class="dash"></div>
										<?php echo PrmediaMinimarketMenuHeaderShowLink($menuSubItem); ?>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
	</nav>
</div>
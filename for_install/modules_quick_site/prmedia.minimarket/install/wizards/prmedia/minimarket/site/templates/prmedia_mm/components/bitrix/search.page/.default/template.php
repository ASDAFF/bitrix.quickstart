<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>

<div class="search-page">
	<form action="<?php echo $APPLICATION->GetCurPage() ?>" method="get" class="search-page-form clearfix">
		<input type="text" name="q" value="<?php echo htmlspecialcharsbx($arResult['REQUEST']['QUERY']) ?>" />
		<input type="submit" value="<?php echo GetMessage('SEARCH_GO') ?>" />
		<input type="hidden" name="how" value="r" />
	</form>

	<?php if (isset($arResult['REQUEST']['ORIGINAL_QUERY'])): ?>
		<div class="search-language-guess">
			<?php echo GetMessage('CT_BSP_KEYBOARD_WARNING', array('#query#' => '<a href="' . $arResult['ORIGINAL_QUERY_URL'] . '">' . $arResult['REQUEST']['ORIGINAL_QUERY'] . '</a>')) ?>
		</div>
	<?php endif; ?>

	<?php if (count($arResult['SEARCH']) > 0): ?>
		<?php if ($arParams['DISPLAY_TOP_PAGER'] != 'N'): ?>
			<?php echo $arResult['NAV_STRING'] ?>
		<?php endif; ?>
		<div class="search-page-items">
			<?php foreach ($arResult['SEARCH'] as $item): ?>
				<div class="search-page-item">
					<a href="<?php echo $item['URL'] ?>"><?php echo $item['TITLE_FORMATED'] ?></a>
					<p><?php echo $item['BODY_FORMATED'] ?></p>
				</div>
			<?php endforeach; ?>
		</div>
		<?php if ($arParams['DISPLAY_BOTTOM_PAGER'] != 'N'): ?>
			<?php echo $arResult['NAV_STRING'] ?>
		<?php endif; ?>
	<?php else: ?>
		<?php ShowNote(GetMessage('SEARCH_NOTHING_TO_FOUND')); ?>
	<?php endif; ?>
</div>
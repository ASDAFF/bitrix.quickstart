<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>

<?php
// return if only one page and not show always
if (!$arResult['NavShowAlways'])
{
	if ($arResult['NavRecordCount'] == 0 || ($arResult['NavPageCount'] == 1 && !$arResult['NavShowAll']))
		return;
}

$strNavQueryString = ($arResult['NavQueryString'] != '' ? $arResult['NavQueryString'] . '&amp;' : '');
$strNavQueryStringFull = ($arResult['NavQueryString'] != '' ? '?' . $arResult['NavQueryString'] : '');
?>

<ul class="pagenav">
	<?php if (!empty($arResult['NavTitle'])): ?><li class="pagenav-name"><?php echo GetMessage('nav_pages') ?><?php endif; ?>
	<?php if ($arResult["bDescPageNumbering"] === true): ?>
		<?php if ($arResult['NavPageNomer'] < $arResult['NavPageCount']): ?>
			<?php if ($arResult['bSavePage']): ?>
				<li><a href="<?php echo $arResult['sUrlPath'] ?>?<?php echo $strNavQueryString ?>PAGEN_<?php echo $arResult['NavNum'] ?>=<?php echo ($arResult['NavPageNomer'] + 1) ?>"><?php echo GetMessage('nav_prev') ?></a>
				<?php else: ?>
					<?php if ($arResult['NavPageCount'] == ($arResult['NavPageNomer'] + 1)): ?>
					<li><a href="<?php echo $arResult['sUrlPath'] ?><?php echo $strNavQueryStringFull ?>"><?php echo GetMessage('nav_prev') ?></a>
					<?php else: ?>
					<li><a href="<?php echo $arResult['sUrlPath'] ?>?<?php echo $strNavQueryString ?>PAGEN_<?php echo $arResult['NavNum'] ?>=<?php echo ($arResult['NavPageNomer'] + 1) ?>"><?php echo GetMessage('nav_prev') ?></a>
					<?php endif; ?>
				<?php endif; ?>
			<?php else: ?>
			<li><?php echo GetMessage('nav_prev') ?>
			<?php endif; ?>
			<? while ($arResult['nStartPage'] >= $arResult['nEndPage']): ?>
				<? $NavRecordGroupPrint = $arResult['NavPageCount'] - $arResult['nStartPage'] + 1; ?>
				<?php if ($arResult['nStartPage'] == $arResult['NavPageNomer']): ?>
				<li><b><?php echo $NavRecordGroupPrint ?></b>
				<?php elseif ($arResult['nStartPage'] == $arResult['NavPageCount'] && $arResult['bSavePage'] == false): ?>
				<li><a href="<?php echo $arResult['sUrlPath'] ?><?php echo $strNavQueryStringFull ?>"><?php echo $NavRecordGroupPrint ?></a>
				<?php else: ?>
				<li><a href="<?php echo $arResult['sUrlPath'] ?>?<?php echo $strNavQueryString ?>PAGEN_<?php echo $arResult['NavNum'] ?>=<?php echo $arResult['nStartPage'] ?>"><?php echo $NavRecordGroupPrint ?></a>
				<?php endif; ?>
				<? $arResult['nStartPage']-- ?>
			<? endwhile; ?>
			<?php if ($arResult['NavPageNomer'] > 1): ?>
			<li><a href="<?php echo $arResult['sUrlPath'] ?>?<?php echo $strNavQueryString ?>PAGEN_<?php echo $arResult['NavNum'] ?>=<?php echo ($arResult['NavPageNomer'] - 1) ?>"><?php echo GetMessage('nav_next') ?></a>
			<?php else: ?>
			<li><?php echo GetMessage('nav_next') ?>
			<?php endif; ?>
		<?php else: ?>
			<?php if ($arResult['NavPageNomer'] > 1): ?>
				<?php if ($arResult['bSavePage']): ?>
				<li><a href="<?php echo $arResult['sUrlPath'] ?>?<?php echo $strNavQueryString ?>PAGEN_<?php echo $arResult['NavNum'] ?>=<?php echo ($arResult['NavPageNomer'] - 1) ?>"><?php echo GetMessage('nav_prev') ?></a>
				<?php else: ?>
					<?php if ($arResult['NavPageNomer'] > 2): ?>
					<li><a href="<?php echo $arResult['sUrlPath'] ?>?<?php echo $strNavQueryString ?>PAGEN_<?php echo $arResult['NavNum'] ?>=<?php echo ($arResult['NavPageNomer'] - 1) ?>"><?php echo GetMessage('nav_prev') ?></a>
					<?php else: ?>
					<li><a href="<?php echo $arResult['sUrlPath'] ?><?php echo $strNavQueryStringFull ?>"><?php echo GetMessage('nav_prev') ?></a>
					<?php endif; ?>
				<?php endif; ?>
			<?php else: ?>
			<li><?php echo GetMessage('nav_prev') ?>
			<?php endif; ?>
			<? while ($arResult['nStartPage'] <= $arResult['nEndPage']): ?>
				<?php if ($arResult['nStartPage'] == $arResult['NavPageNomer']): ?>
				<li><b><?php echo $arResult['nStartPage'] ?></b>
				<?php elseif ($arResult['nStartPage'] == 1 && $arResult['bSavePage'] == false): ?>
				<li><a href="<?php echo $arResult['sUrlPath'] ?><?php echo $strNavQueryStringFull ?>"><?php echo $arResult['nStartPage'] ?></a>
				<?php else: ?>
				<li><a href="<?php echo $arResult['sUrlPath'] ?>?<?php echo $strNavQueryString ?>PAGEN_<?php echo $arResult['NavNum'] ?>=<?php echo $arResult['nStartPage'] ?>"><?php echo $arResult['nStartPage'] ?></a>
				<?php endif; ?>
				<? $arResult['nStartPage']++ ?>
			<? endwhile ?>
			<?php if ($arResult['NavPageNomer'] < $arResult['NavPageCount']): ?>
			<li><a href="<?php echo $arResult['sUrlPath'] ?>?<?php echo $strNavQueryString ?>PAGEN_<?php echo $arResult['NavNum'] ?>=<?php echo ($arResult['NavPageNomer'] + 1) ?>"><?php echo GetMessage('nav_next') ?></a>
			<?php else: ?>
			<li><?php echo GetMessage('nav_next') ?>
			<?php endif; ?>
		<?php endif; ?>
		<?php if ($arResult['bShowAll']): ?>
		<li>
		<noindex>
			<?php if ($arResult['NavShowAll']): ?>
				<a href="<?php echo $arResult['sUrlPath'] ?>?<?php echo $strNavQueryString ?>SHOWALL_<?php echo $arResult['NavNum'] ?>=0" rel="nofollow"><?php echo GetMessage("nav_paged") ?></a>
			<?php else: ?>
				<a href="<?php echo $arResult['sUrlPath'] ?>?<?php echo $strNavQueryString ?>SHOWALL_<?php echo $arResult['NavNum'] ?>=1" rel="nofollow"><?php echo GetMessage("nav_all") ?></a>
			<?php endif; ?>
		</noindex>
	<?php endif; ?>
</ul>
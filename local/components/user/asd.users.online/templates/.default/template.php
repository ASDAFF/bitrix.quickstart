<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

if (method_exists($this, 'setFrameMode')) {
	$this->setFrameMode(true);
}
?>

<b><?= GetMessage('ASD_ONLINE_ALL')?>:</b>
<?= GetMessage('ASD_ONLINE_REG')?> &mdash;

<?foreach ($arResult['USERS'] as $arItem):?>
	<?if (strlen($arItem['PATH'])):?>
		<a class="feed-com-name" id="anchor_<?=$arItem['ID']?>" href="<?= $arItem['PATH']?>"><?=$arItem['NAME']?></a>,
		<?= $arItem['POPUP'];?>
	<?else:?>
		<?= $arItem['NAME']?>,
	<?endif;?>
<?endforeach;?>
<?if (empty($arResult['USERS'])) echo GetMessage('ASD_NOONE') . ', '?>
<?= GetMessage('ASD_ONLINE_GUESTS')?> &mdash; <?= $arResult['GUESTS']>0 ? $arResult['GUESTS'] : GetMessage('ASD_NOONE')?>.

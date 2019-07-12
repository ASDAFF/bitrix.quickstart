<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>
<? if (count($arResult['ERRORS'])>0) {
	ShowError(implode('<br />', $arResult['ERRORS']));
}

if ($arResult['LINK']) { ?>
	<a href="#" id="antispam"<? if ($arResult['ELEMENT_CLASS']) {?>class="<?=$arResult['ELEMENT_CLASS']?>"<? } ?>></a>
<? } else { ?>
	<span id="antispam"<? if ($arResult['ELEMENT_CLASS']) {?>class="<?=$arResult['ELEMENT_CLASS']?>"<? } ?>></span>
<? } ?>

<script type="text/javascript">
	antispan({
		'name': '<?=$arResult["NAME"]?>',
		'domen': '<?=$arResult["DOMEN"]?>',
		'zone': '<?=$arResult["ZONE"]?>',
		'id': 'antispam'
	});
</script>

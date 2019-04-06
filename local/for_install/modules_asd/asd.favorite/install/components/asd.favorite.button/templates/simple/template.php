<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>

<?if (!$GLOBALS['ASD_FAV_SHOWED']):?>
	<script type="text/javascript">
		var sTitleAddFav = '<?= CUtil::JSescape(GetMessage('ASD_TPL_ADD_FAV')) ?>';
		var sTitleDelFav = '<?= CUtil::JSescape(GetMessage('ASD_TPL_DEL_FAV')) ?>';
		var sMessDeniedGuest = '<?= CUtil::JSescape(GetMessage('ASD_TPL_DENIED_GUEST')) ?>';
		var sType = '<?= $arParams['FAV_TYPE'] ?>';
	<?if ($arParams['GET_COUNT_AFTER_LOAD'] != 'Y'):?>
		var sSessId = '<?= bitrix_sessid()?>';
		var bGuest = <?= $USER->IsAuthorized() ? 'false' : 'true' ?>;
	<?else:?>
		var sSessId ='';
		var bGuest = '';
	<?endif;?>
	</script>
<?endif;?>

<a href="#" data-skey="<?= md5($arParams['FAV_TYPE'] . $arParams['ELEMENT_ID'] . Coption::GetOptionString('asd.favorite', 'js_key')) ?>"
	id="asd_fav_<?= $arParams['ELEMENT_ID'] ?>"
	class="asd_fav_simple<?= $arResult['FAVED']=='Y' ? ' asd_faved' : ''?>">
	<?= $arResult['FAVED']=='Y' ? GetMessage('ASD_TPL_DEL_FAV') : GetMessage('ASD_TPL_ADD_FAV')?>
</a>

<?if ($arParams['GET_COUNT_AFTER_LOAD'] == 'Y'):?>
	<script type="text/javascript">
	<?if (!$GLOBALS['ASD_FAV_SHOWED']):?>
		var asd_fav_afterload = 'Y';
		var asd_fav_IDs = new Array();
	<?endif;?>
		asd_fav_IDs.push(<?= $arParams['ELEMENT_ID']?>);
	</script>
<?endif;?>
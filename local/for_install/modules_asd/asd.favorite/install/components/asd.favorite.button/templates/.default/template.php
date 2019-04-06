<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?= $arResult['STYLES'];?>
<? if (!$GLOBALS['ASD_FAV_SHOWED']): ?>
    <script type="text/javascript">
        var sTitleAddFav = '<?= CUtil::JSescape(GetMessage('ASD_TPL_ADD_' . $arParams['BUTTON_TYPE_UPP'])) ?>';
        var sTitleDelFav = '<?= CUtil::JSescape(GetMessage('ASD_TPL_DEL_' . $arParams['BUTTON_TYPE_UPP'])) ?>';
        var sMessDeniedGuest = '<?= CUtil::JSescape(GetMessage('ASD_TPL_DENIED_GUEST')) ?>';
        var sType = '<?= $arParams['FAV_TYPE'] ?>';
        var sButton = '<?= $arParams['BUTTON_TYPE'] ?>';
    <? if ($arParams['GET_COUNT_AFTER_LOAD'] != 'Y'): ?>
	    var sSessId = '<?= bitrix_sessid() ?>';
	    var bGuest = <?= $USER->IsAuthorized() ? 'false' : 'true' ?>;
    <? else: ?>
	    var sSessId ='';
	    var bGuest = '';
    <? endif; ?>
    </script>
<? endif; ?>
<div data-skey="<?= md5($arParams['FAV_TYPE'] . $arParams['ELEMENT_ID'] . Coption::GetOptionString('asd.favorite', 'js_key')) ?>" class="asd_<?= $arParams['BUTTON_TYPE'] ?>_button<? if ($arResult['FAVED'] == 'Y') echo ' asd_' . $arParams['BUTTON_TYPE'] . 'ed' ?>" id="asd_fav_<?= $arParams['ELEMENT_ID'] ?>" title="<?= GetMessage('ASD_TPL_' . ($arResult['FAVED'] == 'Y' ? 'DEL' : 'ADD') . '_' . $arParams['BUTTON_TYPE_UPP']) ?>"></div>
<div class="asd_fav_count" id="asd_count_<?= $arParams['ELEMENT_ID'] ?>"><?= $arResult['COUNT'] ?></div>
<div class="asd_fav_clear"></div>
<? if ($arParams['GET_COUNT_AFTER_LOAD'] == 'Y'): ?>
    <script type="text/javascript">
    <? if (!$GLOBALS['ASD_FAV_SHOWED']): ?>
	    var asd_fav_afterload = 'Y';
	    var asd_fav_IDs = new Array();
    <? endif; ?>
        asd_fav_IDs.push(<?= $arParams['ELEMENT_ID'] ?>);
    </script>
<? endif; ?>
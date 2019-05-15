<?
if(!defined('B_PROLOG_INCLUDED')||B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule('webdebug.reviews')) {
	return;
}

$InterfaceID = IntVal($arParams['INTERFACE_ID']);

if (!is_numeric($InterfaceID) || $InterfaceID<=0) {
	CWD_Reviews2::ShowError(GetMessage('WDR2_ERROR_INTERFACE_EMPTY'));
	return;
}
if (trim($arParams['TARGET'])=='') {
	CWD_Reviews2::ShowError(GetMessage('WDR2_ERROR_TARGET_EMPTY'));
	return;
}

if (strlen($arParams['TARGET_SUFFIX'])>0) {
	$arParams['TARGET'] = $arParams['TARGET_SUFFIX'].$arParams['TARGET'];
}

$GLOBALS['WD_REVIEWS2_ADD_FORM_INDEX'] = IntVal($GLOBALS['WD_REVIEWS2_ADD_FORM_INDEX'])+1;
$arResult['FORM_INDEX'] = $GLOBALS['WD_REVIEWS2_ADD_FORM_INDEX'];
$arResult['FORM_NAME'] = 'wdr2_add_form_'.$arResult['FORM_INDEX'];
$arResult['IFRAME_NAME'] = 'wdr2_add_iframe_'.$arResult['FORM_INDEX'];
$arResult['FORM_ACTION'] = "/bitrix/tools/wd_reviews2.php?action=save&interface={$arParams['INTERFACE_ID']}&target={$arParams['TARGET']}&anticache=".rand(100000000,999999999);
$arResult['FORM_FIELD'] = 'review';
$arResult['ANTIBOT_FIELD_NAME'] = COption::GetOptionString('webdebug.reviews','antibot_field_name');
$arResult['FUNCTION_JS_SUCCESS'] = 'wdr2_success_'.$arResult['FORM_INDEX'];
$arResult['FUNCTION_JS_ERROR'] = 'wdr2_error_'.$arResult['FORM_INDEX'];
$arResult['FUNCTION_UPDATE_CAPTCHA'] = 'wdr2_update_captcha_'.$arResult['FORM_INDEX'].'()';
$arParams["MINIMIZE_FORM"] = $arParams["MINIMIZE_FORM"]!='N';
$arResult['USER_AUTHORIZED'] = $USER->IsAuthorized();

$arInterface = $GLOBALS['WD_REVIEWS2_INTERFACE_'.$InterfaceID];
if (!is_array($arInterface) || empty($arInterface)) {
	$resInterface = CWD_Reviews2_Interface::GetByID($InterfaceID);
	if ($arInterface = $resInterface->GetNext()) {
		$GLOBALS['WD_REVIEWS2_INTERFACE_'.$InterfaceID] = $arInterface;
	}
}

$arCacheID = array($arParams, $USER->GetGroups(), $arInterface);
if($this->StartResultCache(false, $arCacheID)) {
	$resInterface = CWD_Reviews2_Interface::GetByID($InterfaceID);
	if ($arResult['INTERFACE'] = $resInterface->GetNext()) {
		$arResult['INTERFACE']['SUCCESS_MESSAGE'] = str_replace("\r\n","<br/>",$arResult['INTERFACE']['SUCCESS_MESSAGE']);
		$arResult['INTERFACE_ID'] = $InterfaceID;
		$arResult['FIELDS'] = CWD_Reviews2_Reviews::ReviewGetFields(false, $InterfaceID);
		$arResult['RATINGS'] = CWD_Reviews2_Reviews::ReviewGetRatings(false, $InterfaceID);
		$this->SetResultCacheKeys(array('INTERFACE_ID', 'INTERFACE', 'FIELDS', 'RATINGS'));
		$arResult['USE_CAPTCHA'] = $arResult['INTERFACE']['CAPTCHA_MODE']=='Y' || ($arResult['INTERFACE']['CAPTCHA_MODE']=='U' && !$arResult['USER_AUTHORIZED']);
		?>
		<script type="text/javascript">
		//<![CDATA[
		function <?=$arResult['IFRAME_NAME'];?>_loaded(iFrame){
			if (window.wdr2_iframe_initialized_<?=$arResult['IFRAME_NAME'];?>==true) {
				var wdr2_iframe_data = iFrame.contentDocument || iFrame.contentWindow.document;
				var HTML = wdr2_iframe_data.body.innerHTML;
				if (HTML.indexOf('WD_REVIEWS2_REVIEW_SAVED_SUCCESS')>-1) {
					if (typeof <?=$arResult['FUNCTION_JS_SUCCESS'];?> === "function") { <?=$arResult['FUNCTION_JS_SUCCESS'];?>(HTML, iFrame);}
				} else {
					if (typeof <?=$arResult['FUNCTION_JS_ERROR'];?> === "function") {<?=$arResult['FUNCTION_JS_ERROR'];?>(HTML, iFrame);}
				}
			}
			window.wdr2_iframe_initialized_<?=$arResult['IFRAME_NAME'];?> = true;
		}
		<?if($arResult['USE_CAPTCHA']):?>
			function <?=$arResult['FUNCTION_UPDATE_CAPTCHA'];?>{
				$('#<?=$arResult['FORM_NAME'];?> input[name=captcha_word]').val('');
				$.ajax({
					url: '/bitrix/tools/wd_reviews2.php',
					type: 'GET',
					data: 'action=captcha&interface=<?=$arResult['INTERFACE_ID'];?>&form=<?=$arResult['FORM_INDEX'];?>&'+Math.random(),
					success: function(HTML) {
						$('#wdr2_captcha_<?=$arResult['FORM_INDEX'];?>').html(HTML);
					}
				});
			}
		<?endif?>
		//]]>
		</script>
		<div class="wdr2_iframe_hidden" style="display:none"><iframe src="about:blank" name="<?=$arResult['IFRAME_NAME'];?>" id="<?=$arResult['IFRAME_NAME'];?>" class="wdr2_iframe" onload="<?=$arResult['IFRAME_NAME'];?>_loaded(this)"></iframe></div>
		<?
		if ($this->initComponentTemplate()) {
			if ($arParams['MANUAL_CSS_INCLUDE']=='Y') {
				$CssFilename = $this->__template->__folder.'/style.css';
				$CssFilename .= '?'.filemtime($_SERVER['DOCUMENT_ROOT'].$CssFilename);
				print '<link rel="stylesheet" type="text/css" href="'.$CssFilename.'" />';
			}
		}
		$this->IncludeComponentTemplate();
	} else {
		$this->AbortResultCache();
		CWD_Reviews2::ShowError(GetMessage("WDR2_ERROR_INTERFACE_NOT_FOUND"));
	}
}

?>
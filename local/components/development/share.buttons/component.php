<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

$arResult = array();

if (!$arParams['xd_receiver_path']) {
	$this->InitComponentTemplate();
	$arResult['TPL_PATH'] = & $this->GetTemplate()->GetFolder();
}

if ($arParams['ONLY_TPL'] == 'Y') {
	$this->IncludeComponentTemplate();
	return;
}

if (!function_exists('__urlencode')) {

	function __urlencode($str) {
		if (trim($str) == '') {
			return $str;
		}
		$str = urlencode($GLOBALS['APPLICATION']->ConvertCharset($str, SITE_CHARSET, 'UTF-8'));
		return $str;
	}

}

$arParams['VK_API_ID'] = intval($arParams['VK_API_ID']);
$arParams['ASD_ID'] = intval($arParams['ASD_ID']);

if (!isset($arParams['ASD_INCLUDE_SCRIPTS']) || !is_array($arParams['ASD_INCLUDE_SCRIPTS']))
	$arParams['ASD_INCLUDE_SCRIPTS'] = array();

if (!isset($arParams['LIKE_TYPE']) || trim($arParams['LIKE_TYPE']) == '')
	$arParams['LIKE_TYPE'] = 'LIKE';

if (!isset($arParams['VK_LIKE_VIEW']) || trim($arParams['VK_LIKE_VIEW']) == '')
	$arParams['VK_LIKE_VIEW'] = 'mini';

if (!isset($arParams['FB_LIKE_VIEW']) || trim($arParams['FB_LIKE_VIEW']) == '')
	$arParams['FB_LIKE_VIEW'] = 'button_count';

if (substr($arParams['ASD_URL'], 0, 1) == '/') {
	if (defined('SITE_SERVER_NAME') && strlen(SITE_SERVER_NAME) > 0)
		$arParams['ASD_URL'] = 'http://' . SITE_SERVER_NAME . $arParams['ASD_URL'];
	else
		$arParams['ASD_URL'] = 'http://' . COption::GetOptionString('main', 'server_name', $GLOBALS['SERVER_NAME']) . $arParams['ASD_URL'];
}

if (substr($arParams['ASD_PICTURE'], 0, 1) == '/') {
	$arParams['ASD_PICTURE_REL'] = $arParams['ASD_PICTURE'];
	if (defined('SITE_SERVER_NAME') && strlen(SITE_SERVER_NAME) > 0)
		$arParams['ASD_PICTURE'] = 'http://' . SITE_SERVER_NAME . $arParams['ASD_PICTURE'];
	else
		$arParams['ASD_PICTURE'] = 'http://' . COption::GetOptionString('main', 'server_name', $GLOBALS['SERVER_NAME']) . $arParams['ASD_PICTURE'];
}

$arResult['ASD_URL'] = __urlencode($arParams['ASD_URL']);
$arResult['ASD_URL_NOT_ENCODE'] = $arParams['ASD_URL'];
$arResult['ASD_TITLE'] = __urlencode($arParams['ASD_TITLE']);
$arResult['ASD_TITLE_NOT_ENCODE'] = $arParams['ASD_TITLE'];
$arResult['ASD_PICTURE'] = __urlencode($arParams['ASD_PICTURE']);
$arResult['ASD_PICTURE_NOT_ENCODE'] = $arParams['ASD_PICTURE'];
$arResult['ASD_TEXT_NOT_ENCODE'] = TruncateText(strip_tags($arParams['ASD_TEXT']), 250);
$arResult['ASD_TEXT'] = __urlencode($arResult['ASD_TEXT_NOT_ENCODE']);
$arResult['ASD_HTML'] = __urlencode((trim($arParams['ASD_PICTURE']) == '' ? '' : '<img vspace="5" hspace="5" align="left" alt="" src="' . $arParams['ASD_PICTURE'] . '" />') .
		'<a href="' . $arParams['ASD_URL'] . '" target="_blank">' . $arParams['ASD_TITLE'] . '</a><br/><br/>' .
		$arParams['ASD_TEXT']);

if ($arParams['ASD_ID']>0 && !empty($arParams['ASD_INCLUDE_SCRIPTS'])) {
	$arInclScripts = array();
	if (in_array('TWITTER', $arParams['ASD_INCLUDE_SCRIPTS'])) {
		$arInclScripts[] = 'TWITTER';
	}
	if (in_array('GOOGLE', $arParams['ASD_INCLUDE_SCRIPTS'])) {
		$arInclScripts[] = 'GOOGLE';
	}
	$arParams['ASD_INCLUDE_SCRIPTS'] = $arInclScripts;
}

$this->IncludeComponentTemplate();

if ($arParams['SCRIPT_IN_HEAD'] == 'Y') {

	if (!$arParams['ASD_ID']) {
		$APPLICATION->AddHeadString('<meta property="og:title" content="' . $arResult['ASD_TITLE_NOT_ENCODE'] . '"/> ');
		if (strlen($arResult['ASD_TEXT_NOT_ENCODE'])) {
			$APPLICATION->AddHeadString('<meta property="og:description" content="' . $arResult['ASD_TEXT_NOT_ENCODE'] . '"/> ');
		}
		if (strlen($arResult['ASD_URL_NOT_ENCODE'])) {
			$APPLICATION->AddHeadString('<meta property="og:url" content="' . $arResult['ASD_URL_NOT_ENCODE'] . '"/> ');
		}
		if (strlen($arResult['ASD_PICTURE_NOT_ENCODE'])) {
			$APPLICATION->AddHeadString('<meta property="og:image" content="' . $arResult['ASD_PICTURE_NOT_ENCODE'] . '"/> ');
		}
		if (strlen($arParams['ASD_SITE_NAME'])) {
			$APPLICATION->AddHeadString('<meta property="og:site_name" content="' . $arParams['ASD_SITE_NAME'] . '"/> ');
		}
	}

	if (in_array('VK_LIKE', $arParams['ASD_INCLUDE_SCRIPTS'])) {
		$APPLICATION->AddHeadScript('http://userapi.com/js/api/openapi.js?17');
	}
	if (in_array('FB_LIKE', $arParams['ASD_INCLUDE_SCRIPTS'])) {
		$APPLICATION->AddHeadScript('http://connect.facebook.net/' . (LANGUAGE_ID == 'ru' ? 'ru_RU' : 'en_US') . '/all.js#xfbml=1');
	}
	if (in_array('TWITTER', $arParams['ASD_INCLUDE_SCRIPTS'])) {
		$APPLICATION->AddHeadScript('http://platform.twitter.com/widgets.js');
	}
	if (in_array('GOOGLE', $arParams['ASD_INCLUDE_SCRIPTS'])) {
		$APPLICATION->AddHeadString('<script type="text/javascript" src="http://apis.google.com/js/plusone.js">{"lang": "' . (LANGUAGE_ID == 'ru' ? 'ru' : 'en-US') . '"}</script>', true);
	}
}
?>
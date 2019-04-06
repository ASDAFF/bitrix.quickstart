<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) {
	define('PUBLIC_AJAX_MODE', true);
	if (!defined('SITE_ID') && isset($_POST['site_id'])) {
		define('SITE_ID', htmlspecialchars(trim($_POST['site_id'])));
	}
	require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
}

if (!isset($arParams)) {
	$arParams = array('JS_KEY' => md5(LICENSE_KEY));
}

$post = $_POST;

if ($post['asd_subscribe']=='Y' && strlen(trim($post['asd_email'])) &&
	$post['asd_key'] == md5(
							$arParams['JS_KEY'].
							$post['asd_rubrics'].
							(isset($post['asd_show_rubrics'])?$post['asd_show_rubrics']:'').
							(isset($post['asd_not_confirm'])?$post['asd_not_confirm']:'')
							) &&
	check_bitrix_sessid()
) {
	$arReturn = array();
	CComponentUtil::__IncludeLang(substr(__FILE__, strpos(__FILE__, '/bitrix/components'), -strlen(basename(__FILE__))), basename(__FILE__));

	if (CModule::IncludeModule('subscribe')) {
		$arRubrics = strlen($post['asd_rubrics']) ? explode('|', $post['asd_rubrics']) : array();
		$arRubricsUser = isset($post['asd_rub'])&&is_array($post['asd_rub']) ? $post['asd_rub'] : array();
		$arRubricsUser = array_intersect($arRubrics, $arRubricsUser);
		$arRubricsUser = empty($arRubricsUser) ? $arRubrics : $arRubricsUser;
		$email = trim($post['asd_email']);
		$charset = $post['charset'];
		$bShowRubrics = $post['asd_show_rubrics']=='Y';
		$format = trim($post['asd_format']);

		$arFields = Array(
			'USER_ID' => $USER->GetID(),
			'SEND_CONFIRM' => $post['asd_not_confirm']=='Y' ? 'N' : 'Y',
			'EMAIL' => $email,
			'FORMAT' => $format,
			'ACTIVE' => 'Y',
			'RUB_ID' => $bShowRubrics ? $arRubricsUser : $arRubrics,
			'CONFIRMED' => $post['asd_not_confirm']=='Y' ? 'Y' : 'N',
		);
		$subscr = new CSubscription;
		if ($newID = $subscr->Add($arFields)) {
			$arReturn = array('message' => GetMessage('ASD_CMP_SUCCESS'.($post['asd_not_confirm']=='Y' ? '_NC' : '')), 'status' => 'ok');
		} elseif ($ex = $APPLICATION->GetException()) {
			$arReturn = array('message' => $ex->GetString(), 'status' => 'error');
		}
	} else {
		$arReturn = array('message' => GetMessage('ASD_CMP_NOT_INSTALLED'), 'status' => 'error');
	}

	if (defined('PUBLIC_AJAX_MODE') && PUBLIC_AJAX_MODE===true) {
		$arReturn['message'] = $APPLICATION->ConvertCharset(strip_tags($arReturn['message']), $charset, 'UTF-8');
		header('Content-type: application/json');
		echo json_encode($arReturn);
	} else {
		return $arReturn;
	}
}

if (defined('PUBLIC_AJAX_MODE') && PUBLIC_AJAX_MODE===true) {
	require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php';
}
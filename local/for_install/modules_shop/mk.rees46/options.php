<?php
/**
 * @var string $REQUEST_METHOD
 */

CModule::IncludeModule('mk.rees46');

if ($REQUEST_METHOD === 'POST' && (!empty($save) || !empty($apply)) && check_bitrix_sessid()) {
	if (isset($_REQUEST['shop_id'])) {
		COption::SetOptionString(mk_rees46::MODULE_ID, 'shop_id', trim($_REQUEST['shop_id']));
	}
	if (isset($_REQUEST['shop_secret'])) {
		COption::SetOptionString(mk_rees46::MODULE_ID, 'shop_secret', trim($_REQUEST['shop_secret']));
	}
	if (isset($_REQUEST['css'])) {
		COption::SetOptionString(mk_rees46::MODULE_ID, 'css', trim($_REQUEST['css']));
	}
	if (intval($_REQUEST['image_width']) > 0) {
		COption::SetOptionInt(mk_rees46::MODULE_ID, 'image_width', $_REQUEST['image_width']);
	}
	if (intval($_REQUEST['image_height']) > 0) {
		COption::SetOptionInt(mk_rees46::MODULE_ID, 'image_height', $_REQUEST['image_height']);
	}
	if (intval($_REQUEST['recommend_count']) > 0) {
		COption::SetOptionInt(mk_rees46::MODULE_ID, 'recommend_count', $_REQUEST['recommend_count']);
	}

	COption::SetOptionInt(mk_rees46::MODULE_ID, 'recommend_nonavailable', $_REQUEST['recommend_nonavailable'] ? 1 : 0);
}

$export_state = \Rees46\Service\Export::STATUS_NOT_PERFORMED;
$export_count = -1;

if (isset($_REQUEST['do_export'])) {
	try {
		$export_count = \Rees46\Service\Export::exportOrders();
		$export_state = \Rees46\Service\Export::STATUS_SUCCESS;
	} catch (Exception $e) {
		$export_error = $e->getMessage();
		$export_state = \Rees46\Service\Export::STATUS_FAIL;
	}
}

include __DIR__ . '/options/form.php';

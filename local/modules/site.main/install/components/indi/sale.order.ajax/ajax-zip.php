<?
/**
 *  module
 * 
 * @category	
 * @package		Sale
 * @link		http://.ru
 */

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

$response = array(
	'success' => true,
);

try {
	\Bitrix\Main\Localization\Loc::loadMessages(__DIR__ . '/component.php');
	
	if (!\Bitrix\Main\Loader::includeModule('sale')) {
		throw new Exception(GetMessage('SOA_SALE_MODULE_NOT_INSTALL'));
	}
	
	$location = CSaleLocation::GetByZIP($_POST['zip']);
	if (!is_array($location) || !$location) {
		throw new Exception(GetMessage('SOA_ERROR_ZIP'));
	}
	
	$response['data'] = $location;
} catch (Exception $e) {
	$response['success'] = false;
	$response['code'] = $e->getCode();
	$response['message'] = $e->getMessage();
}

$view = new \Site\Main\Mvc\View\Json($response);
$view->sendHeaders();
print $view->render();

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php');
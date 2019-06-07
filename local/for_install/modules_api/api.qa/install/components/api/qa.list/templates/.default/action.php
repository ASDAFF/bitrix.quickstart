<?php
/**
 * Bitrix vars
 *
 * @var CDatabase $DB
 * @var CUser     $USER
 * @var CMain     $APPLICATION
 *
 */

set_time_limit(0);
ignore_user_abort(true);

define('PUBLIC_AJAX_MODE', true);
define('STOP_STATISTICS', true);
define('NO_KEEP_STATISTIC', 'Y');
define('NO_AGENT_STATISTIC', 'Y');
define('DisableEventsCheck', true);
define('BX_SECURITY_SHOW_MESSAGE', true);

if($_SERVER['REQUEST_METHOD'] != 'POST' || !preg_match('/^[A-Za-z0-9_]{2}$/', $_POST['siteId']))
	die();

define('SITE_ID', htmlspecialchars($_POST['siteId']));
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

global $APPLICATION, $USER;

use Bitrix\Main\Loader,
	 Bitrix\Main\Type\DateTime,
	 Bitrix\Main\Application,
	 Bitrix\Main\Web\Json,
	 Bitrix\Main\Localization\Loc,
	 Bitrix\Main\Text\Encoding;


Loc::loadMessages(dirname(__FILE__) . '/template.php');

$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$server  = $context->getServer();

$post = $request->getPostList()->toArray();
if(!Application::isUtfMode())
	$post = Encoding::convertEncoding($post, 'UTF-8', $context->getCulture()->getCharset());

//$request->addFilter(new \Bitrix\Main\Web\PostDecodeFilter);

if(!Loader::includeModule('api.qa'))
	die();

if(!Loader::includeModule('iblock'))
	die();

if(!$request->isPost() || !$post)
	die();

if($post['API_QA_LIST_AJAX'] != 'Y')
	die();

use Api\QA\QuestionTable,
	  Api\QA\Converter,
	  Api\QA\Tools,
	  Api\QA\Event;


//Bitrix\Main\Localization\Loc::loadMessages(dirname(__FILE__).'/class.php');

//---------- Подготовим необходимые параметры ----------//
$return = array(
	 'status'  => 'error',
	 'message' => '',
	 'fields'  => null,
);

$isEditor = ($APPLICATION->GetGroupRight('api.qa') >= 'W');
$form     = &$post['form'];
$action   = $post['api_action'];
$id       = intval($post['id']);

foreach($form as $key => $val) {
	$form[ $key ] = Tools::formatText($val, true);
}

if($isEditor && $action && $id) {

	CBitrixComponent::includeComponentClass("api:qa.list");
	$component = new ApiQaListComponent();


	$row = QuestionTable::getRow(array(
		 'filter' => array('=ID' => $id),
		 'select' => array('ID', 'USER_ID'),
	));

	if($row) {

		if($action == 'save') {
			$result = QuestionTable::update($id, $form);

			if($result->isSuccess()) {

				$form['TEXT'] = Converter::replace($form['TEXT']);

				$return = array(
					 'status'  => 'ok',
					 'message' => '',
					 'fields'  => $form,
				);
			}
		}

		if($action == 'delete') {

			Tools::deleteTree($id);
			//$component->deleteTree($id);

			/*function deleteTree($id)
			{
				// Удаляем текущий комментарий
				QuestionTable::delete($id);

				// Получаем потомки комментария
				$records = QuestionTable::getList(array(
					 'filter' => array('=PARENT_ID' => $id),
					 'select' => array('ID'),
				));

				// Если потомки есть - удаляем рекурсивно
				while($row = $records->fetch())
					deleteTree($row['ID']);
			}

			deleteTree($id);*/

			$return = array(
				 'status'  => 'ok',
				 'message' => '',
			);
		}

		if($action == 'erase') {
			$result = QuestionTable::update($id, array('TEXT' => ''));

			if($result->isSuccess()) {
				$return = array(
					 'status'  => 'ok',
					 'message' => '',
				);
			}
		}

	}
}


$APPLICATION->RestartBuffer();
header('Content-Type: application/json');
echo Json::encode($return);
//CMain::FinalActions();
die();
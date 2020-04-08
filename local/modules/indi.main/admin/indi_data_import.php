<?
if (isset($_REQUEST['work_start'])) {
	define("NO_AGENT_STATISTIC", true);
	define("NO_KEEP_STATISTIC", true);
}
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

use Bitrix\Main\Localization\Loc;
use \Indi\Main\Import;
use \Indi\Main\Iblock;
use Indi\Main\Mvc\View;
use \Indi\Main\Util;

Loc::loadMessages(__FILE__);
if(!$_REQUEST['work_start'] && !empty($_GET)) {
	LocalRedirect($_SERVER["PHP_SELF"]);
}
$APPLICATION->SetTitle(Loc::getMessage("INDI_MAIN_IMPORT_TITLE"));
CModule::IncludeModule("iblock");
IncludeModuleLangFile(__FILE__);
$POST_RIGHT = $APPLICATION->GetGroupRight("main");
if ($POST_RIGHT == "D") { // проверка на авторизацию
	$APPLICATION->AuthForm(Loc::getMessage("INDI_MAIN_IMPORT_ACCESS_IS_DENIED"));
}
$importPositionResult = '';
// получение объекта из выгрузки клиента
if ($_FILES['inputFile']) {
	//переменная директории загрузки
	$uploadDir = $_SERVER['DOCUMENT_ROOT'].'/upload/';
	//переменная файла
	$file = $_FILES['inputFile']['name'];
	//Переменная файл загрузки
	$path_info = pathinfo($file);
	$uploadFile = $uploadDir . 'upload'.time().'.'.$path_info['extension'];
	//Переносим файл загрузки в $_SESSION
	$_SESSION['uploadfile'] = $uploadFile;
	$_SESSION['xml_uploadfile'] = file_get_contents($uploadFile);

	if (!move_uploaded_file($_FILES['inputFile']['tmp_name'], $uploadFile)) {
		throw new \Exception("Ошибка! Не удалось загрузить файл на сервер!");
	}
}

$importPositionResult = Import::importRequsetManage($_REQUEST);
if(check_bitrix_sessid()) {
	echo $importPositionResult;
	die();
}
$aTabs = array(
	array("DIV" => "edit1", "TAB" => Loc::getMessage("INDI_MAIN_IMPORT_NEWS")),
	array("DIV" => "edit2", "TAB" => Loc::getMessage("INDI_MAIN_IMPORT_CATALOG")),
);
$tabControl = new \CAdminTabControl("tabControl", $aTabs);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>
<?php CJSCore::Init(array("jquery")); ?>
<script type="text/javascript" src="/bitrix/js/indi_data_import/script.js"></script>
<form method="post" action="<?echo $APPLICATION->GetCurPage()?>" enctype="multipart/form-data" name="post_form" id="post_form">
	<?
	echo bitrix_sessid_post();
	$tabControl->Begin();
	$tabControl->BeginNextTab();
	$view = new View\Php('import/form.php', array( // Импорт новостей
		'SERVICE' => "import_news",
		'LIMIT' => '1'
	));
	echo $view->render(); 
	$tabControl->BeginNextTab();
	$view->setData(
		array( // Импорт товаров
			'SERVICE' => "import_catalog",
			'LIMIT' => '1',
			'FILE' => 'Y',
		)
	);
	echo $view->render(); 
	$tabControl->End();
	?>
</form>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>
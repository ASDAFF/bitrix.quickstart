<?
if (isset($_REQUEST['work_start'])) {
	define("NO_AGENT_STATISTIC", true);
	define("NO_KEEP_STATISTIC", true);
}
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

use Bitrix\Main\Localization\Loc;
use \Site\Main\Import;
use \Site\Main\Iblock;
use Site\Main\Mvc\View;

Loc::loadMessages(__FILE__);
if(!$_REQUEST['work_start'] && !empty($_GET)) {
	LocalRedirect($_SERVER["PHP_SELF"]);
}
$APPLICATION->SetTitle(Loc::getMessage("site_MAIN_IMPORT_TITLE"));
CModule::IncludeModule("iblock");
IncludeModuleLangFile(__FILE__);
$POST_RIGHT = $APPLICATION->GetGroupRight("main");
if ($POST_RIGHT == "D") { // проверка на авторизацию
	$APPLICATION->AuthForm(Loc::getMessage("site_MAIN_IMPORT_ACCESS_IS_DENIED"));
}
$importPositionResult = Import::importRequsetManage($_REQUEST);
if(check_bitrix_sessid()) {
	echo $importPositionResult;
	die();
}
$aTabs = array(
	array("DIV" => "edit1", "TAB" => Loc::getMessage("site_MAIN_IMPORT_NEWS")),
	array("DIV" => "edit2", "TAB" => Loc::getMessage("site_MAIN_IMPORT_CATALOG")),
);
$tabControl = new \CAdminTabControl("tabControl", $aTabs);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>
<script type="text/javascript" src="/bitrix/js/site_data_import/script.js"></script>
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
			'LIMIT' => '1'
		)
	);
	echo $view->render(); 
	$tabControl->End();
	?>
</form>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>
<?
define("ADMIN_MODULE_NAME", "perfmon");
define("PERFMON_STOP", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/perfmon/include.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/perfmon/prolog.php");

IncludeModuleLangFile(__FILE__);

$RIGHT = $APPLICATION->GetGroupRight("perfmon");
if(!$USER->IsAdmin() || ($RIGHT < "W"))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

if($REQUEST_METHOD == "POST" && ($clear != "") && check_bitrix_sessid())
{
	CPerfomanceComponent::Clear();
	CPerfomanceSQL::Clear();
	CPerfomanceHit::Clear();
	CPerfomanceError::Clear();
	$_SESSION["PERFMON_CLEAR_MESSAGE"] = GetMessage("PERFMON_CLEAR_MESSAGE");
	LocalRedirect("/bitrix/admin/perfmon_clear.php?lang=".LANG);
}

$APPLICATION->SetTitle(GetMessage("PERFMON_CLEAR_TITLE"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

if($_SESSION["PERFMON_CLEAR_MESSAGE"])
{
	CAdminMessage::ShowMessage(array("MESSAGE"=>$_SESSION["PERFMON_CLEAR_MESSAGE"], "TYPE"=>"OK"));
	unset($_SESSION["PERFMON_CLEAR_MESSAGE"]);
}
?>

<form name="clear_form" method="post" action="<?echo $APPLICATION->GetCurPage();?>">
	<?echo bitrix_sessid_post();?>
	<input type="hidden" name="lang" value="<?echo LANG?>">
	<input type="submit" name="clear" value="<?echo GetMessage("PERFMON_CLEAR_BUTTON");?>">
</form>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>

<?
require($_SERVER["DOCUMENT_ROOT"]."/desktop_app/headers.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
if (intval($USER->GetID()) <= 0)
{
	?>
<script type="text/javascript">
	if (typeof(BXDesktopSystem) != 'undefined')
		BXDesktopSystem.Login({});
	else
		location.href = '/';
</script><?
	return true;
}
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/im/install/public/desktop_app/index.php");

if (!CModule::IncludeModule('im'))
	return;

CJSCore::Init(array('desktop'));
?>
<script type="text/javascript">
	if (typeof(BXDesktopSystem) != 'undefined')
		BX.desktop.init();
	else
		location.href = '/';
</script>
<?
$APPLICATION->IncludeComponent("bitrix:im.messenger", "", Array("DESKTOP" => "Y"), false, Array("HIDE_ICONS" => "Y"));

if (IsModuleInstalled('webdav'))
{
	$APPLICATION->IncludeComponent("bitrix:webdav.disk", '');
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>
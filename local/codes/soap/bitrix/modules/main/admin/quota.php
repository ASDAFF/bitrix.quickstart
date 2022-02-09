<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

if(!$USER->CanDoOperation('edit_other_settings'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$res = false;
if (isset($_REQUEST["id"]) && strLen(trim($_REQUEST["id"])) > 0 && check_bitrix_sessid())
{
	$quota = new CDiskQuota();
	$_REQUEST["recount"] = ($_REQUEST["recount"] == "begin"? true : false);
	if (strToLower($_REQUEST["id"]) == "db")
	{
		$_SESSION["SESS_RECOUNT_DB"] = "Y";
		$res = $quota->SetDBSize();
	}
	else 
	{
		$res = $quota->Recount($_REQUEST["id"], $_REQUEST["recount"]);
	}
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_popup_admin.php");

if($res !== false):
?>
<script type="text/javascript">
	if (!window.parent.window.result)
		window.parent.window.result = new Array();
	window.parent.window.result['done'] = true;
<?if ($res["status"] == "error"):?>
	window.parent.window.result['stop'] = true;
	window.parent.window.result['error'] = true;
<?else:?>
	window.parent.window.result['<?=CUtil::JSEscape($_REQUEST["name"])?>'] = new Array();
	window.parent.window.result['<?=CUtil::JSEscape($_REQUEST["name"])?>']['size'] = '<?=$res['size']?>';
	window.parent.window.result['<?=CUtil::JSEscape($_REQUEST["name"])?>']['status'] = '<?=substr($res['status'], 0, 1)?>';
	window.parent.window.result['<?=CUtil::JSEscape($_REQUEST["name"])?>']['time'] = '<?=date(CDatabase::DateFormatToPHP(CLang::GetDateFormat("FULL", LANG)), $res["time"])?>';
	
	window.parent.window.result['stop'] = <?=(($res["status"] == "continue") ? "false" : "true");?>;
	window.parent.window.result['error'] = false;
	
	window.parent.window.onStepDone('<?=CUtil::JSEscape($_REQUEST["name"])?>');
<?endif;?>
</script>
<?
endif;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_popup_admin.php");
?>
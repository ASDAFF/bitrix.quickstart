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

if($REQUEST_METHOD == "POST" && ($activity != "") && check_bitrix_sessid())
{
	if(array_key_exists("ACTIVE", $_REQUEST))
	{
		$ACTIVE = intval($_REQUEST["ACTIVE"]);
		CPerfomanceKeeper::SetActive($ACTIVE > 0, time() + $ACTIVE);
	}
	LocalRedirect("/bitrix/admin/perfmon_activity.php?lang=".LANG);
}

$APPLICATION->SetTitle(GetMessage("PERFMON_ACTIVITY_TITLE"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$interval = COption::GetOptionInt("perfmon", "end_time") - time();
if($interval <= 0)
{
	CPerfomanceKeeper::SetActive(false);
	$ACTIVE = false;
}
else
{
	$ACTIVE = CPerfomanceKeeper::IsActive();
}
?>

<form name="clear_form" method="post" action="<?echo $APPLICATION->GetCurPage();?>">
	<?echo bitrix_sessid_post();?>
	<input type="hidden" name="lang" value="<?echo LANG?>">
	<?if ($ACTIVE):?>
		<h3><?echo GetMessage("PERFMON_ACTIVE_Y")?></h3>
		<p><?echo GetMessage("PERFMON_ACT_ACTIVE_TO")?>:
			<?
			$hours = intval($interval / 3600);
			$interval -= $hours * 3600;
			$minutes = intval($interval / 60 );
			$interval -= $minutes * 60;
			$seconds = intval($interval);
			echo GetMessage("PERFMON_ACT_MINUTES", array("#HOURS#" => $hours, "#MINUTES#" => $minutes, "#SECONDS#" => $seconds));
			?></p>
		<p>
			<label for="ACTIVE"><?echo GetMessage("PERFMON_ACT_SET_IN_ACTIVE")?></label>:
			<input type="checkbox" name="ACTIVE" value="0" id="ACTIVE">
		</p>
		<input type="submit" name="activity" value="<?echo GetMessage("PERFMON_ACTION_BUTTON_OFF");?>">
	<?else:?>
		<h3><?echo GetMessage("PERFMON_ACTIVE_N")?></h3>
		<p><?echo GetMessage("PERFMON_ACT_SET_ACTIVE")?>:
		<select name="ACTIVE">
			<option value="0"><?echo GetMessage("PERFMON_ACT_INTERVAL_NO")?></option>
			<option value="60"><?echo GetMessage("PERFMON_ACT_INTERVAL_60_SEC")?></option>
			<option value="300"><?echo GetMessage("PERFMON_ACT_INTERVAL_300_SEC")?></option>
			<option value="600"><?echo GetMessage("PERFMON_ACT_INTERVAL_600_SEC")?></option>
			<option value="1800"><?echo GetMessage("PERFMON_ACT_INTERVAL_1800_SEC")?></option>
			<option value="3600"><?echo GetMessage("PERFMON_ACT_INTERVAL_3600_SEC")?></option>
		</select></p>
		<input type="submit" name="activity" value="<?echo GetMessage("PERFMON_ACTION_BUTTON_ON");?>">
	<?endif;?>
</form>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>

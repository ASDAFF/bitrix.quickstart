<?
$module_id = 'v1rt.personal';

if (!check_bitrix_sessid())
	return;

$session = explode('=', bitrix_sessid_get());

echo CAdminMessage::ShowNote(GetMessage("V1RT_START_MASTER"));
?>

<form action="/bitrix/admin/wizard_install.php" method="get">
    <input type="hidden" name="lang" value="<?php echo LANG?>"/> 
    <input type="hidden" name="<?php echo $session[0]?>" value="<?php echo $session[1]?>"/> 
	<input type="hidden" name="wizardName" value="v1rt.personal:v1rt:personal"/> 
    <input type="button" onclick="onBackClick()" value="<?php echo GetMessage("MOD_BACK")?>"/>
	<input type="submit" name="" value="<?php echo GetMessage("START_MASTER")?>"/>
<form>
<script type="text/javascript">
function onBackClick()
{
    window.location.reload();
}
</script>
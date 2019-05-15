<?
if (!check_bitrix_sessid()) return;

IncludeModuleLangFile(dirname(__FILE__)."/index.php");

global $errors;
//echo "<pre>"; print_r($errors); echo "</pre>";
$errors = is_array($errors) ? $errors : array();

$btnName = GetMessage('MOD_BACK');
$actionUrl = $GLOBALS["APPLICATION"]->GetCurPage();
$params = array();

foreach ($errors as $error)
{
	echo CAdminMessage::ShowMessage(array('MESSAGE' => $error[1], 'TYPE' => $error[0]));
	if (isset($error[2]) && $error[2])
		$btnName = $error[2];
	if (isset($error[3]) && $error[3])
		$actionUrl = $error[3];
	if (isset($error[4]) && is_array($error[4]))
		$params = $error[4];
}

if (!isset($params["lang"]))
	$params["lang"] = LANG;

//echo "<pre>"; print_r($btnName); print_r($actionUrl); print_r($params); echo "</pre>";

?>
<form action="<?=$actionUrl?>" method="GET">
	<p>
		<?foreach ($params as $key => $value) {?>
			<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
		<?}?>
		<input type="submit" value="<?=$btnName?>" />
	</p>
</form>
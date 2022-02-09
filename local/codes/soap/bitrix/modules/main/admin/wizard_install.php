<?
require_once(dirname(__FILE__)."/../include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/general/wizard.php");

IncludeModuleLangFile(__FILE__);

function _DumpPostVars($vname, $vvalue, $var_stack=array())
{
	if (is_array($vvalue))
	{
		foreach($vvalue as $key=>$value)
			_DumpPostVars($key, $value, array_merge($var_stack ,array($vname)));
	}
	else
	{
		if(count($var_stack)>0)
		{
			$var_name=$var_stack[0];
			for($i=1; $i<count($var_stack);$i++)
				$var_name.="[".$var_stack[$i]."]";
			$var_name.="[".$vname."]";
		}
		else
			$var_name=$vname;

		if ($var_name != "sessid")
		{
			?><input type="hidden" name="<?echo htmlspecialcharsbx($var_name)?>" value="<?echo htmlspecialcharsbx($vvalue)?>"><?
		}
	}
}

if(!$USER->CanDoOperation('edit_php')):
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"), false, false);
elseif (!check_bitrix_sessid()):
?>

	<span style="color:red"><?=GetMessage("MAIN_WIZARD_INSTALL_SESSION_EXPIRED")?></span>
	<form action="<?=$APPLICATION->GetCurPageParam(bitrix_sessid_get(), Array("sessid"))?>" method="post">

	<?
		foreach($_POST as $name => $value)
		{
			if ($name == "USER_LOGIN" || $name == "USER_PASSWORD")
				continue;
			_DumpPostVars($name, $value);
		}
	?><br>
		<input type="submit" value="<?=GetMessage("MAIN_WIZARD_INSTALL_RELOAD_PAGE")?>">
	</form>

<?
else:

	$arWizardNameTmp = explode(":", $_REQUEST["wizardName"]);
	$arWizardName = array();
	foreach ($arWizardNameTmp as $a)
	{
		$a = preg_replace("#[^a-z0-9_.-]+#i", "", $a);
		if (strlen($a) > 0)
			$arWizardName[] = $a;
	}

	if (count($arWizardName) > 2)
	{
		$path = $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$arWizardName[0]."/install/wizards/".$arWizardName[1]."/".$arWizardName[2];

		CopyDirFiles(
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$arWizardName[0]."/install/wizards/".$arWizardName[1]."/".$arWizardName[2],
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/wizards/".$arWizardName[1]."/".$arWizardName[2],
			true,
			true,
			false,
			""
		);

		$arWizardName = array($arWizardName[1], $arWizardName[2]);
	}

	$installer = new CWizard($arWizardName[0].(count($arWizardName) > 1 ? ":".$arWizardName[1] : ""));
	$installer->Install();
endif;
?>
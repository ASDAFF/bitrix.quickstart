<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog.php");

if(!$USER->IsAdmin())
	$APPLICATION->AuthForm();

IncludeModuleLangFile(__FILE__);
$APPLICATION->SetTitle(GetMessage("BITRIX_MPBUILDER_SAG_TRETIY_SOZDANIE"));
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");

$aTabs = array(
	array("DIV"=>"tab1", "TAB"=>GetMessage("BITRIX_MPBUILDER_SAG"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("BITRIX_MPBUILDER_SOZDANIE_ARHIVA")),
);
$editTab = new CAdminTabControl("editTab", $aTabs, true, true);

echo BeginNote().
	GetMessage("BITRIX_MPBUILDER_VSE_SKRIPTY_MODULA_B").' cp1251, '.GetMessage("BITRIX_MPBUILDER_ZATEM_BUDET_SOZDAN_A").' .last_version.tar.gz, '.GetMessage("BITRIX_MPBUILDER_KOTORYY_NADO_OTPRAVI").' <a href="https://partners.1c-bitrix.ru/personal/modules/edit_module.php?ID='.$module_id.'" target="_blank">marketplace</a>.'.
	EndNote();

$module_id = '';
$_REQUEST['module_id'] = str_replace(array('..','/','\\'),'',$_REQUEST['module_id']);
if ($_REQUEST['module_id'] && is_dir($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$_REQUEST['module_id']))
	$module_id = $_SESSION['mpbuilder']['module_id'] = $_REQUEST['module_id'];
else
	$module_id = $_SESSION['mpbuilder']['module_id'];
$m_dir = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$module_id;


if ($_REQUEST['action'] == 'delete' && $module_id && check_bitrix_sessid())
{
	BuilderRmDir($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/'.$module_id);
}
elseif ($_POST['save'] && $module_id)
{
	$strError = '';

	if ($v = $_REQUEST['version'])
	{
		$f = $m_dir.'/install/version.php';
		if(!file_put_contents($f,
			'<'.'?'."\n".
			'$arModuleVersion = array('."\n".
			'	"VERSION" => "'.EscapePHPString($v).'",'."\n".
			'	"VERSION_DATE" => "'.date('Y-m-d H:i:s').'"'."\n".
			');'."\n".
			'?'.'>'
		))
			$strError .= GetMessage("BITRIX_MPBUILDER_NE_UDALOSQ_ZAPISATQ").$f.'<br>';
	}

	if (is_dir($tmp = $_SERVER['DOCUMENT_ROOT'].BX_ROOT.'/tmp/'.$module_id))
		BuilderRmDir($tmp);

	mkdir($tmp.'/.last_version', BX_DIR_PERMISSIONS, true);

	if (function_exists('mb_internal_encoding'))
		mb_internal_encoding('ISO-8859-1');

	$tar = new CTarBuilder;
	$tar->path = $tmp;
	if (!$tar->openWrite($f = $tmp.'/.last_version.tar.gz'))
		$strError .= GetMessage("BITRIX_MPBUILDER_NE_UDALOSQ_OTKRYTQ_F").$f.'<br>';
	else
	{
		$ar = BuilderGetFiles($m_dir, array('.svn', '.hg', '.git'), true);
		foreach($ar as $file)
		{
			$from = $m_dir.$file;
			$to = $tmp.'/.last_version'.$file;

			if (false === $str = file_get_contents($from))
				$strError .= GetMessage("BITRIX_MPBUILDER_NE_UDALOSQ_PROCITATQ").$from.'<br>';
			else
			{
				if (substr($file, -4) == '.php' && GetStringCharset($str) == 'utf8')
					$str = $APPLICATION->ConvertCharset($str, 'utf8', 'cp1251');

				if (!file_exists($dir = dirname($to)))
					mkdir($dir, BX_DIR_PERMISSIONS, true);

				if (false === file_put_contents($to, $str))
					$strError .= GetMessage("BITRIX_MPBUILDER_NE_UDALOSQ_SOHRANITQ").$to.'<br>';
				else
					$tar->addFile($to);
			}
		}
		$tar->close();
	}

	if (!$strError)
		CAdminMessage::ShowMessage(array(
			"MESSAGE" => GetMessage("BITRIX_MPBUILDER_ARHIV_SOZDAN_USPESNO"),
			"DETAILS" => GetMessage("BITRIX_MPBUILDER_GOTOVYY_VARIANT_MOJN").
					': <a href="'.($link = '/bitrix/tmp/'.$module_id.'/.last_version.tar.gz').'">'.$link.'</a>'.
					'<br><input type=button value="'.GetMessage("BITRIX_MPBUILDER_UDALITQ_VREMENNYE_FA").'" onclick="if(confirm(\''.GetMessage("BITRIX_MPBUILDER_UDALITQ_PAPKU").' &quot;/bitrix/tmp/'.$module_id.'&quot; '.GetMessage("BITRIX_MPBUILDER_I_EE_SODERJIMOE").'?\'))document.location=\'?action=delete&'.bitrix_sessid_get().'\'">',
			"TYPE" => "OK",
			"HTML" => true));
	else
		CAdminMessage::ShowMessage(array(
			"MESSAGE" => GetMessage("BITRIX_MPBUILDER_OSIBKA_OBRABOTKI_FAY"),
			"DETAILS" => $strError,
			"TYPE" => "ERROR",
			"HTML" => true));
}

?>
	<form action="<?echo $APPLICATION->GetCurPage()?>?lang=<?=LANG?>" method="POST" enctype="multipart/form-data">
	<?
	$editTab->Begin();
	$editTab->BeginNextTab();
	?>
	<tr class=heading>
		<td colspan=2><?=GetMessage("BITRIX_MPBUILDER_VYBOR_MODULA")?></td>
	</tr>
	<tr>
		<td><?=GetMessage("BITRIX_MPBUILDER_TEKUSIY_MODULQ")?></td>
		<td>
		<select name=module_id onchange="document.location='?module_id='+this.value">
			<option></option>
		<?
			$arModules = array();
			$dir = opendir($path = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules');
			while(false !== $item = readdir($dir))
			{
				if ($item == '.' || $item == '..' || !is_dir($path.'/'.$item) || !strpos($item, '.'))
					continue;
				$arModules[$item] = '<option value="'.$item.'" '.($module_id == $item ? 'selected' : '').'>'.$item.'</option>';
			}
			closedir($dir);
			asort($arModules);
			echo implode("\n", $arModules);
		?>
		</select>
		</td>
	</tr>
	<?
	if ($module_id)
	{
		include($m_dir.'/install/version.php');
	?>
	<tr>
		<td><?=GetMessage("BITRIX_MPBUILDER_VERSIA_MODULA")?></td>
		<td><input name="version" value="<?=VersionUp($arModuleVersion['VERSION'])?>" id='version_field' disabled> <label><input type=checkbox onchange="document.getElementById('version_field').disabled=!this.checked"> <?=GetMessage("BITRIX_MPBUILDER_OBNOVITQ_VERSIU")?></label></td>

	</tr>
	<?
	}

	$editTab->Buttons();
	?>
	<input type="submit" name=save value="<?=GetMessage("BITRIX_MPBUILDER_PRODOLJITQ")?>">
	</form>
	<?
	$editTab->End();
	?>
<?


?>
<?
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
?>

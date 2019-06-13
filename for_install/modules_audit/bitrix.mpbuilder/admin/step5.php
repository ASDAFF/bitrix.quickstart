<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog.php");

if(!$USER->IsAdmin())
	$APPLICATION->AuthForm();

IncludeModuleLangFile(__FILE__);
$MODULE_ID = 'bitrix.mpbuilder';

$APPLICATION->SetTitle(GetMessage("BITRIX_MPBUILDER_SAG_CETVERTYY_SBORK"));
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");

$aTabs = array(
	array("DIV"=>"tab1", "TAB"=>GetMessage("BITRIX_MPBUILDER_SAG"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("BITRIX_MPBUILDER_SBORKA_OBNOVLENIA")),
);
$editTab = new CAdminTabControl("editTab", $aTabs);

echo BeginNote().
	GetMessage("BITRIX_MPBUILDER_V_ARHIV_POPADUT_FAYL").' install/version.php. '.GetMessage("BITRIX_MPBUILDER_OBNOVLENIE_NEOBHODIM").' <a href="https://partners.1c-bitrix.ru/personal/modules/modules.php?ACTIVE=Y" target="_blank">marketplace</a>.'.
	EndNote();

$module_id = '';
$_REQUEST['module_id'] = str_replace(array('..','/','\\'),'',$_REQUEST['module_id']);
if ($_REQUEST['module_id'] && is_dir($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$_REQUEST['module_id']))
	$module_id = $_SESSION['mpbuilder']['module_id'] = $_REQUEST['module_id'];
else
	$module_id = $_SESSION['mpbuilder']['module_id'];
$m_dir = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$module_id;

if ($_REQUEST['action'] == 'version_restore' && $module_id && check_bitrix_sessid())
{
	rename($m_dir.'/install/_version.php', $m_dir.'/install/version.php');
}

if ($module_id)
{
	if (file_exists($m_dir.'/install/version.php'))
		include($m_dir.'/install/version.php');
	$NAMESPACE = COption::GetOptionString($MODULE_ID, 'NAMESPACE', '');
}

if ($_REQUEST['action'] == 'delete' && $module_id && check_bitrix_sessid())
{
	BuilderRmDir($_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/'.$module_id);
}
elseif ($_POST['save'] && $module_id)
{
	$strError = '';
	$strFileList = '<br><br> <b>'.GetMessage("BITRIX_MPBUILDER_SPISOK_FAYLOV_V_ARHI").':</b><br>';
	function TarAddFile($tmp_dir, $f)
	{
		global $tar, $strFileList;
		$strFileList .= substr($f, strlen($tmp_dir)).'<br>';
		return $tar->addFile($f);
	}

	if ($bCustomNameSpace = array_key_exists('NAMESPACE', $_REQUEST))
		COption::SetOptionString($MODULE_ID, 'NAMESPACE', $NAMESPACE = str_replace(array('/','\\',' '),'',$_REQUEST['NAMESPACE']));

	if (!$v = $_REQUEST['version'])
		$strError .= GetMessage("BITRIX_MPBUILDER_VERSIA_MODULA_NE_UKA").'<br>';

	if (!$_REQUEST['description'])
		$strError .= GetMessage("BITRIX_MPBUILDER_NE_UKAZANO_OPISANIE").'<br>';

	$strVersion = 
		'<'.'?'."\n".
		'$arModuleVersion = array('."\n".
		'	"VERSION" => "'.EscapePHPString($v).'",'."\n".
		'	"VERSION_DATE" => "'.date('Y-m-d H:i:s').'"'."\n".
		');'."\n".
		'?'.'>';

	if (!$strError && $_REQUEST['store_version'])
	{
		rename($m_dir.'/install/version.php', $m_dir.'/install/_version.php');
		if (!file_put_contents($f = $m_dir.'/install/version.php', $strVersion))
			$strError .= GetMessage("BITRIX_MPBUILDER_NE_UDALOSQ_ZAPISATQ").$f.'<br>';
	}

	if (!$strError)
	{
		if (is_dir($tmp_dir = $_SERVER['DOCUMENT_ROOT'].BX_ROOT.'/tmp/'.$module_id))
			BuilderRmDir($tmp_dir);

		mkdir($tmp_dir.'/'.$v, BX_DIR_PERMISSIONS, true);

		if (function_exists('mb_internal_encoding'))
			mb_internal_encoding('ISO-8859-1');

		if (!$strError && $_REQUEST['components'])
		{
			$ar = array();
			if ($bCustomNameSpace)
			{
				$dir = opendir($path = $m_dir.'/install/components'); // let's get components list
				while(false !== $item = readdir($dir))
				{
					if ($item == '.' || $item == '..')
						continue;
					if (is_dir($f = $_SERVER['DOCUMENT_ROOT'].'/bitrix/components/'.$NAMESPACE.'/'.$item))
					{
						$arTmp = BuilderGetFiles($f, array(), true);
						foreach($arTmp as $file)
							$ar[] = '/'.$NAMESPACE.'/'.$item.$file;
					}
				}
				closedir($dir);
			}
			else
			{
				$dir = opendir($path = $m_dir.'/install/components');
				while(false !== $item = readdir($dir)) 
				{
					if ($item == '.' || $item == '..' || !is_dir($path0 = $path.'/'.$item))
						continue;

					$dir0 = opendir($path0);
					while(false !== $item0 = readdir($dir0))
					{
						if ($item0 == '.' || $item0 == '..' || !is_dir($f = $path0.'/'.$item0))
							continue;
						$arTmp = BuilderGetFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/'.$item.'/'.$item0, array(), true);
						foreach($arTmp as $file)
							$ar[] = '/'.$item.'/'.$item0.$file;
					}
					closedir($dir0);
				}
				closedir($dir);
			}

			foreach($ar as $file)
			{
				$from = $_SERVER['DOCUMENT_ROOT'].'/bitrix/components'.$file;
				$to = $m_dir.'/install/components'.($bCustomNameSpace ? preg_replace('#^/[^/]+#','',$file) : $file);

				if (!file_exists($to) || filemtime($from) > filemtime($to))
				{
					if (!is_dir($d = dirname($to)) && !mkdir($d, BX_DIR_PERMISSIONS, true))
						$strError .= GetMessage("BITRIX_MPBUILDER_NE_SOZDATQ_PAPKU").$d.'<br>';
					elseif (!copy($from, $to))
						$strError .= GetMessage("BITRIX_MPBUILDER_NE_UDALOSQ_SKOPIROVA").$from.'<br>';
					else
						touch($to, filemtime($from));
				}
			}
		}

		$tar = new CTarBuilder;
		$tar->path = $tmp_dir;
		if (!$tar->openWrite($f = $tmp_dir.'/'.$v.'.tar.gz'))
			$strError .= GetMessage("BITRIX_MPBUILDER_NE_UDALOSQ_OTKRYTQ_F").$f.'<br>';
		else
		{
			$ar = BuilderGetFiles($m_dir, array(), true);
			$time_from = strtotime($arModuleVersion['VERSION_DATE']);
			foreach($ar as $file)
			{
				$from = $m_dir.$file;
				$to = $tmp_dir.'/'.$v.$file;

				if ($file == '/install/_version.php')
					continue;

				if ($file == '/install/version.php')
				{
					if ($_REQUEST['store_version'] && !file_put_contents($from, $strVersion))
						$strError .= GetMessage("BITRIX_MPBUILDER_NE_UDALOSQ_ZAPISATQ").$from.'<br>';

					if (!file_exists($dir = dirname($to)))
						mkdir($dir, BX_DIR_PERMISSIONS, true);
					if(file_put_contents($to, $strVersion))
						TarAddFile($tmp_dir, $to);
					else
						$strError .= GetMessage("BITRIX_MPBUILDER_NE_UDALOSQ_ZAPISATQ").$to.'<br>';
					continue;
				}

				if (filemtime($from) < $time_from)
					continue;

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
						TarAddFile($tmp_dir, $to);
				}
			}

			if (!$strError)
			{
				$f = $tmp_dir.'/'.$v.'/description.ru';
				$description = $_REQUEST['description'];
				if (defined('BX_UTF') && BX_UTF)
					$description = $APPLICATION->ConvertCharset($description, 'utf8', 'cp1251');
				if(file_put_contents($f, $description))
					TarAddFile($tmp_dir, $f);
				else
					$strError .= GetMessage("BITRIX_MPBUILDER_NE_UDALOSQ_ZAPISATQ").$f.'<br>';
			}

			if (!$strError && ($str = trim($_REQUEST['updater'])))
			{
			/*
				$str = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/bitrix.mpbuilder/samples/_updater.php');
				$str = str_replace('{MODULE_ID}', $module_id, $str);
			*/
				if (file_put_contents($f = $tmp_dir.'/'.$v.'/updater.php', $str))
					TarAddFile($tmp_dir, $f);
				else
					$strError .= GetMessage("BITRIX_MPBUILDER_NE_UDALOSQ_SOHRANITQ").$f;
			}

			$tar->close();
		}
	}

	if (!$strError)
		CAdminMessage::ShowMessage(array(
			"MESSAGE" => GetMessage("BITRIX_MPBUILDER_OBNOVLENIE_SOBRANO"),
			"DETAILS" => GetMessage("BITRIX_MPBUILDER_ARHIV_OBNOVLENIA_MOJ").
					': <a href="'.($link = '/bitrix/tmp/'.$module_id.'/'.$v.'.tar.gz').'">'.$link.'</a>.'.
					'<br><a href="https://partners.1c-bitrix.ru/personal/modules/edit_update_module.php?module='.urlencode($module_id).'">'.GetMessage("BITRIX_MPBUILDER_ZAGRUZITQ_V").' marketplace</a> '.
					'<br><input type=button value="'.GetMessage("BITRIX_MPBUILDER_UDALITQ_VREMENNYE_FA").'" onclick="if(confirm(\''.GetMessage("BITRIX_MPBUILDER_UDALITQ_PAPKU").' &quot;/bitrix/tmp/'.$module_id.'&quot; '.GetMessage("BITRIX_MPBUILDER_I_EE_SODERJIMOE").'?\'))document.location=\'?action=delete&'.bitrix_sessid_get().'\'">'.
					$strFileList,
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
	<form action="<?echo $APPLICATION->GetCurPage()?>?lang=<?=LANG?>" method="POST" enctype="multipart/form-data" name="builder_form">
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
	?>
	<tr>
		<td valign=top><?=GetMessage("BITRIX_MPBUILDER_VERSIA_OBNOVLENIA")?></td>
		<td>
			<input name="version" value="<?=($v = $_REQUEST['version'] ? htmlspecialchars($_REQUEST['version']) : VersionUp($arModuleVersion['VERSION']))?>"> <label><input type=checkbox name=store_version <?=$_REQUEST['store_version']?'checked':''?>> <?=GetMessage("BITRIX_MPBUILDER_OBNOVITQ_DATU_I_VERS")?></label>
			<?
				if (file_exists($f = $m_dir.'/install/_version.php'))
				{
					include($f);
					if ($arModuleVersion['VERSION'] != $v)
						echo '<br>'.GetMessage("BITRIX_MPBUILDER_DOSTUPNA_VERSIA").htmlspecialchars($arModuleVersion['VERSION']).'</b> '.GetMessage("BITRIX_MPBUILDER_FILE").' version.php. <a href="javascript:if(confirm(\''.GetMessage("BITRIX_MPBUILDER_VOSSTANOVITQ_STARUU").'?\'))document.location=\'?action=version_restore&'.bitrix_sessid_get().'\'">'.GetMessage("BITRIX_MPBUILDER_VOSSTANOVITQ").'</a>.';
				}
			?>
		</td>
	</tr>
	<tr>
		<td valign=top><?=GetMessage("BITRIX_MPBUILDER_OBRABOTKA_USTANOVLEN")?></td>
		<td>
		<? 
			$bCustomNameSpace = false;
			if (!file_exists($path = $m_dir.'/install/components'))
				echo GetMessage("BITRIX_MPBUILDER_MODULQ_NE_SODERJIT_K").' /install'; 
			else
			{
				echo '<label><input type=checkbox onchange="if(ob=document.getElementById(\'NAMESPACE\'))ob.disabled=!this.checked;" name=components '.($_REQUEST['components']?'checked':'').'> '.GetMessage("BITRIX_MPBUILDER_SKOPIROVATQ_IZMENENN").' /bitrix/components/ '.GetMessage("BITRIX_MPBUILDER_V_ADRO_MODULA").'</label>';
				$dir = opendir($path);
				while(false !== $item = readdir($dir))
				{
					if ($item == '.' || $item == '..' || !is_dir($path0 = $path.'/'.$item))
						continue;
					if ($bCustomNameSpace = file_exists($path0.'/component.php'))
						break;
				}
				closedir($dir);
			}
		?>
		</td>
	</tr>
		<?
		if ($bCustomNameSpace)
		{
		?>
			<tr>
				<td><?=GetMessage("BITRIX_MPBUILDER_PROSTRANSTVO_IMEN_KO")?></td>
				<td>/bitrix/components/<input name=NAMESPACE id=NAMESPACE value="<?=htmlspecialchars($NAMESPACE)?>" <?=$_REQUEST['components'] ? '' : 'disabled'?>></td>
			</tr>
			<tr>
				<td></td>
				<td class=smalltext><?=GetMessage("BITRIX_MPBUILDER_KOMPONENTY_MODULA_LE")?><i>install</i>, <?=GetMessage("BITRIX_MPBUILDER_CTOBY_PROVERITQ_IZME")?><br>
				<?=GetMessage("BITRIX_MPBUILDER_TAKJE_UBEDITESQ_CTO")?><i>updater.php</i> <?=GetMessage("BITRIX_MPBUILDER_PRAVILQNO_KOPIRUET_K")?></td>
			</tr>
		<?
		}
		?>
	<tr>
		<td valign=top><?=GetMessage("BITRIX_MPBUILDER_OPISANIE_OBNOVLENIA")?></td>
		<td>
			<?
				if (!$description = $_REQUEST['description'])
				{
					if (file_exists($f = $_SERVER['DOCUMENT_ROOT'].BX_ROOT.'/tmp/'.$module_id.'/'.$v.'/description.ru'))
					{
						$description = file_get_contents($f);
						if (defined('BX_UTF') && BX_UTF)
							$description = $APPLICATION->ConvertCharset($description, 'cp1251', 'utf8');
					}
				}
				
			?>
			<textarea name=description style="width:100%" rows=10><?=htmlspecialchars($description)?></textarea>
		</td>
	</tr>
	<tr>
		<td></td>
		<td class=smalltext><?=GetMessage("BITRIX_MPBUILDER_PRI_PEREDACE_ISPOLNA")?></td>
	</tr>
	<tr>
		<td valign=top><?=GetMessage("BITRIX_MPBUILDER_SKRIPT_OBNOVLENIA")?> updater.php:</td>
		<td>
			<?
				if ($_SERVER['REQUEST_METHOD'] == 'POST')
					$updater = $_REQUEST['updater'];
				else
				{
					$updater = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/bitrix.mpbuilder/samples/_updater.php');
					$updater = str_replace('{MODULE_ID}', $module_id, $updater);
					$updater = str_replace('{NAMESPACE}', $bCustomNameSpace ? $NAMESPACE : '', $updater);
				}
			?>
			<textarea name=updater style="width:100%" rows=10><?=htmlspecialchars($updater)?></textarea>
		</td>
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

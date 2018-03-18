<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog.php");

if(!$USER->IsAdmin())
	$APPLICATION->AuthForm();

IncludeModuleLangFile(__FILE__);
$MODULE_ID = 'bitrix.mpbuilder';

$APPLICATION->SetTitle(GetMessage("BITRIX_MPBUILDER_SAG_PERVYY_SOZDANIE"));
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");

$aTabs = array(
	array("DIV"=>"tab1", "TAB"=>GetMessage("BITRIX_MPBUILDER_SAG"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("BITRIX_MPBUILDER_SOZDANIE_MODULA_IZ_S")),
);
$editTab = new CAdminTabControl("editTab", $aTabs);

echo BeginNote().
	GetMessage("BITRIX_MPBUILDER_DANNAA_FORMA_POMOJET")." <a href=\"http://marketplace.1c-bitrix.ru\" target=_blank>marketplace.1c-bitrix.ru</a>.".
	"<br>".GetMessage("BITRIX_MPBUILDER_PODROBNUU_DOKUMENTAC")." <a href=\"http://dev.1c-bitrix.ru/docs/solution.php\" target=_blank>dev.1c-bitrix.ru</a>.".
	EndNote();

$strError = '';
if ($_POST['save'])
{
	$match = '#^[a-z][a-z\-0-9]+$#';
	if (strlen($_REQUEST['partner_name']))
		COption::SetOptionString($MODULE_ID, 'partner_name', $_REQUEST['partner_name']);
	else
		$strError .= GetMessage("BITRIX_MPBUILDER_IMA_PARTNERA_NE_DOLJ").'<br>';

	if (strlen($_REQUEST['partner_uri']))
		COption::SetOptionString($MODULE_ID, 'partner_uri', $_REQUEST['partner_uri']);
	else
		$strError .= GetMessage("BITRIX_MPBUILDER_ADRES_SAYTA_NE_DOLJE").'<br>';

	if (preg_match($match, $_REQUEST['partner_code']))
		COption::SetOptionString($MODULE_ID, 'partner_code', $_REQUEST['partner_code']);
	else
		$strError .= GetMessage("BITRIX_MPBUILDER_KOD_PARTNERA_NE_KORR").'<br>';

	if (!preg_match($match, $module_id = $_REQUEST['module_id']))
		$strError .= GetMessage("BITRIX_MPBUILDER_KOD_MODULA_NE_KORREK").'<br>';

	if (!($module_name = $_REQUEST['module_name']))
		$strError .= GetMessage("BITRIX_MPBUILDER_IMA_MODULA_NE_MOJET").'<br>';

	$module_desc = $_REQUEST['module_desc'];

	$bForce = $_REQUEST['overwrite'];
	$module = $partner_code.'.'.$module_id;

	if (!$strError)
	{
		$m_dir = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$module;
		$arStructure = array(
			$m_dir,
			$m_dir.'/admin',
			$m_dir.'/lang/ru/install',
			$m_dir.'/install',
		);

		foreach($arStructure as $dir)
			if (!file_exists($dir) && !mkdir($dir, BX_DIR_PERMISSIONS, true))
				$strError .= GetMessage("BITRIX_MPBUILDER_NE_UDALOSQ_SOZDATQ_P").$dir.'<br>';
	}

	if (!$strError)
	{
		$MODULE_CLASS_NAME = str_replace('.','_',$module);
		$INCLUDE_CLASS_NAME = 'C'.preg_replace('/[^a-z]/i','',ucwords(preg_replace('/[^a-z]/i',' ',$module)));
		
		($str = file_get_contents($f = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/bitrix.mpbuilder/samples/install/index.php')) || die(GetMessage("BITRIX_MPBUILDER_FAYL_NE_NAYDEN").$f);
		$str = str_replace('{MODULE_CLASS_NAME}', $MODULE_CLASS_NAME, $str);
		$str = str_replace('{MODULE_ID}', $module, $str);
		$str = str_replace('{INCLUDE_CLASS_NAME}', $INCLUDE_CLASS_NAME, $str);

		($str1 = file_get_contents($f = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/bitrix.mpbuilder/samples/include.php')) || die(GetMessage("BITRIX_MPBUILDER_FAYL_NE_NAYDEN").$f);
		$str1 = str_replace('{INCLUDE_CLASS_NAME}', $INCLUDE_CLASS_NAME, $str1);

		if (file_exists($f = $m_dir.'/install/index.php') && !$bForce)
			$strError .= GetMessage("BITRIX_MPBUILDER_FAYL_SUSESTVUET").$f.'<br>';
		elseif (file_exists($f = $m_dir.'/install/index.php') && !$bForce)
			$strError .= GetMessage("BITRIX_MPBUILDER_FAYL_SUSESTVUET").$f.'<br>';
		elseif (!file_put_contents($f, $str))
			$strError .= GetMessage("BITRIX_MPBUILDER_OSIBKA_ZAPISI_V_FAYL").$f.'<br>';
		elseif (file_exists($f = $m_dir.'/include.php') && !$bForce)
			$strError .= GetMessage("BITRIX_MPBUILDER_FAYL_SUSESTVUET").$f.'<br>';
		elseif (!file_put_contents($f, $str1))
			$strError .= GetMessage("BITRIX_MPBUILDER_OSIBKA_ZAPISI_V_FAYL").$f.'<br>';
		elseif (file_exists($f = $m_dir.'/lang/ru/install/index.php') && !$bForce)
			$strError .= GetMessage("BITRIX_MPBUILDER_FAYL_SUSESTVUET").$f.'<br>';
		elseif (!file_put_contents($f,
			'<'.'?'."\n".
			'$MESS["'.$module.'_MODULE_NAME"] = "'.EscapePHPString($module_name).'";'."\n".
			'$MESS["'.$module.'_MODULE_DESC"] = "'.EscapePHPString($module_desc).'";'."\n".
			'$MESS["'.$module.'_PARTNER_NAME"] = "'.EscapePHPString($partner_name).'";'."\n".
			'$MESS["'.$module.'_PARTNER_URI"] = "'.EscapePHPString($partner_uri).'";'."\n".
			'?'.'>'
		))
			$strError .= GetMessage("BITRIX_MPBUILDER_OSIBKA_ZAPISI_V_FAYL").$f.'<br>';
		elseif (file_exists($f = $m_dir.'/install/version.php') && !$bForce)
			$strError .= GetMessage("BITRIX_MPBUILDER_FAYL_SUSESTVUET").$f.'<br>';
		elseif (!file_put_contents($f,
			'<'.'?'."\n".
			'$arModuleVersion = array('."\n".
			'	"VERSION" => "1.0.0",'."\n".
			'	"VERSION_DATE" => "'.date('Y-m-d H:i:s').'"'."\n".
			');'."\n".
			'?'.'>'
		))
			$strError .= GetMessage("BITRIX_MPBUILDER_OSIBKA_ZAPISI_V_FAYL").$f.'<br>';
		elseif (is_array($_FILES))
		{
			foreach($_FILES['script']['tmp_name'] as $k=>$tmp)
			{
				if (!$tmp)
					continue;
				if (!move_uploaded_file($tmp, $m_dir.'/admin/'.preg_replace('/[^a-z0-9\.\-]/i','_',$name = $_FILES['script']['name'][$k])))
					$strError .= GetMessage("BITRIX_MPBUILDER_NE_UDALOSQ_ZAGRUZITQ").htmlspecialchars($name).'<br>';
			}
		}

		if (!$strError && is_array($_REQUEST['component']))
		{
			foreach($_REQUEST['component'] as $c)
			{
				if (!$c)
					continue;
				if (!CopyDirFiles($_SERVER['DOCUMENT_ROOT'].BX_ROOT.'/components'.$c, $m_dir.'/install/components'.$c, $bForce, true))
					$strError .= GetMessage("BITRIX_MPBUILDER_NE_UDALOSQ_SKOPIROVA").$c.'<br>';
			}
		}
	}

	if ($strError)
		CAdminMessage::ShowMessage(array(
			"MESSAGE" => GetMessage("BITRIX_MPBUILDER_OSIBKA_VVODA_PARAMET"),
			"DETAILS" => $strError,
			"TYPE" => "ERROR",
			"HTML" => true));
	else
		LocalRedirect('/bitrix/admin/bitrix.mpbuilder_step2.php?module_id='.$module.'&lang='.LANGUAGE_ID);
}
?>
	<form action="<?echo $APPLICATION->GetCurPage()?>?lang=<?=LANG?>" method="POST" enctype="multipart/form-data">
	<?
	$editTab->Begin();
	$editTab->BeginNextTab();
	?>
	<tr class=heading>
		<td colspan=2><?=GetMessage("BITRIX_MPBUILDER_DANNYE_RAZRABOTCIKA")?></td>
	</tr>
	<tr>
		<td><?=GetMessage("BITRIX_MPBUILDER_NAZVANIE_VASEY_KOMPA")?></td>
		<td><input name="partner_name" value="<?=htmlspecialchars(COption::GetOptionString($MODULE_ID, 'partner_name', ''))?>"></td>
	</tr>
	<tr>
		<td><?=GetMessage("BITRIX_MPBUILDER_ADRES_VASEGO_SAYTA")?></td>
		<td><input name="partner_uri" value="<?=htmlspecialchars(COption::GetOptionString($MODULE_ID, 'partner_uri', 'http://www.mysite.ru'))?>"></td>
	</tr>
	<tr>
		<td><?=GetMessage("BITRIX_MPBUILDER_KOD_PARTNERA")?></td>
		<td><input name="partner_code" value="<?=htmlspecialchars(COption::GetOptionString($MODULE_ID, 'partner_code', ''))?>"></td>
	</tr>
	<tr>
		<td></td>
		<td class=smalltext><?=GetMessage("BITRIX_MPBUILDER_KOD_PARTNERA_POSTOAN")?><i>mycompany</i>).</td>
	</tr>
	<tr class=heading>
		<td colspan=2><?=GetMessage("BITRIX_MPBUILDER_DANNYE_NOVOGO_MODULA")?></td>
	</tr>
	<tr>
		<td><?=GetMessage("BITRIX_MPBUILDER_PEREPISATQ_SUSESTVUU")?></td>
		<td><input type=checkbox name="overwrite" <?=$_REQUEST['overwrite'] ? 'checked' : ''?>></td>
	</tr>
	<tr>
		<td><?=GetMessage("BITRIX_MPBUILDER_KOD_MODULA")?></td>
		<td><input name="module_id" value="<?=htmlspecialchars($module_id)?>"></td>
	</tr>
	<tr>
		<td></td>
		<td class=smalltext><?=GetMessage("BITRIX_MPBUILDER_KOD_MODULA_VVODITSA")?> marketplace (<?=GetMessage("BITRIX_MPBUILDER_NAPRIMER")?><i>testmodule</i>).<br><?=GetMessage("BITRIX_MPBUILDER_PAPKA_MODULA_SOSTOIT")?><i>mycompany</i>.<i>testmodule</i>.</td>
	</tr>
	<tr>
		<td><?=GetMessage("BITRIX_MPBUILDER_NAZVANIE_MODULA")?></td>
		<td><input name="module_name" value="<?=htmlspecialchars($module_name)?>"></td>
	</tr>
	<tr>
		<td valign=top><?=GetMessage("BITRIX_MPBUILDER_OPISANIE_MODULA")?></td>
		<td><textarea name="module_desc" cols=40 rows=3><?=htmlspecialchars($module_desc)?></textarea></td>
	</tr>
	<tr class=heading>
		<td colspan=2><?=GetMessage("BITRIX_MPBUILDER_KOMPONENTY_MODULA")?></td>
	</tr>
	<tr>
		<td valign=top><?=GetMessage("BITRIX_MPBUILDER_VYBERITE_KOMPONENTY")?><br>(<?=GetMessage("BITRIX_MPBUILDER_IZ_PAPKI")?> /bitrix/components <?=GetMessage("BITRIX_MPBUILDER_KROME_SISTEMNYH_V")?> bitrix)</td>
		<td>
			<?
			$strSelect = '';
			$strSelect .= '<select name="component[]" onchange="NewComponent(this)"><option value=""></option>';
			$arComponents = array();
			$dir0 = opendir($path = $_SERVER['DOCUMENT_ROOT'].BX_ROOT.'/components');
			while(false !== $item0 = readdir($dir0))
			{
				if ($item0 == '.' || $item0 == '..' || $item0 == 'bitrix')
					continue;
				if (is_dir($path.'/'.$item0))
				{
					$dir1 = opendir($path.'/'.$item0);
					while(false !== $item1 = readdir($dir1))
					{
						if ($item1 == '.' || $item1 == '..')
							continue;
						if (is_dir($path.($f = '/'.$item0.'/'.$item1)))
							$arComponents[$f] = $item0.':'.$item1;
					}
					closedir($dir1);
				}
			}
			closedir($dir0);
			asort($arComponents);
			foreach($arComponents as $key=>$val)
				$strSelect .= '<option value="'.htmlspecialchars($key).'">'.htmlspecialchars($val).'</option>';
			$strSelect .= '</select>';
			?>
			<table id=componentlist border=0 cellspacing=2 cellpadding=2>
			<?
				if (is_array($_REQUEST['component']))
				{
					foreach($_REQUEST['component'] as $c)
					{
						if (!$c)
							continue;
						echo '<tr><td><select name="component[]"><option value=""></option>';
						foreach($arComponents as $key=>$val)
							echo '<option value="'.htmlspecialchars($key).'" '.($key == $c ? 'selected' : '').'>'.htmlspecialchars($val).'</option>';
						echo '</select></td></tr>';
					}
				}
			?>
				<tr><td><?=$strSelect?></td></tr>
			</table>
		</td>
	</tr>
	<tr class=heading>
		<td colspan=2><?=GetMessage("BITRIX_MPBUILDER_SKRIPTY_ADMINISTRATI")?></td>
	</tr>
	<tr>
		<td valign=top><?=GetMessage("BITRIX_MPBUILDER_FAYLY")?></td>
		<td>
			<table id=filelist border=0 cellspacing=2 cellpadding=2>
				<tr><td><input name="script[]" type=file onchange="NewScript(this)"></td></tr>
			</table>
		</td>
	</tr>
	<tr>
		<td></td>
		<td class=smalltext><?=GetMessage("BITRIX_MPBUILDER_POSLE_SOZDANIA_MODUL")?> admin.</td>
	</tr>
	<?
	$editTab->Buttons();
	?>
	<input type="submit" name=save value="<?=GetMessage("BITRIX_MPBUILDER_PRODOLJITQ")?>">
	<?
	$editTab->End();
	?>
	</form>
	<script>
		function NewScript(ob)
		{
			ob.onchange = '';
			oTable = document.getElementById('filelist');
			oRow= oTable.insertRow(-1);
			oCell = oRow.insertCell(-1);
			oCell.innerHTML = '<input name="script[]" type=file onchange="NewScript(this)">';
		}

		function NewComponent(ob)
		{
			ob.onchange = '';
			oTable = document.getElementById('componentlist');
			oRow= oTable.insertRow(-1);
			oCell = oRow.insertCell(-1);
			oCell.innerHTML = '<?=$strSelect?>';
		}
	</script>

<?
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
?>

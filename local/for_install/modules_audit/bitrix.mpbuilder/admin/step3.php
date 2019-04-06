<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog.php");

if(!$USER->IsAdmin())
	$APPLICATION->AuthForm();

IncludeModuleLangFile(__FILE__);
$APPLICATION->SetTitle(GetMessage("BITRIX_MPBUILDER_EDITOR"));
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");

$module_id = '';
$_REQUEST['module_id'] = str_replace(array('..','/','\\'),'',$_REQUEST['module_id']);
if ($_REQUEST['module_id'] && is_dir($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$_REQUEST['module_id']))
	$module_id = $_SESSION['mpbuilder']['module_id'] = $_REQUEST['module_id'];
else
	$module_id = $_SESSION['mpbuilder']['module_id'];

$m_dir = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$module_id;
$file = str_replace('\\','/',$_REQUEST['file']);
if (check_bitrix_sessid())
{
	$lang_file = str_replace('\\','/',$_REQUEST['lang_file']);

	if ($_REQUEST['save'])
	{
		if (($str0 = file_get_contents($m_dir.$file)) && is_array($arMess = GetMess($lang_file)))
		{
			$arNewMess = array();
			foreach($arMess as $key => $val)
			{
				$new_key = str_replace(' ', '_', $_REQUEST['prefix'].$_REQUEST['arMess'][$key]);
				if ($key != $new_key)
				{
					$i = 0;
					while(array_key_exists($new_key, $arNewMess))
						$new_key = str_replace(' ', '_', $_REQUEST['prefix'].$_REQUEST['arMess'][$key]).(++$i);
					$arNewMess[$new_key] = $val;
					$str0 = str_replace('GetMessage("'.$key.'")',   'GetMessage("'.$new_key.'")', $str0);
					$str0 = str_replace('GetMessage(\''.$key.'\')', 'GetMessage("'.$new_key.'")', $str0);
					$str0 = str_replace('GetMessageJS("'.$key.'")',   'GetMessageJS("'.$new_key.'")', $str0);
					$str0 = str_replace('GetMessageJS(\''.$key.'\')', 'GetMessageJS("'.$new_key.'")', $str0);
				}
				else
					$arNewMess[$key] = $val;
			}

			$str = "<"."?\n";
			foreach($arNewMess as $key => $val)
				$str .= '$MESS["'.$key.'"] = "'.str_replace('"','\\"',str_replace('\\','\\\\',$val)).'";'."\n";
			$str .= "?".">";
			if (file_put_contents($lang_file, $str) && file_put_contents($m_dir.$file, $str0))
				CAdminMessage::ShowMessage(array(
					"MESSAGE" => GetMessage("BITRIX_MPBUILDER_SAVED"),
					"DETAILS" => GetMessage("BITRIX_MPBUILDER_LANG_FILE").$lang_file,
					"TYPE" => "OK",
					"HTML" => true));
			else
				CAdminMessage::ShowMessage(array(
					"MESSAGE" => GetMessage("BITRIX_MPBUILDER_ERROR"),
					"DETAILS" => GetMessage("BITRIX_MPBUILDER_ERR_SAVE").$lang_file,
					"TYPE" => "ERROR",
					"HTML" => true));
		}
		else
			CAdminMessage::ShowMessage(array(
				"MESSAGE" => GetMessage("BITRIX_MPBUILDER_ERROR"),
				"DETAILS" => GetMessage("BITRIX_MPBUILDER_FILE_NOT_FOUND").$lang_file,
				"TYPE" => "ERROR",
				"HTML" => true));
	}
}

$aTabs = array(
	array("DIV"=>"tab1", "TAB"=>GetMessage("BITRIX_MPBUILDER_STEP"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("BITRIX_MPBUILDER_EDITOR")),
);
$editTab = new CAdminTabControl("editTab", $aTabs, true, true);

echo BeginNote().
	GetMessage("BITRIX_MPBUILDER_V_KACESTVE_RAZDELITE").' "_".'.
	EndNote();

?>
	<form action="<?echo $APPLICATION->GetCurPage()?>?lang=<?=LANG?>" method="POST" enctype="multipart/form-data">
	<?
	echo bitrix_sessid_post();
	$editTab->Begin();
	$editTab->BeginNextTab();
	?>
	<tr>
		<td width=40%><?=GetMessage("BITRIX_MPBUILDER_CUR_MODULE")?></td>
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
			<td><?=GetMessage("BITRIX_MPBUILDER_PATH_TO_FILE")?></td>
			<td><select name=file onchange="document.location='?file='+this.value">
			<option></option>
			<?
				$ar = BuilderGetFiles($m_dir);
				sort($ar);
				foreach($ar as $f)
					if (!preg_match('#^/lang/#',$f))
						echo '<option value="'.htmlspecialchars($f).'" '.($f == $file ? 'selected' : '').'>'.$f.'</option>';
			?>
			</select></td>
		</tr>
		<?
		if ($file)
		{
			$lang_file = $m_dir.GetLangPath($file, $m_dir);

			if ($arMess = GetMess($lang_file))
				echo '<input type=hidden name=lang_file value="'.htmlspecialchars($lang_file).'">';
			?>
			<tr class=heading>
				<td colspan=2><?=GetMessage("BITRIX_MPBUILDER_KEY_LIST")?></td>
			</tr>
			<?
			if ($arMess)
			{
				if ($_REQUEST['disable_prefix'])
				{
					$prefix = '';
					$l = 0;
				}
				else
				{
					$prefix = strtoupper(str_replace('.', '_', $module_id)).'_';
					$l = strlen($prefix);

					foreach($arMess as $key => $val)
					{
						if (strpos($key, $prefix) !== 0)
						{
							$prefix = '';
							$l = 0;
							break;
						}
					}
				}

				if ($prefix)
				{
					echo '<tr>
						<td colspan=2 align=center>'.GetMessage("BITRIX_MPBUILDER_VSE_KLUCI_IMEUT_PREF").htmlspecialchars($prefix).'</b>. <a href="javascript:if(confirm(\''.GetMessage("BITRIX_MPBUILDER_PEREGRUZITQ_STRANICU").'?\'))document.location=\'?file='.urlencode($file).'&disable_prefix=1\'">'.GetMessage("BITRIX_MPBUILDER_REDAKTIROVATQ_KLUCI").'?</a></td>
					</tr>';
				}


				foreach($arMess as $key => $val)
				{
					echo '<tr>
						<td>'.htmlspecialchars(substr($val,0,30)).(strlen($val) > 30 ? '...' : '').'</td>
						<td style="color:#666">'.$prefix.'<input size=40 name="arMess['.htmlspecialchars($key).']" value="'.htmlspecialchars(substr($key,$l)).'" onchange="this.value=this.value.replace(/ /,\'_\')"></td>
					</tr>';
				}
			}
		}
	}
	$editTab->Buttons();
	?>
	<input type="hidden" name=prefix value="<?=$prefix?>">
	<input type="submit" name=save value="<?=GetMessage("BITRIX_MPBUILDER_SAVE_BTN")?>">
	<?
	$editTab->End();
	?>
	</form>
<?


?>
<?
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");

?>

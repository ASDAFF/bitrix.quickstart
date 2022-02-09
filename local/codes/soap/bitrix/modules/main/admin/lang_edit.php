<?
##############################################
# Bitrix Site Manager                        #
# Copyright (c) 2002-2007 Bitrix             #
# http://www.bitrixsoft.com                  #
# mailto:admin@bitrixsoft.com                #
##############################################

require_once(dirname(__FILE__)."/../include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog.php");
define("HELP_FILE", "settings/lang_edit.php");

ClearVars();

if(!$USER->CanDoOperation('edit_other_settings') && !$USER->CanDoOperation('view_other_settings'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$isAdmin = $USER->CanDoOperation('edit_other_settings');

IncludeModuleLangFile(__FILE__);

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("MAIN_PARAM"), "ICON" => "lang_edit", "TITLE" => GetMessage("MAIN_PARAM_TITLE")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

$message=null;
$bVarsFromForm = false;
$ID=IntVal($ID);
if($REQUEST_METHOD=="POST" && (strlen($save)>0 || strlen($apply)>0) && $isAdmin && check_bitrix_sessid())
{
	$arFields = Array(
		"ACTIVE"			=> $_REQUEST['ACTIVE'],
		"SORT"				=> $_REQUEST['SORT'],
		"DEF"				=> $_REQUEST['DEF'],
		"NAME"				=> $_REQUEST['NAME'],
		"FORMAT_DATE"		=> $_REQUEST['FORMAT_DATE'],
		"FORMAT_DATETIME"	=> $_REQUEST['FORMAT_DATETIME'],
		"WEEK_START"		=> intval($_REQUEST["WEEK_START"]),
		"FORMAT_NAME"		=> CSite::GetNameFormatByValue($_REQUEST["FORMAT_NAME"]),
		"CHARSET"			=> $_REQUEST['CHARSET'],
		"DIRECTION"			=> $_REQUEST['DIRECTION']
		);

	if($ID<=0)
		$arFields["LID"]=$LID;

	$langs = new CLanguage;
	if($ID>0)
	{
		$res = $langs->Update($LID, $arFields);
	}
	else
	{
		$res = (strlen($langs->Add($arFields))>0);
		$new="Y";
	}

	if(!$res)
	{
		$bVarsFromForm = true;
	}
	else
	{
		if (strlen($save)>0) LocalRedirect(BX_ROOT."/admin/lang_admin.php?lang=".LANGUAGE_ID);
		elseif ($new=="Y") LocalRedirect(BX_ROOT."/admin/lang_edit.php?lang=".LANGUAGE_ID."&LID=".$LID."&".$tabControl->ActiveTabParam());
		else
			LocalRedirect(BX_ROOT."/admin/lang_edit.php?lang=".LANGUAGE_ID."&LID=".$LID."&".$tabControl->ActiveTabParam());
	}
}

$str_ACTIVE="Y";
$str_WEEK_START = GetMessage('LANG_EDIT_WEEK_START_DEFAULT');
if (!$str_WEEK_START && $str_WEEK_START !== '0')
	$str_WEEK_START = 1;
$str_WEEK_START = intval($str_WEEK_START);

$ID=0;
if(strlen($COPY_ID)>0)
{
	$lng = CLanguage::GetByID($COPY_ID);
	$lng->ExtractFields("str_");
}
elseif(strlen($LID)>0)
{
	$lng = CLanguage::GetByID($LID);
	if($x = $lng->ExtractFields("str_"))
		$ID=1;
}
else
{
	//only if new
	if (empty($str_FORMAT_NAME))
		$str_FORMAT_NAME = CSite::GetDefaultNameFormat();
}


if($bVarsFromForm)
{
	$DB->InitTableVarsForEdit("b_lang", "", "str_");
	$str_FORMAT_NAME = CSite::GetNameFormatByValue($_POST["FORMAT_NAME"]);
}

$strTitle = ($ID>0) ? str_replace("#ID#", "$str_LID", GetMessage("EDIT_LANG_TITLE")) : GetMessage("NEW_LANG_TITLE");
$APPLICATION->SetTitle($strTitle);
/***************************************************************************
				HTML form
****************************************************************************/

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");
?>

<?
$aMenu = array(
	array(
		"TEXT"	=> GetMessage("RECORD_LIST"),
		"LINK"	=> "/bitrix/admin/lang_admin.php?lang=".LANGUAGE_ID."&set_default=Y",
		"TITLE"	=> GetMessage("RECORD_LIST_TITLE"),
		"ICON"	=> "btn_list"
	)
);

if ($ID>0)
{
	$aMenu[] = array("SEPARATOR"=>"Y");

	$aMenu[] = array(
		"TEXT"	=> GetMessage("MAIN_NEW_RECORD"),
		"LINK"	=> "/bitrix/admin/lang_edit.php?lang=".LANGUAGE_ID,
		"TITLE"	=> GetMessage("MAIN_NEW_RECORD_TITLE"),
		"ICON"	=> "btn_new"
		);
	if($isAdmin)
	{
		$aMenu[] = array(
			"TEXT"	=> GetMessage("MAIN_COPY_RECORD"),
			"LINK"	=> "/bitrix/admin/lang_edit.php?lang=".LANGUAGE_ID."&amp;COPY_ID=".urlencode($LID),
			"TITLE"	=> GetMessage("MAIN_COPY_RECORD_TITLE"),
			"ICON"	=> "btn_copy"
			);
		$aMenu[] = array(
			"TEXT"	=> GetMessage("MAIN_DELETE_RECORD"),
			"LINK"	=> "javascript:if(confirm('".GetMessage("MAIN_DELETE_RECORD_CONF")."')) window.location='/bitrix/admin/lang_admin.php?ID=".urlencode(urlencode($LID))."&lang=".LANGUAGE_ID."&".bitrix_sessid_get()."&action=delete';",
			"TITLE"	=> GetMessage("MAIN_DELETE_RECORD_TITLE"),
			"ICON"	=> "btn_delete"
			);
	}
}

$context = new CAdminContextMenu($aMenu);
$context->Show();
if ($e = $APPLICATION->GetException())
			$message = new CAdminMessage(GetMessage("MAIN_ERROR_SAVING"), $e);

if($message)
	echo $message->Show();

?>
<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?" name="form1">
<?=bitrix_sessid_post()?>
<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
<input type="hidden" name="ID" value="<?echo $ID?>">
<?if(strlen($COPY_ID)>0):?><input type="hidden" name="COPY_ID" value="<?echo htmlspecialcharsbx($COPY_ID)?>"><?endif?>
<?
$tabControl->Begin();

$tabControl->BeginNextTab();
?>
	<tr class="adm-detail-required-field">
		<td width="40%">ID:</td>
		<td width="60%"><?
			if($ID>0):
				echo $str_LID;
				?><input type="hidden" name="LID" value="<? echo $str_LID?>"><?
			else:
				?><input type="text" name="LID" size="2" maxlength="2" value="<? echo $str_LID?>"><?
			endif;
				?></td>
	</tr>
	<tr>
		<td><label for="active"><?echo GetMessage('ACTIVE')?></label></td>
		<td><input type="checkbox" name="ACTIVE" id="active" value="Y"<?if($str_ACTIVE=="Y")echo " checked"?>></td>
	</tr>
	<tr class="adm-detail-required-field">
		<td><?echo GetMessage('NAME')?></td>
		<td><input type="text" name="NAME" size="30" maxlength="50" value="<? echo $str_NAME?>"></td>
	</tr>
	<tr>
		<td><label for="def"><?echo GetMessage('DEF')?></label></td>
		<td><input type="checkbox" name="DEF" id="def" value="Y"<?if($str_DEF=="Y")echo " checked"?>></td>
	</tr>
	<tr class="adm-detail-required-field">
		<td><?echo GetMessage('SORT')?></td>
		<td><input type="text" name="SORT" size="10" maxlength="10" value="<? echo $str_SORT?>"></td>
	</tr>
	<tr class="adm-detail-required-field">
		<td><? echo GetMessage('FORMAT_DATE')?></td>
		<td><input type="text" name="FORMAT_DATE" size="30" maxlength="50" value="<? echo $str_FORMAT_DATE?>"></td>
	</tr>
	<tr class="adm-detail-required-field">
		<td><? echo GetMessage('FORMAT_DATETIME')?></td>
		<td><input type="text" name="FORMAT_DATETIME" size="30" maxlength="50" value="<?echo $str_FORMAT_DATETIME?>"></td>
	</tr>
	<tr>
		<td><? echo GetMessage('LANG_EDIT_WEEK_START')?></td>
		<td><select name="WEEK_START">
<?
for ($i = 0; $i < 7; $i++)
{
	echo '<option value="'.$i.'"'.($i == $str_WEEK_START ? ' selected="selected"' : '').'>'.GetMessage('DAY_OF_WEEK_' .$i).'</option>';
}
?>
		</select></td>
	</tr>
	<tr class="adm-detail-required-field">
		<td><? echo GetMessage('FORMAT_NAME')?></td>
		<td><?echo CSite::SelectBoxName("FORMAT_NAME", $str_FORMAT_NAME);?></td>
	</tr>
	<tr class="adm-detail-required-field">
		<td><? echo GetMessage('CHARSET')?></td>
		<td><input type="text" name="CHARSET" size="30" maxlength="50" value="<?echo $str_CHARSET?>">
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage('DIRECTION')?></td>
		<td><select name="DIRECTION">
				<option value="Y"><?=GetMessage('DIRECTION_LTR')?></option>
				<option value="N"<?if($str_DIRECTION=="N") echo " selected"?>><?=GetMessage('DIRECTION_RTL')?></option>
			</select>
		</td>
	</tr>
<?$tabControl->Buttons(array("disabled"=>!$isAdmin, "back_url"=>"lang_admin.php?lang=".LANGUAGE_ID));
$tabControl->End();
$tabControl->ShowWarnings("form1", $message);
?>
</form>

<?require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");?>

<?
##############################################
# Bitrix Site Manager                        #
# Copyright (c) 2002-2007 Bitrix             #
# http://www.bitrixsoft.com                  #
# mailto:admin@bitrixsoft.com                #
##############################################

require_once(dirname(__FILE__)."/../include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog.php");
define("HELP_FILE", "settings/mail_events/message_edit.php");

if(!$USER->CanDoOperation('edit_other_settings') && !$USER->CanDoOperation('view_other_settings'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$isAdmin = $USER->CanDoOperation('edit_other_settings');

ClearVars();

IncludeModuleLangFile(__FILE__);

$strError="";
$bVarsFromForm = false;
$ID=IntVal($ID);
$COPY_ID=intval($COPY_ID);
$message=null;
if($COPY_ID>0)
	$ID = $COPY_ID;
$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB"), "ICON" => "message_edit", "TITLE" => GetMessage("MAIN_TAB_TITLE")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

if($REQUEST_METHOD=="POST" && (strlen($save)>0 || strlen($apply)>0)&& $isAdmin && check_bitrix_sessid())
{
	$em = new CEventMessage;
	$arFields = Array(
		"ACTIVE"		=> $ACTIVE,
		"EVENT_NAME"	=> $EVENT_NAME,
		"LID"			=> $LID,
		"EMAIL_FROM"	=> $EMAIL_FROM,
		"EMAIL_TO"		=> $EMAIL_TO,
		"BCC"			=> $BCC,
		"CC"			=> $CC,
		"REPLY_TO"		=> $REPLY_TO,
		"IN_REPLY_TO"	=> $IN_REPLY_TO,
		"PRIORITY"		=> $PRIORITY,
		"FIELD1_NAME"	=> $FIELD1_NAME,
		"FIELD1_VALUE"	=> $FIELD1_VALUE,
		"FIELD2_NAME"	=> $FIELD2_NAME,
		"FIELD2_VALUE"	=> $FIELD2_VALUE,
		"SUBJECT"		=> $SUBJECT,
		"MESSAGE"		=> $MESSAGE,
		"BODY_TYPE"		=> $BODY_TYPE
		);

	if($ID>0 && $COPY_ID<=0)
		$res = $em->Update($ID, $arFields);
	else
	{
		$ID = $em->Add($arFields);
		$res = ($ID>0);
		$new="Y";
	}

	if(!$res)
	{
		$bVarsFromForm = true;
	}
	else
	{
		if (strlen($save)>0)
		{
			if (!empty($_REQUEST["type"]))
				LocalRedirect(BX_ROOT."/admin/type_edit.php?EVENT_NAME=".$EVENT_NAME."&lang=".LANGUAGE_ID);
			else
				LocalRedirect(BX_ROOT."/admin/message_admin.php?lang=".LANGUAGE_ID);
		}
		else
			LocalRedirect(BX_ROOT."/admin/message_edit.php?lang=".LANGUAGE_ID."&ID=".$ID."&type=".$_REQUEST["type"]."&".$tabControl->ActiveTabParam());
	}
}
$str_ACTIVE="Y";
$str_EVENT_NAME=$EVENT_NAME;
$em = CEventMessage::GetByID($ID);
if(!$em->ExtractEditFields("str_"))
{
	$ID=0;
}
else
{
	$str_LID = Array();
	$db_LID = CEventMessage::GetLang($ID);
	while($ar_LID = $db_LID->Fetch())
		$str_LID[] = $ar_LID["LID"];
}

if($bVarsFromForm)
{
	$str_LID = $LID;
	$DB->InitTableVarsForEdit("b_event_message", "", "str_");
}

if($ID>0 && $COPY_ID<=0)
	$APPLICATION->SetTitle(str_replace("#ID#", "$ID", GetMessage("EDIT_MESSAGE_TITLE")));
else
	$APPLICATION->SetTitle(GetMessage("NEW_MESSAGE_TITLE"));

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");
?>
<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>" name="form1">
<?=bitrix_sessid_post()?>
<input type="hidden" name="lang" value="<?echo LANG?>" />
<input type="hidden" name="ID" value="<?echo $ID?>" />
<input type="hidden" name="COPY_ID" value="<?echo $COPY_ID?>" />
<input type="hidden" name="type" value="<?echo htmlspecialcharsbx($_REQUEST["type"])?>" />
<script type="text/javascript" language="JavaScript">
<!--
var t=null;
function PutString(str)
{
	if(!t)return;
	if(t.name=="MESSAGE" || t.name=="EMAIL_FROM" || t.name=="EMAIL_TO" || t.name=="SUBJECT" || t.name=="BCC")
	{
		t.value+=str;
		BX.fireEvent(t, 'change');
	}
}
//-->
</script>
<?
$aMenu = array(
	array(
		"TEXT"	=> GetMessage("RECORD_LIST"),
		"LINK"	=> "/bitrix/admin/message_admin.php?lang=".LANGUAGE_ID."&set_default=Y",
		"TITLE"	=> GetMessage("RECORD_LIST_TITLE"),
		"ICON"	=> "btn_list"
	)
);

if (intval($ID)>0 && $COPY_ID<=0)
{
	$aMenu[] = array("SEPARATOR"=>"Y");

	$aMenu[] = array(
		"TEXT"	=> GetMessage("MAIN_NEW_RECORD"),
		"LINK"	=> "/bitrix/admin/message_edit.php?lang=".LANGUAGE_ID,
		"TITLE"	=> GetMessage("MAIN_NEW_RECORD_TITLE"),
		"ICON"	=> "btn_new"
		);

	$aMenu[] = array(
		"TEXT"	=> GetMessage("MAIN_COPY_RECORD"),
		"LINK"	=> "/bitrix/admin/message_edit.php?lang=".LANGUAGE_ID.htmlspecialcharsbx("&COPY_ID=").$ID,
		"TITLE"	=> GetMessage("MAIN_COPY_RECORD_TITLE"),
		"ICON"	=> "btn_copy"
		);

	$aMenu[] = array(
		"TEXT"	=> GetMessage("MAIN_DELETE_RECORD"),
		"LINK"	=> "javascript:if(confirm('".GetMessage("MAIN_DELETE_RECORD_CONF")."')) window.location='/bitrix/admin/message_admin.php?ID=".$ID."&lang=".LANGUAGE_ID."&".bitrix_sessid_get()."&action=delete';",
		"TITLE"	=> GetMessage("MAIN_DELETE_RECORD_TITLE"),
		"ICON"	=> "btn_delete"
		);
}
$context = new CAdminContextMenu($aMenu);
$context->Show();

if ($e = $APPLICATION->GetException())
			$message = new CAdminMessage(GetMessage("MAIN_ERROR_SAVING"), $e);
if($message)
	echo $message->Show();

if(strlen($strError)>0)
	echo CAdminMessage::ShowMessage(Array("MESSAGE"=>$strError, "HTML"=>true, "TYPE"=>"ERROR"));

$tabControl->Begin();

$tabControl->BeginNextTab();
?>
	<?if($ID>0 && $COPY_ID<=0):?>
	<tr>
		<td width="40%"><?echo GetMessage('LAST_UPDATE')?></td>
		<td width="60%"><?echo $str_TIMESTAMP_X?></td>
	</tr>
	<? endif; ?>
	<tr>
		<td><label for="active"><?echo GetMessage('ACTIVE')?></label></td>
		<td><input type="checkbox" name="ACTIVE" id="active" value="Y"<?if($str_ACTIVE=="Y")echo " checked"?>></td>
	</tr>
	<tr class="adm-detail-required-field">
		<td class="adm-detail-valign-top"><?echo GetMessage('LID')?></td>
		<td><?=CLang::SelectBoxMulti("LID", $str_LID);?></td>
	</tr>
	<tr>
		<td><?echo GetMessage("EVENT_NAME")?></td>
		<td><?
			$event_type_ref = array();
			$rsType = CEventType::GetListEx(array(), array(), array("LID"=>LANG, "type" => "type"));
			while ($arType = $rsType->Fetch())
			{
				$arType["NAME"] = "[ ".$arType["EVENT_NAME"]." ]".(empty($arType["NAME"]) ? "" : " == ".$arType["NAME"]);
				$event_type_ref[$arType["EVENT_NAME"]] = $arType;
			}

			?>

			<?
			if($ID>0 && $COPY_ID<=0)
			{
				$arType = $event_type_ref[$str_EVENT_NAME];
				$type_DESCRIPTION = htmlspecialcharsbx($arType["DESCRIPTION"]);
				$type_NAME = htmlspecialcharsbx($arType["NAME"]);
				?><input type="hidden" name="EVENT_NAME" value="<? echo $str_EVENT_NAME?>"><?echo $type_NAME?><?
			}
			else
			{
				$id_1st = false;
				?>
				<select name="EVENT_NAME" style="width:335px" onChange="window.location='message_edit.php?lang=<?=LANGUAGE_ID?>&EVENT_NAME='+this[this.selectedIndex].value">
				<?
				foreach($event_type_ref as $ev_name=>$arType):
					if($id_1st===false)
						$id_1st = $ev_name;
				?>
					<option value="<?=htmlspecialcharsbx($arType["EVENT_NAME"])?>"<?
					if($str_EVENT_NAME==$arType["EVENT_NAME"])
					{
						echo " selected";
						$id_1st = $ev_name;
										}
					?>><?=htmlspecialcharsbx($arType["NAME"])?></option>
				<?
				endforeach;
				?>
				</select>
				<?
				$type_DESCRIPTION = htmlspecialcharsbx($event_type_ref[$id_1st]["DESCRIPTION"]);
			}
		?></td>
	</tr>
	<tr class="adm-detail-required-field">
		<td><? echo GetMessage('MSG_EMAIL_FROM')?></td>
		<td><input type="text" name="EMAIL_FROM" size="50" maxlength="255" value="<?echo $str_EMAIL_FROM?>" onfocus="t=this">
		</td>
	</tr>
	<tr class="adm-detail-required-field">
		<td><?echo GetMessage('MSG_EMAIL_TO')?></td>
		<td><input type="text" name="EMAIL_TO" size="50" maxlength="255" value="<?echo $str_EMAIL_TO?>" onfocus="t=this"></td>
	</tr>

	<?
	$str_show_ext = '';
	$show_ext = ($str_CC!='' || $str_BCC!='' || $str_REPLY_TO!='' || $str_IN_REPLY_TO!='' || $str_PRIORITY!='' || $str_FIELD1_NAME!='' || $str_FIELD1_VALUE!='' || $str_FIELD2_NAME!='' || $str_FIELD2_VALUE!='');
	if(!$show_ext):
		$str_show_ext = 'style="display:none;"';
		?>
		<script>
		function ShowExtH()
		{
			for(var i=1; i<8; i++)
			{
				try
				{
					document.getElementById('msg_ext'+i).style.display = 'table-row';
				}
				catch(e)
				{
					document.getElementById('msg_ext'+i).style.display = 'block';
				}
			}
			document.getElementById('msg_ext0').style.display = 'none';
		}
		</script>
		<tr id="msg_ext0">
			<td></td>
			<td><a href="javascript:void(0)" onclick="return ShowExtH()"><?echo GetMessage("MSG_EXT")?></a></td>
		</tr>
		<?
	endif;
	?>

	<tr id="msg_ext1" <?=$str_show_ext?>>
		<td><?echo GetMessage("MSG_CC")?></td>
		<td><input type="text" name="CC" size="30" maxlength="255" value="<?echo $str_CC?>" onfocus="t=this">
		</td>
	</tr>

	<tr id="msg_ext2" <?=$str_show_ext?>>
		<td><?echo GetMessage("MSG_BCC")?></td>
		<td><input type="text" name="BCC" size="30" maxlength="255" value="<?echo $str_BCC?>" onfocus="t=this">
		</td>
	</tr>

	<tr id="msg_ext3" <?=$str_show_ext?>>
		<td><?echo GetMessage("MSG_REPLY_TO")?></td>
		<td><input type="text" name="REPLY_TO" size="30" maxlength="255" value="<?echo $str_REPLY_TO?>" onfocus="t=this">
		</td>
	</tr>

	<tr id="msg_ext4" <?=$str_show_ext?>>
		<td><?echo GetMessage("MSG_IN_REPLY_TO")?></td>
		<td><input type="text" name="IN_REPLY_TO" size="30" maxlength="255" value="<?echo $str_IN_REPLY_TO?>" onfocus="t=this">
		</td>
	</tr>

	<tr id="msg_ext5" <?=$str_show_ext?>>
		<td><?echo GetMessage("MSG_PRIORITY")?></td>
		<td>
		<input type="text" name="PRIORITY" id="MSG_PRIORITY" size="10" maxlength="255" value="<?echo $str_PRIORITY?>" onfocus="t=this">
		<select onchange="document.getElementById('MSG_PRIORITY').value=this.value">
			<option value=""></option>
			<option value="1 (Highest)"<?if($str_PRIORITY=='1 (Highest)')echo ' selected'?>><?echo GetMessage("MSG_PRIORITY_1")?></option>
			<option value="3 (Normal)"<?if($str_PRIORITY=='3 (Normal)')echo ' selected'?>><?echo GetMessage("MSG_PRIORITY_3")?></option>
			<option value="5 (Lowest)"<?if($str_PRIORITY=='5 (Lowest)')echo ' selected'?>><?echo GetMessage("MSG_PRIORITY_5")?></option>
		</select>
		</td>
	</tr>

	<tr id="msg_ext6" <?=$str_show_ext?>>
		<td><input type="text" name="FIELD1_NAME" size="20" maxlength="255" value="<?echo $str_FIELD1_NAME?>" onfocus="t=this">:</td>
		<td><input type="text" name="FIELD1_VALUE" size="30" maxlength="255" value="<?echo $str_FIELD1_VALUE?>" onfocus="t=this">
		</td>
	</tr>

	<tr id="msg_ext7" <?=$str_show_ext?>>
		<td><input type="text" name="FIELD2_NAME" size="20" maxlength="255" value="<?echo $str_FIELD2_NAME?>" onfocus="t=this">:</td>
		<td><input type="text" name="FIELD2_VALUE" size="30" maxlength="255" value="<?echo $str_FIELD2_VALUE?>" onfocus="t=this">
		</td>
	</tr>


	<tr>
		<td><?echo GetMessage("SUBJECT")?></td>
		<td><input type="text" name="SUBJECT" size="50" maxlength="255" value="<?echo $str_SUBJECT?>" onfocus="t=this"></td>
	</tr>
	<tr class="heading">
		<td colspan="2"><?=GetMessage("MSG_BODY")?></td>
	</tr>
	<tr>
		<td><?echo GetMessage("MSG_BODY_TYPE")?></td>
		<td><input type="radio" id="BODY1" name="BODY_TYPE" value="text"<?if($str_BODY_TYPE!="html")echo " checked"?>> <label for="BODY1"><?echo GetMessage("MSG_BODY_TYPE_TEXT")?></label>&nbsp;/&nbsp;<input id="BODY2" type="radio" name="BODY_TYPE" value="html"<?if($str_BODY_TYPE=="html")echo " checked"?>> <label for="BODY2"><?echo GetMessage("MSG_BODY_TYPE_HTML")?></label></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><textarea cols="40" rows="25" name="MESSAGE" wrap="off" onfocus="t=this" style="width:100%"><?echo $str_MESSAGE?></textarea></td>
	</tr>
	<?
	$str_def =
	"#DEFAULT_EMAIL_FROM# - ".GetMessage("MAIN_MESS_ED_DEF_EMAIL")."
	#SITE_NAME# - ".GetMessage("MAIN_MESS_ED_SITENAME")."
	#SERVER_NAME# - ".GetMessage("MAIN_MESS_ED_SERVERNAME")."
	";
	function ReplaceVars($str)
	{
		return preg_replace("/(#.+?#)/", "<a title='".GetMessage("MAIN_INSERT")."' href=\"javascript:PutString('\\1')\">\\1</a>", $str);
	}
	?>
	<tr>
		<td align="left" colspan="2"><br><b><?=GetMessage("AVAILABLE_FIELDS")?></b><br><br>
			<?echo ReplaceVars(nl2br(trim($type_DESCRIPTION)."\r\n".$str_def));?></td>
	</tr>
<?$tabControl->Buttons(array("disabled" => !$isAdmin, "back_url"=>"message_admin.php?lang=".LANGUAGE_ID));
$tabControl->End();
$tabControl->ShowWarnings("form1", $message);
?>
</form>

<?require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");?>
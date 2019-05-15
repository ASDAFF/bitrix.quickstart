<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/imaginweb.sms/include.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/imaginweb.sms/prolog.php");

IncludeModuleLangFile(__FILE__);

$POST_RIGHT = $APPLICATION->GetGroupRight("imaginweb.sms");
if($POST_RIGHT == "D"){
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("post_posting_tab"), "ICON" => "main_user_edit", "TITLE" => GetMessage("post_posting_tab_title")),
	array("DIV" => "edit2", "TAB" => GetMessage("post_subscr_tab"), "ICON" => "main_user_edit", "TITLE" => GetMessage("post_subscr_tab_title")),
	array("DIV" => "edit3", "TAB" => GetMessage("post_params_tab"), "ICON" => "main_user_edit", "TITLE" => GetMessage("post_params_tab_title")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

$ID = intval($ID);		// Id of the edited record
$bCopy = ($action == "copy");
$message = null;
$bVarsFromForm = false;

if($REQUEST_METHOD == "POST" && ($save.$apply.$Send.$Resend.$Continue != "") && $POST_RIGHT == "W" && check_bitrix_sessid()){
	$posting = new SMSCPosting();
	$arFields = Array(
		"FROM_FIELD"	=> $FROM_FIELD,
		"TO_FIELD"	=> $TO_FIELD,
		"BCC_FIELD"	=> $BCC_FIELD,
		"PHONE_FILTER"	=> $PHONE_FILTER,
		"SUBJECT"	=> $SUBJECT,
		"BODY_TYPE"	=> ($BODY_TYPE <> "html"? "text" : "html"),
		"BODY"		=> $BODY,
		"DIRECT_SEND"	=> "Y"/*($DIRECT_SEND <> "Y" ? "N" : "Y")*/,
		"CHARSET"	=> $CHARSET,
		"SUBSCR_FORMAT"	=> ($SUBSCR_FORMAT <> "html" && $SUBSCR_FORMAT <> "text" ? false : $SUBSCR_FORMAT),
		"RUB_ID"	=> $RUB_ID,
		"GROUP_ID"	=> $GROUP_ID,
		"AUTO_SEND_TIME"=> ($AUTO_SEND_FLAG <> "Y" ? false : $AUTO_SEND_TIME),
	);

	if($STATUS <> ""){
		if($STATUS <> "S" && $STATUS <> "E" && $STATUS <> "P" && $STATUS <> "W"){
			$STATUS = "D";
		}	
	}

	if($ID > 0){
		$res = $posting->Update($ID, $arFields);
		if(strlen($Resend) > 0){
			$STATUS = "W";
		}
		if($res && $STATUS <> ""){
			$res = $posting->ChangeStatus($ID, $STATUS);
		}
	}
	else{
		$arFields["STATUS"] = "D";
		$ID = $posting->Add($arFields);
		$res = ($ID > 0);
	}

	/* if($res){
		//Delete checked
		if(is_array($FILE_ID)){
			foreach($FILE_ID as $file){
				SMSCPosting::DeleteFile($ID, $file);
			}
		}

		//New files
		$arFiles = array();

		//Copy
		if(array_key_exists("FILES", $_POST) && is_array($_POST["FILES"])){
			if(intval($COPY_ID) > 0){
				//Files from imaginweb.sms_posting_edit.php
				foreach($_POST["FILES"] as $key => $file_id){
					//skip "deleted"
					if(is_array($FILE_ID) && array_key_exists($key, $FILE_ID)){
						continue;
					}
					//clone file
					if(intval($file_id) > 0){
						$rsFile = SMSCPosting::GetFileList($COPY_ID, $file_id);
						if($ar = $rsFile->Fetch()){
							$arFiles[] = CFile::MakeFileArray($ar["ID"]);
						}
					}
				}
			}
			else{
				//Files from template_test.php
				foreach($_POST["FILES"] as $arFile){
					if(is_array($arFile)){
						$arFiles[] = $arFile;
					}
				}
			}
		}

		//Brandnew
		if(is_array($_FILES["NEW_FILE"])){
			foreach($_FILES["NEW_FILE"] as $attribute=>$files){
				if(is_array($files)){
					foreach($files as $index=>$value){
						$arFiles[$index][$attribute]=$value;
					}
				}
			}
		}
		foreach($arFiles as $file){
			if(strlen($file["name"])>0 and intval($file["size"])>0){
				$res = $posting->SaveFile($ID, $file);
				if(!$res){
					break;
				}
			}
		}
	} */

	if($res){
		if($Send != "" || $Resend != "" || $Continue != ""){
			LocalRedirect("imaginweb.sms_posting_admin.php?ID=".$ID."&action=send&lang=".LANG."&".bitrix_sessid_get());
		}
		if($apply != ""){
			$_SESSION["SESS_ADMIN"]["POSTING_EDIT_MESSAGE"] = array("MESSAGE" => GetMessage("post_save_ok"), "TYPE" => "OK");
			LocalRedirect("imaginweb.sms_posting_edit.php?ID=".$ID."&lang=".LANG."&".$tabControl->ActiveTabParam());
		}
		else{
			LocalRedirect("imaginweb.sms_posting_admin.php?lang=".LANG);
		}
	}
	else{
		if($e = $APPLICATION->GetException()){
			$message = new CAdminMessage(GetMessage("post_save_error"), $e);
		}
		$bVarsFromForm = true;
	}
}

ClearVars();
$str_STATUS = "D";
$str_DIRECT_SEND = "Y";
$str_BODY_TYPE = (COption::GetOptionString("imaginweb.sms", "posting_use_editor") == "Y"? "html" : "text");
$str_FROM_FIELD = COption::GetOptionString("imaginweb.sms", "default_from");
$str_TO_FIELD = COption::GetOptionString("imaginweb.sms", "default_to");
$str_AUTO_SEND_FLAG = "N";
$str_AUTO_SEND_TIME = ConvertTimeStamp(time() + CTimeZone::GetOffset(), "FULL");

if($ID > 0){
	$post = SMSCPosting::GetByID($ID);
	if(!($post_arr = $post->ExtractFields("str_"))){
		$ID = 0;
	}
}

if($bVarsFromForm){
	if(!array_key_exists("DIRECT_SEND", $_REQUEST)){
		$DIRECT_SEND = "N";
	}
	$DB->InitTableVarsForEdit("iwebsms_posting", "", "str_");
	if(array_key_exists("AUTO_SEND_FLAG", $_REQUEST)){
		$str_AUTO_SEND_FLAG = "Y";
	}
	else{
		$str_AUTO_SEND_FLAG = "N";
	}
}
elseif($ID > 0){
	if(strlen($str_AUTO_SEND_TIME)){
		$str_AUTO_SEND_FLAG = "Y";
	}
	else{
		$str_AUTO_SEND_FLAG = "N";
	}	
}

$APPLICATION->SetTitle(($ID > 0 && !$bCopy ? GetMessage("post_title_edit").$ID : GetMessage("post_title_add")));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$aMenu = array(
	array(
		"TEXT"=>GetMessage("post_mnu_list"),
		"TITLE"=>GetMessage("post_mnu_list_title"),
		"LINK"=>"imaginweb.sms_posting_admin.php?lang=".LANG,
		"ICON"=>"btn_list",
	)
);
if($ID > 0 && !$bCopy)
{
	$aMenu[] = array("SEPARATOR" => "Y");
	$aMenu[] = array(
		"TEXT" => GetMessage("post_mnu_add"),
		"TITLE" => GetMessage("post_mnu_add_title"),
		"LINK" => "imaginweb.sms_posting_edit.php?lang=".LANG,
		"ICON" => "btn_new",
	);
	$aMenu[] = array(
		"TEXT" => GetMessage("post_mnu_copy"),
		"TITLE" => GetMessage("post_mnu_copy_title"),
		"LINK" => "imaginweb.sms_posting_edit.php?ID=".$ID."&amp;action=copy&amp;lang=".LANG,
		"ICON" => "btn_copy",
	);
	$aMenu[] = array(
		"TEXT" => GetMessage("post_mnu_del"),
		"TITLE" => GetMessage("post_mnu_del_title"),
		"LINK" => "javascript:if(confirm('".GetMessage("post_mnu_confirm")."'))window.location='imaginweb.sms_posting_admin.php?ID=".$ID."&action=delete&lang=".LANG."&".bitrix_sessid_get()."';",
		"ICON" => "btn_delete",
	);
}
$context = new CAdminContextMenu($aMenu);
$context->Show();

if(is_array($_SESSION["SESS_ADMIN"]["POSTING_EDIT_MESSAGE"])){
	CAdminMessage::ShowMessage($_SESSION["SESS_ADMIN"]["POSTING_EDIT_MESSAGE"]);
	$_SESSION["SESS_ADMIN"]["POSTING_EDIT_MESSAGE"] = false;
}

if($message){
	echo $message->Show();
}
elseif($posting->LAST_ERROR != ""){
	CAdminMessage::ShowMessage($posting->LAST_ERROR);
}
?>

<form method="POST" Action="<?=$APPLICATION->GetCurPage();?>"  ENCTYPE="multipart/form-data" name="post_form">
	<?$tabControl->Begin();?>
	<?
	//********************
	//Posting issue
	//********************
	$tabControl->BeginNextTab();
	?>
		<tr class="heading">
			<td colspan="2"><?=GetMessage("post_info")?></td>
		</tr>
	<?if($ID > 0 && !$bCopy):?>
		<tr>
			<td><?=GetMessage("post_date_upd")?></td>
			<td><?=$str_TIMESTAMP_X;?></td>
		</tr>
		<?if(strlen($str_DATE_SENT)>0):?>
		<tr>
			<td><?=GetMessage("post_date_sent")?></td>
			<td><?=$str_DATE_SENT;?></td>
		</tr>
		<?endif;?>
		<?
		$arSmsStatuses = SMSCPosting::GetSmsStatuses($ID);
		if(array_key_exists("Y", $arSmsStatuses) || array_key_exists("E", $arSmsStatuses)):?>
		<tr>
			<td><?=GetMessage("POST_TO")?></td>
			<td>[&nbsp;<a class="tablebodylink" href="javascript:void(0)" OnClick="jsUtils.OpenWindow('posting_bcc.php?ID=<?=$str_ID?>&lang=<?=LANG?>&find_status_id=E&set_filter=Y', 600, 500);"><?=GetMessage("POST_SHOW_LIST")?></a>&nbsp;]</td>
		</tr>
		<?endif;?>
	<?endif; //ID?>
		<tr>
			<td width="40%"><?=GetMessage("post_stat")?></td>
			<td width="60%">
	<?
	if($ID > 0 && !$bCopy){
		if($str_STATUS=="D") echo GetMessage("POST_STATUS_DRAFT");
		if($str_STATUS=="S") echo GetMessage("POST_STATUS_SENT");
		if($str_STATUS=="P") echo GetMessage("POST_STATUS_PART");
		if($str_STATUS=="E") echo GetMessage("POST_STATUS_ERROR");
		if($str_STATUS=="W") echo GetMessage("POST_STATUS_WAIT");
	}
	else{
		echo GetMessage("POST_STATUS_DRAFT");
	}
	?>
			</td>
		</tr>
		<?if($ID>0 && !$bCopy && $str_STATUS!="D"):?>
			<tr>
				<td><?=GetMessage("post_status_change")?></td>
				<td>
				<select class="typeselect" name="STATUS">
					<option value=""><?=GetMessage("post_status_not_change")?></option>
					<?if($str_STATUS <> "D" && $str_STATUS <> "P"):?>
						<option value="D"><?=GetMessage("POST_STATUS_DRAFT")?></option>
					<?endif;?>
					<?if($str_STATUS == "P"):?>
						<option value="W"><?=GetMessage("POST_STATUS_WAIT")?></option>
					<?endif;?>
				</select>
				</td>
			</tr>
		<?endif;?>
		<tr class="heading">
			<td colspan="2"><?=GetMessage("post_fields")?></td>
		</tr>
		<tr>
			<td><span class="required">*</span><?=GetMessage("post_fields_from")?></td>
			<td><input type="text" name="FROM_FIELD" value="<?=$str_FROM_FIELD;?>" size="30" maxlength="11"></td>
		</tr>
		<tr>
			<td><?=GetMessage("post_fields_to")?></td>
			<td><input type="text" name="TO_FIELD" value="<?=$str_TO_FIELD;?>" size="30" maxlength="255"></td>
		</tr>
		<tr>
			<td><span class="required">*</span><?=GetMessage("post_fields_subj")?></td>
			<td><input type="text" name="SUBJECT" value="<?=$str_SUBJECT;?>" size="30" maxlength="255"></td>
		</tr>
		<tr class="heading">
			<td colspan="2"><span class="required">*</span><?=GetMessage("post_fields_text")?><span class="required"><sup>1</sup></span></td>
		</tr>
		<?if(COption::GetOptionString("imaginweb.sms", "posting_use_editor")=="Y" && CModule::IncludeModule("fileman")):?>
			<tr>
				<td colspan="2">
				<?
				CFileMan::AddHTMLEditorFrame("BODY", $str_BODY, "BODY_TYPE", $str_BODY_TYPE, 400, "N", 0, "", "", SITE_ID);
				?>
				</td>
			</tr>
		<?else:?>
			<tr>
				<td colspan="2">
				<textarea name="BODY" style="width:100%; height:100px;" maxlength="<?=COption::GetOptionString("imaginweb.sms", "subscribe_max_lenght");?>"><?=$str_BODY;?></textarea>
				</td>
			</tr>
		<?endif;?>
	<?
	//********************
	//Receipients
	//********************
	$tabControl->BeginNextTab();
	?>
		<tr class="heading">
			<td colspan="2"><?=GetMessage("post_subscr")?></td>
		</tr>
		<tr>
			<td width="50%"><?=GetMessage("post_rub")?></td>
			<td width="50%">
				<input type="checkbox" id="RUB_ID_ALL" name="RUB_ID_ALL" value="Y" OnClick="CheckAll('RUB_ID', true)">
				<label for="RUB_ID_ALL"><?=GetMessage("MAIN_ALL")?></label><br>
				<?
				$aPostRub = array();
				if($ID > 0){
					$post_rub = SMSCPosting::GetRubricList($ID);
					while($ar = $post_rub->Fetch())
						$aPostRub[] = $ar["ID"];
				}
				if(!is_array($RUB_ID)){
					$RUB_ID = array();
				}
				
				$rub = SMSCRubric::GetList(array("LID"=>"ASC", "SORT"=>"ASC", "NAME"=>"ASC"), array("ACTIVE"=>"Y"));
				?>
				<?while($ar = $rub->GetNext()):?>
					<input type="checkbox" id="RUB_ID_<?=$ar["ID"]?>" name="RUB_ID[]" value="<?=$ar["ID"]?>"<?if(in_array($ar["ID"], ($bVarsFromForm? $RUB_ID:$aPostRub))) echo " checked"?> OnClick="CheckAll('RUB_ID')">
					<label for="RUB_ID_<?=$ar["ID"]?>"><?="[".$ar["LID"]."] ".$ar["NAME"]?></label><br>
				<?endwhile;?>
			</td>
		</tr>
		<tr class="heading">
			<td colspan="2"><?=GetMessage("post_users")?></td>
		</tr>
		<tr>
			<td><?=GetMessage("post_groups")?></td>
			<td>
				<input type="checkbox" id="GROUP_ID_ALL" name="GROUP_ID_ALL" value="Y" OnClick="CheckAll('GROUP_ID', true)">
				<label for="GROUP_ID_ALL"><?=GetMessage("MAIN_ALL")?></label><br>
				<?
				$aPostGrp = array();
				if($ID > 0){
					$post_grp = SMSCPosting::GetGroupList($ID);
					while($post_grp_arr = $post_grp->Fetch())
						$aPostGrp[] = $post_grp_arr["ID"];
				}
				if(!is_array($GROUP_ID)){
					$GROUP_ID = array();
				}
				$group = CGroup::GetList(($by="sort"), ($order="asc"));
				while($ar = $group->GetNext()):
				?>
					<input type="checkbox" id="GROUP_ID_<?=$ar["ID"]?>" name="GROUP_ID[]" value="<?=$ar["ID"]?>"<?if(in_array($ar["ID"], ($bVarsFromForm? $GROUP_ID: $aPostGrp))) echo " checked"?> OnClick="CheckAll('GROUP_ID')">
					<label for="GROUP_ID_<?=$ar["ID"]?>"><?=$ar["NAME"]?>&nbsp;[<a href="/bitrix/admin/group_edit.php?ID=<?=$ar["ID"]?>&amp;lang=<?=LANGUAGE_ID?>"><?=$ar["ID"]?></a>]</label><br>
				<?
					$n++;
				endwhile;
			?>
			</td>
		</tr>
		<tr class="heading">
			<td colspan="2"><?=GetMessage("post_filter_title")?></td>
		</tr>
		<tr>
			<td><?=GetMessage("post_filter")?></td>
			<td><input type="text" name="PHONE_FILTER" id="PHONE_FILTER" value="<?=$str_PHONE_FILTER;?>" size="30" maxlength="255"></td>
		</tr>
		<tr>
			<td colspan="2" align="center">
			<script language="JavaScript">
			<!--
			function ShowEMails()
			{
				var strParam = 'PHONE_FILTER='+escape(document.post_form.PHONE_FILTER.value);
				var aCheckBox;
				try
				{
					if('['+document.post_form.elements['RUB_ID[]'].type+']'=='[undefined]')
						aCheckBox = document.post_form.elements['RUB_ID[]'];
					else
						aCheckBox = new Array(document.post_form.elements['RUB_ID[]']);

					for(i=0; i<aCheckBox.length; i++)
						if(aCheckBox[i].checked)
							strParam += ('&RUB_ID[]='+aCheckBox[i].value);
				}
				catch (e)
				{
					//there is no rubrics so we can safely ignore
				}
				if('['+document.post_form.elements['GROUP_ID[]'].type+']'=='[undefined]')
					aCheckBox = document.post_form.elements['GROUP_ID[]'];
				else
					aCheckBox = new Array(document.post_form.elements['GROUP_ID[]']);

				for(i=0; i<aCheckBox.length; i++)
					if(aCheckBox[i].checked)
						strParam += ('&GROUP_ID[]='+aCheckBox[i].value);

				jsUtils.OpenWindow('imaginweb.sms_posting_search.php?'+strParam+'&lang=<?echo LANG?>', 600, 500);
			}
			function CheckAll(prefix, act)
			{
				var bCheck = document.getElementById(prefix+'_ALL').checked;
				var bAll = true;
				var aCheckBox;
				try
				{
					if('['+document.post_form.elements[prefix+'[]'].type+']'=='[undefined]')
						aCheckBox = document.post_form.elements[prefix+'[]'];
					else
						aCheckBox = new Array(document.post_form.elements[prefix+'[]']);

					for(i=0; i<aCheckBox.length; i++)
					{
						if(act)
						{
							if(bCheck)
								aCheckBox[i].checked = true;
							else
								aCheckBox[i].checked = false;
						}
						else
							bAll = bAll && aCheckBox[i].checked;
					}
				}
				catch (e)
				{
					//there is no rubrics so we can safely ignore
				}
				if(!act)
					document.getElementById(prefix+'_ALL').checked = bAll;
			}
			CheckAll('RUB_ID');
			CheckAll('GROUP_ID');
			//-->
			</script>[ <a class="tablebodylink" title="<?=GetMessage("post_list_title")?>" href="javascript:ShowEMails()"><?=GetMessage("post_filter_list")?></a> ]</td>
		</tr>
		<tr class="heading">
			<td colspan="2"><?=GetMessage("post_additional")?></td>
		</tr>
		<tr>
			<td align="center" colspan="2"><textarea name="BCC_FIELD" cols="50" rows="7" style="width:100%"><?=$str_BCC_FIELD?></textarea></td>
		</tr>
	<?
	//********************
	//Parameters
	//********************
	$tabControl->BeginNextTab();
	?>
		<!-- <tr class="heading">
			<td colspan="2"><?=GetMessage("post_send_params")?></td>
		</tr>
		<tr>
			<td width="50%"><?=GetMessage("post_direct")?></td>
			<td width="50%">
				<input type="checkbox" name="DIRECT_SEND" value="Y"<?if($str_DIRECT_SEND <> "N") echo " checked"?>>
			</td>
		</tr> -->
		<?if($str_STATUS == "D" || $str_STATUS == "W"):?>
		<tr>
			<td width="50%"><?=GetMessage("post_send_flag")?></td>
			<td width="50%">
				<input type="checkbox" name="AUTO_SEND_FLAG" value="Y"<?if($str_AUTO_SEND_FLAG == "Y") echo " checked"?> OnClick="EnableAutoSend()">
			</td>
		</tr>
		<tr>
			<td><?=GetMessage("post_send_time"). " (".FORMAT_DATETIME."):"?><span class="required"><sup>1</sup></span></td>
			<td><?=CalendarDate("AUTO_SEND_TIME", $str_AUTO_SEND_TIME, "post_form", "20")?></td>
		</tr>
	<script language="JavaScript">
	<!--
	function EnableAutoSend()
	{
		document.post_form.AUTO_SEND_TIME.disabled = !document.post_form.AUTO_SEND_FLAG.checked;
	}
	EnableAutoSend();
	//-->
	</script>
		<?else:
		$str_AUTO_SEND_FLAG = strlen($str_AUTO_SEND_TIME)? "Y": "N";
		?>
		<tr>
			<td width="50%"><?=GetMessage("post_send_flag")?></td>
			<td width="50%"><? echo ($str_AUTO_SEND_FLAG == "Y" ? GetMessage("post_yes") : GetMessage("post_no"))?></td>
		</tr>
		<tr>
			<td><?=GetMessage("post_send_time"). " (".FORMAT_DATETIME."):"?><span class="required"><sup>1</sup></span>
			<input type="hidden" name="AUTO_SEND_FLAG" value="<?=$str_AUTO_SEND_FLAG?>">
			<input type="hidden" name="AUTO_SEND_TIME" value="<?=$str_AUTO_SEND_TIME?>">
			</td>
			<td><?=$str_AUTO_SEND_TIME?></td>
		</tr>
		<?endif;?>
	<?
	$tabControl->Buttons(
		array(
			"disabled"=>($POST_RIGHT<"W"),
			"back_url"=>"imaginweb.sms_posting_admin.php?lang=".LANG,

		)
	);
	?>
	<?=bitrix_sessid_post();?>
	<input type="hidden" name="lang" value="<?=LANG?>">
	<?if($str_STATUS=="D"):?>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input <?if ($POST_RIGHT<"W") echo "disabled" ?> type="submit" value="<?=GetMessage("post_butt_send")?>" name="Send" title="<?=GetMessage("post_hint_send")?>">
	<?elseif($str_STATUS=="W"):?>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input <?if ($POST_RIGHT<"W") echo "disabled" ?> type="submit" value="<?=GetMessage("post_continue")?>" name="Continue" title="<?=GetMessage("post_continue_conf")?>">
	<?elseif($str_STATUS=="E"):?>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input <?if ($POST_RIGHT<"W") echo "disabled" ?> type="submit" value="<?=GetMessage("post_resend")?>" name="Resend" title="<?=GetMessage("post_resend_conf")?>">
	<?endif?>
	<?if($ID > 0):?>
		<?if($bCopy):?>
			<input type="hidden" name="COPY_ID" value="<?=$ID?>">
		<?else:?>
			<input type="hidden" name="ID" value="<?=$ID?>">
		<?endif?>
	<?endif;?>
	<?
	$tabControl->End();
	?>
</form>

<?
$tabControl->ShowWarnings("post_form", $message);
?>

<?=BeginNote();?>
<span class="required">*</span><?=GetMessage("REQUIRED_FIELDS")?><br>
<br>
<span class="required"><sup>1</sup></span><?=GetMessage("post_send_msg")?><br>
<?=EndNote();?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>

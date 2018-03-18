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
	array("DIV" => "edit1", "TAB" => GetMessage("imp_import_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("imp_import_tab_title")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs, true, true);

$arError = array();
$bShowRes = false;
if(!is_array($USER_GROUP_ID)){
	$USER_GROUP_ID = array();
}
	

if($REQUEST_METHOD == "POST" && !empty($Import) && $POST_RIGHT >= "W" && check_bitrix_sessid()){
	//*************************************
	//Prepare emails
	//*************************************
	//This is from the form
	$sAddr = $ADDR_LIST.",";
	//And this is from the file
	if(!empty($_FILES["ADDR_FILE"]["tmp_name"])){
		if((integer)$_FILES["ADDR_FILE"]["error"] <> 0){
			$arError[] = array("id"=>"ADDR_FILE", "text"=>GetMessage("subscr_imp_err1")." (".GetMessage("subscr_imp_err2")." ".$_FILES["ADDR_FILE"]["error"].")");
		}
		else{
			$sAddr .= file_get_contents($_FILES["ADDR_FILE"]["tmp_name"]);
		}	
	}
	
	//explode to emails array
	$aPhone = array();
	$addr = strtok($sAddr, ", \r\n\t");
	while($addr!==false){
		if(strlen($addr) > 0){
			$aPhone[$addr] = true;
		}
		$addr = strtok(", \r\n\t");
	}

	//check for duplicate emails
	$addr = SMSCSubscription::GetList();
	while($addr_arr = $addr->Fetch()){
		if(isset($aPhone[$addr_arr["PHONE"]])){
			unset($aPhone[$addr_arr["PHONE"]]);
		}
	}
	
	//*************************************
	//add users and imaginweb.smsrs
	//*************************************

	//constant part of the imaginweb.smsr
	$subscr = new SMSCSubscription;
	$arFields = Array(
		"ACTIVE" => "Y", 
		"FORMAT" => ($FORMAT <> "html"? "text":"html"), 
		"CONFIRMED" => "Y"/*($CONFIRMED <> "Y"? "N":"Y")*/,
		"SEND_CONFIRM" => "N"/*($SEND_CONFIRM <> "Y"? "N":"Y")*/,
		"RUB_ID" => $RUB_ID,
		"ALL_SITES"	=> "Y"
	);
	
	//constant part of the user
	/* if($USER_TYPE == "U"){
		$user = new CUser;
	} */

	$nError = 0;
	$nSuccess = 0;
	foreach($aPhone as $phone => $temp){
		$USER_ID = false;
		/* if($USER_TYPE == "U"){
			//add user
			$sPassw = randString(6);
			$arUserFields = Array(
				"LOGIN" => randString(50),
				"CHECKWORD" => randString(8),
				"PASSWORD" => $sPassw,
				"CONFIRM_PASSWORD" => $sPassw,
				"PHONE" => $phone,
				"ACTIVE" => "Y",
				"GROUP_ID" => ($USER->IsAdmin()?$USER_GROUP_ID:array(COption::GetOptionString("main", "new_user_registration_def_group")))
			);
			
			if($USER_ID = $user->Add($arUserFields)){
				$user->Update($USER_ID, array("LOGIN"=>"user".$USER_ID));

				//send registration message
				if($SEND_REG_INFO == "Y")
					$user->SendUserInfo($USER_ID, $LID, GetMessage("subscr_send_info"));
			}
			else{
				$arError[] = array("id"=>"", "text"=>$phone.": ".$user->LAST_ERROR);
				$nError++;
				continue;
			}
		}//$USER_TYPE == "U" */
		
		//add subscription
		$arFields["USER_ID"] = $USER_ID;
		$arFields["PHONE"] = $phone;
		if(!$subscr->Add($arFields)){
			$arError[] = array("id"=>"", "text"=>$phone.": ".$subscr->LAST_ERROR);
			$nError++;
		}
		else{
			$nSuccess++;
		}

	}//foreach
	$bShowRes = true;
}//$REQUEST_METHOD=="POST"
else{
	//default falues
	$CONFIRMED = "Y";
	$USER_TYPE = "A";
	$SEND_REG_INFO = "Y";
	$FORMAT = "text";
	$USER_GROUP_ID = array();
	$RUB_ID = array();
}

$APPLICATION->SetTitle(GetMessage("imp_title"));

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

if(count($arError)>0){
	$e = new CAdminException($arError);
	$message = new CAdminMessage(GetMessage("imp_error"), $e);
	echo $message->Show();
}
?>
<?if($bShowRes):?>
	<p class="text"><b><?=GetMessage("imp_results")?></b><br>
	<?=GetMessage("imp_results_total")?> <b><?=count($aPhone)?></b><br>
	<?=GetMessage("imp_results_added")?> <font class="<?=($nSuccess==0? "required":"pointed")?>"><b><?=$nSuccess?></b></font><br>
	<?=GetMessage("imp_results_err")?> <font class="<?=($nError>0? "required":"pointed")?>"><b><?=$nError?></b></font>
	</p>
<?endif;?>
<form ENCTYPE="multipart/form-data" action="<?=$APPLICATION->GetCurPage();?>" method="POST" name="impform">
<?
$tabControl->Begin();
$tabControl->BeginNextTab();
?>
	<tr class="heading">
		<td colspan="2"><?=GetMessage("imp_delim")?></td>
	</tr>
	<tr>
		<td width="40%"><?=GetMessage("imp_file")?></td>
		<td width="60%"><input type=file name="ADDR_FILE" size=30></td>
	</tr>
	<tr>
		<td><?=GetMessage("imp_list")?></td>
		<td><textarea name="ADDR_LIST" rows=10 cols=45></textarea></td>
	</tr>
	<tr>
		<td><?=GetMessage("imp_subscr")?></td>
		<td><?
		$rubrics = SMSCRubric::GetList(array("LID"=>"ASC", "SORT"=>"ASC", "NAME"=>"ASC"), array("ACTIVE"=>"Y"));
		$n=1;
		while(($rub=$rubrics->Fetch())):
			?><input type="checkbox" id="RUB_ID_<?=$n?>" name="RUB_ID[]" value="<?=$rub["ID"]?>"<?if(!$bShowRes || in_array($rub["ID"], $RUB_ID)) echo " checked"?>><label for="RUB_ID_<?=$n?>"><?="[".$rub["LID"]."]&nbsp;".htmlspecialchars($rub["NAME"])?></label><br><?
			$n++;
		endwhile;
		?></td>
	</tr>
<?
$tabControl->Buttons();
?>
<input<?if($POST_RIGHT<"W") echo " disabled";?> class="button" type="submit" name="Import" value="<?=GetMessage("imp_butt")?>">
<input type="hidden" name="lang" value="<?=LANG?>">
<?=bitrix_sessid_post();?>
<?
$tabControl->End();
?>
</form>

<?
$tabControl->ShowWarnings("impform", $message);
?>

<? require ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>
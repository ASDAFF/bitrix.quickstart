<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/imaginweb.sms/include.php");

IncludeModuleLangFile(__FILE__);

$POST_RIGHT = $APPLICATION->GetGroupRight("imaginweb.sms");
if($POST_RIGHT == "D"){
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

$APPLICATION->SetTitle(GetMessage("post_title"));

$aTabs = array(
	array(
		"DIV" => "edit1",
		"TAB" => GetMessage("post_subscribers"),
		"ICON"=>"main_user_edit",
		"TITLE"=>GetMessage("post_tab_title"),
	),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs, true, true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_popup_admin.php");

?>
<form method="GET" action="imaginweb.sms_posting_search.php" name="post_form">
<?
$tabControl->Begin();
$tabControl->BeginNextTab();
?>
	<tr class="heading">
		<td colspan="2"><?=GetMessage("post_search_rub")?></td>
	</tr>
	<tr>
		<td width="40%"><?=GetMessage("post_rub")?>:</td>
		<td width="60%">
			<input type="checkbox" id="RUB_ID_ALL" name="RUB_ID_ALL" value="Y" OnClick="CheckAll('RUB_ID', true)">
			<label for="RUB_ID_ALL"><?=GetMessage("MAIN_ALL")?></label><br>
			<?
			if(!is_array($RUB_ID)){
				$RUB_ID = array();
			}
			$aRub = array();
			$rub = SMSCRubric::GetList(array("LID"=>"ASC", "SORT"=>"ASC", "NAME"=>"ASC"), array("ACTIVE"=>"Y"));
			?>
			<?while($ar = $rub->GetNext()):?>
				<?
				$aRub[] = $ar["ID"];
				?>
				<input type="checkbox" id="RUB_ID_<?=$ar["ID"]?>" name="RUB_ID[]" value="<?=$ar["ID"]?>"<?if(in_array($ar["ID"], $RUB_ID)) echo " checked"?> OnClick="CheckAll('RUB_ID')">
				<label for="RUB_ID_<?=$ar["ID"]?>"><?="[".$ar["LID"]."] ".$ar["NAME"]?></label><br>
			<?endwhile;?>
		</td>
	</tr>
	<tr class="heading">
		<td colspan="2"><?=GetMessage("post_search_users")?></td>
	</tr>
	<tr>
		<td><?=GetMessage("post_group")?></td>
		<td>
			<input type="checkbox" id="GROUP_ID_ALL" name="GROUP_ID_ALL" value="Y" OnClick="CheckAll('GROUP_ID', true)">
			<label for="GROUP_ID_ALL"><?=GetMessage("MAIN_ALL")?></label><br>
			<?
			if(!is_array($GROUP_ID)){
				$GROUP_ID = array();
			}
			$aGroup = array();
			$group = CGroup::GetList(($by="sort"), ($order="asc"));
			?>
			<?while($ar = $group->GetNext()):?>
				<?
				$aGroup[] = $ar["ID"];
				?>
				<input type="checkbox" id="GROUP_ID_<?=$ar["ID"]?>" name="GROUP_ID[]" value="<?=$ar["ID"]?>"<?if(in_array($ar["ID"], $GROUP_ID)) echo " checked"?> OnClick="CheckAll('GROUP_ID')">
				<label for="GROUP_ID_<?=$ar["ID"]?>"><?=$ar["NAME"]?>&nbsp;[<a target="_blank" href="/bitrix/admin/group_edit.php?ID=<?=$ar["ID"]?>&amp;lang=<?=LANGUAGE_ID?>"><?=$ar["ID"]?></a>]</label><br>
			<?endwhile;?>
		</td>
	</tr>
	<tr class="heading">
		<td colspan="2"><?=GetMessage("post_search_filter")?></td>
	</tr>
	<tr>
		<td><?=GetMessage("post_filter")?></td>
		<td><input type="text" name="PHONE_FILTER" value="<?=htmlspecialchars($PHONE_FILTER)?>" size="30" maxlength="255"></td>
	</tr>
<?
$tabControl->Buttons();
?>
<input type="submit" name="search" value="<?=GetMessage("post_search")?>">
<input type="reset" name="Reset" value="<?=GetMessage("post_reset")?>">
<input type="hidden" name="search" value="search">
<input type="hidden" name="lang" value="<?=LANG?>">
<?
$tabControl->End();
?>
<?
$aPhone = array();

/*subscribers*/
$subscr = SMSCSubscription::GetList(
	array("ID" => "ASC"),
	array("RUBRIC_MULTI" => $RUB_ID, "CONFIRMED" => "Y", "ACTIVE" => "Y", "FORMAT" => $SUBSCR_FORMAT, "PHONE" => $PHONE_FILTER)
);
while($subscr_arr = $subscr->Fetch()){
	$aPhone[$subscr_arr["PHONE"]] = 1;
}

/*users by groups*/
if(is_array($GROUP_ID) && count($GROUP_ID) > 0){
	$FIELD_PHONE = COption::GetOptionString("imaginweb.sms", "subscribe_field_phone");
	$arFilter = array("ACTIVE" => "Y", $FIELD_PHONE => $PHONE_FILTER);
	if(!in_array(2, $GROUP_ID)){
		$arFilter["GROUP_MULTI"] = $GROUP_ID;
	}
	$user = CUser::GetList(($b = "id"), ($o = "asc"), $arFilter);
	while($user_arr = $user->Fetch()){
		if(strlen($user_arr[$FIELD_PHONE]) > 0){
			$aPhone[$user_arr[$FIELD_PHONE]] = 1;
		}
	}
}

$aPhone = array_keys($aPhone);

if(count($aPhone) > 0):?>
	<h2><?=GetMessage("post_result")?></h2>
	<p class="imaginweb.sms_border">
		<?=implode(", ",$aPhone)?>
	</p>
	<p><?=GetMessage("post_total")?> <b><?=count($aPhone);?></b></p><?
else:
	CAdminMessage::ShowMessage(GetMessage("post_notfound"));
endif;?>
<script>
<!--
function SetValues()
{
	var d = window.opener.document;
	d.getElementById('PHONE_FILTER').value="<?=CUtil::JSEscape($PHONE_FILTER)?>";
	<?foreach($aRub as $id):?>
		d.getElementById('RUB_ID_<?=$id?>').checked = <?=(in_array($id, $RUB_ID)? "true":"false")?>;
	<?endforeach?>
	<?foreach($aGroup as $id):?>
		d.getElementById('GROUP_ID_<?=$id?>').checked = <?=(in_array($id, $GROUP_ID)? "true":"false")?>;
	<?endforeach?>
	window.opener.CheckAll('RUB_ID');
	window.opener.CheckAll('GROUP_ID');
	window.close();
}
function CheckAll(prefix, act)
{
	var bAll = true;
	var aCheckBox;
	try
	{
		if('['+document.post_form.elements[prefix+'[]'].type+']'=='[undefined]')
			var aCheckBox = document.post_form.elements[prefix+'[]'];
		else
			var aCheckBox = new Array(document.post_form.elements[prefix+'[]']);

		for(i=0; i<aCheckBox.length; i++)
			if(!aCheckBox[i].checked)
			{
				if(act)
					aCheckBox[i].checked = true;
				else
					bAll = false;
			}
	}
	catch (e)
	{
		//there is no rubrics so we can safely ignore
	}
	document.getElementById(prefix+'_ALL').checked = bAll;
}
CheckAll('RUB_ID');
CheckAll('GROUP_ID');
//-->
</script>
<input title="<?=GetMessage("post_search_set_title")?> (<?=count($aPhone);?>)"  type="button" name="Set" value="<?=GetMessage("post_set")?>" OnClick="SetValues()">
<input type="button" name="Close" value="<?=GetMessage("post_cancel")?>" OnClick="window.close()">
</form>
<?=BeginNote();?>
<?=GetMessage("post_search_note")?>
<?=EndNote();?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_popup_admin.php")?>
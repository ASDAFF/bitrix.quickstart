<?
$module_id = "itena.panel";
if(!CModule::IncludeModule('forum')) 
{
  echo GetMessage("PANEL_O_NOMODULE");
  return; 
}

$RIGHT = $APPLICATION->GetGroupRight($module_id);
if ($RIGHT>="R") :

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/options.php");
IncludeModuleLangFile(__FILE__);

$arAllOptions = Array(
	array("def_period", GetMessage("PANEL_O_DEF_PERIOD")." ", array("text", 4))
);

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB_SET"), "ICON" => "panel_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
	array("DIV" => "edit2", "TAB" => GetMessage("MAIN_TAB_RIGHTS"), "ICON" => "panel_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

if($REQUEST_METHOD=="POST" && strlen($Update.$Apply)>0 && $RIGHT>="W" && check_bitrix_sessid())
{
	$old_forum_id = unserialize(COption::GetOptionString($module_id, "forum_id"));
	$new_forum_id = $HTTP_POST_VARS["forum_id"];
	if($old_forum_id != $new_forum_id)
	{
		COption::SetOptionString($module_id, "forum_id", serialize($new_forum_id));
	}
  
  foreach($arAllOptions as $arOption)
  {
    $name=$arOption[0];
    $val=$_REQUEST[$name];
    COption::SetOptionString($module_id, $name, $val, $arOption[1]);
  }
    
	ob_start();
	$Update = $Update.$Apply;
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");
	ob_end_clean();

	if(strlen($_REQUEST["back_url_settings"]) > 0)
	{
		if((strlen($Apply) > 0))
			LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"]));
		else
			LocalRedirect($_REQUEST["back_url_settings"]);
	}
	else
	{
		LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID));
	}
}
?>
<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($module_id)?>&amp;lang=<?=LANGUAGE_ID?>">
<?
$tabControl->Begin();
$tabControl->BeginNextTab();
?>
<tr class="heading">
  <td valign="top" colspan="2" align="center"><b><?echo GetMessage("main_options_sys")?></b></td>
</tr>
<tr>
  <?
  $forum_id = unserialize(COption::GetOptionString($module_id, "forum_id"));
  $arFilter = array("ACTIVE" => "Y");
  $arOrder = array("SORT"=>"ASC", "NAME"=>"ASC");
  $db_Forum = CForumNew::GetList($arOrder, $arFilter);
  while ($ar_Forum = $db_Forum->Fetch())
  { 
    $forums[] = $ar_Forum;
  }
  if(count($forums)>0):?>
    <td valign="top" width="50%"><?=GetMessage("PANEL_O_FORUMID")?>:&nbsp;</td>
    <td valign="top" width="50%">
      <select name="forum_id[]" multiple size="3">
      <option value=""><?=GetMessage("PANEL_O_NOVALUE")?></option>
      <?
      foreach($forums as $forum)
      {
        echo "<option value=\"".$forum["ID"]."\"";
        if(in_array($forum["ID"], $forum_id)) echo " selected=\"selected\"";
        echo ">".$forum["NAME"]."</option>";
      }
      ?>
      </select>
    </td>
  <?else:?>
    <td align="center">
    <?=GetMessage("PANEL_O_NOMODULE_L1")?> <a href="/bitrix/admin/forum_admin.php?lang=ru"><?=GetMessage("PANEL_O_NOMODULE_L2")?></a>.
    </td>
  <?endif;?>
</tr>
<?
foreach($arAllOptions as $arOption):
$val = COption::GetOptionString($module_id, $arOption[0]);
$type = $arOption[2];
?>
<tr>
  <td valign="top" width="50%">
    <label for="<?echo htmlspecialchars($arOption[0])?>"><?echo $arOption[1]?>:</label>
  </td>
  <td valign="top" width="50%">
    <?if($type[0]=="text"):?>
      <input type="text" size="<?echo $type[1]?>" maxlength="255" value="<?echo htmlspecialchars($val)?>" name="<?echo htmlspecialchars($arOption[0])?>" id="<?echo htmlspecialchars($arOption[0])?>">
    <?endif?>
  </td>
</tr>
<?endforeach?>
<?$tabControl->BeginNextTab();?>
<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>
<?$tabControl->Buttons();?>
	<input <?if ($RIGHT<"W") echo "disabled" ?> type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>">
	<input <?if ($RIGHT<"W") echo "disabled" ?> type="submit" name="Apply" value="<?=GetMessage("MAIN_OPT_APPLY")?>" title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>">
	<?if(strlen($_REQUEST["back_url_settings"])>0):?>
		<input <?if ($RIGHT<"W") echo "disabled" ?> type="button" name="Cancel" value="<?=GetMessage("MAIN_OPT_CANCEL")?>" title="<?=GetMessage("MAIN_OPT_CANCEL_TITLE")?>" onclick="window.location='<?echo htmlspecialchars(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'">
		<input type="hidden" name="back_url_settings" value="<?=htmlspecialchars($_REQUEST["back_url_settings"])?>">
	<?endif?>
 	<?=bitrix_sessid_post();?>
<?$tabControl->End();?>
</form>
<?endif;?>
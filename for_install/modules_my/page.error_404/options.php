<?
if(!$USER->IsAdmin())
	return;

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/options.php");
IncludeModuleLangFile(__FILE__);

$templates = array(0 => '');
$res = CSiteTemplate::GetList();
while($r = $res->Fetch())
  $templates[$r['ID']] = $r['NAME'].' ['.$r['ID'].']';

$arSites = array();
$res = CSite::GetList(($b = ""), ($o = ""), array("ACTIVE" => "Y"));
while($r = $res->Fetch())
	$arSites[$r['LID']] = $r['NAME'];


$arAllOptions = array();
foreach( $arSites as $lid => $siteName )
	$arAllOptions[] = array(
		"template_".$lid,
		GetMessage("ERR404_TEMPLATE_OPT", array('#SITE_NAME#' => $siteName, '#LID#' => $lid)),
		'',
		array("selectbox", $templates),
		$lid
	);

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB_SET"), "ICON" => "ib_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

if($REQUEST_METHOD=="POST" && strlen($Update.$Apply)>0 && check_bitrix_sessid())
{
  foreach($arAllOptions as $arOption)
  {
    $name=$arOption[0];
    $val=$_REQUEST[$name];
    COption::SetOptionString("page.error_404", $name, $val, $arOption[1], $arOption[4]);
  }
	if(strlen($Update)>0 && strlen($_REQUEST["back_url_settings"])>0)
		LocalRedirect($_REQUEST["back_url_settings"]);
	else
		LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
}


$tabControl->Begin();
?>
<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?echo LANGUAGE_ID?>">
<?$tabControl->BeginNextTab();?>
	<?
	foreach($arAllOptions as $arOption):
		$val = COption::GetOptionString("page.error_404", $arOption[0], $arOption[2], $arOption[4]);
		$type = $arOption[3];
	?>
	<tr>
		<td valign="top" width="50%"><?
			if($type[0]=="checkbox")
				echo "<label for=\"".htmlspecialchars($arOption[0])."\">".$arOption[1]."</label>";
			else
				echo $arOption[1];?>:</td>
		<td valign="top" width="50%">
			<?if($type[0]=="checkbox"):?>
				<input type="checkbox" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="Y"<?if($val=="Y")echo" checked";?>>
			<?elseif($type[0]=="text"):?>
				<input type="text" size="<?echo $type[1]?>" maxlength="255" value="<?echo htmlspecialchars($val)?>" name="<?echo htmlspecialchars($arOption[0])?>">
			<?elseif($type[0]=="textarea"):?>
				<textarea rows="<?echo $type[1]?>" cols="<?echo $type[2]?>" name="<?echo htmlspecialchars($arOption[0])?>"><?echo htmlspecialchars($val)?></textarea>
      <?elseif($type[0]=="selectbox"):?>
        <select name="<?echo htmlspecialchars($arOption[0])?>">
          <?if(sizeof($type[1])){?>
            <?foreach($type[1] as $k => $v){?>
              <option value="<?=$k?>" <?=($k == $val ? 'selected="selected"' : '')?>><?=$v?></option>
            <?}?>
          <?}?>
        </select>
			<?endif?>
		</td>
	</tr>
	<?endforeach?>
<?$tabControl->Buttons();?>
	<input type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>">
	<input type="submit" name="Apply" value="<?=GetMessage("MAIN_OPT_APPLY")?>" title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>">
	<?if(strlen($_REQUEST["back_url_settings"])>0):?>
		<input type="button" name="Cancel" value="<?=GetMessage("MAIN_OPT_CANCEL")?>" title="<?=GetMessage("MAIN_OPT_CANCEL_TITLE")?>" onclick="window.location='<?echo htmlspecialchars(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'">
		<input type="hidden" name="back_url_settings" value="<?=htmlspecialchars($_REQUEST["back_url_settings"])?>">
	<?endif?>
	<?=bitrix_sessid_post();?>
<?$tabControl->End();?>
</form>

<?
$module_id = "bestrank.mono";
$module_id_short="mono";

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/include.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/options.php");
IncludeModuleLangFile(__FILE__);



$GKL_RIGHT = $APPLICATION->GetGroupRight($module_id);
if ($GKL_RIGHT>="R") :




$aSite = array();
$rsSites = CSite::GetList($by="sort", $order="asc", Array());
while ($arSite = $rsSites->Fetch())
{
  $siteList[] = Array("ID" => $arSite["ID"], "NAME" => $arSite["NAME"]);
}

$aOptions =  Array(
			"shopFacebook" => Array(GetMessage("BMONO_SS_FB"), Array("text", 60)),
			"shopVk" => Array(GetMessage("BMONO_SS_VK"), Array("text", 60)),
			"shopTwitter" => Array(GetMessage("BMONO_SS_TWITTER"), Array("text", 60)),

			);

$aTabs=array();
foreach($siteList as $val){
		$aTabs[] = Array(
			"DIV"=>"mono".$val["ID"], 
			"TAB" => "[".$val["ID"]."] ".($val["NAME"]), 
			"TITLE" => "[".$val["ID"]."] ".($val["NAME"]), "OPTIONS"=> $aOptions);
}




$aTabs[] =array(
		"DIV" => "rights",
		"TAB" => GetMessage("MAIN_TAB_RIGHTS"),
		"TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS"),
	);

$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin();

include_once($GLOBALS["DOCUMENT_ROOT"]."/bitrix/modules/bestrank.mono/include.php");

$redirect_to_url="";



if($REQUEST_METHOD=="POST" && strlen($Update.$Apply.$RestoreDefaults)>0 && check_bitrix_sessid() && $GKL_RIGHT ="W") {


	if (strlen($RestoreDefaults)>0)
	{
		COption::RemoveOption($module_id_short);
		$z = CGroup::GetList($v1="id",$v2="asc", array("ACTIVE" => "Y", "ADMIN" => "N"));
		while($zr = $z->Fetch())
			$APPLICATION->DelGroupRight($module_id, array($zr["ID"]));

		$redirect_to_url = $APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam();
	
	} else {



		foreach($_POST as $site_id => $arValue){
			
			foreach($aTabs as $i => $aTab)
			{
				foreach($aTab["OPTIONS"] as $name => $arOption)
				{
					$val = $arValue[$name];
					
					if($arOption[1][0]=="checkbox" && $val!="Y")
						$val="N";
					COption::SetOptionString($module_id_short, $name, $val, $arOption[0], $site_id);
				}
			}
		}

		if(strlen($_REQUEST["back_url_settings"])>0) $redirect_to_url=$_REQUEST["back_url_settings"]; 

	}
}


?>
<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&lang=<?echo LANG?>" name="ara">

<?
foreach($siteList as $val){ 
	$tabControl->BeginNextTab();
	foreach($aOptions as $name=>$option){ 
		$value = COption::GetOptionString($module_id_short, $name, false, $val["ID"]);
		$type = $option[1];
	?>
		
			<tr>
				<td valign="top" width="50%"><?=$option[0]?></td>
				<td valign="top" width="50%">
				
					<?if($type[0]=="checkbox"):?>
						<input type="checkbox" name="<?=$val["ID"]?>[<?echo htmlspecialchars($name)?>]" id="<?echo htmlspecialchars($name)?>" value="Y"<?if($value=="Y")echo" checked";?>>
					<?elseif($type[0]=="text"):?>
						<input type="text" size="<?echo $type[1]?>" maxlength="255" value="<?echo htmlspecialchars($value)?>" name="<?=$val["ID"]?>[<?echo htmlspecialchars($name)?>]">
					<?elseif($type[0]=="textarea"):?>
						<textarea rows="<?echo $type[1]?>" cols="<?echo $type[2]?>" name="<?=$val["ID"]?>[<?echo htmlspecialchars($name)?>]"><?echo htmlspecialchars($value)?></textarea>
					<?elseif($type[0]=="selectbox"):?>
						<select  name="<?=$val["ID"]?>[<?echo htmlspecialchars($name)?>]">
							<?foreach($type[1] as $k=>$v):?>
								<option value="<?=$k?>" <? if($value==$k) echo 'selected';?>><?=$v?></option>
							<?endforeach?>
						</select>
					<?endif?>
				
				</td>
			</tr>


	<?}
}?>
 

 
<?
$tabControl->BeginNextTab();
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");
?>

<?$tabControl->Buttons();?>
<input type="submit" <?if ($GKL_RIGHT<"W") echo "disabled" ?> name="Update" value="<?echo GetMessage("MAIN_SAVE")?>">
<input type="hidden" name="Update" value="Y">
<?if(strlen($_REQUEST["back_url_settings"])>0):?>
	<input type="button" name="Cancel" value="<?=GetMessage("MAIN_OPT_CANCEL")?>" title="<?=GetMessage("MAIN_OPT_CANCEL_TITLE")?>" onclick="window.location='<?echo htmlspecialchars(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'">
	<input type="hidden" name="back_url_settings" value="<?=htmlspecialchars($_REQUEST["back_url_settings"])?>">
<?endif?>
<input type="reset" name="reset" value="<?echo GetMessage("MAIN_RESET")?>">
	<input type="submit" <?if ($GKL_RIGHT<"W") echo "disabled" ?> name="RestoreDefaults" title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="return confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>')" value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>">
	<?=bitrix_sessid_post();?>
<?$tabControl->End();?>
</form>
<?
 if(strlen($redirect_to_url)>0) LocalRedirect($redirect_to_url);
?>
<?endif;?>

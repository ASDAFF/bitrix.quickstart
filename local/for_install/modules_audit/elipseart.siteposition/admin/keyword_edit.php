<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/elipseart.siteposition/include.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/elipseart.siteposition/prolog.php");

IncludeModuleLangFile(__FILE__);

$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/admin/keyword_edit.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/region_list.php"));

global $DB;
global $APPLICATION;

$POST_RIGHT = $APPLICATION->GetGroupRight("elipseart.siteposition");
if ($POST_RIGHT == "D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
	
$ID = intval($ID);
$message = null;
$bVarsFromForm = false;
$error = null;

$aTabs = array();
$aTabs[] = array("DIV" => "edit1", "TAB" => GetMessage("EASP_TAB_MAIN"), "ICON" => "", "TITLE" => GetMessage("EASP_TAB_MAIN_TITLE"));

$tabControl = new CAdminTabControl("tabControl", $aTabs);

if($REQUEST_METHOD == "POST" && ($save!="" || $apply!="") && $POST_RIGHT=="W" && check_bitrix_sessid())
{
	$arFields = Array(
		"HOST_ID" => intval($HOST_ID),
		"REGION_ID" => intval($REGION_ID),
		"ACTIVE" => ($ACTIVE <> "Y" ? "N" : "Y"),
		"NAME" => trim($NAME),
		"SORT" => intval($SORT),
	);
	
	if(empty($arFields["HOST_ID"]))
	{
		$error[]["text"] = GetMessage("EASP_HOST_ID_NOT_EMPTY");
		$bVarsFromForm = true;
	}
	if(empty($arFields["NAME"]))
	{
		$error[]["text"] = GetMessage("EASP_KEYWORD_NOT_EMPTY");
		$bVarsFromForm = true;
	}
	if(!$bVarsFromForm)
	{
		if($ID > 0)
		{
			$res = CEASitePositionKeyword::Update($ID, $arFields["HOST_ID"], $arFields["REGION_ID"], $arFields);
		}
		else
		{
			$ID = CEASitePositionKeyword::Add($arFields["HOST_ID"], $arFields["REGION_ID"], $arFields);
			$res = ($ID > 0);
			
			CEASitePositionUpdate::Update($ID,false,"N");
		}
		
		if($res)
		{
			if ($apply != "")
				LocalRedirect("/bitrix/admin/elipseart.siteposition.keyword_edit.php?ID=".$ID."&mess=ok&lang=".LANG."&".$tabControl->ActiveTabParam());
			else
				LocalRedirect("/bitrix/admin/elipseart.siteposition.keyword.php?lang=".LANG);
		}
		else
		{
			if($e = $APPLICATION->GetException())
				$message = new CAdminMessage(GetMessage("EASP_SAVE_ERROR"), $e);
			$bVarsFromForm = true;
		}
	}
}

$str_HOST_ID = "";
$str_REGION_ID = "";
$str_ACTIVE = "Y";
$str_NAME = "";
$str_SORT = 500;

if($ID > 0)
{
	$rubric = CEASitePositionKeyword::GetList(array(),array("ID"=>$ID),false,false,1);
	if(!$rubric->ExtractFields("str_"))
		$ID = 0;
}

if($bVarsFromForm)
	$DB->InitTableVarsForEdit("b_ea_siteposition_keyword", "", "str_");

$aMenu = array(
	array(
		"TEXT"=>GetMessage("EASP_BACK_TO_ADMIN"),
		"LINK"=>'/bitrix/admin/elipseart.siteposition.keyword.php?lang='.$lang,
		"ICON"=>"btn_list",
	),
);

$APPLICATION->SetTitle(($ID > 0 ? GetMessage("EASP_TITLE_EDIT").$ID : GetMessage("EASP_TITLE_ADD")));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$context = new CAdminContextMenu($aMenu);
$context->Show();

if($_REQUEST["mess"] == "ok" && $ID > 0)
	CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage("EASP_KEYWORD_SAVED"), "TYPE"=>"OK"));

if($message)
	echo $message->Show();

if($error)
{
	$e = new CAdminException($error);
	$APPLICATION->ThrowException($e);
	$message = new CAdminMessage(GetMessage("EASP_SAVE_ERROR"), $e);
	echo $message->Show();
}
?>

<form method="POST" name="frm" id="frm" action="/bitrix/admin/elipseart.siteposition.keyword_edit.php?lang=<?echo LANG?>&amp;admin=<?echo ($_REQUEST["admin"]=="Y"? "Y": "N")?>"  ENCTYPE="multipart/form-data">
<?=bitrix_sessid_post()?>
<input type="hidden" name="lang" value="<?=LANG?>" />
<?if($ID>0 && !$bCopy):?>
	<input type="hidden" name="ID" value="<?=$ID?>" />
<?endif;?>
<?if(strlen($_REQUEST["return_url"])>0):?>
	<input type="hidden" name="return_url" value="<?=htmlspecialchars($_REQUEST["return_url"])?>">
<?endif?>
<?
$tabControl->Begin();
$tabControl->BeginNextTab();
?>
	<?if($ID):?>
	<tr>
		<td valign="top" width="40%"><?=GetMessage("EASP_ID")?>:</td>
		<td valign="top" width="60%"><?echo $str_ID?></td>
	</tr>
	<?endif;?>
	<tr>
		<td valign="top"><label for="ACTIVE"><?=GetMessage("EASP_ACTIVE")?>:</label></td>
		<td valign="top">
			<input type="checkbox" id="ACTIVE" name="ACTIVE" value="Y"<?if($str_ACTIVE=="Y")echo " checked"?> />
		</td>
	</tr>
	
	<tr>
		<td valign="top" width="40%"><span class="required">*</span><?=GetMessage("EASP_DOMAIN")?>:</td>
		<td valign="top" width="60%">
		<?if($ID):?>
			<?
			$hostDB = CEASitePositionHost::GetList();
			while($res = $hostDB->Fetch())
			{
				if($str_HOST_ID == $res["ID"])
				{
					?><input type="hidden" name="HOST_ID" value="<?=$str_HOST_ID?>" />
					[<?=$res["SITE_ID"]?>] <?=$res["NAME"]?><?
				}
			}
			?>
		<?else:?>
			<select name="HOST_ID">
				<option value=""></option>
				<?
				CEASitePositionHost::UpdateSiteHost();
				$hostDB = CEASitePositionHost::GetList();
				while($res = $hostDB->Fetch())
				{
					if($str_HOST_ID == $res["ID"])
						$selected = "selected";
					else
						$selected = "";
					
					?><option value="<?=$res["ID"]?>"<?=$selected?>>[<?=$res["SITE_ID"]?>] <?=$res["NAME"]?></option><?
				}
				?>
			</select>
		<?endif;?>
		</td>
	</tr>
	
	<tr>
		<td valign="top" width="40%"><?=GetMessage("EASP_REGION")?>:<span class="required"><sup>1</sup></span></td>
		<td valign="top" width="60%">
		<?if($ID):?>
			<?
			$arRes = array();
			$regionDB = CEASitePositionRegion::GetList();
			while($res = $regionDB->Fetch())
			{
				$res["NAME"] = GetMessage("REG_".$res["CODE"]);
				$arRes[] = $res;
			}
			foreach($arRes as $val)
			{
				if($str_REGION_ID == $val["ID"] && !empty($val["NAME"]))
				{
					?><input type="hidden" name="REGION_ID" value="<?=$str_REGION_ID?>" />
					<?=$val["NAME"]?><?
				}
			}
			?>
		<?else:?>
			<select name="REGION_ID">
				<option value=""></option>
				<?
				$arRes = array();
				$regionDB = CEASitePositionRegion::GetList();
				while($res = $regionDB->Fetch())
				{
					$arRes[$res["ID"]] = GetMessage("REG_".$res["CODE"]);
				}
				asort($arRes);
				foreach($arRes as $key=>$val)
				{
					if(!empty($val))
					{
						if($str_REGION_ID == $key)
							$selected = "selected";
						else
							$selected = "";
						
						?><option value="<?=$key?>" <?=$selected?>><?=$val?></option><?
					}
				}
				?>
			</select>
		<?endif;?>
		</td>
	</tr>

	<tr>
		<td valign="top" ><span class="required">*</span><?=GetMessage("EASP_NAME")?>:</td>
		<td valign="top">
		<?if($ID):?>
			<input type="hidden" name="NAME" value="<?=$str_NAME?>" />
			<?=$str_NAME?>
		<?else:?>
			<input type="text" name="NAME" size="40" maxlength="255"  value="<?echo $str_NAME?>" />
		<?endif;?>
		</td>
	</tr>
	
	<tr>
		<td valign="top" ><?=GetMessage("EASP_SORT")?>:</td>
		<td valign="top">
			<input type="text" name="SORT" size="10"  maxlength="10" value="<?echo $str_SORT?>" />
		</td>
	</tr>

<?
$tabControl->Buttons(array("disabled"=>($POST_RIGHT<"W"), "back_url"=>'elipseart.siteposition.keyword.php?lang='.$lang));
$tabControl->End();
$tabControl->ShowWarnings("post_form", $message);
?>
</form>

<?echo BeginNote();?>
<span class="required">*</span><?=GetMessage("REQUIRED_FIELDS")?>
<br /><br />
<span class="required"><sup>1</sup></span><?=GetMessage("EASP_REGION_SUPPORT")?>
<?echo EndNote();?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>
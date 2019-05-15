<?
#################################################
#   Company developer: ALTASIB                  #
#   Site: http://www.altasib.ru                 #
#   E-mail: dev@altasib.ru                      #
#   Copyright (c) 2006-2015 ALTASIB             #
#################################################

IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");


$PING_RIGHT = $APPLICATION->GetGroupRight("altasib.ping");
if($PING_RIGHT=="D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$module_id = "altasib.ping";
$strWarning = "";

$message = null;
$arAllAllOptions = array();

$URLeror = false;
$siteList = array();
$rsSites = CSite::GetList($by="sort", $order="ASC", Array("ACTIVE" => "Y"));
while ($arSite = $rsSites->Fetch())
{
	$arrSite[$arSite["ID"]] = $arSite;
	$siteList[] = array('ID' => $arSite['LID'], 'NAME' => $arSite['NAME']);
	if($arSite["SERVER_NAME"] == "")
		$URLeror = true;
}
$siteCount = count($siteList);

$arAllAllOptions[] = array(GetMessage("FIND_SYSTEM"));

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("PING_TAB_SET"), "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
	array("DIV" => "edit2", "TAB" => GetMessage("PING_TAB_RIGHTS"), "TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS")),
	array("DIV" => "edit3", "TAB" => GetMessage("PING_RECOMMENDATIONS"), "TITLE" => GetMessage("MAIN_TAB_TITLE_RECOMMENDATIONS")),
);

//Restore defaults
if (($PING_RIGHT>="W") && $_SERVER["REQUEST_METHOD"]=="GET" && strlen($RestoreDefaults)>0 && check_bitrix_sessid())
{
	COption::RemoveOption($module_id);
}
$tabControl = new CAdminTabControl("tabControl", $aTabs);

function ShowParamsHTMLByArray($module_id,$arParams)
{
	foreach($arParams as $Option)
	{
		__AdmSettingsDrawRow($module_id, $Option);
	}
}
//Save options
 
if(strlen($Update.$RestoreDefaults)>0 && check_bitrix_sessid())
{
	if($PING_RIGHT<"W")
		$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

	if(strlen($RestoreDefaults)>0)
	{
		COption::RemoveOption($module_id);
		COption::RemoveOption($module_id, "IBLOCK_ID");
		COption::SetOptionString($module_id, "url_impotant_params", "ID,IBLOCK_ID,SECTION_ID,ELEMENT_ID,PARENT_ELEMENT_ID,FID,TID,MID,UID,VOTE_ID,print,goto");
	}
	else
	{
		COption::SetOptionString($module_id, "send_blog_ping_address", $_REQUEST["textarea"]);
		$arUrlParams = explode(",", str_replace(array(";","."," "), array(",",",",""), $_REQUEST["url_impotant_params"]));
		if(is_array($arUrlParams)){
			foreach($arUrlParams as $Param){
				if(!empty($Param)){
					$arNewUrlParam[] = $Param;
				}
			}
		} else {
			$arNewUrlParam = $arUrlParams;
		}
		$arNewUrlParamStr = implode(",", $arNewUrlParam);

		COption::SetOptionString($module_id, "url_impotant_params", $arNewUrlParamStr);

		foreach($arAllAllOptions as $aOptGroup)
		{

			foreach($aOptGroup as $option)
			{	
				__AdmSettingsSaveOption($module_id, $option);
			}
		}

		if((count($_REQUEST['IBLOCK'])>0) && (is_set($_REQUEST['IBLOCK'])))
		{
			COption::RemoveOption($module_id, "IBLOCK_ID");

			for ($i = 0; $i < $siteCount; $i++)
			{
				$IDs_Ibock = array_keys($_REQUEST["IBLOCK"][$siteList[$i]["ID"]]);
				$IDs_Ibock = implode(",", $IDs_Ibock);
				COption::SetOptionString($module_id, "IBLOCK_ID", $IDs_Ibock, false, $siteList[$i]["ID"]);
			}
		}
		else
		{
			COption::RemoveOption($module_id, "IBLOCK_ID");
		}
	}

	ob_start(); 
	$Update = $Update.$Apply;
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");
	ob_end_clean();

	if(strlen($Update)>0 && strlen($_REQUEST["back_url_settings"])>0){
		LocalRedirect($_REQUEST["back_url_settings"]);
	}
	else
	{
		LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());

	}
}

//******
// Get array with info about iblock
//*****

if (CModule::IncludeModule("iblock"))
{
	$arIblock = array();
	$rsIblock = CIBlock::GetList();
	while($ar_res = $rsIblock->Fetch()){
		$arIblock[] = $ar_res;
	}

	for ($i = 0; $i < $siteCount; $i++){
		$IDs_Ibock = COption::GetOptionString($module_id, 'IBLOCK_ID', '', $siteList[$i]["ID"]);
		$IDs_Ibock = explode(",", $IDs_Ibock);
		$rsIblock = CIBlock::GetList();
		foreach($arIblock as $iblock){
			if (in_array($iblock["ID"], $IDs_Ibock))
			{
				$arIblockOptions[$siteList[$i]["ID"]][$iblock["IBLOCK_TYPE_ID"]][]
					= array("IBLOCK[".$siteList[$i]["ID"]."][".$iblock["ID"]."]", $iblock["NAME"], 'Y', array("checkbox"));
			}
			else
			{
				$arIblockOptions[$siteList[$i]["ID"]][$iblock["IBLOCK_TYPE_ID"]][]
					= array("IBLOCK[".$siteList[$i]["ID"]."][".$iblock["ID"]."]", $iblock["NAME"], 'N', array("checkbox"));
			}
		}
	}
}

?>

<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialcharsEx($mid)?>&amp;lang=<?echo LANG?>">
<?=bitrix_sessid_post();?>
<?
$tabControl->Begin();
$tabControl->BeginNextTab();
?>
	<tr>
		<td colspan="2">
			<div style='background-color: #fff; padding: 0; border-top: 1px solid #8E8E8E; border-bottom: 1px solid #8E8E8E; margin-bottom: 15px;'>
				<div style='background-color: #8E8E8E; height: 30px; padding: 7px; border: 1px solid #fff'>
					<a href='http://www.is-market.ru?param=cl' target='_blank'>
						<img src='/bitrix/images/altasib.ping/is-market.gif' style='float: left; margin-right: 15px;' border='0' />
					</a>
					<div style='margin: 13px 0px 0px 0px'>
						<a href='http://www.is-market.ru?param=cl' target='_blank' style='color: #fff; font-size: 10px; text-decoration: none'><?=GetMessage('ALTASIB_IS')?></a>
					</div>
				</div>
			</div>
		</td>
	</tr>
<?
//Output list to find system
	foreach ($arAllAllOptions as $aOptGroup) {
		ShowParamsHTMLByArray($module_id, $aOptGroup);
	}
//Output field with params of address to ping
$url = COption::GetOptionString($module_id, "send_blog_ping_address","http://ping.blogs.yandex.ru/RPC2\nhttp://rpc.weblogs.com/RPC2\nhttp://blogsearch.google.com/ping/RPC2");
//COption::SetOptionString($module_id,"url_impotant_params","ID, IBLOCK_ID, SECTION_ID, ELEMENT_ID, PARENT_ELEMENT_ID, FID, TID, MID, UID, VOTE_ID, print, goto");
$urlParams = COption::GetOptionString($module_id, "url_impotant_params");

?>

<tr>
	<td valign="top" width="50%" class="field-name">
		<label for='TEXTAREA'><?=GetMessage("BLOG_SEND_BLOG_PING_ADDRESS")?></label>
	</td>
	<td valign="middle" width="50%">
		<textarea cols="50" rows = "10" name='textarea'><?echo $url;?></textarea>
	</td>
</tr>
 <?ShowParamsHTMLByArray($module_id, array(GetMessage("BLOG_SEND_OPT_PING_STAT")));?>
<tr>
	<td valign="top" width="50%" class="field-name">
		<label for='url_impotant_params'><?=GetMessage("PING_MAIN_URL_PARAMS")?></label>
	</td>
	<td valign="middle" width="50%">
		<input type="text" maxlength="255" size="60" value="<?=str_replace(",",", ",$urlParams)?>" name="url_impotant_params" id="url_impotant_params">
	</td>
</tr>

<tr class="heading">
	<td colspan="2"><?=GetMessage("SELECT_IBLOCK")?></td>
</tr>
<tr>
	<td colspan="2">
		<?

		$aTabs2 = Array();
		foreach($siteList as $val)
		{
			$aTabs2[] = Array("DIV"=>"reminder".$val["ID"], "TAB" => "[".$val["ID"]."] ".($val["NAME"]), "TITLE" => "[".$val["ID"]."] ".($val["NAME"]));
		}
		$tabControl2 = new CAdminViewTabControl("tabControl2", $aTabs2);
		$tabControl2->Begin();
		foreach($siteList as $val)
		{
			$arStores = array();
			if (CModule::IncludeModule("catalog"))
			{
				$dbStore = CCatalogStore::GetList(array("SORT" => "DESC", "ID" => "ASC"), array("ACTIVE" => "Y", "SHIPPING_CENTER" => "Y", "+SITE_ID" => $val["ID"]));
				while ($arStore = $dbStore->GetNext())
					$arStores[] = $arStore;
			}

			$tabControl2->BeginNextTab();
			?>
			<table cellspacing="5" cellpadding="0" border="0" width="100%" align="center">
				<?
				//Output list of iblock
				ShowParamsHTMLByArray($module_id, array(GetMessage("SELECT_IBLOCK_FOR_SITE").$val["ID"]));

				$i=0;
				foreach($arIblockOptions[$val["ID"]] as $iblocktype){
					$keys = array_keys($arIblockOptions[$val["ID"]]);
					$arIBType = CIBlockType::GetByIDLang($keys[$i], LANG);
					?>
					<tr>
						<td valign="top" width="50%" class="field-name">
							<label for='IBLOCKTYPE[<?=$val["ID"]?>][<?echo $keys[$i];?>]' ><b><?=$arIBType["NAME"]?></b></label>
						</td> <td></td>
					</tr>
					<?
						foreach($iblocktype as $iblockopt){
						?>
						<tr>
							<td valign="top" width="50%" class="field-name">
								<label for='<?=$iblockopt['0']?>'><?=$iblockopt['1']?></label>
							</td>
							<td valign="middle" width="50%">
								<input type="checkbox" <? if(isset($arControllerOption[$iblockopt[0]]))echo ' disabled title="'.GetMessage("MAIN_ADMIN_SET_CONTROLLER_ALT").'"';?> id="<?=htmlspecialcharsEx($iblockopt[0])?>" name="<?=htmlspecialcharsEx($iblockopt[0])?>" value="Y"<?if($iblockopt[2]=="Y")echo" checked";?><?=$disabled?>>
							</td>
						</tr>
						<?
						}
					$i++;
				}
				?>
			</table>
			<?
		}
		$tabControl2->End();
		?>
	</td>
</tr>

<?$tabControl->BeginNextTab();?>
<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>
<?$tabControl->BeginNextTab();?>
<?

if($URLeror == true){
	echo "<font color='red'>".GetMessage("MAIN_RECOMMENDATIONS_NAME")."</font>";
} else {
	echo "<font color='green'>".GetMessage("MAIN_RECOMMENDATIONS_GOOD")."</font>";
}?>
<br/><br/>

<tr class="heading">
	<td width="33%"><?echo GetMessage("MAIN_SITE_NAME")?></td>
	<td width="33%"><?echo GetMessage("PING_STATUS")?></td>
	<td width="33%"><?echo GetMessage("PING_LINK")?></td>
</tr>

<?foreach($arrSite as $arSite){
	$str_SERVER_NAME = $arSite["SERVER_NAME"];

	?><tr>
		<td style="text-align:center;" valign="top"><?=$arSite["NAME"]?></td>
		<td style="text-align:center;<?if($str_SERVER_NAME==""){echo "color:red;";}else{echo "color:green;";}?>" ><?if($str_SERVER_NAME==""){ echo GetMessage("PING_ANSWER_1");}else{echo $str_SERVER_NAME." ".GetMessage("PING_ANSWER_2");}?></td>
		<td style="text-align:center;"><?if($str_SERVER_NAME==""){echo "<A href= 'http://".$_SERVER["SERVER_NAME"]."/bitrix/admin/site_edit.php?LID=".$arSite["LID"]."&lang=ru'>".GetMessage("LINK_TO_OPTIONS")."</A>";}?></td>
	</tr>

<?}?>


<?$tabControl->Buttons();?>
<script type="text/javascript" >
function RestoreDefault()
{
	if(confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>'))
		window.location = "<?echo $APPLICATION->GetCurPage()?>?RestoreDefaults=Y&lang=<?echo LANG?>&mid=<?echo urlencode($mid)?>&<?=bitrix_sessid_get()?>";
}
</script>
<div align="left">
	<input type="hidden" name="Update" value="Y">
	<input type="submit" <?if(!$USER->IsAdmin())echo " disabled ";?> name="Update" value="<?echo GetMessage("MAIN_SAVE")?>" class="adm-btn-save">
	<input type="reset" <?if (!$USER->IsAdmin()) echo " disabled ";?> name="reset" value="<?echo GetMessage("MAIN_RESET")?>" onClick="window.location.reload()">

	<input type="button" <?if(!$USER->IsAdmin())echo " disabled ";?> title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="RestoreDefault();" value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>">
</div>

<?$tabControl->End();?>

</form>
<?
include_once(dirname(__FILE__)."/constants.php");
global $MESS;
global $arOptions;


include(dirname(__FILE__)."/fields.php");
IncludeModuleLangFile(__FILE__);
include_once(dirname(__FILE__)."/include.php");
$module_id = $sModuleID = $obModule->sModuleID;

$CAT_RIGHT = $obModule->GetGroupRight();

if ($CAT_RIGHT>="R"):
	$obModule->ShowScripts();
	
	$REQUEST_METHOD = $_SERVER["REQUEST_METHOD"];
	$Update = $_REQUEST["Update"];
	$RestoreDefaults = $_REQUEST["RestoreDefaults"];
	if($CAT_RIGHT>="W" && check_bitrix_sessid())
	{
		if ($REQUEST_METHOD=="GET" && strlen($RestoreDefaults)>0)
		{
			COption::RemoveOption($sModuleID);
			$z = CGroup::GetList($v1="id",$v2="asc", array("ACTIVE" => "Y", "ADMIN" => "N"));
			while($zr = $z->Fetch())
				$APPLICATION->DelGroupRight($sModuleID, array($zr["ID"]));
		}
		if($REQUEST_METHOD=="POST" && strlen($Update)>0)
		{

				foreach($_REQUEST["GROUPS"] as $iKey=>$iGroupID)
				{
					if($iGroupID)
					{
						$APPLICATION->SetGroupRight($sModuleID,$iGroupID,$_REQUEST["RIGHTS"][$iKey]);
					}
				}
		
			foreach($arOptions["OPTIONS"] as $sSiteID=>$arSite)
			{
				if($_REQUEST["tabControl_active_tab"]!="edit{$sSiteID}") continue;
				$arRequestData = $_REQUEST[$sSiteID];
				$bNeedRevalidate=false;
				
				if(($arRequestData["api_key"]!=$arSite["api_key"]) || ($arRequestData["partner_id"]!=$arSite["partner_id"]))
				{
					$bNeedRevalidate=true;
				}

				if($bNeedRevalidate)
				{
					COption::SetOptionString($sModuleID, "{$sSiteID}_api_key", $arRequestData["api_key"]);
					COption::SetOptionString($sModuleID, "{$sSiteID}_partner_id", $arRequestData["partner_id"]);
				}
				if(!isset($arRequestData["open_widget"])) $arRequestData["open_widget"]="n";
				COption::SetOptionString($sModuleID, "{$sSiteID}_open_widget", $arRequestData["open_widget"]);
				COption::SetOptionString($sModuleID, "{$sSiteID}_courier_mode", $arRequestData["courier_mode"]);
				COption::SetOptionString($sModuleID, "{$sSiteID}_salt", $arRequestData["salt"]);
				COption::SetOptionString($sModuleID, "{$sSiteID}_button", $arRequestData["button"]);
				COption::SetOptionString($sModuleID, "{$sSiteID}_partner_name", $arRequestData["partner_name"]);
				COption::SetOptionString($sModuleID, "{$sSiteID}_round", $arRequestData["round"]);
				
				if($arRequestData["host_type"]!="another")
				{
					$sHost = $obModule->GetHost($arRequestData["host_type"],"SRC");
					$sApiHost = $obModule->GetHost($arRequestData["host_type"],"API");
				}
				else 
				{
					$sHost = $arRequestData["host"];
					$sApiHost = $arRequestData["host_api"];
				}
				COption::SetOptionString($sModuleID, "{$sSiteID}_host", $sHost);
				COption::SetOptionString($sModuleID, "{$sSiteID}_host_api", $sApiHost);
				COption::SetOptionString($sModuleID, "{$sSiteID}_host_type", $arRequestData["host_type"]);
				
				$bValid = $obModule->CheckValid($sSiteID);
				if(!($ex = $APPLICATION->GetException()))
				{
					COption::SetOptionString($sModuleID, "{$sSiteID}_active", $bValid?"y":"n");
					if(!$bValid)
					{
						$APPLICATION->ThrowException(GetMessage("TCS_VERIFICATION_ERROR", Array ("#SITE_ID#" => $sSiteID)));
						//$sError = GetMessage("TCS_VERIFICATION_ERROR", Array ("#SITE_ID#" => $sSiteID));
						$bError = true;
					}
				}
				
				else $bError = true;
			}
			
		
			$arTableOptions = $arRequestData["OPTIONS"];
			
			foreach($arTableOptions as $iPT=>$arPersonTypeValues)
			{
				foreach($arPersonTypeValues as $sValueCode=>$arValues)
				{
					foreach($arValues as $iKey=>$arValueList)
					{
						
						if($arValueList["TYPE"]=="ANOTHER")
						{
							$arTableOptions[$iPT][$sValueCode][$iKey]["VALUE"] = $arValueList["VALUE_ANOTHER"];
						}
						unset($arTableOptions[$iPT][$sValueCode][$iKey]["VALUE_ANOTHER"]);
					}
				}
				COption::SetOptionString($sModuleID, "person_{$iPT}_data", serialize($arTableOptions[$iPT]));
			}
			
			if(/*!$bError*/0) LocalRedirect($APPLICATION->GetCurPageParam());
			else $arOptions["OPTIONS"] = $obModule->GetSitesData();
			
		}
	}
	
	$aTabs = Array();
	
	foreach($arOptions["OPTIONS"] as $sSiteID=>$arSite)
	{
		$aTabs[] = array(
			"DIV" => "edit{$sSiteID}", 
			"TAB" => GetMessage("TCS_SITE").": ".$sSiteID, 
			"ICON" => "support_settings", 
			"TITLE" => $arSite["site_info"]["NAME"]
		);
	}
	
	$aTabs[] = array("DIV" => "editrights", "TAB" => GetMessage("MAIN_TAB_RIGHTS"), "ICON" => "", "TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS"));

	if($ex = $APPLICATION->GetException()) CAdminMessage::ShowOldStyleError($ex->GetString());
	if($sError) CAdminMessage::ShowOldStyleError($sError);
	$tabControl = new CAdminTabControl("tabControl", $aTabs);
	$tabControl->Begin();
	?>
	
	<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&lang=<?echo LANG?>" name="ara">
	<?=bitrix_sessid_post();?>
	<?foreach($arOptions["OPTIONS"] as $sSiteID=>$arSite):?>
		<?$tabControl->BeginNextTab();?>

			<tr>
				<td valign="top" width="50%">
					<?=GetMessage("TCS_SITE_IDENT");?>
				</td>
				<td valign="top" width="50%">
					<input type="text" size="30" maxlength="255" value="<?=htmlspecialchars($arSite["partner_id"])?>" name="<?=$sSiteID?>[partner_id]">
				</td>
			</tr>
			<tr>
				<td valign="top" width="50%">
					<?=GetMessage("TCS_SITE_NAME");?>
				</td>
				<td valign="top" width="50%">
					<input type="text" size="30" maxlength="255" value="<?=htmlspecialchars($arSite["partner_name"])?>" name="<?=$sSiteID?>[partner_name]">
				</td>
			</tr>
			<tr>
				<td valign="top" width="50%">
					<?=GetMessage("TCS_API_KEY");?>
				</td>
				<td valign="top" width="50%">
					<input type="text" size="30" maxlength="255" value="<?=htmlspecialchars($arSite["api_key"])?>" name="<?=$sSiteID?>[api_key]">
				</td>
			</tr>
			<tr>
				<td valign="top" width="50%">
					<?=GetMessage("TCS_SECRET_PHRASE");?>
				</td>
				<td valign="top" width="50%">
					<input type="text" size="30" maxlength="255" value="<?=htmlspecialchars($arSite["salt"])?>" name="<?=$sSiteID?>[salt]">
				</td>
			</tr>
			<tr>
				<td valign="top" width="50%">
					<?=GetMessage("TCS_OPEN_WIDGET");?>
				</td>
				<td valign="top" width="50%">
					<input type = "checkbox" name = "<?=$sSiteID?>[open_widget]" value="y" <?=$arSite["open_widget"]=="y"?"checked":""?>/>
				</td>
			</tr>
			<tr>
				<td valign="middle" width="50%">
					<?=GetMessage("TCS_COURIER_MODE")?>
				</td>
				<td valign="top" width="50%">
					<?foreach($arCourierModes as $sCode=>$arCourierMode):?>
						<label style="display:block">
							<input type = "radio" name = "<?=$sSiteID?>[courier_mode]" value = "<?=$sCode?>" <?=$arSite["courier_mode"]==$sCode?"checked":""?>/>
							<?=$arCourierMode["NAME"]?>
						</label>
					<?endforeach?>
				</td>			
			</tr>
			<tr>
				<td valign="top" width="50%">
					<?=GetMessage("TCS_HOST");?>
				</td>
				<td valign="top" width="50%">
					<?$bDisabled = true;?>
					<select autocomplete="off" onchange="SelectHost(this);" name="<?=$sSiteID?>[host_type]" class = "sHostSelect">
					<?foreach($arHosts as $sCode=>$arHost):?>
						<?
							$bSelected = ($sCode==$arSite["host_type"]);
							if($arSite["host_type"]=="another" && $sCode=="another")
							{
								$sURL = $arSite["host"];
								$sApiURL = $arSite["host_api"];
								$bDisabled = false;
							}
							else 
							{
								$sURL = $arHost["SRC"];
								$sApiURL = $arHost["API"];
							}
						?>
						<option value = "<?=$sCode?>" <?=($bSelected)?"selected='selected'":""?> url = "<?=$sURL?>" api_url = "<?=$sApiURL?>"><?=$arHost["NAME"]?></option>
					<?endforeach;?>
					</select>
					<br/>
					<p class = "pHostText"><?=GetMessage("TCS_WIDGET_URL")?>:</p>
					<input <?=$bDisabled?"disabled='disabled'":""?> class = "iHostAddress" type="text" size="30" maxlength="255" value="<?=htmlspecialchars($arSite["host"])?>" name="<?=$sSiteID?>[host]">
					<p class = "pHostText"><?=GetMessage("TCS_API_URL")?>:</p>
					<input <?=$bDisabled?"disabled='disabled'":""?> class = "iHostAddress iApi" type="text" size="30" maxlength="255" value="<?=htmlspecialchars($arSite["host_api"])?>" name="<?=$sSiteID?>[host_api]">
				</td>
			</tr>		
			<?if($arSite["active"]!="y"):?>

				<tr>
					<td valign="top" width="50%">
						<?=GetMessage("TCS_STATUS");?>:
					</td>
					<td valign="top" width="50%">
						<?=GetMessage("TCS_INACTIVE");?>
					</td>
				</tr>			
				<tr>
					<td colspan = "2" align = "center">
						<div width = "70%"><?=GetMessage("TCS_LEAVE_OFFER");?></div>
					</td>
				</tr>
				<tr class = "send<?=$sSiteID?> message hidden">
					<td colspan = "2" align = "center">
						<div></div>
					</td>
				</tr>
				<tr class = "send<?=$sSiteID?> data">
					<td align = "right">
						<?=GetMessage("TCS_SITE");?><span class="required">*</span>
					</td>
					<td align = "left">
						<input  type = "text" readonly="y" name = "SEND[SITE]"  value = "<?=$arSite["site_info"]["SERVER_NAME"]?>"/>
					</td>
				</tr>
				<tr class = "send<?=$sSiteID?> data">					
					<td align = "right">
						<?=GetMessage("TCS_FIO");?><span class="required">*</span>
					</td>
					<td align = "left">
						<input type = "text" name = "SEND[FIO]" value = ""/>
					</td>
				</tr>
				<tr class = "send<?=$sSiteID?> data">					
					<td align = "right">
						<?=GetMessage("TCS_PHONE");?><span class="required">*</span>
					</td>
					<td align = "left">
						<input type = "text" name = "SEND[PHONE]" value = ""/>
					</td>
				</tr>
				<tr class = "send<?=$sSiteID?> data">					
					<td align = "right">
						<?=GetMessage("TCS_EMAIL");?><span class="required">*</span>
					</td>
					<td align = "left">
						<input type = "text" name = "SEND[EMAIL]" value = ""/>
					</td>
				</tr>
				<tr class = "send<?=$sSiteID?> data">					
					<td align = "right">
						<?=GetMessage("TCS_COMMENT");?>
					</td>
					<td align = "left">
						<textarea name = "SEND[COMMENT]"></textarea>
					</td>
				</tr>
				<tr class = "send<?=$sSiteID?> data">					
					<td align = "right">
					</td>
					<td align = "left">
						<button class = "bSendData" onclick = "TKSSendRequest('send<?=$sSiteID?>'); return false;"><?=GetMessage("TCS_SEND_OFFER");?></button>
					</td>
				</tr>
			<?else:?>
				<tr>
					<td valign="top" width="50%">
						<?=GetMessage("TCS_ROUND");?>:
					</td>
					<td valign="top" width="50%">
						<?foreach($arRoundMethods as $sRoundType=>$sRoundName):?>
							<label style="display:block">
								<input type = "radio" name = "<?=$sSiteID?>[round]" value = "<?=$sRoundType?>" <?=$arSite["round"]==$sRoundType?"checked":""?>/>
								<?=$sRoundName?>
							</label>
						<?endforeach?>
					</td>
				</tr>
				<tr>
					<td valign="top" width="50%">
						<?=GetMessage("TCS_STATUS");?>:
					</td>
					<td valign="top" width="50%">
						<?=GetMessage("TCS_ACTIVE");?>
					</td>
				</tr>				
				<tr>
					<td colspan = "2" align="center">
						<script>
							var arFields = <?=$obModule->PHPArrayToJS($arFields)?>;
							var arFieldsName = <?=$obModule->PHPArrayToJS($arFieldsName)?>;
						</script>
						<?
						$aTabs1 = array();
						foreach($arSite["person_types"] as $arPersonType)
						{
							$aTabs1[] = Array("DIV"=>"oedit".$arPersonType["ID"], "TAB" => $arPersonType["NAME"], "TITLE" => $arPersonType["NAME"]);
						}
						$aTabs1[] = Array("DIV"=>"oedit_button", "TAB" => GetMessage("TCS_BUTTON_STYLE"), "TITLE" => GetMessage("TCS_BUTTON_STYLE"));
						$aTabs1[] = Array("DIV"=>"oedit_button2", "TAB" => GetMessage("TCS_INSERT_BUTTONS"), "TITLE" => GetMessage("TCS_INSERT_BUTTONS"));
						$tabControl1 = new CAdminViewTabControl("tabControl1", $aTabs1);
						$tabControl1->Begin();
						foreach($arSite["person_types"] as $val)
						{
							$tabControl1->BeginNextTab();
							?>
							<table class = "internal" width = "80%">

								<?$arSelected = count($val["DATA"]["PHONE"])?$val["DATA"]["PHONE"]:Array($arOptionDefaults["PHONE"]);
								$arRow = Array(
									"NAME"=>GetMessage("TCS_PHONE"),
									"CODE"=>"PHONE",
									"SELECTED"=>$arSelected
								);
								require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/{$sModuleID}/row.php");?>
								
							
								<?$arSelected = count($val["DATA"]["EMAIL"])?$val["DATA"]["EMAIL"]:Array($arOptionDefaults["EMAIL"]);
								$arRow = Array(
									"NAME"=>GetMessage("TCS_EMAIL"),
									"CODE"=>"EMAIL",
									"SELECTED"=>$arSelected
								);
								require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/{$sModuleID}/row.php");?>						
							</table>
							<?
						}
						$tabControl1->BeginNextTab();?>
							<table class = "internal">
								<tr>
									<td width = "50">
										<?=GetMessage("TCS_BUTTON_VIEW");?>
									</td>
									<td>
										<?=GetMessage("TCS_ORDER_VIEW");?>
									</td>
									<td>
										<?=GetMessage("TCS_CODE");?>
									</td>
								</tr>							
								<?foreach($arButtonTypes as $sType):?>
									<tr>
										<td width = "50">
											<img src = "<?=$obModule->GetHost("main","SRC")?>/button/index.php?n=<?=$sType?>&a=1234.5"/>
										</td>
										<td>
											<input id = "but_<?=$sType?>" <?=($sType==IntVal($arSite["button"]))?"checked":""?> type = "radio" name = "<?=$sSiteID?>[button]" value = "<?=$sType?>"/>
												
											<label for = "but_<?=$sType?>">
												<?=GetMessage("TCS_TYPE");?> <?=$sType?>
											</label>
										</td>
										<td>
											<nobr><font color="#3434FF">&lt;img </font><font color="#FF4040">src</font> = <font color="#3434FF">"</font><font color="#8034FF"><?=$obModule->GetHost("main","SRC")?>/button/index.php?n=<?=$sType?>&a=1234<font color="#3434FF">"</font><font color="#3434FF">/&gt;</font></nobr>
										</td>
									</tr>
								
								<?endforeach;?>
													
							</table>	
						<?$tabControl1->BeginNextTab();
							require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/{$module_id}/admin/tcsbank_buttons.php");
						$tabControl1->End();?>				
					</td>
				</tr>
			<?endif;?>
		<?endforeach;?>
	<?
	$tabControl->BeginNextTab();
	?>
		<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>
		<?$tabControl->Buttons();?>
		<script language="JavaScript">
		function RestoreDefaults()
		{
			if (confirm('<?echo AddSlashes(GetMessage("TCS_MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>'))
				window.location = "<?echo $APPLICATION->GetCurPage()?>?RestoreDefaults=Y&lang=<?echo LANG?>&mid=<?echo urlencode($mid)?>&<?=bitrix_sessid_get()?>";
		}
		</script>
		<input type="submit" <?if ($CAT_RIGHT<"W") echo "disabled" ?> name="Update" value="<?echo GetMessage("MAIN_SAVE")?>">
		<input type="hidden" name="Update" value="Y">
		<input type="reset" name="reset" value="<?echo GetMessage("MAIN_RESET")?>">
	<?$tabControl->End();?>
	</form>
<?endif;?>

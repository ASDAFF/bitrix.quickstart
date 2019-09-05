<?
/**
 * @category	
 * @link		http://.ru
 * @revision	$Revision$
 * @date		$Date$
 */

$moduleID = 'site.main';
$moduleRight = $APPLICATION->GetGroupRight($moduleID);
if($moduleRight >= "R"):
	IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/options.php");
	IncludeModuleLangFile(__FILE__);
	
	$arTabs = array(
		array(
			'tab' => array(
				"DIV" => "SoapClient", 
				"TAB" => GetMessage("site_MAIN_TAB_SOAP_CLIENT"), 
				"ICON" => $moduleID."_settings", 
				"TITLE" => GetMessage("site_MAIN_TAB_SOAP_CLIENT_TITLE"),
			),
			'options' => array(
				array("site_soapclient_wsdl_cache_enabled", GetMessage("site_WSDL_CACHE_ENABLED"), array("checkbox"), 'default'=>'Y'),
				array("site_soapclient_wsdl_cache", GetMessage("site_WSDL_CACHE_TYPE"), array("selectbox"), 'values'=>array(0=>GetMessage('site_WSDL_CACHE_TYPE_0'), 1=>GetMessage('site_WSDL_CACHE_TYPE_1'), 2=>GetMessage('site_WSDL_CACHE_TYPE_2'), 3=>GetMessage('site_WSDL_CACHE_TYPE_3')), 'default'=>'3'),
				array("site_soapclient_wsdl_cache_ttl", GetMessage("site_WSDL_CACHE_TTL"), array("text",24), 'default'=>'86400'),
				array("site_soapclient_wsdl_cache_dir", GetMessage("site_WSDL_CACHE_DIR"), array("text",64), 'default'=>'/tmp'),
				array("site_soapclient_wsdl_cache_limit", GetMessage("site_WSDL_CACHE_LIMIT"), array("text",24), 'default'=>'10'),
				array("site_soapclient_default_socket_timeout", GetMessage("site_DEFAULT_SOCKET_TIMEOUT"), array("text",24), 'default'=>'60'),
			)
		),
		array(
			'tab' => array(
				"DIV" => "edit2",
				"TAB" => GetMessage("MAIN_TAB_RIGHTS"),
				"ICON" => $moduleID."_settings",
				"TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS")
			),
			'options' => array(),
			'require' => "/bitrix/modules/main/admin/group_rights.php",
		),
		array(
			'tab' => array(
				"DIV" => "edit3",
				"TAB" => GetMessage("site_MAIN_TAB_AMAZON"),
				"ICON" => $moduleID."_settings",
				"TITLE" => GetMessage("site_MAIN_TAB_AMAZON"),
			),
			'options' => array(
				array("amazon_directory", GetMessage("site_AMAZON_DIRECTORY"), array("text", 24), 'default'=>''),
				array("amazon_key", GetMessage("site_AMAZON_KEY"), array("text", 60), 'default'=>''),
				array("amazon_secret_key", GetMessage("site_AMAZON_SECRET"), array("text", 60), 'default'=>''),
				array("amazon_region", GetMessage("site_AMAZON_REGION"), array("text", 60), 'default'=>''),
			),
		),
		array(
			'tab' => array(
				"DIV" => "edit4",
				"TAB" => GetMessage("site_MAIN_TAB_GOOGLE"),
				"ICON" => $moduleID."_settings",
				"TITLE" => GetMessage("site_MAIN_TAB_GOOGLE"),
			),
			'options' => array(
				array("recaptcha_public_key", GetMessage("site_RECAPTCHA_PUBLIC_KEY"), array("text", 24), 'default'=>''),
				array("recaptcha_private_key", GetMessage("site_RECAPTCHA_PRIVATE_KEY"), array("text", 24), 'default'=>''),
			),
		),
		array(
			'tab' => array(
				"DIV" => "edit5",
				"TAB" => GetMessage("site_MAIN_TAB_LOG"),
				"ICON" => $moduleID."_settings",
				"TITLE" => GetMessage("site_MAIN_TAB_LOG"),
			),
			'options' => array(
				array("log_user_block", GetMessage("site_USER_BLOCK"), array("checkbox", "Y"), 'default' => ''),
				array("log_user_download_manual", GetMessage("site_USER_DOWNLOAD_MANUAL"), array("checkbox", "Y"), 'default' => ''),
				array("log_user_install_fr", GetMessage("site_USER_INSTALL_FR"), array("checkbox", "Y"), 'default' => ''),
				array("log_user_get_trial", GetMessage("site_USER_GET_TRIAL"), array("checkbox", "Y"), 'default' => ''),
				array("log_user_update_rating", GetMessage("site_USER_UPDATE_RATING"), array("checkbox", "Y"), 'default' => ''),
				array("log_packets_get", GetMessage("site_PACKETS_GET"), array("checkbox", "Y"), 'default' => ''),
				array("log_packets_cancel", GetMessage("site_PACKETS_CANCEL"), array("checkbox", "Y"), 'default' => ''),
				array("log_packets_confirm", GetMessage("site_PACKETS_CONFIRM"), array("checkbox", "Y"), 'default' => ''),
				array("log_packets_check", GetMessage("site_PACKETS_CHECK"), array("checkbox", "Y"), 'default' => ''),
				array("log_packets_timeout", GetMessage("site_PACKETS_TIMEOUT"), array("checkbox", "Y"), 'default' => ''),
				array("log_events_send", GetMessage("site_EVENTS_SEND"), array("checkbox", "Y"), 'default' => ''),
			),
		),
	);
	
	
	$arTabsConfig = array();
	foreach($arTabs as $tab)
		$arTabsConfig[] = $tab['tab'];
	$tabControl = new CAdminTabControl("tabControl", $arTabsConfig);
	

	
	
	if($moduleRight >= "W" && $_SERVER['REQUEST_METHOD'] == "POST" && strlen($_POST['RestoreDefaults'].$_POST['Update'])>0 && check_bitrix_sessid())
	{
		if($_POST['RestoreDefaults'])
		{
			COption::RemoveOption($moduleID);
			$rsGroups = CGroup::GetList($v1 = "id", $v2 = "asc", array("ACTIVE" => "Y", "ADMIN" => "N"));
			while($arGroup = $rsGroups->Fetch())
				$APPLICATION->DelGroupRight($moduleID, array($arGroup["ID"]));
		}
		if($_POST['Update'])
		{
			foreach($arTabs as $tab)
			{
				if(is_array($tab['options']))
				{
					foreach($tab['options'] as $option)
					{
						if(!is_array($option))
							continue;

						if(in_array($option[2][0], array('checkbox','text','textarea','selectbox')))
						{
							$name = $option[0];
							$val = ${$name};
							if($option[2][0] == "checkbox" && $val != "Y")
								$val = "N";
							if($option[2][0] == "multiselectbox")
								$val = @implode(",", $val);
							
							COption::SetOptionString($moduleID, $name, $val, $option[1]);
						}
					}
					
					if($tab['tab']['DIV'] == 'SoapClient')
					{
						$site_soapclients;
						$arClientCodes = array();
						if(is_array($site_soapclients))
						{
							foreach($site_soapclients as $k=>$v)
							{
								$arSoapClientoldcode = trim($v['oldcode']);
								if(strlen($arSoapClientoldcode) > 0)
									COption::RemoveOption($moduleID, 'site_soapclients['.$oldcode.']');
								$arSoapClient_ = array();
								$arSoapClient_['code'] = trim($v['code']);
								if(strlen($arSoapClient_['code']) <= 0) continue;
								$arSoapClient_['wsdl'] = trim($v['wsdl']);
								if(strlen($arSoapClient_['wsdl']) <= 0) continue;
								$arClientCodes[] = $arSoapClient_['code'];
								$arSoapClient_['params'] = array();
								foreach($v['params'] as $kp => $kv)
								{
									$kp = trim($kp);
									if(strlen($kp)<=0)	continue;
									if(!is_array($kv))
									{
										$kv = trim($kv);
										if(strlen($kv)<=0)	continue;
										$arSoapClient_['params'][$kp] = $kv;
									}
									else
									{
										foreach($kv as $kkv => $vkv)
										{
											$kkv = trim($kkv);
											$vkv = trim($vkv);
											if(strlen($kkv)<=0)	continue;
											if(strlen($vkv)<=0)	continue;
											$arSoapClient_['params'][$kp][$kkv] = $vkv;
										}
									}
								}
								if(strlen($arSoapClient_['params']['encoding']) <= 0) continue;
								COption::SetOptionString($moduleID, 'site_soapclients['.$arSoapClient_['code'].']', serialize($arSoapClient_));
							}
						}
						if($arClientCodes)
							COption::SetOptionString($moduleID, 'site_soapclients_codes', implode(',',$arClientCodes));
					}
					
				}
			} 
		}
		
		LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($moduleID)."&lang=".urlencode(LANGUAGE_ID)."&".$tabControl->ActiveTabParam());
	}
	
	CModule::IncludeModule($moduleID);
	?>
	<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($moduleID)?>&amp;lang=<?=LANGUAGE_ID?>">
		<?
		$tabControl->Begin();
		
		foreach($arTabs as $tab)
		{
			$tabControl->BeginNextTab();
			if(is_array($tab['options']))
			{
				if($tab['tab']['DIV'] == 'SoapClient')
				{
				?>
				<tr class="heading">
					<td colspan="2" align="center" valign="top" ><b><?=GetMessage('site_SOAP_CLIENT_COMMON_SETUP')?> <?=$SoapClientCounter?></b></td>
				</tr>
				<?
				}
			
				foreach($tab['options'] as $arOption)
				{
					$val = COption::GetOptionString($moduleID, $arOption[0], $arOption['default']);
					$type = $arOption[2];
					if(in_array($type[0], array('checkbox','text','textarea','selectbox')))
					{
					?>
						<tr>
							<td valign="top" width="50%">
								<label for="<?echo htmlspecialchars($arOption[0])?>"><?echo $arOption[1]?>:</label>
							<td valign="top" width="50%">
								<?if($type[0]=="checkbox"):?>
									<input type="checkbox" name="<?echo htmlspecialchars($arOption[0])?>" id="<?echo htmlspecialchars($arOption[0])?>" value="Y"<?if($val=="Y")echo" checked";?>>
								<?elseif($type[0]=="text"):?>
									<input type="text" size="<?echo $type[1]?>" maxlength="255" value="<?echo htmlspecialchars($val)?>" name="<?echo htmlspecialchars($arOption[0])?>" id="<?echo htmlspecialchars($arOption[0])?>">
								<?elseif($type[0]=="textarea"):?>
									<textarea rows="<?echo $type[1]?>" cols="<?echo $type[2]?>" name="<?echo htmlspecialchars($arOption[0])?>" id="<?echo htmlspecialchars($arOption[0])?>"><?echo htmlspecialchars($val)?></textarea>
								<?elseif($type[0]=="selectbox"):?>
									<?=SelectBoxFromArray($arOption[0], array('REFERENCE'=>array_values($arOption['values']),'REFERENCE_ID'=>array_keys($arOption['values'])),$val);?>
								<?endif?>
							</td>
						</tr>
					<?
					}
					
				}
			}
			
		
			if($tab['tab']['DIV'] == 'SoapClient')
			{
				$ClientCodes = COption::GetOptionString($moduleID, 'site_soapclients_codes');
				$arClientCodes = explode(',',$ClientCodes);
				$SoapClientCounter = 0;
				
				if($arClientCodes[0] != '')
					$arClientCodes[] = '';
				foreach($arClientCodes as $ClientCodeKey => $ClientCode)
				{
					$val = COption::GetOptionString($moduleID, 'site_soapclients['.$ClientCode.']');
					$arVal = unserialize($val);
					$SoapClientCounter++;
					$hspcClientCode = htmlspecialchars($ClientCodeKey);
				?>
				<tr class="heading">
					<td colspan="2" align="center" valign="top" ><b><?=GetMessage('site_SOAP_CLIENT_N')?> <?=$SoapClientCounter?></b></td>
				</tr>
				<tr>
					<td valign="top" width="50%"><label for="site_soapclients_<?=$hspcClientCode?>_code"><?echo GetMessage('site_SOAP_CLIENT_CODE')?><font color="red">*</font>:</label></td>
					<td valign="top" width="50%">
						<input type="text" size="24" maxlength="255" value="<?echo htmlspecialchars($arVal['code'])?>" name="site_soapclients[<?=$hspcClientCode?>][code]" id="site_soapclients_<?=$hspcClientCode?>_code">
						<input type="hidden" value="<?echo htmlspecialchars($arVal['code'])?>" name="site_soapclients[<?=$hspcClientCode?>][oldcode]" id="site_soapclients_<?=$hspcClientCode?>_oldcode">
					</td>
				</tr>
				<tr>
					<td valign="top" width="50%"><label for="site_soapclients_<?=$hspcClientCode?>_wsdl"><?echo GetMessage('site_SOAP_CLIENT_WSDL')?><font color="red">*</font>:</label></td>
					<td valign="top" width="50%">
						<input type="text" size="50" maxlength="255" value="<?echo htmlspecialchars($arVal['wsdl'])?>" name="site_soapclients[<?=$hspcClientCode?>][wsdl]" id="site_soapclients_<?=$hspcClientCode?>_wsdl">
					</td>
				</tr>
				<tr>
					<td valign="top" width="50%"><label for="site_soapclients_<?=$hspcClientCode?>_params_encoding"><?echo GetMessage('site_SOAP_CLIENT_PARAMS_ENCODING')?><font color="red">*</font>:</label></td>
					<td valign="top" width="50%">
						<input type="text" size="24" maxlength="255" value="<?echo htmlspecialchars($arVal['params']['encoding'])?>" name="site_soapclients[<?=$hspcClientCode?>][params][encoding]" id="site_soapclients_<?=$hspcClientCode?>_params_encoding">
					</td>
				</tr>
				<tr>
					<td valign="top" width="50%"><label for="site_soapclients_<?=$hspcClientCode?>_params_login"><?echo GetMessage('site_SOAP_CLIENT_PARAMS_LOGIN')?>:</label></td>
					<td valign="top" width="50%">
						<input type="text" size="24" maxlength="255" value="<?echo htmlspecialchars($arVal['params']['login'])?>" name="site_soapclients[<?=$hspcClientCode?>][params][login]" id="site_soapclients_<?=$hspcClientCode?>_params_login">
					</td>
				</tr>
				<tr>
					<td valign="top" width="50%"><label for="site_soapclients_<?=$hspcClientCode?>_params_password"><?echo GetMessage('site_SOAP_CLIENT_PARAMS_PASSWORD')?>:</label></td>
					<td valign="top" width="50%">
						<input type="text" size="24" maxlength="255" value="<?echo htmlspecialchars($arVal['params']['password'])?>" name="site_soapclients[<?=$hspcClientCode?>][params][password]" id="site_soapclients_<?=$hspcClientCode?>_params_password">
					</td>
				</tr>
				<tr>
					<td valign="top" width="50%"><label for="site_soapclients_<?=$hspcClientCode?>_params_trace"><?echo GetMessage('site_SOAP_CLIENT_PARAMS_TRACE')?>:</label></td>
					<td valign="top" width="50%">
						<input type="checkbox"  value="1" <?=(htmlspecialchars($arVal['params']['trace']) == 1 ? 'checked': '') ?> name="site_soapclients[<?=$hspcClientCode?>][params][trace]" id="site_soapclients_<?=$hspcClientCode?>_params_trace">
					</td>
				</tr>
				<tr>
					<?
					$arSoapClientCodeFeaturesValues = array(
						''=>'-',
						1=>'SOAP_SINGLE_ELEMENT_ARRAYS',
						2=>'SOAP_WAIT_ONE_WAY_CALLS',
						4=>'SOAP_USE_XSI_ARRAY_TYPE',
					);
					?>
					<td valign="top" width="50%"><label for="site_soapclients_<?=$hspcClientCode?>_params_features"><?echo GetMessage('site_SOAP_CLIENT_PARAMS_FEATURES')?>:</label></td>
					<td valign="top" width="50%">
						<?=SelectBoxFromArray('site_soapclients['.$hspcClientCode.'][params][features]', array('REFERENCE'=>array_values($arSoapClientCodeFeaturesValues),'REFERENCE_ID'=>array_keys($arSoapClientCodeFeaturesValues)),htmlspecialchars($arVal['params']['features']),'',' id="site_soapclients_'.$hspcClientCode.'_params_features" ');?>
					</td>
				</tr>
				<tr>
					<td valign="top" width="50%"><label for="site_soapclients_<?=$hspcClientCode?>_params_connection_timeout"><?echo GetMessage('site_SOAP_CLIENT_PARAMS_CONNECTION_TIMEOUT')?>:</label></td>
					<td valign="top" width="50%">
						<input type="text" size="24" maxlength="255" value="<?echo htmlspecialchars($arVal['params']['connection_timeout'])?>" name="site_soapclients[<?=$hspcClientCode?>][params][connection_timeout]" id="site_soapclients_<?=$hspcClientCode?>_connection_timeout">
					</td>
				</tr>
				<tr>
					<td valign="top" width="50%"><label for="site_soapclients_<?=$hspcClientCode?>_params_location"><?echo GetMessage('site_SOAP_CLIENT_PARAMS_LOCATION')?>:</label></td>
					<td valign="top" width="50%">
						<input type="text" size="50" maxlength="255" value="<?echo htmlspecialchars($arVal['params']['location'])?>" name="site_soapclients[<?=$hspcClientCode?>][params][location]" id="site_soapclients_<?=$hspcClientCode?>_params_location">
					</td>
				</tr>
				<tr>
					<td valign="top" width="50%"><label for="site_soapclients_<?=$hspcClientCode?>_params_uri"><?echo GetMessage('site_SOAP_CLIENT_PARAMS_URI')?>:</label></td>
					<td valign="top" width="50%">
						<input type="text" size="50" maxlength="255" value="<?echo htmlspecialchars($arVal['params']['uri'])?>" name="site_soapclients[<?=$hspcClientCode?>][params][uri]" id="site_soapclients_<?=$hspcClientCode?>_params_uri">
					</td>
				</tr>
				<tr>
					<td valign="top" width="50%"><label for="site_soapclients_<?=$hspcClientCode?>_params_user_agent"><?echo GetMessage('site_SOAP_CLIENT_PARAMS_USER_AGENT')?>:</label></td>
					<td valign="top" width="50%">
						<input type="text" size="24" maxlength="255" value="<?echo htmlspecialchars($arVal['params']['user_agent'])?>" name="site_soapclients[<?=$hspcClientCode?>][params][user_agent]" id="site_soapclients_<?=$hspcClientCode?>_params_user_agent">
					</td>
				</tr>
				<tr>
					<?
					$arSoapClientCodeCacheWsdlValues = array(
						''=>'-', 
						0=>GetMessage('site_WSDL_CACHE_TYPE_0'), 
						1=>GetMessage('site_WSDL_CACHE_TYPE_1'), 
						2=>GetMessage('site_WSDL_CACHE_TYPE_2'),
						2=>GetMessage('site_WSDL_CACHE_TYPE_3'),
					);
					?>
					<td valign="top" width="50%"><label for="site_soapclients_<?=$hspcClientCode?>_params_cache_wsdl"><?echo GetMessage('site_SOAP_CLIENT_PARAMS_CACHE_WSDL')?>:</label></td>
					<td valign="top" width="50%">
						<?=SelectBoxFromArray('site_soapclients['.$hspcClientCode.'][params][cache_wsdl]', array('REFERENCE'=>array_values($arSoapClientCodeCacheWsdlValues),'REFERENCE_ID'=>array_keys($arSoapClientCodeCacheWsdlValues)),htmlspecialchars($arVal['params']['cache_wsdl']),'',' id="site_soapclients_'.$hspcClientCode.'_params_cache_wsdl" ');?>
					</td>
				</tr>
				<tr>
					<td valign="top" width="50%"><label for="site_soapclients_<?=$hspcClientCode?>_keep_alive"><?echo GetMessage('site_SOAP_CLIENT_KEEP_ALIVE')?>:</label></td>
					<td valign="top" width="50%">
						<input type="checkbox"  value="1" <?=(htmlspecialchars($arVal['params']['keep_alive']) == 1 ? 'checked': '') ?> name="site_soapclients[<?=$hspcClientCode?>][params][keep_alive]" id="site_soapclients_<?=$hspcClientCode?>_params_keep_alive">
					</td>
				</tr>
				<tr>
					<td valign="top" width="50%"><label for="site_soapclients_<?=$hspcClientCode?>_params_local_cert"><?echo GetMessage('site_SOAP_CLIENT_PARAMS_LOCAL_CERT')?>:</label></td>
					<td valign="top" width="50%">
						<input type="text" size="50" maxlength="255" value="<?echo htmlspecialchars($arVal['params']['local_cert'])?>" name="site_soapclients[<?=$hspcClientCode?>][params][local_cert]" id="site_soapclients_<?=$hspcClientCode?>_params_local_cert">
					</td>
				</tr>
				<tr>
					<td valign="top" width="50%"><label for="site_soapclients_<?=$hspcClientCode?>_params_passphrase"><?echo GetMessage('site_SOAP_CLIENT_PARAMS_PASSPHRASE')?>:</label></td>
					<td valign="top" width="50%">
						<input type="text" size="50" maxlength="255" value="<?echo htmlspecialchars($arVal['params']['passphrase'])?>" name="site_soapclients[<?=$hspcClientCode?>][params][passphrase]" id="site_soapclients_<?=$hspcClientCode?>_params_passphrase">
					</td>
				</tr>
				<?
				}
			}
			
			if(isset($tab['require']))
			{
				require_once($_SERVER["DOCUMENT_ROOT"].$tab['require']);
			}
		}
		?>
		
		<?$tabControl->Buttons();?>
		<input <?if($moduleRight < "W") echo "disabled" ?> type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>">
		<?if(strlen($_REQUEST["back_url_settings"]) > 0):?>
			<input <?if ($moduleRight < "W") echo "disabled" ?> type="button" name="Cancel" value="<?=GetMessage("MAIN_OPT_CANCEL")?>" title="<?=GetMessage("MAIN_OPT_CANCEL_TITLE")?>" onclick="window.location='<?echo htmlspecialchars(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'">
			<input type="hidden" name="back_url_settings" value="<?=htmlspecialchars($_REQUEST["back_url_settings"])?>">
		<?endif?>
		<input type="submit" name="RestoreDefaults" title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>')" value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>">
		<?=bitrix_sessid_post();?>
		
		<?$tabControl->End();?>
	</form>
<?endif;?>
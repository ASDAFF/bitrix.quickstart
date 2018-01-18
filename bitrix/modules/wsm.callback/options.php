<?
	$module_id = 'wsm.callback';
	IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/options.php');
	IncludeModuleLangFile(__FILE__);

	$MODULE_RIGHT = $APPLICATION->GetGroupRight($module_id);

	$SITE_ID = SITE_ID;
	
	if ($MODULE_RIGHT >='R'):

		CModule::IncludeModule('iblock');

		//==========================
		// Табы
		//==========================

		$aTabs = array(
			array(
				'DIV' => 'edit1', 
				'TAB' => GetMessage('WSM_OPT_TAB_MAIN'),  
				'ICON' => 'wsm_callback_settings', 
				'TITLE' => GetMessage('WSM_OPT_TAB_MAIN_TITLE')
				),
			);

		$aTabs[] = array(
				'DIV' => 'edit2', 
				'TAB' => GetMessage('WSM_OPT_TAB_NOTICE'), 
				'ICON' => 'wsm_callback_notice', 
				'TITLE' => GetMessage('WSM_OPT_TAB_NOTICE_TITLE')
			);
		
		$aTabs[] = array(
				'DIV' => 'edit3', 
				'TAB' => GetMessage('WSM_OPT_TAB_SMS_SERVICE'), 
				'ICON' => 'wsm_callback_sms_service', 
				'TITLE' => GetMessage('WSM_OPT_TAB_SMS_SERVICE_TITLE')
			);
		/*	
		$aTabs[] = array(
				'DIV' => 'edit4', 
				'TAB' => GetMessage('WSM_OPT_TAB_RIGHTS'), 
				'ICON' => 'wsm_callback_right', 
				'TITLE' => GetMessage('WSM_OPT_TAB_RIGHTS_TITLE')
			);
		*/
			
		$tabControl = new CAdminTabControl('tabControl', $aTabs);
		
		
		//==========================
		// Инфоблоки
		//==========================
		$arrIBlock = array();
		$arIBlockType = array();
		$rsIBlock = CIBlock::GetList(
			Array(), 
			Array(
				'ACTIVE'=>'Y'
			), true
		);
		while($arIBlock = $rsIBlock->GetNext())
		{
			$arrIBlock[$arIBlock['ID']] = '['.$arIBlock['ID'].'] '.$arIBlock['NAME'];
			$arIBlockType[$arIBlock['ID']] = $arIBlock['IBLOCK_TYPE_ID'];
			
		}

		
		//==========================
		// суб Табы
		//==========================
		
		$dbSites = CSite::GetList(($b = ""), ($o = ""), Array("ACTIVE" => "Y"));
		$arSites = array();
		$aSubTabs = array();
		$aSubTabs2 = array();
		$aSubTabs3 = array();
		
		while ($site = $dbSites->Fetch())
		{
			$site["ID"] = htmlspecialcharsbx($site["ID"]);
			$site["NAME"] = htmlspecialcharsbx($site["NAME"]);
			$arSites[] = $site;

			$aSubTabs[] = array("DIV" => "opt_main_".$site["ID"], "TAB" => $site["NAME"].' ['.$site["ID"].']', 'TITLE' => '');
			$aSubTabs2[] = array("DIV" => "opt_notice_".$site["ID"], "TAB" => $site["NAME"].' ['.$site["ID"].']', 'TITLE' => "");
			$aSubTabs3[] = array("DIV" => "opt_sms_".$site["ID"], "TAB" => $site["NAME"].' ['.$site["ID"].']', 'TITLE' => "");
			
			//==========================
			// Свойства инфоблока
			//==========================
			
			//foreach($arSites
			$set_iblock = COption::GetOptionInt($module_id, 'iblock', 0, $site["ID"]);
			
			$arrIBlockProperty[$site["ID"]] = array();

			$arrIBlockPropertyL[$site["ID"]] = array('' => '...');
			$arrIBlockPropertyS[$site["ID"]] = array('' => '...');
			$arrIBlockPropertyLNS[$site["ID"]] = array();
			$arrIBlockPropertyFull[$site["ID"]] = array();
			$arrIBlockPropertyTheme[$site["ID"]] = array();
			
			if($set_iblock >= 0)
			{
				$arFilter = Array(
					'IBLOCK_ID' => $set_iblock, 
					"ACTIVE"	=> "Y",
					);

				$properties = CIBlockProperty::GetList(Array("SORT"=>"ASC", "ID"=>"ASC"), $arFilter );
				while ($arProp = $properties->GetNext())
				{
					$arrIBlockPropertyFull[$site["ID"]][$arProp["ID"]] = $arProp;
					
					if($arProp['IS_REQUIRED'] == 'Y') 
						$arProp["NAME"] .= ' *'; 
						
					if(in_array($arProp["PROPERTY_TYPE"], array('L','N','S')))
						$arrIBlockPropertyLNS[$site["ID"]][$arProp["ID"]] = "[".$arProp["ID"]."] ".$arProp["NAME"]."";
					if($arProp["PROPERTY_TYPE"] == 'S')
						$arrIBlockPropertyS[$site["ID"]][$arProp["ID"]] = "[".$arProp["ID"]."] ".$arProp["NAME"]."";	
					if($arProp["PROPERTY_TYPE"] == 'L')
						$arrIBlockPropertyL[$site["ID"]][$arProp["ID"]] = "[".$arProp["ID"]."] ".$arProp["NAME"]."";		
				}
				
				$property_theme = COption::GetOptionInt($module_id, 'iblock_property_theme', 0, $site["ID"]);

				if($property_theme > 0)
				{
					$property_enums = CIBlockPropertyEnum::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_ID"=>$set_iblock, "PROPERTY_ID"=>$property_theme));
					while($enum_fields = $property_enums->GetNext())
					{
						$arrIBlockPropertyTheme[$site["ID"]][$enum_fields["ID"]] = $enum_fields["VALUE"];
					}
				}
			}
		}
		
		$subTabControl = new CAdminViewTabControl("subTabControl", $aSubTabs);
		$subTabControl2 = new CAdminViewTabControl("subTabControl2", $aSubTabs2);
		$subTabControl3 = new CAdminViewTabControl("subTabControl3", $aSubTabs3);
		
		$arrSmsService = array();
		
		if(CModule::IncludeModule($module_id))
			$arrSmsService = WSMCallbackSMS::GetModules();

		if($REQUEST_METHOD=='POST' && strlen($Update.$Apply.$RestoreDefaults)>0 && $MODULE_RIGHT >= 'W' && check_bitrix_sessid())
		{
			if(strlen($RestoreDefaults)>0)
			{
				COption::RemoveOption($module_id);
				$APPLICATION->DelGroupRight($module_id);
			}
			else
			{	
				//save for sites
				foreach($arSites as $site)
				{
					$form_captcha[$site["LID"]] = $form_captcha[$site["LID"]]=='Y' ? 'Y' : 'N';
					
					COption::SetOptionInt($module_id, 'iblock', $iblock[$site["LID"]], 'information block storage applications', $site["LID"]);

					//проверяем обяхательные поля
					foreach($arrIBlockPropertyFull[$site["LID"]] as $id => $field)
					{
						if($field['IS_REQUIRED'] == 'Y' && !in_array($id, $form_property[$site["LID"]])) 
							$form_property[$site["LID"]][] = $id;
							
						if($field['IS_REQUIRED'] == 'Y' && !in_array($id, $notice_iblock_property[$site["LID"]])) 
							$notice_iblock_property[$site["LID"]][] = $id;
							
					}
					
					//удаляем из сообщения не заполняемые поля
					foreach($notice_iblock_property[$site["LID"]] as $index => $prop)
					{
						if(!in_array($prop, $form_property[$site["LID"]]))
							unset($notice_iblock_property[$site["LID"]][$index]);
					}
					
					COption::SetOptionString($module_id, 'form_property', implode(',',$form_property[$site["LID"]]), 'properties to display in the form of', $site["LID"]);
					COption::SetOptionString($module_id, 'form_captcha', $form_captcha[$site["LID"]], 'use captcha', $site["LID"]);
					
					COption::SetOptionInt($module_id, 'iblock_property_time', $iblock_property_time[$site["LID"]], 'Property Information Block - Time', $site["LID"]);
					COption::SetOptionInt($module_id, 'iblock_property_theme', $iblock_property_theme[$site["LID"]], 'Property Information Block - Topic call', $site["LID"]);
					
					$form_message_add[$site["LID"]] = htmlspecialcharsEx($form_message_add[$site["LID"]]);
					
					COption::SetOptionString($module_id, 'form_message_add', $form_message_add[$site["LID"]], 'message when successfully added', $site["LID"]);
					
					
					foreach($arOptionNotice as $arOption)
					{
						$name=$arOption[0];
						$val=$_REQUEST[$name];
						if($arOption[2][0]=='checkbox' && $val!='Y')
							$val='N';
						COption::SetOptionString($module_id, $name, $val, $arOption[1], $site["LID"]);
					}
					
					COption::SetOptionString($module_id, 'notice_email', trim($notice_email[$site["LID"]]), '', $site["LID"]);
					COption::SetOptionString($module_id, 'notice_phone', trim($notice_phone[$site["LID"]]), '', $site["LID"]);
					
					$notice_send_to_main_always[$site["LID"]] = $notice_send_to_main_always[$site["LID"]] == 'Y' ? 'Y' : 'N';

					COption::SetOptionString($module_id, 'notice_send_to_main_always', $notice_send_to_main_always[$site["LID"]], '', $site["LID"]);
					
					COption::SetOptionString($module_id, 'notice_iblock_property', implode(',',$notice_iblock_property[$site["LID"]]), '', $site["LID"]);
					
					
					foreach ($arrIBlockPropertyTheme[$site["LID"]] as $prop_id => $prop_name)
					{
						$email = 'notice_email_'.$prop_id;
						$phone = 'notice_phone_'.$prop_id;
						
						$email = $$email;
						$phone = $$phone;
						
						if(check_email($email[$site["LID"]]))
							COption::SetOptionString($module_id, 'notice_email_'.$prop_id, $email[$site["LID"]], '', $site["LID"]);
							
						COption::SetOptionString($module_id, 'notice_phone_'.$prop_id, $phone[$site["LID"]], '', $site["LID"]);
					}
					
					
					
					
					//общие настройки для сайтов
					$sms_translit[$site["LID"]] = $sms_translit[$site["LID"]] == 'Y' ? 'Y' : 'N';
					
					if(intval($sms_time_from[$site["LID"]]) > intval($sms_time_to[$site["LID"]])) 
						$sms_time_to[$site["LID"]] = intval($sms_time_from[$site["LID"]]) + 1;

					$sms_time = intval($sms_time_from[$site["LID"]]).','.intval($sms_time_to[$site["LID"]]);
					
					COption::SetOptionString($module_id, 'sms_service', $sms_service[$site["LID"]], '', $site["LID"]);
					COption::SetOptionString($module_id, 'sms_service_sender', $sms_service_sender[$site["LID"]], '', $site["LID"]);
					
					COption::SetOptionString($module_id, 'sms_time', $sms_time, '', $site["LID"]);
					COption::SetOptionString($module_id, 'sms_translit', $sms_translit[$site["LID"]], '', $site["LID"]);
					
					
				}
			}
		}
		
		$ONE_SITE = count($arSites) == 1 ? true : false ;

		//если есть уведомление на телефон и не используется капча
		$sms_service = COption::GetOptionString($module_id, 'sms_service', '', $SITE_ID);
		$use_captcha = true;
		foreach ($arSites as $site)
		{
			$form_captcha = COption::GetOptionString($module_id, 'form_captcha', 'N', $site["LID"]);
			$use_captcha = ( $form_captcha != 'Y' && $use_captcha) ? false : true ;
		}
		?>
		
		<?if($sms_service!='' && $use_captcha === false):?>
		<div id="empty_error" style="">
			<div class="adm-info-message-wrap adm-info-message-red">
				<div class="adm-info-message">
					<div class="adm-info-message-title"><?=GetMessage("WSM_CALLBACK_WARNING");?></div>
					<?=GetMessage("WSM_CALLBACK_WARNING_USE_CAPCHA");?>
					<div class="adm-info-message-icon"></div>
				</div>
			</div>
		</div>
		<?endif;?>
		
		<?$tabControl->Begin();?>
		
		<form method='post' action='<?=$APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?=LANGUAGE_ID?>'>
		<?$tabControl->BeginNextTab();?>
			<?if(!$ONE_SITE):?>
			<tr>
				<td colspan="2">
				<?endif;?>
					<?
					if(!$ONE_SITE) 
						$subTabControl->Begin();
					
					foreach ($arSites as $site)
					{
						if(!$ONE_SITE) 
							$subTabControl->BeginNextTab();
						?>
						<?if(!$ONE_SITE):?><table width="75%" align="center"><?endif;?>
							<tr>
								<td><?=GetMessage("WSM_CALLBACK_IBLOCK") ?></td>
								<td>
									<?
									$iblock = COption::GetOptionInt($module_id, 'iblock', 0, $site["LID"]);
									?>
									<select name="iblock[<?=$site["LID"]?>]">
										<?foreach ($arrIBlock as $key => $prop):?>
											<option value="<?=$key?>"<?if ($key == $iblock) echo " selected";?>><?=$prop;?></option>
										<?endforeach;?>
									</select>
								</td>
							</tr>
							
							<tr class='heading'>
								<td align='center' colspan='2' nowrap><?=GetMessage('WSM_OPT_TAB_MAIN_PROP')?></td>
							</tr>
							<tr>
								<td><?=GetMessage("WSM_CALLBACK_IBLOCK_PROPERTY_TIME") ?></td>
								<td>
									<?
									$iblock_property_time = COption::GetOptionString($module_id, 'iblock_property_time', 0, $site["LID"]);
									?>
									<select name="iblock_property_time[<?=$site["LID"]?>]">
										<?foreach ($arrIBlockPropertyS[$site["ID"]] as $key => $prop):?>
											<option value="<?=$key?>"<?if ($key == $iblock_property_time) echo " selected";?>><?=$prop;?></option>
										<?endforeach;?>
									</select>
								</td>
							</tr>

							<tr>
								<td><?=GetMessage("WSM_CALLBACK_IBLOCK_PROPERTY_THEME") ?></td>
								<td>
									<?
									$iblock_property_theme = COption::GetOptionString($module_id, 'iblock_property_theme', 0 , $site["LID"]);
									?>
									<select name="iblock_property_theme[<?=$site["LID"]?>]">
										<?foreach ($arrIBlockPropertyL[$site["ID"]] as $key => $prop):?>
											<option value="<?=$key?>"<?if ($key == $iblock_property_theme) echo " selected";?>><?=$prop;?></option>
										<?endforeach;?>
									</select>
								</td>
							</tr>
							
							<tr class='heading'>
								<td align='center' colspan='2' nowrap><?=GetMessage('WSM_OPT_TAB_MAIN_FORM')?></td>
							</tr>
							<tr>
								<td>
									<?=GetMessage("WSM_CALLBACK_IBLOCK_FORM_PROPERTY") ?><br/>
									<small>* - <?=GetMessage("WSM_CALLBACK_REQUERED") ?></small>
								
								</td>
								<td>
									<?
									$form_property = COption::GetOptionString($module_id, 'form_property', '', $site["LID"]);
									$form_property = explode(",", $form_property);
									?>
									<select name="form_property[<?=$site["LID"]?>][]" multiple size="5">
										<?foreach ($arrIBlockPropertyLNS[$site["LID"]] as $key => $prop):?>
											<option value="<?=$key?>"<?if (in_array($key, $form_property)) echo " selected";?>><?=$prop;?></option>
										<?endforeach;?>
									</select>
								</td>
							</tr>
							<tr>
								<td>
									<?=GetMessage("WSM_CALLBACK_IBLOCK_FORM_CAPTCHA");?><br/>
									<small>(<?=GetMessage("WSM_CALLBACK_RECOMEND_USE");?>)</small>
								</td>
								<td>
									<?
									$form_captcha = COption::GetOptionString($module_id, 'form_captcha', 'N', $site["LID"]);
									?>
									<input type="checkbox" name="form_captcha[<?=$site["LID"]?>]" value="Y" <?if($form_captcha == 'Y') echo " checked";?>/>
									
								</td>
							</tr>
							
							<tr>
								<td>
									<?=GetMessage("WSM_CALLBACK_IBLOCK_FORM_MESS_OK");?><br/>
									<small>(<?=GetMessage("WSM_CALLBACK_IBLOCK_FORM_MESS_OK_TAG");?>)</small>
								</td>
								<td>
									<?
									$form_message_add = htmlspecialcharsBack(COption::GetOptionString($module_id, 'form_message_add', '', $site["LID"]));
									?>
									<textarea cols="55" rows="4" name="form_message_add[<?=$site["LID"]?>]"><?=$form_message_add?></textarea>
									
								</td>
							</tr>
							
						<?if(!$ONE_SITE):?></table><?endif;?>
						<?
					}
					if(!$ONE_SITE) 
						$subTabControl->End();
					?>
				<?if(!$ONE_SITE):?>
				</td>
			</tr>
			<?endif;?>
		<?$tabControl->BeginNextTab();?>
			<?if(!$ONE_SITE):?>
			<tr>
				<td colspan="2">
				<?endif;?>
					<?
					if(!$ONE_SITE) $subTabControl2->Begin();
					foreach ($arSites as $site)
					{
						if(!$ONE_SITE)  $subTabControl2->BeginNextTab();
						?>
						<?if(!$ONE_SITE):?><table width="75%" align="center"><?endif;?>
							
							<tr>
								<td><?=GetMessage("WSM_CALLBACK_EMAIL_MAIN") ?></td>
								<td>
									<?
									$notice_email = COption::GetOptionString($module_id, 'notice_email', '', $site["LID"]);
									?>
									<input type="text" name="notice_email[<?=$site["LID"]?>]" value="<?=$notice_email?>" placeholder="<?=GetMessage('WSM_CALLBACK_EMAIL')?>"/>
								</td>
							</tr>
							
							<tr>
								<td><?=GetMessage("WSM_CALLBACK_PHONE_MAIN") ?></td>
								<td>
									<?
									$notice_phone = COption::GetOptionString($module_id, 'notice_phone', '', $site["LID"]);
									?>
									<input type="text" name="notice_phone[<?=$site["LID"]?>]" value="<?=$notice_phone?>" placeholder="<?=GetMessage('WSM_CALLBACK_PHONE')?>"/>
								</td>
							</tr>
							
							
							
							
							
							<tr>
								<td><?echo GetMessage("WSM_CALLBACK_SEND_TOMAIN_ALWAYS") ?></td>
								<td>
									<?
									$notice_send_to_main_always = COption::GetOptionString($module_id, 'notice_send_to_main_always', 'N', $site["LID"]);
									?>
									<input type="checkbox" name="notice_send_to_main_always[<?=$site["LID"]?>]" value="Y" <?if($notice_send_to_main_always == 'Y') echo " checked";?>/>
								</td>
							</tr>
							<tr>
								<td><?=GetMessage("WSM_CALLBACK_MAIL_EVENT") ?></td>
								<td>
								<a href="/bitrix/admin/type_edit.php?EVENT_NAME=WSM_CALLBACK_NOTICE" target="_blank"><?=GetMessage("WSM_CALLBACK_MAIL_EVENT_GO_SETTING")?></a> <small>(WSM_CALLBACK_NOTICE)</small>
								</td>
							</tr>
							
							<tr class='heading'>
								<td align='center' colspan='2' nowrap><?=GetMessage('WSM_CALLBACK_NOTICE_CONTACT')?></td>
							</tr>
							
							<?
							$iblock_property_theme = COption::GetOptionString($module_id, 'iblock_property_theme', 0 , $site["LID"]);
							?>
							
							<?if(!$iblock_property_theme):?>
								<tr>
									<td align='center' colspan='2' nowrap> <?=GetMessage('WSM_CALLBACK_IBLOCK_PROPERTY_THEME_LINK')?>: "<?=GetMessage('WSM_CALLBACK_IBLOCK_PROPERTY_THEME')?>"</td>
								</tr>
							<?else:?>	
							
								<?if(count($arrIBlockPropertyTheme[$site["LID"]]) > 0):?>
									<?foreach ($arrIBlockPropertyTheme[$site["LID"]] as $prop_id => $prop_name):?>
										<?
										$email = COption::GetOptionString($module_id, 'notice_email_'.$prop_id, '', $site["LID"]);
										$phone = COption::GetOptionString($module_id, 'notice_phone_'.$prop_id, '', $site["LID"]);
										?>
										<tr>
											<td><?=$prop_name?></td>
											<td>
												<input type="text" name="notice_email_<?=$prop_id?>[<?=$site["LID"]?>]" placeholder="<?=GetMessage('WSM_CALLBACK_EMAIL')?>" value="<?=$email?>"/>
												<input type="text" name="notice_phone_<?=$prop_id?>[<?=$site["LID"]?>]" placeholder="<?=GetMessage('WSM_CALLBACK_PHONE')?>" value="<?=$phone?>"/>
											</td>
										</tr>
									<?endforeach;?>
									<tr>
										<td align='center' colspan='2'><?=GetMessage('WSM_CALLBACK_NOTICE_CONTACT_INFO')?></td>
									</tr>
								<?else:?>
									<?
									$iblock = COption::GetOptionInt($module_id, 'iblock', '', $site["LID"]);
									$iblock_type = $arIBlockType[$iblock];
									?>
									<tr>
										<td><?=GetMessage('WSM_CALLBACK_IBLOCK_PROPERTY_THEME_NOLIST')?> "<?=GetMessage('WSM_CALLBACK_IBLOCK_PROPERTY_THEME')?>"</td>
										<td>
										<a target="_blank" href="/bitrix/admin/iblock_edit.php?type=<?=$iblock_type?>&lang=ru&ID=<?=$iblock;?>&admin=Y">Настроить инфоблок</a>
										</td>
									</tr>
								<?endif;?>
							<?endif;?>
							<tr class='heading'>
								<td align='center' colspan='2' nowrap><?=GetMessage('WSM_CALLBACK_SMS_FIELDS')?></td>
							</tr>
							<tr>
								<td><?=GetMessage("WSM_CALLBACK_IBLOCK_PROPERTY2") ?></td>
								<td>
									<?
									$notice_iblock_property = COption::GetOptionString($module_id, 'notice_iblock_property', '', $site["LID"]);
									$notice_iblock_property = explode(",", $notice_iblock_property);
									?>
									<select name="notice_iblock_property[<?=$site["LID"]?>][]" multiple size="5">
										<?foreach ($arrIBlockPropertyLNS[$site["LID"]] as $key => $prop):?>
											<option value="<?=$key?>"<?if (in_array($key, $notice_iblock_property)) echo " selected";?>><?=$prop;?></option>
										<?endforeach;?>
									</select>
								</td>
							</tr>
						<?if(!$ONE_SITE):?></table><?endif;?>
						<?
					}
					if(!$ONE_SITE) $subTabControl2->End();
					?>
				<?if(!$ONE_SITE):?>	
				</td>
			</tr>
			<?endif;?>
		<?$tabControl->BeginNextTab();?>
			<tr>
				<td colspan='2'>
				<div class="adm-info-message-wrap" align="left">
					<div class="adm-info-message">
						<?=GetMessage("WSM_CALLBACK_EXAMPLE_INIT_INFO") ?><br/>
					</div>
				</div>
				</td>
			</tr>
		<?if(!$ONE_SITE):?>
			<tr>
				<td colspan="2">
				<?endif;?>
					<?
					if(!$ONE_SITE) $subTabControl3->Begin();
					foreach ($arSites as $site)
					{
						if(!$ONE_SITE)  $subTabControl3->BeginNextTab();
						?>
						<?if(!$ONE_SITE):?><table width="75%" align="center"><?endif;?>
		
		
						<tr>
							<td><?echo GetMessage("WSM_CALLBACK_SMS_SERVICE") ?></td>
							<td>
								<?$sms_service = COption::GetOptionString($module_id, 'sms_service', '', $site["LID"]);?>
								<table>
									<?foreach($arrSmsService as $sid => $SmsService):?>
									<tr>
										<td>
										<input id="sms_service_<?=$sid?>_<?=$site["LID"]?>" type="radio" name="sms_service[<?=$site["LID"]?>]" value="<?=$sid?>" <?if($sms_service == $sid):?>checked<?endif;?> <?if(!$SmsService['INSTALLED']):?>disabled<?endif;?>/>
										</td>
										<td>
											<label for="sms_service_<?=$sid?>_<?=$site["LID"]?>">
												<?=$SmsService['NAME']?>
											</label>
											
											<?if(!$SmsService['INSTALLED']):?>
												<a  href="http://marketplace.1c-bitrix.ru/solutions/<?=$sid?>/" target="_blank"><?=GetMessage("WSM_CALLBACK_INSTALL_MODUL");?></a>
											<?endif;?>
										</td>
									</tr>
									<?endforeach;?>
									<tr>
										<td><input id="sms_service_other_<?=$site["LID"]?>" type="radio" name="sms_service[<?=$site["LID"]?>]" value="other" <?if($sms_service == 'other'):?>checked<?endif;?>/></td>
										<td><label for="sms_service_other_<?=$site["LID"]?>"><?=GetMessage("WSM_CALLBACK_SMS_SERVICE_OTHER");?></label></td>
									</tr>
									<tr>
										<td><input id="sms_service_none_<?=$site["LID"]?>" type="radio" name="sms_service[<?=$site["LID"]?>]" value="" <?if($sms_service == ''):?>checked<?endif;?>/></td>
										<td><label for="sms_service_none_<?=$site["LID"]?>"><?=GetMessage("WSM_CALLBACK_SMS_SERVICE_NOT_USE");?></label></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td><?echo GetMessage("WSM_CALLBACK_SMS_SERVICE_INFO") ?></td>
							<td>
							<?
								switch ($sms_service)
								{
									case 'rarus.sms4bcontacts':
										
										if(!CModule::IncludeModule($sms_service)) 
											continue;
											
										global $SMS4B;
										
										$balance = intval($SMS4B->arBalance['Rest']);
										$sender = $SMS4B->arBalance['Addresses'];
										?>
										<table>
											<tr>
												<td><?echo GetMessage("WSM_CALLBACK_SMS_SERVICE_SMS_BALANSE") ?></td>
												<td><?=$balance?></td>
											</tr>
											<tr>								
												<td><?echo GetMessage("WSM_CALLBACK_SMS_SERVICE_SENDER") ?></td>
												<td>
												<?if(is_array($sender)):?> 
													<?foreach($sender as $s):?>
													<?=$s?><br/>
													<?endforeach;?>
												<?endif;?>
												</td>
											</tr>
										</table>
										<?
									break;
									
									default:
										echo '-';
									break;
									
								}
							?>				
							</td>		
						</tr>
						<tr class='heading'>
							<td align='center' colspan='2'><?=GetMessage('WSM_CALLBACK_SMS_SERVICE_SETTING')?></td>
						</tr>

						<?
							switch ($sms_service)
							{
								case 'rarus.sms4bcontacts':
									
									if(!CModule::IncludeModule($sms_service)) 
										continue;
										
									global $SMS4B;
									$sender = $SMS4B->arBalance['Addresses'];
									$sms_service_sender = COption::GetOptionString($module_id, 'sms_service_sender', '', $site["LID"]);
									?>
									<tr>
										<td><?echo GetMessage("WSM_CALLBACK_SMS_SERVICE_SENDER") ?></td>
										<td>
										<?if(is_array($sender)):?> 
											<select name="sms_service_sender[<?=$site["LID"]?>]">
											<?foreach($sender as $s):?>
											<option value="<?=$s?>" <?if($sms_service_sender == $s):?>selected<?endif;?>><?=$s?></option>
											<?endforeach;?>
											</select>
										<?endif;?>
										</td>
									</tr>
									
									<?
								break;
								
								default:
									echo '-';
								break;
								
							}
						?>				

						<tr>
							<td><?echo GetMessage("WSM_CALLBACK_SMS_TIME") ?></td>
							<td>
								<?
								$sms_time = COption::GetOptionString($module_id, 'sms_time', '', $site["LID"]);
								$sms_time = explode(",", $sms_time);
								?>
								<input type="text" size="2" maxlength="2" name="sms_time_from[<?=$site["LID"]?>]" placeholder="<?=GetMessage('WSM_CALLBACK_SMS_TIME_FROM')?>" value="<?=$sms_time[0]?>"/> -
								<input type="text" size="2" maxlength="2" name="sms_time_to[<?=$site["LID"]?>]" placeholder="<?=GetMessage('WSM_CALLBACK_SMS_TIME_TO')?>" value="<?=$sms_time[1]?>"/>
								<?=GetMessage('WSM_CALLBACK_SMS_TIME_HINT')?>
							</td>
						</tr>
					<tr>
						<td><?echo GetMessage("WSM_CALLBACK_SMS_TRANSLIT") ?></td>
						<td>
							<?$sms_translit = COption::GetOptionString($module_id, 'sms_translit', 'N', $site["LID"]);?>
							<input disabled type="checkbox" name="sms_translit[<?=$site["LID"]?>]" value="Y" <?if($sms_translit == 'Y') echo " checked";?>/>
						</td>
					</tr>
		
						<?if(!$ONE_SITE):?></table><?endif;?>
						<?
					}
					if(!$ONE_SITE) $subTabControl3->End();
					?>
				<?if(!$ONE_SITE):?>	
				</td>
			</tr>
			<?endif;?>
		
			
			
		<tr class='heading'>
			<td align='center' colspan='2'><?=GetMessage('WSM_OPT_SMS_SERVICE_EXAMPLE')?></td>
		</tr>
		<tr>
			<td colspan='2'>
			<?=GetMessage("WSM_CALLBACK_EXAMPLE_INIT0") ?>
			<br/><br/>
			<?=GetMessage("WSM_CALLBACK_EXAMPLE_INIT2") ?>
			<br/><br/><br/>
			<b><?=GetMessage("WSM_CALLBACK_EXAMPLE_INIT") ?></b><br/>
			<br/>
			<div class="code">
				AddEventHandler("wsm.callback", "OnSmsSend", Array("MyClass", "OnSmsSendHandler"));
				<br/><br/>
				class MyClass<br/>
				{<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;function OnSmsSendHandler(&$Data)<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;{<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;foreach($Data['PHONE'] as $phone)<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{<br/>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SMS::Send($phone, $Data['MESSAGE']);<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;}<br/>
				}<br/>
				<br/>
				
			</div>	
			</td>
		</tr>
		<?//$tabControl->BeginNextTab();?>
		<?//require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>

		<?
		if($REQUEST_METHOD=='POST' && strlen($Update.$Apply.$RestoreDefaults)>0 && check_bitrix_sessid()) 
		{
			if(strlen($Update)>0 && strlen($_REQUEST['back_url_settings'])>0)
				LocalRedirect($_REQUEST['back_url_settings']);
			else
				LocalRedirect($APPLICATION->GetCurPage().'?mid='.urlencode($mid).'&lang='.urlencode(LANGUAGE_ID).'&back_url_settings='.urlencode($_REQUEST['back_url_settings']).'&'.$tabControl->ActiveTabParam());	
		}
		?>

		<?$tabControl->Buttons();?>
			<input <?if ($MODULE_RIGHT<'W') echo 'disabled' ?> type='submit' name='Update' value='<?=GetMessage('MAIN_SAVE')?>' title='<?=GetMessage('MAIN_OPT_SAVE_TITLE')?>'>
			<input <?if ($MODULE_RIGHT<'W') echo 'disabled' ?> type='submit' name='Apply' value='<?=GetMessage('MAIN_OPT_APPLY')?>' title='<?=GetMessage('MAIN_OPT_APPLY_TITLE')?>'>
			<?if(strlen($_REQUEST['back_url_settings'])>0):?>
				<input type='button' name='Cancel' value='<?=GetMessage('MAIN_OPT_CANCEL')?>' title='<?=GetMessage('MAIN_OPT_CANCEL_TITLE')?>' onclick='window.location='<?=htmlspecialchars(CUtil::addslashes($_REQUEST['back_url_settings']))?>''>
				<input type='hidden' name='back_url_settings' value='<?=htmlspecialchars($_REQUEST['back_url_settings'])?>'>
			<?endif?>
			<input <?if ($MODULE_RIGHT<'W') echo 'disabled' ?> type='submit' name='RestoreDefaults' title='<?=GetMessage('MAIN_HINT_RESTORE_DEFAULTS')?>' OnClick='confirm('<?=AddSlashes(GetMessage('MAIN_HINT_RESTORE_DEFAULTS_WARNING'))?>')' value='<?=GetMessage('MAIN_RESTORE_DEFAULTS')?>'>
			<?=bitrix_sessid_post();?>
		<?$tabControl->End();?>
		</form>
	<?else:?>
		<?=CAdminMessage::ShowMessage(GetMessage('NO_RIGHTS_FOR_VIEWING'));?>
	<?endif;?>

<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if ($arResult["NEED_AUTH"] == "Y")
	$APPLICATION->AuthForm("");
elseif (strlen($arResult["FatalError"])>0)
{
	?><span class='errortext'><?=$arResult["FatalError"]?></span><br /><br /><?
}
else
{
	if(strlen($arResult["ErrorMessage"]) > 0 && (!is_array($arResult["ErrorFields"]) || count($arResult["ErrorFields"]) <= 0)):
		?><span class='errortext'><?=$arResult["ErrorMessage"]?></span><br /><br /><?
	endif;

	if ($arResult["ShowForm"] == "Input")
	{
		?><script type="text/javascript">
		top.BXExtranetMailList = [];

		BX.message({
			SONET_GROUP_TITLE_EDIT : '<?=CUtil::JSEscape(GetMessage("SONET_GCE_T_TITLE_EDIT"))?>'
			<?
			if (array_key_exists("POST", $arResult) && array_key_exists("NAME", $arResult["POST"]) && strlen($arResult["POST"]["NAME"]) > 0)
			{
				?>
				, SONET_GROUP_TITLE : '<?=CUtil::JSEscape($arResult["POST"]["NAME"])?>'
				<?
			}
			?>
		});

		<?
		if ($arResult["IS_IFRAME"] && $arResult["CALLBACK"] == "REFRESH")
		{
			$APPLICATION->RestartBuffer();
			?>
<script type="text/javascript">
top.BX.onCustomEvent('onSonetIframeCallbackRefresh');
</script>
			<?
			die();
		}
		elseif ($arResult["IS_IFRAME"] && $arResult["CALLBACK"] == "GROUP")
		{
			$APPLICATION->RestartBuffer();
			?>
<script type="text/javascript">
top.BX.onCustomEvent('onSonetIframeCallbackGroup', [<?=intval($_GET["GROUP_ID"])?>]);
</script>
			<?
			die();
		}
		elseif ($arResult["IS_IFRAME"] && $arResult["CALLBACK"] == "EDIT")
		{
// this situation is impossible now but this code may be needed in the future
			?>
			(function() {
				var iframePopup = window.top.BX.SonetIFramePopup;
				if (iframePopup)
				{
					BX.adjust(iframePopup.title, {text: BX.message("SONET_GROUP_TITLE_EDIT").replace('#GROUP_NAME#', BX.message("SONET_GROUP_TITLE"))});
				}
			})();
			<?
		}
		?>

		BX.ready(
			function()
			{
				BXGCESwitchTabs();
				BXGCESwitchFeatures();
				BX.bind(BX("sonet_group_create_popup_form_button_submit"), "click", BXGCESubmitForm);
				BX.bind(BX("sonet_group_create_popup_form_email_input"), "keydown", BXGCEEmailKeyDown);

				if (BX("USERS_employee_section_extranet"))
				{
					BX("USERS_employee_section_extranet").style.display = "<?=($arResult["POST"]["IS_EXTRANET_GROUP"] == "Y" ? "inline-block" : "none")?>";
				}
			}
		);
		</script>
		<?
		if (is_array($arResult["ErrorFields"]) && count($arResult["ErrorFields"]) > 0)
		{
			$bHasUserFieldError = false;
			foreach ($arResult["GROUP_PROPERTIES"] as $FIELD_NAME => $arUserField)
			{
				if (in_array($FIELD_NAME, $arResult["ErrorFields"]))
				{
					$bHasUserFieldError = true;
					break;
				}
			}

			if (
				(
					in_array("GROUP_INITIATE_PERMS", $arResult["ErrorFields"])
					|| in_array("GROUP_SPAM_PERMS", $arResult["ErrorFields"])
					|| $bHasUserFieldError
				)
				&& !in_array("GROUP_SUBJECT_ID", $arResult["ErrorFields"])
				&& !in_array("GROUP_NAME", $arResult["ErrorFields"])
			)
				$active_tab = "additional";
		}
		?>
		<form method="post" name="sonet_group_create_popup_form" id="sonet_group_create_popup_form" action="<?=POST_FORM_ACTION_URI?>" enctype="multipart/form-data">
			<div id="sonet_group_create_popup" class="sonet-group-create-popup"><?
				?><div class="sonet-group-create-popup-tabs-block">
					<span class="sonet-group-create-popup-tabs-wrap"><?
						if (!array_key_exists("TAB", $arResult) || $arResult["TAB"] == "edit")
						{
							?><span class="sonet-group-create-popup-tab<?=($active_tab == "additional" ? "" : " sonet-group-create-popup-tab-active")?>">
								<span class="sonet-group-create-popup-tab-left"></span><span class="sonet-group-create-popup-tab-text"><?=GetMessage("SONET_GCE_TAB_1")?></span><span class="sonet-group-create-popup-tab-right"></span>
							</span><?
						}

						if (!array_key_exists("TAB", $arResult) || $arResult["TAB"] == "edit")
						{
							?><span class="sonet-group-create-popup-tab">
								<span class="sonet-group-create-popup-tab-left"></span><span class="sonet-group-create-popup-tab-text"><?=GetMessage("SONET_GCE_TAB_2")?></span><span class="sonet-group-create-popup-tab-right"></span>
							</span><?
						}

						if (!array_key_exists("TAB", $arResult) || $arResult["TAB"] == "invite")
						{
							?><span class="sonet-group-create-popup-tab">
								<span class="sonet-group-create-popup-tab-left"></span><span class="sonet-group-create-popup-tab-text"><?=GetMessage("SONET_GCE_TAB_3")?></span><span class="sonet-group-create-popup-tab-right"></span>
							</span><?
						}

						if (!array_key_exists("TAB", $arResult) || $arResult["TAB"] == "edit")
						{
							?><span class="sonet-group-create-popup-tab<?=($active_tab == "additional" ? " sonet-group-create-popup-tab-active" : "")?>">
								<span class="sonet-group-create-popup-tab-left"></span><span class="sonet-group-create-popup-tab-text"><?=GetMessage("SONET_GCE_TAB_4")?></span><span class="sonet-group-create-popup-tab-right"></span>
							</span><?
						}

					?></span>
					<div class="sonet-group-create-tabs-block-line"></div>
				</div>

				<div id="sonet_group_create_tabs_content" class="sonet-group-create-tabs-content"><?

					if (!array_key_exists("TAB", $arResult) || $arResult["TAB"] == "edit")
					{
						?><div id="sonet_group_create_tabs_description" style="<?=($active_tab == "additional" ? "display: none;" : "")?>"><?

							$strSubmitButtonTitle = ($arParams["GROUP_ID"] > 0 ? GetMessage("SONET_GCE_T_DO_EDIT") : GetMessage("SONET_GCE_T_DO_CREATE"));
							?>
							<div>
								<div class="sonet-group-create-popup-main-fields">
									<div class="sonet-group-create-popup-field-corners-top">
										<div class="sonet-group-create-popup-form-left-corner"></div>
										<div class="sonet-group-create-popup-form-right-corner"></div>
									</div>
									<div class="sonet-group-create-popup-field-content">
										<div class="sonet-group-create-tabs-text-input-wrap <?=(in_array("GROUP_NAME", $arResult["ErrorFields"]) ? "sonet-group-create-tabs-text-error" : "")?>" value="<?=(strlen($arResult["POST"]["NAME"]) > 0 ? $arResult["POST"]["NAME"] : GetMessage("SONET_GCE_T_NAME"));?>">
											<input type="text" name="GROUP_NAME" class="sonet-group-create-tabs-text-input<?=(strlen($arResult["POST"]["NAME"]) > 0 ? " sonet-group-create-tabs-text-input-active" : "");?><?=(in_array("GROUP_NAME", $arResult["ErrorFields"]) ? " sonet-group-create-tabs-text-error" : "")?>" value="<?=(strlen($arResult["POST"]["NAME"]) > 0 ? htmlspecialcharsbx($arResult["POST"]["NAME"]) : GetMessage("SONET_GCE_T_NAME"));?>" onblur="if(this.value == ''){ BX.removeClass(this, 'sonet-group-create-tabs-text-input-active'); this.value = this.value.replace(new RegExp(/^$/), '<?=GetMessage("SONET_GCE_T_NAME")?>')}" onfocus="BX.addClass(this, 'sonet-group-create-tabs-text-input-active'); this.value = this.value.replace('<?=GetMessage("SONET_GCE_T_NAME")?>', '')" />
										</div>
									</div>
								</div>
								<div class="sonet-group-create-popup-additional-fields">
									<div class="sonet-group-create-popup-field-content">
										<div class="sonet-group-create-tabs-textarea-wrap">
											<textarea class="<?=(strlen($arResult["POST"]["DESCRIPTION"]) > 0 ? "sonet-group-create-tabs-textarea-active" : "");?>" name="GROUP_DESCRIPTION" onblur="if(this.value == ''){BX.removeClass(this, 'sonet-group-create-tabs-textarea-active');  this.value = this.value.replace(new RegExp(/^$/), '<?=GetMessage("SONET_GCE_T_DESCR")?>')}" onfocus="BX.addClass(this, 'sonet-group-create-tabs-textarea-active'); this.value = this.value.replace('<?=GetMessage("SONET_GCE_T_DESCR")?>', '')"><?=(strlen($arResult["POST"]["DESCRIPTION"]) > 0 ? $arResult["POST"]["DESCRIPTION"] : GetMessage("SONET_GCE_T_DESCR"));?></textarea>
										</div>
										<div style="margin-top: 10px;" class="<?=(in_array("GROUP_IMAGE_ID", $arResult["ErrorFields"]) ? "sonet-group-create-popup-field-upload-error" : "")?>">
											<?
											$APPLICATION->IncludeComponent('bitrix:main.file.input', '', array(
												'INPUT_NAME' => 'GROUP_IMAGE_ID',
												'INPUT_NAME_UNSAVED' => 'GROUP_IMAGE_ID_UNSAVED',
												'CONTROL_ID' => 'GROUP_IMAGE_ID',
												'INPUT_VALUE' => $arResult["POST"]["IMAGE_ID"],
												'MULTIPLE' => 'N',
												'ALLOW_UPLOAD' => 'I',
												'INPUT_CAPTION' => GetMessage("SONET_GCE_T_UPLOAD_IMAGE")
											));
											?>
											<script>
											function onFileUploaderChangeHandler(files) {
												if (files && files.length > 0)
												{
													BX('sonet_group_create_popup_image').src = files[0].fileURL;
													BX.show(BX('sonet_group_create_tabs_image_block'));
													BX("sonet_group_create_tabs_image_block").style.visibility = "visible";
												}
												else
												{
													BX.hide(BX('sonet_group_create_tabs_image_block'));
													BX("sonet_group_create_tabs_image_block").style.visibility = "hidden";
												}
											}
											BX.addCustomEvent(window.FILE_INPUT_GROUP_IMAGE_ID, 'onFileUploaderChange', onFileUploaderChangeHandler);
											</script>
										</div><?

										$bIsSepNeeded = false;

										?><div class="sonet-group-create-tabs-filter-wrap"><?
											if (!CModule::IncludeModule('extranet') || !CExtranet::IsExtranetSite() || intval($arResult["GROUP_ID"]) > 0):

												?><div><?

													if (!CModule::IncludeModule('extranet') || !CExtranet::IsExtranetSite()):
														$bIsSepNeeded = true;
														if (IsModuleInstalled('extranet')):
															?><div id="GROUP_VISIBLE_block" class="<?=($arResult["POST"]["VISIBLE"] == "Y" ? "sonet-group-create-popup-checkbox-active" : "")?>" style="<?=($arResult["POST"]["IS_EXTRANET_GROUP"] == "Y" ? " display: none;" : "")?>"><input type="checkbox" onclick="BXSwitchNotVisible(this.checked)" class="sonet-group-create-popup-checkbox" id="GROUP_VISIBLE" value="Y" name="GROUP_VISIBLE"<?= ($arResult["POST"]["VISIBLE"] == "Y") ? " checked" : ""?>> <label for="GROUP_VISIBLE"><?= GetMessage("SONET_GCE_T_PARAMS_VIS") ?></label></div><?
														else:
															?><div id="GROUP_VISIBLE_block" class="<?=($arResult["POST"]["VISIBLE"] == "Y" ? "sonet-group-create-popup-checkbox-active" : "")?>"><input type="checkbox" onclick="BXSwitchNotVisible(this.checked)" class="sonet-group-create-popup-checkbox" id="GROUP_VISIBLE" value="Y" name="GROUP_VISIBLE"<?= ($arResult["POST"]["VISIBLE"] == "Y") ? " checked" : ""?>> <label for="GROUP_VISIBLE"><?= GetMessage("SONET_GCE_T_PARAMS_VIS") ?></label></div><?
														endif;
													else:
														?><input type="hidden" value="N" name="GROUP_VISIBLE"><?
													endif;

													if (!CModule::IncludeModule('extranet') || !CExtranet::IsExtranetSite()):
														$bIsSepNeeded = true;
														if (IsModuleInstalled('extranet')):
															?><div id="GROUP_OPENED_block" class="<?=($arResult["POST"]["OPENED"] == "Y" ? "sonet-group-create-popup-checkbox-active" : "")?>" style="<?=($arResult["POST"]["IS_EXTRANET_GROUP"] == "Y" ? " display: none;" : "")?>"><input type="checkbox" onclick="BX.toggleClass(this.parentNode, 'sonet-group-create-popup-checkbox-active')" class="sonet-group-create-popup-checkbox" id="GROUP_OPENED" value="Y" name="GROUP_OPENED"<?= ($arResult["POST"]["OPENED"] == "Y") ? " checked" : ""?> <?= ($arResult["POST"]["VISIBLE"] == "Y") ? "" : " disabled"?>> <label for="GROUP_OPENED"><?= GetMessage("SONET_GCE_T_PARAMS_OPEN") ?></label></div><?
														else:
															?><div class="<?=($arResult["POST"]["OPENED"] == "Y" ? "sonet-group-create-popup-checkbox-active" : "")?>"><input type="checkbox"  onclick="BX.toggleClass(this.parentNode, 'sonet-group-create-popup-checkbox-active')"  class="sonet-group-create-popup-checkbox<?=($arResult["POST"]["IS_EXTRANET_GROUP"] == "Y" ? " sonet-group-create-popup-checkbox-active" : "")?>" id="GROUP_OPENED" value="Y" name="GROUP_OPENED"<?= ($arResult["POST"]["OPENED"] == "Y") ? " checked" : ""?> <?= ($arResult["POST"]["VISIBLE"] == "Y") ? "" : " disabled"?>> <label for="GROUP_OPENED"><?= GetMessage("SONET_GCE_T_PARAMS_OPEN") ?></label></div><?
														endif;
													else:
														?><input type="hidden" value="N" name="GROUP_OPENED"><?
													endif;

													if (intval($arParams["GROUP_ID"]) > 0):
														$bIsSepNeeded = true;
														?><div class="<?=($arResult["POST"]["CLOSED"] == "Y" ? "sonet-group-create-popup-checkbox-active" : "")?>"><input type="checkbox" onclick="BX.toggleClass(this.parentNode, 'sonet-group-create-popup-checkbox-active')" class="sonet-group-create-popup-checkbox" id="GROUP_CLOSED" value="Y" name="GROUP_CLOSED"<?= ($arResult["POST"]["CLOSED"] == "Y") ? " checked" : ""?>> <label for="GROUP_CLOSED"><?= GetMessage("SONET_GCE_T_PARAMS_CLOSED") ?></label></div><?
													else:
														?><input type="hidden" value="N" name="GROUP_CLOSED"><?
													endif;

												?></div><?

											endif;

											if (CModule::IncludeModule('extranet') && strlen(COption::GetOptionString("extranet", "extranet_site")) > 0):
												if (!CExtranet::IsExtranetSite()):
													if ($bIsSepNeeded):
														?><div class="sonet-group-create-popup-sep"></div><?
													endif;
													$bIsSepNeeded = true;
													?><div id="IS_EXTRANET_GROUP_block" class="<?=($arResult["POST"]["IS_EXTRANET_GROUP"] == "Y" ? " sonet-group-create-popup-checkbox-active" : "")?>"><input type="checkbox" class="sonet-group-create-popup-checkbox" id="IS_EXTRANET_GROUP" value="Y" name="IS_EXTRANET_GROUP"<?=($arResult["POST"]["IS_EXTRANET_GROUP"] == "Y" ? " checked" : "")?> onclick="BXSwitchExtranet(this.checked)"><label for="IS_EXTRANET_GROUP"><?= GetMessage("SONET_GCE_T_IS_EXTRANET_GROUP") ?></label></div><?
												else:
													?><input type="hidden" value="Y" name="IS_EXTRANET_GROUP"><?
												endif;
											endif;

											if (count($arResult["Subjects"]) == 1):
												$arKeysTmp = array_keys($arResult["Subjects"]);
												?><input type="hidden" name="GROUP_SUBJECT_ID" value="<?=$arKeysTmp[0]?>"><?
											else:
												if ($bIsSepNeeded):
													?><div class="sonet-group-create-popup-sep"></div><?
												endif;
												?><div class="sonet-group-create-tabs-select-wrap">
													<label for="GROUP_SUBJECT_ID"><?= GetMessage("SONET_GCE_T_SUBJECT") ?></label>
													<span class="<?=(in_array("GROUP_SUBJECT_ID", $arResult["ErrorFields"]) ? "sonet-group-create-tabs-select-error" : "")?>"><select name="GROUP_SUBJECT_ID" id="GROUP_SUBJECT_ID" class="sonet-group-create-popup-select">
														<option value=""><?= GetMessage("SONET_GCE_T_TO_SELECT") ?></option>
														<?foreach ($arResult["Subjects"] as $key => $value):?>
															<option value="<?= $key ?>"<?= ($key == $arResult["POST"]["SUBJECT_ID"]) ? " selected" : "" ?>><?= $value ?></option>
														<?endforeach;?>
													</select></span>
												</div><?
											endif;

											?><div class="sonet-group-create-tabs-image-block" id="sonet_group_create_tabs_image_block">
												<input type="hidden" name="GROUP_IMAGE_ID_DEL" id="GROUP_IMAGE_ID_DEL" value=""/>
												<span class="sonet-group-create-tabs-image-wrap"><?
													if (strlen($arResult["POST"]["IMAGE_ID_IMG"]) > 0):?>
														<?=$arResult["POST"]["IMAGE_ID_IMG"];?><br /><?
													endif;
													?><a class="sonet-group-create-popup-del" id="sonet_group_create_popup_del" href="javascript:void(0);" onclick="BXDeleteImage();"></a>
												</span>
											</div>
										</div>
									</div>
									<div class="sonet-group-create-popup-field-corners-bottom">
										<div class="sonet-group-create-popup-form-left-corner"></div>
										<div class="sonet-group-create-popup-form-right-corner"></div>
									</div>
								</div>
							</div>
						</div><?
					}

					if (!array_key_exists("TAB", $arResult) || $arResult["TAB"] == "edit")
					{
						?><div id="sonet_group_create_tabs_features" style="display: none;">
							<div class="sonet-group-create-popup-features-title"><?=GetMessage("SONET_GCE_T_FEATURES_TAB_TITLE");?></div>
							<div style="overflow: hidden; padding: 0 25px;" id="sonet_group_create_popup_features">
								<div class="sonet-group-create-popup-features-leftcol"><?
									$i = 1;
									foreach ($arResult["POST"]["FEATURES"] as $feature => $arFeature):

										if ($i > intval(count($arResult["POST"]["FEATURES"])/2)):
											?></div>
											<div class="sonet-group-create-popup-features-rightcol"><?
											$i = 1;
										endif;

										?><a href="javascript:void(0);" class="sonet-group-create-popup-feature<?= ($arFeature["Active"] ? " sonet-group-create-popup-feature-active" : "") ?>"><span class="sonet-group-create-popup-feature-img"></span><?= (array_key_exists("title", $GLOBALS["arSocNetFeaturesSettings"][$feature]) && strlen($GLOBALS["arSocNetFeaturesSettings"][$feature]["title"]) > 0 ? $GLOBALS["arSocNetFeaturesSettings"][$feature]["title"] : GetMessage("SONET_FEATURES_".$feature))?></a><?
										?><input class="sonet-group-create-popup-feature-hidden" type="hidden" id="<?= $feature ?>_active_id" name="<?= $feature ?>_active" value="<?= ($arFeature["Active"] ? "Y" : "") ?>"><?
										$i++;
									endforeach;
								?></div>
							</div>
						</div><?
					}

					if (!array_key_exists("TAB", $arResult) || $arResult["TAB"] == "invite")
					{
						if ($arResult["TAB"] == "invite")
							$strSubmitButtonTitle = GetMessage("SONET_GCE_T_DO_INVITE");

						?><div id="sonet_group_create_tabs_invite"<?=($arResult["TAB"] != "invite" ? ' style="display: none;"' : '')?>><?
							if (!$arResult["bIntranet"])
							{
								?><script language="JavaScript">
								<!--
								var bFirstUser = true;
								function AddUser(name)
								{
									if (name.length <= 0)
										return;

									var userDiv = BX("id_users");

									if (bFirstUser)
									{
										userDiv.innerHTML = "";
										bFirstUser = false;
									}

									userDiv.innerHTML += "<b>" + name.replace("<", "&lt;").replace(">", "&gt;") + "</b><br />";
									BX("users_list").value += name + ",";
								}
								//-->
								</script><?

								?><div id="id_users"><i><?= GetMessage("SONET_GCE_T_UNOTSET") ?></i></div>
								<input type="hidden" name="users_list" id="users_list" value=""><br /><?

								$APPLICATION->IncludeComponent(
									"bitrix:socialnetwork.user_search_input",
									".default",
									array(
										"TEXT" => "size='50'",
										"EXTRANET" => "I",
										"FUNCTION" => "AddUser",
										"NAME_TEMPLATE" => $arParams["NAME_TEMPLATE"],
										"SHOW_LOGIN" => $arParams["SHOW_LOGIN"],
										"CLASS_NAME" => (in_array("USERS", $arResult["ErrorFields"]) ? " search-tags-error" : "")
									)
								);
							}
							else
							{
								if ($arResult["bExtranet"])
									echo "<p>".GetMessage("SONET_GCE_T_USER_INTRANET")."<br>";

								$arValue = ($arResult["POST"]["USER_IDS"] ? $arResult["POST"]["USER_IDS"] : array());
								?><script type="text/javascript">
									window.bx_user_url_tpl = '<?=CUtil::JSEscape($arParams["PATH_TO_USER"])?>';

									window.arInvitationUsersList = [];

									function UpdateInvitationUsersList(arUsers)
									{
										BX.cleanNode(BX('invitation_users'));
										var h = '';

										for (var i = 0; i < arUsers.length; i++)
											h += '<input type="hidden" name="USER_IDS[]" value="'+arUsers[i].id+'" />';
										BX('invitation_users').innerHTML = h;
									}

									BX.addCustomEvent('onInvitationUsersListChange', UpdateInvitationUsersList);

								</script>
								<div class="sonet-invitation-users-block<?=(in_array("USERS", $arResult["ErrorFields"]) ? " sonet-group-create-tabs-users-error" : "")?>">
									<span class="sonet-invitation-new-users">
										<div class="sonet-group-create-popup-form-user-title" style="padding-left: 6px;"><?=GetMessage("SONET_GCE_T_INVITATION_EMPLOYEES")?>:</div>
										<?
										$APPLICATION->IncludeComponent(
											"bitrix:intranet.user.selector.new", ".default", array(
												"MULTIPLE" => "Y",
												"NAME" => "USERS",
												"VALUE" => $arValue,
												"POPUP" => "N",
												"ON_CHANGE" => "BXOnInviteListChange",
												"SITE_ID" => SITE_ID,
												"NAME_TEMPLATE" => $arParams["NAME_TEMPLATE"],
												"SHOW_LOGIN" => $arParams["SHOW_LOGIN"],
												"SHOW_EXTRANET_USERS" => ($arResult["POST"]["IS_EXTRANET_GROUP"] == "Y" ? "FROM_MY_GROUPS" : "NONE")
											),
											null,
											array("HIDE_ICONS" => "Y")
										);
										?>
										<span id="invitation_users"></span>
									</span>
								</div><div style="margin-bottom: 10px;"></div><?
							}

							if (IsModuleInstalled("extranet") && strlen(COption::GetOptionString("extranet", "extranet_site")) > 0)
							{
								?><div id="EMAILS_block" style="display: <?=($arResult["POST"]["IS_EXTRANET_GROUP"] == "Y" ? "block" : "none")?>;">
									<div class="sonet-group-create-popup-form-user-title"><?=GetMessage("SONET_GCE_T_EMAIL")?></div>
									<div class="sonet-group-create-popup-form-email-block">
										<?if(strlen($arResult["WarningMessage"]) > 0):?>
											<span class='errortext'><?=$arResult["WarningMessage"]?></span><br/>
										<?endif;?>
										<span class="sonet-group-create-popup-form-email-wrap<?=(in_array("USERS", $arResult["ErrorFields"]) ? " sonet-group-create-tabs-users-error" : "")?>">
											<input type="hidden" name="EMAILS" id="EMAILS" value="<?=$arResult["POST"]["EMAILS"]?>" />
											<input name="EMAIL" class="sonet-group-create-popup-form-email-input" id="sonet_group_create_popup_form_email_input" value="e-mail" type="text"
													onblur="if(this.value == '') { BX.removeClass(this, 'sonet-group-create-popup-form-email-input-active'); this.value = this.value.replace(new RegExp(/^$/), 'e-mail') }"
													onclick="BX.addClass(this, 'sonet-group-create-popup-form-email-input-active'); this.value = this.value.replace('e-mail', '');"/><span class="sonet-group-create-popup-form-email-add" onclick="__addExtranetEmail()"></span>
										</span><span class="sonet-group-create-popup-form-email-bl" id="sonet_group_create_popup_form_email_bl"><?
										if (strlen(trim($arResult["POST"]["EMAILS"])) > 0):
											$arEmails = explode(", ", $arResult["POST"]["EMAILS"]);
											$i = 1;
											foreach($arEmails as $email):
												?>
												<span class="sonet-group-create-popup-form-email" id="sonet_group_create_popup_form_email_<?=$i?>"><?=$email?><a class="sonet-group-create-popup-del" href="javascript:void(0);" onclick="__deleteExtranetEmail(this);"></a></span>
												<script type="text/javascript">
													top.BXExtranetMailList.push('<?=$email?>');
												</script><?
												$i++;
											endforeach;
										endif;
										?></span>
									</div>
								</div><?
							}
						?></div><?
					}

					if (!array_key_exists("TAB", $arResult) || $arResult["TAB"] == "edit")
					{
						?><div id="sonet_group_create_tabs_additional" style="<?=($active_tab == "additional" ? "" : "display: none;")?>">
							<div class="sonet-group-create-popup-form-add">
								<div class="sonet-group-create-popup-form-corners-top">
									<div class="sonet-group-create-popup-form-left-corner"></div>
									<div class="sonet-group-create-popup-form-right-corner"></div>
								</div>
								<div class="sonet-group-create-popup-field-content"><?

									if ($arResult["POST"]["CLOSED"] != "Y"):
										?><div class="sonet-group-create-popup-form-add-title"><?= GetMessage("SONET_GCE_T_INVITE") ?></div>
										<div class="sonet-group-create-popup-form-add-select"><select name="GROUP_INITIATE_PERMS" id="GROUP_INITIATE_PERMS" class="sonet-group-create-popup-select<?=(in_array("GROUP_INITIATE_PERMS", $arResult["ErrorFields"]) ? " sonet-group-create-tabs-select-error" : "")?>">
											<option value=""><?= GetMessage("SONET_GCE_T_TO_SELECT") ?>-</option><?
											foreach ($arResult["InitiatePerms"] as $key => $value):
												?><option id="GROUP_INITIATE_PERMS_OPTION_<?=$key?>" value="<?= $key ?>"<?= ($key == $arResult["POST"]["INITIATE_PERMS"]) ? " selected" : "" ?>><?= $value ?></option><?
											endforeach;
										?></select></div><?
									else:
										?><input type="hidden" value="<?=$arResult["POST"]["INITIATE_PERMS"]?>" name="GROUP_INITIATE_PERMS"><?
									endif;

									if (
										$arResult["POST"]["CLOSED"] != "Y"
										&& (!CModule::IncludeModule('extranet') || !CExtranet::IsExtranetSite())
										&& !IsModuleInstalled("im")
									):
										?><div class="sonet-group-create-popup-form-add-title"><?= GetMessage("SONET_GCE_T_SPAM_PERMS") ?></div>
										<div class="sonet-group-create-popup-form-add-select"><select name="GROUP_SPAM_PERMS" class="sonet-group-create-popup-select-perms<?=(in_array("GROUP_SPAM_PERMS", $arResult["ErrorFields"]) ? " sonet-group-create-tabs-select-error" : "")?>">
											<option value=""><?= GetMessage("SONET_GCE_T_TO_SELECT") ?>-</option><?
											foreach ($arResult["SpamPerms"] as $key => $value):
												?><option value="<?= $key ?>"<?= ($key == $arResult["POST"]["SPAM_PERMS"]) ? " selected" : "" ?>><?= $value ?></option><?
											endforeach;
										?></select></div><?
									else:
										?><input type="hidden" value="<?=$arResult["POST"]["SPAM_PERMS"]?>" name="GROUP_SPAM_PERMS"><?
									endif;

									if ($arParams["USE_KEYWORDS"] == "Y"):
										?><div class="sonet-group-create-popup-form-add-title"><?= GetMessage("SONET_GCE_T_KEYWORDS") ?></div><div class="sonet-group-create-popup-form-add-select"><?
										if (IsModuleInstalled("search")):?><?
											$APPLICATION->IncludeComponent(
												"bitrix:search.tags.input",
												".default",
												Array(
													"NAME" => "GROUP_KEYWORDS",
													"ID" => "GROUP_KEYWORDS",
													"VALUE" => $arResult["POST"]["KEYWORDS"],
													"arrFILTER" => "socialnetwork",
													"PAGE_ELEMENTS" => "10",
													"SORT_BY_CNT" => "Y",
												)
											);
											?><?
										else:
											?><input type="text" name="GROUP_KEYWORDS" style="width:98%" value="<?= $arResult["POST"]["KEYWORDS"]; ?>"><?
										endif;
										?></div><?
									endif;
									//user fields
									if (is_array($arResult["GROUP_PROPERTIES"]) && count($arResult["GROUP_PROPERTIES"]) > 0)
									{
										?>
										<div class="sonet-group-create-uf-header"><?=GetMessage("SONET_GCE_UF_HEADER")?></div>
										<div class="sonet-group-create-uf-content">
										<?
										foreach ($arResult["GROUP_PROPERTIES"] as $FIELD_NAME => $arUserField):
											?><div class="sonet-group-create-tabs-select-wrap<?=(in_array($FIELD_NAME, $arResult["ErrorFields"]) ? " sonet-group-create-tabs-uf-error" : "")?>">
												<div class="sonet-group-create-popup-form-add-title"><label><?= $arUserField["EDIT_FORM_LABEL"] ?><?= ($arUserField["MANDATORY"] == "Y") ? '<span class="sonet-group-create-uf-required">&nbsp;*</span>' : ''?></label></div>
												<div class="sonet-group-create-popup-form-add-select-uf"><?
												$APPLICATION->IncludeComponent(
													"bitrix:system.field.edit",
													$arUserField["USER_TYPE"]["USER_TYPE_ID"],
													array("bVarsFromForm" => $arResult["bVarsFromForm"], "arUserField" => $arUserField),
													null,
													array("HIDE_ICONS"=>"Y")
												);
												?></div>
											</div><?
										endforeach;
									}

									?>
									</div>
								</div>
								<div class="sonet-group-create-popup-form-corners-bottom">
									<div class="sonet-group-create-popup-form-left-corner"></div>
									<div class="sonet-group-create-popup-form-right-corner"></div>
								</div>
							</div>
						</div><?
					}

				?></div>

				<div class="sonet-group-create-tabs-footer">
					<input type="hidden" name="SONET_USER_ID" value="<?= $GLOBALS["USER"]->GetID() ?>">
					<input type="hidden" name="SONET_GROUP_ID" value="<?=intval($arResult["GROUP_ID"])?>">
					<?=bitrix_sessid_post()?>
					<a href="javascript:void(0);" class="sonet-group-create-popup-form-smbutton sonet-group-create-popup-form-smbutton-accept" id="sonet_group_create_popup_form_button_submit">
						<span class="sonet-group-create-popup-form-smbutton-left"></span><span class="sonet-group-create-popup-form-smbutton-text"><?=$strSubmitButtonTitle?></span><span class="sonet-group-create-popup-form-smbutton-right"></span>
					</a>
					<span class="popup-window-button popup-window-button-link popup-window-button-link-cancel" onclick="onCancelClick(event);"><span class="popup-window-button-link-text"><?= GetMessage("SONET_GCE_T_T_CANCEL") ?></span></span>
				</div>

			</div>
		</form>


		<?
	}
	else
	{
		if ($arParams["GROUP_ID"] > 0):
			?><?= GetMessage("SONET_GCE_T_SUCCESS_EDIT")?><?
		else:
			?><?= GetMessage("SONET_GCE_T_SUCCESS_CREATE")?><?
		endif;
		?><br><br>
		<a href="<?= $arResult["Urls"]["NewGroup"] ?>"><?= $arResult["POST"]["NAME"]; ?></a><?
	}
}
?>
<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); $this->setFrameMode(true);?>

<div aria-hidden="true" aria-labelledby="myModalLabel4" role="dialog" tabindex="-1" class="modal hide fade full-regist" id="regModal">
	<div class="modal-header">
		<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
		<h3 id="myModalLabel4"><?=GetMessage("AUTH_SIMPLE_REG")?></h3>
	</div>
	<div class="modal-body">
     
	<div id="error_container"></div>
	<div id="register_container">
		
		<? if (count($arResult["ERRORS"]) > 0): ?>
			<h1 class="title"><?= GetMessage("AUTH_REGISTER"); ?></h1>
			<?
				foreach ($arResult["ERRORS"] as $key => $error) if (intval($key) == 0 && $key !== 0) $arResult["ERRORS"][$key] = str_replace("#FIELD_NAME#", "&quot;".GetMessage("REGISTER_FIELD_".$key)."&quot;", $error);
				ShowError(implode("<br />", $arResult["ERRORS"]));
			?>
		<? endif;
		?>
		<form id="reg" method="post" action="<?=SITE_DIR?>auth/ajax/forms.php" name="regform" >
			<input type="hidden" name="UF_SHOWNOTES" value="0">
			<input type="hidden" name="form_id" value="reg" >
			<? if ($arResult["BACKURL"] <> ""): ?>
				<input type="hidden" name="backurl" value="<?= $arResult["BACKURL"] ?>" />
			<? endif; ?>
			<div class="reg-form">
				<? foreach ($arResult["SHOW_FIELDS"] as $FIELD): ?>
					
						<? if ($FIELD == "AUTO_TIME_ZONE" && $arResult["TIME_ZONE_ENABLED"] == true): ?>
							<div>
								<?= GetMessage("main_profile_time_zones_auto"); ?>
								<? if ($arResult["REQUIRED_FIELDS_FLAGS"][$FIELD] == "Y"): ?>
									<span class="starrequired">
										*
									</span>
								<? endif; ?>
							</div>
							<div>
								<select name="REGISTER[AUTO_TIME_ZONE]" onchange="this.form.elements['REGISTER[TIME_ZONE]'].disabled=(this.value != 'N')">
									<option value="">
										<?= GetMessage("main_profile_time_zones_auto_def"); ?>
									</option>
									<option value="Y"<?= ($arResult["VALUES"][$FIELD] == "Y") ? " selected=\"selected\"" : "" ?>>
										<?= GetMessage("main_profile_time_zones_auto_yes"); ?>
									</option>
									<option value="N"<?= ($arResult["VALUES"][$FIELD] == "N") ? " selected=\"selected\"" : ""?>>
										<?= GetMessage("main_profile_time_zones_auto_no"); ?>
									</option>
								</select>
							</div>
							<div>
								<?= GetMessage("main_profile_time_zones_zones"); ?>
							</div>
							<div>
								<select name="REGISTER[TIME_ZONE]" <? if (!isset($_REQUEST["REGISTER"]["TIME_ZONE"])): ?>disabled<? endif; ?>>
									<? foreach ($arResult["TIME_ZONE_LIST"] as $tz => $tz_name): ?>
										<option value="<?= htmlspecialchars($tz); ?>"<?= ($arResult["VALUES"]["TIME_ZONE"] == $tz) ? " selected=\"selected\"" : "" ?>>
											<?= htmlspecialchars($tz_name); ?>
										</option>
									<? endforeach; ?>
								</select>
							</div>
						<? else: ?>
							<div class="name">
								<?= GetMessage("REGISTER_FIELD_".$FIELD)?>:
								<? if ($arResult["REQUIRED_FIELDS_FLAGS"][$FIELD] == "Y"): ?>
									<span class="starrequired">
										*
									</span>
								<? endif; ?>
							</div>
							<div class="value">
								<? switch ($FIELD) {
									case "PASSWORD":?>
										<input size="30" type="password" id="REGISTER_<?=$FIELD?>" name="REGISTER[<?= $FIELD ?>]" value="<?= $arResult["VALUES"][$FIELD] ?>" autocomplete="off" class="bx-auth-input" />
										<? if ($arResult["SECURE_AUTH"]): ?>
											<span class="bx-auth-secure" id="bx_auth_secure" title="<?= GetMessage("AUTH_SECURE_NOTE"); ?>" style="display:none">
												<div class="bx-auth-secure-icon"></div>
											</span>
											<noscript>
												<span class="bx-auth-secure" title="<?= GetMessage("AUTH_NONSECURE_NOTE"); ?>">
													<div class="bx-auth-secure-icon bx-auth-secure-unlock"></div>
												</span>
											</noscript>
											<script type="text/javascript">
												document.getElementById('bx_auth_secure').style.display = 'inline-block';
											</script>
										<? endif; ?>
										<? break;
									case "CONFIRM_PASSWORD": ?>
										<input size="30" type="password" id="REGISTER_<?=$FIELD?>" name="REGISTER[<?= $FIELD ?>]" value="<?= $arResult["VALUES"][$FIELD] ?>" autocomplete="off" />
										<? break;
									case "PERSONAL_GENDER": ?>
										<select name="REGISTER[<?= $FIELD ?>]">
											<option value="">
												<?= GetMessage("USER_DONT_KNOW"); ?>
											</option>
											<option value="M"<?= ($arResult["VALUES"][$FIELD]) == "M" ? " selected=\"selected\"" : "" ?>>
												<?= GetMessage("USER_MALE"); ?>
											</option>
											<option value="F"<?= ($arResult["VALUES"][$FIELD] == "F") ? " selected=\"selected\"" : "" ?>>
												<?= GetMessage("USER_FEMALE"); ?>
											</option>
										</select>
										<? break;
									case "PERSONAL_COUNTRY":
									case "WORK_COUNTRY": ?>
										<select name="REGISTER[<?= $FIELD ?>]">
											<? foreach ($arResult["COUNTRIES"]["reference_id"] as $key => $value): ?>
												<option value="<?= $value ?>"<?if ($value == $arResult["VALUES"][$FIELD]): ?> selected<? endif; ?>>
													<?= $arResult["COUNTRIES"]["reference"][$key] ?>
												</option>
											<? endforeach; ?>
										</select>
										<? break;
									case "PERSONAL_PHOTO":
									case "WORK_LOGO": ?>
										<input size="30" type="file" name="REGISTER_FILES_<?= $FIELD ?>" />
										<? break;
									case "PERSONAL_NOTES":
									case "WORK_NOTES": ?>
										<textarea cols="30" rows="5" name="REGISTER[<?= $FIELD ?>]">
											<?= $arResult["VALUES"][$FIELD] ?>
										</textarea>
										<? break;
									default: ?>
										<? if ($FIELD == "PERSONAL_BIRTHDAY"): ?>
											<small>
												<?= $arResult["DATE_FORMAT"] ?>
											</small>
											<br />
										<? endif; ?>
										<input size="30" type="text" id="REGISTER_<?=$FIELD?>" name="REGISTER[<?= $FIELD ?>]" value="<?= $arResult["VALUES"][$FIELD] ?>" />
										<? if ($FIELD == "PERSONAL_BIRTHDAY"): ?>
											<? $APPLICATION->IncludeComponent(
												'bitrix:main.calendar',
												'',
												array(
													'SHOW_INPUT' => 'N',
													'FORM_NAME' => 'regform',
													'INPUT_NAME' => 'REGISTER[PERSONAL_BIRTHDAY]',
													'SHOW_TIME' => 'N'
												),
												null,
												array("HIDE_ICONS"=>"Y")
											); ?>
										<? endif; ?>
								<? } ?>
							</div>
							<div class="clear"></div>
						<? endif; ?>
					
				<? endforeach; ?>


<? if ($arResult["USE_CAPTCHA"] == "Y"): ?>

<div class="name ch"><?= GetMessage("REGISTER_CAPTCHA_TITLE"); ?></div>

<div class="capca">
	<input type="hidden" name="captcha_sid" value="<?= $arResult["CAPTCHA_CODE"] ?>" />
		<img src="/bitrix/tools/captcha.php?captcha_sid=<?= $arResult["CAPTCHA_CODE"] ?>" width="180" height="40" alt="CAPTCHA" />
</div>
<div class="clear"></div>
<div class="name"><?= GetMessage("REGISTER_CAPTCHA_PROMT"); ?>: <span class="starrequired"> * </span></div>
<div class="value">
	<input type="text" name="captcha_word" maxlength="50" value="" />
</div>
<? endif; ?>
				

				<div class="value agree">
								<input type="checkbox" name="agree" checked id="chb_agree">
								<label for="chb_agree"><a id="agree-link" href="#">
								<?= GetMessage("RULES1"); ?>
								<?= GetMessage("RULES2"); ?>
								<?= GetMessage("RULES3"); ?>
</a></label>
				</div>
				<input type="submit" value="<?=GetMessage("REGISTER_BUTTON")?>" id="reg_submit"  class="btn btn-r">	
				<? /* if ($arResult["USE_CAPTCHA"] == "Y"): ?>
					<div>
						<b>
							<?= GetMessage("REGISTER_CAPTCHA_TITLE"); ?>
						</b>
					</div>
					<div>
						<input type="hidden" name="captcha_sid" value="<?= $arResult["CAPTCHA_CODE"] ?>" />
						<img src="/bitrix/tools/captcha.php?captcha_sid=<?= $arResult["CAPTCHA_CODE"] ?>" width="180" height="40" alt="CAPTCHA" />
					</div>
					<div>
						<?= GetMessage("REGISTER_CAPTCHA_PROMT"); ?>:
						<span class="starrequired">*</span>
					</div>
					<div>
						<input type="text" name="captcha_word" maxlength="50" value="" />
					</div>
				<? endif; */?>
				
					<a id="register_already" class="already-l" ><?= GetMessage("REGISTER_EARLIER"); ?></a>
					<div class="clear"></div>
					<p><a href="<?=SITE_DIR?>" class="back">
						<span>
							<?= GetMessage("REGISTER_HOME"); ?>
						</span>
					</a></p>
				
			</div>
		</form>
	</div>
	</div>
</div>
<??>
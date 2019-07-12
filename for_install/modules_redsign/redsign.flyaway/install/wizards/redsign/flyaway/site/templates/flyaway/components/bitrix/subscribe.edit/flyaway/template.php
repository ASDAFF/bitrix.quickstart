<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!empty($arResult['MESSAGE']) && count($arResult['MESSAGE']) > 0) {
	?><div class="row"><?
		?><div class="col col-md-6"><?
			?><div class="alert alert-success"><?
			foreach($arResult["MESSAGE"] as $message) {
				echo $message;
			}
			?></div><?
		?></div><?
	?></div><?
}

if(!empty($arResult['ERROR']) && count($arResult['ERROR']) > 0) {
	?><div class="row"><?
		?><div class="col col-md-6"><?
			?><div class="alert alert-danger"><?
			foreach($arResult["ERROR"] as $error) {
				echo $error;
			}
			?></div><?
		?></div><?
	?></div><?
}

if($arResult["ALLOW_ANONYMOUS"]=="N" && !$USER->IsAuthorized()) {
	?><div class="row"><?
		?><div class="col col-md-6"><?
			?><div class="alert alert-info"><?
			echo GetMessage("CT_BSE_AUTH_ERR");
			?></div><?
		?></div><?
	?></div><?
} else {
	?><div class="subscription"><?
		?><div class="row"><?
			?><div class="col col-md-6"><?
				?><form action="<?=$arResult["FORM_ACTION"]?>" method="post"><?
					echo bitrix_sessid_post();
					?><input type="hidden" name="PostAction" value="<?echo ($arResult["ID"]>0? "Update":"Add")?>" /><?
					?><input type="hidden" name="ID" value="<?echo $arResult["SUBSCRIPTION"]["ID"];?>" /><?
					?><input type="hidden" name="RUB_ID[]" value="0" /><?

					?><div class="field-wrap"><?
						?><div class="label-wrap"><?=GetMessage('RS.FLYAWAY.FIELD_EMAIL')?><span class="required"> *</span></div><?
						?><div class="form"><input class="form-control form-item" type="text" name="EMAIL" value="<?echo $arResult["SUBSCRIPTION"]["EMAIL"]!=""? $arResult["SUBSCRIPTION"]["EMAIL"]: $arResult["REQUEST"]["EMAIL"];?>" /></div><?
					?></div><?

					?><div class="field-wrap"><?
						?><div class="label-wrap"><?=GetMessage('CT_BSE_FORMAT_LABEL')?></div><?
						?><div class="padleft"><?
							?><div class="row"><?
								?><div class="col col-xs-2"><?
									?><div class="gui-box"><?
										?><label class="gui-radiobox" for="MAIL_TYPE_TEXT"><?
											?><input type="radio" class="gui-radiobox-item" name="FORMAT" id="MAIL_TYPE_TEXT" value="text" <?if($arResult["SUBSCRIPTION"]["FORMAT"] != "html") echo "checked"?> />
											<span class="gui-out">
												<span class="gui-inside"></span>
											</span>
											<span class="js-name-filter"><?echo GetMessage("CT_BSE_FORMAT_TEXT")?></span>
										</label>
									</div>
								</div><?
								?><div class="col col-xs-2"><?
									?><div class="gui-box"><?
										?><label class="gui-radiobox" for="MAIL_TYPE_HTML"><?
											?><input type="radio" class="gui-radiobox-item" name="FORMAT" id="MAIL_TYPE_HTML" value="html" <?if($arResult["SUBSCRIPTION"]["FORMAT"] == "html") echo "checked"?> />
											<span class="gui-out">
												<span class="gui-inside"></span>
											</span>
											<span class="js-name-filter"><?echo GetMessage("CT_BSE_FORMAT_HTML")?></span>
										</label><?
									?></div><?
								?></div><?
							?></div><?
						?></div><?
					?></div><?

					?><div class="field-wrap"><?
						?><div class="label-wrap"><?=GetMessage('RS.FLYAWAY.RUBRIC')?></div><?
						?><div class="padleft"><?
							foreach($arResult["RUBRICS"] as $itemID => $itemValue) {
								?><div class="rubric"><?
									?><label class="gui-checkbox"><?
										?><input type="checkbox" id="RUBRIC_<?=$itemID?>" class="gui-checkbox-input" name="RUB_ID[]" value="<?=$itemValue["ID"]?>"<?if($itemValue["CHECKED"]) echo " checked"?> />
										<span class="gui-checkbox-icon"></span>
										<span class="js-name-filter"><?=$itemValue["NAME"]?></span>
									</label><?
									?><div><?=$itemValue["DESCRIPTION"]?></div><?
								?></div><?
							}
						?></div><?
					?></div><?

					?><hr /><?

					?><div class="field-wrap"><?
						if($arResult["ID"]==0) {
							?><div class="subscription-notes"><?=GetMessage("CT_BSE_NEW_NOTE")?></div><?
						} else {
							?><div class="subscription-notes"><?=GetMessage("CT_BSE_EXIST_NOTE")?></div><?
						}
						?><div class="btns"><?
							?><input class="btn btn-primary btn2" type="submit" name="Save" value="<?echo ($arResult["ID"] > 0 ? GetMessage("CT_BSE_BTN_EDIT_SUBSCRIPTION"): GetMessage("CT_BSE_BTN_ADD_SUBSCRIPTION"))?>" /><?
						?></div><?
					?></div><?

					if($arResult["ID"]>0 && $arResult["SUBSCRIPTION"]["CONFIRMED"] <> "Y") {
						?><hr /><?
						?><div class="field-wrap"><?
							?><div class="label-wrap"><?=GetMessage('CT_BSE_CONF_NOTE')?></div><?
							?><div class="form"><input class="form-control form-item" name="CONFIRM_CODE" type="text" value="" placeholder="<?=GetMessage("CT_BSE_CONFIRMATION")?>" /></div><br /><?
							?><input class="btn btn-primary btn2" type="submit" name="confirm" value="<?=GetMessage("CT_BSE_BTN_CONF")?>" /><?
						?></div><?
					}

				?></form><?

				if(!CSubscription::IsAuthorized($arResult["ID"])) {
					?><hr /><?
					?><form action="<?=$arResult["FORM_ACTION"]?>" method="post"><?
						echo bitrix_sessid_post();
						?><input type="hidden" name="action" value="sendcode" /><?
						?><div class="field-wrap"><?
							?><div class="label-wrap"><?=GetMessage("CT_BSE_SEND_NOTE")?></div><?
							?><div class="form">
								<input class="form-control form-item" name="sf_EMAIL" type="text" value="" placeholder="<?=GetMessage("CT_BSE_EMAIL")?>" />
							</div>
								<br /><?
							?><input class="btn btn-primary btn2" type="submit" value="<?=GetMessage("CT_BSE_BTN_SEND")?>" /><?
						?></div><?
					?></form><?
				}

				?><div class="field-wrap"><?
					?><span><span class="required">*</span><?=GetMessage('RS.FLYAWAY.REQUIRE_NOTE')?></span><?
				?></div><?

			?></div><?
		?></div><?

	?></div><?
}

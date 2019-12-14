<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

foreach($arResult["MESSAGE"] as $itemID=>$itemValue)
	echo ShowMessage(array("MESSAGE"=>$itemValue, "TYPE"=>"OK"));
foreach($arResult["ERROR"] as $itemID=>$itemValue)
	echo ShowMessage(array("MESSAGE"=>$itemValue, "TYPE"=>"ERROR"));

if($arResult["ALLOW_ANONYMOUS"]=="N" && !$USER->IsAuthorized()) {
	echo ShowMessage(array("MESSAGE"=>GetMessage("CT_BSE_AUTH_ERR"), "TYPE"=>"ERROR"));
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
						?><div class="label-wrap"><?=GetMessage('RS.MONOPOLY.FIELD_EMAIL')?><span class="required"> *</span></div><?
						?><input class="form-control" type="text" name="EMAIL" value="<?echo $arResult["SUBSCRIPTION"]["EMAIL"]!=""? $arResult["SUBSCRIPTION"]["EMAIL"]: $arResult["REQUEST"]["EMAIL"];?>" /><?
					?></div><?

					?><div class="field-wrap"><?
						?><div class="label-wrap"><?=GetMessage('CT_BSE_FORMAT_LABEL')?></div><?
						?><div class="padleft"><?
							?><div class="row"><?
								?><div class="col col-xs-2"><?
									?><input type="radio" name="FORMAT" id="MAIL_TYPE_TEXT" value="text" <?if($arResult["SUBSCRIPTION"]["FORMAT"] != "html") echo "checked"?> /><label for="MAIL_TYPE_TEXT"><?echo GetMessage("CT_BSE_FORMAT_TEXT")?></label><?
								?></div><?
								?><div class="col col-xs-2"><?
									?><input type="radio" name="FORMAT" id="MAIL_TYPE_HTML" value="html" <?if($arResult["SUBSCRIPTION"]["FORMAT"] == "html") echo "checked"?> /><label for="MAIL_TYPE_HTML"><?echo GetMessage("CT_BSE_FORMAT_HTML")?></label><?
								?></div><?
							?></div><?
						?></div><?
					?></div><?

					?><div class="field-wrap"><?
						?><div class="label-wrap"><?=GetMessage('RS.MONOPOLY.RUBRIC')?></div><?
						?><div class="padleft"><?
							foreach($arResult["RUBRICS"] as $itemID => $itemValue) {
								?><div class="rubric"><?
									?><input type="checkbox" id="RUBRIC_<?=$itemID?>" name="RUB_ID[]" value="<?=$itemValue["ID"]?>"<?if($itemValue["CHECKED"]) echo " checked"?> /><label for="RUBRIC_<?echo $itemID?>"><?=$itemValue["NAME"]?></label><?
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
							?><input class="btn btn-primary" type="submit" name="Save" value="<?echo ($arResult["ID"] > 0 ? GetMessage("CT_BSE_BTN_EDIT_SUBSCRIPTION"): GetMessage("CT_BSE_BTN_ADD_SUBSCRIPTION"))?>" /><?
						?></div><?
					?></div><?

					if($arResult["ID"]>0 && $arResult["SUBSCRIPTION"]["CONFIRMED"] <> "Y") {
						?><hr /><?
						?><div class="field-wrap"><?
							?><div class="label-wrap"><?=GetMessage('CT_BSE_CONF_NOTE')?></div><?
							?><input class="form-control" name="CONFIRM_CODE" type="text" value="" placeholder="<?=GetMessage("CT_BSE_CONFIRMATION")?>" /><br /><?
							?><input class="btn btn-primary" type="submit" name="confirm" value="<?=GetMessage("CT_BSE_BTN_CONF")?>" /><?
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
							?><input class="form-control" name="sf_EMAIL" type="text" value="" placeholder="<?=GetMessage("CT_BSE_EMAIL")?>" /><br /><?
							?><input class="btn btn-primary" type="submit" value="<?=GetMessage("CT_BSE_BTN_SEND")?>" /><?
						?></div><?
					?></form><?
				}

				?><div class="field-wrap"><?
					?><span><span class="required">*</span><?=GetMessage('RS.MONOPOLY.REQUIRE_NOTE')?></span><?
				?></div><?

			?></div><?
		?></div><?

	?></div><?
}
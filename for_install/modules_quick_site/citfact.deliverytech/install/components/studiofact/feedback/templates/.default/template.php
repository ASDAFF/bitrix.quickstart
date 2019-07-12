<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->createFrame()->begin("");  ?>
<? if ($arResult["SUCCESS"]) { ?>
	<div class="sf_feedback_form_success"><?=GetMessage("FEEDBACK_SUCCESS");?></b></div>
<? } else { ?>
	<? if (strlen($arParams["HEAD"]) > 1) {
		?><div class="popup_head"><?=$arParams["HEAD"];?></div><?
	} ?>
	<form name="sf_feedback_form" action="" method="POST" enctype="multipart/form-data">
		<input type="hidden" name="<?=$arParams["PARENT_ID"];?>_submit" value="Y" />
		<? foreach ($arResult["PROPS"] as $code => $arProp) {
			?><div class="feedback_form_prop_line"<? if ($arProp["VISIBLE"] == "N") { ?> style="display: none;"<? } ?>>
				<label for="feedback_form_prop_<?=$arParams["PARENT_ID"].$code;?>"><?=$arProp["NAME"];?><? if ($arProp["REQUIRED"] == "Y") { ?><span class="req">*</span><? } ?>:</label>
				<? if ($arProp["PROPERTY_TYPE"] == "S" && ($arProp["USER_TYPE"] == "" || $arProp["USER_TYPE"] == "UserID")) {
					?><input type="text" class="<? if ($arResult["ERROR"]["FIELD"][$code]) { echo ' input_error'; } ?>" name="<?=$arProp["CODE"];?>" id="feedback_form_prop_<?=$arParams["PARENT_ID"].$code;?>" value="<?=$arProp["VALUE"];?>" placeholder="<?=$arProp["DEFAULT_VALUE"];?>" /><?
				} else if ($arProp["PROPERTY_TYPE"] == "S" && $arProp["USER_TYPE"] == "HTML") {
					?><textarea class="<? if ($arResult["ERROR"]["FIELD"][$code]) { echo ' input_error'; } ?>" name="<?=$arProp["CODE"];?>" id="feedback_form_prop_<?=$arParams["PARENT_ID"].$code;?>" placeholder="<?=$arProp["DEFAULT_VALUE"];?>"><?=$arProp["VALUE"];?></textarea><?
				} ?>
			</div><?
		} ?>
		<? if (count($arResult["ERROR"]) > 0) { ?>
			<div class="sf_feedback_form_error">
				<? foreach ($arResult["ERROR"]["FIELD"] as $code => $value) {
					echo "<br />".GetMessage("FEEDBACK_ERROR_".$code);
				}
				foreach ($arResult["ERROR"]["BITRIX"] as $code => $value) {
					echo $value;
				} ?>
			</div>
		<? } ?>
		<div class="feedback_form_prop_line">
			<input type="submit" value="<?=GetMessage("FEEDBACK_SUBMIT");?>" />
		</div>
	</form>
<? } ?>
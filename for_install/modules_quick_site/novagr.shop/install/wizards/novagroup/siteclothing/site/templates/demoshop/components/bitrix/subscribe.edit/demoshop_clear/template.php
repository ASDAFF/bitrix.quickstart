<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
// если в урле есть ошибка - то добавляем ее в массив ошибок

$arResult["ERROR"] = array_filter($arResult["ERROR"]);

if (isset($_REQUEST["error"]) and strlen(trim($_REQUEST["error"]))>0) {
	
	$arResult["ERROR"][] = $_REQUEST["error"];
}

$APPLICATION->AddHeadScript('/local/js/novagr.library/scripts/form/input/hide-value-by-click.js');

if (count($arResult["MESSAGE"])>0) {
	?><div class="alert alert-success">
	<?
	foreach($arResult["MESSAGE"] as $itemID=>$itemValue)
		echo "$itemValue<br>";	
	//echo ShowMessage(array("MESSAGE"=>$itemValue, "TYPE"=>"OK"));
	?></div>
	<?
	
}

if (count($arResult["ERROR"])>0) {
	?><div class="alert alert-error">
	<?
	//deb($arResult["ERROR"]);
	foreach($arResult["ERROR"] as $itemID=>$itemValue)
		echo "$itemValue";
		//echo ShowMessage(array("MESSAGE"=>$itemValue, "TYPE"=>"ERROR"));
	?></div>
	<?	
}

if($arResult["ALLOW_ANONYMOUS"]=="N" && !$USER->IsAuthorized()):
	?><div class="alert alert-error">
	<?
	echo GetMessage("CT_BSE_AUTH_ERR") ;
	//echo ShowMessage(array("MESSAGE"=>GetMessage("CT_BSE_AUTH_ERR"), "TYPE"=>"ERROR"));
	?></div>
		<?
else:

//$actionUrl = "/cabinet/?tab=subscr";
//deb($_REQUEST);
//deb($arParams);
if ($arParams["AJAX"] != 1) {
?>
	<div id="subscrDiv">
	<?php 
}	
	?>
	<form id="subscrForm1" class="form-horizontal demo-sub" action="<?=POST_FORM_ACTION_URI?>" method="post">
	<input type="hidden" name="ajax" value="1" />
	<?
	//deb($arResult["SUBSCRIPTION"]["FORMAT"] );
	echo bitrix_sessid_post();?>
	<input type="hidden" name="PostAction" value="<?echo ($arResult["ID"]>0? "Update":"Add")?>" />
	<input type="hidden" name="ID" value="<?echo $arResult["SUBSCRIPTION"]["ID"];?>" />
	<input type="hidden" name="RUB_ID[]" value="0" />
	
	<legend><?echo GetMessage("CT_BSE_BTN_EDIT_SUBSCRIPTION")?></legend>
	<p><span><?echo GetMessage("CT_BSE_FORMAT_LABEL")?></span>
	<label class="radio" for="MAIL_TYPE_TEXT">
	<?=GetMessage("CT_BSE_FORMAT_TEXT")?> <input type="radio" name="FORMAT" id="MAIL_TYPE_TEXT" value="text" <?if($arResult["SUBSCRIPTION"]["FORMAT"] != "html") echo "checked"?> />
	
	</label>
	<label class="radio" for="MAIL_TYPE_HTML">
	<?=GetMessage("CT_BSE_FORMAT_HTML")?> <input type="radio" name="FORMAT" id="MAIL_TYPE_HTML" value="html" <?if($arResult["SUBSCRIPTION"]["FORMAT"] == "html") echo "checked"?> />
	</label></p>
	
	<div class="control-group">
              				<label for="inputEmail" class="control-label"><?echo GetMessage("CT_BSE_EMAIL_LABEL")?></label>
              					<div class="controls">
              					<input type="text" name="EMAIL" data-hide-value-by-click="Email" placeholder="Email" value="<?echo $arResult["SUBSCRIPTION"]["EMAIL"]!=""? $arResult["SUBSCRIPTION"]["EMAIL"]: $arResult["REQUEST"]["EMAIL"];?>"  />
                					
              					</div>
	</div>
	<div class="control-group">
		<div class="left-bl"><?=GetMessage("CT_BSE_RUBRIC_LABEL")?></div>
        	<div class="right-bl">
            	<?
				//deb($arResult["RUBRICS"]);
				foreach($arResult["RUBRICS"] as $itemID => $itemValue):?>
					<section>
              		<label class="checkbox">
					<input type="checkbox" id="RUBRIC_<?echo $itemID?>" name="RUB_ID[]" value="<?=$itemValue["ID"]?>"<?if($itemValue["CHECKED"]) echo " checked"?> /> <span class="tlt"><?echo $itemValue["NAME"]?></span>
                	</label>
                	<p class="text-bl"><?echo $itemValue["DESCRIPTION"]?></p>
                	</section>
				<?endforeach;?>
              			</div>
              			<div class="clear"></div>
              			<?if($arResult["ID"]==0):?>
						<p class="padd"><?echo GetMessage("CT_BSE_NEW_NOTE")?></p>
						<?else:?>
							<p class="padd"><?echo GetMessage("CT_BSE_EXIST_NOTE")?></p>
						<?endif?>    			
              			<p class="padd">
              			<input class="btn" type="submit" name="Save" value="<?echo ($arResult["ID"] > 0? GetMessage("CT_BSE_BTN_EDIT_SUBSCRIPTION"): GetMessage("CT_BSE_BTN_ADD_SUBSCRIPTION"))?>" />
              			</p>
	</div>
	<?if($arResult["ID"]>0 && $arResult["SUBSCRIPTION"]["CONFIRMED"] <> "Y"):?>
	<div class="subscription-utility">
		<p><?echo GetMessage("CT_BSE_CONF_NOTE")?></p>
		<input name="CONFIRM_CODE" type="text" class="subscription-textbox" value="<?echo GetMessage("CT_BSE_CONFIRMATION")?>" onblur="if (this.value=='')this.value='<?echo GetMessage("CT_BSE_CONFIRMATION")?>'" onclick="if (this.value=='<?echo GetMessage("CT_BSE_CONFIRMATION")?>')this.value=''" /> <input type="submit" name="confirm" class="btn" value="<?echo GetMessage("CT_BSE_BTN_CONF")?>" />
	</div>
	<?endif?>

	</form>
	<?if(!CSubscription::IsAuthorized($arResult["ID"])):?>
	<form id="subscrForm2" class="form-inline" action="<?=POST_FORM_ACTION_URI?>" method="post">
	<?echo bitrix_sessid_post();?>
	<input type="hidden" name="action" value="sendcode" />
	<input type="hidden" name="ajax" value="1" />
	<div class="control-group">
	    <p><b><?echo GetMessage("CT_BSE_SEND_NOTE")?></b></p>
    	<label class="control-label">
    		<input name="sf_EMAIL" data-hide-value-by-click="<?=GetMessage("CT_EMAIL_PLACEHOLDER")?>" type="text" value="<?echo GetMessage("CT_EMAIL_PLACEHOLDER")?>"  placeholder="<?echo GetMessage("CT_EMAIL_PLACEHOLDER")?>">
    	</label>
    	<button type="submit" class="btn"><?echo GetMessage("CT_BSE_BTN_SEND")?></button>
    </div>
	</form>
	<?endif?>
<?endif;
if ($arParams["AJAX"] != 1) {
	?>
	</div>
<?php 
}
?>

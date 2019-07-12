<?
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();
/**
 * Bitrix vars
 *
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @global CMain $APPLICATION
 * @global CUser $USER
 */
?>

<?if($arParams['USE_IN_COMPONENT']!="Y"):?>
	<div class="container"><div class="row">
<?endif?>



<?if(!empty($arResult["ERROR_MESSAGE"]))
{
	foreach($arResult["ERROR_MESSAGE"] as $v)
		ShowError($v);
}
if(strlen($arResult["OK_MESSAGE"]) > 0)
{
	?><div class="mf-ok-text"><?=$arResult["OK_MESSAGE"]?></div><?
}
?>

<?if(!empty($arParams["FORM_TITLE"])):?>
<div style="text-align: center;"><<?=$arParams['FORM_TITLE_TYPE'];?> style="font-size: <?=$arParams['FORM_TITLE_SIZE'];?>em;"><?=$arParams['FORM_TITLE']?></<?=$arParams['FORM_TITLE_TYPE'];?>></div>
<?endif?>

<form action="<?=POST_FORM_ACTION_URI?>" method="POST">
<?=bitrix_sessid_post()?>
<div class="col-lg-12">
<?if(in_array("NAME", $arParams["USED_FIELDS"]) or in_array("EMAIL", $arParams["USED_FIELDS"]) or in_array("PHONE", $arParams["USED_FIELDS"])):?>
	<div class="col-lg-6">

	<?if(in_array("NAME", $arParams["USED_FIELDS"])):?>
			<?if(!empty($arParams["NAME_HINT_TITLE"])):?><?=$arParams['NAME_HINT_TITLE']?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("NAME", $arParams["REQUIRED_FIELDS"])):?><span style="color: red;">*</span><?endif?><?endif?>
		<input style="margin-top: 5px; margin-bottom: 5px; font-size: 19px; height: 50px;" placeholder="<?=$arParams['NAME_HINT_TEXT']?>" class="required form-control" type="text" name="user_name" value="<?=$arResult["AUTHOR_NAME"]?>">
	<?endif?>
	<?if(in_array("EMAIL", $arParams["USED_FIELDS"])):?>
			<?if(!empty($arParams["EMAIL_HINT_TITLE"])):?><?=$arParams['EMAIL_HINT_TITLE']?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("EMAIL", $arParams["REQUIRED_FIELDS"])):?><span style="color: red;">*</span><?endif?><?endif?>
		<input style="margin-top: 5px; margin-bottom: 5px; font-size: 19px; height: 50px;" placeholder="<?=$arParams['EMAIL_HINT_TEXT']?>" class="required form-control" type="text" name="user_email" value="<?=$arResult["AUTHOR_EMAIL"]?>">
	<?endif?>
	<?if(in_array("PHONE", $arParams["USED_FIELDS"])):?>
			<?if(!empty($arParams["PHONE_HINT_TITLE"])):?><?=$arParams['PHONE_HINT_TITLE']?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("PHONE", $arParams["REQUIRED_FIELDS"])):?><span style="color: red;">*</span><?endif?><?endif?>
		<input style="margin-top: 5px; margin-bottom: 5px; font-size: 19px; height: 50px;" placeholder="<?=$arParams['PHONE_HINT_TEXT']?>" class="required form-control" type="text" name="user_phone" value="">
	<?endif?>
	</div>
<?endif?>

<?if(in_array("MESSAGE", $arParams["USED_FIELDS"])):?>
	<div class="col-lg-6">
	<?if(!empty($arParams["MESSAGE_HINT_TITLE"])):?><?=$arParams['MESSAGE_HINT_TITLE']?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("MESSAGE", $arParams["REQUIRED_FIELDS"])):?><span style="color: red;">*</span><?endif?><?endif?>
	<textarea class="required form-control" name="MESSAGE" rows="<?=$arParams['MESSAGE_HIDTH']?>" cols="40"><?=$arResult["MESSAGE"]?></textarea>
	</div>
<?endif?>	
</div>

	<br/>



	<?if($arParams["USE_CAPTCHA"] == "Y"):?>
		<div class="col-lg-12">
			<div class="col-lg-6">
				<p style="text-align: center;"><?=GetMessage("MFT_CAPTCHA_CODE")?><span class="mf-req">*</span></p>
				<div class="col-lg-6">
					<p style="text-align: center;"><input type="hidden" name="captcha_sid" value="<?=$arResult["capCode"]?>">
						<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["capCode"]?>" width="158" height="35" alt="CAPTCHA"></p>
				</div>
				<div class="col-lg-6">
					<input class="required form-control" type="text" name="captcha_word" size="30" maxlength="50" value="">
				</div>
			</div>
			<div class="col-lg-6">
			<br/>
				<p style="text-align:  center;">
				<input type="hidden" name="PARAMS_HASH" value="<?=$arResult["PARAMS_HASH"]?>">
				<input class="btn <?=$arParams['PROPERTY_CODE_BUTTON_COLOR']?> btn-lg" type="submit" name="submit" value="<?=$arParams['BUTTON_MESSAGE']?>" style="padding-left: 50px; padding-right: 50px;">
				</p>
			</div>
		</div>
	<?else:?>
		<div class="col-lg-12">
		<br/>
			<p style="text-align:  center;">
			<input type="hidden" name="PARAMS_HASH" value="<?=$arResult["PARAMS_HASH"]?>">
			<input class="btn <?=$arParams['PROPERTY_CODE_BUTTON_COLOR']?> btn-lg" type="submit" name="submit" value="<?=$arParams['BUTTON_MESSAGE']?>" style="padding-left: 50px; padding-right: 50px;">
			</p>
		</div>
	<?endif;?>

</form>

<?if($arParams['USE_IN_COMPONENT']!="Y"):?>
	</div></div>
<?endif?>

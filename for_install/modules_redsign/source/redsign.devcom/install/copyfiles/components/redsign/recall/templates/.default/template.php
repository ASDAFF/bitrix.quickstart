<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();?>

<?if($arResult["LAST_ERROR"]!=""):?>
	<?ShowError( $arResult["LAST_ERROR"] );?>
<?endif;?>

<?if($arResult["GOOD_SEND"]=="Y"):?>
	<span style="color:green;"><?=$arParams["ALFA_MESSAGE_AGREE"]?></span>
<?endif;?>

<form action="<?=$arResult["ACTION_URL"]?>" method="POST">
	<?=bitrix_sessid_post()?>
	<input type="hidden" name="redsignRecall" value="Y" />
	<?=GetMessage("MSG_AUT_NAME")?><span style="color:red">*</span>:<br /><input type="text" name="REDSIGN_AUTHOR" /><br />
	<?=GetMessage("MSG_AUT_COMPANY")?>:<br /><input type="text" name="REDSIGN_COMPANY_NAME" /><br />
	<?=GetMessage("MSG_AUT_EMAIL")?>:<br /><input type="text" name="REDSIGN_AUTHOR_PHONE" /><br />
	<?=GetMessage("MSG_AUT_PHONE")?><span style="color:red">*</span>:<br /><input type="text" name="REDSIGN_AUTHOR_PHONE" /><br />
	<?=GetMessage("MSG_AUT_COMMENT")?>:<br /><textarea name="REDSIGN_AUTHOR_COMMENT"></textarea><br />
	<?if($arParams["ALFA_USE_CAPTCHA"]=="Y"):?>
		<?=GetMessage("MSG_CAPTHA")?><span style="color:red">*</span>:<br />
		<input type="text" name="captcha_word" size="30" maxlength="50" value=""><br />
		<input type="hidden" name="captcha_sid" value="<?=$arResult["CATPCHA_CODE"]?>">
		<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CATPCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA">
		<br />
	<?endif;?>
	<input type="submit" name="submit" value="<?=GetMessage("MSG_SUBMIT")?>">
</form>
<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();?>

<?if($arResult["LAST_ERROR"]!=""):?>
	<?ShowError( $arResult["LAST_ERROR"] );?>
<?endif;?>

<?if($arResult["GOOD_SEND"]=="Y"):?>
	<?ShowMessage( array("MESSAGE"=>$arParams["ALFA_MESSAGE_AGREE"],"TYPE"=>"OK") );?>
<?endif;?>

<form action="<?=$arResult["ACTION_URL"]?>" method="POST">
	<?=bitrix_sessid_post()?>
	<h3><?=GetMessage("MSG_BUY1CLICK")?></h3>
	<input type="hidden" name="<?=$arParams["REQUEST_PARAM_NAME"]?>" value="Y" />
	<?foreach($arResult["FIELDS"] as $arField):?>
		<?if($arField["SHOW"]=="Y"):?>
			<?if($arField["CONTROL_NAME"]!="RS_AUTHOR_ORDER_LIST"):?>
				<?=GetMessage("MSG_".$arField["CONTROL_NAME"])?>: 
				<?if(in_array($arField["CONTROL_NAME"], $arParams["REQUIRED_FIELDS"])):?><span class="redsign_devcom_required">*</span><?endif;?>
				<input type="text" name="<?=$arField["CONTROL_NAME"]?>" value="<?=$arField["HTML_VALUE"]?>" /><br />
			<?else:?>
				<div class="redsign_devcom_dontshow"><textarea name="<?=$arField["CONTROL_NAME"]?>"></textarea></div>
			<?endif;?>
		<?endif;?>
	<?endforeach;?>
	<?if($arParams["ALFA_USE_CAPTCHA"]=="Y"):?>
		<?=GetMessage("MSG_CAPTHA")?><span class="redsign_devcom_required">*</span>:<br />
		<input type="text" name="captcha_word" size="30" maxlength="50" value=""><br />
		<input type="hidden" name="captcha_sid" value="<?=$arResult["CATPCHA_CODE"]?>">
		<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CATPCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA"><br />
	<?endif;?>
	<input type="hidden" name="PARAMS_HASH" value="<?=$arResult["PARAMS_HASH"]?>">
	<input type="submit" name="submit" value="<?=GetMessage("MSG_SUBMIT")?>">
</form>
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
 $this->setFrameMode(true);
?>
<div class="mfeedback">
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

<form action="<?=POST_FORM_ACTION_URI?>" method="POST">
<?=bitrix_sessid_post()?>
	 		 	                    <div class="text-box">
	 		 	                     <label>
									 <input type="text" class="textbox" name="user_name" value="<?=GetMessage("MFT_NAME")?>" onfocus="this.value = '';" onblur="if (this.value == '') {this.value = '<?=GetMessage("MFT_NAME")?>';}">
									</label>
									<label>
									 <input type="text" class="textbox" name="user_email" value="<?=GetMessage("MFT_EMAIL")?>" onfocus="this.value = '';" onblur="if (this.value == '') {this.value = '<?=GetMessage("MFT_EMAIL")?>';}">
									</label>
									<div class="clear"></div>
									</div>
									<textarea  name="MESSAGE" value="<?=GetMessage("MFT_MESSAGE")?>" onfocus="this.value = '';" onblur="if (this.value == '') {this.value = '<?=GetMessage("MFT_MESSAGE")?>';}"><?=$arResult["MESSAGE"]?></textarea>
									<input type="hidden" name="PARAMS_HASH" value="<?=$arResult["PARAMS_HASH"]?>">
	<a href="#contact"><input type="submit" name="submit" value="<?=GetMessage("MFT_SUBMIT")?>"></a>								
									</form>
</div>
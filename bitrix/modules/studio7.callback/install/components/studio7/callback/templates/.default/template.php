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
 //echo"<pre>";print_r($arParams);echo"</pre>";
?>
<?CJSCore::Init(array("jquery"));?>

<!--Заказать звонок-->
<div id="form_wrapper_call">
<pre><?//print_r($_SESSION["CMPT_PARAMS"]);?></pre>
<div id="fhead">
	<h3><?=GetMessage("MFT_HEADER")?></h3>
  <span class="wr_close"><img src="<?=$templateFolder?>/images/cls_btn.png" /></span>
</div>
   <div class="frm_place">
		<form action="<?=$componentPath?>/script/senddata.php" method="POST" id="call_ord">
			<?=bitrix_sessid_post()?>
			<div>
				<input type="text" name="v_name" id="v_name" value="<?=GetMessage("MFT_NAME")?>" maxlength="30" />
				<?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("NAME", $arParams["REQUIRED_FIELDS"])):?><span class="mf-req">*</span><?endif?>
			</div>
			<div>
				<input type="text" name="v_phone" id="v_phone" value="<?=GetMessage("MFT_PHONE")?>" maxlength="20" />
				<?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("PHONE", $arParams["REQUIRED_FIELDS"])):?><span class="mf-req">*</span><?endif?>
			</div>
			<div>
				<input type="text" name="v_time" id="v_time" value="<?=GetMessage("MFT_TIME")?>" maxlength="10" />
				<?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("TIMETOCALL", $arParams["REQUIRED_FIELDS"])):?><span class="mf-req">*</span><?endif?>
			</div>
			<?if($arParams["USE_MESSAGE_FIELD"] == "Y"):?>
			<div>
				<textarea name="v_mess" id="v_mess" maxlength="150"><?=GetMessage("MFT_MESSAGE")?></textarea>
				<?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("MESSAGE", $arParams["REQUIRED_FIELDS"])):?><span class="mf-req">*</span><?endif?>
			</div>
			<?endif;?>
			<?if($arParams["USE_CAPTCHA"] == "Y"):?>
				<div class="mf-captcha">
					<div class="mf-text"><?=GetMessage("MFT_CAPTCHA")?></div>
					<input type="hidden" name="captcha_sid" id="captcha_sid" value="<?=$arResult["capCode"]?>">
					<div class="mf-captcha"><img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["capCode"]?>" width="180" height="40" alt="CAPTCHA"></div>
					<div class="mf-text"><?=GetMessage("MFT_CAPTCHA_CODE")?><span class="mf-req">*</span>
					<input type="text" name="captcha_word" id="captcha_word" size="30" maxlength="10" value="" />
					</div>
				</div>
			<?endif;?>
			<div class="bsubm">
				<input type="submit" name="sord_call" id="sord_call" value="<?=GetMessage("MFT_SUBMIT")?>" />
			</div>
		</form>
   </div>
 <div id="fbott"></div>
</div>
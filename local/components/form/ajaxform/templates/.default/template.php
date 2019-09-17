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
$this->addExternalJS($templateFolder."/lib/inputmask.js");
if($arParams['USE_JQUERY3_2_1']=="Y")
{
	$this->addExternalJS("//ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js");
}
?>

<div class="form-box">
	<div class="title">
		<?echo $arParams['FORM_TITLE'];?>
	</div>
	<div class="form">
		<form id="wapxazAjaxForm" method="POST" onsubmit="ToForm('<?=$arParams["EMAIL_TO"]?>', $('#name_wapxaz_preform').val(), $('#phone_wapxaz_preform').val(), $('#message_wapxaz_preform').val()); return false;">
			<?if($arParams['USE_NAME']=="Y")
			{?>
				<div class="item">
					<input id="name_wapxaz_preform" type="text" name="name" placeholder="<?=GetMessage("WAPXAZ_AJAXFORM_IMA")?>" required>
				</div>
			<?}?>
			<?if($arParams['USE_PHONE']=="Y")
			{?>
				<div class="item">
					<input id="phone_wapxaz_preform"type="text" name="phone" placeholder="<?=GetMessage("WAPXAZ_AJAXFORM_TELEFON")?>" class="wapxazAjaxFormPhone" required>
				</div>
			<?}?>
			<?if($arParams['USE_MESSAGE']=="Y")
			{?>
				<div class="item">
					<textarea id="message_wapxaz_preform" name="text" placeholder="<?=GetMessage("WAPXAZ_AJAXFORM_VASE_SOOBSENIE")?>" required></textarea>
				</div>
			<?}?>
			<?if($arParams['USE_RULE']=="Y")
			{?>
				<div class="item">
					<input id="rule_wapxaz_preform" type="checkbox" name="rule" checked />
					<span><?=GetMessage("WAPXAZ_AJAXFORM_A_SOGLASEN_NA_OBRABO")?><br><a href="<?echo $arParams['URL_RULES'];?>" class="rule_personal"><?=GetMessage("WAPXAZ_AJAXFORM_PERSONALQNYH_DANNYH")?></a></span>
				</div>
			<?}else{?>
				<input id="rule_wapxaz_preform" type="checkbox" name="rule" style="display:none;" checked />
			<?}?>
			<?if($arParams['USE_CAPTCHA']=="Y")
			{?>
				<input id="email_wapxaz_preform" type="email" class="white" placeholder="E-mail *" name="email" value="" style="position: absolute; left: -10000000000000px; z-index: -10000000000000;">
			<?}?>
			<div class="bt">
				<button type="submit" onclick="return formPreSubmit()"><?echo $arParams['FORM_BTN_SUBMIT'];?></button>
			</div>
		</form>
	</div>
</div>

<div id="modalThnks" class="modal_div">
	<span class="modal_close">X</span>
         <?echo $arParams['OK_TEXT'];?>
</div>
<div id="overlay"></div>
<a href="#modalThnks" class="open_modal"></a>

<script>
BX.message({
	TEMPLATE_PATH: '<? echo $this->GetFolder(); ?>'
});
</script>
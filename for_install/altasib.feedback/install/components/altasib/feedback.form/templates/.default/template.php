<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?
//p($arParams);
$fVerComposite = (defined("SM_VERSION") && version_compare(SM_VERSION, "14.5.0") >= 0 ? true : false); 
if ($fVerComposite) $this->setFrameMode(true); 
$ALX = "FID" . $arParams["FORM_ID"];

 if (!isset($arResult["POST"]))
{
	if ((!isset($arParams['ALX_GET_POPUP'.$ALX]) || $arParams['ALX_GET_POPUP'.$ALX]=='Y') && $arParams['ALX_LINK_POPUP'] == 'Y')
	{
		if (!((substr($arParams["ALX_NAME_LINK"], 0, 1) == '#') || (substr($arParams["ALX_NAME_LINK"], 0, 1) == '.'))):?>
		<span class="alx_feedback_popup" id="form_id_<?=$ALX?>"><?=$arParams["ALX_NAME_LINK"];?></span>
		<?endif;?>
		
<?		$this->addExternalJS($this->__folder."/form_script.js");?>

		<script type="text/javascript">
			if(typeof ALXpopup_<?=$ALX?>=='undefined'&&typeof ALXpopup!='undefined'&&typeof BX!='undefined')
				var ALXpopup_<?=$ALX?>=BX.clone(ALXpopup);

			$(document).ready(function(){
				var param = {
					'width': "<?=$arParams['WIDTH_FORM']?>",
					'url': '',
					'data': {"AJAX_CALL": "Y", "OPEN_POPUP": "<?=$ALX?>"},
					'cssURL': ["<?=CUtil::GetAdditionalFileURL($this->GetFolder()."/form_style.css");?>",
						<?if ($arParams['INPUT_APPEARENCE'] != 'DEFAULT' && !is_array($arParams['INPUT_APPEARENCE'])):?>
							"<?=$this->__folder?>/themes/theme.add_<?=strtolower($arParams['INPUT_APPEARENCE'])?>.css",
						<?elseif (is_array($arParams['INPUT_APPEARENCE'])):?>
							<?foreach($arParams['INPUT_APPEARENCE'] as $param):?>
								<?if ($param != 'DEFAULT'):?>
									"<?=$this->__folder?>/themes/theme.add_<?=strtolower($param)?>.css",
								<?endif?>
							<?endforeach?>
						<?endif?>
						<?if ($arParams['ALX_RESET_THEME'] === 'Y'):?>
							"<?=CUtil::GetAdditionalFileURL($this->__folder."/themes/default.css");?>"
						<?else:?>
							"<?=CUtil::GetAdditionalFileURL($this->__folder."/themes/theme_".md5($arParams['COLOR_THEME'].'_'.$arParams['COLOR_OTHER'].'_'.$arParams['COLOR_SCHEME'].'_'.$ALX).".css");?>"
						<?endif?>
					],
					'objClick': <?if (!((substr($arParams["ALX_NAME_LINK"], 0, 1) == '#') || (substr($arParams["ALX_NAME_LINK"], 0, 1) == '.'))):?>
								'#form_id_<?=$ALX?>.alx_feedback_popup',
								<?else:?>
								'<?=$arParams["ALX_NAME_LINK"]?>',
								<?endif;?>
					'popupAnimation': [
						"alx-popup-show-anime<?=$arParams['POPUP_ANIMATION']?>",
						"alx-popup-hide-anime<?=$arParams['POPUP_ANIMATION']?>",
						"alx-popup-mess-show-anime<?=$arParams['POPUP_ANIMATION']?>"],
					'openDelay': '<?=intval($arParams["POPUP_DELAY"])?>'
				};
				if(typeof ALXpopup_<?=$ALX?>!='undefined')
					ALXpopup_<?=$ALX?>.init(param);
				else
					ALXpopup.init(param);
			});

<?			if($arParams['ALX_LOAD_PAGE']=='Y' && $APPLICATION->get_cookie("ALTASIB_FDB_SEND_".$ALX) != 'Y'):?>
			$(window).load(function(){
				if(typeof ALXpopup_<?=$ALX?>!='undefined'){
					if(typeof ALXpopup_<?=$ALX?>.param.openDelay!='undefined')
						setTimeout(function(){
							ALXpopup_<?=$ALX?>.show();
						},ALXpopup_<?=$ALX?>.param.openDelay);
					else
						ALXpopup_<?=$ALX?>.show();
				}else{
					if(typeof ALXpopup.param.openDelay!='undefined')
						setTimeout(function(){
							ALXpopup.show();
						},ALXpopup.param.openDelay);
					else
						ALXpopup.show();
				}
			});
<?			endif;?>
		</script>

<?	} else { ?>
<?		if ($arParams['ALX_LINK_POPUP'] !== 'Y'):?>

		<script type="text/javascript">
			if(typeof ALXpopup_<?=$ALX?>=='undefined'&&typeof ALXpopup!='undefined'&&typeof BX!='undefined')
				var ALXpopup_<?=$ALX?>=BX.clone(ALXpopup);

			$(document).ready(function(){
				var param = {
					'popupWindow':"N"
				};

				if(typeof ALXpopup_<?=$ALX?>!='undefined')
					ALXpopup_<?=$ALX?>.init(param);
				else
					ALXpopup.init(param);
			});
		</script>
		<div id="afbf_err_<?=$ALX?>" class="alx-feedb-error"></div>
<?		endif?>
<?
		$this->addExternalJs($this->GetFolder() . "/form_script.js");
		$this->addExternalCss(CUtil::GetAdditionalFileURL($this->GetFolder()."/form_style.css"));
		if ($arParams['INPUT_APPEARENCE'] != 'DEFAULT' && !is_array($arParams['INPUT_APPEARENCE'])):
			$this->addExternalCss($this->GetFolder() . "/themes/theme.add_" . strtolower($arParams['INPUT_APPEARENCE']) . ".css");
		elseif (is_array($arParams['INPUT_APPEARENCE'])):
			foreach ($arParams['INPUT_APPEARENCE'] as $param):
				if ($param != 'DEFAULT'):
					$this->addExternalCss($this->GetFolder() . "/themes/theme.add_" . strtolower($param) . ".css");
				endif;
			endforeach;
		endif;
		if ($arParams['ALX_RESET_THEME'] === 'Y'):
			$this->addExternalCss(CUtil::GetAdditionalFileURL($this->GetFolder()."/themes/default.css"));
		else:
			$this->addExternalCss(CUtil::GetAdditionalFileURL($this->GetFolder()."/themes/theme_".md5($arParams['COLOR_THEME'].'_'.$arParams['COLOR_OTHER'].'_'.$arParams['COLOR_SCHEME'].'_'.$ALX).".css"));
		endif;
?>
<? if($arParams["SECTION_FIELDS_ENABLE"] == "Y" && $_POST["REFRESH"]=="Y" && $_SERVER["REQUEST_METHOD"]=="POST"): ?>
<!--REFRESH_SECTION-->
<? endif; ?>
<?
		if(is_array($arParams["PROPERTY_FIELDS"]) &&
			is_array($arParams["MASKED_INPUT_PHONE"]) &&
			!empty($arParams["MASKED_INPUT_PHONE"])){
?>
		<script type="text/javascript">
			$(function($){
				if(typeof $.mask!='undefined'){
<?			foreach($arParams["MASKED_INPUT_PHONE"] as $propCode):
				if(in_array($propCode, $arParams["PROPERTY_FIELDS"])){?>
				$('input[name="FIELDS[<?=$propCode?>_<?=$ALX?>]"]').mask("9 (999) 999-99-99", {placeholder: '_'});
<?
				}
			endforeach;?>
				}else if(typeof console.warn!='undefined'){console.warn('Conflict when accessing the jQuery Mask Input Plugin: %s typeof $.mask',typeof $.mask);}
			});
		</script>
<?		}?>

<?		require_once("script.php"); // include js ?>

<?		if (!is_array($arParams['INPUT_APPEARENCE'])): ?>
			<div class="afbf alx_feed_back <? $arParams['INPUT_APPEARENCE'] != 'DEFAULT' ? strtolower($arParams['INPUT_APPEARENCE']) : '' ?>" id="alx_feed_back_<?=$ALX ?>">
<?		else: ?>
			<div class="afbf alx_feed_back <? foreach ($arParams['INPUT_APPEARENCE'] as $param) if ($param != 'DEFAULT') echo strtolower($param) . ' ' ?>" id="alx_feed_back_<?=$ALX ?>">
<?		endif ?>
<?
?>
<?		require("form.php"); // include form ?>
		</div>

		<script type="text/javascript">
			$(document).ready(function(){
				var file_w_<?=$ALX?> = parseInt($("#alx_feed_back_<?=$ALX?> .afbf_feedback_poles").width() / 5);

				function str_replace_<?=$ALX?>(search, replace, subject){
					return subject.split(search).join(replace);
				}
<?				for($i = 1;$i <= $k;$i++):?>
				$("#alx_feed_back_<?=$ALX?> #afbf_file_input_add<?=$i?>")
					.attr('size', file_w_<?=$ALX?>)
					.change(function(){
						var input_<?=$ALX?>_<?=$i?> = $(this)[0];
						if(typeof input_<?=$ALX?>_<?=$i?>.files!='undefined' && input_<?=$ALX?>_<?=$i?>.files!=null)
							var len = input_<?=$ALX?>_<?=$i?>.files.length;
						if (typeof len != 'undefined' && len > 1){
							var myStr_<?=$ALX?>_<?=$i?> = '';
							for (var x = 0; x < len; x++) {
								if (typeof input_<?=$ALX?>_<?=$i?>.files[x].name != 'undefined') {
									myStr_<?=$ALX?>_<?=$i?> += input_<?=$ALX?>_<?=$i?>.files[x].name;
									if (x + 1 != len)
										myStr_<?=$ALX?>_<?=$i?> += ", ";
								}
							}
						} else {
							var myStr_<?=$ALX?>_<?=$i?> = str_replace_<?=$ALX?>("C:\\fakepath\\", "", $(this).val());
							textInput = $(this).siblings('.afbf_input_group').children('.afbf_inputtext');
							textInput.val(myStr_<?=$ALX?>_<?=$i?>);
						}
					});
<?				endfor;?>
			});
		</script>
<?	if(!empty($arParams["WIDTH_FORM"]) && $arParams['ALX_LINK_POPUP'] != 'Y'): ?>
<style type="text/css">
#alx_feed_back_<?=$ALX?>.alx_feed_back,
#afbf_err_<?=$ALX?>.alx-feedb-error{
	width:<?=$arParams["WIDTH_FORM"];?>;
}
</style>
<?		endif; ?>
<?	} ?>
<?
} else { ?>

	<? if (count($arResult["FORM_ERRORS"]) == 0 && ($arResult["success_" . $ALX] == "yes" || $_REQUEST["success_" . $ALX] == "yes")): ?>
		<div class="afbf_success_block<?if ($arParams['ALX_LINK_POPUP'] !== 'Y'):?> _without-popup<?endif;?>">
			<div class="afbf_mess_ok">
				<div class="afbf_ok_icon"></div>
				<div class="mess"><?=$arParams["MESSAGE_OK"]; ?></div>
			</div>
		</div>
		<?if ($arParams['ALX_LINK_POPUP'] == 'Y'):?>
			<div class="afbf_close_container">
				<button class="modal_close_ok">OK</button>
			</div>
		<?endif?>
		<?if ($arParams['SHOW_LINK_TO_SEND_MORE']=='Y'):?>
			<div class="afbf_send_another_message">
				<a href="<?=$APPLICATION->GetCurUri()?>"><?=$arParams['LINK_SEND_MORE_TEXT']?></a>
			</div>
		<?endif;?>
		<script type="text/javascript">
			var param = {'width':'350','filledWithErrors':'N','fid':'<?=$ALX?>'}
			if(typeof ALXpopup_<?=$ALX?>=='undefined'&&typeof ALXpopup!='undefined'&&typeof BX!='undefined')
				var ALXpopup_<?=$ALX?>=BX.clone(ALXpopup);

			if(typeof ALXpopup_<?=$ALX?>!='undefined')
				ALXpopup_<?=$ALX?>.ok_window(param);
			else
				ALXpopup.ok_window(param);
		</script>
	<? elseif ($arParams["CHECK_ERROR"] == "Y" && count($arResult["FORM_ERRORS"]) > 0): ?>
		<? if($arParams["USE_CAPTCHA"]):?>
			<script type="text/javascript">
				<?if($arParams["CAPTCHA_TYPE"] != 'recaptcha'):?>
					<?if($arParams["CHANGE_CAPTCHA"] == "Y"):?>
					<?/**/?>	ALX_ChangeCaptcha('<?=$ALX?>');<?/**/?>
					<?else:?>
						ALX_ReloadCaptcha('<?=$_SESSION['ALX_CAPTHA_CODE']?>','<?=$ALX?>');
					<?endif;?>
				<?else:?>
					grecaptcha.reset();
				<?endif;?>
			</script>
		<? endif?>
		<? if ($arParams['ALX_LINK_POPUP'] !== 'Y'):?>
			<script type="text/javascript">
				if(typeof ALXpopup_<?=$ALX?>=='undefined'&&typeof ALXpopup!='undefined'&&typeof BX!='undefined')
					var ALXpopup_<?=$ALX?>=BX.clone(ALXpopup);

				$(document).ready(function(){
					var param = {
						'popupWindow':"N",
						'filledWithErrors':'Y'
					};

					if(typeof ALXpopup_<?=$ALX?>!='undefined')
						ALXpopup_<?=$ALX?>.init(param);
					else
						ALXpopup.init(param);
				});
			</script>
		<? endif;?>
		<div class="afbf_error_block">
			<div class="afbf_error_icon"></div>
			<div class="afbf_error_text"><?=GetMessage('ALX_FILL_INPUTS_MSG');?></div>
		</div>
	<? endif;?>
	<?
	//p($arResult["FORM_ERRORS"]);
//die();
	?>
	<script type="text/javascript">
		validateForm($('.alx-feedb-data, #alx_feed_back_<?=$ALX?>.alx_feed_back').find('form'));
		<?if (strlen($arResult["FORM_ERRORS"]["CAPTCHA_WORD"]["ALX_CP_WRONG_CAPTCHA"])>0):?>
		ALX_captcha_Error();
		<?endif?>

		<?if (!empty($arResult["FORM_ERRORS"]["ERROR_FIELD"])):
		
			if(isset($arResult["FORM_ERRORS"]["EMPTY_FIELD"]["alx_fb_agreement"]))
			{
				?>ALX_fileError($('#alx_feed_back_<?=$ALX?> #alx_fb_agreement'));<?
			}
			foreach($arResult["FIELDS"] as $k=>$v)
			{
				if($v["TYPE"] == "F" && !empty($arResult["FORM_ERRORS"]["EMPTY_FIELD"][$v["CODE"]]))
				{
					?>ALX_fileError($('#alx_feed_back_<?=$ALX?> #afbf_<?=mb_strtolower($v["CODE"])?>'));<?
				}
			}
		?>
		<?endif?>
<?		if($arParams['LOCAL_REDIRECT_ENABLE'] == 'Y' && strlen($arParams['LOCAL_REDIRECT_URL']) > 0
			&& ($arResult["success_" . $ALX] == "yes" || $_REQUEST["success_" . $ALX] == "yes")
		):?>
		function AltasibFeedbackRedirect_<?=$ALX?>(){
			document.location.href = '<?=(trim(htmlspecialcharsEx($arParams['LOCAL_REDIRECT_URL'])));?>';
		}
		AltasibFeedbackRedirect_<?=$ALX?>();
<?		endif?>
	</script>
<?}?>
<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

if($arResult["LAST_ERROR"]!='') {
	?><div class="alert alert-danger" role="alert"><?=$arResult['LAST_ERROR']?></div><?
}
if($arResult['GOOD_SEND']=='Y') {
	?><div class="alert alert-success" role="alert"><?=$arResult['MESSAGE_AGREE']?></div><?
	?><script>$(document).trigger("closeFancy");</script><?
}

?><div class="vacanciesForm overflower mainform" style="display: none;"><a name="vacancyForm"></a><?
	?><div class="row"><?
		?><div class="col col-md-12 form"><?
			if ($arParams['FORM_TITLE'] !='') {
				?><div class="fancybox-title-inside-wrap"><?=$arParams['FORM_TITLE']?></div><?
			}
			if ( $arParams['FORM_DESCRIPTION']!='' ) {
				?><span><?=$arParams['FORM_DESCRIPTION']?></span><?
			}
			?><form action="<?=$arResult["ACTION_URL"]?>" method="POST"><?
				?><div class="separator webform clearfix"><?
					?><?=bitrix_sessid_post()?><?
					?><input type="hidden" name="<?=$arParams["REQUEST_PARAM_NAME"]?>" value="Y" /><?
					$rowCounter = 0;
					foreach ($arResult['FIELDS'] as $key => $arField) {
						if ($arField['EXT']!="Y") {
							if ($arField['SHOW']=="Y") {
								if ($arField['CONTROL_NAME']=='RS_TEXTAREA') {
									?><div class="row"><?
										?><div class="col col-md-12 field"><?
											?><span class="label-wrap"><?
												if (isset($arParams["INPUT_NAME_".$arField["CONTROL_NAME"]]) && $arParams["INPUT_NAME_".$arField["CONTROL_NAME"]]!="") {
													?><?=$arParams["INPUT_NAME_".$arField["CONTROL_NAME"]]?>:<?
												} else {
													?><?=GetMessage("MSG_".$arField["CONTROL_NAME"])?>: <?
												}
												if(in_array($arField["CONTROL_NAME"], $arParams["REQUIRED_FIELDS"])) {
													?><span class="required"> *</span><?
												}
											?></span><?
											?><textarea name="<?=$arField["CONTROL_NAME"]?>" class="form-item form-control"><?=$arField["HTML_VALUE"]?></textarea><?
										?></div><?
									?></div><?
								} else {
									if ($rowCounter%2==0) {
										?><div class="row"><?
									}
									?><div class="col col-md-6 field <?if(in_array($arField["CONTROL_NAME"], $arParams["REQUIRED_FIELDS"])){?>req<?}?>"><?
										?><span class="label-wrap"><?
											if (isset($arParams["INPUT_NAME_".$arField["CONTROL_NAME"]]) && $arParams["INPUT_NAME_".$arField["CONTROL_NAME"]]!="") {
												?><?=$arParams["INPUT_NAME_".$arField["CONTROL_NAME"]]?>:<?
											} else {
												?><?=GetMessage("MSG_".$arField["CONTROL_NAME"])?>: <?
											}
											if(in_array($arField["CONTROL_NAME"], $arParams["REQUIRED_FIELDS"])) {
												?><span class="required"> *</span><?
											}
										?></span><?
										?><input type="text" value="<?=$arField["HTML_VALUE"]?>" name="<?=$arField["CONTROL_NAME"]?>" class="<?if(in_array($arField["CONTROL_NAME"], $arParams["REQUIRED_FIELDS"])){?>req-input<?}?> form-item form-control"><?
									?></div><?
									if ($rowCounter%2!=0) {
										?></div><?
									}
									$rowCounter++;
								}
							}
						} else {
							if ($rowCounter%2==0) {
								?><div class="row"><?
							}
							?><div class="col col-md-6 field req"><?
								?><span class="label-wrap"><?
									?><?=$arParams['RS_MONOPOLY_FIELD_'.$arField['INDEX'].'_NAME']?><span class="required"> *</span><?
								?></span><?
								?><input type="text" <?
									?>value="<?=$arField["HTML_VALUE"]?>" <?
									?>name="<?=$arField["CONTROL_NAME"]?>" <?
									?>class="form-control form-item" <?
									?>readonly <?
								?>><?
							?></div><?
							if ($rowCounter%2!=0) {
								?></div><?
							}
							$rowCounter++;
						}
					}
				?></div><?
				?><div class="row"><?
					if($arParams["USE_CAPTCHA"] == "Y") {
						?><div class="col col-md-6 form-group field-wrap req"><?
							?><div class="captcha_wrap"><?
								?><label for="captcha_<?=$arResult['WEB_FORM_NAME']?>" class="col col-md-12"><?=GetMessage("MSG_CAPTHA")?>: <span class="required"><?=$arResult['REQUIRED_SIGN']?></span><br /></label><?
								?><div class="row"><?
									?><div class="col col-md-6"><img class="captchaImg" src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CATPCHA_CODE"]?>" alt="CAPTCHA"></div><br /><?
									?><div class="col col-md-6"><input class="form-control req-input" id="captcha_<?=$arResult['WEB_FORM_NAME']?>" type="text" name="captcha_word" size="30" maxlength="50" value=""></div><?
									?><input type="hidden" class="captchaSid" name="captcha_sid" value="<?=$arResult["CATPCHA_CODE"]?>"><?
								?></div><?
							?></div><?
						?></div><?
					}
					?><div class="col col-md-<?if($arParams["USE_CAPTCHA"]=="Y"):?>6<?else:?>12<?endif;?> buttons"><?
						?><span class="form__text"><?=GetMessage('MSG_REQUIRED_FIELDS')?></span><?
						?><input type="hidden" name="PARAMS_HASH" value="<?=$arResult["PARAMS_HASH"]?>"><?
						?><input type="submit" class="btn btn-primary btn-group-lg btn2 webform-button" name="submit" value="<?=GetMessage("MSG_SUBMIT")?>"><?
					?></div><?
				?></form><?
			?></div><?
		?></div><?
	?></div><?
?></div><?


?><script>$.fancybox.toggle();</script>

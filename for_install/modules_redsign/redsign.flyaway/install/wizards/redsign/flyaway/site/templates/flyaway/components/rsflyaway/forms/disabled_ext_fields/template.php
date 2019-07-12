<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

if($arResult["LAST_ERROR"]!='') {
	?><div class="alert alert-danger" role="alert"><?=$arResult['LAST_ERROR']?></div><?
}

if($arResult['GOOD_SEND']=='Y') {
	?><div class="alert alert-success" role="alert"><?=$arResult['MESSAGE_AGREE']?></div><?
	?><script>$(document).trigger("closeFancy");</script><?
}

?><div class="overflower"><?
	?><div class="row"><?
		?><div class="col col-md-12 form"><?
			?><form action="<?=$arResult["ACTION_URL"]?>" method="POST" class="mainform"><?
				?><div class="webform clearfix"><?
					?><div class="row"><?
						?><?=bitrix_sessid_post()?><?
						?><input type="hidden" name="<?=$arParams["REQUEST_PARAM_NAME"]?>" value="Y" /><?
						foreach ($arResult['FIELDS'] as $key => $arField) {
							if ($arField['EXT']!="Y") {
								if ($arField['SHOW']=="Y") {
									if ($arField['CONTROL_NAME']=='RS_TEXTAREA') {
										?><div class="col col-md-12 form"><?
											?><span class="label-wrap"><?
												if (isset($arParams["INPUT_NAME_".$arField["CONTROL_NAME"]]) && $arParams["INPUT_NAME_".$arField["CONTROL_NAME"]]!="") {
													?><?=$arParams["INPUT_NAME_".$arField["CONTROL_NAME"]]?>:<?
												} else {
													?><?=getMessage("MSG_".$arField["CONTROL_NAME"])?>: <?
												}
												if(in_array($arField["CONTROL_NAME"], $arParams["REQUIRED_FIELDS"])) {
													?><span class="required"> *</span><?
												}
											?></span><?
											?><textarea name="<?=$arField["CONTROL_NAME"]?>" class="form-item form-control"><?=$arField["HTML_VALUE"]?></textarea><?
										?></div><?
									} else {
										?><div class="col col-md-12 field <?if(in_array($arField["CONTROL_NAME"], $arParams["REQUIRED_FIELDS"])){?>req<?}?>"><?
											?><span class="label-wrap"><?
												if (isset($arParams["INPUT_NAME_".$arField["CONTROL_NAME"]]) && $arParams["INPUT_NAME_".$arField["CONTROL_NAME"]]!="") {
													?><?=$arParams["INPUT_NAME_".$arField["CONTROL_NAME"]]?>:<?
												} else {
													?><?=getMessage("MSG_".$arField["CONTROL_NAME"])?>: <?
												}
												if(in_array($arField["CONTROL_NAME"], $arParams["REQUIRED_FIELDS"])) {
													?><span class="required"> *</span><?
												}
											?></span><?
											?><input type="text" value="<?=$arField["HTML_VALUE"]?>" name="<?=$arField["CONTROL_NAME"]?>" class="<?if(in_array($arField["CONTROL_NAME"], $arParams["REQUIRED_FIELDS"])){?>req-input<?}?> form-item form-control"><?
										?></div><?
									}
								}
							} else {
								?><div class="col col-md-12 field"><?
									?><span class="label-wrap"><?
										?><?=$arParams['RS_FLYAWAY_FIELD_'.$arField['INDEX'].'_NAME']?><?
									?></span><?
									?><input type="text" <?
										?>value="<?=$arField["HTML_VALUE"]?>" <?
										?>name="<?=$arField["CONTROL_NAME"]?>" <?
										?>class="form-item form-control" <?
										?>readonly <?
										?>><?
								?></div><?
							}
						}
					?></div><?
					?></div><?
					?><div class="row"><?
						if($arParams["USE_CAPTCHA"] == "Y") {
							?><div class="inner-wrap-capcha col col-md-12 form-group field-wrap req"><?
									
								?><div class="row"><?
									?><div class="col col-md-6"><img class="captchaImg" src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CATPCHA_CODE"]?>" alt="CAPTCHA"></div><br /><?
									?><div class="col col-md-6 form"><input class="form-control req-input form-item" id="captcha_<?=$arResult['WEB_FORM_NAME']?>" type="text" name="captcha_word" size="30" maxlength="50" value=""></div><?
									?><input type="hidden" class="captchaSid" name="captcha_sid" value="<?=$arResult["CATPCHA_CODE"]?>"><?
								?></div><?
							?></div><?
						}
						?><div class="col col-md-12 buttons"><?
							?><span class="form__text"><?=getMessage('MSG_REQUIRED_FIELDS')?></span><?
							?><input type="hidden" name="PARAMS_HASH" value="<?=$arResult["PARAMS_HASH"]?>"><?
							?><input type="submit" class="btn btn-primary btn-group-lg btn-default btn2 webform-button" name="submit" value="<?=getMessage("MSG_SUBMIT")?>"><?
						?></div><?
					?></div><?
			?></form><?
		?></div><?
	?></div><?
?></div><?

?><script>$.fancybox.toggle();</script>
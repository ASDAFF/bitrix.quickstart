<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

use \Bitrix\Main\Localization\Loc;

if ($arResult["LAST_ERROR"] != '') {
	?><div class="alert alert-danger" role="alert"><?=$arResult['LAST_ERROR']?></div><?
}

if ($arResult['GOOD_SEND'] == 'Y') {
	?><div class="alert alert-success" role="alert"><?=$arResult['MESSAGE_AGREE']?></div><?
	?><script>$(document).trigger("closeFancy");</script><?
}

?><div class="mainform"><?
	?><div class="row"><?
		?><div class="col col-md-12"><?
			?><div class="form"><?
				if ($arParams['FORM_TITLE'] != ''):
					?><h3 class="form__title form__title_simple"><?=$arParams['FORM_TITLE']?></h3><?
				endif;
				
				if ($arParams['FORM_DESCRIPTION'] != ''):
					?><span><?=$arParams['FORM_DESCRIPTION']?></span><?
				endif;
				
				?><form action="<?=$arResult["ACTION_URL"]?>" method="POST"><?
					?><div class="separator webform"><?=Loc::getMessage("MSG_HELP")?></div><?
					?><div class="separator webform clearfix"><?
						?><?=bitrix_sessid_post()?><?
						?><input type="hidden" name="<?=$arParams["REQUEST_PARAM_NAME"]?>" value="Y" /><?
						$rowCounter = 0;
							foreach ($arResult['FIELDS'] as $key => $arField):
								if ($arField['EXT'] != "Y"):
									if ($arField['SHOW'] == "Y"):
										if ($arField['CONTROL_NAME'] == 'RS_TEXTAREA'):
											if ($rowCounter%2 != 0): 
												?></div><?
											endif;
											?><div class="row"><?
												?><div class="col col-md-12 field"><?
													?><span class="label-wrap"><?
														if (isset($arParams["INPUT_NAME_".$arField["CONTROL_NAME"]]) && $arParams["INPUT_NAME_".$arField["CONTROL_NAME"]]!=""):
															?><?=$arParams["INPUT_NAME_".$arField["CONTROL_NAME"]]?>:<?
														else:
															?><?=Loc::getMessage("MSG_".$arField["CONTROL_NAME"])?>: <?
														endif;
														
														if (in_array($arField["CONTROL_NAME"], $arParams["REQUIRED_FIELDS"])):
															?><span class="required"> *</span><?
														endif;
													?></span><?
													?><textarea name="<?=$arField["CONTROL_NAME"]?>" class="form-item form-item_area form-control"><?=$arField["HTML_VALUE"]?></textarea><?
												?></div><?
											?></div><?
										else:
											if ($rowCounter%2 == 0): 
												?><div class="row"><?
											endif; 
											?><div class="col col-md-6 field <?if(in_array($arField["CONTROL_NAME"], $arParams["REQUIRED_FIELDS"])){?>req<?}?>"><?
												?><span class="label-wrap"><?
													if (isset($arParams["INPUT_NAME_".$arField["CONTROL_NAME"]]) && $arParams["INPUT_NAME_".$arField["CONTROL_NAME"]]!=""):
														?><?=$arParams["INPUT_NAME_".$arField["CONTROL_NAME"]]?>:<?
													else:
														?><?=Loc::getMessage("MSG_".$arField["CONTROL_NAME"])?>: <?
													endif;
													
													if (in_array($arField["CONTROL_NAME"], $arParams["REQUIRED_FIELDS"])):
														?><span class="required"> *</span><?
													endif;
												?></span><?
												?><input type="text" value="<?=$arField["HTML_VALUE"]?>" name="<?=$arField["CONTROL_NAME"]?>" class="<?if(in_array($arField["CONTROL_NAME"], $arParams["REQUIRED_FIELDS"])){?>req-input<?}?> form-item form-control"><?
											?></div><?
											if ($rowCounter%2 != 0): 
												?></div><?
											endif;
											
											$rowCounter++;
										endif;
									endif;
								else:
									if ($rowCounter%2 == 0):
										?><div class="row"><?
									endif;
									?><div class="col col-md-6 field req"><?
										?><span class="label-wrap"><?
											?><?=$arParams['RS_FLYAWAY_FIELD_'.$arField['INDEX'].'_NAME']?><span class="required"> *</span><?
										?></span><?
										?><input type="text" <?
											?>value="<?=$arField["HTML_VALUE"]?>" <?
											?>name="<?=$arField["CONTROL_NAME"]?>" <?
											?>class="form-item form-control" <?
											?>readonly <?
											?>><?
									?></div><?
									if ($rowCounter%2 != 0): 
										?></div><?
									endif;
									
									$rowCounter++;
								endif;
							endforeach;
					?></div><?
					
					?><div class="row"><?
							if ($arParams["USE_CAPTCHA"] == "Y"):
								?><div class="inner-wrap-capcha col col-sm-7 form-group req"><?
									?><div class="row"><?
										?><div class="col col-md-6 form-capcha__img"><img class="captchaImg" src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CATPCHA_CODE"]?>" alt="CAPTCHA"></div><?
										?><div class="col col-md-6"><input class="form-control form-item req-input" id="captcha_<?=$arResult['WEB_FORM_NAME']?>" type="text" name="captcha_word" size="30" maxlength="50" value=""></div><?
										?><input type="hidden" class="captchaSid" name="captcha_sid" value="<?=$arResult["CATPCHA_CODE"]?>"><?
									?></div><?
								?></div><?
							endif;
							
							?><div class="col col-sm-<?if ($arParams["USE_CAPTCHA"]=="Y"):?>5<?else:?>12<?endif;?> text-right"><?
								?><div class="form-comment"><?=Loc::getMessage('MSG_REQUIRED_FIELDS')?></div><?
								?><input type="hidden" name="PARAMS_HASH" value="<?=$arResult["PARAMS_HASH"]?>"><?
								?><input type="submit" class="btn btn-primary btn2 btn-group-lg" name="submit" value="<?=Loc::getMessage("MSG_SUBMIT")?>"><?
							?></div><?
					?></div><?
				?></form><?
			?></div><?
		?></div><?
	?></div><?
?></div><?

<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(empty($arResult['ERROR_MESSAGE']) && !empty($arResult['OK_MESSAGE']))
{
	echo ShowNote($arResult['OK_MESSAGE']);
}

?><div class="reviewform noned"><?
	?><form action="<?=POST_FORM_ACTION_URI?>#postform" name="<?=$arParams['FORM_ID']?>" id="<?=$arParams['FORM_ID']?>" method="POST" enctype="multipart/form-data" onsubmit="return RSGoPro_SummComment(this);"><?
		$frame = $this->createFrame()->begin('');
		
		?><input type="hidden" name="index" value="<?=htmlspecialcharsbx($arParams['form_index'])?>" /><?
		?><input type="hidden" name="back_page" value="<?=$arResult['CURRENT_PAGE']?>" /><?
		?><input type="hidden" name="ELEMENT_ID" value="<?=$arParams['ELEMENT_ID']?>" /><?
		?><input type="hidden" name="SECTION_ID" value="<?=$arResult['ELEMENT_REAL']['IBLOCK_SECTION_ID']?>" /><?
		?><input type="hidden" name="save_product_review" value="Y" /><?
		?><input type="hidden" name="preview_comment" value="N" /><?
		?><?=bitrix_sessid_post()?><?
		
		?><div class="rating clearfix"><?
			?><input type="hidden" name="REVIEW_TEXT_rate" value="<?=(isset($arResult['REVIEW_TEXT_EXT']['RATING']) ? $arResult['REVIEW_TEXT_EXT']['RATING'] : '0')?>" /><?
			?><span><?=GetMessage('POST_MSG_TEXT_RATE_PROD')?>: </span><?
			?><a class="icon pngicons<?if(IntVal($arResult['REVIEW_TEXT_EXT']['RATING'])>0):?> selected<?endif;?>" href="#" data-id="1"></a><?
			?><a class="icon pngicons<?if(IntVal($arResult['REVIEW_TEXT_EXT']['RATING'])>1):?> selected<?endif;?>" href="#" data-id="2"></a><?
			?><a class="icon pngicons<?if(IntVal($arResult['REVIEW_TEXT_EXT']['RATING'])>2):?> selected<?endif;?>" href="#" data-id="3"></a><?
			?><a class="icon pngicons<?if(IntVal($arResult['REVIEW_TEXT_EXT']['RATING'])>3):?> selected<?endif;?>" href="#" data-id="4"></a><?
			?><a class="icon pngicons<?if(IntVal($arResult['REVIEW_TEXT_EXT']['RATING'])>4):?> selected<?endif;?>" href="#" data-id="5"></a><?
		?></div><?
		
		if(!$arResult['IS_AUTHORIZED'])
		{
			?><div class="fieldname"><?=GetMessage('OPINIONS_NAME')?></div><?
			?><input type="text" name="REVIEW_AUTHOR" id="REVIEW_AUTHOR<?=$arParams['form_index']?>" value="<?=$arResult['REVIEW_AUTHOR']?>" /><?
		}
		
		?><div class="fieldname"><?=GetMessage('POST_MSG_TEXT_PLUS')?></div><?
		?><textarea name="REVIEW_TEXT_plus"><?=(isset($arResult['REVIEW_TEXT_EXT']['PLUS']) ? $arResult['REVIEW_TEXT_EXT']['PLUS'] : '')?></textarea><br /><?
		?><div class="fieldname"><?=GetMessage('POST_MSG_TEXT_MINUS')?></div><?
		?><textarea name="REVIEW_TEXT_minus"><?=(isset($arResult['REVIEW_TEXT_EXT']['MINUS']) ? $arResult['REVIEW_TEXT_EXT']['MINUS'] : '')?></textarea><br /><?
		?><div class="fieldname"><?=GetMessage('POST_MSG_TEXT_COMMENT')?></div><?
		?><textarea class="comment" name="REVIEW_TEXT_comment"><?=(isset($arResult['REVIEW_TEXT_EXT']['COMMENT']) ? $arResult['REVIEW_TEXT_EXT']['COMMENT'] : '')?></textarea><br /><?
		
		?><textarea class="noned" name="REVIEW_TEXT"><?=(isset($arResult['REVIEW_TEXT']) ? $arResult['REVIEW_TEXT'] : '')?></textarea><?
		
		if(strLen($arResult['CAPTCHA_CODE']) > 0)
		{
			?><div class="fieldname"><?=GetMessage("F_CAPTCHA_PROMT")?></div><?
			?><img class="captcha_image" src="/bitrix/tools/captcha.php?captcha_code=<?=$arResult['CAPTCHA_CODE']?>" width="180" height="39" /><br /><?
			?><input type="hidden" name="captcha_code" value="<?=$arResult['CAPTCHA_CODE']?>"/><?
			?><input type="text" name="captcha_word" autocomplete="off" /><?
			?><br /><?
		}
		
		?><input class="send" name="send_button" type="submit" value="<?=GetMessage("OPINIONS_SEND")?>" /><?
		
		$frame->end();
	?></form><?
?></div><?
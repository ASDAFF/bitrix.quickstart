<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(empty($arResult['ERROR_MESSAGE']) && !empty($arResult['OK_MESSAGE']))
{
  ?><div class="form-message form-message_success mainform preview_form"><?
    echo ShowNote($arResult['OK_MESSAGE']);
  ?></div><?
}
?><div class="g-hidden">
    <div class="popup popup-review" id="form_reviews"><?
  ?><form class="popup-form form-review form" action="<?=POST_FORM_ACTION_URI?>#postform" name="<?=$arParams['FORM_ID']?>" id="<?=$arParams['FORM_ID']?>" method="POST" enctype="multipart/form-data"><?

    ?><input type="hidden" name="index" value="<?=htmlspecialcharsbx($arParams['form_index'])?>" /><?
    ?><input type="hidden" name="back_page" value="<?=$arResult['CURRENT_PAGE']?>" /><?
    ?><input type="hidden" name="ELEMENT_ID" value="<?=$arParams['ELEMENT_ID']?>" /><?
    ?><input type="hidden" name="SECTION_ID" value="<?=$arResult['ELEMENT_REAL']['IBLOCK_SECTION_ID']?>" /><?
    ?><input type="hidden" name="save_product_review" value="Y" /><?
    ?><input type="hidden" name="preview_comment" value="N" /><?
    ?><?=bitrix_sessid_post()?>

    <?=GetMessage('RS.FLYAWAY.REVIEW_RATING')?> 
      <div class = "stars-rating js-stars">
        <? for ($i = 1; $i <= 5; $i++): ?>
          <span class = "star" data-index=<?=$i?>></span>
        <? endfor; ?>
      </div>

      <div class="form-box webform"><?
        ?><div class="field form">
          <span class="label-wrap"><?=GetMessage('OPINIONS_NAME')?><span class="required">*</span></span>
            <input class="form-item form-control" id="REVIEW_AUTHOR<?=$arParams['form_index']?>" name="REVIEW_AUTHOR" type="text" value="<?=$arResult['REVIEW_AUTHOR']?>" />
        </div><?

        ?><div class="field form"><?
          ?><span class="label-wrap"><?=GetMessage('POST_MSG_TEXT_PLUS')?></span><?
          ?><textarea class="form-item form-control" name="REVIEW_TEXT_plus"><?=(isset($arResult['REVIEW_TEXT_EXT']['PLUS']) ? $arResult['REVIEW_TEXT_EXT']['PLUS'] : '')?></textarea><?
        ?></div><?

        ?><div class="field form"><?
          ?><span class="label-wrap"><?=GetMessage('POST_MSG_TEXT_MINUS')?></span><?
          ?><textarea name="REVIEW_TEXT_minus" class="form-item form-control" id="shortcomings" name="shortcomings" type="text"><?=(isset($arResult['REVIEW_TEXT_EXT']['MINUS']) ? $arResult['REVIEW_TEXT_EXT']['MINUS'] : '')?></textarea><?
        ?></div><?

        ?><div class="field form"><?
          ?><span class="label-wrap"><?=GetMessage('POST_MSG_TEXT_COMMENT')?><span class="required">*</span></span><?
          ?><textarea name="REVIEW_TEXT_comment" class="form-item form-control" id="comment" name="comment" type="text"><?=(isset($arResult['REVIEW_TEXT_EXT']['COMMENT']) ? $arResult['REVIEW_TEXT_EXT']['COMMENT'] : '')?></textarea><?
        ?></div><?
    
      ?><textarea style="display:none;" name="REVIEW_TEXT"><?=(isset($arResult['REVIEW_TEXT']) ? $arResult['REVIEW_TEXT'] : '')?></textarea><?
                 ?></div><?
      ?><div class="form-raw"><?
        ?><div class="form-raw__item form-raw__item_simple"><?
          ?><dl class="field"><?
            $frame = $this->createFrame()->begin("");
              if(strLen($arResult["CAPTCHA_CODE"]) > 0)
              {
                ?><div class="inner-wrap-capcha col col-md-12 form-group field-wrap req"><?
                  
                ?><div class="row"><?
                  /*?><label for="captcha_<?=$arResult['WEB_FORM_NAME']?>" class="col col-md-12 inner-wrap-capcha__text"><?=GetMessage("F_CAPTCHA_PROMT")?>: <span class="required"><?=$arResult['REQUIRED_SIGN']?></span><br /></label><?*/
                  ?><div class="col col-md-6"><img class="captcha_image field__captcha" src="/bitrix/tools/captcha.php?captcha_code=<?=$arResult['CAPTCHA_CODE']?>" width="180" height="39" /></div><br /><?
                  ?><div class="col col-md-6 form"><input class="form-control req-input form-item" id="captcha_<?=$arResult['WEB_FORM_NAME']?>" type="text" name="captcha_word" size="30" maxlength="50" value=""></div><?
                  ?><input class="hidden_input_captcha" type="hidden" name="captcha_code" value="<?=$arResult['CAPTCHA_CODE']?>"/><?
                  /*?><a id="reloadCaptcha" class="inner-wrap-capcha__reload"><?=GetMessage('CHANGE_IMG_REVIEWS')?></a><?*/
                ?></div><?
              ?></div><?
              }
              $frame->end();
            ?></dl><?
          ?></div><?
          ?></div><?
          ?><div class="form-raw__item form-raw__item_simple"><?
            ?><div class="buttons"><?
              ?><span class="inner-wrap__text"><span class="required">*</span> &#8211;<?=GetMessage('REQUIRES_INPUT')?></span><?
              ?><input name="send_button" class="btn btn-primary btn-group-lg btn-default btn2 webform-button" type="submit" value="<?=GetMessage("OPINIONS_SEND")?>" /><?
            ?></div><?
          ?></div><?
     
   ?></form><?
  ?></div><?
?></div><?


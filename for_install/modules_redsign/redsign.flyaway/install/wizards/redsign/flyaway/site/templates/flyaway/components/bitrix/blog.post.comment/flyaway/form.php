<?php
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__DIR__.'/template.php');

$ajaxPath = $templateFolder.'/ajax.php';
?>

<div class="rsform" id="form_reviews">
    <form method="POST" name="form_comment" class="popup-form form-review form" id="form_comment" action="<?=$ajaxPath?>">
        <div id="form_c_del" style="display:none;"></div>
        <input type="hidden" name="parentId" id="parentId" value="">
		<input type="hidden" name="edit_id" id="edit_id" value="">
		<input type="hidden" name="act" id="act" value="add">
		<input type="hidden" name="post" value="Y">

        <?php if(isset($_REQUEST["IBLOCK_ID"])): ?>
            <input type="hidden" name="IBLOCK_ID" value="<?=(int)$_REQUEST["IBLOCK_ID"]; ?>">
        <?php endif; ?>

        <?php if(isset($_REQUEST["ELEMENT_ID"])): ?>
            <input type="hidden" name="ELEMENT_ID" value="<?=(int)$_REQUEST["ELEMENT_ID"]; ?>">
        <?php endif; ?>

        <?php if(isset($_REQUEST["SITE_ID"])): ?>
            <input type="hidden" name="SITE_ID" value="<?=htmlspecialcharsbx($_REQUEST["SITE_ID"]); ?>">
        <?php endif; ?>

		<?=bitrix_sessid_post()?>

        <b><?=Loc::getMessage('RS.FLYAWAY.REVIEW_RATING')?></b>
        <div class="stars-rating js-stars" style="margin-bottom: 10px;">
            <? for ($i = 1; $i <= 5; $i++): ?>
              <span class = "star" data-index=<?=$i?>></span>
            <? endfor; ?>
            <input type="hidden" name="review_rating" id="review_rating" value="0">
        </div>

        <?php if(empty($arResult["User"])): ?>
            <div class="form-group">
                <label for="user_name">
                    <?=Loc::getMessage("B_B_MS_NAME")?><span class="required"> *</span>
                </label>
                <input class="req-input form-item form-control" maxlength="255" size="30" tabindex="3" type="text" name="user_name" id="user_name" value="<?=htmlspecialcharsEx($_SESSION["blog_user_name"])?>">
                <span class="help-block" style="display:none;"><?=Loc::getMessage('USER_LOGIN_ERROR'); ?></span>
            </div>
            <div class="form-group">
                <label for="user_email">
                    E-mail
                </label>
                <input class="req-input form-item form-control" maxlength="255" size="30" tabindex="4" type="text" name="user_email" id="user_email" value="<?=htmlspecialcharsEx($_SESSION["blog_user_email"])?>">
            </div>
        <?php endif; ?>

        <?php if(strlen($arResult["NoCommentReason"]) > 0): ?>
			<div id="nocommentreason" style="display:none;"><?=$arResult["NoCommentReason"]?></div>
		<?php endif; ?>

        <div class="form-group">
            <label for="review_text_plus">
                <?=Loc::getMessage('POST_MSG_TEXT_PLUS');?>
            </label>
            <textarea class="form-item form-control" id="review_text_plus" name="review_text_plus"></textarea>
        </div>
        <div class="form-group">
            <label for="review_text_plus">
                <?=Loc::getMessage('POST_MSG_TEXT_MINUS');?>
            </label>
            <textarea class="form-item form-control" id="review_text_minus" name="review_text_minus"></textarea>
        </div>
        <div class="form-group">
            <label for="review_text_comment">
                <?=Loc::getMessage('POST_MSG_TEXT_COMMENT');?> <span class="required"> *</span>
            </label>
            <textarea class="form-item form-control" id="review_text_comment" name="review_text_comment"></textarea>
             <span class="help-block" style="display:none;"><?=Loc::getMessage('POST_MSG_ERROR_COMMENT'); ?></span>
            <input type="hidden" name="comment" id="comment">
        </div>

        <?php if($arResult["use_captcha"]===true): ?>
            <div class="rsform__captcha clearfix">
                <div class="captcha_wrap">
                    <div class="rsform__captcha-label"><label for="captcha_sid"><?=Loc::getMessage("B_B_MS_CAPTCHA_SYM");?></label></div>
                    <div class="rsform__captcha-input">
                        <input type="hidden" name="captcha_code" id="captcha_code" value="<?=$arResult["CaptchaCode"]?>">
						<input class="form-control req-input form-item"  type="text" size="30" name="captcha_word" id="captcha_word" value=""  tabindex="7">
                    </div>
                    <div class="rsform__captcha-image" >
                        <img src="/bitrix/tools/captcha.php?captcha_code=<?=$arResult["CaptchaCode"]?>" width="180" height="40" id="captcha">
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="separator rsform__separator"></div>

        <div class="rsform__bottom">
            <div class="rsform__bottom-ps"><?=Loc::getMessage('MSG_REQUIRED_FIELDS')?></div>
            <div class="rsform__bottom-button">
                <input name="sub-post" id="post-button" onclick="submitComment()" class="btn btn-group-lg btn-default btn2" type="button"  value="<?=Loc::getMessage('B_B_MS_SEND')?>">
            </div>
        </div>

    </form>
</div>

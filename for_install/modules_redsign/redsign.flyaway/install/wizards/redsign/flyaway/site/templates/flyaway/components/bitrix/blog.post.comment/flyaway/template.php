<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Localization\Loc;
?>
<div class="reviews-bar row" id="blg-comment-<?=$arParams["ID"]?>">
    <?php
    if($arResult["is_ajax_post"] != "Y"):
        include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/script.php");
    else:
        $APPLICATION->RestartBuffer();
    ?>
        <script>
            window.BX = top.BX;
            <?php if($arResult["use_captcha"]===true): ?>
                var cc;
                if(document.cookie.indexOf('<?echo session_name()?>'+'=') == -1)
					cc = Math.random();
				else
					cc ='<?=$arResult["CaptchaCode"]?>';

				BX('captcha').src='/bitrix/tools/captcha.php?captcha_code='+cc;
				BX('captcha_code').value = cc;
				BX('captcha_word').value = "";
            <?php endif; ?>
        </script>
        <?php if(strlen($arResult["COMMENT_ERROR"])>0): ?>
            <script>top.commentEr = 'Y';</script>
            <div class="col col-md-12">
                <div class="alert alert-danger"><?=$arResult["COMMENT_ERROR"]?></div>
            </div>
        <?php endif; ?>
    <? endif; ?>

    <?php if(strlen($arResult["MESSAGE"])>0): ?>
        <div class="col col-md-12">
            <div class="alert alert-success"><?=$arResult["MESSAGE"]?></div>
        </div>
    <?php endif; ?>

    <?php if(strlen($arResult["ERROR_MESSAGE"])>0): ?>
        <div class="col col-md-12">
            <div class="alert alert-warning"><?=$arResult["ERROR_MESSAGE"]?></div>
        </div>
    <?php endif; ?>

    <?php if(strlen($arResult["FATAL_MESSAGE"])>0): ?>
        <div class="col col-md-12">
            <div class="alert alert-danger"><?=$arResult["FATAL_MESSAGE"]?></div>
        </div>

    <?php else: ?>
        <?php if($arResult["is_ajax_post"] != "Y"): ?>
        <div class="col col-md-12">
            <div class="row">
                <div class="col col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <h2 class="product-content__title"><?=Loc::getMessage('RS.FLYAWAY.REVIEWS')?></h2>
                </div>
                <?php if($arResult["CanUserComment"]): ?>
                    <div class="col col-xs-12 col-sm-12 col-md-9 col-lg-9">
                        <div class="form-panel">
                            <a class="btn btn-default btn2" href="#form_reviews" title="<?=Loc::getMessage('TITLE_FORM')?>" onClick="showComment(0, 0, this)">
                                <?=Loc::getMessage('RS.FLYAWAY.ADD_REVIEW')?>
                            </a>
                        </div>
                        <?php include(__DIR__."/form.php"); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if($arResult["is_ajax_post"] != "Y"): ?>

            <?php if($arResult['CanUserComment']): ?>
            <div id="form_comment_0">
				<div id="err_comment_0"></div>
				<div id="form_comment_0"></div>
				<div id="new_comment_cont_0"></div>
				<div id="new_comment_0" style="display:none;"></div>
			</div>
            <?php endif; ?>

            <div class="col col-md-12">
                <div class="row">
        <?php endif; ?>

            <?php if(!empty($arResult['CommentsResult']) && !empty($arResult['CommentsResult'][$arResult["firstLevel"]])):?>

                <?php if($arResult["is_ajax_post"] != "Y" && $arResult['NEED_NAV'] == 'Y'): ?>

                    <?php
                    $currentPage = $arResult["PAGE_COUNT"] - $arResult["PAGE"] + 1;
                    for($i = $arResult["PAGE_COUNT"]; $i >= 1; $i--):
                        $tmp = $arResult["CommentsResult"];
	                    $tmp[0] = array_reverse($arResult["PagesComment"][$i]);
                    ?>
                    <div id="blog-comment-page-<?=$i?>"<?if($currentPage != $i) echo "style=\"display:none;\""?> data-page="<?=$i?>">
                        <?php foreach($tmp[$arResult["firstLevel"]] as $comment): ?>
                            <?php showComment($comment, $arParams); ?>
                        <?php endforeach; ?>
                    </div>
                    <?php endfor; ?>

                    <div class="col col-md-12 text-center">
                        <div class="btn btn-default btn-button reviews-bar__btn" onclick="loadMoreReviews(this)"> <?=Loc::getMessage('MESSAGE_LOAD_MORE'); ?></div>
                    </div>
                <?php else: $comments = array_reverse($arResult['CommentsResult'][$arResult["firstLevel"]]); ?>
                    <?php foreach($comments as $comment): ?>
                        <?php showComment($comment, $arParams); ?>
                    <?php endforeach; ?>
                <?php endif; ?>

            <?php elseif($arResult["is_ajax_post"] != "Y"): ?>
                <div class="col col-md-12">
                    <div class="alert alert-info"><?=Loc::getMessage('NO_MESSAGES');?></div>
                </div>
            <?php endif; ?>

        <?php if($arResult["is_ajax_post"] != "Y"): ?>
                </div>
            </div>
        <?php endif; ?>


    <?php endif; ?>

    <?php if($arResult["is_ajax_post"] == "Y") die(); ?>
</div>


<?php function showComment($comment, $arParams) { ?>
    <div id="blg-comment-<?=$comment["ID"]?>">
        <div class="reviews__item col col-md-12">
            <div class="row">
                <div class="col col-xs-12 col-sm-4 col-md-3 col-lg-3 reviews__user">
                    <div class="reviews__image">
                        <span class="reviews__image-avatar">
                            <img src="<?=SITE_TEMPLATE_PATH?>/images/no-name.png" width="94px" alt="<?=$comment['AuthorName']; ?>">
                        </span>
                    </div>
                    <div class="reviews__info">
                        <span class="reviews__date visible-lg"><?=$comment['DateFormated'];?></span>
                        <span class="reviews__user-name"><?=$comment['AuthorName']; ?></span>
                        <span class="reviews__mail visible-lg">
                            <?php if(!empty($comment['AUTHOR_EMAIL'])): ?>
                                <?=$comment['AUTHOR_EMAIL']?>
                            <?php elseif(!empty($comment['arUser']['EMAIL'])): ?>
                                <?=$comment['arUser']['EMAIL']?>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
                <div class="col col-xs-12 col-sm-8 col-md-9 col-lg-9">
                    <?php $post = getPostExt(':S:', $comment['POST_TEXT']); ?>
                    <div class="reviews__rating">

                        <div class="reviews__stars stars-rating rating-<?=$post['RATING']?>">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <span class="star" data-index="<?=$i?>"></span>
                            <?php endfor; ?>
                        </div>

                        <div class="reviews__detail">
                            <span class="reviews__detail-name"><?=Loc::getMessage('POST_MSG_TEXT_PLUS')?></span>
                            <span class="reviews__detail-content">
                                <?=$post['PLUS']?>
                            </span>
                        </div>
                        <div class="reviews__detail">
                            <span class="reviews__detail-name"><?=Loc::getMessage('POST_MSG_TEXT_MINUS')?></span>
                            <span class="reviews__detail-content">
                                <?=$post['MINUS']?>
                            </span>
                        </div>

                        <div class="reviews__detail detail">
                            <span class="reviews__detail-content">
                                <?=$post['COMMENT']?>
                            </span>
                        </div>

                    </div>

                </div>
                <div class="col col-md-12">
                    <?php if(strlen($comment["urlToShow"])>0): ?>
                        <a href="javascript:void(0)" onclick="return hideShowComment('<?=$comment["urlToShow"]."&".bitrix_sessid_get()?>', '<?=$comment["ID"]?>');" title="<?=Loc::getMessage("BPC_MES_SHOW")?>">
                            <?=Loc::getMessage("BPC_MES_SHOW")?>
                        </a> |
                    <?php endif; ?>
                    <?php if(strlen($comment["urlToHide"])>0): ?>
                        <a href="javascript:void(0)" onclick="return hideShowComment('<?=$comment["urlToHide"]."&".bitrix_sessid_get()?>&IBLOCK_ID=<?=$_REQUEST["IBLOCK_ID"]?>&ELEMENT_ID=<?=$_REQUEST["ELEMENT_ID"]?>', '<?=$comment["ID"]?>');" title="<?=Loc::getMessage("BPC_MES_HIDE")?>">
                            <?=Loc::getMessage("BPC_MES_HIDE")?>
                        </a> |
                    <?php endif; ?>
                    <?php if(strlen($comment["urlToDelete"])>0): ?>
                        <a href="javascript:void(0)" onclick="if(confirm('<?=Loc::getMessage("BPC_MES_DELETE_POST_CONFIRM")?>')) deleteComment('<?=$comment["urlToDelete"]."&".bitrix_sessid_get()?>&IBLOCK_ID=<?=$_REQUEST["IBLOCK_ID"]?>&ELEMENT_ID=<?=$_REQUEST["ELEMENT_ID"]?>', '<?=$comment["ID"]?>');" title="<?=Loc::getMessage("BPC_MES_DELETE")?>">
                            <?=Loc::getMessage("BPC_MES_DELETE")?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

<?php }

function getPostExt($separator, $message)
{
	$message = htmlspecialcharsbx($message);
	$arrPostMessage = explode($separator, $message);
	return array(
		"RATING" => $arrPostMessage[0],
		"PLUS" => $arrPostMessage[1],
		"MINUS" => $arrPostMessage[2],
		"COMMENT" => $arrPostMessage[3],
	);
}

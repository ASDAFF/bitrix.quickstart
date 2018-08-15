<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/*
 * Подключаем jQuery
 * */
CJSCore::Init(array("jquery"));
IncludeTemplateLangFile(__FILE__);
?>

<div class="body_box">
    <div id="comments_soobwa">
        <div class="comments_main_box">
            <?if($arResult['COUNT_MASSAGE'] > 0){?>
                <p class="comments_title"><?=getMessage('COMMENTS_TITLE')?> <span>(<?=$arResult['COUNT_MASSAGE']?>)</span></p>
            <?}else{?>
                <p class="comments_title"><?=getMessage('COMMENTS_TITLE')?></p>
            <?}?>

            <div class="comments_list js-get-comment-<?=$arResult['ID_COMMENTS']?>">
                <? if($_REQUEST['GET_COMMENT'] == 'Y'){ $GLOBALS['APPLICATION']->RestartBuffer(); } ?>
                <?foreach ($arResult['ITEMS'] as $arItems){?>
                    <div class="comments_item  js-del-item-<?=$arItems['ID']?> <?if($arItems['ACTIVE'] != 'Y'){?>no_posted<?}?>">
                        <div class="comments_main">
                            <div class="comments_header">
                                <div class="user_summary">
                                    <div class="user_summary_avatar">
                                        <?if(!empty($arResult['USERS'][$arItems['ID_USER']]['PERSONAL_PHOTO'])){?>
                                            <img src="<?=$arResult['USERS'][$arItems['ID_USER']]['RESIZE_IMG']['src']?>" alt="">
                                        <?}else{?>
                                            <svg width="35" enable-background="new 0 0 128 128" id="Layer_1" version="1.1" viewBox="0 0 128 128" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                                <circle cx="64" cy="64" fill="#0094e5" id="circle" r="64"/>
                                                <g id="icon">
                                                    <path d="M64,99h35c0-16-10.4-29-24.6-33.4C80.1,62,84,55.7,84,48.5c0-11-9-20-20-20" fill="#E6E6E6" id="right"/>
                                                    <path d="M64,28.5c-11,0-20,9-20,20c0,7.2,3.9,13.6,9.6,17.1C39.4,70,29,83,29,99h35" fill="#FFFFFF" id="left"/>
                                                </g>
                                            </svg>
                                        <?}?>
                                    </div>
                                    <div class="user_summary_desc">
                                        <?if($arItems['ID_USER'] != 0){?>
                                            <?if(strlen($arResult['USERS'][$arItems['ID_USER']]['NAME']) > 0 or strlen($arResult['USERS'][$arItems['ID_USER']]['LAST_NAME']) > 0){?>
                                                <p class="user_summary_name"><?=$arResult['USERS'][$arItems['ID_USER']]['NAME']?>  <?=$arResult['USERS'][$arItems['ID_USER']]['LAST_NAME']?></p>
                                            <?}else{?>
                                                <p class="user_summary_name"><?=$arResult['USERS'][$arItems['ID_USER']]['LOGIN']?></p>
                                            <?}?>
                                        <?}else{?>
                                            <p class="user_summary_name"><?=getMessage('COMMENTS_NO_USER')?></p>
                                        <?}?>
                                        <div class="user_summary_content">
                                            <p class="user_summary_date"><?=$arItems['FORMAT_DATA']?></p>
                                        </div>
                                        <?if($arItems['ACTIVE'] != 'Y'){?>
                                            <p class="no_posted_status"><?=getMessage('COMMENTS_NO_ACTIVE')?></p>
                                        <?}?>
                                    </div>
                                    <?if($arResult['USER']['IS_ADMIN'] == 'Y'){?>
                                        <div class="admin_panel">
                                            <div class="admin_panel_wrap">
                                                <div class="admin_panel_button"><span></span><span></span><span></span></div>
                                                <div class="admin_panel_menu">
                                                    <ul>
                                                        <?if($arItems['ACTIVE'] != 'Y'){?>
                                                            <li><a data-id="<?=$arItems['ID']?>" class="js-posted-comment" href="#"><?=getMessage('COMMENTS_NO_PUBLISH')?></a></li>
                                                        <?}?>
                                                        <li><a data-id="<?=$arItems['ID']?>" class="js-del-comment" href="#"><?=getMessage('COMMENTS_DELETE')?></a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    <?}?>
                                </div>
                            </div>
                            <div class="comments_body">
                                <div class="comments_body_inner">
                                    <div class="comments_body_text">
                                        <?=$arItems['TEXT']?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?}?>
                <? if($_REQUEST['GET_COMMENT'] == 'Y'){ die(); } ?>
            </div>
            <?if($arResult['COUNT_PAGES'] > 1){?>
                <div class="comments_more">
                    <a href="#"
                       data-pages="<?=$arResult['COUNT_PAGES']?>"
                       data-pagen="1"
                       data-comments="<?=$arResult['ID_COMMENTS']?>"
                       class="btn btn_light btn_inline js-read-more"><?=getMessage('COMMENTS_READ_MORE')?></a>
                </div>
            <?}else{?>
                <?if($arResult['COUNT_MASSAGE'] == 0){?>
                    <p class="comments_not_auth_text"><?=getMessage('COMMENTS_MESSAGE_FIRS_COMMIT')?></p>
                <?}?>
            <?}?>
        </div>

        <?if($arResult['USER']['IS_AUTHORIZED'] == 'Y' or $arParams['AUTH'] == 'Y'){?>
            <div class="comments_main_form">
                <div class="comments_main_form_wrap">
                    <p class="comments_title"><?=getMessage('COMMENTS_ADD_COMMENT')?></p>
                    <div class="form_box_wrap">
                        <form action="" class="js-comments-form">
                            <input type="hidden" name="ID_COMMENTS" value="<?=$arResult['ID_COMMENTS']?>">
                            <input type="hidden" name="ADD_COMMENT" value="Y">
                            <input type="hidden" name="ID_USER" value="<?=$arResult['USER']['ID']?>">
                            <div class="form_box">
                                <div class="field">
                                    <div class="field_wrap">
                                        <textarea class="auto_resize" name="TEXT" placeholder="<?=getMessage('COMMENTS_PLACEHOLDER')?>" required></textarea>
                                    </div>
                                </div>
                                <div class="form_buttons">
                                    <button class="btn btn_blue btn_comment"><?=getMessage('COMMENTS_SEND')?></button>
                                </div>
                            </div>
                        </form>
                        <div class="comments_thanks">
                            <div class="comments_thanks_wrapper">
                                <span><?=getMessage('COMMENTS_YOU_COMMENT_ADD')?></span>
                                <svg
                                    fill="#0094e5"
                                    id="Layer_1"
                                    class="send_mail"
                                    version="1.1"
                                    viewBox="0 0 512 512"
                                    xml:space="preserve"
                                    xmlns="http://www.w3.org/2000/svg"
                                    xmlns:xlink="http://www.w3.org/1999/xlink">
                                    <path class="st0" d="M235.1,386.3c-5.7,0-11.1-2.4-14.9-6.6l-104.1-116c-7.4-8.2-6.7-20.9,1.5-28.2c8.2-7.4,20.9-6.7,28.2,1.5   l86.8,96.8l131.6-199.1c6.1-9.2,18.5-11.7,27.7-5.7c9.2,6.1,11.7,18.5,5.7,27.7L251.8,377.4c-3.4,5.2-9,8.5-15.2,8.9   C236.1,386.3,235.6,386.3,235.1,386.3z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?}else{?>
            <div class="comments_main_form">
                <p class="comments_title"><?=getMessage('COMMENTS_ADD_COMMENT')?></p>
                <div class="comments_not_auth">
                    <p class="comments_not_auth_text"><?=getMessage('COMMENTS_ADD_COMMENT')?></p>
                    <div class="comments_not_auth_link">
                        <a href="<?=$arParams['ENTRY_URL']?>" class="btn btn_blue btn_inline"><?=getMessage('COMMENTS_AUTH')?></a>
                        <a href="<?=$arParams['AUTH_URL']?>" class="btn btn_gray btn_inline"><?=getMessage('COMMENTS_REGISTRATION')?></a>
                    </div>
                </div>
            </div>
        <?}?>
    </div>
</div>
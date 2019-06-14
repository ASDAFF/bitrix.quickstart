<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

//---------- component.php ----------//
$MESS['API_REVIEWS_MODULE_ERROR']             = 'Модуль «TS Умные отзывы о магазине и о товаре» не установлен';
$MESS['API_REVIEWS_STATUS_404']               = 'Отзыв не найден';
$MESS['API_REVIEWS_LIST_INFORMATION']         = 'Информация';
$MESS['API_REVIEWS_LIST_UPDATE_INFO_SUCCESS'] = 'Информация обновлена';
$MESS['API_REVIEWS_LIST_SESSION_EXPIRED']     = 'Ваша сессия истекла';
$MESS['API_REVIEWS_LIST_ERROR_REVIEW_ID']     = 'Не указан ID отзыва';
$MESS['API_REVIEWS_LIST_SAVE_REPLY_ERROR']    = 'Ошибка! Ответ не добавился в базу';
$MESS['API_REVIEWS_LIST_FORMAT_NAME']         = '#LAST_NAME# #NAME#';
$MESS['API_REVIEWS_LIST_GUEST_NAME']          = 'Аноним';
$MESS['API_REVIEWS_LIST_STATUS_D']            = 'Не публиковать';
$MESS['API_REVIEWS_LIST_STATUS_M']            = 'Отзыв скрыт';


//MESS FOR REVIEW
$MESS['API_REVIEWS_LIST_DELETE_REVIEW_SUCCESS']       = 'Отзыв удален';
$MESS['API_REVIEWS_LIST_DELETE_REVIEW_ERROR']         = 'Ошибка! Отзыв не удалился';
$MESS['API_REVIEWS_LIST_SHOW_REVIEW_SUCCESS']         = 'Отзыв активирован';
$MESS['API_REVIEWS_LIST_SHOW_REVIEW_ERROR']           = 'Ошибка! Отзыв не активирован';
$MESS['API_REVIEWS_LIST_HIDE_REVIEW_SUCCESS']         = 'Отзыв деактивирован';
$MESS['API_REVIEWS_LIST_HIDE_REVIEW_ERROR']           = 'Ошибка! Отзыв не деактивирован';
$MESS['API_REVIEWS_LIST_UPDATE_REVIEW_SUCCESS']       = 'Отзыв обновлен';
$MESS['API_REVIEWS_LIST_UPDATE_REVIEW_ERROR']         = 'Ошибка! Отзыв не обновлен';
$MESS['API_REVIEWS_LIST_MESS_ADD_UNSWER_EVENT_THEME'] = 'Ответ магазина ' . ToUpper($_SERVER['SERVER_NAME']);
$MESS['API_REVIEWS_LIST_MESS_ADD_UNSWER_EVENT_TEXT']  = 'Здравствуйте! К Вашему отзыву добавлен ответ, для просмотра перейдите по ссылке';
$MESS['API_REVIEWS_LIST_MESS_TRUE_BUYER']             = 'Проверенный покупатель';
$MESS['API_REVIEWS_LIST_MESS_HELPFUL_REVIEW']         = 'Отзыв полезен?';


//==============================================================================
//                        COMPONENT $arParams replace langs
//==============================================================================
$MESS['API_REVIEWS_LIST_SHOP_NAME']       = '';
$MESS['API_REVIEWS_LIST_SHOP_NAME_REPLY'] = '';
$MESS['API_REVIEWS_LIST_PAGE_TITLE']      = 'Интернет-магазин ' . ToUpper($_SERVER['SERVER_NAME']);
$MESS['API_REVIEWS_LIST_BROWSER_TITLE']   = 'Отзывы об интернет-магазине ' . ToUpper($_SERVER['SERVER_NAME']);


//==============================================================================
//                              TEMPLATE MESS
//==============================================================================
$MESS['API_REVIEWS_LIST_COMPANY']      = 'Компания';
$MESS['API_REVIEWS_LIST_WEBSITE']      = 'Веб-сайт';
$MESS['API_REVIEWS_LIST_ADVANTAGE']    = 'Достоинства';
$MESS['API_REVIEWS_LIST_DISADVANTAGE'] = 'Недостатки';
$MESS['API_REVIEWS_LIST_ANNOTATION']   = 'Комментарий';
$MESS['API_REVIEWS_LIST_BTN_REPLY']    = 'Ответить';
$MESS['API_REVIEWS_LIST_BTN_EDIT']     = 'Изменить';
$MESS['API_REVIEWS_LIST_BTN_SAVE']     = 'Сохранить';
$MESS['API_REVIEWS_LIST_BTN_CANCEL']   = 'Отмена';
$MESS['API_REVIEWS_LIST_BTN_DELETE']   = 'Удалить';
$MESS['API_REVIEWS_LIST_BTN_HIDE']     = 'Скрыть';
$MESS['API_REVIEWS_LIST_BTN_SHOW']     = 'Активировать';
$MESS['API_REVIEWS_LIST_BTN_SEND']     = 'Добавить в рассылку';
$MESS['API_REVIEWS_LIST_ORDER_NUM']    = '№';
$MESS['API_REVIEWS_LIST_FILES']        = 'Файлы';
$MESS['API_REVIEWS_LIST_VIDEOS']       = 'Видео';


//TEMPLATE JS MESS
$MESS['API_REVIEWS_LIST_JS_BTN_REPLY_SAVE']   = 'Сохранить';
$MESS['API_REVIEWS_LIST_JS_BTN_REPLY_CANCEL'] = 'Отмена';
$MESS['API_REVIEWS_LIST_JS_BTN_REPLY_SEND']   = 'Сохранить и отправить клиенту';
$MESS['API_REVIEWS_LIST_JS_REVIEW_DELETE']    = 'Желаете удалить отзыв №{id}?';
$MESS['API_REVIEWS_LIST_JS_REVIEW_LINK']      = 'Ссылка на отзыв #{id}';

//getFileDelete
$MESS['apiReviesList_getFileDelete_confirmTitle']   = 'Вы уверены?';
$MESS['apiReviesList_getFileDelete_confirmContent'] = 'Файл будет удален с диска и базы';
$MESS['apiReviesList_getFileDelete_labelOk']        = 'Удалить';
$MESS['apiReviesList_getFileDelete_labelCancel']    = 'Отмена';

//getVideoDelete
$MESS['apiReviesList_getVideoDelete_confirmTitle']   = 'Вы уверены?';
$MESS['apiReviesList_getVideoDelete_confirmContent'] = 'Видео будет удалено с диска и базы';
$MESS['apiReviesList_getVideoDelete_labelOk']        = 'Удалить';
$MESS['apiReviesList_getVideoDelete_labelCancel']    = 'Отмена';


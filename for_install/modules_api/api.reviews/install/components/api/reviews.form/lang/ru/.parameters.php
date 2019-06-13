<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

$MESS['API_REVIEWS_MODULE_ERROR'] = 'Модуль "TS Умные отзывы о магазине и о товаре" не установлен';

//BASE
$MESS['EMAIL_TO']              = 'E-mail для уведомлений';
$MESS['SHOP_NAME']             = 'Название магазина';
$MESS['SHOP_NAME_DEFAULT']     = ToUpper($_SERVER['SERVER_NAME']);
$MESS['SHOP_TEXT']             = 'Текст перед названием магазина';
$MESS['SHOP_TEXT_DEFAULT']     = 'Оставьте свой отзыв мииииии о магазине';
$MESS['SHOP_BTN_TEXT']         = 'Текст кнопки под названием магазина';
$MESS['SHOP_BTN_TEXT_DEFAULT'] = 'Оставить свой отзыв';
$MESS['FORM_TITLE']            = 'Заголовок формы';
$MESS['FORM_TITLE_DEFAULT']    = 'Отзыв о магазине ' . ToUpper($_SERVER['SERVER_NAME']);
$MESS['CITY_VIEW']             = 'Населенный пункт в виде строки';
$MESS['USE_PLACEHOLDER']       = 'Выводить подсказки полей';
$MESS['IBLOCK_ID']             = 'ID инфоблока';
$MESS['SECTION_ID']            = 'ID раздела';
$MESS['ELEMENT_ID']            = 'ID элемента';
$MESS['ORDER_ID']              = 'ID заказа';
$MESS['URL']                   = 'URL страницы';


//FORM FIELDS
$MESS['TITLE']            = 'Заголовок';
$MESS['ADVANTAGE']        = 'Достоинства';
$MESS['DISADVANTAGE']     = 'Недостатки';
$MESS['ANNOTATION']       = 'Комментарий';
$MESS['DELIVERY']         = 'Доставка';
$MESS['CITY']             = 'Населенный пункт';
$MESS['FORM_GUEST_FIELDS'] = array(
	'GUEST_EMAIL' => 'E-mail гостя',
	'GUEST_PHONE' => 'Телефон гостя',
);

$MESS['DISPLAY_FIELDS']  = 'Выводимые поля';
$MESS['REQUIRED_FIELDS'] = 'Обязательные поля';
$MESS['CHOOSE']          = '-- Не выбрано --';

//RULES
$MESS['RULES']              = 'Правила публикации отзывов';
$MESS['RULES_TEXT']         = 'Текст ссылки';
$MESS['RULES_TEXT_DEFAULT'] = 'Правила публикации отзывов';
$MESS['RULES_LINK']         = 'Ссылка на страницу с правилами';
$MESS['RULES_LINK_DEFAULT'] = 'http://' . $_SERVER['SERVER_NAME'] . '/rules/';

$MESS['PREMODERATION']        = 'Премодерация';
$MESS['PREMODERATION_VALUES'] = array(
	'N' => 'Нет',
	'Y' => 'Все',
	'A' => 'Анонимные',
);


//MESSAGES
$MESS['MESSAGES'] = 'Сообщения';
$MESS['ADD_REVIEW_VIZIBLE']            = 'Отзыв добавлен и выводится';
$MESS['ADD_REVIEW_VIZIBLE_DEFAULT']    = 'Спасибо! Ваш отзыв опубликован';
$MESS['ADD_REVIEW_HIDDEN']             = 'Отзыв добавлен, но скрыт';
$MESS['ADD_REVIEW_HIDDEN_DEFAULT']     = 'Спасибо! Ваш отзыв не будет опубликован, но мы его обязательно прочтем';
$MESS['ADD_REVIEW_MODERATION']         = 'Отзыв отправлен на модерацию';
$MESS['ADD_REVIEW_MODERATION_DEFAULT'] = 'Спасибо! Ваш отзыв отправлен на модерацию';
$MESS['ADD_REVIEW_ERROR']              = 'Ошибка добавления отзыва';
$MESS['ADD_REVIEW_ERROR_DEFAULT']      = 'Внимание! Ошибка добавления отзыва';

//MAIN_MESS
$MESS['MAIN_MESS'] = 'Основные фразы';
$MESS['MESS_STAR_RATING_1']         = 'Текст "Оценка 1"';
$MESS['MESS_STAR_RATING_1_DEFAULT'] = 'Ужасный магазин';
$MESS['MESS_STAR_RATING_2']         = 'Текст "Оценка 2"';
$MESS['MESS_STAR_RATING_2_DEFAULT'] = 'Плохой магазин';
$MESS['MESS_STAR_RATING_3']         = 'Текст "Оценка 3"';
$MESS['MESS_STAR_RATING_3_DEFAULT'] = 'Обычный магазин';
$MESS['MESS_STAR_RATING_4']         = 'Текст "Оценка 4"';
$MESS['MESS_STAR_RATING_4_DEFAULT'] = 'Хороший магазин';
$MESS['MESS_STAR_RATING_5']         = 'Текст "Оценка 5"';
$MESS['MESS_STAR_RATING_5_DEFAULT'] = 'Отличный магазин';

$MESS['MESS_ADD_REVIEW_EVENT_THEME']         = 'Тема письма для нового отзыва';
$MESS['MESS_ADD_REVIEW_EVENT_THEME_DEFAULT'] = 'Отзыв о магазине ' . ToUpper($_SERVER['SERVER_NAME']);
$MESS['MESS_ADD_REVIEW_EVENT_TEXT']          = 'Текст письма для нового отзыва';
$MESS['MESS_ADD_REVIEW_EVENT_TEXT_DEFAULT']  = 'Добавлен новый отзыв, для просмотра перейдите по ссылке';



//ADDITIONAL_SETTINGS
$MESS['INCLUDE_CSS']    = 'Подключать стили шаблона';
$MESS['INCLUDE_JQUERY'] = 'Подключать jQuery если не работают кнопки/форма';
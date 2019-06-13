<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

//==============================================================================
//                             Component mess
//==============================================================================
$MESS['API_REVIEWS_MODULE_ERROR']              = 'Модуль "TS Умные отзывы о магазине и о товаре" не установлен';
$MESS['API_REVIEWS_MODULE_SALE_ERROR']         = 'Модуль «Интернет-магазин» не установлен';
$MESS['API_REVIEWS_FORM_SESSION_EXPIRED']      = 'Ваша сессия истекла, обновите страницу';
$MESS['API_REVIEWS_FORM_ERROR_RATING']         = 'Поставьте оценку от 1 до 5';
$MESS['API_REVIEWS_FORM_FIELD_ERROR']          = 'Укажите #NAME#';
$MESS['API_REVIEWS_FORM_FIELD_ORDER_ID_ERROR'] = 'Заказ не найден';

//Alert video
$MESS['API_REVIEWS_FORM_ALERT_WRONG_VIDEO_URL'] = array(
	 'type'    => 'info',
	 'theme'   => 'jbox',
	 'title'   => 'Нераспознанный адрес!',
	 'content' => 'Попробуйте ввести другой',
);
$MESS['API_REVIEWS_FORM_ALERT_UPLOAD_VIDEO_LIMIT'] = array(
	 'type'    => 'info',
	 'theme'   => 'jbox',
	 'title'   => 'Больше загружать нельзя!',
	 'content' => 'Превышен лимит загружаемых видео',
);
$MESS['API_REVIEWS_FORM_ALERT_VIDEO_ISSET'] = array(
	 'type'    => 'info',
	 'theme'   => 'jbox',
	 'title'   => 'Повтор видео!',
	 'content' => 'Данный видео уже загружен, попробуйте выбрать другое',
);

//Alert file
$MESS['API_REVIEWS_FORM_ALERT_UPLOAD_FILE_LIMIT'] = array(
	 'type'    => 'info',
	 'theme'   => 'jbox',
	 'title'   => 'Больше загружать нельзя!',
	 'content' => 'Превышен лимит загружаемых файлов',
);


//==============================================================================
//                        Multilanguage phrases replace
//==============================================================================
//BASE
$MESS['API_REVIEWS_FORM_MESS_SHOP_NAME']     = '';
$MESS['API_REVIEWS_FORM_MESS_SHOP_TEXT']     = '';
$MESS['API_REVIEWS_FORM_MESS_SHOP_BTN_TEXT'] = '';
$MESS['API_REVIEWS_FORM_MESS_FORM_TITLE']    = '';

//RULES
$MESS['API_REVIEWS_FORM_MESS_RULES_TEXT'] = '';
$MESS['API_REVIEWS_FORM_MESS_RULES_LINK'] = '';

//DIALOG MESSAGES
$MESS['API_REVIEWS_FORM_MESS_ADD_REVIEW_VIZIBLE']    = '';
$MESS['API_REVIEWS_FORM_MESS_ADD_REVIEW_MODERATION'] = '';
$MESS['API_REVIEWS_FORM_MESS_ADD_REVIEW_ERROR']      = '';

//MESS_STAR_RATING
$MESS['API_REVIEWS_FORM_MESS_STAR_RATING_1'] = '';
$MESS['API_REVIEWS_FORM_MESS_STAR_RATING_2'] = '';
$MESS['API_REVIEWS_FORM_MESS_STAR_RATING_3'] = '';
$MESS['API_REVIEWS_FORM_MESS_STAR_RATING_4'] = '';
$MESS['API_REVIEWS_FORM_MESS_STAR_RATING_5'] = '';

//EVENT_MESS
$MESS['API_REVIEWS_FORM_MESS_ADD_REVIEW_EVENT_THEME'] = '';
$MESS['API_REVIEWS_FORM_MESS_ADD_REVIEW_EVENT_TEXT']  = 'Добавлен новый отзыв, для просмотра перейдите по ссылке';

//==============================================================================
//                            Template mess
//==============================================================================
//FORM LABELS
//$MESS['API_REVIEWS_FORM_LABEL_RATING']    = 'Оценка';
$MESS['API_REVIEWS_FORM_LABEL_PUBLISH']       = 'Публиковать';
$MESS['API_REVIEWS_FORM_LABEL_INTRODUCE']     = 'Представьтесь';
$MESS['API_REVIEWS_FORM_ANONYMOUS']           = 'Анонимно';
$MESS['API_REVIEWS_FORM_MY_NAME']             = 'От моего имени';
$MESS['API_REVIEWS_FORM_NO_PUBLISH']          = 'Не публиковать';
$MESS['API_REVIEWS_FORM_CHOOSE']              = '(не выбрано)';
$MESS['API_REVIEWS_FORM_SUBMIT_TEXT_DEFAULT'] = 'Отправить отзыв';
$MESS['API_REVIEWS_FORM_SUBMIT_TEXT_AJAX']    = 'Отправляется...';


//FORM FIELDS
$MESS['API_REVIEWS_FORM_ORDER_ID']     = array(
	 'NAME'        => '№ заказа',
	 'PLACEHOLDER' => '№ заказа',
	 'MESSAGE'     => 'Не указан номер заказа',
);
$MESS['API_REVIEWS_FORM_TITLE']        = array(
	 'NAME'        => 'Заголовок',
	 'PLACEHOLDER' => 'Заголовок отзыва',
	 'MESSAGE'     => 'Не указан заголовок',
);
$MESS['API_REVIEWS_FORM_COMPANY']      = array(
	 'NAME'        => 'Компания',
	 'PLACEHOLDER' => 'Название компании',
	 'MESSAGE'     => 'Не указана компания',
);
$MESS['API_REVIEWS_FORM_WEBSITE']      = array(
	 'NAME'        => 'Веб-сайт',
	 'PLACEHOLDER' => 'Адрес веб-сайта',
	 'MESSAGE'     => 'Укажите адрес сайта',
);
$MESS['API_REVIEWS_FORM_ADVANTAGE']    = array(
	 'NAME'        => 'Достоинства',
	 'PLACEHOLDER' => 'Что вам понравилось',
	 'MESSAGE'     => 'Не указаны достоинства',
);
$MESS['API_REVIEWS_FORM_DISADVANTAGE'] = array(
	 'NAME'        => 'Недостатки',
	 'PLACEHOLDER' => 'Что не оправдало ожиданий',
	 'MESSAGE'     => 'Не указаны недостатки',
);
$MESS['API_REVIEWS_FORM_ANNOTATION']   = array(
	 'NAME'        => 'Комментарий',
	 'PLACEHOLDER' => 'Другие впечатления',
	 'MESSAGE'     => 'Не указан комментарий',
);
$MESS['API_REVIEWS_FORM_FILES']        = array(
	 'NAME'        => 'Файлы',
	 'PLACEHOLDER' => 'Прикрепить файлы',
	 'MESSAGE'     => 'Прикрепите файлы',
);
$MESS['API_REVIEWS_FORM_VIDEOS']       = array(
	 'NAME'        => 'Видео',
	 'PLACEHOLDER' => 'Ссылка на видео YouTube, Rutube, Vimeo',
	 'MESSAGE'     => 'Добавьте ссылку на видео',
);
$MESS['API_REVIEWS_FORM_DELIVERY']     = array(
	 'NAME'        => 'Доставка',
	 'PLACEHOLDER' => 'Служба доставки',
	 'MESSAGE'     => 'Не указана служба доставки',
);

$MESS['API_REVIEWS_FORM_GUEST_NAME']  = array(
	 'NAME'        => '---',
	 'PLACEHOLDER' => 'Как вас зовут?',
	 'MESSAGE'     => 'Ваше имя обязательно',
);
$MESS['API_REVIEWS_FORM_CITY']        = array(
	 'NAME'        => 'Откуда вы?',
	 'PLACEHOLDER' => 'Откуда вы?',
	 'MESSAGE'     => 'Ваш город обязательно',
);
$MESS['API_REVIEWS_FORM_GUEST_EMAIL'] = array(
	 'NAME'        => '---',
	 'PLACEHOLDER' => 'Ваш e-mail (не публикуется)',
	 'MESSAGE'     => 'Ваш e-mail обязательно',
);
$MESS['API_REVIEWS_FORM_GUEST_PHONE'] = array(
	 'NAME'        => '---',
	 'PLACEHOLDER' => 'Ваш телефон (не публикуется)',
	 'MESSAGE'     => 'Ваш телефон обязательно',
);


//FILE UPLOAD
$MESS['API_REVIEWS_FORM_UPLOAD_DROP'] = 'Перетащите сюда файлы или нажмите для выбора';
$MESS['API_REVIEWS_FORM_UPLOAD_INFO'] = 'Максимальный размер загружаемого файла #UPLOAD_FILE_SIZE# в формате #FILE_TYPE#.<br>
Максимальное количество файлов - #UPLOAD_FILE_LIMIT# шт.<br>';

$MESS['API_REVIEWS_FORM_UPLOAD_onFileSizeError'] = '{{fileName}} размером {{fileSize}} превышает допустимый размер <b>{{maxFileSize}}</b>';
$MESS['API_REVIEWS_FORM_UPLOAD_onFileTypeError'] = 'Тип файла {{fileType}} не соответствует разрешенному {{allowedTypes}}';
$MESS['API_REVIEWS_FORM_UPLOAD_onFileExtError']  = 'Разрешены следующие расширения файлов: <b>{{extFilter}}</b>';
$MESS['API_REVIEWS_FORM_UPLOAD_onFilesMaxError'] = 'Разрешено максимум {{maxFiles}} файлов';


//VIDEO UPLOAD
$MESS['API_REVIEWS_FORM_UPLOAD_VIDEO_INFO'] = 'Максимальное количество видео - #UPLOAD_VIDEO_LIMIT# шт.<br>';
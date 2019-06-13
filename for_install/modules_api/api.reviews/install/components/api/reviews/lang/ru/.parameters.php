<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

$MESS['API_REVIEWS_MODULE_ERROR'] = 'Модуль «TS Умные отзывы о магазине и о товаре» не установлен';
$MESS['IBLOCK_MODULE_ERROR']      = 'Модуль «Информационные блоки» не установлен';

//SEF_MODE
$MESS['ARP_VARIABLE_ALIASES_REVIEW_ID'] = 'Идентификатор отзыва';
$MESS['ARP_VARIABLE_ALIASES_USER_ID']   = 'Идентификатор пользователя';
$MESS['ARP_SEF_MODE_LIST']              = 'Страница списка отзывов';
$MESS['ARP_SEF_MODE_DETAIL']            = 'Страница отзыва детально';
$MESS['ARP_SEF_MODE_USER']              = 'Страница пользователя';
$MESS['ARP_SEF_MODE_SEARCH']            = 'Страница поиска';
$MESS['ARP_SEF_MODE_RSS']               = 'Страница RSS';

//GROUPS
$MESS['REVIEWS_FILTER'] = 'Фильтры / Привязка отзывов';
$MESS['REVIEWS_FORM']   = 'Форма добавления отзыва';
$MESS['REVIEWS_LIST']   = 'Список отзывов';
$MESS['REVIEWS_EVENT']  = 'E-mail уведомления';


//GENERAL
$MESS['CHOOSE']                = '-- Не выбрано --';
$MESS['INCLUDE_CSS']           = 'Подключать встроенные стили CSS';
$MESS['INCLUDE_CSS_TIP']       = 'Подключаются все встроенные в компонент стили оформления';
$MESS['INCLUDE_JQUERY']        = 'Подключать jQuery';
$MESS['INCLUDE_JQUERY_TIP']    = 'Если на вашем сайте уже подключена jQuery, то здесь необходимо выключить. Для проверки включайте любую версию, если не работают всплывающие окна и т.д.';
$MESS['INCLUDE_JQUERY_VALUES'] = array(
	 'N'       => '(нет)',
	 'jquery'  => 'v1.8.3 (ядро Битрикс)',
	 'jquery2' => 'v2.1.3 (ядро Битрикс)',
);
$MESS['EMAIL_TO']              = 'Email для уведомлений';
$MESS['EMAIL_TO_TIP']          = 'Email администратора, куда будут отправляться все уведомления компонента';
$MESS['SHOP_NAME']             = 'Название магазина';
$MESS['SHOP_NAME_DEFAULT']     = ToUpper($_SERVER['SERVER_NAME']);
$MESS['IBLOCK_ID']             = 'ID инфоблока';
$MESS['IBLOCK_ID_TIP']         = 'К заданному ID будут привязываться все новые отзывы.<br>Если вы не разработчик, просто оставьте это поле пустым.<br>Только число';
$MESS['SECTION_ID']            = 'ID раздела';
$MESS['SECTION_ID_TIP']        = 'К заданному ID будут привязываться все новые отзывы.<br>Только число.';
$MESS['ELEMENT_ID']            = 'ID элемента';
$MESS['ELEMENT_ID_TIP']        = 'К заданному ID будут привязываться все новые отзывы.<br>Только число.';
$MESS['ORDER_ID']              = 'ID заказа';
$MESS['ORDER_ID_TIP']          = 'К заданному ID будут привязываться все новые отзывы.<br>Только число.';
$MESS['URL']                   = 'URL страницы';
$MESS['URL_TIP']               = 'К заданному URL будут привязываться все новые отзывы. Хоть число, хоть строка, в БД это поле типа TEXT';


//REVIEWS_FILTER


//REVIEWS_FORM
$MESS['FORM_CITY_VIEW']    = 'Город в виде строки';
$MESS['FORM_BASE_FIELDS']  = array(
	 'RATING'       => 'Рейтинг',
	 'ORDER_ID'     => '№ заказа',
	 'TITLE'        => 'Заголовок',
	 'COMPANY'      => 'Компания',
	 'WEBSITE'      => 'Веб-сайт',
	 'ADVANTAGE'    => 'Достоинства',
	 'DISADVANTAGE' => 'Недостатки',
	 'ANNOTATION'   => 'Комментарий',
	 'FILES'        => 'Файлы',
	 'VIDEOS'       => 'Видео',
);
$MESS['FORM_DELIVERY']     = 'Служба доставки';
$MESS['FORM_DELIVERY_TIP'] = 'Если не выбирать доставки, то автоматичиски будут выводиться в форме все активные, иначе только выбранные';
$MESS['FORM_DOP_FIELDS']   = array(
	 'DELIVERY' => 'Доставка',
);
$MESS['FORM_GUEST_FIELDS'] = array(
	 'GUEST_NAME'  => 'Имя гостя',
	 'GUEST_EMAIL' => 'E-mail гостя',
	 'GUEST_PHONE' => 'Телефон гостя',
	 'CITY'        => 'Город',
);

//BASE_MESS
$MESS['REVIEWS_BASE_MESS']               = 'Основные фразы';
$MESS['USE_MESS_FIELD_NAME']             = 'Заменить встроенные названия полей на свои';
$MESS['USE_FORM_MESS_FIELD_PLACEHOLDER'] = 'Заменить встроенные placeholder полей на свои';
$MESS['DISPLAY_FIELDS_NAME_MESS']        = array(
	 'RATING'       => 'Рейтинг',
	 'ORDER_ID'     => '№ заказа',
	 'TITLE'        => 'Заголовок',
	 'COMPANY'      => 'Компания',
	 'WEBSITE'      => 'Веб-сайт',
	 'ADVANTAGE'    => 'Достоинства',
	 'DISADVANTAGE' => 'Недостатки',
	 'ANNOTATION'   => 'Комментарий',
	 'DELIVERY'     => 'Доставка',
	 'GUEST_NAME'   => 'Имя гостя',
	 'GUEST_EMAIL'  => 'E-mail гостя',
	 'GUEST_PHONE'  => 'Телефон гостя',
	 'CITY'         => 'Город',
	 'FILES'        => 'Файлы',
	 'VIDEOS'       => 'Видео',
);


//EULA
$MESS['FORM_USE_EULA']                  = 'Выводить условия Пользовательского соглашения';
$MESS['FORM_MESS_EULA']                 = 'Пользовательское соглашение';
$MESS['FORM_MESS_EULA_DEFAULT']         = 'Нажимая кнопку «Отправить отзыв», я принимаю условия Пользовательского соглашения и даю своё согласие на обработку моих персональных данных, в соответствии с Федеральным законом от 27.07.2006 года №152-ФЗ «О персональных данных», на условиях и для целей, определенных Политикой конфиденциальности.';
$MESS['FORM_MESS_EULA_CONFIRM']         = 'Сообщение о необходимости принять Пользовательское соглашения';
$MESS['FORM_MESS_EULA_CONFIRM_DEFAULT'] = 'Для продолжения вы должны принять условия Пользовательского соглашения';

//PRIVACY
$MESS['FORM_USE_PRIVACY']                  = 'Выводить соглашение на обработку персональных данных';
$MESS['FORM_MESS_PRIVACY']                 = 'Пользовательское соглашение';
$MESS['FORM_MESS_PRIVACY_DEFAULT']         = 'Я согласен на обработку персональных данных';
$MESS['FORM_MESS_PRIVACY_LINK']            = 'Ссылка на соглашение';
$MESS['FORM_MESS_PRIVACY_CONFIRM']         = 'Сообщение о необходимости принять соглашение';
$MESS['FORM_MESS_PRIVACY_CONFIRM_DEFAULT'] = 'Для продолжения вы должны принять соглашение на обработку персональных данных';


$MESS['FORM_SHOP_TEXT']             = 'Фраза над кнопкой "Добавить отзыв"';
$MESS['FORM_SHOP_TEXT_DEFAULT']     = 'Отзывы о магазине ' . ToUpper($_SERVER['SERVER_NAME']);
$MESS['FORM_SHOP_BTN_TEXT']         = 'Фраза кнопки "Добавить отзыв"';
$MESS['FORM_SHOP_BTN_TEXT_DEFAULT'] = 'Оставить свой отзыв';
$MESS['FORM_FORM_TITLE']            = 'Заголовок формы';
$MESS['FORM_FORM_TITLE_TIP']        = 'Например: Новый отзыв';
$MESS['FORM_FORM_SUBTITLE']         = 'Подзаголовок формы';
$MESS['FORM_FORM_SUBTITLE_TIP']     = 'В коде через ключ FORM_FORM_SUBTITLE можно подставить название товара, например:<br>
"FORM_FORM_SUBTITLE" => $arResult["NAME"] 
';
$MESS['FORM_FORM_TITLE_DEFAULT']    = 'Отзыв о магазине ' . ToUpper($_SERVER['SERVER_NAME']);
$MESS['FORM_PREMODERATION']         = 'Премодерация новых отзывов';
$MESS['FORM_PREMODERATION_VALUES']  = array('N' => 'Нет', 'Y' => 'Все отзывы', 'A' => 'Только анонимные');
$MESS['FORM_DISPLAY_FIELDS']        = 'Поля формы';
$MESS['FORM_REQUIRED_FIELDS']       = 'Обязательные поля';

$MESS['FORM_RULES_TEXT']         = 'Фраза ссылки правил публикации отзывов';
$MESS['FORM_RULES_TEXT_DEFAULT'] = 'Правила публикации отзывов';
$MESS['FORM_RULES_LINK']         = 'Ссылка на страницу с правилами';
$MESS['FORM_RULES_LINK_DEFAULT'] = 'http://' . $_SERVER['SERVER_NAME'] . '/rules/';

$MESS['FORM_MESS_ADD_REVIEW_VIZIBLE']            = 'Сообщение «Отзыв добавлен и опубликован»';
$MESS['FORM_MESS_ADD_REVIEW_VIZIBLE_DEFAULT']    = 'Спасибо!<br>Ваш отзыв №#ID# опубликован';
$MESS['FORM_MESS_ADD_REVIEW_MODERATION']         = 'Сообщение «Отзыв отправлен на модерацию»';
$MESS['FORM_MESS_ADD_REVIEW_MODERATION_DEFAULT'] = 'Спасибо!<br>Ваш отзыв отправлен на модерацию';
$MESS['FORM_MESS_ADD_REVIEW_ERROR']              = 'Сообщение «Ошибка добавления отзыва»';
$MESS['FORM_MESS_ADD_REVIEW_ERROR_DEFAULT']      = 'Внимание!<br>Ошибка добавления отзыва';

$MESS['FORM_MESS_STAR_RATING_1']         = 'Фраза «Оценка 1»';
$MESS['FORM_MESS_STAR_RATING_1_DEFAULT'] = 'Ужасный магазин';
$MESS['FORM_MESS_STAR_RATING_2']         = 'Фраза «Оценка 2»';
$MESS['FORM_MESS_STAR_RATING_2_DEFAULT'] = 'Плохой магазин';
$MESS['FORM_MESS_STAR_RATING_3']         = 'Фраза «Оценка 3»';
$MESS['FORM_MESS_STAR_RATING_3_DEFAULT'] = 'Обычный магазин';
$MESS['FORM_MESS_STAR_RATING_4']         = 'Фраза «Оценка 4»';
$MESS['FORM_MESS_STAR_RATING_4_DEFAULT'] = 'Хороший магазин';
$MESS['FORM_MESS_STAR_RATING_5']         = 'Фраза «Оценка 5»';
$MESS['FORM_MESS_STAR_RATING_5_DEFAULT'] = 'Отличный магазин';

$MESS['FORM_MESS_ADD_REVIEW_EVENT_THEME']         = 'Тема письма для нового отзыва';
$MESS['FORM_MESS_ADD_REVIEW_EVENT_THEME_DEFAULT'] = 'Отзыв о магазине ' . ToUpper($_SERVER['SERVER_NAME']) . ' (оценка: #RATING#) ##ID#';
$MESS['FORM_MESS_ADD_REVIEW_EVENT_TEXT']          = 'Текст письма для нового отзыва';
$MESS['FORM_MESS_ADD_REVIEW_EVENT_TEXT_DEFAULT']  = '<p>#USER_NAME# добавил(а) новый отзыв (оценка: #RATING#) ##ID#</p>
<p>Открыть в админке #LINK_ADMIN#</p>
<p>Открыть на сайте #LINK#</p>';

$MESS['FORM_MESS_ADD_REVIEW_EVENT_TEXT_TIP'] = 'Доступны следующие макросы:<br>
#EMAIL_FROM# - От кого<br>
#EMAIL_TO# - Кому<br>
#THEME# - Тема письма<br>
#WORK_AREA# - Текст письма<br>
#LINK# - Ссылка на отзыв<br>
#LINK_ADMIN# - Ссылка на отзыв в админке<br>
#ID# - ID отзыва<br>
#RATING# - Рейтинг<br>
#USER_NAME# - Имя пользователя<br>
#USER_ID# - ID пользователя<br>
#PAGE_URL# -  Адрес страницы<br>
#PAGE_TITLE# - Заголовок страницы<br>
#SITE_NAME# - Название сайта<br>
#SITE_HOST# - Домен сайта<br>
';

$MESS['FORM_MESS_ADD_REVIEW_EVENT_THEME_TIP'] = $MESS['FORM_MESS_ADD_REVIEW_EVENT_TEXT_TIP'];


//REVIEWS_LIST
$MESS['LIST_SHOP_NAME_REPLY']         = 'Название магазина для ответа';
$MESS['LIST_SHOP_NAME_REPLY_DEFAULT'] = 'Интернет-магазин ' . ToUpper($_SERVER['SERVER_NAME']);
$MESS['LIST_PAGER_TITLE']             = 'Отзывы';
$MESS['LIST_ITEMS_LIMIT']             = 'Элементов на странице';
$MESS['LIST_ACTIVE_DATE_FORMAT']      = 'Формат показа даты';
$MESS['LIST_SET_TITLE']               = 'Устанавливать заголовок страницы';
$MESS['LIST_SHOW_THUMBS']             = 'Выводить лайки';
$MESS['LIST_SORT_FIELD_1']            = 'Поле первой сортировки';
$MESS['LIST_SORT_ORDER_1']            = 'Направление первой сортировки';
$MESS['LIST_SORT_FIELD_2']            = 'Поле второй сортировки';
$MESS['LIST_SORT_ORDER_2']            = 'Направление второй сортировки';
$MESS['LIST_SORT_FIELD_3']            = 'Поле третьей сортировки';
$MESS['LIST_SORT_ORDER_3']            = 'Направление третьей сортировки';
$MESS['LIST_SORT']                    = array(
	 'ID'          => 'ID (уникальный айди)',
	 'ACTIVE_FROM' => 'Начало активности',
	 'DATE_CREATE' => 'Дата создания',
	 'RATING'      => 'Оценка (Рейтинг)',
	 'THUMBS_UP'   => 'Полезность +',
	 'THUMBS_DOWN' => 'Полезность -',
	 'REPLY'       => 'С ответом',
);
$MESS['LIST_ORDER']                   = array(
	 'ASC'  => 'По возрастанию',
	 'DESC' => 'По убыванию',
);
$MESS['LIST_SORT_FIELDS']             = 'Поля внешней сортировки';
$MESS['LIST_SORT_FIELDS_VALUES']      = array(
	 ''            => '-- Не выбрано --',
	 'ACTIVE'      => 'Активности',
	 'ACTIVE_FROM' => 'Дате',
	 'RATING'      => 'Рейтингу',
	 'THUMBS'      => 'Полезности',
);
$MESS['LIST_SORT_FIELDS_DEFAULT']     = array('ACTIVE', 'ACTIVE_FROM', 'RATING', 'THUMBS');


$MESS['LIST_ALLOW']        = 'Заменять в тексте отзыва';
$MESS['LIST_ALLOW_VALUES'] = array(
	 ''       => $MESS['CHOOSE'],
	 'ANCHOR' => 'URL-адреса на кликабельные ссылки',
);

$MESS['PICTURE']                = 'Изображение товара';
$MESS['PICTURE_TIP']            = 'Включайте поиск изображений только если отзывы привязываются к товарам';
$MESS['RESIZE_PICTURE']         = 'Ресайз изображения, XxY';
$MESS['RESIZE_PICTURE_TIP']     = '48x48 - разные ограничения сторон.<br>48 - одинаковое ограничение по обеим сторонам';
$MESS['RESIZE_PICTURE_DEFAULT'] = '48x48';
$MESS['PICTURE_VALUES']         = array(
	 ''                => '-- Не выбрано --',
	 'PREVIEW_PICTURE' => 'Картинка для анонса',
	 'DETAIL_PICTURE'  => 'Детальная картинка',
);


//REVIEWS_DETAIL
$MESS['REVIEWS_DETAIL']  = 'Отзыв детально';
$MESS['USE_LIST']        = 'Не выводить отзыв детально';
$MESS['USE_LIST_TIP']    = 'Пригодится, когда отзывы размещены в карточке товара и при переходе по детальной ссылке сработает прокрутка к отзыву, а не переход на детальную страницу';
$MESS['DETAIL_HASH']     = '#Якорь детальной ссылки отзыва';
$MESS['DETAIL_HASH_TIP'] = 'Пригодится, когда отзывы размещены в карточке товара в переключаемых табах с #якорем, например:<br>#tab_reviews';


//REVIEWS_USER
$MESS['REVIEWS_USER'] = 'Отзывы пользователя';
$MESS['USE_USER']     = 'Включить';


//REVIEWS_STAT
$MESS['REVIEWS_STAT']                       = 'Статистика по отзывам';
$MESS['USE_STAT']                           = 'Выводить статистику';
$MESS['STAT_MESS_CUSTOMER_REVIEWS']         = 'Фраза «Отзывы покупателей (X)»';
$MESS['STAT_MESS_CUSTOMER_REVIEWS_DEFAULT'] = 'Отзывы покупателей <span class="api-reviews-count"></span>';
$MESS['STAT_MESS_TOTAL_RATING']             = 'Фраза «Рейтинг покупателей»';
$MESS['STAT_MESS_TOTAL_RATING_DEFAULT']     = 'Рейтинг покупателей';
$MESS['STAT_MESS_CUSTOMER_RATING']          = 'Фраза «На основе #N# оценок покупателей»';
$MESS['STAT_MESS_CUSTOMER_RATING_DEFAULT']  = 'На основе #N# оценок покупателей';
$MESS['STAT_MIN_AVERAGE_RATING']            = 'Минимальное значение рейтинга для микроразметки';
$MESS['STAT_MIN_AVERAGE_RATING_TIP']        = 'Это поле скрыто, но поисковики его видят, обязательно рейтинг должны быть указан';


//REVIEWS_SUBSCRIBE
$MESS['REVIEWS_SUBSCRIBE']                        = 'Подписка на отзывы';
$MESS['USE_SUBSCRIBE']                            = 'Включить';
$MESS['SUBSCRIBE_AJAX_URL']                       = 'Адрес AJAX-обработчика подписки';
$MESS['SUBSCRIBE_AJAX_URL_TIP']                   = 'Можете указать путь до своего AJAX-обработчика';
$MESS['SUBSCRIBE_AJAX_URL_DEFAULT']               = '/bitrix/components/api/reviews.subscribe/ajax.php';
$MESS['MESS_SUBSCRIBE_LINK']                      = 'Текст ссылки «Подписаться на новые отзывы»';
$MESS['MESS_SUBSCRIBE_LINK_DEFAULT']              = 'Подписаться на новые отзывы';
$MESS['MESS_SUBSCRIBE_FIELD_PLACEHOLDER']         = 'Текст поля «Введите свой e-mail»';
$MESS['MESS_SUBSCRIBE_FIELD_PLACEHOLDER_DEFAULT'] = 'Введите свой e-mail';
$MESS['MESS_SUBSCRIBE_BUTTON_TEXT']               = 'Текст кнопки «Подписаться»';
$MESS['MESS_SUBSCRIBE_BUTTON_TEXT_DEFAULT']       = 'Подписаться';
$MESS['MESS_SUBSCRIBE_SUBSCRIBE']                 = 'Сообщение об успешной подписке';
$MESS['MESS_SUBSCRIBE_SUBSCRIBE_DEFAULT']         = 'Вы успешно подписались!';
$MESS['MESS_SUBSCRIBE_UNSUBSCRIBE']               = 'Сообщение об успешной отписке';
$MESS['MESS_SUBSCRIBE_UNSUBSCRIBE_DEFAULT']       = 'Вы успешно отписались!';
$MESS['MESS_SUBSCRIBE_ERROR']                     = 'Сообщение о неизвестной ошибке';
$MESS['MESS_SUBSCRIBE_ERROR_DEFAULT']             = 'Ошибка изменения подписки!';
$MESS['MESS_SUBSCRIBE_ERROR_EMAIL']               = 'Сообщение о пустом поле e-mail';
$MESS['MESS_SUBSCRIBE_ERROR_EMAIL_DEFAULT']       = 'Укажите e-mail';
$MESS['MESS_SUBSCRIBE_ERROR_CHECK_EMAIL']         = 'Сообщение о некорректном e-mail';
$MESS['MESS_SUBSCRIBE_ERROR_CHECK_EMAIL_DEFAULT'] = 'Указанный e-mail некорректен!';


//REVIEWS_BASE_MESS
$MESS['LIST_MESS_ADD_UNSWER_EVENT_THEME']         = 'Тема письма для ответа';
$MESS['LIST_MESS_ADD_UNSWER_EVENT_THEME_DEFAULT'] = 'Официальный ответ к вашему отзыву';
$MESS['LIST_MESS_ADD_UNSWER_EVENT_TEXT']          = 'Текст письма для ответа';
$MESS['LIST_MESS_ADD_UNSWER_EVENT_TEXT_DEFAULT']  = '#USER_NAME#, здравствуйте! 
К Вашему отзыву добавлен официальный ответ, для просмотра перейдите по ссылке #LINK#';
$MESS['LIST_MESS_ADD_UNSWER_EVENT_TEXT_TIP']      = 'Доступны следующие макросы:<br>
#EMAIL_FROM# - От кого<br>
#EMAIL_TO# - Кому<br>
#THEME# - Тема письма<br>
#WORK_AREA# - Текст письма<br>
#LINK# - Ссылка на отзыв<br>
#ID# - ID отзыва<br>
#RATING# - Рейтинг<br>
#USER_NAME# - Имя пользователя<br>
#USER_ID# - ID пользователя<br>
#PAGE_URL# -  Адрес страницы<br>
#PAGE_TITLE# - Заголовок страницы<br>
#SITE_NAME# - Название сайта<br>
#SITE_HOST# - Домен сайта<br>
';
$MESS['LIST_MESS_ADD_UNSWER_EVENT_THEME_TIP']     = $MESS['LIST_MESS_ADD_UNSWER_EVENT_TEXT_TIP'];

$MESS['LIST_MESS_TRUE_BUYER']             = 'Фраза "Проверенный покупатель"';
$MESS['LIST_MESS_TRUE_BUYER_TIP']         = 'Покупатель, заказ которого выполнен';
$MESS['LIST_MESS_TRUE_BUYER_DEFAULT']     = 'Проверенный покупатель';
$MESS['LIST_MESS_HELPFUL_REVIEW']         = 'Фраза "Отзыв полезен?"';
$MESS['LIST_MESS_HELPFUL_REVIEW_DEFAULT'] = 'Отзыв полезен?';


//CACHE
$MESS['CACHE_TIME_TIP'] = '86400 это сутки = 24ч.';

//PAGER
$MESS['ARP_PAGER_TITLE'] = 'Отзывы';


//FILE_SETTINGS
$MESS['FILES_SETTINGS']          = 'Загрузка файлов';
$MESS['UPLOAD_FILE_TYPE']        = 'Типы загружаемых файлов (расширения через запятую)';
$MESS['UPLOAD_FILE_TYPE_VALUES'] = array(
	 ''                                                                                                    => '(любые)',
	 'jpg, gif, bmp, png, jpeg'                                                                            => 'Изображения',
	 'mp3, wav, midi, snd, au, wma, ogg, aac, flac, cda'                                                   => 'Звуки',
	 'mpg, avi, wmv, mpeg, mpe, flv, mkv, mov, wma, mp4, xvid, asf, divx, vob, swf'                        => 'Видео',
	 'doc, docx, xls, xlsx, ppt, pptx, pub, txt, csv, xml, rtf, odt, ods, odp, pdf, djvu, xps, epub, tiff' => 'Документы',
);

$MESS['UPLOAD_FOLDER']          = 'Временная директория, используемая для хранения файлов во время закачивания';
$MESS['UPLOAD_FOLDER_TIP']      = 'После отправки формы файлы из временной директории удаляются';
$MESS['UPLOAD_FILE_SIZE']       = 'Максимальный размер загружаемого файла - ' . ini_get('upload_max_filesize');
$MESS['UPLOAD_FILE_SIZE_TIP']   = 'K - килобайт<br>М - мегабайт<br>G - гигабайт<br>T - терабайт';
$MESS['UPLOAD_FILE_LIMIT']      = 'Максимальное количество загружаемых файлов - ' . ini_get('max_file_uploads');
$MESS['UPLOAD_FILE_LIMIT_TIP']  = '0 - без ограничений';
$MESS['UPLOAD_VIDEO_LIMIT']     = 'Максимальное количество загружаемых видео';
$MESS['UPLOAD_VIDEO_LIMIT_TIP'] = '0 - без ограничений';

$MESS['THUMBNAIL_WIDTH']  = 'Ширина превью';
$MESS['THUMBNAIL_HEIGHT'] = 'Высота превью';


//VIDEOS_SETTINGS
$MESS['VIDEOS_SETTINGS'] = 'Загрузка видео';


/*
$MESS['LIST_ALLOW_VALUES'] = array(
	'HTML' => ($allow['HTML'] == 'Y' ? 'Y' : 'N'),
	'NL2BR' => ($allow['NL2BR'] == 'Y' ? 'Y' : 'N'),
	'CODE' => ($allow['CODE'] == 'N' ? 'N' : 'Y'),
	'VIDEO' => ($allow['VIDEO'] == 'N' ? 'N' : 'Y'),
	'ANCHOR' => ($allow['ANCHOR'] == 'N' ? 'N' : 'Y'),
	'BIU' => ($allow['BIU'] == 'N' ? 'N' : 'Y'),
	'IMG' => ($allow['IMG'] == 'N' ? 'N' : 'Y'),
	'QUOTE' => ($allow['QUOTE'] == 'N' ? 'N' : 'Y'),
	'FONT' => ($allow['FONT'] == 'N' ? 'N' : 'Y'),
	'LIST' => ($allow['LIST'] == 'N' ? 'N' : 'Y'),
	'SMILES' => ($allow['SMILES'] == 'N' ? 'N' : 'Y'),
	'TABLE' => ($allow['TABLE'] == 'N' ? 'N' : 'Y'),
	'ALIGN' => ($allow['ALIGN'] == 'N' ? 'N' : 'Y'),
	'CUT_ANCHOR' => ($allow['CUT_ANCHOR'] == 'Y' ? 'Y' : 'N'),
	'SHORT_ANCHOR' => ($allow['SHORT_ANCHOR'] == 'Y' ? 'Y' : 'N'),
	'USER' => ($allow['USER'] == 'N' ? 'N' : 'Y'),
	'TAG' => ($allow['TAG'] == 'N' ? 'N' : 'Y'),
);
*/
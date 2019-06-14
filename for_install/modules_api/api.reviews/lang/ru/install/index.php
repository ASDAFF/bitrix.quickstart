<?
$MESS['ARII_MODULE_NAME']  = 'TS Умные отзывы о магазине и о товаре';
$MESS['ARII_MODULE_DESC']  = 'Cистема отзывов о магазине, о товаре, о разделе и о странице';
$MESS['ARII_PARTNER_NAME'] = 'Тюнинг-Софт';
$MESS['ARII_PARTNER_URI']  = 'https://tuning-soft.ru';


//---------- Events ----------//
$MESS['ARII_EVENT_TYPE'] = array(
	 array(
			'EVENT_NAME'  => 'API_REVIEWS_ADD',
			'NAME'        => 'Новый отзыв',
			'DESCRIPTION' => '=== Макросы модуля ===
#EMAIL_FROM# - От кого
#EMAIL_TO# - Кому
#THEME# - Тема письма
#WORK_AREA# - Текст письма
#LINK# - Ссылка на отзыв в публичке
#LINK_ADMIN# - Ссылка на отзыв в админке
#ID# - ID отзыва
#RATING# - Рейтинг
#USER_NAME# - Имя пользователя
#USER_ID# - ID пользователя
#PAGE_URL# -  Адрес страницы
#PAGE_TITLE# - Заголовок страницы
#SITE_NAME# - Название сайта
#SITE_HOST# - Домен сайта

=== Макросы системы ===',
	 ),
	 array(
			'EVENT_NAME'  => 'API_REVIEWS_SUBSCRIBE',
			'NAME'        => 'Подписка на новые отзывы',
			'DESCRIPTION' => '=== Макросы модуля ===
#EMAIL_FROM# - От кого
#EMAIL_TO# - Кому
#PAGE_URL# -  Адрес страницы
#PAGE_TITLE# - Заголовок страницы
#ID# - ID отзыва
#RATING# - Рейтинг
#SITE_NAME# - Название сайта
#SITE_HOST# - Адрес сайта

=== Макросы системы ===',
	 ),
	 array(
			'EVENT_NAME'  => 'API_REVIEWS_REPLY',
			'NAME'        => 'Официальный ответ к вашему отзыву',
			'DESCRIPTION' => '=== Макросы модуля ===
#EMAIL_FROM# - От кого
#EMAIL_TO# - Кому
#THEME# - Тема письма
#WORK_AREA# - Текст письма
#LINK# - Ссылка на отзыв
#ID# - ID отзыва
#RATING# - Рейтинг
#USER_NAME# - Имя пользователя
#USER_ID# - ID пользователя
#PAGE_URL# -  Адрес страницы
#PAGE_TITLE# - Заголовок страницы
#SITE_NAME# - Название сайта
#SITE_HOST# - Домен сайта

=== Макросы системы ===',
	 ),
);

$MESS['ARII_EVENT_MESSAGE'] = array(
	 array(
			'ACTIVE'      => 'Y',
			'EVENT_NAME'  => 'API_REVIEWS_ADD',
			//'LID'        => 's1',
			//'LANGUAGE_ID' => 'ru',
			'EMAIL_FROM'  => '#EMAIL_FROM#',
			'EMAIL_TO'    => '#EMAIL_TO#',
			'SUBJECT'     => '#THEME#',
			'BODY_TYPE'   => 'html',
			'MESSAGE'     => '#WORK_AREA#',
	 ),
	 array(
			'ACTIVE'      => 'Y',
			'EVENT_NAME'  => 'API_REVIEWS_SUBSCRIBE',
			//'LID'        => 's1',
			//'LANGUAGE_ID' => 'ru',
			'EMAIL_FROM'  => '#EMAIL_FROM#',
			'EMAIL_TO'    => '#EMAIL_TO#',
			'SUBJECT'     => '#SITE_NAME#: Новый отзыв (оценка: #RATING#) ##ID#',
			'BODY_TYPE'   => 'html',
			'MESSAGE'     => 'Здравствуйте!<br>
На сайте #SITE_NAME# добавлен новый отзыв, для просмотра перейдите по этой <a href="#PAGE_URL#">ссылке</a>
<hr>
<small>Вы получили это письмо, потому что подписывались на получение новых отзывов.<br>
Чтобы отписаться от рассылки перейдите по ссылке и заново введите этот email в форме подписки.<br>
</small>',
	 ),
	 array(
			'ACTIVE'      => 'Y',
			'EVENT_NAME'  => 'API_REVIEWS_REPLY',
			//'LID'        => 's1',
			//'LANGUAGE_ID' => 'ru',
			'EMAIL_FROM'  => '#EMAIL_FROM#',
			'EMAIL_TO'    => '#EMAIL_TO#',
			'SUBJECT'     => '#THEME#',
			'BODY_TYPE'   => 'html',
			'MESSAGE'     => '#WORK_AREA#',
	 ),
);

?>
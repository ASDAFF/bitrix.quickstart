<?
$MESS["api.qa_MODULE_NAME"]  = "TS Умные вопросы и ответы";
$MESS["api.qa_MODULE_DESC"]  = "Система вопросов, ответов и комментариев к товару";
$MESS["api.qa_PARTNER_NAME"] = "Тюнинг-Софт";
$MESS["api.qa_PARTNER_URI"]  = "https://tuning-soft.ru";



//---------- Events ----------//
$MESS['AQAII_EVENT_TYPE'] = array(
	 array(
			'EVENT_NAME'  => 'API_QA_QUESTION_ADD',
			'NAME'        => 'Новый вопрос',
			'DESCRIPTION' => '=== Макросы модуля ===
#EMAIL_FROM# - От кого (берется сначала из настроек сайте, если там нет, из настроек главного модуля)
#EMAIL_TO# - Кому (изменяется по условиям)
#BCC# - Скрытая копия
#ID# - ID вопроса
#USER_ID# - ID пользователя
#GUEST_NAME# - Имя пользователя
#GUEST_EMAIL# - Email пользователя
#TEXT# - Текст вопроса
#AUTHOR_NAME# - Имя автора
#LINK# - html-cсылка на вопрос/ответ/комментарий
#URL# - Ссылка на вопрос/ответ/комментарий
#PAGE_URL# -  Ссылка на товар/модель/элемент
#PAGE_TITLE# - Наименование товара/модели/элемента
#SITE_NAME# - Название сайта
#SITE_HOST# - Адрес сайта с http(s)

=== Макросы системы ===',
	 ),
	 array(
			'EVENT_NAME'  => 'API_QA_ANSWER_ADD',
			'NAME'        => 'Новый ответ',
			'DESCRIPTION' => '=== Макросы модуля ===
#EMAIL_FROM# - От кого (берется сначала из настроек сайте, если там нет, из настроек главного модуля)
#EMAIL_TO# - Кому (изменяется по условиям)
#BCC# - Скрытая копия
#ID# - ID вопроса
#USER_ID# - ID пользователя
#GUEST_NAME# - Имя пользователя
#GUEST_EMAIL# - Email пользователя
#TEXT# - Текст вопроса
#AUTHOR_NAME# - Имя автора
#LINK# - html-cсылка на вопрос/ответ/комментарий
#URL# - Ссылка на вопрос/ответ/комментарий
#PAGE_URL# -  Ссылка на товар/модель/элемент
#PAGE_TITLE# - Наименование товара/модели/элемента
#SITE_NAME# - Название сайта
#SITE_HOST# - Адрес сайта с http(s)

=== Макросы системы ===',
	 ),
	 array(
			'EVENT_NAME'  => 'API_QA_COMMENT_ADD',
			'NAME'        => 'Новый комментарий',
			'DESCRIPTION' => '=== Макросы модуля ===
#EMAIL_FROM# - От кого (берется сначала из настроек сайте, если там нет, из настроек главного модуля)
#EMAIL_TO# - Кому (изменяется по условиям)
#BCC# - Скрытая копия
#ID# - ID вопроса
#USER_ID# - ID пользователя
#GUEST_NAME# - Имя пользователя
#GUEST_EMAIL# - Email пользователя
#TEXT# - Текст вопроса
#AUTHOR_NAME# - Имя автора
#LINK# - html-cсылка на вопрос/ответ/комментарий
#URL# - Ссылка на вопрос/ответ/комментарий
#PAGE_URL# -  Ссылка на товар/модель/элемент
#PAGE_TITLE# - Наименование товара/модели/элемента
#SITE_NAME# - Название сайта
#SITE_HOST# - Адрес сайта с http(s)

=== Макросы системы ===',
	 ),
);

$MESS['AQAII_EVENT_MESSAGE'] = array(
	 array(
			'ACTIVE'     => 'Y',
			'EVENT_NAME' => 'API_QA_QUESTION_ADD',
			//'LID'        => 's1',
			//'LANGUAGE_ID' => 'ru',
			'EMAIL_FROM' => '#EMAIL_FROM#',
			'EMAIL_TO'   => '#EMAIL_TO#',
			'SUBJECT'    => '[Q] #PAGE_TITLE# - #GUEST_NAME# добавил(а) вопрос ##ID#',
			'BODY_TYPE'  => 'html',
			'MESSAGE'    => '#GUEST_NAME# добавил(а) вопрос ##ID# к модели<br>
<a href="#PAGE_URL#">#PAGE_TITLE#</a><br>
<br>
=============================================<br>
#TEXT#<br>
=============================================<br>
<br>
Для просмотра перейдите по этой ссылке<br>
#LINK#',
	 ),
	 array(
			'ACTIVE'     => 'Y',
			'EVENT_NAME' => 'API_QA_ANSWER_ADD',
			//'LID'        => 's1',
			//'LANGUAGE_ID' => 'ru',
			'EMAIL_FROM' => '#EMAIL_FROM#',
			'EMAIL_TO'   => '#EMAIL_TO#',
			//'BCC'        => '#BCC#',
			'SUBJECT'    => '[A] Re: #PAGE_TITLE# - Ответ эксперта ##ID# ',
			'BODY_TYPE'  => 'html',
			'MESSAGE'    => '#AUTHOR_NAME#, здравствуйте!<br>
<br>
Вы получили ответ эксперта на ваш вопрос о модели<br>
<a href="#PAGE_URL#">#PAGE_TITLE#</a><br>
<br>
=============================================<br>
#TEXT#<br>
=============================================<br>
<br>
Возможно там есть и другие полезные для вас ответы, для просмотра перейдите по этой ссылке<br>
#LINK#',
	 ),
	 array(
			'ACTIVE'     => 'Y',
			'EVENT_NAME' => 'API_QA_COMMENT_ADD',
			//'LID'        => 's1',
			//'LANGUAGE_ID' => 'ru',
			'EMAIL_FROM' => '#EMAIL_FROM#',
			'EMAIL_TO'   => '#EMAIL_TO#',
			'BCC'        => '#BCC#',
			'SUBJECT'    => '[C] Re: #PAGE_TITLE# - #GUEST_NAME# прокомментировал(а) ваш вопрос ##ID#',
			'BODY_TYPE'  => 'html',
			'MESSAGE'    => '#AUTHOR_NAME#, здравствуйте!<br>
<br>
#GUEST_NAME# прокомментировал(а) ваш вопрос ##ID# о модели<br>
<a href="#PAGE_URL#">#PAGE_TITLE#</a><br>
<br>
=============================================<br>
#TEXT#<br>
=============================================<br>
<br>
Возможно там есть и другие полезные для вас ответы, для просмотра перейдите по этой ссылке<br>
#LINK#',
	 ),
);

?>
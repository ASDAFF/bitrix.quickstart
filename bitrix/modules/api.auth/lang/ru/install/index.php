<?
$MESS["api.auth_MODULE_NAME"]  = "TS Умная авторизация";
$MESS["api.auth_MODULE_DESC"]  = "Авторизация по e-mail и социальные сети";
$MESS["api.auth_PARTNER_NAME"] = "Тюнинг-Софт";
$MESS["api.auth_PARTNER_URI"]  = "https://tuning-soft.ru";


//---------- EVENT_TYPE ----------//
$MESS['API_AUTH_INSTALL_EVENT_TYPE'] = array(
	 array(
			'EVENT_NAME'  => 'API_AUTH_NEW_USER',
			'NAME'        => 'Регистрационная информация',
			'DESCRIPTION' => '',
	 ),
	 array(
			'EVENT_NAME'  => 'API_AUTH_NEW_USER_CONFIRM',
			'NAME'        => 'Подтверждение регистрации',
			'DESCRIPTION' => '',
	 ),
	 array(
			'EVENT_NAME'  => 'API_AUTH_CONFIRM',
			'NAME'        => 'Подтверждение учетной записи',
			'DESCRIPTION' => '',
	 ),
	 array(
			'EVENT_NAME'  => 'API_AUTH_RESTORE',
			'NAME'        => 'Вспомнить пароль',
			'DESCRIPTION' => '',
	 ),
	 array(
			'EVENT_NAME'  => 'API_AUTH_CHANGE',
			'NAME'        => 'Изменение пароля',
			'DESCRIPTION' => '',
	 ),
);

//---------- EVENT_MESSAGE ----------//
$MESS['API_AUTH_INSTALL_EVENT_MESSAGE'] = array(
	 array(
			'ACTIVE'     => 'Y',
			'EVENT_NAME' => 'API_AUTH_NEW_USER',
			//'LID'        => 's1',
			//'LANGUAGE_ID' => 'ru',
			'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
			'EMAIL_TO'   => '#EMAIL#',
			'SUBJECT'    => 'Регистрация на сайте #SITE_NAME#',
			'BODY_TYPE'  => 'html',
			'MESSAGE'    => '<p>Здравствуйте!</p>
<p>Вы успешно зарегистрированы на сайте #SITE_NAME#</p>
<p>Ваши данные для входа на сайт:</p>
<p>
E-mail: #EMAIL#<br>
Пароль: #PASSWORD#<br>
</p>
<p>
Сменить пароль можно по этой ссылке:<br>
<a href="#SERVER_URL#/login/?change=yes&lang=#LANGUAGE_ID#&USER_CHECKWORD=#CHECKWORD#&USER_LOGIN=#URL_LOGIN#">#SERVER_URL#/login/?change=yes&lang=#LANGUAGE_ID#&USER_CHECKWORD=#CHECKWORD#&USER_LOGIN=#URL_LOGIN#</a>
</p>
',
	 ),
	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 array(
			'ACTIVE'     => 'Y',
			'EVENT_NAME' => 'API_AUTH_NEW_USER_CONFIRM',
			'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
			'EMAIL_TO'   => '#EMAIL#',
			'SUBJECT'    => 'Подтверждение регистрации на сайте #SITE_NAME#',
			'BODY_TYPE'  => 'html',
			'MESSAGE'    => '<p>Здравствуйте!</p>
<p>Вы получили это сообщение, так как ваш адрес <strong>#EMAIL#</strong> был использован при регистрации на сайте #SITE_NAME#</p>
<p>Ваши данные для входа на сайт:</p>
<p>
E-mail: #EMAIL#<br>
Пароль: #PASSWORD#
</p>
<p>
Для подтверждения регистрации перейдите по следующей ссылке: <br>
<a href="#SERVER_URL#/login/?confirm=yes&uid=#USER_ID#&code=#CONFIRM_CODE#">#SERVER_URL#/login/?confirm=yes&uid=#USER_ID#&code=#CONFIRM_CODE#</a>
</p>
<p style="background-color: #ffeeee; padding: 15px; border-radius: 5px;">
Обратите внимание! <br>
Вы сможете пользоваться своей учётной записью только после подтверждения регистрации.
</p>',
	 ),

	 //NEW 2017
	 array(
		  'ACTIVE'     => 'Y',
		  'EVENT_NAME' => 'API_AUTH_CONFIRM',
		  'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
		  'EMAIL_TO'   => '#EMAIL#',
		  'SUBJECT'    => 'Подтверждение учетной записи на сайте #SITE_NAME#',
		  'BODY_TYPE'  => 'html',
		  'MESSAGE'    => '<p>Здравствуйте!</p>
<p>Вы получили это сообщение, так как ваш адрес <strong>#EMAIL#</strong> был использован при регистрации на сайте #SITE_NAME#</p>
<p>
Для подтверждения регистрации перейдите по следующей ссылке:<br>
<a href="#SERVER_URL#/login/?confirm=yes&uid=#USER_ID#&code=#CONFIRM_CODE#">#SERVER_URL#/login/?confirm=yes&uid=#USER_ID#&code=#CONFIRM_CODE#</a>
</p>
',
	 ),
	 array(
		  'ACTIVE'     => 'Y',
		  'EVENT_NAME' => 'API_AUTH_RESTORE',
		  'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
		  'EMAIL_TO'   => '#EMAIL#',
		  'SUBJECT'    => 'Вспомнить пароль на сайте #SITE_NAME#',
		  'BODY_TYPE'  => 'html',
		  'MESSAGE'    => '<p>Здравствуйте!</p>
<p>#MESSAGE#</p>
<p>
Для смены пароля перейдите по следующей ссылке:<br>
<a href="#SERVER_URL#/login/?change=yes&lang=ru&USER_CHECKWORD=#CHECKWORD#&USER_LOGIN=#URL_LOGIN#">#SERVER_URL#/login/?change=yes&lang=ru&USER_CHECKWORD=#CHECKWORD#&USER_LOGIN=#URL_LOGIN#</a>
</p>
',
	 ),
	 array(
		  'ACTIVE'     => 'Y',
		  'EVENT_NAME' => 'API_AUTH_CHANGE',
		  'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
		  'EMAIL_TO'   => '#EMAIL#',
		  'SUBJECT'    => 'Изменение пароля на сайте #SITE_NAME#',
		  'BODY_TYPE'  => 'html',
		  'MESSAGE'    => '<p>Здравствуйте!</p>
<p>#MESSAGE#</p>
',
	 ),
);
?>
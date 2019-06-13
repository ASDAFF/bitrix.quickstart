<?
/* main */
$MESS ['SELECT_SERVICE'] = "< отправка смс выключена >";
$MESS ['SMSBLISS'] = "SMS-Bliss";
$MESS ['SMSKONTAKT'] = "SMS-Kontakt";
$MESS ['OPT_SMS_SERVICE']	= "Использовать для отправки СМС сервис";
$MESS ['SUPPORT']			= "Техническая поддержка: support@sozdavatel.ru";
$MESS ['ERR_MODULE_OFF']	= "Отправка СМС выключена";
$MESS ['RUB'] = "руб.";
$MESS ['SMS'] = "СМС";
$MESS ['SMS_DEFAULT_RECIEVER_PHONE'] = "Получатель по умолчанию (79001112233)";

$MESS ['COPY_SMS_TO_EMAIL_PHONE'] = "Укажите номер телефона, смс на который нужно дублировать по эл. почте. Не заполняйте, если дублировать все смс. (79001112233)";
$MESS ['COPY_SMS_TO_EMAIL_EMAIL'] = "Укажите адрес эл. почты, на который отправлять копии смс-сообщений. Оставьте пустым, чтобы отключить дублирование смс по эл. почте";

$MESS ['SMS_SEND_COPY_TO_DEFAULT_RECIEVER'] = "Дублировать все СМС получателю по умолчанию";

$MESS ['TAB_MAIN_TEXT'] = 	"1. Выберите сервис для отправки СМС.<br/>".
							"2. Укажите телефон получателя по умолчанию. На этот номер будут отправляться все смс, для которых не указан получатель.<br/>".
							"3. При необходимости включите отправку копий всех СМС на номер получателя по умолчанию";
							
$MESS ['ERR_PHP_JSON_NOT_FOUND'] = "Для отправки СМС необходим установленный модуль PHP JSON. Обратитесь к системному администратору.";

$MESS ['ERR_PHP_CURL_NOT_FOUND'] = "Для отправки СМС необходим установленный модуль PHP CURL. Обратитесь к системному администратору.";

$MESS ['TAB_MAIN'] = "Основные настройки";
$MESS ['TAB_MAIN_TITLE'] = "Основные настройки";

$MESS ['TAB_SMSKONTAKT'] = "SMS-Kontakt";
$MESS ['TAB_SMSKONTAKT_TITLE'] = "Настройки сервиса SMS-Kontakt";

$MESS ['TAB_SMSBLISS'] = "SMS-Bliss";
$MESS ['TAB_SMSBLISS_TITLE'] = "Настройки сервиса SMS-Bliss";

$MESS ['TAB_EXAMPLE'] = "Справка";
$MESS ['TAB_EXAMPLE_TITLE'] = "Описание модуля и примеры использования";

$MESS ['TAB_RIGHTS'] = "Доступ";
$MESS ['TAB_RIGHTS_TITLE'] = "Доступ";

$MESS ['TAB_LOG'] = "Логи";
$MESS ['TAB_LOG_TITLE'] = "Логи ошибок";

/* sms-bliss */
$MESS ['BLISS_GERFORM_INFO'] = "Чтобы получить логин и пароль, отправьте заявку на подключение";
$MESS ['BLISS_USER_COMPANY'] = "Компания";
$MESS ['BLISS_USER_NAME'] = "ФИО";
$MESS ['BLISS_USER_PHONE'] = "Телефон";
$MESS ['BLISS_USER_EMAIL'] = "E-Mail";
$MESS ['BLISS_REGISTER_SUBMIT'] = "Отправить заявку";
$MESS ['BLISS_REQUIRED_HINT'] = "*Поля, обязательные для заполнения";

$MESS ['ERR_SMSBLISS_INCORRECT_LOGIN'] = "Некорректный логин. Пожалуйста, отправьте заявку на подключение и введите логин и пароль, которые вы получите в ответном письме после обработки вашей заявки.";

$MESS ['SMSBLISS_ALL_REQUIRED'] = "Пожалуйста, заполните все обязательные поля";
$MESS ['SMSBLISS_REGISTER_OK'] = "Ваша заявка принята. После обработки вашего запроса логин и пароль будут высланы на e-mail, указанный в заявке.";
$MESS ['SMSBLISS_REGISTER_OK2'] = "Укажите полученный логин и пароль ниже и нажмите <Сохранить>.";
$MESS ['SMSBLISS_REGISTER_ERROR'] = "К сожалению, заявка не была отправлена. Проверьте, подключен ли сервер к интернету и попробуйте еще раз.";

$MESS ['SMSBLISS_OPT_LOGIN'] = "Логин";
$MESS ['SMSBLISS_OPT_PASSWORD'] = "Пароль";
$MESS ['SMSBLISS_OPT_SENDER_ID'] = "Подпись отправителя";
$MESS ['SMSBLISS_BALANCE'] = "Ваш баланс";
$MESS ['SMSBLISS_BALANCE_ADD'] = "пополнить";
$MESS ['ERR_SMSBLISS_CONNECT'] = "Не удается подключиться к серверу. Проверьте правильность логина и пароля.";
$MESS ['SMSBLISS_INSTRUCTION'] =	'Для настройки модуля выполните несколько простых действий:<br/>'.
									'1. Отправьте заявку на подключение.<br/>'.
									'2. Укажите ниже <b>логин</b> и <b>пароль</b>, которые вы получите после обработки заявки на подключение.<br/>'.
									'3. Нажмите кнопку <b>Сохранить</b>.';

/* sms-kontakt */
$MESS ['SMSKONTAKT_OPT_SENDER_ID'] = "Подпись отправителя";
$MESS ['SMSKONTAKT_OPT_SENDER_PHONE'] = "Телефон отправителя (79001112233)";
$MESS ['SMSKONTAKT_OPT_API_KEY'] = "API-ключ";
$MESS ['SMSKONTAKT_OPT_GET_API_KEY_TITLE'] = "API-ключ будет отправлен вам через смс";
$MESS ['SMSKONTAKT_OPT_GET_API_KEY_BUTTON'] = "'Получить API-ключ'";
$MESS ['SMSKONTAKT_ERR_EMPTY_PARAM'] = "Не указано значение";
$MESS ['SMSKONTAKT_MSG_API_KEY_SENT'] = "API-Ключ был отправлен на номер";
$MESS ['SMSKONTAKT_MSG_API_KEY_SENT_2']	= "Пожалуйста, укажите его на этой странице и сохраните настройки.";
$MESS ['SMSKONTAKT_ERR_API_KEY_NOT_SENT'] = "API-Ключ не был отправлен. Пожалуйста, попробуйте еще раз.";
$MESS ['SMSKONTAKT_PRICE'] = "Стоимость одного СМС-сообщения:";
$MESS ['SMSKONTAKT_BALANCE'] = "Ваш баланс";
$MESS ['SMSKONTAKT_BALANCE_ADD'] = "пополнить";
$MESS ['SMSKONTAKT_INSTRUCTION'] = 'Для настройки модуля выполните несколько простых действий:'.
	'<br/>1. Укажите <b>Подпись отправителя</b> (например, "sozdavatel") - это имя будет отображаться у получателя в качестве отправителя сообщения.'.
	'<br/>2. Укажите <b>Телефон отправителя</b> (например, "79001112233") - это номер телефона, к которому будет привязан ваш баланс. На этот номер будет отправлен <b>API-ключ</b>, поэтому укажите действующий номер, принадлежащий вам.'.
	'<br/>3. Нажмите кнопку <b>Получить API-ключ</b>. Вам будет отправлено СМС-сообщение с персональным API-ключом.'.
	'<br/>4. Введите в поле <b>API-ключ</b> код, который вы получили в СМС.'.
	'<br/>5. Нажмите кнопку <b>Сохранить</b>.';

$MESS ['SMS_SUPPORT'] = 
'<a target="_blank" href="http://dev.1c-bitrix.ru/community/webdev/group/24/forum/message/37043/203365/">Подробное описание на форуме</a>.
<br/>
Техподдержка: <a href="mailto:sms@sozdavatel.ru">sms@sozdavatel.ru</a>';

$MESS ['SMS_EXAMPLE'] = '
<div class="info-title">Описание API</div>
<br/>
<br/>Чтобы отправить СМС, нужно подключить модуль <b>sozdavatel.sms</b> и вызвать функцию <br/><b>CSMS::Send</b>($message, $reciever_phone = false, $charset = false).
<br/>
<br/>

<span style="color: #000000">
<span style="color: #0000BB">&lt;?php
<br></span><span style="color: #007700">if&nbsp;(</span><span style="color: #0000BB">CModule</span><span style="color: #007700">::</span><span style="color: #0000BB">IncludeModule</span><span style="color: #007700">(</span><span style="color: #DD0000">"sozdavatel.sms"</span><span style="color: #007700">))&nbsp;
<br>{&nbsp;
<br>&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #0000BB">$message&nbsp;</span><span style="color: #007700">=&nbsp;</span><span style="color: #DD0000">"Сообщение&nbsp;отправлено&nbsp;через&nbsp;модуль&nbsp;\n&nbsp;sozdavatel.sms"</span><span style="color: #007700">;&nbsp;
<br>&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #0000BB">CSMS</span><span style="color: #007700">::</span><span style="color: #0000BB">Send</span><span style="color: #007700">(</span><span style="color: #0000BB">$message</span><span style="color: #007700">,&nbsp;</span><span style="color: #DD0000">"79043015041"</span><span style="color: #007700">,&nbsp;</span><span style="color: #DD0000">"UTF-8"</span><span style="color: #007700">);&nbsp;
<br>}
<br></span><span style="color: #0000BB">?&gt;</span>
</span>

<br/>
<br/>Функция возвращает <b>true</b> в случае успешной отправки и <b>false</b>, если отправка не удалась.
<br/>
<br/>Функция принимает следующие параметры:
<br/>&bull; <b>message</b> - текст сообщения. Строки разделяются символом "\n";
<br/>&bull; <b>reciever_phone</b> - телефон получателя (11 цифр). Если телефон получателя не указан, то СМС отправляется получателю по умолчанию, то есть на номер, указанный в настройках модуля;
<br/>&bull; <b>charset</b> - кодировка сообщения. Если кодировка не указана, то используется кодировка сайта.
<br/>
<br/><b>Примечание</b>: если сообщение отправляется при помощи AJAX-запроса на сайте, работающем в кодировке windows-1251, то следует указывать кодировку UTF-8, так как AJAX работает только в UTF-8.
<br/>
<br/>
<br/>Чтобы получить информацию о смс-сервисе, используйте функцию <b>CSMS::GetServiceInfo</b>($serviceID = false).
<br/>Функция возвращает массив с информацией о смс-сервисе <b>$serviceID</b>. Если <b>$serviceID</b> не указан, то возвращается информация о сервисе, выбранном в настройках модуля.
<br/><b>$serviceID</b> может принимать значения: <b>SMSKONTAKT</b>, <b>SMSBLISS</b>.
<br/>
<br/>Функция возвращает массив со следующими ключами:
<br/>&bull; <b>ID</b> — Идентификатор смс-сервиса, например, SMSKONTAKT.
<br/>&bull; <b>CURRENCY</b> — Валюта, в которой работает сервис, например, rub (рубли).
<br/>&bull; <b>ACCOUNT_LINK</b> — Ссылка на личный кабинет на сайте смс-провайдера.
<br/>&bull; <b>BALANCE_PAY_LINK</b> — Ссылка на пополнение баланса.
<br/>&bull; <b>BALANCE</b> — Текущий баланс.
<br/>&bull; <b>PRICE</b> — Стоимость одного смс-сообщения (только SMSKONTAKT).
<br/>
<br/>
<br/>
<div class="info-title">Пример: СМС-уведомление о новом заказе</div>
<br/>Вы можете назначить отправку СМС на любое системное событие.
<br/>Обработчики событий описываются в файле <b>/bitrix/php_interface/init.php</b>.
<br/>Например, чтобы отправить СМС при оформлении заказа в интернет-магазине, нужно написать следующий обработчик:
<br/>
<br/>

<span style="color: #000000">
<span style="color: #0000BB">&lt;?php
<br>AddEventHandler</span><span style="color: #007700">(</span><span style="color: #DD0000">"sale"</span><span style="color: #007700">,&nbsp;</span><span style="color: #DD0000">"OnOrderAdd"</span><span style="color: #007700">,&nbsp;</span><span style="color: #DD0000">"sms_OnOrderAdd"</span><span style="color: #007700">);&nbsp;
<br>function&nbsp;</span><span style="color: #0000BB">sms_OnOrderAdd</span><span style="color: #007700">(</span><span style="color: #0000BB">$id</span><span style="color: #007700">,&nbsp;</span><span style="color: #0000BB">$arFields</span><span style="color: #007700">)&nbsp;
<br>{&nbsp;
<br>&nbsp;&nbsp;&nbsp;&nbsp;if&nbsp;(</span><span style="color: #0000BB">CModule</span><span style="color: #007700">::</span><span style="color: #0000BB">IncludeModule</span><span style="color: #007700">(</span><span style="color: #DD0000">"sozdavatel.sms"</span><span style="color: #007700">))&nbsp;
<br>&nbsp;&nbsp;&nbsp;&nbsp;{&nbsp;
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #FF8000">//&nbsp;получить&nbsp;номер&nbsp;телефона&nbsp;клиента&nbsp;из&nbsp;личного&nbsp;профиля&nbsp;
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #0000BB">$rsUser&nbsp;</span><span style="color: #007700">=&nbsp;</span><span style="color: #0000BB">CUser</span><span style="color: #007700">::</span><span style="color: #0000BB">GetByID</span><span style="color: #007700">(</span><span style="color: #0000BB">$arFields</span><span style="color: #007700">[</span><span style="color: #0000BB">USER_ID</span><span style="color: #007700">]);&nbsp;
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #0000BB">$arUser&nbsp;</span><span style="color: #007700">=&nbsp;</span><span style="color: #0000BB">$rsUser</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">Fetch</span><span style="color: #007700">();&nbsp;
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #0000BB">$phone&nbsp;</span><span style="color: #007700">=&nbsp;</span><span style="color: #0000BB">$arUser</span><span style="color: #007700">[</span><span style="color: #0000BB">PERSONAL_PHONE</span><span style="color: #007700">];&nbsp;
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #FF8000">//&nbsp;если&nbsp;телефон&nbsp;указан,&nbsp;отправить&nbsp;клиенту&nbsp;смс&nbsp;
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">if&nbsp;(</span><span style="color: #0000BB">$phone</span><span style="color: #007700">)&nbsp;
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #0000BB">$message&nbsp;</span><span style="color: #007700">= &nbsp;</span><span style="color: #DD0000">"Ваш&nbsp;заказ&nbsp;принят!\n"</span><span style="color: #007700">.&nbsp;
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #DD0000">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"Номер&nbsp;заказа:&nbsp;"</span><span style="color: #007700">.</span><span style="color: #0000BB">$id</span><span style="color: #007700">.</span><span style="color: #DD0000">",\n"</span><span style="color: #007700">.&nbsp;
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #DD0000">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"сумма:&nbsp;"</span><span style="color: #007700">.</span><span style="color: #0000BB">$arFields</span><span style="color: #007700">[</span><span style="color: #0000BB">PRICE</span><span style="color: #007700">].</span><span style="color: #0000BB">$arFields</span><span style="color: #007700">[</span><span style="color: #0000BB">CURRENCY</span><span style="color: #007700">].</span><span style="color: #DD0000">"."</span><span style="color: #007700">;<br>
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;if&nbsp;(</span><span style="color: #0000BB">CSMS</span><span style="color: #007700">::</span><span style="color: #0000BB">Send</span><span style="color: #007700">(</span><span style="color: #0000BB">$message</span><span style="color: #007700">,&nbsp;</span><span style="color: #0000BB">$phone</span><span style="color: #007700">))&nbsp;
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{&nbsp;
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #FF8000">//&nbsp;отправлено
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}&nbsp;
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;else&nbsp;
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{&nbsp;
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #FF8000">//&nbsp;ошибка
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}&nbsp;
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}&nbsp;
<br>&nbsp;&nbsp;&nbsp;&nbsp;}&nbsp;
<br>}
<br></span><span style="color: #0000BB">?&gt;</span>
</span>

<br/>
<br/>Клиент магазина получит такое сообщение:
<br/>
<br/><img src="/bitrix/images/sozdavatel.smskontakt/sms_example.png">';
?>
<?
$MESS['TWOFACTORAUTHENTIFICATIONLITE_REGISTER_S'] = "Выберите шаблон символов из которых будет состоять код подтверждения";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_REGISTER_L'] = "Длина кода подтверждения";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_REGISTER_T'] = "Сообщение при успешном подтверждении регистрации";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_PUT_LOGIN'] = "Логин пользователя";

$MESS['TWOFACTORAUTHENTIFICATIONLITE_SAVE_NOTICE'] = "Обратите внимание что любые настройки будут сохранены только после ввода ключа для авторизации.
<a href='https://my.sms16.ru/cabinet/76/profile.html' target='_blank'>Получить ключ</a>";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_SELECT_FIELD'] = "--Выберите поле--";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_SYM'] = " символов";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_TAB1'] = "Подключение к СМС шлюзу";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_TAB2'] = "Шаблон генерации пароля";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_TAB3'] = "Настройки";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_TAB31'] = "Регистрация пользователей";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_TAB4'] = "API";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_TAB5'] = "Баланс";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_TAB6'] = "Инструкция";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_TAB7'] = "Контакты";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_TAB8'] = "Доступ";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_TAB1_HEADING'] = "Токен для авторизации на шлюзе";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_TAB2_HEADING1'] = "Выберите шаблон символов из которых будет состоять одноразовый пароль";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_TAB2_HEADING2'] = "Длина одноразового пароля";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_TAB3_HEADING0'] = "Имя отправителя";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_TAB3_HEADING00'] = "Протокол передачи данных";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_TAB3_HEADING1'] = "Группы пользователей для которых доступны одноразовые пароли";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_TAB3_HEADING2'] = "Поле содержащее телефон";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_TAB3_HEADING22'] = "Поле содержащее телефон на который можно будет восстановить пароль";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_TAB3_HEADING3'] = "Привязка пароля к IP адресу";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_TAB3_HEADING4'] = "Блокировка IP";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_TAB3_HEADING5'] = "Оповещение администратора о подозрительных попытках авторизации";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_TAB3_HEADING6'] = "Документация по модулю";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_TAB3_HEADING7'] = "Состояние счёта на ";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_TAB3_HEADING8'] = "Инструкция по первичной настройке модуля";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_TAB3_HEADING9'] = "Контакты разработчиков модуля";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_TAB3_LABEL_FOR_ADMIN_PHONE'] = "Укажите номер телефона, если хотите получать оповещения о подозрительных попытках авторизации";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_SAVE'] = "Сохранить";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_ON'] = "Включить";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_BINDING_TO_IP_NOTICE'] = 'Обратите внимание что при включении данной опции одноразовый пароль будет привязан к
IP адресу с которого был запрошен.<br />Не устанавливайте флажок если привязка одноразового пароля к IP адресу не нужна.';
$MESS['TWOFACTORAUTHENTIFICATIONLITE_IP_BLOCK_NOTICE'] = "При включении данной опции IP адрес посетителя будет включен в <a href='/bitrix/admin/security_iprule_list.php?lang=ru'>стоп-лист</a>
и заблокирован на 15 минут в случае если основной пароль будет введён неверно 3 раза.";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_SECURITY_NOT_INSTALL'] = 'Для активации данной опции должен быть установлен модуль "Проактивная защита".';
$MESS['TWOFACTORAUTHENTIFICATIONLITE_API_1'] = " - вернёт установленный шаблон генерации пароля";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_API_2'] = " - вернёт установленную длину пароля";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_API_3'] = " - сгенерирует пароль.".' <b>$lenght</b>'." - длина пароля";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_API_4'] = " - вернёт код установленного поля которое содержит телефон";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_API_5'] = " - вернёт токен для авторизации";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_API_6'] = ' - вернёт "<b>on</b>" если стоит галочка привязки пароля к IP';
$MESS['TWOFACTORAUTHENTIFICATIONLITE_API_7'] = ' - вернёт "<b>on</b>" если стоит галочка блокировки по IP';
$MESS['TWOFACTORAUTHENTIFICATIONLITE_API_8'] = " - вернёт ID инфоблока в котором сохраняются элементы при включенной блокировке по IP.
Элементы служат только для подсчёта попыток авторизации до блокировки.";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_API_9'] = " - вернёт если установлен, номер телефона на который отправлять оповещения о подозрительных попытках авторизации";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_API_10'] = " - вернёт ID групп пользователям которых разрешена двухфакторная аутентификация, в формате: <b>1,2,3,4,5</b>";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_API_11'] = " - вернёт текущее установленное имя отправителя. <b>Внимание!</b> Если установлено пустое имя отправителя,
или желаемое имя отправителя не зарегистрировано - все СМС будут отправлены от имени <b>inetsms</b>. При установленном напрямую в коде имени отправителя которое не активировано на аккаунте
СМС сообщение не будет отправлено";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_API_12'] = " - вернёт активные на аккаунте имена отправителей в виде select`а. ".'<b>$secretKey</b>'." - токен для авторизации";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_API_13'] = " - вернёт приведенный к правильному формату номер телефона (в текущей версии только для России). ".'<b>$phone</b>'." - номер";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_API_14'] = " - удалит элемент с именем ".'<b>$name</b>'." из инфоблока с ID=".'<b>$iblockId</b>';
$MESS['TWOFACTORAUTHENTIFICATIONLITE_API_15'] = " - используется при установленном значении блокировки по IP. Создаёт в специальном инфоблоке элемент в котором фиксируется
количество попыток авторизации. Если количество попыток = 3, создаёт запись в стоп-листе и блокирует IP адрес пользователя на 15 минут.
Также, оповещает при необходимости о подозрительных попытках авторизации. <b>Внимание!</b> Логика работы этой функции может измениться в будущих версиях модуля!";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_API_16'] = " - функция используется агентом. Осуществляет поиск в стоп-листе записей с датой/временем окончания активности меньше текущей
и удаляет запись. Также ищет созданные до блокировки элементы в ИБ и удаляет их.";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_API_17'] = " - создает запись в стоп-листе. ".'<b>$ip, $activeFrom, $activeTo, $name, $status</b>'."
Соответственно:  IP, начало активности, окончание активности, название, статус";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_API_18'] = " - отправляет СМС. ".'<b>$message, $phone, $secretKey, $oneTimePass</b>'." Соответственно: текст сообщения, номер телефона,
токен для авторизации, одноразовый пароль. Если это просто какое-либо сообщение, т.е. не содержит одноразового пароля, то последним параметром нужно передать false.";
$MESS['TWOFACTORAUTHENTIFICATIONLITE_API_19'] = " - вернёт актуальный баланс при правильном ключе авторизации.";
$MESS['TWOFACTORAUTHENTIFICATION_RELOAD'] = " Обновить";
$MESS['TWOFACTORAUTHENTIFICATION_CONTACT_OOO_INTIS'] = '<a href="http://www.sms16.ru" target="_blank">ООО "Интис"</a><br />';
$MESS['TWOFACTORAUTHENTIFICATION_CONTACT_PHONE'] = "Тел.: 8 800-333-12-02 (звонок бесплатный)<br />";
$MESS['TWOFACTORAUTHENTIFICATION_CONTACT_EMAIL'] = "Email: sms@sms16.ru (сотрудничество, финансы, общие вопросы), module@sms16.ru (вопросы связанные с модулем)<br />";
$MESS['TWOFACTORAUTHENTIFICATION_CONTACT_SKYPE'] = "Skype: pixel365 (как правило в сети в будни с 08.00 до ~22.00 мск)";
$MESS['TWOFACTORAUTHENTIFICATION_MANUAL1'] = "<b>Важно!</b> Сразу после <a href='https://my.sms16.ru/reg.html' target='_blank'>регистрации</a> аккаунта
необходимо позвонить по бесплатному номеру 8 800-333-12-02 и сообщить логин. В этом случае аккаунт будет привязан к приоритетному каналу, т.е. СМС будут приходить за считанные секунды
минуя очередь. Это действительно важно при работе с модулем.<br /><br />";
$MESS['TWOFACTORAUTHENTIFICATION_MANUAL2'] = "После регистрации аккаунта зайдите на <a href='https://my.sms16.ru/cabinet/76/profile.html' target='_blank'>страницу настроек</a>,
скопируйте значение поля <b>Токен для авторизации по XML</b> и введите полученное значение в поле <b>Токен для авторизации на шлюзе</b> в первой вкладке модуля.
В случае необходимости можно сгенерировать новый токен, для этого перейдите на <a href='https://my.sms16.ru/cabinet/76/profile.html' target='_blank'>страницу настроек</a> аккаунта
и нажмите на кнопку <b>Обновить</b> и сохраните настройки. После этого можно ввести новый токен в настройках модуля. <b>Обратите внимание</b> что сохранение любых настроек модуля возможно
лишь при положительном значении поля <b>Токен для авторизации на шлюзе</b>.<br /><br />";
$MESS['TWOFACTORAUTHENTIFICATION_MANUAL3'] = "Во вкладке <b>Шаблон генерации пароля</b> выберите сочетание символов из которых будут генерироваться одноразовые пароли,
а также выберите длину пароля.<br /><br />";
$MESS['TWOFACTORAUTHENTIFICATION_MANUAL4'] = "Во вкладке <b>Настройки</b> выберите отправителя от имени которого будут отправляться СМС сообщения.<br />
<b>Обратите внимание</b> имя отправителя необходимо <a href='https://my.sms16.ru/cabinet/76/originator.html' target='_blank'>зарегистрировать</a> в аккаунте.
Все новые имена отправителей проходят модерацию.<br />Для срочной активации имени отправителя сразу после подачи заявки позвоните по бесплатному номеру 8 800-333-12-02.<br />
После активации модератором, имя отправителя становится доступно в настройках модуля. На одном аккаунте может использоваться несколько имён отправителей, каждое проходит модерацию.<br /><br />
<b>Вопрос:</b> Получается что пока имя отправителя неактивно модуль не работает?<br />
<b>Ответ:</b> Модуль будет работать. Все СМС сообщения будут отправляться от имени отправителя <b>inetsms</b>.<br /><br />
<b>Вопрос:</b> А если я не буду активировать имя отправителя на аккаунте, а буду напрямую передавать в функцию отправки в коде произвольное имя, СМС будет отправлено?<br />
<b>Ответ:</b> Нет, СМС сообщение будет заблокировано на уровне шлюза, т.к. при успешной авторизации по ключу проверяется список отправителей, и если передаваемое имя не используется
в аккаунте - СМС блокируется. Это же правило можно отнести и к ключу, если ключ неверный - СМС не будет отправлено.<br /><br />";
$MESS['TWOFACTORAUTHENTIFICATION_MANUAL5'] = "Далее в этой же вкладке выберите группу/группы пользователей для которых будет действовать двухфакторная авторизация.<br /><br />";
$MESS['TWOFACTORAUTHENTIFICATION_MANUAL6'] = "Выберите поле которое содержит номер телефона пользователя.<br /><br />";
$MESS['TWOFACTORAUTHENTIFICATION_MANUAL7'] = "В модуле предусмотрена возможность привязки одноразового пароля к IP-адресу с которого он был запрошен.
Включите данную опцию если это необходимо.<br /><br />";
$MESS['TWOFACTORAUTHENTIFICATION_MANUAL8'] = "В модуле предусмотрена возможность блокирования IP-адреса на время (15 минут) при 3х кратном неправильном вводе основного пароля пользователя
(не одноразовый). Это позволяет ещё на первом этапе предотвратить брутфорс. Данная опция доступна при установленном модуле <b>Проактивная защита</b>.<br /><br />";
$MESS['TWOFACTORAUTHENTIFICATION_MANUAL9'] = "В модуле предусмотрена возможность оперативного оповещения администратора о подозрительных попытках авторизации.
Данная функция работает независимо от модуля <b>Проактивная защита</b>, т.е. модуль <b>Проактивная защита</b> может быть и не установлен в Системе.
Для активации этой опции, укажите в соответствующем поле номер телефона на который необходимо отправлять СМС оповещения. После трижды неуспешной авторизации на этот номер будет отправляться
СМС следующеого содержания: Зафиксировано 3 попытки войти под логином 'LOGIN', с IP xxx.xxx.xxx.xxx";
$MESS['TWOFACTORAUTHENTIFICATION_MANUAL10'] = "Примечание:<br />Количество пользователей для которых доступна двухфакторная авторизация неограничено.";
$MESS['TWOFACTORAUTHENTIFICATION_MANUAL11'] = "<b>Внимание!</b> Обработчик необходимо разместить на какой либо отдельной странице подключив только пролог/эпилог.";
?>
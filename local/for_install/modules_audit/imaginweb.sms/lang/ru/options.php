<?
$MESS["IMAGINWEB_SMS_VOZMOJNOSTI"] = "Модуль позволяет быстро подключить к интернет-магазину SMS рассылку, для шлюзов:<br/>
1. <a target=\"_blank\" href=\"http://mainsms.ru/\">mainSMS</a>(Одна из самых низких цен за SMS по России, высокая скорость доставки, в качестве теста 50 смс, всегда 0,15 рубля!) Не нужно регистрировать имя отправителя!<br/>
2. <a target=\"_blank\" href=\"http://kompeito.ru/\">Kompeito</a>(Одна из самых низких цен за SMS по России, высокая скорость доставки, в качестве теста 40 смс, максимально 0,25 рубля!)<br/>
3. <a target=\"_blank\" href=\"http://www.infosmska.ru/\">InfoSMSka</a>(Одна из самых низких цен за SMS по России, высокая скорость доставки, в качестве теста 11 смс, максимально 0,35 рубля)<br/>
4. <a target=\"_blank\" href=\"http://www.bytehand.com/\">bytehand.com</a>(Одна из самых низких цен за SMS по России, высокая скорость доставки, в качестве теста 20 смс, максимально 0,40 рубля) Накопительные скидки!<br/>
5. <a target=\"_blank\" href=\"http://www.imobis.ru/\">Imobis.ru</a><br/>
6. <a target=\"_blank\" href=\"http://www.axtele.com/\">Axtelecom</a> имеет прямые подключения к федеральным операторам сотовой связи — «МТС», «Билайн», «Мегафон». (Высокое качество и скорость доставки)<br/>
7. <a target=\"_blank\" href=\"http://www.mobilmoney.ru/\">МобилМаниТелеком</a>(в качестве теста 14 смс, максимально 0,7 рубля)<br/>
8. <a target=\"_blank\" href=\"http://TurboSMS.ua\">TurboSMS.ua</a> (в качестве теста 10 смс, максимально 0,16 гривны)<br/>
9. <a href=\"http://www.epochtasms.ru\" target=\"_blank\">ePochtaSMS</a> (для теста 10 смс, максимально 0,45 рубля и 0,13 гривны)<br/>
10. <a href=\"http://giper.mobi\" target=\"_blank\">giper.mobi</a><br/><br/>
стоимость зависит от объема, минимальная цена в большинстве шлюзов от 0,15 руб. или от 0,12 гривны.";
$MESS["IMAGINWEB_SMS_BLAGODARIM"] = "Благодарим за использование нашего модуля,<br>
					по вопросам развития и предложений (мы постараемся оперативно вносить исправления): support@imaginweb.ru (24/7 - круглосуточно),<br>
					skype: imaginwebpartner (если доступен)<br/>
					+7 (495) 543-81-62 (в будние дни с 10:00 до 18:00 по московскому времени)<br>
					Наш сайт <a target=\"_blank\" href=\"imaginweb.ru\">imaginweb.ru</a><br><br/>
					Проголосуйте и/или оставьте отзыв на <a href=\"http://marketplace.1c-bitrix.ru/solutions/imaginweb.sms/\" name=\"\" target=\"_blank\">MarketPlace</a>, это поможет нам развивать и поддерживать этот модуль!
";
$MESS["IMAGINWEB_SMS_OBSIE_NASTROYKI"] = "Общие настройки";
$MESS["IMAGINWEB_SMS_UPRAVLENIE_SLUZOM_S"] = "Управление шлюзом (сервис для отправки СМС и отключение)";
$MESS["IMAGINWEB_SMS_RASSYLKA_OTKLUCENA"] = "Рассылка отключена";
$MESS["IMAGINWEB_SMS_KOD_SVOYSTVA_ZAKAZA"] = "Код свойства заказа содержащий телефон";
$MESS["IMAGINWEB_SMS_TEKSTA_SOOBSENIY"] = "Текста сообщений (шаблоны: #ACCOUNT_NUMBER# - сгенерированный номер заказа, #ORDER_NUMBER# - ID заказа, #ORDER_SUMM# - сумма заказа, #PRICE_DELIVERY# - стоимость доставки, #PRICE# - полная стоимость).";
$MESS["IMAGINWEB_SMS_NOVYY_ZAKAZ"] = "Новый заказ";
$MESS["IMAGINWEB_SMS_OTPRAVLATQ_KOPIU_NA"] = "Отправлять копию на телефон №";
$MESS["IMAGINWEB_SMS_OPLATA_ZAKAZA"] = "Оплата заказа";
$MESS["IMAGINWEB_SMS_OBAZATELQNYE_POLA"] = "обязательные поля";
$MESS["IMAGINWEB_SMS_PRIMECHANIE"] = "ПРИ ПУСТОМ ПОЛЕ СООБЩЕНИЕ ОТПРАВЛЕНО НЕ БУДЕТ";
$MESS["IMAGINWEB_SMS_OBRABATYVAEMYE_SOBYT"] = "Обрабатываемые события";
$MESS["IMAGINWEB_SMS_SOBYTIA"] = "
					На данный момент модуль обрабатывает следующие <a target=\"_blank\" href=\"http://dev.1c-bitrix.ru/api_help/sale/sale_events.php\" name=\"\">события</a> (подключение модуля не требуется):<br><br> 
					1. для нового заказа - OnSaleComponentOrderOneStepComplete и OnSaleComponentOrderComplete<br>
					2. успешная оплата заказа - OnSalePayOrder<br>
                                        3. доставка разрешена - OnSaleDeliveryOrder<br>
                                        4. заказ отменён - OnSaleCancelOrder<br>
                                        5. любое кол-во статусов заказов(изменение) - OnSaleStatusOrder<br><br>
					
					<span style=\"color: red;\">ВНИМАНИЕ! номер телефона берётся из свойства заказа указанного в настройках на вкладке \"интернет-магазин\"<br>
					формат телефона не важен при любом использовании в рамках модуля, например(варианты могут быть любыми): +7 (925) 543-8162 или 8-925-5438162 будет означать для отправки +79255438162, или 029-543-81-62 = +380295438162<br>
					НЕТ НЕОБХОДИМОСТИ ПРОВЕРКИ ФОРМАТА НОМЕРА ТЕЛЕФОНА					</span>
";
$MESS["IMAGINWEB_SMS_OPISANIE_ISPOLQZOVAN"] = "Описание использования API";
$MESS["IMAGINWEB_SMS_API"] = "Перед использованием API при прямом использовании рассылки, необходимо подключить модуль<br>
					пример отправки SMS<br><br>";
$MESS["IMAGINWEB_SMS_CIWEBSMS"] = "
					<div class=\"heading\">Описание класса CIWebSMS:</div>
					методы проверки и правки номера телефона:<br>";
$MESS["IMAGINWEB_SMS_CIWEBSMS_2"] = " Разбирает телефон в любом формате(не менее 10 знаков) и возвращает строку 11,12,14-ти значный номер телефона (в зависимости от страны) слитно или пустую строку в случае неудачи<br><br>";
$MESS["IMAGINWEB_SMS_CIWEBSMS_3"] = "проверяет номер телефона 11-14 цифр, в случае успеха возвращает true, в случае не удачи - false и описание ошибки в свойство  объекта при его наличии<br><br>";
$MESS["IMAGINWEB_SMS_CIWEBSMS_4"] = "Поля не обязательны. GATE - (может принимать значения: turbosms.ua или epochtasms. по умолчанию - текущий шлюз)<br/>LOGIN - логин шлюза; PASSWORD - пароль шлюза; ORIGINATOR - имя отправителя<br/>отсылает SMS сообщение message, в кодировке encoding на номер телефона phone, c именем отправителя originator, предварительно форматируя его методом MakePhoneNumber, после проверяя методом CheckPhoneNumber, при выбранном шлюзе SMS рассылки на вкладке настроек<br/><br/>";
$MESS["IMAGINWEB_SMS_CIWEBSMS_5"] = " - параметры запроса как у CIWebSMS::Send, возвращает текущий баланс в кредитах, пока только для ePochtaSMS и TurboSMS.ua.";
$MESS["IMAGINWEB_SMS_MOBILMANITELEKOM"] = "МобилМаниТелеком";
$MESS["IMAGINWEB_SMS_NASTROYKI_DOSTUPA"] = "Настройки доступа";
$MESS["IMAGINWEB_SMS_PODKLUCENIA"] = "подключения:";
$MESS["IMAGINWEB_SMS_LOGIN"] = "Логин:";
$MESS["IMAGINWEB_SMS_PAROLQ"] = "Пароль:";
$MESS["IMAGINWEB_SMS_NASTROYKI_PODKLUCENI"] = "Настройки подключения";
$MESS["IMAGINWEB_SMS_POLE_OTPRAVITELQ"] = "Поле отправитель";
$MESS["IMAGINWEB_SMS_POLE_OTPRAVITELQ_2"] = "Поле отправитель (14 цифровых символов или 11 цифробуквенных (английские буквы и цифры))";
$MESS["IMAGINWEB_SMS_INTERNET_MAGAZIN"] = "Интернет-магазин";
$MESS["IMAGINWEB_SMS_NASTROYKI_INTERNET_M"] = "Настройки интернет-магазина";
$MESS["IMAGINWEB_SMS_OPISANIE"] = "Описание";
$MESS["IMAGINWEB_SMS_SETTINGS"] = "Настройки сервиса рассылки";
$MESS["IMAGINWEB_SMS_SETTING_PARAMS"] = "Настройка параметров сервиса рассылки";
$MESS["IMAGINWEB_SMS_DOSTUP"] = "Доступ";
$MESS["IMAGINWEB_SMS_BALANS_KREDITOV"] = "Баланс (кредитов):";
$MESS["IMAGINWEB_SMS_DLA_AKTIVACII_SMS_SL"] = "Для активации СМС шлюза необходимо в ";
$MESS["IMAGINWEB_SMS_NASTROYKAH_POLQZVATE"] = "настройках пользвателя";
$MESS["IMAGINWEB_SMS_NA_VKLADKE"] = "на вкладке";
$MESS["IMAGINWEB_SMS_AKTIVIROVATQ_ISPOLQZ"] = "активировать использование";
$MESS["IMAGINWEB_SMS_DLA_ETOGO_V_PUNKTE"] = "Для этого в пункте ";
$MESS["IMAGINWEB_SMS_VKLUCITQ"] = "Включить";
$MESS["IMAGINWEB_SMS_INTERFEYS"] = "интерфейс";
$MESS["IMAGINWEB_SMS_NEOBHODIMO_VYBRATQ_P"] = "необходимо выбрать пункт ";
$MESS["IMAGINWEB_SMS_DA"] = "Да";
$MESS["IMAGINWEB_SMS_V_PUNKTE"] = "В пункте ";
$MESS["IMAGINWEB_SMS_REJIM"] = "Режим";
$MESS["IMAGINWEB_SMS_INTERFEYSA"] = "интерфейса";
$MESS["IMAGINWEB_SMS_VYBRATQ_LIBO"] = "выбрать  либо ";
$MESS["IMAGINWEB_SMS_REALQNAA_OTPRAVKA"] = "Реальная отправка";
$MESS["IMAGINWEB_SMS_LIBO"] = "либо ";
$MESS["IMAGINWEB_SMS_TESTOVYY_REJIM"] = "Тестовый режим";
$MESS["IMAGINWEB_SMS_V_SLUCAE_VYBORA_REJI"] = "В случае выбора режима ";
$MESS["IMAGINWEB_SMS_REALQNOY_OTPRAVKI"] = "Реальной отправки";
$MESS["IMAGINWEB_SMS_SOOBSENIA_BUDUT_OTPR"] = "сообщения будут отправлены незамедлительно после поступления запроса на шлюз. В  случае выбора ";
$MESS["IMAGINWEB_SMS_TESTOVOGO_REJIMA"] = "Тестового режима";
$MESS["IMAGINWEB_SMS_SOOBSENIA_OTPRAVLENY"] = "сообщения отправлены не будут. Но будут созданы  задачи со статусом ";
$MESS["IMAGINWEB_SMS_NE_GOTOVO"] = "Не готово";
$MESS["IMAGINWEB_SMS_ETO_SVIDETELQSTVUET"] = "Это свидетельствует о том, что было успешное  подключение к шлюзу и данные на сервер были переданы.";
$MESS["IMAGINWEB_SMS_NAZVANIE_STATUSA"] = "Название статуса";
$MESS["IMAGINWEB_SMS_DOPOLNITELQNYE_SABLO"] = "Дополнительные шаблоны";
$MESS["IMAGINWEB_SMS_TELEFON"] = "Телефон:";
$MESS["IMAGINWEB_SMS_SOOBSENIE"] = "Сообщение:";
$MESS["IMAGINWEB_SMS_DOPOLNITELQNOE_SOOBS"] = "Дополнительное сообщение для события: \"Новый заказ\"";
$MESS["IMAGINWEB_SMS_TELEFON1"] = "Телефон: ";
$MESS["IMAGINWEB_SMS_SOOBSENIE1"] = "Сообщение";
$MESS["IMAGINWEB_SMS_DOPOLNITELQNOE_SOOBS1"] = "Дополнительное сообщение для события: \"Оплата заказа\"";
$MESS["IMAGINWEB_SMS_OTMENA_ZAKAZA"] = "Отмена заказа";
$MESS["IMAGINWEB_SMS_DOPOLNITELQNOE_SOOBS2"] = "Дополнительное сообщение для события: \"Отмена заказа\"";
$MESS["IMAGINWEB_SMS_DOSTAVKA_RAZRESENA"] = "Доставка разрешена";
$MESS["IMAGINWEB_SMS_DOPOLNITELQNOE_SOOBS3"] = "Дополнительное сообщение для события: \"Доставка разрешена\"";
$MESS["IMAGINWEB_SMS_DOPOLNITELQNOE_SOOBS4"] = "Дополнительное сообщение для смены статусов заказа.";
$MESS["IMAGINWEB_SMS_DOPOLNITELQNOE_SOOBS5"] = "Дополнительное сообщение для смены статуса на: \"";
$MESS["IMAGINWEB_SMS_STATUS_ZAKAZA"] = "Статус заказа";
$MESS["IMAGINWEB_SMS_NAZVANIE_SLUJBY_DOST"] = "Название службы доставки";
$MESS["IMAGINWEB_SMS_NOMER_DOKUMENTA_OTGR"] = "Номер документа отгрузки";
$MESS["IMAGINWEB_SMS_DATA_DOKUMENTA_OTGRU"] = "Дата документа отгрузки";
$MESS["IMAGINWEB_SMS_UBEDITESQ_CTO_U_VSE"] = "Убедитесь, что у всех необходимых ";
$MESS["IMAGINWEB_SMS_SVOYSTV_ZAKAZA"] = "свойств заказа";
$MESS["IMAGINWEB_SMS_ESTQ_MNEMONICESKIY"] = "есть \"мнемонический код";
$MESS["IMAGINWEB_SMS_VNIMANIE_DLA_RABO"] = "ВНИМАНИЕ!!! Для работы данного шлюза, необходимо подключить";
$MESS["IMAGINWEB_SMS_KOMPONENTY"] = "Компоненты
			";
$MESS["IMAGINWEB_SMS_AVTORIZACIA_REGISTRA"] = "авторизация/регистрация/забыли пароль посредством sms";
$MESS["IMAGINWEB_SMS_VALIDACIA_POLQZOVATE"] = "валидация пользователя или его действий посредством";
$MESS["IMAGINWEB_SMS_NAPODOBIE"] = "наподобие";
$MESS["IMAGINWEB_SMS_U_OBOIH_KOMPONETOV_N"] = "у обоих компонетов на выходе в перемменную ";
$MESS["IMAGINWEB_SMS_PISUTSA_REZULQTATY_R"] = "пишутся результаты работы компонента";
$MESS["IMAGINWEB_SMS_OBA_KOMPONENTA_PODDE"] = "оба компонента поддерживают ";
$MESS["IMAGINWEB_SMS_OOO_INFOSMS_PREDLA"] = "ООО «ИнфоСМС» предлагает современный и качественный сервис СМС рассылок. Не нужно покупать";
$MESS["IMAGINWEB_SMS_PAKETAMI_POPOLNAYTE"] = "пакетами, пополняйте баланс на любую удобную сумму, списание денег производится за каждое отправленное сообщение. Низкие цены, никаких дополнительных платежей, множество способов оплаты и отличная поддержка. ";
$MESS["IMAGINWEB_SMS_KONTAKTY"] = "Контакты:
";
$MESS["IMAGINWEB_SMS_TEL"] = "Тел";
$MESS["IMAGINWEB_SMS_PN_PT_S_DO_CASO"] = "Пн-Пт с 7 до 17 часов по московскому времени, звонок беcплатный";
$MESS["IMAGINWEB_SMS_INSTRUKCIA"] = "Инструкция:
";
$MESS["IMAGINWEB_SMS_DLA_ISPOLQZOVANIA_DA"] = "Для использования данного модуля необходимо пройти регистрацию на сайте ";
$MESS["IMAGINWEB_SMS_U_VAS_NA_BALANSE_BUD"] = "У вас на балансе будет 4 рубля, достаточных для отправки нескольких тестовых сообщений.
";
$MESS["IMAGINWEB_SMS_CTOBY_ISPOLQZOVATQ_S"] = "Чтобы использовать свой собственный адреса отправителя, его необходимо создать в личном кабинете на сайте";
$MESS["IMAGINWEB_SMS_DLA_ETOGO_VOYDITE_V"] = "Для этого, войдите в личный кабинет, нажмите \"Новая рассылка\", выберите \"создание обычной рассылки\", в поле адрес отправителя нажмите на ссылку \"Создать/Удалить\".";
$MESS["IMAGINWEB_SMS_POLE_OTPRAVITELA_BU"] = "Поле отправителя (буквы латинского алфавита, цифры, пробел, и следующие знаки: .&!*()-+=_ Длинна не должна превышать 11 символов.) ";
$MESS["IMAGINWEB_SMS_DLINNA_NE_DOLJNA_PRE"] = "Длинна не должна превышать 11 символов.)";
$MESS["IMAGINWEB_SMS_BALANS_RUBLEY"] = "Баланс (рублей)";
$MESS["IMAGINWEB_SMS_ISPOLQZOVATQ_RAZRESE"] = "Использовать разрешённый для отправки интервал времени. Задаётся в настройках личного кабинета. По умолчанию интервал не учитывается.";
$MESS["IMAGINWEB_SMS_IMEET_PRAMYE_PODKLUC"] = "имеет прямые подключения к федеральным операторам сотовой связи — «МТС», «Билайн», «Мегафон». (Высокое качество и скорость доставки";
$MESS["IMAGINWEB_SMS_PREIMUSESTVA"] = "Преимущества";
$MESS["IMAGINWEB_SMS_SMS_DLA_TESTIROVANIA"] = "смс для тестирования";
$MESS["IMAGINWEB_SMS_SAMAA_NIZKAA_CENA_DL"] = "Самая низкая цена для Украины";
$MESS["IMAGINWEB_SMS_CENA_DLA_ROSSII"] = "Цена для России";
$MESS["IMAGINWEB_SMS_NE_BOLEE_KOPEEK"] = "не более 45 копеек (есть бонусы и скидки";
$MESS["IMAGINWEB_SMS_PODDERJIVAET_OTPRAVK"] = "Поддерживает отправку смс на телефонные номера";
$MESS["IMAGINWEB_SMS_OPERATOROV_ROSSII"] = "операторов России";
$MESS["IMAGINWEB_SMS_RASSYLKA"] = "Рассылка";
$MESS["IMAGINWEB_SMS_PO_VSEMU_MIRU_BOLEE"] = "по всему миру (более";
$MESS["IMAGINWEB_SMS_STRAN"] = "стран";
$MESS["IMAGINWEB_SMS_POSLE"] = "После ";
$MESS["IMAGINWEB_SMS_REGISTRACII"] = "регистрации";
$MESS["IMAGINWEB_SMS_DOSTUPNY_SMS_DLA"] = "доступны 10 смс для тестирования отправки, при оплате в течение 3 часов после регистрации";
$MESS["IMAGINWEB_SMS_BONUS"] = "бонус";
$MESS["IMAGINWEB_SMS_OPLACIVATQ_MOJNO_KAK"] = "Оплачивать можно как электронными валютами, кредитными картами";
$MESS["IMAGINWEB_SMS_TAK_I_BEZNALICNYM_RA"] = "так и безналичным расчетом с предоставлением бухгалтерских документов. ";
$MESS["IMAGINWEB_SMS_OPISANIE_SERVISA"] = "Описание сервиса: ";
$MESS["IMAGINWEB_SMS_REGISTRACIA"] = "Регистрация: ";
$MESS["IMAGINWEB_SMS_SOHRANATQ_PAROLQ_V_S"] = "Сохранять пароль в свойстве пользователя (для дальнейшего использования в";
$MESS["IMAGINWEB_SMS_PAROLI_SOHRANAUTSA_V"] = "Пароли сохраняются в момент регистрации и обновления данных пользователя, т.е. для всех новых пользователей и тех кто сменит пароль, при необходимости можно сменить пароль и вписать его вручную для конкретного пользователя";
$MESS["IMAGINWEB_SMS_NE_SOHRANATQ"] = "Не сохранять";
$MESS["IMAGINWEB_SMS_DOSTUPNY_REGISTRACIO"] = "Доступны регистрационные данные пользователя (Шаблоны";
$MESS["IMAGINWEB_SMS_LOGIN1"] = "Логин";
$MESS["IMAGINWEB_SMS_PAROLQ1"] = "Пароль";
$MESS["IMAGINWEB_SMS_IMA"] = "Имя";
$MESS["IMAGINWEB_SMS_FAMILIA"] = "Фамилия";
$MESS["IMAGINWEB_SMS_OTECESTVO_I_DRUGIE"] = "Отечество и другие ";
$MESS["IMAGINWEB_SMS_POLA"] = "поля";
$MESS["IMAGINWEB_SMS_V_TOM_CISLE_I_POLQZO"] = "в том числе и пользовательские";
$MESS["IMAGINWEB_SMS_SAYT"] = "Сайт";
$MESS["IMAGINWEB_SMS_POLE_OTPRAVITELA"] = "Поле отправителя";
$MESS["IMAGINWEB_SMS_BUKVY_LATINSKOGO_ALF"] = "буквы латинского алфавита, цифры, пробел, и следующие знаки";
$MESS["IMAGINWEB_SMS_ESLI_NE_UKAZANO_TO"] = "Если не указано, то берётся из настроек шлюза";
$MESS["IMAGINWEB_SMS_ESLI_NE_UKAZANO_BERE"] = "Если не указано берётся из настроек шлюза";
$MESS["opt_encoding"] = "Кодировки сообщения в рассылку:";
$MESS["opt_delete"] = "Через сколько дней удалять неподтвержденные подписки (0 - не удалять):";
$MESS["MAIN_RESTORE_DEFAULTS"] = "По умолчанию";
$MESS["opt_anonym"] = "Разрешить анонимную подписку:";
$MESS["opt_links"] = "Показывать ссылки на авторизацию при анонимной подписке:";
$MESS["opt_sect"] = "Публичный раздел, где находится страница редактирования подписки (макросы: #SITE_DIR#):";
$MESS["opt_vis_edit"] = "Использовать HTML редактор (только для IE 5.0 или FireFox 1.0 и выше):";
$MESS["opt_interval"] = "Интервал в секундах для пошаговой рассылки (0 - рассылать за один шаг):";
$MESS["opt_def_from"] = "Имя отправителя по умолчанию:";
$MESS["opt_def_to"] = "Номер получателя по умолчанию:";
$MESS["opt_allow_8bit"] = "Разрешить 8-битные символы в заголовке письма:";
$MESS["opt_attach"] = "Отправлять картинки в виде вложений в письмо:";
$MESS["opt_method_agent"] = "Агент";
$MESS["opt_method_cron"] = "Крон";
$MESS["opt_method"] = "Метод автоматической рассылки:";
$MESS["opt_max_per_hit"] = "Количество sms для автоматической рассылки агентом за один запуск:";
$MESS["opt_template_method"] = "Метод автоматической генерации выпусков:";
$MESS["opt_template_interval"] = "Интервал проверки необходимости генерации выпусков в секундах:";
$MESS["opt_max_bcc_count"] = "Максимальное количество номеров в поле BCC <b>не</b> в режиме &quot;Персонально подписчику&quot; (0 - отправить одно sms):";
$MESS["opt_mail_additional_parameters"] = "Дополнительный параметр для передачи функции mail:";
$MESS["subscribe_max_lenght"] = "Максимальная длина SMS сообщения<br/>(<b>1 сообщение:</b> латиницей - 160 символов, кириллицей - 70 символов):";
$MESS["subscribe_field_phone"] = "Свойство пользователя содержащее номер телефона:";
$MESS["IMAGINWEB_SMS_NAKOPITELQNYE_SKIDKI"] = "Накопительные скидки! Одна из самых низких цен для РФ.";
$MESS["IMAGINWEB_SMS_OPLATA_V_RUBLAH"] = "оплата в рублях";
$MESS["IMAGINWEB_SMS_ANDEKS_DENQGI_PLAST"] = "Яндекс-деньги, пластиковые карты, безналичный расчёт и другое";
$MESS["IMAGINWEB_SMS_POSLE_REGISTRCII_DO"] = "после регистрции  доступны";
$MESS["IMAGINWEB_SMS_DLA_TESTA_NAKOPTELQ"] = "для теста, накоптельная скидка на все смс";
$MESS["IMAGINWEB_SMS_UNIKALQNAA_PARTNERSK"] = "Уникальная партнерская программа для";
$MESS["IMAGINWEB_SMS_MASEROV_S_VOZMOJNOST"] = "масеров с возможностью заработка";
$MESS["IMAGINWEB_SMS_OPERATIVNAA"] = "Оперативная";
$MESS["IMAGINWEB_SMS_PODDERJKA"] = "поддержка
			";
$MESS["IMAGINWEB_SMS_INFO_DLA_NASTROEK"] = "Инфо для настроек";
$MESS["IMAGINWEB_SMS_KLUC"] = "Ключ";
$MESS["IMAGINWEB_SMS_OSNOVNYE_NASTROYKI"] = "Основные настройки";
$MESS["IMAGINWEB_SMS_NASTROYKI_SLUZOV"] = "Настройки шлюзов";
$MESS["IMAGINWEB_SMS_SEND"] = "Отправить SMS";
$MESS["IMAGINWEB_SMS_ODNA_IZ_SAMYH_NIZKIH"] = "Одна из самых низких цен за СМС! Всегда 15 копеек!!!";
$MESS["IMAGINWEB_SMS_KOP_SMS_PO_ROSSII_V"] = "коп./смс по России вне зависимости от объёмов";
$MESS["IMAGINWEB_SMS_SMS_V_PODAROK_PRI_RE"] = "смс в подарок при регистрации";
$MESS["IMAGINWEB_SMS_SMS_V_PODAROK_KAJDYY"] = "смс в подарок каждый месяц";
$MESS["IMAGINWEB_SMS_PODROBNEE"] = "Подробнее";
$MESS["IMAGINWEB_SMS_LUBOE_IMA_OTPRAVITEL"] = "любое имя отправителя";
$MESS["IMAGINWEB_SMS_NAZVANIE_PROEKTA_BE"] = "Название проекта, берется ";
$MESS["IMAGINWEB_SMS_SO_STRANICY"] = "со страницы";
$MESS["IMAGINWEB_SMS_KLUC_PROEKTA_BERETS"] = "Ключ проекта, берется ";
$MESS["IMAGINWEB_SMS_BALANS_RUB"] = "Баланс (руб)";
$MESS["IMAGINWEB_SMS_ODNA_IZ_SAMYH_NIZKIH1"] = "Одна из самых низких цен за СМС! Максимум 25 копеек!";
$MESS["IMAGINWEB_SMS_SERVIS"] = "Сервис";
$MESS["IMAGINWEB_SMS_NE_ODIN_V_SVOEM_RODE"] = "не один в своем роде, но у него есть ";
$MESS["IMAGINWEB_SMS_PREIMUSESTVA1"] = "преимущества";
$MESS["IMAGINWEB_SMS_VYGODNO_OTLICAUSIE_O"] = "выгодно отличающие от конкурентов.";
$MESS["IMAGINWEB_SMS_TELEFONY"] = "Телефоны:";
$MESS["IMAGINWEB_SMS_BESSLPATNO_PO_ROSSII"] = "бесслпатно по России";
$MESS["IMAGINWEB_SMS_DLA_MOSKVY"] = "для Москвы";
$MESS["IMAGINWEB_SMS_PODKLUCENIE_OBYCNOE"] = "Подключение обычное, зарегистрируйтесь ";
$MESS["IMAGINWEB_SMS_TUT"] = "тут";
$MESS["IMAGINWEB_SMS_IMA_OTPRAVITELA_PO_U"] = "Имя отправителя по умолчанию";
$MESS["IMAGINWEB_SMS_VVEDITE_V_POLE_NIJE"] = "Введите в поле ниже, логин, пароль и имя отправителя";
$MESS["IMAGINWEB_SMS_BALANS"] = "Баланс";
$MESS["IMAGINWEB_SMS_ODNA_IZ_SAMYH_NIZKIH2"] = "Одна из самых низких цен за СМС! Специальная цена для пользователей модуля 15 копеек!!! ";
$MESS["IMAGINWEB_SMS_DLA_POLUCENIA_SPEC_C"] = "Для получения спец цены, ";
$MESS["IMAGINWEB_SMS_SOOBSITE_MENEDJERU_K"] = "сообщите менеджеру кодовое слово";
$MESS["IMAGINWEB_SMS_ILI_IMEYDJIN_VEB"] = "или  \"Имейджин Вэб\" и ваш тариф измениться.";
$MESS["IMAGINWEB_SMS_SMS_RASSYLKI_ALEF_M"] = "смс-рассылки «Алеф Маркетинг Сервис»";
$MESS["IMAGINWEB_SMS_VLADIMIR"] = "Владимир";
$MESS["IMAGINWEB_SMS_MOSKVA"] = "Москва";
$MESS["IMAGINWEB_SMS_BESPLATNO_PO_RF"] = "Бесплатно по РФ";
$MESS["IMAGINWEB_SMS_ODNA_IZ_SAMYH_NIZKIH3"] = "Одна из самых низких цен за СМС! Специальная цена для пользователей модуля 15 копеек";
$MESS["IMAGINWEB_SMS_PRI"] = "при ";
$MESS["IMAGINWEB_SMS_REGISTRACII_V_SERVIS"] = "регистрации в сервисе";
$MESS["IMAGINWEB_SMS_UKAJITE_KOD_PRIGLASE"] = "укажите код приглашения";
$MESS["IMAGINWEB_SMS_ILI"] = "или";
$MESS["IMAGINWEB_SMS_IMEYDJIN_VEB"] = "Имейджин Вэб";
$MESS["IMAGINWEB_SMS_I_VAS_TARIF_AVTOMATI"] = "и ваш тариф автоматически измениться";
$MESS["IMAGINWEB_SMS_ESLI_VY_UJE_POLQZUET"] = "Если Вы уже пользуетесь сервисом, то отправьте СМС со словом";
$MESS["IMAGINWEB_SMS_NA_NOMER"] = "на номер";
$MESS["IMAGINWEB_SMS_I_MENEDJER_POMENAET"] = "и менеджер поменяет Ваш тариф";
$MESS["IMAGINWEB_SMS_SMS_RASSYLKI"] = "смс-рассылки";
$MESS["IMAGINWEB_SMS_ALEF_MARKETING_SERVI"] = "Алеф Маркетинг Сервис";
$MESS["IMAGINWEB_SMS_TELEFON_PODDERJKI"] = "Телефон поддержки";
$MESS["IMAGINWEB_SMS_V_NASTROYKE_MODULA_U"] = "В настройке модуля указывайте логин и пароль основного пользователя сервиса";
$MESS["IMAGINWEB_SMS_NO_MY_NASTOATELQNO_R"] = "но мы настоятельно рекомендуем задавать иной пароль в ";
$MESS["IMAGINWEB_SMS_NASTROYKAH_BEZOPASNO"] = "настройках безопасности";
$MESS["IMAGINWEB_SMS_LICNOGO_KABINETA_TR"] = "личного кабинета (Транспортный";
$MESS["IMAGINWEB_SMS_PROTOKOL_I_ISPOLQZ"] = "протокол), и использовать его";
$MESS["IMAGINWEB_SMS_UDOBNYY_I_KACESTVENN"] = "Удобный и качественный сервис по отправке одиночных и массовых СМС сообщений+доп. услуги - СМС купон, СМС планировщик, СМС поздравления, Обработчик";
$MESS["IMAGINWEB_SMS_UVEDOMLENIA_OB"] = "Уведомления об";
$MESS["IMAGINWEB_SMS_ONLAYN_KONSULQTANT"] = "Онлайн-консультант \"На связи";
$MESS["IMAGINWEB_SMS_SMS_DLA_TESTA"] = "СМС для теста. ";
$MESS["IMAGINWEB_SMS_DLA_PODKLUCENIA_TARI"] = "Для подключения тарифа 15 копеек, Ваш код приглашения - Имейджин Вэб";
$MESS["IMAGINWEB_SMS_V_STADII_TESTIROVANI"] = "В стадии тестирования!!!";
$MESS["IMAGINWEB_SMS_DLA_PODKLUCENIA_TARI1"] = "Для подключения тарифа 15 копеек, Ваш код приглашения - Имейджин Вэб или";
$MESS["IMAGINWEB_SMS_NED_TO_FIELD"] = "Поле \"Кому\" не может быть пустым.";
$MESS["IMAGINWEB_SMS_WRONG_PHONE"] = "Неверный формат телефона в поле \"Кому\".";
$MESS["IMAGINWEB_SMS_NED_BODY"] = "Поле \"Текст sms\" не может быть пустым.";
$MESS["IMAGINWEB_SMS_MESS_SENT"] = "Сообщение отправлено.";
$MESS["IMAGINWEB_SMS_FIELD_SMS"] = "Поля sms";
$MESS["IMAGINWEB_SMS_FROM_FIELD"] = "От кого:";
$MESS["IMAGINWEB_SMS_TO_FIELD"] = "Кому:";
$MESS["IMAGINWEB_SMS_TEXT_SMS"] = "Текст sms";
$MESS["IMAGINWEB_SMS_BUTTON_SEND"] = "Отправить";
$MESS["IMAGINWEB_SMS_PROMO_REDSMS"] = "от 8 копеек за SMS !
<br /><br />
Сервис использует только прямые подключения к операторам с буквенным именем отправителя. Доставка на номера России, Украины, Беларуси, Казахстана и др.
<br /><br />
Платформа 2005 года, 1500 смс/сек, 5000 активных клиентов, бесплатное тестирование, низкие цены. Работаем с юр. и физ. лицами.";
$MESS["IMAGINWEB_SMS_INTERNET_MAGAZIN_ZV"] = "Интернет-магазин, Звонки";
$MESS["IMAGINWEB_SMS_OSNOVNYE_NASTROYKI1"] = "Основные настройки, звонки";
$MESS["IMAGINWEB_SMS_OTPRAVKA_SOOBSENIA"] = "Отправка сообщения";
$MESS["IMAGINWEB_SMS_ZVONOK_POSTAVLEN_V_O"] = "Звонок поставлен в очередь";
$MESS["IMAGINWEB_SMS_SOVERSAYTE_AVTOMATIC"] = "Совершайте автоматические телефонные звонки клиентам без оператора, просто введите текст, робот позвонит от вашего номера телефона и голосом девушки прочитает его!";
$MESS["IMAGINWEB_SMS_ZVONKI_MOGUT_OSUSEST"] = "Звонки могут осуществляться от вашего номера телефона";
$MESS["IMAGINWEB_SMS_VAS_KLUC_VYSYLAETSA"] = "Ваш Ключ, высылается вам на";
$MESS["IMAGINWEB_SMS_POSLE_REGISTRACII"] = "после регистрации";
$MESS["IMAGINWEB_SMS_ZAREGISTRIROVATQSA"] = "Зарегистрироваться";
$MESS["IMAGINWEB_SMS_ZVONKI_OTKLUCENY"] = "Звонки отключены";
$MESS["IMAGINWEB_SMS_ZVONKI_VKLUCENY"] = "Звонки включены";
$MESS["IMAGINWEB_SMS_NOMER_TELEFONA_ISHOD"] = "Номер телефона исходящего звонка";
$MESS["IMAGINWEB_SMS_DESATQ_ZNAKOV_NAPRI"] = "десять знаков, например";
$MESS["IMAGINWEB_SMS_PREDVARITELQNO_DOLJE"] = "предварительно должен быть зарегистрирован в личном кабинете";
$MESS["IMAGINWEB_SMS_TREBUETSA_VERSIA"] = "Требуется версия";
$MESS["IMAGINWEB_SMS_NE_NIJE"] = "не ниже";
$MESS["IMAGINWEB_SMS_OBRATITESQ_POJALUYST"] = "обратитесь пожалуйста к хостинг провайдеру. ";
$MESS["IMAGINWEB_SMS_DLA_RABOTY_TREBUETSA"] = "Для работы требуется устанвленное расширение";
$MESS["IMAGINWEB_SMS_I_ODNO_IZ_DVUH_RASSI"] = "и одно из двух расширений связи с сервером";
$MESS["IMAGINWEB_SMS_POZVONITQ"] = "Позвонить";
$MESS["IMAGINWEB_SMS_TEKST_ZVONKA"] = "Текст звонка
			";
$MESS["IMAGINWEB_SMS_OTPRAVITQ_SMS"] = "Отправить смс";
$MESS["IMAGINWEB_SMS_VKLUCITQ_OTKLUCITQ_Z"] = "Включить/отключить звонки";
?>
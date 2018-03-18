# Смс сервисы + Viber, 25 шлюзов. 500 смс для РБ в подарок
![alt-текст](img_md/smsservices.jpg "1")

https://marketplace.1c-bitrix.ru/solutions/mlife.smsservices/

**Описание решения**  
Модуль позволяет отправлять смс уведомления с админ панели, сохраняет историю всех отправленных sms сообщений.
Возможна отправка уведомлений с любых других компонентов с использованием api модуля (документация есть! см. описание установки).

C версии 1.5.6 поддерживаются смс уведомления для интернет-магазина, соответствующая опция появится в настройках модуля при установленном модуле "Интернет-магазин" (sale);

![alt-текст](img_md/sms-assistent.png "1")

* для получения смс в настройках модуля нужно запросить промо-код.


На данный момент поддерживаются 25 шлюзов:
SMSC.RU, 
SMS-ASSISTENT.BY,
INCOREDEVELOPMENT.COM, 
SMS.RU, 
TARGETSMS.RU,

SMSPILOT.RU, 
SMS4B.RU, 
SMS16.RU, 
SMS-ASSISTENT.RU,
ROCKETSMS.BY,
SMSGK.RU, 
DEVINOTELE.COM, 
P1SMS.RU, 
QTELECOM.RU, 
IBATELE.COM,
USER.REKLAMAVKARMANE.RU,
LITTLESMS.RU, 
SMS96.RU,
ESPUTNIK.COM,
BYTEHAND.COM,
SMS-FLY.UA,
SMSINT.RU,
SMS.BY

APP шлюзы:
SMSC.RU - Viber, 
DEVINOTELE.COM - Viber, 

+ добавим любой шлюз по вашему запросу


**Установка**

Модуль можно установить на все редакции 1С-Битрикс: Управление сайтом.
Требования: база данных MySQL (с Oracle и MsSQL работать не будет), Curl, SimpleXML (для sms4b.ru).

Установка стандартная, в автоматическом режиме.
После установки требуется в настройках модуля указать данные доступа к шлюзу и права на доступ к модулю.

Важно!
Отложенная отправка сообщений и получение статусов работает на агентах, если у вас нет плотного потока посетителей на сайте, то рекомендуется перенести выполнение агентов на cron -
http://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&LESSON_ID=2943&LESSON_PATH=3913.4776....

проверить работу агентов можно тут - ваш-сайт-ру/bitrix/admin/site_checker.php?lang=ru

документация по модулю: https://bitbucket.org/artlux/mlife.smsservices/wiki/Home

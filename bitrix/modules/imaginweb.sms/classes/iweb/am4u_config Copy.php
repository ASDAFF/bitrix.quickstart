<?php

	if(!defined('IWEB_AM4U_HTTPS_ADDRESS')) define("IWEB_AM4U_HTTPS_ADDRESS", "http://sms.am4u.ru/"); //HTTPS-Адрес, к которому будут обращаться скрипты. Со слэшем на конце.
	if(!defined('IWEB_AM4U_HTTP_ADDRESS')) define("IWEB_AM4U_HTTP_ADDRESS", "http://sms.am4u.ru/"); //HTTP-Адрес, к которому будут обращаться скрипты. Со слэшем на конце.
	if(!defined('IWEB_AM4U_HTTPS_CHARSET')) define("IWEB_AM4U_HTTPS_CHARSET", LANG_CHARSET); //кодировка ваших скриптов. cp1251 - для Windows-1251, либо же utf-8 для, сообственно - utf-8 :)
	if(!defined('IWEB_AM4U_HTTPS_METHOD')) define("IWEB_AM4U_HTTPS_METHOD", "curl"); //метод, которым отправляется запрос (curl)
	if(!defined('IWEB_AM4U_USE_HTTPS')) define("IWEB_AM4U_USE_HTTPS", 0); //1 - использовать HTTPS-адрес, 0 - HTTP
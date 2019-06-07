<?
$request = \Bitrix\Main\Context::getCurrent()->getRequest();
$httpHost = ($request->isHttps() ? 'https://' : 'http://') . $request->getHttpHost();

$MESS['AOS_MACROS_EDIT_NOTE_1'] = '
<p>
	<b>НАЗВАНИЕ МАКРОСА</b><br>
	Название может начинаться только с буквы или символа подчеркивания и состоит из букв латинского алфавита, цифр и подчеркиваний.<br>
	Общепринято названия писать в #ВЕРХНЕМ_РЕГИСТРЕ# заменяя пробелы  знаком подчеркивания.<br>
	Все макросы записываются в виде: #MACROS_NAME#<br>
</p>
<p>
	<b>ЗНАЧЕНИЕ МАКРОСА</b><br>
	Любое текстовое значение, форматированный текст, ссылка на страницу, картинку, видеоролик YouTube, макросы заказа, магазина, сайта и т.д.<br>
	Все доступные <a href="https://tuning-soft.ru/docs/api.orderstatus/" target="_blank">макросы</a>
</p>
<p><b>ССЫЛКИ</b></p>
<p>
	<b>Можно использовать относительные ссылки:</b><br>
	 /personal/payment/?ORDER_ID=#ID#&HASH=#HASH#
</p>
<p>
	<b>Можно использовать относительные ссылки c макросом адреса сайта:</b><br>
	 #SERVER_URL#/personal/payment/?ORDER_ID=#ID#&HASH=#HASH#
</p>
<p>
	<b>Можно использовать прямые ссылки:</b><br>
	'. $httpHost .'/personal/payment/?ORDER_ID=#ID#&HASH=#HASH# 
</p>
<p>
	<b>Пример создания макроса #PAYMENT_LINK# - ссылка на оплату:</b><br>
	Название: PAYMENT_LINK<br>
	Значение: '. $httpHost .'/personal/payment/?ORDER_ID=#ID#&HASH=#HASH#<br>
	<br>
	Теперь в описаниях к статусам заказа можно использовать этот макрос: #PAYMENT_LINK#
</p>
';

$MESS['AOS_MACROS_EDIT_TITLE']            = 'Новый макрос';
$MESS['AOS_MACROS_EDIT_FIELD_ERROR']      = '#FIELD# - обязательное поле';
$MESS['AOS_MACROS_EDIT_NAME_VALID_ERROR'] = 'Название макроса неверного формата, смотрите правила ниже';
$MESS['AOS_MACROS_EDIT_TAB_NAME']         = 'Настройка';
$MESS['AOS_MACROS_EDIT_TAB_TITLE']        = 'Настройка макроса';
$MESS['AOS_MACROS_EDIT_DELETE_CONFIRM']   = 'Будет удалена вся информация, связанная с этой записью. Продолжить?';

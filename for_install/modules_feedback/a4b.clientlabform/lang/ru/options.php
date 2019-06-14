<?
$MESS["CLIENTLAB_FORM_OPTIONS_TITLE"]="Основные настройки";
$MESS["CLIENTLAB_FORM_IBLOCK_ID_TITLE"]="ID инфоблока для результатов форм";
$MESS["CLIENTLAB_FORM_BTN_APPLY"]="Применить";
$MESS["CLIENTLAB_FORM_RE_TITLE"]="Настройки для ReCaptcha";
$MESS["CLIENTLAB_FORM_RE_SITE_KEY"]="Ключ сайта";
$MESS["CLIENTLAB_FORM_RE_SEC_KEY"]="Секретный ключ";
$MESS["CLIENTLAB_FORM_RE_REG_LINK"]="Регистрация сайта";

?>
<?php $MESS["CLIENTLAB_FORM_EVENTS_DESCRIPTION"]='
<h2>Список событий модуля</h2>
<table class="events-table">
	<thead>
		<td>Пример подписки на событие</td>
		<td>Переменные передаваемые в функцию обработчик</td>
		<td>Описание</td>
	</thead>
	<tr>
		<td><code>AddEventHandler("a4b.clientlabform", "OnEmailSent", "OnEmailSentHandler");</code></td>
		<td><code>array $formResult, array $Errors</code></td>
		<td>Событие вызываемое при успешной отправке почтового сообщения</td>
	</tr>
	<tr>
		<td><code>AddEventHandler("a4b.clientlabform", "OnEmailSentError", "OnEmailSentErrorHandler");</code></td>
		<td><code>array $formResult, array $Errors</code></td>
		<td>Событие вызываемое при ошибке отправки почтового сообщения</td>
	</tr>
	<tr>
		<td><code>AddEventHandler("a4b.clientlabform", "OnIBlockAdd", "OnIBlockAddHandler");</code></td>
		<td><code>array $formResult, array $Errors</code></td>
		<td>Событие вызываемое при успешном добавлении элемента инфоблока</td>
	</tr>
	<tr>
		<td><code>AddEventHandler("a4b.clientlabform", "OnIBlockAddError", "OnIBlockAddErrorHandler");</code></td>
		<td><code>array $formResult, array $Errors</code></td>
		<td>Событие вызываемое при ошибке добавления элемента инфоблока</td>
	</tr>
</table>


<h2>Список событий фронтенда</h2>
<table class="events-table">
	<thead>
		<td>Пример подписки на событие</td>
		<td>Описание</td>
	</thead>
	<tr>
		<td><code>forms.subscribe(function(data){
				console.log("Valid form!", data)
				}, forms.subscribtionsList.successful_validation);</code></td>
		<td>Подписка на событие удачной валидации</td>
	</tr>
	<tr>
		<td><code>forms.subscribe(function(data){
				console.log("Invalid form!", data)
				}, forms.subscribtionsList.unsuccessful_validation);</code></td>
		<td>Подписка на событие неудачной валидации</td>
	</tr>
	<tr>
		<td><code>forms.subscribe(function(data){
				console.log("Successful send!", data)
				}, forms.subscribtionsList.succesfull_send);</code></td>
		<td>Подписка на событие удачной отправки</td>
	</tr>
	<tr>
		<td><code>forms.subscribe(function(data){
				console.log("Unsuccessful send!", data)
				}, forms.subscribtionsList.unsuccesfull_send);</code></td>
		<td>Подписка на событие неудачной отправки</td>
	</tr>
	
	<tr>
		<td><code>forms.subscribe(function(data){
				console.log("Recaptcha error!", data)
				}, forms.subscribtionsList.recaptcha_error);</code></td>
		<td>Подписка на событие неверной капчи.</td>
	</tr>
</table>

<h2>Дополнительные настройки компонента</h2>
<table class="events-table">
	<thead>
		<td>Ключ массива настроек</td>
		<td>Значение</td>
		<td>Описание</td>
		<td>Пример</td>
	</thead>
	<tr>
		<td>ADDITIONAL_HIDDEN_FIELDS</td>
		<td>
			<pre><code>
array(
	array(
		"NAME" => "field-name", 
		"VALUE" => "test",
		"LABEL"=>"Какой-то лейбл"
	),
	array(
		"NAME" => "field-name",
		"VALUE" => "test",
		"LABEL"=>"Необязательный"
	)
)</code></pre>
		</td>
		<td>Возможность добавления в форму скрытых полей без визуального редактора.</td>
		<td>
			<pre>
<code>
"ADDITIONAL_HIDDEN_FIELDS" => array(
	array(
		"NAME" => "field-name",
		"VALUE" => "test",
		"LABEL"=>"Какойто лейбл"
	)
)
</code>
			</pre>
		</td>
	</tr>
</table>


<style>
.events-table thead td{
	font-weight: bold;
}
.events-table td {
	border-bottom: 1px solid #eee;
}
.events-table td {
	padding: 8px;
	vertical-align: top;
	text-align: left;
}


.clform-options .row {
	overflow: hidden;
	padding: 15px;
}
.clform-options label {
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	display: block;
	margin: 20px;
	font-weight: bold;
}

.clform-options label input {
	margin-left: 20px;
}
</style>';
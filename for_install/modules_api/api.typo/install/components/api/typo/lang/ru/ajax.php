<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

$MESS['API_TYPO_AJAX_SEND_OK'] = 'Спасибо!<br>Cообщение отправлено';

$MESS['API_TYPO_AJAX_MESSAGE'] = '
<div style="padding:10px;border-bottom:1px dashed #dadada;">
	<div style="font-weight:bold;">Текст ошибки</div>
	<div style="color: red">#ERROR#</div>
</div>
<div style="padding:10px;border-bottom:1px dashed #dadada;">
	<div style="font-weight:bold;">Комментарий</div>
	#COMMENT#
</div>
<div style="padding:10px;border-bottom:1px dashed #dadada;">
	<div style="font-weight:bold;">Страница</div>
	<a href="#URL#" target="_blank">#URL#</a>
</div>
';

$MESS['API_TYPO_AJAX_MESSAGE_SHORT'] = '
<div style="padding:10px;border-bottom:1px dashed #dadada;">
	<div style="font-weight:bold;">Текст ошибки</div>
	<div style="color: red">#ERROR#</div>
</div>
<div style="padding:10px;border-bottom:1px dashed #dadada;">
	<div style="font-weight:bold;">Страница</div>
	<a href="#URL#" target="_blank">#URL#</a>
</div>
';
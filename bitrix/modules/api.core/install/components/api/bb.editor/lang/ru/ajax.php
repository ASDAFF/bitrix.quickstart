<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

$MESS['API_BB_EDITOR_HTML'] = <<<HTML
<style>
.api-bb-ref-table {width: 100%; border: 0; border-collapse: collapse; border-spacing: 0; text-align: left;font-size: 14px;}
.api-bb-ref-table td,
.api-bb-ref-table th{vertical-align: top;border: 1px solid #e9ecef;padding: 5px}
.api-bb-ref-table td[colspan="2"]{background-color: #fafafa;font-weight: bold}
.api-bb-ref-table hr{margin: 10px 0}
.api-bb-ref-table .h, .api-bb-ref-table .alert{margin: 0 0 5px}
.api-bb-ref-table .bb-code, .api-bb-ref-table .bb-quote, .api-bb-ref-table .bb-spoiler, .api-bb-ref-table .bb-hide{margin: 0}
</style>
<table class="api-bb-ref-table">
<thead>
<tr>
<th>BB-коды</th>
<th>Вывод</th>
</tr>
</thead>
<tbody>

<tr><td colspan="2">Заголовок</td></tr>
<tr>
	<td>
		[h=1]H1 Заголовок[/h]<br>
		[h=2]H2 Заголовок[/h]<br>
		[h=3]H3 Заголовок[/h]<br>
		[h=4]H4 Заголовок[/h]<br>
		[h=5]H5 Заголовок[/h]<br>
		[h=6]H6 Заголовок[/h]<br>
	</td>
	<td>
		<div class="h h1">H1 Заголовок</div>
		<div class="h h2">H2 Заголовок</div>
		<div class="h h3">H3 Заголовок</div>
		<div class="h h4">H4 Заголовок</div>
		<div class="h h5">H5 Заголовок</div>
		<div class="h h6">H6 Заголовок</div>
	</td>
</tr>

<tr><td colspan="2">Абзац</td></tr>
<tr>
	<td>[p]Абзац текста[p]</td>
	<td><p>Абзац текста</p></td>
</tr>

<tr><td colspan="2">Alerts</td></tr>
<tr>
	<td>
		[alert=primary]This is a primary alert[/alert]<br>
		[alert=secondary]This is a primary alert[/alert]<br>
		[alert=success]This is a primary alert[/alert]<br>
		[alert=danger]This is a primary alert[/alert]<br>
		[alert=warning]This is a primary alert[/alert]<br>
		[alert=info]This is a primary alert[/alert]<br>
		[alert=light]This is a primary alert[/alert]<br>
		[alert=dark]This is a primary alert[/alert]<br>
	</td>
	<td>
		<div class="alert alert-primary">This is a primary alert</div>
		<div class="alert alert-secondary">This is a secondary alert</div>
		<div class="alert alert-success">This is a success alert</div>
		<div class="alert alert-danger">This is a danger alert</div>
		<div class="alert alert-warning">This is a warning alert</div>
		<div class="alert alert-info">This is a info alert</div>
		<div class="alert alert-light">This is a light alert</div>
		<div class="alert alert-dark">This is a dark alert</div>
	</td>
</tr>

<tr><td colspan="2">Начертание</td></tr>
<tr>
	<td>
		[b]Жирный текст[/b]<br>
		[i]Курсивный текст[/i]<br>
		[s]Зачеркнутый текст[/s]<br>
	</td>
	<td>
		<b>Жирный текст</b><br>
		<i>Курсивный текст</i><br>
		<s>Зачеркнутый текст</s><br>
	</td>
</tr>

<tr><td colspan="2">Размер текста</td></tr>
<tr>
	<td>
		[size=1]S1 Размер текста[/size]<br>
		[size=2]S2 Размер текста[/size]<br>
		[size=3]S3 Размер текста[/size]<br>
		[size=4]S4 Размер текста[/size]<br>
		[size=5]S5 Размер текста[/size]<br>
		[size=6]S6 Размер текста[/size]<br>
	</td>
	<td>
	 <span class="h h1">S1 Размер текста</span><br>
	 <span class="h h2">S2 Размер текста</span><br>
	 <span class="h h3">S3 Размер текста</span><br>
	 <span class="h h4">S4 Размер текста</span><br>
	 <span class="h h5">S5 Размер текста</span><br>
	 <span class="h h6">S6 Размер текста</span><br>
	</td>
</tr>

<tr><td colspan="2">Код</td></tr>
<tr>
	<td>[code]&lt;script&gt;alert('Hi')&lt;/script&gt;[/code]</td>
	<td><pre class="bb-code">&lt;script&gt;alert('Hi')&lt;/script&gt;</pre></td>
</tr>

<tr><td colspan="2">Цитата</td></tr>
<tr>
	<td>[quote]Цитата[/quote]</td>
	<td><blockquote class="bb-quote">Цитата</blockquote></td>
</tr>

<tr><td colspan="2">Спойлер</td></tr>
<tr>
	<td>[spoiler]Скрытый спойлером текст[/spoiler]</td>
	<td>
		<div class="bb-spoiler"><div class="bb-spoiler-title" onclick="jQuery.fn.apiBB('showSpoiler',this);"><span></span>Скрытый текст</div><div class="bb-spoiler-text">Скрытый спойлером текст</div></div>
	</td>
</tr>

<tr><td colspan="2">Скрытый контент</td></tr>
<tr>
	<td>[hide]Скрытый контент[/hide]</td>
	<td>
		<div class="bb-hide"><div class="bb-hide-title"><span></span>Скрытый контент</div><div class="bb-hide-text">Скрытый контент видят только зарегистрированные пользователи</div></div>
	</td>
</tr>

<tr><td colspan="2">Ссылка</td></tr>
<tr>
	<td>
	[url=https://example.com/]Ссылка текстом[/url]<br>
	[url=https://example.com/][/url]<br>
	</td>
	<td>
		<a href="#">Ссылка текстом</a><br>
		<a href="#">https://example.com/</a><br>
	</td>
</tr>

<tr><td colspan="2">Изображение</td></tr>
<tr>
	<td>
	[img]https://example.com/picture.gif[/img]
	</td>
	<td>
	Изображение выводится в теге &lt;img&gt; 
	</td>
</tr>

<tr><td colspan="2">Разделительная линия</td></tr>
<tr>
	<td>[hr][/hr]</td><td><hr></td>
</tr>

<tr><td colspan="2">Смайлы</td></tr>
<tr>
	<td>:smile: :wink: :laughing: :sunglasses: :disappointed: :blush: :cry: :rage: :open_mouth: :like:</td>
	<td>
	<img class="bb-smile" src="/bitrix/images/main/smiles/3/bx_smile_smile.png"> <img class="bb-smile" src="/bitrix/images/main/smiles/3/bx_smile_wink.png"> <img class="bb-smile" src="/bitrix/images/main/smiles/3/bx_smile_biggrin.png"> <img class="bb-smile" src="/bitrix/images/main/smiles/3/bx_smile_cool.png"> <img class="bb-smile" src="/bitrix/images/main/smiles/3/bx_smile_sad.png"> <img class="bb-smile" src="/bitrix/images/main/smiles/3/bx_smile_redface.png"> <img class="bb-smile" src="/bitrix/images/main/smiles/3/bx_smile_cry.png"> <img class="bb-smile" src="/bitrix/images/main/smiles/3/bx_smile_evil.png"> <img class="bb-smile" src="/bitrix/images/main/smiles/3/bx_smile_eek.png"> <img class="bb-smile" src="/bitrix/images/main/smiles/3/bx_smile_like.png">
</td>
</tr>

</tbody>
</table>

HTML;

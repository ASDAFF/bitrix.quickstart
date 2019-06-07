<?php

//TRANSLIT
$MESS['TRANSLIT_FROM'] = "а,б,в,г,д,е,ё,ж,з,и,й,к,л,м,н,о,п,р,с,т,у,ф,х,ц,ч,ш,щ,ъ,ы,ь,э,ю,я,А,Б,В,Г,Д,Е,Ё,Ж,З,И,Й,К,Л,М,Н,О,П,Р,С,Т,У,Ф,Х,Ц,Ч,Ш,Щ,Ъ,Ы,Ь,Э,Ю,Я,@,\xb3,\xb2";
$MESS['TRANSLIT_TO']   = "a,b,v,g,d,e,ye,zh,z,i,y,k,l,m,n,o,p,r,s,t,u,f,kh,ts,ch,sh,shch,,y,,e,yu,ya,A,B,V,G,D,E,YE,ZH,Z,I,Y,K,L,M,N,O,P,R,S,T,U,F,KH,TS,CH,SH,SHCH,,Y,,E,YU,YA,at,i,I";

//GENERAL
$MESS['CONFIRM_DELETE']   = 'Действие необратимо. Удалить запись?';
$MESS['ENTRY_EDIT']       = 'Редактирование записи №';
$MESS['ENTRY_NEW']        = 'Новая запись';
$MESS['ENTRY_ADD_ERROR']  = 'Ошибка добавления/изменения записи';
$MESS['ENTRY_ADD_OK']     = 'Изменения сохранены';
$MESS['TAB_OPTIONS']      = 'Настройки';
$MESS['FIELD_ERROR_TEXT'] = 'Поле "#FIELD#" не может быть пустым.';

//SEND ERRORS
$MESS['NO_FOUND_EVENT_MESSAGE']  = 'Не найден почтовый шаблон формы.<br>Возможно настроена многосайтовость и/или в почтовом шаблоне в админке не задана/перепутана привязка к текущему сайту.';
$MESS['NO_WORK_MAIL_FUNCTION']   = 'Не работает php-функция mail(), скорее всего на сервере не хватает памяти.';
$MESS['API_FDI_MAIL_SEND_ERROR'] = 'Ошибка! Сообщение не отправляется';

//PAGE_VARS
$MESS['PAGE_VARS_TITLE']      = 'ПЕРЕМЕННЫЕ СТРАНИЦЫ';
$MESS['PAGE_VARS_FORM_TITLE'] = 'Заголовок формы';
$MESS['PAGE_VARS_PAGE_TITLE'] = 'Заголовок страницы';
$MESS['PAGE_VARS_PAGE_URL']   = 'URL-адрес страницы';
$MESS['PAGE_VARS_DIR_URL']    = 'URL-адрес раздела';
$MESS['PAGE_VARS_DATE_TIME']  = 'Дата и время';
$MESS['PAGE_VARS_DATE']       = 'Дата';
$MESS['PAGE_VARS_IP']         = 'IP-адрес посетителя';

$MESS['SERVER_VARS_TITLE'] = 'ПЕРЕМЕННЫЕ СЕРВЕРА';
$MESS['UTM_VARS_TITLE']    = 'ПЕРЕМЕННЫЕ UTM';



$MESS['API_FD_INCLUDE_VARS_TPL']     = "<br><br>
<table style='font-size:13px;color:#333333; background-color: #e1e1e1; width: 100%'  cellspacing='1' cellpadding='3'>
	<thead>
		<tr bgcolor='#fafafa'>
			<th colspan='2'>#TITLE#</th>
		</tr>
	</thead>
	<tbody>
	#ROWS#
	</tbody>
</table>
";
$MESS['API_FD_INCLUDE_VARS_ROW_TPL'] = "
	<tr bgcolor='#ffffff'>
		<th align='left' width='190'>#NAME#</th>
		<td>#VALUE#</td>
	</tr>
";

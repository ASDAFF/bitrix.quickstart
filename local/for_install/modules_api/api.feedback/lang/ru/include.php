<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

$MESS['TRANSLIT_FROM'] = 'а,б,в,г,д,е,ё,ж,з,и,й,к,л,м,н,о,п,р,с,т,у,ф,х,ц,ч,ш,щ,ъ,ы,ь,э,ю,я,А,Б,В,Г,Д,Е,Ё,Ж,З,И,Й,К,Л,М,Н,О,П,Р,С,Т,У,Ф,Х,Ц,Ч,Ш,Щ,Ъ,Ы,Ь,Э,Ю,Я,@';
$MESS['TRANSLIT_TO']   = 'a,b,v,g,d,e,ye,zh,z,i,y,k,l,m,n,o,p,r,s,t,u,f,kh,ts,ch,sh,shch,,y,,e,yu,ya,A,B,V,G,D,E,YE,ZH,Z,I,Y,K,L,M,N,O,P,R,S,T,U,F,KH,TS,CH,SH,SHCH,,Y,,E,YU,YA,at';

//SOME ERRORS
$MESS['NO_FOUND_EVENT_MESSAGE']          = 'Не найден почтовый шаблон формы.<br>Возможно настроена многосайтовость и/или в почтовом шаблоне в админке не задана/перепутана привязка к текущему сайту.';
$MESS['NO_WORK_MAIL_FUNCTION']           = 'Не работает php-функция mail(), скорее всего на сервере не хватает памяти.';
$MESS['DOWNLOAD_FILES']                  = 'Загруженные файлы';
$MESS['NOT_FOUND_IBLOCK_PROP_TICKET_ID'] = 'Внимание! В инфоблоке не найдено свойство типа "число" с кодом TICKET_ID. Нумерация элементов невозможна.';

$MESS['SERVER_VARS_TITLE']  = '== $_SERVER ==';
$MESS['REQUEST_VARS_TITLE'] = '== $_REQUEST ==';
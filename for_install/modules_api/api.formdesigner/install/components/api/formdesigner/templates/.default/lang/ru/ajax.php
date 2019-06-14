<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

$MESS['AFD_AJAX_UPLOAD_DROP'] = 'Перетащите сюда файлы или нажмите для выбора';
$MESS['AFD_AJAX_UPLOAD_INFO'] = 'Максимальный размер загружаемого файла #UPLOAD_FILE_SIZE# в формате #FILE_TYPE#.<br>
Максимальное количество файлов - #UPLOAD_FILE_LIMIT# шт.<br>';

$MESS['AFD_AJAX_UPLOAD_onFileSizeError'] = '{{fileName}} размером {{fileSize}} превышает допустимый размер <b>{{maxFileSize}}</b>';
$MESS['AFD_AJAX_UPLOAD_onFileTypeError'] = 'Тип файла {{fileType}} не соответствует разрешенному {{allowedTypes}}';
$MESS['AFD_AJAX_UPLOAD_onFileExtError']  = 'Разрешены следующие расширения файлов: <b>{{extFilter}}</b>';
$MESS['AFD_AJAX_UPLOAD_onFilesMaxError'] = 'Разрешено максимум {{maxFiles}} файлов';

//CAPTCHA
$MESS['AFD_AJAX_FIELD_CAPTCHA_SID']     = 'Код защиты от автоматических сообщений';
$MESS['AFD_AJAX_FIELD_CAPTCHA_WORD']    = 'Введите код защиты';
$MESS['AFD_AJAX_FIELD_CAPTCHA_REFRESH'] = 'Нажмите, чтобы обновить код защиты';
$MESS['AFD_AJAX_FIELD_CAPTCHA_LOADING'] = 'Закгрузка captcha...';
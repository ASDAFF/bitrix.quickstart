<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

$MESS['API_REVIEWS_MODULE_ERROR'] = 'Модуль "TS Умные отзывы" не установлен';

$MESS['ARTA_RIGHTS_ERROR'] = 'У вас недостаточно прав для работы с модулем';

//Alert video
$MESS['ARTA_ALERT_WRONG_VIDEO_URL'] = array(
	 'type'    => 'info',
	 'theme'   => 'jbox',
	 'title'   => 'Нераспознанный адрес!',
	 'content' => 'Попробуйте ввести другой',
);
$MESS['ARTA_ALERT_UPLOAD_VIDEO_LIMIT'] = array(
	 'type'    => 'info',
	 'theme'   => 'jbox',
	 'title'   => 'Больше загружать нельзя!',
	 'content' => 'Превышен лимит загружаемых видео',
);
$MESS['ARTA_ALERT_VIDEO_ISSET'] = array(
	 'type'    => 'info',
	 'theme'   => 'jbox',
	 'title'   => 'Повтор видео!',
	 'content' => 'Данный видео уже загружен, попробуйте выбрать другое',
);

//Alert file
$MESS['ARTA_ALERT_UPLOAD_FILE_LIMIT'] = array(
	 'type'    => 'info',
	 'theme'   => 'jbox',
	 'title'   => 'Больше загружать нельзя!',
	 'content' => 'Превышен лимит загружаемых файлов',
);
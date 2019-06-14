<?php
use Bitrix\Main\Loader,
	 Bitrix\Main\Web\Json,
	 Bitrix\Main\Application,
	 Bitrix\Main\Text\Encoding,
	 Bitrix\Main\Localization\Loc;

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

/**
 * Bitrix vars
 *
 * @var CBitrixComponent $this
 * @var array            $arParams
 * @var array            $arResult
 * @var string           $componentPath
 * @var string           $componentName
 * @var string           $componentTemplate
 *
 * @var string           $parentComponentPath
 * @var string           $parentComponentName
 * @var string           $parentComponentTemplate
 *
 * @var CDatabase        $DB
 * @var CUser            $USER
 * @var CMain            $APPLICATION
 */

Loc::loadMessages(__FILE__);

$arResultModules = array(
	 'api.reviews' => Loader::includeModule('api.reviews'),
	 'sale'        => Loader::includeModule('sale'),
);

if(!$arResultModules['api.reviews']) {
	ShowError(GetMessage('API_REVIEWS_MODULE_ERROR'));
	return;
}

//Inc template lang
$templateFile = CApiReviews::getTemplateFile($this);
Loc::loadMessages($templateFile);


use Api\Reviews\Tools,
	 Api\Reviews\Event,
	 Api\Reviews\Agent,
	 Api\Reviews\VideoTable,
	 Api\Reviews\ReviewsTable;


$context      = Application::getInstance()->getContext();
$request      = $context->getRequest();
$scheme       = $request->isHttps() ? 'https://' : 'http://';
$httpHost     = $scheme . $request->getHttpHost();
$isUtfMode    = Application::isUtfMode();
$documentRoot = Application::getDocumentRoot();


$arParams['FORM_ID']    = $this->GetEditAreaId($this->__currentCounter);
$arParams['SITE_ID']    = SITE_ID;
$arParams['PAGE_URL']   = ($arParams['PAGE_URL'] ? $arParams['PAGE_URL'] : $httpHost . $request->getRequestUri());
$arParams['PAGE_TITLE'] = ($arParams['PAGE_TITLE'] ? $arParams['PAGE_TITLE'] : $APPLICATION->GetTitle());
$arParams['DETAIL_URL'] = ($arParams['~DETAIL_URL'] ? $httpHost . $arParams['~DETAIL_URL'] . $arParams['~DETAIL_HASH'] : '');
//if($arParams['USE_LIST'] == 'Y' && $arParams['~DETAIL_HASH'])
//$arParams['DETAIL_URL'] .= $arParams['~DETAIL_HASH'];


//==============================================================================
//                     MULTILANGUAGE PHRASES REPLACE
//==============================================================================
$arParams['MESS_SHOP_NAME']     = $arParams['~SHOP_NAME'] ? $arParams['~SHOP_NAME'] : Loc::getMessage('API_REVIEWS_FORM_MESS_SHOP_NAME');
$arParams['MESS_SHOP_TEXT']     = $arParams['~SHOP_TEXT'] ? $arParams['~SHOP_TEXT'] : Loc::getMessage('API_REVIEWS_FORM_MESS_SHOP_TEXT');
$arParams['MESS_SHOP_BTN_TEXT'] = $arParams['~SHOP_BTN_TEXT'] ? $arParams['~SHOP_BTN_TEXT'] : Loc::getMessage('API_REVIEWS_FORM_MESS_SHOP_BTN_TEXT');
$arParams['MESS_FORM_TITLE']    = $arParams['~FORM_TITLE'] ? $arParams['~FORM_TITLE'] : Loc::getMessage('API_REVIEWS_FORM_MESS_FORM_TITLE');
$arParams['MESS_FORM_SUBTITLE'] = trim($arParams['~FORM_SUBTITLE']);

$arParams['MESS_STAR_RATING_1'] = CUtil::JSEscape($arParams['~MESS_STAR_RATING_1'] ? $arParams['~MESS_STAR_RATING_1'] : Loc::getMessage('API_REVIEWS_FORM_MESS_STAR_RATING_1'));
$arParams['MESS_STAR_RATING_2'] = CUtil::JSEscape($arParams['~MESS_STAR_RATING_2'] ? $arParams['~MESS_STAR_RATING_2'] : Loc::getMessage('API_REVIEWS_FORM_MESS_STAR_RATING_2'));
$arParams['MESS_STAR_RATING_3'] = CUtil::JSEscape($arParams['~MESS_STAR_RATING_3'] ? $arParams['~MESS_STAR_RATING_3'] : Loc::getMessage('API_REVIEWS_FORM_MESS_STAR_RATING_3'));
$arParams['MESS_STAR_RATING_4'] = CUtil::JSEscape($arParams['~MESS_STAR_RATING_4'] ? $arParams['~MESS_STAR_RATING_4'] : Loc::getMessage('API_REVIEWS_FORM_MESS_STAR_RATING_4'));
$arParams['MESS_STAR_RATING_5'] = CUtil::JSEscape($arParams['~MESS_STAR_RATING_5'] ? $arParams['~MESS_STAR_RATING_5'] : Loc::getMessage('API_REVIEWS_FORM_MESS_STAR_RATING_5'));

//RULES
$arParams['MESS_RULES_TEXT'] = $arParams['~RULES_TEXT'] ? $arParams['~RULES_TEXT'] : Loc::getMessage('API_REVIEWS_FORM_MESS_RULES_TEXT');
$arParams['MESS_RULES_LINK'] = $arParams['~RULES_LINK'] ? $arParams['~RULES_LINK'] : Loc::getMessage('API_REVIEWS_FORM_MESS_RULES_LINK');

//EULA
$arParams['USE_EULA']          = $arParams['~USE_EULA'] == 'Y';
$arParams['MESS_EULA']         = trim($arParams['~MESS_EULA']);
$arParams['MESS_EULA_CONFIRM'] = trim($arParams['~MESS_EULA_CONFIRM']);

//PRIVACY
$arParams['USE_PRIVACY']          = $arParams['~USE_PRIVACY'] == 'Y';
$arParams['MESS_PRIVACY']         = trim($arParams['~MESS_PRIVACY']);
$arParams['MESS_PRIVACY_LINK']    = trim($arParams['~MESS_PRIVACY_LINK']);
$arParams['MESS_PRIVACY_CONFIRM'] = trim($arParams['~MESS_PRIVACY_CONFIRM']);


$arParams['MESS_ADD_REVIEW_VIZIBLE']    = $arParams['~MESS_ADD_REVIEW_VIZIBLE'] ? $arParams['~MESS_ADD_REVIEW_VIZIBLE'] : Loc::getMessage('API_REVIEWS_FORM_MESS_ADD_REVIEW_VIZIBLE');
$arParams['MESS_ADD_REVIEW_MODERATION'] = $arParams['~MESS_ADD_REVIEW_MODERATION'] ? $arParams['~MESS_ADD_REVIEW_MODERATION'] : Loc::getMessage('API_REVIEWS_FORM_MESS_ADD_REVIEW_MODERATION');
$arParams['MESS_ADD_REVIEW_ERROR']      = $arParams['~MESS_ADD_REVIEW_ERROR'] ? $arParams['~MESS_ADD_REVIEW_ERROR'] : Loc::getMessage('API_REVIEWS_FORM_MESS_ADD_REVIEW_ERROR');

$arParams['MESS_ADD_REVIEW_EVENT_THEME'] = $arParams['~MESS_ADD_REVIEW_EVENT_THEME'] ? $arParams['~MESS_ADD_REVIEW_EVENT_THEME'] : Loc::getMessage('API_REVIEWS_FORM_MESS_ADD_REVIEW_EVENT_THEME');
if(!$arParams['MESS_ADD_REVIEW_EVENT_THEME'])
	$arParams['MESS_ADD_REVIEW_EVENT_THEME'] = $arParams['MESS_FORM_TITLE'];

$arParams['MESS_ADD_REVIEW_EVENT_TEXT'] = $arParams['~MESS_ADD_REVIEW_EVENT_TEXT'] ? $arParams['~MESS_ADD_REVIEW_EVENT_TEXT'] : Loc::getMessage('API_REVIEWS_FORM_MESS_ADD_REVIEW_EVENT_TEXT');



//==============================================================================
//                                $arParams
//==============================================================================
$arParams['THEME']           = ($arParams['THEME'] ? trim($arParams['THEME']) : 'orange');
$arParams['CITY_VIEW']       = $arParams['CITY_VIEW'] == 'Y';
$arParams['USE_PLACEHOLDER'] = $arParams['USE_PLACEHOLDER'] == 'Y';
$arParams['EMAIL_TO']        = trim($arParams['EMAIL_TO']);
$arParams['IBLOCK_ID']       = (int)$arParams['IBLOCK_ID'];
$arParams['SECTION_ID']      = (int)$arParams['SECTION_ID'];
$arParams['ELEMENT_ID']      = (int)$arParams['ELEMENT_ID'];
$arParams['ORDER_ID']        = trim($arParams['ORDER_ID']);
$arParams['URL']             = trim($arParams['URL']);

//FILES_SETTINGS
$arParams['UPLOAD_FILE_TYPE']    = trim($arParams['UPLOAD_FILE_TYPE']);
$arParams['UPLOAD_FILE_SIZE']    = trim($arParams['UPLOAD_FILE_SIZE']);
$arParams['UPLOAD_MAX_FILESIZE'] = $arParams['UPLOAD_FILE_SIZE'] ? Tools::getFileSizeInBytes($arParams['UPLOAD_FILE_SIZE']) : 10000 * 1024; //10M
$arParams['UPLOAD_FOLDER']       = '/upload/api_reviews';
$arParams['UPLOAD_TMP_DIR']      = $documentRoot . '/upload/tmp/api_reviews';
$arParams['UPLOAD_FILE_LIMIT']   = intval($arParams['UPLOAD_FILE_LIMIT']);
$arParams['UPLOAD_VIDEO_LIMIT']  = intval($arParams['UPLOAD_VIDEO_LIMIT']);


$arParams['PREMODERATION']   = ($arParams['PREMODERATION'] ? $arParams['PREMODERATION'] : 'N');
$arParams['DISPLAY_FIELDS']  = (array)$arParams['DISPLAY_FIELDS'];
$arParams['REQUIRED_FIELDS'] = (array)$arParams['REQUIRED_FIELDS'];
$arParams['DELIVERY']        = (array)$arParams['DELIVERY'];

foreach($arParams["DISPLAY_FIELDS"] as $k => $v)
	if($v === "")
		unset($arParams["DISPLAY_FIELDS"][ $k ]);

foreach($arParams["REQUIRED_FIELDS"] as $k => $v)
	if($v === "")
		unset($arParams["REQUIRED_FIELDS"][ $k ]);

foreach($arParams["DELIVERY"] as $k => $v)
	if($v === "")
		unset($arParams["DELIVERY"][ $k ]);


//Лэнги полей
if($arParams['DISPLAY_FIELDS']) {
	foreach($arParams['DISPLAY_FIELDS'] as $FIELD) {

		//Заменить встроенные названия полей на свои
		$pFieldNameMess   = $arParams[ 'MESS_FIELD_NAME_' . $FIELD ];
		$tplFieldNameMess = Loc::getMessage('API_REVIEWS_FORM_' . $FIELD);

		$arParams[ 'MESS_FIELD_NAME_' . $FIELD ] = ($pFieldNameMess ? $pFieldNameMess : $tplFieldNameMess['NAME']);


		//Заменить встроенные placeholder полей на свои
		$pFieldPlaceholderMess   = $arParams[ 'MESS_FIELD_PLACEHOLDER_' . $FIELD ];
		$tplFieldPlaceholderMess = Loc::getMessage('API_REVIEWS_FORM_' . $FIELD);

		$arParams[ 'MESS_FIELD_PLACEHOLDER_' . $FIELD ] = ($pFieldPlaceholderMess ? $pFieldPlaceholderMess : $tplFieldPlaceholderMess['PLACEHOLDER']);
	}
}



//ADDITIONAL_SETTINGS
$arParams['INCLUDE_CSS'] = $arParams['INCLUDE_CSS'] == 'Y';
$arParams['THEME']       = ($arParams['THEME'] ? $arParams['THEME'] : 'flat');

if($arParams['INCLUDE_JQUERY'] && $arParams['INCLUDE_JQUERY'] != 'N') {
	CJSCore::Init($arParams['INCLUDE_JQUERY']);
	$arParams['INCLUDE_JQUERY'] = 'N';
}
CJSCore::Init(array('core', 'session', 'ls'));//array('ajax', 'json', 'ls', 'session', 'jquery', 'popup', 'pull')


///////////////////////////////////////////////////////////////////////////////
//  Upload file and write to $_SESSION
///////////////////////////////////////////////////////////////////////////////


$isAction = ($request->isPost() && $request->get('API_REVIEWS_FORM_ACTION') && check_bitrix_sessid());
if($isAction) {

	$action   = $request->get('API_REVIEWS_FORM_ACTION');
	$response = array(
		 'result'  => 'error',
		 'message' => null,
		 'alert'   => null,
		 'file'    => null,
	);

	if($action == 'FILE_UPLOAD') {

		$arFile    = &$_FILES['FILES'];
		$sessFiles = &$_SESSION['API_REVIEWS_FORM']['FILES'];

		//Проверим лимиты на загружаемые видео
		if($arParams['UPLOAD_FILE_LIMIT']) {
			if($sessFiles && count($sessFiles) == $arParams['UPLOAD_FILE_LIMIT']) {
				$response['alert'] = Loc::getMessage('API_REVIEWS_FORM_ALERT_UPLOAD_FILE_LIMIT');
				unset($arFile);
			}
		}

		if($arFile) {

			//Создаем папку для загрузки временных файлов, если не создана
			if(!is_dir($arParams['UPLOAD_TMP_DIR']))
				if(!mkdir($arParams['UPLOAD_TMP_DIR'], 0755, true))
					$response['message'] = Loc::getMessage('AFDC_WARNING_UPLOAD_TMP_DIR');


			$fileName = Tools::translitFileName($arFile['name']);
			$fileCode = Tools::getUniqueFileName($fileName, 'FILES', $USER->GetID());;
			$destination = $arParams['UPLOAD_TMP_DIR'] . '/' . $fileCode;

			$arFile['name']       = $fileName;
			$arFile['code']       = $fileCode;
			$arFile['size_round'] = \CFile::FormatSize($arFile['size'], 0);
			$arFile['MODULE_ID']  = 'api.reviews';

			/* Sample of file array
			$_FILES => Array(
			 [FILES] => Array
			 (
				 [name] => 2015-01-30_14.33.02.png
				 [type] => image/png
				 [tmp_name] => D:/OpenServer/domains/tuning-soft.os/upload/tmp/api_reviews/56ad434f08e7f87a4e89cd710341d3b8.png
				 [error] => 0
				 [size] => 155756
				 [size_round] => 210.33 KB
				 [code] => 56ad434f08e7f87a4e89cd710341d3b8.png
				 [del] =>
				 [description] =>
			 ),
			);
			*/

			if($arFile['error'] == 0) {

				if(@is_uploaded_file($_FILES['FILES']['tmp_name'])) {
					$res = CFile::CheckFile($arFile, $arParams['UPLOAD_MAX_FILESIZE'], false, $arParams['UPLOAD_FILE_TYPE']);
					if(strlen($res) > 0) {
						$response['message'] = $res;
					}
					else {
						@move_uploaded_file($_FILES['FILES']['tmp_name'], $destination);
					}
				}

				if(file_exists($destination)) {
					$arFile['tmp_name'] = $destination;

					//Main session file array
					$_SESSION['API_REVIEWS_FORM']['FILES'][ $fileCode ] = $arFile;

					//Hide server vars form ajax response!!!
					unset($arFile['tmp_name'], $arFile['MODULE_ID']);
					$response = array(
						 'result'  => 'ok',
						 'message' => null,
						 'file'    => $arFile,
					);
				}
			}
			else {
				$response['message'] = $arFile['error'];
			}

			unset($arFile);
		}
	}
	elseif($action == 'FILE_DELETE') {
		if($fileCode = $request->get('FILE_CODE')) {

			//Удалит файл с диска
			$filePath = $arParams['UPLOAD_TMP_DIR'] . '/' . $fileCode;
			if(is_file($filePath) && file_exists($filePath)) {
				@unlink($filePath);
			}

			//Удалит файл из сессии
			if($arSessFile = &$_SESSION['API_REVIEWS_FORM']['FILES']) {
				if(isset($arSessFile[ $fileCode ]))
					unset($arSessFile[ $fileCode ]);
			}
		}
		unset($fileName, $fileCode, $filePath, $arSessFile);
	}
	elseif($action == 'VIDEO_UPLOAD') {

		$url = trim($request->get('VIDEO_URL'));

		$sessVideos = &$_SESSION['API_REVIEWS_FORM']['VIDEOS'];

		//Проверим лимиты на загружаемые видео
		if($arParams['UPLOAD_VIDEO_LIMIT']) {
			if($sessVideos && count($sessVideos) == $arParams['UPLOAD_VIDEO_LIMIT']) {
				$response['alert'] = Loc::getMessage('API_REVIEWS_FORM_ALERT_UPLOAD_VIDEO_LIMIT');
				unset($url);
			}
		}

		if($url) {

			$videoId = $videoSrv = $videoUrl = $videoTitle = $videoDesc = $imageUrl = '';

			//YouTube
			if(
				 preg_match('/[http|https]+:\/\/(?:www\.|)youtube\.com\/watch\?(?:.*)?v=([a-zA-Z0-9_\-]+)/i', $url, $matches) ||
				 preg_match('/[http|https]+:\/\/(?:www\.|)youtube\.com\/embed\/([a-zA-Z0-9_\-]+)/i', $url, $matches) ||
				 preg_match('/[http|https]+:\/\/(?:www\.|)youtu\.be\/([a-zA-Z0-9_\-]+)/i', $url, $matches)
			) {
				//Исходный адрес ролика
				//https://www.youtube.com/watch?v=8kJbXM1rftk

				//Уменьшенное превью с черными полосами в ужасном качестве
				//http://img.youtube.com/vi/8kJbXM1rftk/0.jpg

				//В исходном коде есть такие ссылки
				//<link rel="shortlink" href="https://youtu.be/8kJbXM1rftk">
				//<link itemprop="embedURL" href="https://www.youtube.com/embed/8kJbXM1rftk">
				//<link itemprop="thumbnailUrl" href="https://i.ytimg.com/vi/8kJbXM1rftk/maxresdefault.jpg">
				//<link itemprop="thumbnailUrl" href="https://i.ytimg.com/vi/8kJbXM1rftk/hqdefault.jpg">

				$videoSrv = 'youtube';
				$videoId  = trim($matches[1]);
				$videoUrl = 'https://www.youtube.com/embed/' . $videoId;
				$imageUrl = 'http://img.youtube.com/vi/' . $videoId . '/0.jpg';

				//Получаем html-страницу ролика и парсим
				$html = Tools::curlExec($url);

				preg_match('/<title>(.*)<\/title>/im', $html, $matches);
				$videoTitle = str_replace(' - YouTube', '', trim($matches[1]));

				preg_match('/<meta.*name="description".*content="(.*)">/im', $html, $matches);
				$videoDesc = trim($matches[1]);

				preg_match('/<link.*itemprop="thumbnailUrl".*href="(.*)">/im', $html, $matches);
				$imageUrl = ($matches[1] ? trim($matches[1]) : $imageUrl);
			}
			//Vimeo
			elseif(
				 preg_match('/[http|https]+:\/\/(?:www\.|)vimeo\.com\/([a-zA-Z0-9_\-]+)(&.+)?/i', $url, $matches) ||
				 preg_match('/[http|https]+:\/\/player\.vimeo\.com\/video\/([a-zA-Z0-9_\-]+)(&.+)?/i', $url, $matches)
			) {
				$videoSrv = 'vimeo';
				$videoId  = trim($matches[1]);
				$videoUrl = 'http://player.vimeo.com/video/' . $videoId;
				$xmlUrl   = 'http://vimeo.com/api/v2/video/' . $videoId . '.xml';

				$imageUrl = '';
				if($xml = simplexml_load_file($xmlUrl)) {
					$videoTitle = (string)$xml->video->title;
					$videoDesc  = (string)$xml->video->description;
					$imageUrl   = $xml->video->thumbnail_large ? (string)$xml->video->thumbnail_large : (string)$xml->video->thumbnail_medium;
				}
			}
			//Rutube
			elseif(
				 preg_match('/[http|https]+:\/\/(?:www\.|)rutube\.ru\/video\/embed\/([a-zA-Z0-9_\-]+)/i', $url, $matches) ||
				 preg_match('/[http|https]+:\/\/(?:www\.|)rutube\.ru\/tracks\/([a-zA-Z0-9_\-]+)(&.+)?/i', $url, $matches) ||
				 preg_match('/[http|https]+:\/\/(?:www\.|)rutube\.ru\/video\/([a-zA-Z0-9_\-]+)\//i', $url, $matches)
			) {
				$videoSrv = 'rutube';
				$videoId  = trim($matches[1]);
				$videoUrl = 'http://rutube.ru/video/embed/' . $videoId;
				$xmlUrl   = 'http://rutube.ru/cgi-bin/xmlapi.cgi?rt_mode=movie&rt_movie_id=' . $videoId . '&utf=1';

				$imageUrl = '';
				if($xml = simplexml_load_file($xmlUrl)) {
					$videoTitle = (string)$xml->title;
					$videoDesc  = (string)$xml->description;
					$imageUrl   = (string)$xml->thumbnail_url;
				}
			}

			if($videoId) {
				if(!array_key_exists($videoId, $sessVideos)) {
					$sessVideos[ $videoId ] = array(
						 'id'          => $videoId,
						 'service'     => $videoSrv,
						 'url'         => $videoUrl,
						 'title'       => $videoTitle,
						 'description' => $videoDesc,
					);

					//Скачиваем картинку и добавляем в таблицу b_file
					if($imageUrl) {
						$fileName    = $videoId . '.' . GetFileExtension($imageUrl);
						$tmpFileName = Tools::getUniqueFileName($fileName, 'VIDEOS', $USER->GetID());
						$tmpFilePath = $arParams['UPLOAD_TMP_DIR'] . '/' . $tmpFileName;

						@chmod($tmpFilePath, BX_FILE_PERMISSIONS);
						if($handle = @fopen($tmpFilePath, "w+")) {
							$image = Tools::curlExec($imageUrl);
							@fwrite($handle, $image);
							@fclose($handle);
							unset($image, $handle);

							$arFile['MODULE_ID'] = 'api.reviews';
							$arFile['name']      = $videoTitle . '.' . GetFileExtension($imageUrl);

							if(file_exists($tmpFilePath)) {
								$arFile['type']     = \CFile::GetContentType($tmpFilePath, true);
								$arFile['tmp_name'] = $tmpFilePath;

								$_SESSION['API_REVIEWS_FORM']['VIDEOS_THUMBS'][ $videoId ] = $arFile;
								unset($fileName, $tmpFileName, $tmpFilePath, $arFile);
							}
						}
					}

					$response = array(
						 'result'  => 'ok',
						 'message' => null,
						 'video'   => array(
								'id'          => $videoId,
								'service'     => $videoSrv,
								'url'         => $videoUrl,
								'title'       => $videoTitle,
								'description' => $videoDesc,
						 ),
						 'image'   => array(
								'url' => $imageUrl,
						 ),
					);
				}
				else {
					$response['alert'] = Loc::getMessage('API_REVIEWS_FORM_ALERT_VIDEO_ISSET');
				}
			}
			else {
				$response['alert'] = Loc::getMessage('API_REVIEWS_FORM_ALERT_WRONG_VIDEO_URL');
			}
		}
	}
	elseif($action == 'VIDEO_DELETE') {
		if($videoId = trim($request->get('VIDEO_ID'))) {

			//Удалит файл c диска и из сессии
			if($arSessThums = &$_SESSION['API_REVIEWS_FORM']['VIDEOS_THUMBS']) {
				if(isset($arSessThums[ $videoId ])) {

					$filePath = $arSessThums[ $videoId ]['tmp_name'];
					if($filePath && file_exists($filePath)) {
						@unlink($filePath);
					}
					unset($arSessThums[ $videoId ]);
				}
			}

			//Удалит видео из сессии
			if($arSessVideo = &$_SESSION['API_REVIEWS_FORM']['VIDEOS']) {
				if(isset($arSessVideo[ $videoId ]))
					unset($arSessVideo[ $videoId ]);
			}
		}

		$response['result'] = 'ok';
	}

	$APPLICATION->RestartBuffer();
	header('Content-Type: application/json');
	echo Json::encode($response);
	die();
}


//==============================================================================
//                      WORK WITH $_REQUEST AJAX
//==============================================================================
if($request->isPost() && $request->get('API_REVIEWS_FORM_AJAX') == 'Y') {

	$sendResult = array(
		 'STATUS'  => 'ERROR',
		 'FIELDS'  => array(),
		 'MESSAGE' => array(),
		 'ID'      => 0,
	);

	if(check_bitrix_sessid()) {

		$fields = $request->getPostList()->toArray();

		foreach($fields as $key => $val) {
			if(in_array($key, $arParams['DISPLAY_FIELDS']))
				$fields[ $key ] = $val;
			else
				unset($fields[ $key ]);
		}

		$fields['SITE_ID']     = SITE_ID;
		$fields['USER_ID']     = intval($USER->GetID());
		$fields['TIMESTAMP_X'] = new \Bitrix\Main\Type\DateTime();
		$fields['ACTIVE_FROM'] = $fields['TIMESTAMP_X'];
		$fields['DATE_CREATE'] = $fields['TIMESTAMP_X'];
		$fields['IP']          = $request->getRemoteAddress();

		if(!Application::isUtfMode())
			$fields = Encoding::convertEncoding($fields, 'UTF-8', $context->getCulture()->getCharset());


		//---------- Премодерация ----------//
		if($arParams['PREMODERATION'] == 'Y') //Все
			$fields['ACTIVE'] = 'N';
		elseif($arParams['PREMODERATION'] == 'A' && !$USER->IsAuthorized()) //Аноним
			$fields['ACTIVE'] = 'N';
		else
			$fields['ACTIVE'] = 'Y';


		if($fields['ACTIVE'] != 'Y')
			$fields['ACTIVE_FROM'] = null;


		$fields['FILES']  = (array)$_SESSION['API_REVIEWS_FORM']['FILES'];
		$fields['VIDEOS'] = (array)$_SESSION['API_REVIEWS_FORM']['VIDEOS'];


		//---------- Проверка обязательных полей ----------//
		if($arParams['REQUIRED_FIELDS']) {
			foreach($arParams['REQUIRED_FIELDS'] as $code) {
				if(!$fields[ $code ]) {
					$arMess = Loc::getMessage('API_REVIEWS_FORM_' . $code);
					if($code == 'RATING') {
						$sendResult['FIELDS'][ $code ] = Loc::getMessage('API_REVIEWS_FORM_ERROR_RATING');
					}
					elseif($arMess['MESSAGE']) {
						$sendResult['FIELDS'][ $code ] = $arMess['MESSAGE'];
					}
					else {
						$arTplFormFieldMess            = Loc::getMessage('API_REVIEWS_FORM_' . $code);
						$sendResult['FIELDS'][ $code ] = Loc::getMessage('API_REVIEWS_FORM_FIELD_ERROR', array('#NAME#' => $arTplFormFieldMess['NAME']));
					}
				}
			}
		}

		//---------- Проверка существования выполненного заказа ----------//
		if($orderId = $fields['ORDER_ID']) {
			if($arResultModules['sale']) {

				$arFilterOrder = array('=ACCOUNT_NUMBER' => $orderId, '=STATUS_ID' => 'F');
				if($USER->IsAuthorized() && $fields['USER_ID'])
					$arFilterOrder['=USER_ID'] = $fields['USER_ID'];

				$rsOrder = \Bitrix\Sale\Order::getList(array(
					 'select' => array('ID', 'ACCOUNT_NUMBER'),
					 'filter' => $arFilterOrder,
				));

				if($arOrder = $rsOrder->fetch()) {
					$arParams['ORDER_ID'] = $arOrder['ACCOUNT_NUMBER'];
				}
				else {
					$sendResult['FIELDS']['ORDER_ID'] = Loc::getMessage('API_REVIEWS_FORM_FIELD_ORDER_ID_ERROR');
				}
			}
			else {
				$sendResult['FIELDS']['ORDER_ID'] = Loc::getMessage('API_REVIEWS_MODULE_SALE_ERROR');
			}
		}
		else {
			unset($fields['ORDER_ID']);
		}


		//---------- Доп. параметры привязки отзыва ----------//
		$fields['IBLOCK_ID']  = $arParams['IBLOCK_ID'];
		$fields['SECTION_ID'] = $arParams['SECTION_ID'];
		$fields['ELEMENT_ID'] = $arParams['ELEMENT_ID'];
		$fields['ORDER_ID']   = $arParams['ORDER_ID'];
		$fields['URL']        = $arParams['URL'];


		//---------- Если ошибок нет, добавляем отзыв ----------//
		if(!$sendResult['FIELDS']) {

			Event::execute('onBeforeReviewAdd', array(&$fields, $arParams));

			//Файлы добавим в таблицу только после успешного добавления отзыва
			$fileList = array();
			if($fields['FILES']) {
				$fileList = $fields['FILES'];
			}
			$fields['FILES'] = '';

			//Видео и превью добавим в таблицу только после успешного добавления отзыва
			$videoList = array();
			if($fields['VIDEOS']) {
				$videoList = $fields['VIDEOS'];
			}
			$fields['VIDEOS'] = '';

			//Добавляем отзыв в базу
			$result = ReviewsTable::add($fields);
			if($result->isSuccess()) {

				$id = $result->getId();

				//Добавим файлы в таблицу b_file
				$arFileId = array();
				if($fileList) {
					foreach($fileList as $arFile) {
						$arFileId[] = \CFile::SaveFile($arFile, 'api_reviews', false, false, $id);
						@unlink($arFile['tmp_name']);
					}
					unset($fileList, $arFile, $_SESSION['API_REVIEWS_FORM']['FILES']);
				}


				//Сначала добавим превью видео в таблицу b_file
				if($thumbList = (array)$_SESSION['API_REVIEWS_FORM']['VIDEOS_THUMBS']) {
					foreach($thumbList as $key => $arFile) {
						if($videoList[ $key ]) {
							$videoList[ $key ]['file_id'] = \CFile::SaveFile($arFile, 'api_reviews', false, false, $id);
						}
						@unlink($arFile['tmp_name']);
					}
					unset($thumbList, $arFile, $_SESSION['API_REVIEWS_FORM']['VIDEOS_THUMBS']);
				}

				//Добавим видео в таблицу api_reviews_video
				$arVideoId = array();
				if($videoList) {
					foreach($videoList as $arVideo) {
						$result = VideoTable::add(array(
							 'CODE'        => $arVideo['id'],
							 'SERVICE'     => $arVideo['service'],
							 'FILE_ID'     => $arVideo['file_id'],
							 'TITLE'       => $arVideo['title'],
							 'DESCRIPTION' => $arVideo['description'],
						));
						if($result->isSuccess()) {
							$arVideoId[] = (int)$result->getId();
						}
					}
					unset($videoList, $arVideo, $result, $_SESSION['API_REVIEWS_FORM']['VIDEOS']);
				}


				//Обновим отзыв, запишем айдишники файлов и видео
				if($arFileId || $arVideoId) {
					ReviewsTable::update($id, array(
						 'FILES'  => implode(',', $arFileId),
						 'VIDEOS' => implode(',', $arVideoId),
					));
				}
				unset($arFileId, $arVideoId);


				//For Event::sendAdd()
				$arParams['ADMIN_URL'] = $httpHost . '/bitrix/admin/api_reviews_edit.php?ID=' . $id . '&lang=' . LANGUAGE_ID;

				if($arParams['PAGE_TITLE'])
					$fields['PAGE_TITLE'] = $arParams['PAGE_TITLE'];

				if($arParams['DETAIL_URL'])
					$fields['PAGE_URL'] = Tools::makeUrl($id, $arParams['DETAIL_URL']);


				if($arParams['USE_SUBSCRIBE'] == 'Y' && $fields['ACTIVE'] == 'Y') {
					Agent::add($id, SITE_ID);
				}

				$sendResult['STATUS'] = 'OK';
				$sendResult['ID']     = $id;

				//PREMODERATION MESS
				if($fields['ACTIVE'] == 'N') {
					$sendResult['MESSAGE'][] = $arParams['MESS_ADD_REVIEW_MODERATION'];
				}
				else {
					$sendResult['MESSAGE'][] = str_replace('#ID#', $id, $arParams['MESS_ADD_REVIEW_VIZIBLE']);
				}

				Event::execute('onAfterReviewAdd', array($id, $fields, $arParams));
				Event::sendAdd($id, $fields, $arParams);


				//Delete components cache
				BXClearCache(true, '/' . SITE_ID . '/api/reviews.list');
				BXClearCache(true, '/' . SITE_ID . '/api/reviews.stat');
				BXClearCache(true, '/' . SITE_ID . '/api/reviews.recent');
			}
			else {
				//$sendResult['MESSAGE'] = $result->getErrorMessages();
				$sendResult['MESSAGE'][] = $arParams['MESS_ADD_REVIEW_ERROR'];
			}
		}
	}
	else {
		$sendResult['MESSAGE'][] = Loc::getMessage('API_REVIEWS_FORM_SESSION_EXPIRED');
	}


	$sendResult['MESSAGE'] = join('<br>', $sendResult['MESSAGE']);

	$APPLICATION->RestartBuffer();
	echo Json::encode($sendResult);
	CMain::FinalActions();
	die();
}



//==============================================================================
//                         WORK WITH $arResult
//==============================================================================

//SHOP DELIVERY
$arDelivery = array();
if($arResultModules['sale']) {
	$arTmpDelivery = array();

	//STATIC DELIVERY
	$dbRes = CSaleDelivery::GetList(
		 array('SORT' => 'ASC', 'NAME' => 'ASC'),
		 array('ACTIVE' => 'Y', "SITE_ID" => SITE_ID),
		 false,
		 false,
		 array('ID', 'NAME', 'DESCRIPTION')
	);
	while($delivery = $dbRes->Fetch()) {
		$arTmpDelivery[] = array(
			 'ID'   => $delivery['ID'],
			 'NAME' => $delivery['NAME'],
		);
	}
	unset($dbRes, $delivery);

	//AUTOMATIC DELIVERY
	$dbRes = CSaleDeliveryHandler::GetList(
		 array('SORT' => 'ASC', 'NAME' => 'ASC'),
		 array('ACTIVE' => 'Y', "SITE_ID" => SITE_ID)
	);
	while($delivery = $dbRes->Fetch()) {
		$arTmpDelivery[] = array(
			 'ID'   => $delivery['ID'],
			 'NAME' => $delivery['NAME'],
		);
	}
	unset($dbRes, $delivery);

	if($arParams['DELIVERY']) {
		foreach($arParams['DELIVERY'] as $deliveryID) {
			if($arTmpDelivery[ $deliveryID ])
				$arDelivery[] = $arTmpDelivery[ $deliveryID ];
		}
	}
	else {
		$arDelivery = $arTmpDelivery;
	}
}

$arResult['MODULES']  = $arResultModules;
$arResult['DELIVERY'] = $arDelivery;


if(Loader::includeModule('api.core')) {
	$extList = array('api_width', 'api_upload', 'api_button', 'api_form', 'api_modal', 'api_alert');

	//if($arParams['INPUTMASK_JS'])
	//		$extList[] = 'api_inputmask';

	\CUtil::InitJSCore($extList);
}


$arResult['FILES'] = array();
if(isset($_SESSION['API_REVIEWS_FORM']['FILES'])) {
	$arResult['FILES'] = $_SESSION['API_REVIEWS_FORM']['FILES'];
}

$arResult['VIDEOS'] = array();
if(isset($_SESSION['API_REVIEWS_FORM']['VIDEOS'])) {
	$arResult['VIDEOS'] = $_SESSION['API_REVIEWS_FORM']['VIDEOS'];
}

$arResult['VIDEOS_THUMBS'] = array();
if(isset($_SESSION['API_REVIEWS_FORM']['VIDEOS_THUMBS'])) {
	$arResult['VIDEOS_THUMBS'] = $_SESSION['API_REVIEWS_FORM']['VIDEOS_THUMBS'];
}


$this->IncludeComponentTemplate();
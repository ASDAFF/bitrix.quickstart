<?

use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Web\Json;

define('STOP_STATISTICS', true);
define('NO_AGENT_CHECK', true);

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
Loc::loadMessages(__FILE__);

global $DB, $USER, $APPLICATION;
CUtil::JSPostUnescape();

$context      = Application::getInstance()->getContext();
$request      = $context->getRequest();
$scheme       = $request->isHttps() ? 'https://' : 'http://';
$httpHost     = $scheme . $request->getHttpHost();
$isUtfMode    = Application::isUtfMode();
$documentRoot = Application::getDocumentRoot();


$response = array(
	 'result'  => 'error',
	 'message' => null,
	 'alert'   => null,
	 'file'    => null,
);

if(!Loader::includeModule('api.reviews')) {
	$response['message'] = Loc::getMessage('API_REVIEWS_MODULE_ERROR');
}

$rights = $APPLICATION->GetGroupRight('api.reviews');
if($rights < 'W') {
	$response['message'] = Loc::getMessage('ARTA_RIGHTS_ERROR');
}

use \Api\Reviews\Tools;
use \Api\Reviews\VideoTable;
use \Api\Reviews\ReviewsTable;


//FILES_SETTINGS
$arParams['UPLOAD_FILE_TYPE']    = '';
$arParams['UPLOAD_FILE_SIZE']    = '';
$arParams['UPLOAD_MAX_FILESIZE'] = 0;
$arParams['UPLOAD_FOLDER']       = '/upload/api_reviews';
$arParams['UPLOAD_TMP_DIR']      = $documentRoot . '/upload/tmp/api_reviews';
$arParams['UPLOAD_FILE_LIMIT']   = 0;
$arParams['UPLOAD_VIDEO_LIMIT']  = 0;


// UPLOAD FILE AND WRITE TO $_SESSION
$isAction = ($request->isPost() && $request->get('API_REVIEWS_EDIT_ACTION') && check_bitrix_sessid());
if($isAction && !$response['message']) {
	$id     = intval($request->get('id'));
	$action = $request->get('API_REVIEWS_EDIT_ACTION');

	$review = array();
	if($id) {
		$review = ReviewsTable::getRow(array(
			 'select' => array('ID', 'REPLY_SEND', 'SITE_ID', 'FILES', 'VIDEOS'),
			 'filter' => array('=ID' => $id),
		));
	}

	if($action == 'FILE_UPLOAD') {

		$arFile    = &$_FILES['FILES'];
		$sessFiles = &$_SESSION['API_REVIEWS_FORM']['FILES'];

		//Проверим лимиты на загружаемые видео
		if($arParams['UPLOAD_FILE_LIMIT']) {
			if($sessFiles && count($sessFiles) == $arParams['UPLOAD_FILE_LIMIT']) {
				$response['alert'] = Loc::getMessage('ARTA_ALERT_UPLOAD_FILE_LIMIT');
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
	elseif($action == 'FILE_UNSET') {
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

			$response['result'] = 'ok';
		}
		unset($fileName, $fileCode, $filePath, $arSessFile);
	}
	elseif($action == 'FILE_DELETE'){
		if($fileId = intval($request->get('fileId'))) {
			if($review['FILES']) {
				\CFile::Delete($fileId);

				$arExpFiles = explode(',', $review['FILES']);
				if($arExpFiles) {
					foreach($arExpFiles as $key => $val) {
						if($val == $fileId)
							unset($arExpFiles[ $key ]);
					}

					$strFiles = implode(',', $arExpFiles);
					ReviewsTable::update($id, array(
						 'FILES' => TrimExAll($strFiles, ','),
					));
				}
			}

			BXClearCache(true, '/' . $review['SITE_ID'] . '/api/reviews.list');

			$response['result'] = 'ok';
		}
	}
	elseif($action == 'VIDEO_UPLOAD') {

		$url = trim($request->get('VIDEO_URL'));

		$sessVideos = &$_SESSION['API_REVIEWS_FORM']['VIDEOS'];

		//Проверим лимиты на загружаемые видео
		if($arParams['UPLOAD_VIDEO_LIMIT']) {
			if($sessVideos && count($sessVideos) == $arParams['UPLOAD_VIDEO_LIMIT']) {
				$response['alert'] = Loc::getMessage('ARTA_ALERT_UPLOAD_VIDEO_LIMIT');
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
					$response['alert'] = Loc::getMessage('ARTA_ALERT_VIDEO_ISSET');
				}
			}
			else {
				$response['alert'] = Loc::getMessage('ARTA_ALERT_WRONG_VIDEO_URL');
			}
		}
	}
	elseif($action == 'VIDEO_UNSET') {
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
	elseif($action == 'VIDEO_DELETE') {
		if($fileId = intval($request->get('fileId'))) {
			if($review['VIDEOS']) {
				if($arExpVideos = explode(',', $review['VIDEOS'])) {
					foreach($arExpVideos as $key => $videoId) {
						if($videoId == $fileId) {
							$video = VideoTable::getRow(array(
								 'select' => array('FILE_ID'),
								 'filter' => array('=ID' => $videoId),
							));
							if($video['FILE_ID']) {
								\CFile::Delete($video['FILE_ID']);
							}
							VideoTable::delete($videoId);
							unset($arExpVideos[ $key ]);
						}
					}

					$strVideos = implode(',', $arExpVideos);
					ReviewsTable::update($id, array(
						 'VIDEOS' => TrimExAll($strVideos, ','),
					));
				}
			}

			BXClearCache(true, '/' . $review['SITE_ID'] . '/api/reviews.list');

			$response['result'] = 'ok';
		}
	}
}

/*
$arPrint = array(
	 '$_REQUEST' => $_REQUEST,
	 '$response' => $response,
	 '$isAction' => $isAction,
);
$tttfile = dirname(__FILE__) . '/1_txt.php';
file_put_contents($tttfile, "<pre>" . print_r($_SESSION['API_REVIEWS_FORM'], 1) . "</pre>\n");
*/


///////////////////////////////////////////////////////////////////////////////
//  echo
///////////////////////////////////////////////////////////////////////////////
$APPLICATION->RestartBuffer();
header('Content-Type: application/json');
echo Json::encode($response);
die();

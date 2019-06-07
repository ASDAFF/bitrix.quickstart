<?
/**
 * Bitrix vars
 *
 * @var CUser $USER
 * @var CMain $APPLICATION
 *
 */

use \Bitrix\Main\Loader,
	 \Bitrix\Main\Application,
	 \Bitrix\Main\Type\DateTime,
	 \Bitrix\Main\Web\Json,
	 \Bitrix\Main\Localization\Loc;

define("ADMIN_MODULE_NAME", "api.reviews");
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
Loc::loadMessages(__FILE__);

global $USER, $APPLICATION, $USER_FIELD_MANAGER;

$AR_RIGHT = $APPLICATION->GetGroupRight(ADMIN_MODULE_NAME);
if($AR_RIGHT < 'W')
	$APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));

if(!Loader::includeModule(ADMIN_MODULE_NAME))
	$APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));


use \Api\Reviews\ReviewsTable;
use \Api\Reviews\VideoTable;
use \Api\Reviews\Event;
use \Api\Reviews\Tools;

CUtil::InitJSCore(array('jquery2'));

$useCore = Loader::includeModule('api.core');
if($useCore) {
	CUtil::InitJSCore(array('api_alert','api_upload'));
}

//Лэнги полей
$arFieldTitle = array();
foreach(ReviewsTable::getMap() as $key => $value) {
	$arFieldTitle[ $key ] = $value['title'];
}

/*
 * @var \Bitrix\Main\Entity\Field $field
foreach(ReviewsTable::getMap() as $key=>$field)
{
	$data = array($field->getName(),$field->getDataType());
}*/


$context      = Application::getInstance()->getContext();
$documentRoot = Application::getDocumentRoot();
$lang         = $context->getLanguage();
$request      = $context->getRequest();
$id           = intval($request->get('ID'));
$ufEntityId   = 'API_REVIEWS';
$bCopy        = ($action == "copy");
$bUpdate      = ($request->get('update') == "Y");
$bSale        = Loader::includeModule('sale');
$bIblock      = Loader::includeModule('iblock');
$arReview     = array();
$errorMessage = '';


if($request->isPost() && $bUpdate && check_bitrix_sessid()) {
	$postFields = $request->getPostList()->toArray();

	//Prepare post fields
	foreach($postFields as $key => $val) {
		if(!array_key_exists($key, $arFieldTitle))
			unset($postFields[ $key ]);
	}

	$postFields['ACTIVE'] = ($postFields['ACTIVE'] == 'Y' ? 'Y' : 'N');

	if($postFields['ACTIVE_FROM'])
		$postFields['ACTIVE_FROM'] = new DateTime($postFields['ACTIVE_FROM']);


	if($postFields['DATE_CREATE'])
		$postFields['DATE_CREATE'] = new DateTime();


	if(!$id) {
		if($postFields['PUBLISH'] == 'U' && !$postFields['USER_ID'])
			$postFields['USER_ID'] = intval($USER->GetID());
	}

	$postFields['TIMESTAMP_X'] = new DateTime();
	//$postFields['MODIFIED_BY'] = $USER->GetID();


	//Удалим из формы данные по файлам, чтобы не затереть уже прикрепленные
	unset($postFields['VIDEOS'], $postFields['FILES']);


	//if(empty($postFields['RATING']))
	//$errorMessage .= Loc::getMessage('ARAE_FIELD_ERROR', array('#FIELD#' => $arFieldTitle['RATING'])) . "\n";

	if(empty($postFields['SITE_ID']))
		$errorMessage .= Loc::getMessage('ARAE_FIELD_ERROR', array('#FIELD#' => $arFieldTitle['SITE_ID'])) . "\n";


	//Write data to db
	if(empty($errorMessage)) {
		$uFields = array();
		$USER_FIELD_MANAGER->EditFormAddFields($ufEntityId, $uFields);

		$fields = array_merge($postFields, $uFields);

		$result = null;
		if($id && !$bCopy) {
			$result = ReviewsTable::update($id, $fields);
		}
		else {
			$result = ReviewsTable::add($fields);
		}

		if($result && $result->isSuccess()) {
			$id = $result->getId();

			$review = ReviewsTable::getRow(array(
				 'select' => array('ID', 'SITE_ID', 'FILES', 'VIDEOS'),
				 'filter' => array('=ID' => $id),
			));

			//Добавим файлы в таблицу b_file
			$arFileId = ($review['FILES'] ? explode(',', $review['FILES']) : array());
			$fileList = (array)$_SESSION['API_REVIEWS_FORM']['FILES'];
			if($fileList) {
				foreach($fileList as $arFile) {
					$arFileId[] = \CFile::SaveFile($arFile, 'api_reviews', false, false, $id);
					@unlink($arFile['tmp_name']);
				}
				unset($fileList, $arFile, $_SESSION['API_REVIEWS_FORM']['FILES']);
			}


			//Сначала добавим превью видео в таблицу b_file
			$videoList = (array)$_SESSION['API_REVIEWS_FORM']['VIDEOS'];
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
			$arVideoId = ($review['VIDEOS'] ? explode(',', $review['VIDEOS']) : array());
			if($videoList) {
				foreach($videoList as $arVideo) {
					$rsVideo = VideoTable::add(array(
						 'CODE'        => $arVideo['id'],
						 'SERVICE'     => $arVideo['service'],
						 'FILE_ID'     => $arVideo['file_id'],
						 'TITLE'       => $arVideo['title'],
						 'DESCRIPTION' => $arVideo['description'],
					));
					if($rsVideo->isSuccess()) {
						$arVideoId[] = (int)$rsVideo->getId();
					}
				}
				unset($videoList, $arVideo, $rsVideo, $_SESSION['API_REVIEWS_FORM']['VIDEOS']);
			}

			//Обновим отзыв, запишем айдишники файлов и видео
			if($arFileId || $arVideoId) {
				ReviewsTable::update($id, array(
					 'FILES'  => implode(',', $arFileId),
					 'VIDEOS' => implode(',', $arVideoId),
				));
			}
			unset($arFileId, $arVideoId);



			//Delete components cache
			BXClearCache(true, '/' . $fields['SITE_ID'] . '/api/reviews.list');
			BXClearCache(true, '/' . $fields['SITE_ID'] . '/api/reviews.stat');
			BXClearCache(true, '/' . $fields['SITE_ID'] . '/api/reviews.recent');


			//Отправим ответ клиенту
			if($request->get('SEND_EVENT') == 'Y') {
				Event::sendReply($id, $fields);
			}

			if(strlen($request->getPost("apply")) == 0)
				LocalRedirect("/bitrix/admin/api_reviews_list.php?lang=" . $lang . "&" . GetFilterParams("filter_", false));
			else
				LocalRedirect("/bitrix/admin/api_reviews_edit.php?lang=" . $lang . "&ID=" . $id . "&" . GetFilterParams("filter_", false));
		}
		else {
			$errorMessage .= join("\n", $result->getErrorMessages());
		}
	}

	unset($fields);
}

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');
//START VIEW

if($errorMessage)
	CAdminMessage::ShowMessage($errorMessage);


if($id > 0) {
	$select     = array('*');
	$userFields = $USER_FIELD_MANAGER->GetUserFields($ufEntityId);
	foreach($userFields as $field)
		$select[] = $field['FIELD_NAME'];

	$params   = array(
		 'select' => $select,
		 'filter' => array('=ID' => $id),
	);
	$arReview = ReviewsTable::getList($params)->fetch();

	$APPLICATION->SetTitle($arReview['TITLE'] ? $arReview['TITLE'] : 'ID(' . $id . ')');
}
else {
	$APPLICATION->SetTitle(Loc::getMessage("ARAE_TITLE"));
}


//SHOP DELIVERY
$arDelivery = array('' => Loc::getMessage('ARAE_OPTION_EMPTY'));
if($bSale) {
	$dFilter = array('ACTIVE' => 'Y');
	if($arReview['SITE_ID'])
		$dFilter['SITE_ID'] = $arReview['SITE_ID'];

	//STATIC DELIVERY
	$dbRes = CSaleDelivery::GetList(
		 array('SORT' => 'ASC', 'ID' => 'ASC', 'NAME' => 'ASC'),
		 $dFilter,
		 false,
		 false,
		 array('ID', 'NAME', 'DESCRIPTION')
	);
	while($delivery = $dbRes->Fetch()) {
		$arDelivery[ $delivery['ID'] ] = '[' . $delivery['ID'] . '] ' . $delivery['NAME'];
	}
	unset($dbRes, $delivery);


	//AUTOMATIC DELIVERY
	$dbRes = CSaleDeliveryHandler::GetList(
		 array('SORT' => 'ASC', 'ID' => 'ASC', 'NAME' => 'ASC'),
		 $dFilter
	);
	while($delivery = $dbRes->Fetch()) {
		$arDelivery[ $delivery['ID'] ] = '[' . $delivery['ID'] . '] ' . $delivery['NAME'];
	}
	unset($dbRes, $delivery);
}



//Кнопки = Добавить/Копировать/Удалить
$aContext = array(
	 array(
			"TEXT" => Loc::getMessage('MAIN_ADMIN_MENU_LIST'),
			"LINK" => "api_reviews_list.php?lang=" . $lang,
			"ICON" => "btn_list",
	 ),
);
if($id && !$bCopy && $AR_RIGHT == 'W') {
	$aContext[] = array("SEPARATOR" => "Y");
	$aContext[] = array(
		 "TEXT" => Loc::getMessage('MAIN_ADMIN_MENU_ADD'),
		 "LINK" => "api_reviews_edit.php?lang=" . $lang,
		 "ICON" => "btn_new",
	);
	$aContext[] = array(
		 "TEXT" => Loc::getMessage('MAIN_ADMIN_MENU_COPY'),
		 "LINK" => "api_reviews_edit.php?ID=" . $id . "&amp;action=copy&amp;lang=" . $lang,
		 "ICON" => "btn_copy",
	);
	$aContext[] = array(
		 "TEXT" => Loc::getMessage('MAIN_ADMIN_MENU_DELETE'),
		 "LINK" => "javascript:if(confirm('" . Loc::getMessage('ARAE_DELETE_CONFIRM') . "'))window.location='api_reviews_list.php?ID=" . $id . "&action=delete&lang=" . $lang . "&" . bitrix_sessid_get() . "';",
		 "ICON" => "btn_delete",
	);
}
$context = new CAdminContextMenu($aContext);
$context->Show();


$aTabs      = array(
	 array(
			"DIV"   => "review",
			"TAB"   => Loc::getMessage('ARAE_TAB_NAME'),
			"ICON"  => "",
			"TITLE" => Loc::getMessage('ARAE_TAB_TITLE'),
	 ),
);
$tabControl = new CAdminForm("review_edit", $aTabs);


//---------- Все данные по отзыву ----------//
$fields = ($request->isPost()) ? $request->getPostList()->toArray() : $arReview;


//---------- Все данные по инфоблоку/разделу/элементу ----------//
$arIblock  = array();
$arElement = array();
if($bIblock) {
	if($elId = $fields['ELEMENT_ID']) {
		$arElement = CIBlockElement::GetList(false, array('=ID' => $elId), false, false, array('ID', 'IBLOCK_ID', 'NAME', 'IBLOCK_SECTION_ID', 'DETAIL_PAGE_URL'))->GetNext(false, false);
	}

	$rsIblock = CIBlock::GetList(array('ID' => 'ASC'));
	while($iblock = $rsIblock->Fetch())
		$arIblock[ $iblock['ID'] ] = $iblock['NAME'];
}



//---------- Файлы и видео из БД ----------//
if($fields['FILES']) {
	$arFiles = array();
	if($arFileId = explode(',', $fields['FILES'])) {
		foreach($arFileId as $fileId) {
			$arFile = \CFile::GetFileArray($fileId);

			if($arFile['SRC'])
				$arFile['SRC'] = CUtil::GetAdditionalFileURL($arFile['SRC'], true);

			$arFile['FORMAT_SIZE'] = \CFile::FormatSize($arFile['FILE_SIZE'], 0);
			$arFile['FORMAT_NAME'] = htmlspecialcharsbx($arFile['ORIGINAL_NAME'] . ' (' . $arFile['FORMAT_SIZE'] . ')');
			$arFile['EXTENSION']   = GetFileExtension($arFile['ORIGINAL_NAME']);

			$arFile['THUMBNAIL'] = array();
			if(preg_match('/image*/', $arFile['CONTENT_TYPE'])) {
				$arFileTmp = CFile::ResizeImageGet(
					 $arFile,
					 array("width" => 114, "height" => 72)
				);

				if($arFileTmp['src'])
					$arFileTmp['src'] = CUtil::GetAdditionalFileURL($arFileTmp['src'], true);

				$arFile['THUMBNAIL'] = array_change_key_case($arFileTmp, CASE_UPPER);
			}

			$arFiles[] = $arFile;
		}
	}

	$fields['FILES'] = $arFiles;
}

if($fields['VIDEOS']) {
	$arVideos = VideoTable::getList(array(
		 'filter' => array('=ID' => explode(',', $fields['VIDEOS'])),
	))->fetchAll();

	if($arVideos) {
		foreach($arVideos as &$video) {

			$video['SRC']       = Tools::getVideoUrl($video);
			$video['TITLE']     = CUtil::JSEscape($video['TITLE']);
			$video['THUMBNAIL'] = array();
			if($video['FILE_ID']) {
				$arFileTmp = CFile::ResizeImageGet(
					 $video['FILE_ID'],
					 array("width" => 114, "height" => 72)
				);

				if($arFileTmp['src'])
					$arFileTmp['src'] = CUtil::GetAdditionalFileURL($arFileTmp['src'], true);

				$video['THUMBNAIL'] = array_change_key_case($arFileTmp, CASE_UPPER);
			}
		}
	}

	$fields['VIDEOS'] = $arVideos;

	unset($video);
}


//---------- Файлы и видео из сессии ----------//
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


$tabControl->BeginPrologContent();
?>
	<style type="text/css">
		#review textarea{ width: 100%; min-height: 80px }
		#review input[size*=]{ width: 100%; }

		/* .api-field-files */
		#review_edit_form .api-field-files a{
			position: relative;
			text-decoration: none;
			display: inline-block;
			vertical-align: top;
			margin: 0 6px 6px 0;
			border: 1px solid rgba(0, 0, 0, .1);
			-webkit-transition: all 0.2s ease-in-out;
			-o-transition: all 0.2s ease-in-out;
			transition: all 0.2s ease-in-out;
		}
		#review_edit_form .api-field-files a:hover{
			border: 1px solid rgba(0, 0, 0, .2);
			-webkit-box-shadow: 0 0 2px rgba(0, 0, 0, .2);
			-moz-box-shadow: 0 0 2px rgba(0, 0, 0, .2);
			box-shadow: 0 0 2px rgba(0, 0, 0, .2);
		}
		#review_edit_form .api-field-files .api-file-outer{
			border: 1px solid #fff;
		}
		#review_edit_form .api-file-content{
			font-size: 13px;
			line-height: 15px;
			max-height: 30px;
			overflow: hidden;
			display: block;
		}
		#review_edit_form .api-file-thumbnail{
			display: block;
			width: 114px;
			height: 72px;
			background-size: contain;
			background-position: center;
			background-color: rgba(0, 0, 0, .05);
			background-repeat: no-repeat;
			overflow: hidden;
			position: relative;
		}
		#review_edit_form .api-file-attachment{
			display: block;
			width: 114px;
			height: 72px;
			background: #eeece9;
			color: #000;
			padding: 5px 7px;
			overflow: hidden;
			position: relative;
			-webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px;
		}
		#review_edit_form .api-file-extension{
			display: block;
			padding: 0 4px;
			text-transform: uppercase;
			color: #fff;
			position: absolute;
			bottom: 5px;
			left: 5px;
			background: #60605a;
			text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.2);
			-webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px;
		}
		#review_edit_form .api-file-delete{
			display: block;
			position: absolute;
			z-index: 1;
			right: -10px;
			top: -10px;
			width: 16px;
			height: 16px;
			background: #FFF;
			color: #000;
			opacity: .75;
			filter: alpha(opacity=75);
			line-height: 16px;
			font-size: 20px;
			font-family: sans-serif;
			text-align: center;
			cursor: pointer;
			-webkit-border-radius: 50%; -moz-border-radius: 50%; border-radius: 50%;
			-webkit-transition: all 50ms linear 0s; -moz-transition: all 50ms linear 0s; -ms-transition: all 50ms linear 0s; -o-transition: all 50ms linear 0s; transition: all 50ms linear 0s;
		}
		#review_edit_form .api-file-delete:hover{
			background: #F00;
			color: #FFF;
			text-shadow: 0 -1px 0 rgba(0, 0, 0, .5);
		}
		#review_edit_form .api-file-delete:active{
			opacity: .3;
			filter: alpha(opacity=30);
		}
		#review_edit_form .api-file-ext-mp3,
		#review_edit_form .api-file-ext-wav,
		#review_edit_form .api-file-ext-midi,
		#review_edit_form .api-file-ext-snd,
		#review_edit_form .api-file-ext-au,
		#review_edit_form .api-file-ext-wma,
		#review_edit_form .api-file-ext-ogg,
		#review_edit_form .api-file-ext-aac,
		#review_edit_form .api-file-ext-flac,
		#review_edit_form .api-file-ext-cda{ background: #23a9db; }
		#review_edit_form .api-file-ext-mpg,
		#review_edit_form .api-file-ext-avi,
		#review_edit_form .api-file-ext-wmv,
		#review_edit_form .api-file-ext-mpeg,
		#review_edit_form .api-file-ext-mpe,
		#review_edit_form .api-file-ext-flv,
		#review_edit_form .api-file-ext-mkv,
		#review_edit_form .api-file-ext-mov,
		#review_edit_form .api-file-ext-wma,
		#review_edit_form .api-file-ext-mp4,
		#review_edit_form .api-file-ext-xvid,
		#review_edit_form .api-file-ext-asf,
		#review_edit_form .api-file-ext-divx,
		#review_edit_form .api-file-ext-vob{ background: #7e70ee; }
		#review_edit_form .api-file-ext-swf{ background: #A42222; }
		#review_edit_form .api-file-ext-odt,
		#review_edit_form .api-file-ext-doc,
		#review_edit_form .api-file-ext-docx{ background: #03689b; }
		#review_edit_form .api-file-ext-csv,
		#review_edit_form .api-file-ext-ods,
		#review_edit_form .api-file-ext-xls,
		#review_edit_form .api-file-ext-xlsx{ background: #5bab6e; }
		#review_edit_form .api-file-ext-odp,
		#review_edit_form .api-file-ext-ppt,
		#review_edit_form .api-file-ext-pptx{ background: #f1592a; }
		#review_edit_form .api-file-ext-rar,
		#review_edit_form .api-file-ext-tar,
		#review_edit_form .api-file-ext-7zip,
		#review_edit_form .api-file-ext-zip{ background: #867c75; }
		#review_edit_form .api-file-ext-djvu,
		#review_edit_form .api-file-ext-epub,
		#review_edit_form .api-file-ext-tiff,
		#review_edit_form .api-file-ext-xps{ background: #3468b0; }
		#review_edit_form .api-file-ext-pdf{ background: #d00; }
		#review_edit_form .api-file-ext-txt{ background: #a4a7ac; }
		#review_edit_form .api-file-ext-rtf{ background: #a94bb7; }
		#review_edit_form .api-file-ext-app{ background: #ed558f; }
		#review_edit_form .api-file-ext-php{ background: #8993BE; }
		#review_edit_form .api-file-ext-js{ background: #d0c54d; }
		#review_edit_form .api-file-ext-css{ background: #44afa6; }
		#review_edit_form .api-file-ext-jpeg,
		#review_edit_form .api-file-ext-jpg{ background: #e15955; }
		/* Adobe */
		#review_edit_form .api-file-ext-psd{ background: #26cdf7; }
		#review_edit_form .api-file-ext-ae{ background: #d4a6ff; }
		#review_edit_form .api-file-ext-au{ background: #00dfb9; }
		#review_edit_form .api-file-ext-an{ background: #ff4926; }
		#review_edit_form .api-file-ext-ai{ background: #ff7e19; }
		#review_edit_form .api-file-ext-ic{ background: #fc64f6; }
		#review_edit_form .api-file-ext-id{ background: #ff3f8d; }
		#review_edit_form .api-file-ext-mu{ background: #d0e73e; }
		#review_edit_form .api-file-ext-pr{ background: #e383ff; }
		/* .api_video_upload */
		#review_edit_form .api_video_upload{ position: relative }
		#review_edit_form .api_video_list{ position: relative; overflow: hidden; }
		#review_edit_form .api_video_item{ margin-bottom: 3px; white-space: nowrap; position: relative }
		#review_edit_form .api_video_remove{
			position: absolute;
			right: 0;
			top: 0;
			bottom: 0;
			background: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAICAYAAAArzdW1AAAAVklEQVR42nWOgQnAMAgE3aCjOJEDuY3DZJ8rqZYipg8KPgen7GACvpAKLFCBgCxcYfeSYAHVFUhYHqdRR96w/A/40hRNzXUAJpiQdcVQB+QuYPxo8XQ33NCTVnhoHP8AAAAASUVORK5CYII=") no-repeat 50% 50%;
			width: 20px;
			height: 20px;
			cursor: pointer;
			opacity: .6;
		}
		#review_edit_form .api_video_remove:hover{ opacity: 1 }
		#review_edit_form .api_video_info{ font-size: 13px; color: #7a7a7a; font-family: Tahoma, Helvetica, Arial, sans-serif; }
	</style>
	<script>
		$(function () {

			var review_form = $('#review_edit_form');

			///////////////////////////////////////////////////////////////////////
			//  Videos
			///////////////////////////////////////////////////////////////////////
			var videoDeleteLang = <?=Json::encode(Loc::getMessage('ARAE_VIDEO_DELETE_LANG'))?>;
			var fileDeleteLang = <?=Json::encode(Loc::getMessage('ARAE_FILE_DELETE_LANG'))?>;

			review_form.on('change paste', '.api_video_upload input', function (e) {
				var self = this;
				var url = '';

				var clipboardData = e.originalEvent.clipboardData || e.clipboardData || w.clipboardData || null;
				if (clipboardData) {
					url = clipboardData.getData("text");
				}
				else {
					url = $(this).val();
				}
				if (url.length) {
					$(self).parent().addClass('api_button_busy');
					$.ajax({
						type: 'POST',
						cache: false,
						url: '/bitrix/admin/api_reviews_action.php',
						data: {
							'sessid': BX.bitrix_sessid(),
							'API_REVIEWS_EDIT_ACTION': 'VIDEO_UPLOAD',
							'VIDEO_URL': url
						},
						success: function (response) {
							$(self).val('');

							if (response.result === 'ok') {
								var video = response.video || {};
								var image = response.image || {};

								var html = '';
								html += '<div class="api_video_item">';
								html += '<div class="api_video_remove" data-id="' + video.id + '"></div>';
								html += '<a href="' + video.url + '" target="_blank">' + video.title + '</a>';
								html += '</div>';
								review_form.find('.api_video_list').append(html);
							}
							else {
								$.fn.apiAlert(response.alert);
							}

							$(self).parent().removeClass('api_button_busy');
						}
					});
				}
			});
			//Удалит видео из сессии
			review_form.on('click', '.api_video_remove', function () {
				var videBtn = $(this);
				var videId = $(this).data('id') || '';

				if (videId) {
					$.ajax({
						type: 'POST',
						cache: false,
						url: '/bitrix/admin/api_reviews_action.php',
						data: {
							'sessid': BX.bitrix_sessid(),
							'API_REVIEWS_EDIT_ACTION': 'VIDEO_UNSET',
							'VIDEO_ID': videId
						},
						success: function () {
							$(videBtn).closest('.api_video_item').remove();
						}
					});
				}
				else {
					$(videBtn).closest('.api_video_item').remove();
				}
			});
			//Удалит видео из базы
			review_form.on('click', '.js-getVideoDelete', function (e) {
				e.preventDefault();
				API_fileDelete(this, videoDeleteLang, 'VIDEO_DELETE');
			});
			//Удалит файл из базы
			review_form.on('click','.js-getFileDelete',function(e){
				e.preventDefault();
				API_fileDelete(this,fileDeleteLang,'FILE_DELETE');
			});
		});

		function API_fileDelete(node, lang, action) {
			var id = $(node).data('id');
			var fileId = $(node).data('file');
			var link = $(node).closest('a');

			if (id && fileId && link) {
				$.fn.apiAlert({
					type: 'confirm',
					//class: 'warning',
					theme: 'jbox',
					title: lang.confirmTitle,
					content: lang.confirmContent,
					labels: {
						ok: lang.labelOk,
						cancel: lang.labelCancel,
					},
					callback: {
						onConfirm: function (isConfirm) {
							if (isConfirm) {
								$(link).addClass('api_button_busy');
								$.ajax({
									type: 'POST',
									url: '/bitrix/admin/api_reviews_action.php',
									data: {
										sessid: BX.bitrix_sessid(),
										API_REVIEWS_EDIT_ACTION: action,
										id: id,
										fileId: fileId,
									},
									error: function (jqXHR, textStatus, errorThrown) {
										console.error('textStatus: ' + textStatus);
										console.error('errorThrown: ' + errorThrown);
									},
									success: function () {
										$(link).remove();
									}
								});
							}
						},
					}
				});
			}
		}

	</script>
<?
//echo BeginNote();
//echo Loc::getMessage('ARAE_NOTE_1');
//echo EndNote();

echo $USER_FIELD_MANAGER->ShowScript();

$tabControl->EndPrologContent();


$tabControl->BeginEpilogContent();
?>
<?=bitrix_sessid_post()?>
	<input type="hidden" name="update" value="Y">
	<input type="hidden" name="lang" value="<?=$lang;?>">

<? if(!$fields['DATE_CREATE'] || $bCopy): ?>
	<input type="hidden" name="DATE_CREATE" value="Y">
<? endif ?>

<? if(!$bCopy): ?>
	<input type="hidden" name="ID" value="<?=$id;?>">
<? endif ?>
<?
$tabControl->EndEpilogContent();

//заголовки закладок
$tabControl->Begin(array('FORM_ACTION' => $APPLICATION->GetCurPage() . "?lang=" . $lang));


//*********************************************************
//                   первая закладка
//*********************************************************
$tabControl->BeginNextFormTab();

$tabControl->AddViewField('ID', $arFieldTitle['ID'] . ':', $fields['ID']);
$tabControl->AddCheckBoxField('ACTIVE', $arFieldTitle['ACTIVE'], false, 'Y', $fields['ACTIVE'] != 'N');

if($fields['DATE_CREATE'])
	$tabControl->AddViewField('DATE_CREATE', $arFieldTitle['DATE_CREATE'] . ':', $fields['DATE_CREATE']);

if($fields['TIMESTAMP_X'])
	$tabControl->AddViewField('TIMESTAMP_X', $arFieldTitle['TIMESTAMP_X'] . ':', $fields['TIMESTAMP_X']);

//if($fields['MODIFIED_BY'])
//	$tabControl->AddViewField('FIELDS[MODIFIED_BY]', $arFieldTitle['MODIFIED_BY'] . ':', CApiSystemMessage::getFormatedUserName($fields['MODIFIED_BY'], false, true));

//$tabControl->AddEditField('FIELDS[SORT]', $arFieldTitle['SORT'], false, array('size' => 5), (isset($fields['SORT']) ? $fields['SORT'] : 500));


$tabControl->BeginCustomField('ACTIVE_FROM', $arFieldTitle['ACTIVE_FROM']);
?>
	<tr id="tr_ACTIVE_FROM">
		<td><?=$tabControl->GetCustomLabelHTML()?>:</td>
		<td><?=CAdminCalendar::CalendarDate('ACTIVE_FROM', $fields['ACTIVE_FROM'], 19, true)?></td>
	</tr>
<?
$tabControl->EndCustomField("ACTIVE_FROM", '<input type="hidden" id="ACTIVE_FROM" name="ACTIVE_FROM" value="' . $fields['ACTIVE_FROM'] . '">');


//---------- HEADING_REVIEW ----------//
$tabControl->AddSection('HEADING_REVIEW', Loc::getMessage('ARAE_HEADING_REVIEW'));

$tabControl->BeginCustomField('RATING', $arFieldTitle['RATING']);
$arRating = Loc::getMessage('ARAE_RATING_VALUES');
?>
	<tr>
		<td><?=$tabControl->GetCustomLabelHTML()?></td>
		<td>
			<select name="RATING">
				<? foreach($arRating as $key => $val): ?>
					<option value="<?=$key?>"<?=(($fields['RATING'] == $key) || (!$fields['RATING'] && $key == 5) ? ' selected' : '')?>><?=$val?></option>
				<? endforeach; ?>
			</select>
		</td>
	</tr>
<?
$tabControl->EndCustomField('RATING');

$tabControl->AddEditField('ORDER_ID', $arFieldTitle['ORDER_ID'], false, array('size' => 100), $fields['ORDER_ID']);
$tabControl->AddEditField('TITLE', $arFieldTitle['TITLE'], false, array('size' => 100), $fields['TITLE']);
$tabControl->AddEditField('COMPANY', $arFieldTitle['COMPANY'], false, array('size' => 100), $fields['COMPANY']);
$tabControl->AddEditField('WEBSITE', $arFieldTitle['WEBSITE'], false, array('size' => 100), $fields['WEBSITE']);

$tabControl->AddTextField('ADVANTAGE', $arFieldTitle['ADVANTAGE'], $fields['ADVANTAGE']);
$tabControl->AddTextField('DISADVANTAGE', $arFieldTitle['DISADVANTAGE'], $fields['DISADVANTAGE']);
$tabControl->AddTextField('ANNOTATION', $arFieldTitle['ANNOTATION'], $fields['ANNOTATION']);

$tabControl->BeginCustomField('FILES', $arFieldTitle['FILES']);
?>
	<tr>
		<td><?=$tabControl->GetCustomLabelHTML()?></td>
		<td>

			<? if($fields['FILES']): ?>
				<div class="api-field api-field-files">
					<div class="api-field-value">
						<?foreach($fields['FILES'] as $arFile):?>
							<a href="<?=$arFile['SRC']?>"
								 <?=($arFile['THUMBNAIL'] ? 'rel="apiReviewsPhoto"' : '')?>
								 data-group="review<?=$fields['ID']?>" target="_blank">
									<div class="api-file-delete js-getFileDelete" data-id="<?=$fields['ID']?>" data-file="<?=$arFile['ID']?>">&times;</div>
								<?if($arFile['THUMBNAIL']):?>
									<div class="api-file-outer api-file-thumbnail api-file-ext-<?=$arFile['EXTENSION']?>"
									     style="background-image: url(<?=$arFile['THUMBNAIL']['SRC']?>)"></div>
								<?else:?>
									<div class="api-file-outer api-file-attachment js-getDownload" title="<?=$arFile['FORMAT_NAME']?>">
										<span class="api-file-content"><?=$arFile['ORIGINAL_NAME']?></span>
										<span class="api-file-extension api-file-ext-<?=$arFile['EXTENSION']?>"><?=$arFile['EXTENSION']?></span>
									</div>
								<?endif?>
							</a>
						<?endforeach;?>
					</div>
				</div>
			<? endif ?>

			<div class="api_upload" id="api_upload">
				<ul class="api_file_list">
					<? if($arResult['FILES']): ?>
						<? foreach($arResult['FILES'] as $file): ?>
							<li>
								<div class="api_progress_bar">
									<div class="api_progress" rel="100" style="width: 100%;"></div>
									<div class="api_file_remove" data-code="<?=$file['code']?>" data-type="<?=$file['type']?>"></div>
								</div>
								<div class="api_file_label">
									<span class="api_file_ext_<?=GetFileExtension($file['name'])?>"></span>
									<span class="api_file_name"><?=$file['name']?></span>
									<span class="api_file_size"><?=$file['size_round']?></span>
								</div>
							</li>
						<? endforeach; ?>
					<? endif; ?>
				</ul>
				<div class="api_upload_drop">
					<span class="api_upload_drop_icon"></span>
					<span class="api_upload_drop_text"><?=Loc::getMessage('ARAE_UPLOAD_DROP')?></span>
					<input id="api_upload_file"
					       class="api_upload_file api-field"
					       type="file"
					       name="FILES[]"
					       multiple="">
				</div>
			</div>
			<script type="text/javascript">
				(function ($) {
					$('#api_upload').apiUpload({
						fileName: 'FILES',
						url: '/bitrix/admin/api_reviews_action.php',
						extraData: {
							'sessid': BX.bitrix_sessid(),
							'API_REVIEWS_EDIT_ACTION': 'FILE_UPLOAD',
						},
						errors: <?=Json::encode(Loc::getMessage('ARAE_UPLOAD_ERRORS'))?>,
						callback: {
							onComplete: function(node, response, xhr){
								if(response){
									if(response.result === 'error'){
										if(response.alert)
											$.fn.apiAlert(response.alert);
										else if(response.message.length)
											$.fn.apiAlert({content: response.message});
									}
								}
							},
							onError: function (node, errors) {
								var mess = '';
								for (var i in errors) {
									mess += errors[i] + "<br>";
								}
								$.fn.apiAlert({
									type: 'info',
									theme: 'jbox',
									content: mess,
								});
							},
							onFallbackMode: function(message) {
								$('#api_upload .api_upload_drop').html(message);
								console.error(message);
							},
						}
					});

					$('#api_upload').on('click','.api_file_remove',function(){
						var fileButton = $(this);
						var fileCode = $(this).data('code') || '';
						if(fileCode.length){
							$.ajax({
								type: 'POST',

								cache: false,
								data: {
									'sessid': BX.bitrix_sessid(),
									'API_REVIEWS_EDIT_ACTION': 'FILE_UNSET',
									'FILE_CODE': fileCode,
								},
								success: function () {
									$(fileButton).closest('li').remove();
								}
							});
						}
						else{
							$(fileButton).closest('li').remove();
						}
					});

					$(window).on('load',function(){
						$('#api_upload_file').closest('.adm-input-file').removeClass('adm-input-file');
					});

				})(jQuery)
			</script>
		</td>
	</tr>
<?
$tabControl->EndCustomField('FILES');

$tabControl->BeginCustomField('VIDEOS', $arFieldTitle['VIDEOS']);
?>
	<tr>
		<td><?=$tabControl->GetCustomLabelHTML()?></td>
		<td>
			<? if($fields['VIDEOS']): ?>
				<div class="api-field api-field-videos api-field-files">
					<div class="api-field-value">
						<? foreach($fields['VIDEOS'] as $video): ?>
							<a href="<?=$video['SRC']?>"
							   rel="apiReviewsVideo"
							   title="<?=$video['TITLE']?>"
							   data-group="review<?=$fields['ID']?>"
							   data-id="<?=$video['CODE']?>"
							   data-service="<?=$video['SERVICE']?>"
							   data-title="<?=$video['TITLE']?>"
							   target="_blank">
								<? if($AR_RIGHT == 'W'): ?>
									<div class="api-file-delete js-getVideoDelete" data-id="<?=$fields['ID']?>" data-file="<?=$video['ID']?>">&times;</div>
								<? endif ?>
								<div class="api-file-outer api-file-thumbnail"
								     style="background-image: url(<?=$video['THUMBNAIL']['SRC']?>)"></div>
							</a>
						<? endforeach; ?>
					</div>
				</div>
				<? unset($video) ?>
			<? endif ?>
			<div id="API_FIELD_VIDEOS">
				<div class="api_video_list">
					<? if($arResult['VIDEOS']): ?>
						<? foreach($arResult['VIDEOS'] as $video): ?>
							<div class="api_video_item">
								<div class="api_video_remove" data-id="<?=$video['id']?>"></div>
								<a href="<?=$video['url']?>" target="_blank"><?=$video['title']?></a>
							</div>
						<? endforeach; ?>
						<? unset($video) ?>
					<? endif; ?>
				</div>
				<div class="api_video_upload">
					<input type="text" size="100" class="api-field" placeholder="<?=Loc::getMessage('ARAE_VIDEOS_PLACEHOLDER')?>">
				</div>
			</div>
		</td>
	</tr>
<?
$tabControl->EndCustomField('VIDEOS');


$tabControl->AddEditField('THUMBS_UP', $arFieldTitle['THUMBS_UP'], false, array(), $fields['THUMBS_UP']);
$tabControl->AddEditField('THUMBS_DOWN', $arFieldTitle['THUMBS_DOWN'], false, array(), $fields['THUMBS_DOWN']);



//---------- HEADING_REPLY ----------//
$tabControl->AddSection('HEADING_REPLY', Loc::getMessage('ARAE_HEADING_REPLY'));
$tabControl->AddViewField('REPLY_SEND', $arFieldTitle['REPLY_SEND'] . ':', Loc::getMessage('ARAE_LOGIC_' . $fields['REPLY_SEND']));
$tabControl->AddTextField('REPLY', $arFieldTitle['REPLY'], $fields['REPLY']);
$tabControl->AddCheckBoxField('SEND_EVENT', Loc::getMessage('ARAE_SEND_EVENT'), false, 'Y', '');



//---------- HEADING_REFERENCE ----------//
$tabControl->AddSection('HEADING_REFERENCE', Loc::getMessage('ARAE_HEADING_REFERENCE'));

$tabControl->BeginCustomField('SITE_ID', $arFieldTitle['SITE_ID'], true);
?>
	<tr>
		<td><?=$tabControl->GetCustomLabelHTML()?></td>
		<td>
			<?=CSite::SelectBox('SITE_ID', $fields['SITE_ID']);?>
		</td>
	</tr>
<?
$tabControl->EndCustomField('SITE_ID');


$tabControl->BeginCustomField('IBLOCK_ID', $arFieldTitle['IBLOCK_ID']);
//$tabControl->AddEditField('IBLOCK_ID', $arFieldTitle['IBLOCK_ID'], false, array(), $fields['IBLOCK_ID']);
$arRating = Loc::getMessage('ARAE_RATING_VALUES');
?>
	<tr>
		<td><?=$tabControl->GetCustomLabelHTML()?></td>
		<td>
			<select name="IBLOCK_ID">
				<option value="0"><?=Loc::getMessage('ARAE_OPTION_EMPTY')?></option>
				<? foreach($arIblock as $key => $val): ?>
					<option value="<?=$key?>"<?=($fields['IBLOCK_ID'] == $key ? ' selected' : '')?>>[<?=$key?>] <?=$val?></option>
				<? endforeach; ?>
			</select>
		</td>
	</tr>
<?
$tabControl->EndCustomField('IBLOCK_ID');



$tabControl->BeginCustomField('SECTION_ID', $arFieldTitle['SECTION_ID']);
//$tabControl->AddEditField('SECTION_ID', $arFieldTitle['SECTION_ID'], false, array(), $fields['SECTION_ID']);
?>
	<tr>
		<td><?=$tabControl->GetCustomLabelHTML()?></td>
		<td>
			<input type="text"
			       name="SECTION_ID"
			       id="SECTION_ID_VALUE"
			       value="<?=$fields['SECTION_ID']?>">
			<input type="button"
			       value="..."
			       class="OpenWindow"
			       onclick="jsUtils.OpenWindow('/bitrix/admin/iblock_section_search.php?lang=<?=$lang;?>&IBLOCK_ID=<?=$fields['IBLOCK_ID']?>&n=SECTION_ID_VALUE&k=0', 900, 700);">
		</td>
	</tr>
<?
$tabControl->EndCustomField('SECTION_ID');



$tabControl->BeginCustomField('ELEMENT_ID', $arFieldTitle['ELEMENT_ID']);
//$tabControl->AddEditField('ELEMENT_ID', $arFieldTitle['ELEMENT_ID'], false, array(), $fields['ELEMENT_ID']);
?>
	<tr>
		<td><?=$tabControl->GetCustomLabelHTML()?></td>
		<td>
			<input type="text"
			       name="ELEMENT_ID"
			       id="ELEMENT_ID_VALUE"
			       value="<?=$fields['ELEMENT_ID']?>">
			<input type="button"
			       value="..."
			       class="OpenWindow"
			       onclick="jsUtils.OpenWindow('/bitrix/admin/iblock_element_search.php?lang=<?=$lang;?>&IBLOCK_ID=<?=$fields['IBLOCK_ID']?>&n=ELEMENT_ID_VALUE&k=0', 900, 700);">
			<? if($arElement['ID']): ?>
				<?
				$publicLink = $arElement['DETAIL_PAGE_URL'];
				$adminLink  = "/bitrix/admin/iblock_element_edit.php?IBLOCK_ID={$arElement['IBLOCK_ID']}&type={$arElement['IBLOCK_TYPE_ID']}&ID={$arElement['ID']}&lang={$lang}&find_section_section={$arElement['IBLOCK_SECTION_ID']}&WF=Y";
				?>
				(
				<a href="<?=$adminLink;?>" target="_blank" title="<?=GetMessage('ARAE_ADMIN_LINK');?>"><?=GetMessage('ARAE_ADMIN_LINK');?></a> |
				<a href="<?=$publicLink;?>" target="_blank" title="<?=GetMessage('ARAE_PUBLIC_LINK');?>"><?=GetMessage('ARAE_PUBLIC_LINK');?></a>
				)
				<?=$arElement['NAME']?>
			<? endif ?>
		</td>
	</tr>
<?
$tabControl->EndCustomField('ELEMENT_ID');



$tabControl->BeginCustomField('USER_ID', $arFieldTitle['USER_ID']);
?>
	<tr>
		<td><?=$tabControl->GetCustomLabelHTML()?></td>
		<td>
			<?=FindUserID(
			/*$tag_name=*/
				 "USER_ID",
				 /*$tag_value=*/
				 $fields['USER_ID'],
				 /*$user_name=*/
				 "",
				 /*$form_name=*/
				 "review_edit_form",
				 /*$tag_size=*/
				 "",
				 /*$tag_maxlength=*/
				 "",
				 /*$button_value=*/
				 "...",
				 /*$tag_class=*/
				 "",
				 /*$button_class=*/
				 ""
			);?>
		</td>
	</tr>
<?
/*if($fields['USER_ID']) {
	//$userName = ($fields['USER_ID'] ? Tools::getFormatedUserName($fields['USER_ID'], false) : $USER->GetFormattedName());
	$tabControl->AddViewField('USER_ID', $arFieldTitle['USER_ID'] . ':', Tools::getFormatedUserName($fields['USER_ID'], true));
}
else {
	$tabControl->AddEditField('USER_ID', $arFieldTitle['USER_ID'], false, array(), $fields['USER_ID']);
}
*/
$tabControl->EndCustomField('USER_ID');



$tabControl->AddEditField('URL', $arFieldTitle['URL'], false, array('size' => 100), $fields['URL']);

if($bSale && $arDelivery)
	$tabControl->AddDropDownField('DELIVERY', $arFieldTitle['DELIVERY'], false, $arDelivery, $fields['DELIVERY']);
else
	$tabControl->AddEditField('DELIVERY', $arFieldTitle['DELIVERY'], false, array('size' => 100), $fields['DELIVERY']);


//---------- HEADING_GUEST ----------//
$tabControl->AddSection('HEADING_GUEST', Loc::getMessage('ARAE_HEADING_GUEST'));
$tabControl->AddEditField('GUEST_NAME', $arFieldTitle['GUEST_NAME'], false, array('size' => 50), $fields['GUEST_NAME']);
$tabControl->AddEditField('GUEST_EMAIL', $arFieldTitle['GUEST_EMAIL'], false, array('size' => 50), $fields['GUEST_EMAIL']);
$tabControl->AddEditField('GUEST_PHONE', $arFieldTitle['GUEST_PHONE'], false, array('size' => 50), $fields['GUEST_PHONE']);

if($bSale) {
	$tabControl->BeginCustomField('CITY', $arFieldTitle['CITY']);
	?>
	<tr>
		<td><?=$tabControl->GetCustomLabelHTML()?></td>
		<td>
			<?
			if(intval($fields['CITY'])>0){
				CSaleLocation::proxySaleAjaxLocationsComponent(
					 array(
							"LOCATION_VALUE"  => $fields['CITY'],
							"CITY_INPUT_NAME" => 'CITY',
							"SITE_ID"         => $fields['SITE_ID'],
					 ),
					 array(),
					 '',
					 true,
					 'api-location'
				);
			}
			else{
				?><input type="text" size="50" name="CITY" value="<?=$fields['CITY']?>"><?
			}
			?>
		</td>
	</tr>
	<?
	$tabControl->EndCustomField('CITY');
}
else {
	$tabControl->AddEditField('CITY', $arFieldTitle['CITY'], false, array('size' => 100), $fields['CITY']);
}

$tabControl->Buttons(array(
	 "disabled" => ($AR_RIGHT < "W"),
	 "back_url" => "api_reviews_list.php?lang=" . $lang,
));

$tabControl->Show();

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');
?>
<?php
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

/** @global CCacheManager $CACHE_MANAGER */

use Bitrix\Main\Loader,
	 Bitrix\Main\Application,
	 Bitrix\Main\UserTable,
	 Bitrix\Main\Web\Json,
	 Bitrix\Main\Text\Encoding,
	 Bitrix\Main\Localization\Loc;

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

Loc::loadMessages(__FILE__);

$arResultModules = array(
	 'api.reviews' => Loader::includeModule('api.reviews'),
	 'sale'        => Loader::includeModule('sale'),
);

if(!$arResultModules['api.reviews']) {
	ShowError(Loc::getMessage('API_REVIEWS_MODULE_ERROR'));
	return;
}

if(Loader::includeModule('api.core')){
	CUtil::InitJSCore(array('api_width', 'api_button', 'api_form', 'api_modal', 'api_alert', 'api_magnific_popup'));
}

//Inc template lang
$templateFile = CApiReviews::getTemplateFile($this);
Loc::loadMessages($templateFile);

use Api\Reviews\Tools,
	 Api\Reviews\Event,
	 Api\Reviews\Agent,
	 Api\Reviews\Converter,
	 Api\Reviews\VideoTable,
	 Api\Reviews\ReviewsTable;



$cache        = Application::getInstance()->getCache();
$taggetCache  = Application::getInstance()->getTaggedCache();
$managedCache = Application::getInstance()->getManagedCache();

$context  = Application::getInstance()->getContext();
$request  = $context->getRequest();
$scheme   = $request->isHttps() ? 'https://' : 'http://';
$httpHost = $scheme . $request->getHttpHost();


$arParams['ID'] = (int)$arParams['ID'];

$arParams['SITE_ID']    = SITE_ID;
$arParams['PAGE_URL']   = ($arParams['~PAGE_URL']   ? $arParams['~PAGE_URL'] : $httpHost . $request->getRequestUri());
$arParams['PAGE_TITLE'] = ($arParams['~PAGE_TITLE'] ? $arParams['~PAGE_TITLE'] : $APPLICATION->GetTitle());
$arParams['DETAIL_URL'] = ($arParams['~DETAIL_URL'] ? $httpHost . $arParams['~DETAIL_URL'] . $arParams['~DETAIL_HASH'] : '');
$arParams['LIST_URL']   = ($arParams['~LIST_URL']   ? $httpHost . $arParams['~LIST_URL'] . $arParams['~DETAIL_HASH'] : '');
//if($arParams['USE_LIST'] == 'Y' && $arParams['~DETAIL_HASH'])
//$arParams['DETAIL_URL'] .= $arParams['~DETAIL_HASH'];


//Правила доступа к модулю
$isEditor              = ($APPLICATION->GetGroupRight('api.reviews') >= 'W');
$arParams['IS_EDITOR'] = $isEditor;

//==============================================================================
//                     MULTILANGUAGE PHRASES REPLACE
//==============================================================================
$arParams['SHOP_NAME']                   = $arParams['~SHOP_NAME'] ? $arParams['~SHOP_NAME'] : Loc::getMessage('API_REVIEWS_LIST_SHOP_NAME');
$arParams['SHOP_NAME_REPLY']             = $arParams['~SHOP_NAME_REPLY'] ? $arParams['~SHOP_NAME_REPLY'] : Loc::getMessage('API_REVIEWS_LIST_SHOP_NAME_REPLY');
$arParams['MESS_ADD_UNSWER_EVENT_THEME'] = $arParams['~MESS_ADD_UNSWER_EVENT_THEME'] ? $arParams['~MESS_ADD_UNSWER_EVENT_THEME'] : Loc::getMessage('API_REVIEWS_LIST_MESS_ADD_UNSWER_EVENT_THEME');
$arParams['MESS_ADD_UNSWER_EVENT_TEXT']  = $arParams['~MESS_ADD_UNSWER_EVENT_TEXT'] ? $arParams['~MESS_ADD_UNSWER_EVENT_TEXT'] : Loc::getMessage('API_REVIEWS_LIST_MESS_ADD_UNSWER_EVENT_TEXT');
$arParams['MESS_TRUE_BUYER']             = $arParams['~MESS_TRUE_BUYER'] ? $arParams['~MESS_TRUE_BUYER'] : Loc::getMessage('API_REVIEWS_LIST_MESS_TRUE_BUYER');
$arParams['MESS_HELPFUL_REVIEW']         = $arParams['~MESS_HELPFUL_REVIEW'] ? $arParams['~MESS_HELPFUL_REVIEW'] : Loc::getMessage('API_REVIEWS_LIST_MESS_HELPFUL_REVIEW');

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



//==============================================================================
//                                  $arParams
//==============================================================================
//BASE PARAMS
$arParams['DISPLAY_FIELDS'] = (array)$arParams['DISPLAY_FIELDS'];
$arParams['THEME']          = ($arParams['THEME'] ? trim($arParams['THEME']) : 'orange');
$arParams['SHOW_THUMBS']    = $arParams['SHOW_THUMBS'] == 'Y';
$arParams['IBLOCK_ID']      = (int)$arParams['IBLOCK_ID'];
$arParams['SECTION_ID']     = (int)$arParams['SECTION_ID'];
$arParams['ELEMENT_ID']     = (int)$arParams['ELEMENT_ID'];
$arParams['ORDER_ID']       = trim($arParams['ORDER_ID']);
$arParams['URL']            = trim($arParams['URL']);

$arParams['THUMBNAIL_WIDTH']  = ($arParams['THUMBNAIL_WIDTH'] ? intval($arParams['THUMBNAIL_WIDTH']) : 114);
$arParams['THUMBNAIL_HEIGHT'] = ($arParams['THUMBNAIL_HEIGHT'] ? intval($arParams['THUMBNAIL_HEIGHT']) : 72);


$arParams['ACTIVE_DATE_FORMAT'] = isset($arParams['ACTIVE_DATE_FORMAT']) ? trim($arParams['ACTIVE_DATE_FORMAT']) : $DB->DateFormatToPHP(CSite::GetDateFormat('SHORT'));
$arParams['ALLOW']              = (array)$arParams['ALLOW'];
foreach($arParams["ALLOW"] as $k => $v)
	if(!$v)
		unset($arParams["ALLOW"][ $k ]);



//PICTURE
$arParams['PICTURE'] = (array)$arParams['PICTURE'];
foreach($arParams["PICTURE"] as $k => $v)
	if(!$v)
		unset($arParams["PICTURE"][ $k ]);

$arParams['RESIZE_PICTURE'] = ($arParams['RESIZE_PICTURE'] ? explode('x', $arParams['RESIZE_PICTURE']) : array(64, 64));
$arParams['PICTURE_WIDTH']  = (int)($arParams['RESIZE_PICTURE'][0] ? $arParams['RESIZE_PICTURE'][0] : 64);
$arParams['PICTURE_HEIGHT'] = (int)($arParams['RESIZE_PICTURE'][1] ? $arParams['RESIZE_PICTURE'][1] : $arParams['PICTURE_WIDTH'] * 2);


//ADDITIONAL PARAMS
$arParams['SET_TITLE']     = $arParams['SET_TITLE'] == 'Y';
$arParams['PAGE_TITLE']    = $arParams['~SHOP_NAME'] ? $arParams['~SHOP_NAME'] : Loc::getMessage('API_REVIEWS_LIST_PAGE_TITLE');
$arParams['BROWSER_TITLE'] = $arParams['~BROWSER_TITLE'] ? $arParams['~BROWSER_TITLE'] : Loc::getMessage('API_REVIEWS_LIST_BROWSER_TITLE');
$arParams['INCLUDE_CSS']   = $arParams['INCLUDE_CSS'] == 'Y';
$arParams['THEME']         = ($arParams['THEME'] ? $arParams['THEME'] : 'flat');

if($arParams['INCLUDE_JQUERY'] && $arParams['INCLUDE_JQUERY'] != 'N') {
	CJSCore::Init($arParams['INCLUDE_JQUERY']);
	$arParams['INCLUDE_JQUERY'] = 'N';
}
CJSCore::Init(array('core', 'session', 'ls'));//array('ajax', 'json', 'ls', 'session', 'jquery', 'popup', 'pull')


//CACHE
$arParams['CACHE_TYPE'] = trim($arParams['CACHE_TYPE']);
$arParams['CACHE_TIME'] = ($arParams['CACHE_TYPE'] != 'N') ? $arParams['CACHE_TIME'] : 0;


//$arrFilter
$arParams['FILTER_NAME'] = $arParams['FILTER_NAME'] ? $arParams['FILTER_NAME'] : 'arrFilter';
if(empty($arParams['FILTER_NAME']) || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams['FILTER_NAME'])) {
	$arrFilter = array();
}
else {
	global ${$arParams['FILTER_NAME']};
	$arrFilter = ${$arParams['FILTER_NAME']};

	if(!is_array($arrFilter))
		$arrFilter = array();
}

$arParams['CACHE_FILTER'] = $arParams['CACHE_FILTER'] == 'Y';
//if(!$arParams['CACHE_FILTER'] && count($arrFilter) > 0)
//	$arParams['CACHE_TIME'] = 0;


/*
$uri = new \Bitrix\Main\Web\Uri($request->getRequestUri());
$uri->deleteParams(\Bitrix\Main\HttpRequest::getSystemParameters());
$page = $uri->getUri();
*/


$isClearCache = ($request->get('clear_cache') == 'Y');
$tagCacheId   = 'review_' . $arParams['ID'];


//---------- $arSelect ----------//
$arBaseSelect = array(
	 'ID',
	 'ACTIVE_FROM',
	 'DATE_CREATE',
	 'ACTIVE',
	 'RATING',
	 'THUMBS_UP',
	 'THUMBS_DOWN',
	 'USER_ID',
	 'SITE_ID',
	 'IBLOCK_ID',
	 'SECTION_ID',
	 'ELEMENT_ID',
	 'ORDER_ID',
	 'URL',
	 'REPLY',
	 'REPLY_SEND',
	 'SUBSCRIBE_SEND',
	 'IP',
);
$arDopSelect  = array_values($arParams['DISPLAY_FIELDS']);

$arSelect = array_merge($arBaseSelect, $arDopSelect);


//---------- $arFilter ----------//
$arFilter = array('=ACTIVE' => 'Y', '=SITE_ID' => SITE_ID);

if($isEditor)
	unset($arFilter['=ACTIVE']);

if($arParams['ID'])
	$arFilter['=ID'] = $arParams['ID'];

$arFilter = array_merge($arFilter, $arrFilter);



//==============================================================================
//                             WORK WITH POST
//==============================================================================
$isPost = ($request->isPost() && $request->get('API_REVIEWS_DETAIL_AJAX') == 'Y' && check_bitrix_sessid());

if($isPost) {
	$return = array();
	$id     = intval($request->getPost('id'));
	$action = $request->getPost('API_REVIEWS_DETAIL_ACTION');

	if($action && !$isClearCache)
		$isClearCache = true;

	if($id) {

		if($arParams['DETAIL_URL'])
			$arParams['PAGE_URL'] = Tools::makeUrl($id, $arParams['DETAIL_URL']);

		//---------- Проверим существование отзыва с таким ID ----------//
		$row = ReviewsTable::getRow(array(
			 'select' => array('ID', 'REPLY_SEND', 'FILES', 'VIDEOS'),
			 'filter' => array('=ID' => $id),
		));

		if($row == null)
			die();


		//---------- Админский экшн ----------//
		if($isEditor) {
			if($action == 'send') {
				if($arParams['USE_SUBSCRIBE'] == 'Y') {

					Agent::add($id, SITE_ID);
					ReviewsTable::update($id, array('SUBSCRIBE_SEND' => 'Y'));
					//$taggetCache->clearByTag($tagCacheId);

					$return = array('status' => 'ok');
				}
				else {
					$return = array('status' => 'error');
				}
			}
			if($action == 'delete') {
				ReviewsTable::delete($id);
				if($arParams['USE_SUBSCRIBE'] == 'Y') {
					Agent::delete($id);
				}
				//$taggetCache->clearByTag($tagCacheId);

				$return = array(
					 'status'  => 'ok',
					 'href' => $arParams['LIST_URL'],
				);
			}
			if($action == 'hide') {
				ReviewsTable::update($id, array('ACTIVE' => 'N'));
			}
			if($action == 'show') {
				ReviewsTable::update($id, array('ACTIVE' => 'Y', 'ACTIVE_FROM' => new \Bitrix\Main\Type\DateTime()));
			}
			if($action == 'save') {
				$fields = (array)$request->getPost('fields');

				if(!Application::isUtfMode())
					$fields = Encoding::convertEncoding($fields, 'UTF-8', $context->getCulture()->getCharset());

				if($arParams['DISPLAY_FIELDS'] && $fields) {
					foreach($fields as $key => $val) {
						if(in_array($key, $arParams['DISPLAY_FIELDS']))
							$fields[ $key ] = trim($val);
						else
							unset($fields[ $key ]);
					}

					//Обязательно обновить эти поля если изменяется отзыв
					$fields['PAGE_URL']   = $arParams['PAGE_URL'];
					$fields['PAGE_TITLE'] = $arParams['PAGE_TITLE'];

					ReviewsTable::update($id, $fields);
				}
			}
			if($action == 'reply') {
				$reply = $request->getPost('reply');
				$bSend = intval($request->getPost('bSend'));

				if(!Application::isUtfMode())
					$reply = Encoding::convertEncoding($reply, 'UTF-8', $context->getCulture()->getCharset());

				$result = ReviewsTable::update($id, array(
					 'REPLY'      => $reply,
					 'REPLY_SEND' => 'N',
					 'PAGE_URL'   => $arParams['PAGE_URL'],
					 'PAGE_TITLE' => $arParams['PAGE_TITLE'],
				));

				if($result->isSuccess()) {
					$return = array(
						 'status' => 'ok',
						 'bSend'  => false,
						 'text'   => Converter::replace(nl2br($reply), $arParams['ALLOW']),
					);
					if($reply && $bSend) {
						Event::sendReply($id, $arParams);
						$return['bSend'] = true;
					}

					//$taggetCache->clearByTag($tagCacheId);
				}
				else {
					$return = array(
						 'status'  => 'error',
						 'message' => Loc::getMessage('API_REVIEWS_LIST_SAVE_REPLY_ERROR'),
					);
				}
			}
			if($action == 'fileDelete') {
				if($fileId = intval($request->get('fileId'))) {
					if($row['FILES']) {
						\CFile::Delete($fileId);

						$arExpFiles = explode(',', $row['FILES']);
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

						//$taggetCache->clearByTag($tagCacheId);
					}

					$return = array(
						 'status' => 'ok',
					);
				}
			}
			if($action == 'videoDelete') {
				if($fileId = intval($request->get('fileId'))) {

					if($row['VIDEOS']) {
						if($arExpVideos = explode(',', $row['VIDEOS'])) {
							foreach($arExpVideos as $key=>$videoId) {
								if($videoId == $fileId){
									$row2 = VideoTable::getRow(array(
										 'select' => array('FILE_ID'),
										 'filter' => array('=ID' => $videoId),
									));
									if($row2['FILE_ID']) {
										\CFile::Delete($row2['FILE_ID']);
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
						//$taggetCache->clearByTag($tagCacheId);
					}

					$return = array(
						 'status' => 'ok',
					);
				}
			}
		}

		//---------- Публичный экшн ----------//
		if($action == 'vote') {

			$vote     = $request->getPost('value');
			$vote_val = ReviewsTable::addVote($id, $vote);

			//if($vote_val != false)
				//$taggetCache->clearByTag($tagCacheId);

			$return = array(
				 'status' => 'ok',
				 'vote'   => $vote_val,
			);
		}
	}

	//return result
	if($return) {
		$APPLICATION->RestartBuffer();
		header('Content-Type: application/json');
		echo Json::encode($return);
		die();
	}
}



//==============================================================================
//                             WORK WITH CACHE
//==============================================================================

//---------- Cache ----------//
//TODO
//$arGroups = ($arParams["CACHE_GROUPS"] === "N" ? false : $USER->GetGroups());
/*
$cache_time = $arParams['CACHE_TIME'];
$cache_id   = $this->getCacheID(array($isEditor, $arParams['ID'], $arrFilter)); //$arGroups
$cache_path = $managedCache->getCompCachePath($this->__relativePath);

//Refresh ajax cache
if($isClearCache) {
	$cache_time = 0;
}

if($cache_time > 0 && $cache->initCache($cache_time, $cache_id, $cache_path)) {

	$arResult = $cache->getVars();
}
else {
*/
	//Обновление кэша при аякс-изменениях
	/*if($cache_time == 0) {
		//$cache->clean($cache_id, $cache_path);
		$cache->cleanDir($cache_path);

		$cache_time = $arParams['CACHE_TIME'];
	}*/

	//$taggetCache->startTagCache($cache_path);


	//---------- Query ----------//


	$rsReviews = ReviewsTable::getList(array(
		 'filter' => $arFilter,
		 'select' => $arSelect,
		 "limit"  => 1,
	));

	if($arResult = $rsReviews->fetch(new Converter)) {

		//SHOP DELIVERY
		$arDelivery = array();
		if($arResultModules['sale']) {
			//STATIC DELIVERY
			$dbRes = CSaleDelivery::GetList(
				 array('SORT' => 'ASC', 'NAME' => 'ASC'),
				 array('ACTIVE' => 'Y', 'SITE_ID' => SITE_ID),
				 false,
				 false,
				 array('ID', 'NAME', 'DESCRIPTION')
			);
			while($delivery = $dbRes->Fetch())
				$arDelivery[ $delivery['ID'] ] = $delivery;

			unset($dbRes, $delivery);

			//AUTOMATIC DELIVERY
			$dbRes = CSaleDeliveryHandler::GetList(
				 array('SORT' => 'ASC', 'NAME' => 'ASC'),
				 array('ACTIVE' => 'Y', 'SITE_ID' => SITE_ID)
			);
			while($delivery = $dbRes->Fetch())
				$arDelivery[ $delivery['ID'] ] = $delivery;

			unset($dbRes, $delivery);
		}
		//\\SHOP DELIVERY


		//RATING STATISTIC
		$bRating  = 0;
		$arUserID = array();

		//$arResult = Tools::formatFields($arResult);
		foreach($arParams['DISPLAY_FIELDS'] as $FIELD) {
			$arResult[ $FIELD ] = Converter::replace($arResult[ $FIELD ], $arParams['ALLOW']);
		}

		$arResult['REPLY'] = Converter::replace($arResult['REPLY'], $arParams['ALLOW']);

		if(strlen($arResult['ACTIVE_FROM']) > 0)
			$arResult['DISPLAY_ACTIVE_FROM'] = Tools::formatDate($arParams['ACTIVE_DATE_FORMAT'], MakeTimeStamp($arResult['ACTIVE_FROM'], CSite::GetDateFormat()));
		else
			$arResult['DISPLAY_ACTIVE_FROM'] = '';


		//Shema.org (ISO 8601) format: 2016-06-12
		$arResult['DISPLAY_DATE_PUBLISHED'] = '';
		$date_published                     = $arResult['ACTIVE_FROM'];
		if(!$date_published) {
			$date_published = $arResult['DATE_CREATE'];
		}
		if($date_published) {
			$arResult['DISPLAY_DATE_PUBLISHED'] = Tools::formatDate('Y-m-d', MakeTimeStamp($date_published, CSite::GetDateFormat()));
		}


		$DELIVERY_ID          = (int)$arResult['DELIVERY'];
		$arResult['DELIVERY'] = $arDelivery[ $DELIVERY_ID ] ? $arDelivery[ $DELIVERY_ID ] : '';


		//USER_ID
		$user = array();
		if($arResult['USER_ID']){

			$arUser = UserTable::getList(array(
				 'filter' => array('=ID' => $arResult['USER_ID']),
				 'select' => array('ID', 'TITLE', 'NAME', 'LAST_NAME', 'SECOND_NAME', 'LOGIN', 'EMAIL', 'PERSONAL_PHONE', 'PERSONAL_PHOTO'),
			))->fetch();

			$siteNameFormat = \CSite::GetNameFormat(false);
			$user = array(
				 'ID'          => $arUser['ID'],
				 'PICTURE'     => $arUser['PERSONAL_PHOTO'],
				 'GUEST_NAME'  => \CUser::FormatName($siteNameFormat, $arUser, true, true),
				 'GUEST_EMAIL' => $arUser['EMAIL'],
				 'GUEST_PHONE' => $arUser['PERSONAL_PHONE'],
			);

			/*$dbRes = CUser::GetList(
				 $by = 'ID',
				 $order = 'ASC',
				 array('=ID' => $arResult['USER_ID']),
				 array('FIELDS' => array('ID', 'LOGIN', 'NAME', 'LAST_NAME', 'EMAIL'))
			);
			if($arUser = $dbRes->Fetch()) {

				if(!$arUser['GUEST_NAME'])
					$arUser['GUEST_NAME'] = trim(str_replace(array('#NAME#', '#LAST_NAME#'), array($arUser['NAME'], $arUser['LAST_NAME']), Loc::getMessage('API_REVIEWS_LIST_FORMAT_NAME')));
				//$arUser['GUEST_NAME'] = trim(Loc::getMessage('API_REVIEWS_LIST_FORMAT_NAME', array('#NAME#' => $arUser['NAME'], '#LAST_NAME#' => $arUser['LAST_NAME'])));

				if(!$arUser['GUEST_NAME'] && $arUser['LOGIN'])
					$arUser['GUEST_NAME'] = $arUser['LOGIN'];

				if(!$arUser['GUEST_EMAIL'] && $arUser['EMAIL'])
					$arUser['GUEST_EMAIL'] = $arUser['EMAIL'];

				if(!$arUser['GUEST_PHONE'] && $arUser['PERSONAL_PHONE'])
					$arUser['GUEST_PHONE'] = $arUser['PERSONAL_PHONE'];

				//$arUser['PICTURE'] = $arUser['PERSONAL_PHOTO'];
			}*/
		}

		$arResult['USER_URL'] = ($user['ID'] ? str_replace('#user_id#',$user['ID'],$arParams['USER_URL']) : '');

		if(!$arResult['GUEST_NAME']) {
			$arResult['GUEST_NAME'] = ($user['GUEST_NAME'] ? $user['GUEST_NAME'] : Loc::getMessage('API_REVIEWS_LIST_GUEST_NAME'));
		}
		$arResult['GUEST_EMAIL'] = ($arResult['GUEST_EMAIL'] ? $arResult['GUEST_EMAIL'] : $user['GUEST_EMAIL']);
		$arResult['GUEST_PHONE'] = ($arResult['GUEST_PHONE'] ? $arResult['GUEST_PHONE'] : $user['GUEST_PHONE']);

		$arResult['PICTURE'] = $arResult['GUEST_PICTURE'] = array();
		if($picture = $user['PICTURE']) {

			$arFileTmp = CFile::ResizeImageGet($picture, array("width" => 64, "height" => 64), BX_RESIZE_IMAGE_EXACT, true);

			$arFileTmp['src'] = CUtil::GetAdditionalFileURL($arFileTmp['src'], true);

			$arResult['PICTURE'] = array_change_key_case($arFileTmp, CASE_UPPER);

			//TODO: Compatible with old template
			$arResult['GUEST_PICTURE']['SRC'] = $arResult['PICTURE']['SRC'];
		}{
			$arResult['PICTURE']['SRC'] = $arResult['GUEST_PICTURE']['SRC'] = '/bitrix/images/api.reviews/userpic.png?v=1';
		}


		//CITY_ID
		if(intval($arResult['CITY']) > 0 && $arResultModules['sale']) {
			$arLocations = array();
			$arRegion    = array();

			$dbRes       = CSaleLocation::GetList(
				 array(),
				 array('REGION_ID' => $arResult['CITY'], 'LID' => LANGUAGE_ID),
				 false,
				 false,
				 array('ID', 'CITY_NAME', 'REGION_NAME')
			);
			if($arLoc = $dbRes->Fetch()) {
				$arRegion[ $arLoc['ID'] ] = $arLoc;
			}

			$arCity = array();
			$dbRes  = CSaleLocation::GetList(
				 array(),
				 array('CITY_ID' => $arResult['CITY'], 'LID' => LANGUAGE_ID),
				 false,
				 false,
				 array('ID', 'CITY_NAME', 'REGION_NAME')
			);
			while($arLoc = $dbRes->Fetch()) {
				$arCity[ $arLoc['ID'] ] = $arLoc;
			}

			$arLocations = $arRegion + $arCity;

			//CITY INFO
			$arResult['LOCATION'] = $arResult['CITY'];
			if($arLocations && $arCurLocation = $arLocations[ $arResult['CITY'] ]) {
				if($arCurLocation['CITY_NAME'])
					$arResult['LOCATION'] = $arCurLocation['CITY_NAME'];

				if($arCurLocation['REGION_NAME'])
					$arResult['LOCATION'] .= ', ' . $arCurLocation['REGION_NAME'];
			}
			//\\CITY INFO

		}

		$arResult['STATUS'] = ($arResult['ACTIVE'] == 'N' ? Loc::getMessage('API_REVIEWS_LIST_STATUS_M') : '');

		$arResult['THUMBS_UP']   = trim($arResult['THUMBS_UP'], '+');
		$arResult['THUMBS_DOWN'] = trim($arResult['THUMBS_DOWN'], '-');

		$cookieVote                     = $_COOKIE[ 'API_REVIEWS_VOTE_' . $arResult['ID'] ];
		$arResult['THUMBS_UP_ACTIVE']   = (isset($cookieVote) && $cookieVote == 1);
		$arResult['THUMBS_DOWN_ACTIVE'] = (isset($cookieVote) && $cookieVote == -1);

		//$arResult['DETAIL_URL'] = str_replace('#review_id#', $arResult['ID'], $arParams['~DETAIL_URL']);
		$arResult['DETAIL_URL'] = Tools::makeUrl($arResult['ID'], $arParams['DETAIL_URL']);


		//IBLOCK ELEMENT_ID
		$arResult['ELEMENT_FIELDS'] = array();
		if($arParams['PICTURE'] && $arResult['ELEMENT_ID'] && CModule::IncludeModule('iblock')) {
			$arElSelect = array('ID', 'IBLOCK_ID', 'NAME', 'DETAIL_PAGE_URL');
			$arElSelect = array_merge($arElSelect, $arParams['PICTURE']);

			$arElFilter = array('=ID' => $arResult['ELEMENT_ID']);

			$dbRes = CIBlockElement::GetList(array(), $arElFilter, false, false, $arElSelect);
			while($arRes = $dbRes->GetNext(true, false)) {
				$arElFields = array(
					 'ID'              => $arRes['ID'],
					 'IBLOCK_ID'       => $arRes['IBLOCK_ID'],
					 'NAME'            => $arRes['NAME'],
					 'DETAIL_PAGE_URL' => $arRes['DETAIL_PAGE_URL'],
					 'PICTURE'         => $arRes['PICTURE'],
				);

				$picture = false;
				if($arRes['PREVIEW_PICTURE'] && in_array('PREVIEW_PICTURE', $arParams['PICTURE']))
					$picture = $arRes['PREVIEW_PICTURE'];
				elseif($arRes['DETAIL_PICTURE'] && in_array('DETAIL_PICTURE', $arParams['PICTURE']))
					$picture = $arRes['DETAIL_PICTURE'];

				if($picture) {
					if($arParams['RESIZE_PICTURE']) {
						$arFileTmp = CFile::ResizeImageGet(
							 $picture,
							 array(
									'width'  => $arParams['PICTURE_WIDTH'],
									'height' => $arParams['PICTURE_HEIGHT'],
							 )
						);

						$arElFields['PICTURE'] = array(
							 'SRC' => $arFileTmp['src'],
							 //'WIDTH'  => $arFileTmp['width'],
							 //'HEIGHT' => $arFileTmp['height'],
						);
					}
					else {
						$arElFields['PICTURE'] = CFile::GetFileArray($picture);
					}
				}

				$arResult['ELEMENT_FIELDS'] = $arElFields;
			}
		}

		//IBLOCK SECTION_ID
		$arResult['SECTION_FIELDS'] = array();


		//FILE_INFO and THUMBNAIL
		if($arResult['FILES']){
			$arFiles = array();
			if($arFileId = explode(',',$arResult['FILES'])){
				foreach($arFileId as $fileId){
					$arFile = \CFile::GetFileArray($fileId);

					if($arFile['SRC'])
						$arFile['SRC'] = CUtil::GetAdditionalFileURL($arFile['SRC'], true);

					$arFile['FORMAT_SIZE'] = \CFile::FormatSize($arFile['FILE_SIZE'],0);
					$arFile['FORMAT_NAME'] = htmlspecialcharsbx($arFile['ORIGINAL_NAME'] .' ('. $arFile['FORMAT_SIZE'] .')');
					$arFile['EXTENSION'] = GetFileExtension($arFile['ORIGINAL_NAME']);

					$arFile['THUMBNAIL'] = array();
					if(preg_match('/image*/',$arFile['CONTENT_TYPE'])){
						$arFileTmp = CFile::ResizeImageGet(
							 $arFile,
							 array("width" => $arParams['THUMBNAIL_WIDTH'], "height" => $arParams['THUMBNAIL_HEIGHT']),
							 BX_RESIZE_IMAGE_PROPORTIONAL,
							 false
						);

						if($arFileTmp['src'])
							$arFileTmp['src'] = CUtil::GetAdditionalFileURL($arFileTmp['src'], true);

						$arFile['THUMBNAIL'] = array_change_key_case($arFileTmp, CASE_UPPER);
					}

					$arFiles[] = $arFile;
				}
			}

			$arResult['FILES'] = $arFiles;
		}


		if($arResult['VIDEOS']){
			$arVideos = VideoTable::getList(array(
				 'filter' => array('=ID' => explode(',',$arResult['VIDEOS']))
			))->fetchAll();

			if($arVideos){
				foreach($arVideos as &$video){

					$video['SRC']   = Tools::getVideoUrl($video);
					$video['TITLE'] = CUtil::JSEscape($video['TITLE']);
					$video['THUMBNAIL'] = array();
					if($video['FILE_ID']){
						$arFileTmp = CFile::ResizeImageGet(
							 $video['FILE_ID'],
							 array("width" => $arParams['THUMBNAIL_WIDTH'], "height" => $arParams['THUMBNAIL_HEIGHT']),
							 BX_RESIZE_IMAGE_PROPORTIONAL,
							 false
						);

						if($arFileTmp['src'])
							$arFileTmp['src'] = CUtil::GetAdditionalFileURL($arFileTmp['src'], true);

						$video['THUMBNAIL'] = array_change_key_case($arFileTmp, CASE_UPPER);
					}
				}
			}
			$arResult['VIDEOS'] = $arVideos;
		}


		/*$taggetCache->registerTag($tagCacheId);
		$taggetCache->endTagCache();

		if($cache_time) {
			//начинаем буферизирование вывода
			$cache->startDataCache($cache_time, $cache_id, $cache_path);

			//Кэшируем переменные
			$cache->endDataCache($arResult);
		}*/
	}
	else {

		//Отменяем кэширование
		//$cache->abortDataCache();

		//Выводим 404 страницу
		Tools::send404(
			 trim($arParams["MESSAGE_404"]) ?: Loc::getMessage('API_REVIEWS_STATUS_404')
			 , true
			 , $arParams["SET_STATUS_404"] === "Y"
			 , $arParams["SHOW_404"] === "Y"
			 , $arParams["FILE_404"]
		);
	}
/*}*/




if($isPost) {
	$APPLICATION->RestartBuffer();
	$this->includeComponentTemplate('ajax');
	die();
}
else {
	$this->includeComponentTemplate();
}



if($arParams['SET_TITLE']) {

	if($arResult['TITLE']){
		$APPLICATION->SetTitle($arResult['TITLE']);
		$APPLICATION->SetPageProperty("title", $arResult['TITLE']);
		$APPLICATION->AddChainItem($arResult['TITLE']);
	}
	elseif($arParams['SHOP_NAME']){
		$APPLICATION->SetTitle($arParams['PAGE_TITLE']);
		$APPLICATION->SetPageProperty("title", $arParams['BROWSER_TITLE']);
	}
}
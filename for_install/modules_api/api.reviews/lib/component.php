<?php

namespace Api\Reviews;

use Bitrix\Main\Loader,
	 Bitrix\Main\Application,
	 Bitrix\Main\UserTable,
	 Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Component extends \CBitrixComponent
{
	public function getReviewsList(array $parameters = array())
	{
		global $USER, $APPLICATION;

		$request  = &$this->request;
		$arParams = &$this->arParams;
		$arResult = &$this->arResult;

		//Правила доступа к модулю
		$useSale   = Loader::includeModule('sale');
		$useIblock = Loader::IncludeModule('iblock');

		$cache        = Application::getInstance()->getCache();
		$taggetCache  = Application::getInstance()->getTaggedCache();
		$managedCache = Application::getInstance()->getManagedCache();

		$scheme   = $request->isHttps() ? 'https://' : 'http://';
		$httpHost = $scheme . $request->getHttpHost();


		$arSort   = $parameters['order'];
		$arSelect = $parameters['select'];
		$arFilter = $parameters['filter'];


		$navParams = array();
		if(($value = $request->getQuery($arParams['PAGER_ID'])) !== null) {
			//parameters are in the QUERY_STRING
			$params = explode("-", $value);
			for($i = 0, $n = count($params); $i < $n; $i += 2) {
				$navParams[ $params[ $i ] ] = $params[ $i + 1 ];
			}
		}
		else {
			//probably parametrs are in the SEF URI
			$matches = array();
			if(preg_match("'/" . preg_quote($arParams['PAGER_ID'], "'") . "/page-([\\d]+|all)+(/size-([\\d]+))?'", $request->getRequestUri(), $matches)) {
				$navParams["page"] = $matches[1];
				if(isset($matches[3])) {
					$navParams["size"] = $matches[3];
				}
			}
		}

		$isClearCache = ($request->get('clear_cache') == 'Y');
		$tagCacheId = 'page_' . intval($navParams['page']);


		$cache_time = $arParams['CACHE_TIME'];
		$cache_id   = $this->getCacheID(array($arFilter, $arSort, $navParams)); //$arGroups
		$cache_path = $managedCache->getCompCachePath($this->getRelativePath());

		//Refresh ajax cache
		if($isClearCache) {
			$cache_time = 0;
		}

		if($cache_time > 0 && $cache->initCache($cache_time, $cache_id, $cache_path)) {
			$arResult = $cache->getVars();
		}
		else {
			//Обновление кэша при аякс-изменениях
			if($cache_time == 0) {
				//$cache->clean($cache_id, $cache_path);
				$cache->cleanDir($cache_path);

				$cache_time = $arParams['CACHE_TIME'];
			}

			$taggetCache->startTagCache($cache_path);

			$b404 = false;


			//Pager from DB
			$cnt = ReviewsTable::getCount($arFilter);
			$nav = new \Bitrix\Main\UI\ReversePageNavigation($arParams['PAGER_ID'], $cnt);
			$nav->allowAllRecords($arParams['PAGER_SHOW_ALL'])
				 ->setPageSize($arParams['ITEMS_LIMIT'])
				 ->initFromUri();

			if(isset($navParams['page'])) {
				$currentPage = $nav->getCurrentPage();
				if($currentPage != $navParams['page']) {
					$b404 = true;
				}
			}

			if(!$b404) {

				//SHOP DELIVERY
				$arDelivery = array();
				if($useSale) {
					//STATIC DELIVERY
					$dbRes = \CSaleDelivery::GetList(
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
					$dbRes = \CSaleDeliveryHandler::GetList(
						 array('SORT' => 'ASC', 'NAME' => 'ASC'),
						 array('ACTIVE' => 'Y', 'SITE_ID' => SITE_ID)
					);
					while($delivery = $dbRes->Fetch())
						$arDelivery[ $delivery['ID'] ] = $delivery;

					unset($dbRes, $delivery);
				}
				//\\SHOP DELIVERY


				//Proccess data
				$rsReviews = ReviewsTable::getList(array(
					 'order'  => $arSort,
					 'filter' => $arFilter,
					 'select' => $arSelect,
					 "offset" => $nav->getOffset(),//pager
					 "limit"  => $nav->getLimit(),//pager
				));


				//RATING STATISTIC
				$bRating  = 0;
				$arUserID = $arCityID = array();

				$arElementId = $arSectionId = array();

				while($arItem = $rsReviews->fetch(new Converter)) {

					foreach($arParams['DISPLAY_FIELDS'] as $FIELD) {
						$arItem[ $FIELD ] = Converter::replace($arItem[ $FIELD ], $arParams['ALLOW']);
					}

					$arItem['REPLY'] = Converter::replace($arItem['REPLY'], $arParams['ALLOW']);

					if(strlen($arItem['ACTIVE_FROM']) > 0)
						$arItem['DISPLAY_ACTIVE_FROM'] = Tools::formatDate($arParams['ACTIVE_DATE_FORMAT'], MakeTimeStamp($arItem['ACTIVE_FROM'], \CSite::GetDateFormat()));
					else
						$arItem['DISPLAY_ACTIVE_FROM'] = '';


					//Shema.org (ISO 8601) format: 2016-06-12
					$arItem['DISPLAY_DATE_PUBLISHED'] = '';
					$date_published                   = $arItem['ACTIVE_FROM'];
					if(!$date_published) {
						$date_published = $arItem['DATE_CREATE'];
					}
					if($date_published) {
						$arItem['DISPLAY_DATE_PUBLISHED'] = Tools::formatDate('Y-m-d', MakeTimeStamp($date_published, \CSite::GetDateFormat()));
					}


					$DELIVERY_ID        = (int)$arItem['DELIVERY'];
					$arItem['DELIVERY'] = $arDelivery[ $DELIVERY_ID ] ? $arDelivery[ $DELIVERY_ID ] : '';


					//USER_ID
					if($arItem['USER_ID'])
						$arUserID[] = $arItem['USER_ID'];

					//CITY_ID
					if(intval($arItem['CITY']) > 0)
						$arCityID[] = $arItem['CITY'];


					$arItem['STATUS'] = ($arItem['ACTIVE'] == 'N' ? Loc::getMessage('API_REVIEWS_LIST_STATUS_M') : '');

					$arItem['THUMBS_UP']   = trim($arItem['THUMBS_UP'], '+');
					$arItem['THUMBS_DOWN'] = trim($arItem['THUMBS_DOWN'], '-');

					$cookieVote                   = $_COOKIE[ 'API_REVIEWS_VOTE_' . $arItem['ID'] ];
					$arItem['THUMBS_UP_ACTIVE']   = (isset($cookieVote) && $cookieVote == 1);
					$arItem['THUMBS_DOWN_ACTIVE'] = (isset($cookieVote) && $cookieVote == -1);


					//ELEMENT_ID
					$arItem['ELEMENT_FIELDS'] = array();
					if($arItem['ELEMENT_ID'])
						$arElementId[] = $arItem['ELEMENT_ID'];


					//SECTION_ID
					$arItem['SECTION_FIELDS'] = array();
					if($arItem['SECTION_ID'])
						$arSectionId[] = $arItem['SECTION_ID'];


					//$arItem['DETAIL_URL'] = str_replace('#review_id#', $arItem['ID'], $arParams['~DETAIL_URL']);
					$arItem['DETAIL_URL'] = Tools::makeUrl($arItem['ID'], $arParams['DETAIL_URL']);

					$arResult['ITEMS'][] = $arItem;
				}
				unset($arItem, $RATING, $DELIVERY_ID);

				//GET CITY INFO
				$arLocations = array();
				if($useSale && $arCityID = array_unique($arCityID)) {
					$arRegion = array();
					$dbRes    = \CSaleLocation::GetList(
						 array(),
						 array('REGION_ID' => $arCityID, 'LID' => LANGUAGE_ID),
						 false,
						 false,
						 array('ID', 'CITY_NAME', 'REGION_NAME')
					);
					if($arLoc = $dbRes->Fetch()) {
						$arRegion[ $arLoc['ID'] ] = $arLoc;
					}

					$arCity = array();
					$dbRes  = \CSaleLocation::GetList(
						 array(),
						 array('CITY_ID' => $arCityID, 'LID' => LANGUAGE_ID),
						 false,
						 false,
						 array('ID', 'CITY_NAME', 'REGION_NAME')
					);
					while($arLoc = $dbRes->Fetch()) {
						$arCity[ $arLoc['ID'] ] = $arLoc;
					}

					$arLocations = $arRegion + $arCity;
				}
				//\\GET CITY INFO


				//GET USERS INFO
				$arUsers = array();
				if($arUserID = array_unique($arUserID)) {
					$rsUsers = UserTable::getList(array(
						 'filter' => array('=ID' => array_values($arUserID)),
						 'select' => array('ID', 'TITLE', 'NAME', 'LAST_NAME', 'SECOND_NAME', 'LOGIN', 'EMAIL', 'PERSONAL_PHONE', 'PERSONAL_PHOTO'),
					));

					$siteNameFormat = \CSite::GetNameFormat(false);
					while($arUser = $rsUsers->fetch()) {
						$arUsers[ $arUser['ID'] ] = array(
							 'ID'          => $arUser['ID'],
							 'PICTURE'     => $arUser['PERSONAL_PHOTO'],
							 'GUEST_NAME'  => \CUser::FormatName($siteNameFormat, $arUser, true, true),
							 'GUEST_EMAIL' => $arUser['EMAIL'],
							 'GUEST_PHONE' => $arUser['PERSONAL_PHONE'],
						);
					}
				}
				//\\GET USERS INFO


				$arElementFields = array();
				if($arParams['PICTURE'] && $useIblock && $arElementId) {
					$arElSelect = array('ID', 'IBLOCK_ID', 'NAME', 'DETAIL_PAGE_URL');
					$arElSelect = array_merge($arElSelect, $arParams['PICTURE']);

					$arElFilter = array('=ID' => $arElementId);
					if($arParams['IBLOCK_ID'])
						$arElFilter['IBLOCK_ID'] = $arParams['IBLOCK_ID'];


					$dbRes = \CIBlockElement::GetList(array(), $arElFilter, false, false, $arElSelect);
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
								$arFileTmp = \CFile::ResizeImageGet(
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
								$arElFields['PICTURE'] = \CFile::GetFileArray($picture);
							}
						}

						$arElementFields[ $arRes['ID'] ] = $arElFields;
					}
				}


				//OTHER DATA REPLACE
				if($arResult['ITEMS']) {
					foreach($arResult['ITEMS'] as $key => &$arItem) {

						$user = (array)$arUsers[ $arItem['USER_ID'] ];

						if(!$arItem['GUEST_NAME'])
							$arItem['GUEST_NAME'] = ($user['GUEST_NAME'] ? $user['GUEST_NAME'] : Loc::getMessage('API_REVIEWS_LIST_GUEST_NAME'));

						$arItem['GUEST_EMAIL'] = ($arItem['GUEST_EMAIL'] ? $arItem['GUEST_EMAIL'] : $user['GUEST_EMAIL']);
						$arItem['GUEST_PHONE'] = ($arItem['GUEST_PHONE'] ? $arItem['GUEST_PHONE'] : $user['GUEST_PHONE']);


						//$arItem['PICTURE'] = Tools::getGravatar($arItem['GUEST_EMAIL']);;
						$arItem['PICTURE'] = $arItem['GUEST_PICTURE'] = array();
						if($picture = $arUsers[ $arItem['USER_ID'] ]['PICTURE']) {

							$arFileTmp = \CFile::ResizeImageGet($picture, array("width" => 64, "height" => 64), BX_RESIZE_IMAGE_EXACT, true);

							$arFileTmp['src'] = \CUtil::GetAdditionalFileURL($arFileTmp['src'], true);

							$arItem['PICTURE'] = array_change_key_case($arFileTmp, CASE_UPPER);
						}
						else{
							$arItem['PICTURE']['SRC'] = '/bitrix/images/api.reviews/userpic.png?v=1';
						}


						//CITY INFO
						$arItem['LOCATION'] = $arItem['CITY'];
						if($arLocations && $arCurLocation = $arLocations[ $arItem['CITY'] ]) {
							if($arCurLocation['CITY_NAME'])
								$arItem['LOCATION'] = $arCurLocation['CITY_NAME'];

							if($arCurLocation['REGION_NAME'])
								$arItem['LOCATION'] .= ', ' . $arCurLocation['REGION_NAME'];
						}
						//\\CITY INFO


						//ELEMENT_INFO
						if($ELEMENT_ID = $arItem['ELEMENT_ID']) {
							if($arElement = $arElementFields[ $ELEMENT_ID ]) {
								$arItem['ELEMENT_FIELDS'] = $arElement;
							}
						}

						//FILE_INFO and THUMBNAIL
						if($arItem['FILES']) {
							$arFiles = array();
							if($arFileId = explode(',', $arItem['FILES'])) {
								foreach($arFileId as $fileId) {
									$arFile = \CFile::GetFileArray($fileId);

									if($arFile['SRC'])
										$arFile['SRC'] = \CUtil::GetAdditionalFileURL($arFile['SRC'], true);

									$arFile['FORMAT_SIZE'] = \CFile::FormatSize($arFile['FILE_SIZE'], 0);
									$arFile['FORMAT_NAME'] = htmlspecialcharsbx($arFile['ORIGINAL_NAME'] . ' (' . $arFile['FORMAT_SIZE'] . ')');
									$arFile['EXTENSION']   = GetFileExtension($arFile['ORIGINAL_NAME']);

									$arFile['THUMBNAIL'] = array();
									if(preg_match('/image*/', $arFile['CONTENT_TYPE'])) {
										$arFileTmp = \CFile::ResizeImageGet(
											 $arFile,
											 array("width" => $arParams['THUMBNAIL_WIDTH'], "height" => $arParams['THUMBNAIL_HEIGHT']),
											 BX_RESIZE_IMAGE_PROPORTIONAL,
											 false
										);

										if($arFileTmp['src'])
											$arFileTmp['src'] = \CUtil::GetAdditionalFileURL($arFileTmp['src'], true);

										$arFile['THUMBNAIL'] = array_change_key_case($arFileTmp, CASE_UPPER);
									}

									$arFiles[] = $arFile;
								}
							}

							$arItem['FILES'] = $arFiles;
						}

						if($arItem['VIDEOS']) {
							$arVideos = VideoTable::getList(array(
								 'filter' => array('=ID' => explode(',', $arItem['VIDEOS'])),
							))->fetchAll();

							if($arVideos) {
								foreach($arVideos as &$video) {

									$video['SRC']       = Tools::getVideoUrl($video);
									$video['TITLE']     = \CUtil::JSEscape($video['TITLE']);
									$video['THUMBNAIL'] = array();
									if($video['FILE_ID']) {
										$arFileTmp = \CFile::ResizeImageGet(
											 $video['FILE_ID'],
											 array("width" => $arParams['THUMBNAIL_WIDTH'], "height" => $arParams['THUMBNAIL_HEIGHT']),
											 BX_RESIZE_IMAGE_PROPORTIONAL,
											 false
										);

										if($arFileTmp['src'])
											$arFileTmp['src'] = \CUtil::GetAdditionalFileURL($arFileTmp['src'], true);

										$video['THUMBNAIL'] = array_change_key_case($arFileTmp, CASE_UPPER);
									}
								}
							}
							$arItem['VIDEOS'] = $arVideos;
						}
					}
				}

				$arResult['NAV_OBJECT']  = $nav;
				$arResult['COUNT_ITEMS'] = (int)$cnt;

				$taggetCache->registerTag($tagCacheId);
				$taggetCache->endTagCache();

				if($cache_time) {
					//начинаем буферизирование вывода
					$cache->startDataCache($cache_time, $cache_id, $cache_path);

					//Кэшируем переменные
					$cache->endDataCache($arResult);
				}
			}
			else {

				//Отменяем кэширование
				$cache->abortDataCache();

				//Выводим 404 страницу
				Tools::send404(
					 trim($arParams["MESSAGE_404"]) ?: Loc::getMessage('API_REVIEWS_STATUS_404')
					 , true
					 , $arParams["SET_STATUS_404"] === "Y"
					 , $arParams["SHOW_404"] === "Y"
					 , $arParams["FILE_404"]
				);
			}
		}
	}
}
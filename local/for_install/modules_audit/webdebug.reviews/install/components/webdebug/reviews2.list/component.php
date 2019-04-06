<?
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule('webdebug.reviews')) {
	return;
}

$arParamsOriginal = $arParams;
foreach ($arParamsOriginal as $Key => $Value) {
	if (strpos($Key,'~')===0) {
		unset($arParamsOriginal[$Key]);
	}
}

$Lang = LANGUAGE_ID;

$InterfaceID = IntVal($arParams['INTERFACE_ID']);
if (!is_numeric($InterfaceID) || $InterfaceID<=0) {
	CWD_Reviews2::ShowError(GetMessage('WDR2_ERROR_INTERFACE_EMPTY'));
	return;
}
if (trim($arParams['TARGET'])=='') {
	CWD_Reviews2::ShowError(GetMessage('WDR2_ERROR_TARGET_EMPTY'));
	return;
}

if (strlen($arParams['TARGET_SUFFIX'])>0) {
	$arParams['TARGET'] = $arParams['TARGET_SUFFIX'].$arParams['TARGET'];
}

if (strlen($arParams['USER_ANSWER_NAME'])<=0) {
	$arParams['USER_ANSWER_NAME'] = '#NAME# #LAST_NAME#';
}
$arParams["DISPLAY_TOP_PAGER"] = $arParams["DISPLAY_TOP_PAGER"]=='Y';
$arParams["DISPLAY_BOTTOM_PAGER"] = $arParams["DISPLAY_BOTTOM_PAGER"]=='Y';
$arParams['COUNT'] = IntVal($arParams['COUNT']);
if ($arParams['COUNT']<=0) {
	$arParams['COUNT'] = 10;
}
$arParams["SHOW_AVATARS"] = $arParams["SHOW_AVATARS"]!='N';
$arParams["SHOW_ANSWERS"] = $arParams["SHOW_ANSWERS"]!='N';
$arParams["SHOW_ANSWER_DATE"] = $arParams["SHOW_ANSWER_DATE"]!='N';
$arParams["SHOW_ANSWER_AVATAR"] = $arParams["SHOW_ANSWER_AVATAR"]!='N';
$arParams["ALLOW_VOTE"] = $arParams["ALLOW_VOTE"]!='N';

$arParams["AUTO_LOADING"] = $arParams["AUTO_LOADING"]=='Y';

$arSorter = array();
if (strlen($arParams['SORT_BY_1'])) {
	$arSorter[$arParams['SORT_BY_1']] = $arParams['SORT_ORDER_1'];
}
if (strlen($arParams['SORT_BY_2'])) {
	$arSorter[$arParams['SORT_BY_2']] = $arParams['SORT_ORDER_2'];
}
$arFilter = array(
	'INTERFACE_ID' => $InterfaceID,
	'MODERATED' => 'Y',
	'TARGET' => $arParams['TARGET'],
);
if ($arParams['SHOW_ALL_IF_ADMIN']!='N' && $USER->IsAdmin()) {
	unset($arFilter['MODERATED']);
	$arResult['SHOW_UNMODERATED'] = true;
}
if (strlen($arParams['FILTER_NAME']) && is_array($GLOBALS[$arParams['FILTER_NAME']])) {
	$arFilter = array_merge($arFilter, $GLOBALS[$arParams['FILTER_NAME']]);
}
$arNavParams = array(
	'nPageSize' => $arParams['COUNT'],
	'bDescPageNumbering' => $arParams['PAGER_DESC_NUMBERING'],
	'bShowAll' => $arParams['PAGER_SHOW_ALWAYS'],
);
$arNavigation = CDBResult::GetNavParams($arNavParams);
if (strlen($arNavigation['SESS_PAGEN'].$arNavigation['SESS_ALL'])) {
	unset($_SESSION[$arNavigation['SESS_PAGEN']], $_SESSION[$arNavigation['SESS_ALL']]);
}
if($arNavigation["PAGEN"]==0 && $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]>0)
	$arParams["CACHE_TIME"] = $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"];

$arNavParams = array();
if ($arNavigation['PAGEN']>=1) {
	$arNavParams['iNumPage'] = $arNavigation['PAGEN'];
}
if ($arNavigation['SIZEN']>=1) {
	$arNavParams['nPageSize'] = $arNavigation['SIZEN'];
}
if ($arNavigation['SHOW_ALL']) {
	$arNavParams['bShowAll'] = true;
}

$arInterface = $GLOBALS['WD_REVIEWS2_INTERFACE_'.$InterfaceID];
if (!is_array($arInterface) || empty($arInterface)) {
	$resInterface = CWD_Reviews2_Interface::GetByID($InterfaceID);
	if ($arInterface = $resInterface->GetNext()) {
		$GLOBALS['WD_REVIEWS2_INTERFACE_'.$InterfaceID] = $arInterface;
	}
}

$LastVotingDate = CWD_Reviews2_Reviews::GetTargetLastVotingDate($arParams['TARGET']);
$arCacheID = array($arParams, $USER->GetGroups(), $arSorter, $arFilter, $arNavParams, $LastVotingDate, $arInterface);
if($this->StartResultCache(false, $arCacheID)) {
	$resInterface = CWD_Reviews2_Interface::GetByID($InterfaceID);
	if ($arResult['INTERFACE'] = $resInterface->GetNext()) {
		$arResult['INTERFACE_ID'] = $InterfaceID;
		// Get data
		$arResult['TYPES'] = WDR2_GetFieldTypes();
		$arResult['FIELDS'] = CWD_Reviews2_Reviews::ReviewGetFields(false, $InterfaceID, true);
		$arResult['RATINGS'] = CWD_Reviews2_Reviews::ReviewGetRatings(false, $InterfaceID, true);
		$arResult['MAX_RATING'] = $arResult['INTERFACE']['RATING_STARS_COUNT'];
		// Get items
		$arResult['ITEMS'] = array();
		$resReviews = CWD_Reviews2_Reviews::GetList($arSorter,$arFilter,false,$arNavParams);
		$arUsers = array();
		while ($arReview = $resReviews->GetNext()) {
			$arReview['DISPLAY_DATE'] = FormatDate($arParams['DATE_FORMAT'], MakeTimestamp($arReview['DATE_CREATED'],FORMAT_DATETIME));
			$arReview['DISPLAY_DATE_ANSWER'] = FormatDate($arParams['DATE_FORMAT'], MakeTimestamp($arReview['DATE_ANSWER'],FORMAT_DATETIME));
			// Prepare fields
			$arReview['DATA_FIELDS'] = unserialize($arReview['~DATA_FIELDS']);
			$arDataFields = array();
			foreach($arReview['DATA_FIELDS'] as $Key => $Value) {
				if(!is_array($arResult['FIELDS'][$Key]) || $arResult['FIELDS'][$Key]['HIDDEN']=='Y'){continue;}
				$arDataFields[$Key] = array(
					'NAME' => $arResult['FIELDS'][$Key]['NAME'],
					'CODE' => $arResult['FIELDS'][$Key]['CODE'],
					'SORT' => $arResult['FIELDS'][$Key]['SORT'],
					'DESCRIPTION' => $arResult['FIELDS'][$Key]['DESCRIPTION'],
					'REQUIRED' => $arResult['FIELDS'][$Key]['REQUIRED'],
					'TYPE' => $arResult['FIELDS'][$Key]['TYPE'],
					'VALUE' => $Value,
					'DISPLAY_VALUE' => $Value,
					'PARAMS' => $arResult['FIELDS'][$Key]['PARAMS'],
				);
				if (is_array($arResult['TYPES'][$arResult['FIELDS'][$Key]['TYPE']])) {
					$ClassName = $arResult['TYPES'][$arResult['FIELDS'][$Key]['TYPE']]['CLASS'];
					if (method_exists($ClassName,'GetDisplayValue')) {
						$arDataFields[$Key]['DISPLAY_VALUE'] = $ClassName::GetDisplayValue($Value, $arResult['FIELDS'][$Key]);
					}
				}
			}
			$arReview['FIELDS'] = $arDataFields;
			// Prepare ratings
			$arReview['DATA_RATINGS'] = unserialize($arReview['~DATA_RATINGS']);
			$arDataRatings = array();
			$intRatingCountReal = 0;
			$intRatingValueReal = 0;
			$intRatingCountFull = 0;
			$intRatingValueFull = 0;
			if (is_array($arReview['DATA_RATINGS'])) {
				foreach($arReview['DATA_RATINGS'] as $Key => $Value) {
					if(!is_array($arResult['RATINGS'][$Key])){continue;}
					$arDataRatings[$Key] = array(
						'NAME' => $arResult['RATINGS'][$Key]['NAME'],
						'CODE' => $arResult['RATINGS'][$Key]['CODE'],
						'SORT' => $arResult['RATINGS'][$Key]['SORT'],
						'DESCRIPTION' => $arResult['RATINGS'][$Key]['DESCRIPTION'],
						'PARTICIPATES' => $arResult['RATINGS'][$Key]['PARTICIPATES'],
						'VALUE' => $Value,
					);
					if ($arResult['RATINGS'][$Key]['PARTICIPATES']=='Y') {
						$intRatingCountReal++;
						$intRatingValueReal += $Value;
					}
					$intRatingCountFull++;
					$intRatingValueFull += $Value;
				}
			}
			$arReview['RATINGS'] = $arDataRatings;
			$arReview['RATING_RESULT_REAL'] = $intRatingCountReal==0 ? 0 : round($intRatingValueReal/$intRatingCountReal,1);
			$arReview['RATING_RESULT_FULL'] = $intRatingCountFull==0 ? 0 : round($intRatingValueFull/$intRatingCountFull,1);
			// ToolBar buttons
			$bShowContextButtons = $USER->IsAdmin();
			if ($bShowContextButtons) {
				$arReview['EDIT_LINK'] = "/bitrix/admin/wd_reviews2_edit.php?bxpublic=Y&interface={$InterfaceID}&ID={$arReview['ID']}&lang={$Lang}";
				$arReview['EDIT_NAME'] = GetMessage('WD_REVIEWS2_EDIT_LINK');
				$arReview['DELETE_LINK'] = "/bitrix/tools/wd_reviews2.php?action=delete&interface={$InterfaceID}&target={$arParams['TARGET']}&review={$arReview['ID']}&wd_back_url=".urlencode($_SERVER['REQUEST_URI']);
				$arReview['DELETE_NAME'] = GetMessage('WD_REVIEWS2_DELETE_LINK');
				$arReview['DELETE_TITLE'] = GetMessage('WD_REVIEWS2_DELETE_TITLE');
			}
			// Get users
			if ($arReview['USER_ID']>0) {$arUsers[] = $arReview['USER_ID'];}
			if ($arReview['ANSWER_USER_ID']>0) {$arUsers[] = $arReview['ANSWER_USER_ID'];}
			// Insert to result
			$arResult['ITEMS'][] = $arReview;
		}
		// Add button on context panel
		if ($bShowContextButtons) {
			$Action = $APPLICATION->GetPopupLink(
				array(
					"URL" => "/bitrix/admin/wd_reviews2_edit.php?public_add=Y&bxpublic=Y&interface={$InterfaceID}&target={$arParams['TARGET']}&lang={$Lang}",
					"PARAMS" => array(
						"width" => 700,
						'height' => 400,
						'resize' => false,
					),
				)
			);
			$this->AddIncludeAreaIcon(
				array(
					'URL' => 'javascript:'.$Action,
					'TITLE' => GetMessage('WD_REVIEWS2_ADD_LINK'),
					'ICON' => 'bx-context-toolbar-create-icon',
				)
			);
		}
		// Get users and their photos:
		$arUsers = array_unique($arUsers);
		$arUsersData = array();
		if (!empty($arUsers)) {
			$strUsersID = implode('||',$arUsers);
			$arResult['USERS'] = array();
			$resUsers = CUser::GetList($By='ID',$Order='ASC',array('ID'=>$strUsersID));
			$arUserPhotos = array();
			while ($arUser = $resUsers->GetNext(false,false)) {
				$UserName = $arParams['USER_ANSWER_NAME'];
				foreach($arUser as $Key => $Value) {
					$UserName = str_replace('#'.$Key.'#', $Value, $UserName);
				}
				$arUser['ANSWER_DISPLAY_NAME'] = $UserName;
				$arResult['USERS'][$arUser['ID']] = $arUser;
				if ($arParams["SHOW_AVATARS"] && $arUser['PERSONAL_PHOTO']>0) {
					$arUserPhotos[] = $arUser['PERSONAL_PHOTO'];
				}
			}
			if ($arParams["SHOW_AVATARS"] && !empty($arUserPhotos)) {
				$strUserPhotos = implode(',',$arUserPhotos);
				$resPhotos = CFile::GetList(false,array('@ID'=>$strUserPhotos));
				while ($arPhoto = $resPhotos->GetNext(false,false)) {
					foreach($arResult['USERS'] as $Key => $arUser) {
						if ($arUser['PERSONAL_PHOTO']==$arPhoto['ID']) {
							$arResult['USERS'][$Key]['PHOTO'] = $arPhoto;
							break;
						}
					}
				}
			}
		}
		// Nav
		$arResult["NAV_STRING"] = $resReviews->GetPageNavStringEx($navComponentObject, $arParams['PAGER_TITLE'], $arParams['PAGER_TEMPLATE'], $arParams['PAGER_SHOW_ALWAYS']=='Y');
		$arResult["NAV_RESULT"] = $resReviews;
		$arResult["NAV_CACHED_DATA"] = $navComponentObject->GetTemplateCachedData();
		$this->SetResultCacheKeys(array('INTERFACE_ID', 'INTERFACE', 'FIELDS', 'RATINGS'));
		// AutoLoading
		$arResult['AUTO_LOADING_1'] = '';
		$arResult['AUTO_LOADING_2'] = '';
		if ($arParams["AUTO_LOADING"]) {
			$CompID = 'wd_reviews2_list_'.$this->randString();
			$arResult['AUTO_LOADING_1'] = '<div id="'.$CompID.'">';
		}
		if ($this->initComponentTemplate()) {
			// Manual CSS include
			if ($arParams['MANUAL_CSS_INCLUDE']=='Y') {
				$CssFilename = $this->__template->__folder.'/style.css';
				$CssFilename .= '?'.filemtime($_SERVER['DOCUMENT_ROOT'].$CssFilename);
				print '<link rel="stylesheet" type="text/css" href="'.$CssFilename.'" />';
			}
			// Auto loading
			if ($arParams["AUTO_LOADING"]) {
				$arResult['AUTO_LOADING_2'] = '</div>';
				$arResult['AUTO_LOADING_2'] .= '<script type="text/javascript">';
				$strParams = http_build_query($arParamsOriginal);
				$strPath1 = urlencode($_SERVER['REQUEST_URI']);
				$strPath2 = urlencode($_SERVER['SCRIPT_NAME']);
				$NavNum = $arResult["NAV_RESULT"]->NavNum;
				$PageNum = $arResult["NAV_RESULT"]->NavPageNomer;
				$arResult['AUTO_LOADING_2'] .= '$("#'.$CompID.'").load("/bitrix/tools/wd_reviews2.php?template='.$this->__template->__name.'&interface='.$arParams['INTERFACE_ID'].'&action=list&'.$strParams.'&path1='.$strPath1.'&path2='.$strPath2.'&PAGEN_'.$NavNum.'='.$PageNum.'")';
				$arResult['AUTO_LOADING_2'] .= '</script>';
			}
		}
		$this->IncludeComponentTemplate();
	} else {
		$this->AbortResultCache();
		CWD_Reviews2::ShowError(GetMessage('WDR2_ERROR_INTERFACE_NOT_FOUND'));
	}
}

?>
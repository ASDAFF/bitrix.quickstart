<?
/** @var array $arCurrentValues */

use \Bitrix\Main\Loader;
use \Bitrix\Main\Localization\Loc;

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

Loc::loadMessages(__FILE__);

if(!Loader::includeModule('api.reviews')) {
	ShowError(Loc::getMessage('API_REVIEWS_MODULE_ERROR'));
	return;
}

use \Api\Reviews\Tools;

//==============================================================================
//                               REVIEWS_FORM
//==============================================================================
$arBaseFields  = (array)Loc::getMessage('FORM_BASE_FIELDS');
$arDopFields   = (array)Loc::getMessage('FORM_DOP_FIELDS');
$arGuestFields = (array)Loc::getMessage('FORM_GUEST_FIELDS');


$arDelivery = array();
if(Loader::includeModule('sale')) {
	$dbRes = CSaleDelivery::GetList(
		 array('SORT' => 'ASC', 'NAME' => 'ASC'),
		 array('ACTIVE' => 'Y', 'SITE_ID' => SITE_ID),
		 false,
		 false,
		 array('ID', 'NAME', 'DESCRIPTION')
	);
	while($delivery = $dbRes->Fetch()) {
		$arDelivery[ $delivery['ID'] ] = '[' . $delivery['ID'] . ']' . $delivery['NAME'];
	}
	unset($dbRes, $delivery);

	$dbRes = CSaleDeliveryHandler::GetList(
		 array('SORT' => 'ASC', 'NAME' => 'ASC'),
		 array('ACTIVE' => 'Y', 'SITE_ID' => SITE_ID)
	);
	while($delivery = $dbRes->Fetch()) {
		$arDelivery[ $delivery['ID'] ] = '[' . $delivery['ID'] . ']' . $delivery['NAME'];
	}
	unset($dbRes, $delivery);
}
else {
	unset($arDopFields['DELIVERY'], $arBaseFields['ORDER_ID']);
}

$arFileType     = array();
$fileTypeValues = Loc::getMessage('UPLOAD_FILE_TYPE_VALUES');
foreach($fileTypeValues as $ext => $title) {
	$arFileType[ $ext ] = $title . ($ext ? ' (' . $ext . ')' : '');
}

$arSort  = Loc::getMessage('LIST_SORT');
$arOrder = Loc::getMessage('LIST_ORDER');


$countDelivery = count($arDelivery);

//---------- Группы параметров стандартные ----------//
//BASE                  (сортировка 100). Основные параметры.
//DATA_SOURCE           (сортировка 200). Тип и ID инфоблока.
//VISUAL                (сортировка 300). Внешний вид.
//URL_TEMPLATES         (сортировка 400). Шаблоны ссылок
//SEF_MODE              (сортировка 500). ЧПУ.
//AJAX_SETTINGS         (сортировка 550). AJAX.
//CACHE_SETTINGS        (сортировка 600). Кэширование.
//ADDITIONAL_SETTINGS   (сортировка 700). Доп. настройки.
//COMPOSITE_SETTINGS    (сортировка 800). Композитный сайт


$arComponentParameters['GROUPS'] = array(
	 'REVIEWS_FILTER'    => array(
			'NAME' => Loc::getMessage('REVIEWS_FILTER'),
			'SORT' => 295,
	 ),
	 'FILES_SETTINGS'    => array(
			'NAME' => Loc::getMessage('FILES_SETTINGS'),
			'SORT' => 296,
	 ),
	 'VIDEOS_SETTINGS'   => array(
			'NAME' => Loc::getMessage('VIDEOS_SETTINGS'),
			'SORT' => 297,
	 ),
	 'REVIEWS_FORM'      => array(
			'NAME' => Loc::getMessage('REVIEWS_FORM'),
			'SORT' => 300,
	 ),
	 'REVIEWS_LIST'      => array(
			'NAME' => Loc::getMessage('REVIEWS_LIST'),
			'SORT' => 310,
	 ),
	 'REVIEWS_DETAIL'    => array(
			'NAME' => Loc::getMessage('REVIEWS_DETAIL'),
			'SORT' => 315,
	 ),
	 'REVIEWS_USER'      => array(
			'NAME' => Loc::getMessage('REVIEWS_USER'),
			'SORT' => 316,
	 ),
	 'REVIEWS_STAT'      => array(
			'NAME' => Loc::getMessage('REVIEWS_STAT'),
			'SORT' => 320,
	 ),
	 'REVIEWS_SUBSCRIBE' => array(
			'NAME' => Loc::getMessage('REVIEWS_SUBSCRIBE'),
			'SORT' => 330,
	 ),
	 'REVIEWS_BASE_MESS' => array(
			'NAME' => Loc::getMessage('REVIEWS_BASE_MESS'),
			'SORT' => 340,
	 ),
);

$arComponentParameters['PARAMETERS'] = array(
	 'VARIABLE_ALIASES' => Array(
			'review_id' => Array(
				 'NAME' => Loc::getMessage('ARP_VARIABLE_ALIASES_REVIEW_ID'),
			),
			'user_id'   => Array(
				 'NAME' => Loc::getMessage('ARP_VARIABLE_ALIASES_USER_ID'),
			),
	 ),
	 'SEF_MODE'         => Array(
			'list'   => array(
				 'NAME'      => Loc::getMessage('ARP_SEF_MODE_LIST'),
				 'DEFAULT'   => '',
				 'VARIABLES' => array(),
			),
			'detail' => array(
				 'NAME'      => Loc::getMessage('ARP_SEF_MODE_DETAIL'),
				 'DEFAULT'   => 'review#review_id#/',
				 'VARIABLES' => array('review_id'),
			),
			'user'   => array(
				 'NAME'      => Loc::getMessage('ARP_SEF_MODE_USER'),
				 'DEFAULT'   => 'user#user_id#/',
				 'VARIABLES' => array('user_id'),
			),
			'search' => array(
				 'NAME'      => Loc::getMessage('ARP_SEF_MODE_SEARCH'),
				 'DEFAULT'   => 'search/',
				 'VARIABLES' => array(),
			),
			'rss'    => array(
				 'NAME'      => Loc::getMessage('ARP_SEF_MODE_RSS'),
				 'DEFAULT'   => 'rss/',
				 'VARIABLES' => array(),
			),
	 ),


	 //GENERAL
	 'CACHE_TIME'       => Array('DEFAULT' => 86400 * 365),
	 'INCLUDE_CSS'      => array(
			'PARENT'  => 'BASE',
			'NAME'    => Loc::getMessage('INCLUDE_CSS'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'Y',
	 ),
	 'INCLUDE_JQUERY'   => array(
			'PARENT'  => 'BASE',
			'NAME'    => Loc::getMessage('INCLUDE_JQUERY'),
			'TYPE'    => 'LIST',
			'DEFAULT' => 'jquery2',
			'VALUES'  => Loc::getMessage('INCLUDE_JQUERY_VALUES'),
	 ),
	 'EMAIL_TO'         => Array(
			'PARENT'  => 'BASE',
			'NAME'    => Loc::getMessage('EMAIL_TO'),
			'TYPE'    => 'STRING',
			'DEFAULT' => '',
	 ),
	 'SHOP_NAME'        => Array(
			'PARENT'  => 'BASE',
			'NAME'    => Loc::getMessage('SHOP_NAME'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('SHOP_NAME_DEFAULT'),
			'REFRESH' => 'Y',
	 ),

	 'IBLOCK_ID'            => array(
			'PARENT'  => 'REVIEWS_FILTER',
			'NAME'    => Loc::getMessage('IBLOCK_ID'),
			'TYPE'    => 'STRING',
			'DEFAULT' => '', //={$_REQUEST["IBLOCK_ID"]}
	 ),
	 'SECTION_ID'           => array(
			'PARENT'  => 'REVIEWS_FILTER',
			'NAME'    => Loc::getMessage('SECTION_ID'),
			'TYPE'    => 'STRING',
			'DEFAULT' => '', //={$_REQUEST["SECTION_ID"]}
	 ),
	 'ELEMENT_ID'           => array(
			'PARENT'  => 'REVIEWS_FILTER',
			'NAME'    => Loc::getMessage('ELEMENT_ID'),
			'TYPE'    => 'STRING',
			'DEFAULT' => '', //={$_REQUEST["ELEMENT_ID"]}
	 ),
	 /*'ORDER_ID'             => array(
			'PARENT'  => 'REVIEWS_FILTER',
			'NAME'    => Loc::getMessage('ORDER_ID'),
			'TYPE'    => 'STRING',
			'DEFAULT' => '', //={$_REQUEST["ORDER_ID"]}
	 ),*/
	 'URL'                  => array(
			'PARENT'  => 'REVIEWS_FILTER',
			'NAME'    => Loc::getMessage('URL'),
			'TYPE'    => 'STRING',
			'DEFAULT' => '',
	 ),

	 //FILES_SETTINGS
	 'UPLOAD_FILE_TYPE'     => array(
			'NAME'     => Loc::getMessage('UPLOAD_FILE_TYPE'),
			'TYPE'     => 'CUSTOM',
			'JS_FILE'  => '/bitrix/components/api/reviews/settings/settings.js',
			'JS_EVENT' => 'OnApiReviewsSettingsEdit',
			'JS_DATA'  => $arFileType,
			'DEFAULT'  => '',
			'PARENT'   => 'FILES_SETTINGS',
	 ),
	 'UPLOAD_FILE_SIZE'     => array(
			'NAME'    => Loc::getMessage('UPLOAD_FILE_SIZE'),
			'TYPE'    => 'STRING',
			'DEFAULT' => '10M',
			'COLS'    => 42,
			'PARENT'  => 'FILES_SETTINGS',
	 ),
	 'UPLOAD_FILE_LIMIT'    => array(
			'NAME'    => Loc::getMessage('UPLOAD_FILE_LIMIT'),
			'TYPE'    => 'STRING',
			'DEFAULT' => 8,
			'COLS'    => 42,
			'PARENT'  => 'FILES_SETTINGS',
	 ),
	 'UPLOAD_VIDEO_LIMIT'   => array(
			'NAME'    => Loc::getMessage('UPLOAD_VIDEO_LIMIT'),
			'TYPE'    => 'STRING',
			'DEFAULT' => 8,
			'COLS'    => 42,
			'PARENT'  => 'FILES_SETTINGS',
	 ),
	 /*'UPLOAD_FOLDER'        => array(
			'NAME'    => Loc::getMessage('UPLOAD_FOLDER'),
			'TYPE'    => 'STRING',
			'DEFAULT' => '/upload/api_reviews',
			'COLS'    => 42,
			'PARENT'  => 'FILES_SETTINGS',
	 ),*/
	 'THUMBNAIL_WIDTH'      => array(
			'NAME'    => Loc::getMessage('THUMBNAIL_WIDTH'),
			'TYPE'    => 'STRING',
			'DEFAULT' => 114,
			'COLS'    => 42,
			'PARENT'  => 'FILES_SETTINGS',
	 ),
	 'THUMBNAIL_HEIGHT'     => array(
			'NAME'    => Loc::getMessage('THUMBNAIL_HEIGHT'),
			'TYPE'    => 'STRING',
			'DEFAULT' => 72,
			'COLS'    => 42,
			'PARENT'  => 'FILES_SETTINGS',
	 ),


	 //VIDEOS_SETTINGS


	 //REVIEWS_FORM
	 'FORM_FORM_TITLE'      => Array(
			'PARENT'  => 'REVIEWS_FORM',
			'NAME'    => Loc::getMessage('FORM_FORM_TITLE'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('FORM_FORM_TITLE_DEFAULT'),
	 ),
	 'FORM_FORM_SUBTITLE'   => Array(
			'PARENT'  => 'REVIEWS_FORM',
			'NAME'    => Loc::getMessage('FORM_FORM_SUBTITLE'),
			'TYPE'    => 'STRING',
			'DEFAULT' => '',
	 ),
	 'FORM_PREMODERATION'   => Array(
			'PARENT'  => 'REVIEWS_FORM',
			'NAME'    => Loc::getMessage('FORM_PREMODERATION'),
			'TYPE'    => 'LIST',
			'VALUES'  => Loc::getMessage('FORM_PREMODERATION_VALUES'),
			'DEFAULT' => 'N',
			'REFRESH' => 'N',
	 ),
	 'FORM_DISPLAY_FIELDS'  => Array(
			'PARENT'   => 'REVIEWS_FORM',
			'NAME'     => Loc::getMessage('FORM_DISPLAY_FIELDS'),
			'TYPE'     => 'LIST',
			'VALUES'   => array_merge($arBaseFields, $arDopFields, $arGuestFields),
			'DEFAULT'  => array('RATING', 'TITLE', 'ADVANTAGE', 'DISADVANTAGE', 'ANNOTATION', 'GUEST_NAME', 'GUEST_EMAIL'),
			'MULTIPLE' => 'Y',
			'SIZE'     => 13,
	 ),
	 'FORM_REQUIRED_FIELDS' => Array(
			'PARENT'   => 'REVIEWS_FORM',
			'NAME'     => Loc::getMessage('FORM_REQUIRED_FIELDS'),
			'TYPE'     => 'LIST',
			'VALUES'   => array_merge($arBaseFields, $arDopFields, $arGuestFields),
			'DEFAULT'  => array('RATING', 'TITLE', 'ANNOTATION'),
			'MULTIPLE' => 'Y',
			'SIZE'     => 13,
	 ),
	 'FORM_DELIVERY'        => Array(
			'PARENT'            => 'REVIEWS_FORM',
			'NAME'              => Loc::getMessage('FORM_DELIVERY'),
			'TYPE'              => 'LIST',
			'VALUES'            => $arDelivery,
			'DEFAULT'           => '',
			'MULTIPLE'          => 'Y',
			'SIZE'              => ($countDelivery < 10 ? $countDelivery + 1 : 10),
			'ADDITIONAL_VALUES' => 'N',
	 ),
	 'FORM_CITY_VIEW'       => array(
			'PARENT'  => 'REVIEWS_FORM',
			'NAME'    => Loc::getMessage('FORM_CITY_VIEW'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'Y',
	 ),
	 /*'FORM_USE_PLACEHOLDER' => array(
			'PARENT'  => 'REVIEWS_FORM',
			'NAME'    => Loc::getMessage('FORM_USE_PLACEHOLDER'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'Y',
	 ),*/
	 'FORM_SHOP_TEXT'       => Array(
			'PARENT'  => 'REVIEWS_FORM',
			'NAME'    => Loc::getMessage('FORM_SHOP_TEXT'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('FORM_SHOP_TEXT_DEFAULT'),
	 ),
	 'FORM_SHOP_BTN_TEXT'   => Array(
			'PARENT'  => 'REVIEWS_FORM',
			'NAME'    => Loc::getMessage('FORM_SHOP_BTN_TEXT'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('FORM_SHOP_BTN_TEXT_DEFAULT'),
	 ),

	 'FORM_RULES_TEXT'                  => Array(
			'PARENT'  => 'REVIEWS_FORM',
			'NAME'    => Loc::getMessage('FORM_RULES_TEXT'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('FORM_RULES_TEXT_DEFAULT'),
	 ),
	 'FORM_RULES_LINK'                  => Array(
			'PARENT'  => 'REVIEWS_FORM',
			'NAME'    => Loc::getMessage('FORM_RULES_LINK'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('FORM_RULES_LINK_DEFAULT'),
	 ),
	 'FORM_MESS_ADD_REVIEW_VIZIBLE'     => Array(
			'PARENT'  => 'REVIEWS_FORM',
			'NAME'    => Loc::getMessage('FORM_MESS_ADD_REVIEW_VIZIBLE'),
			'DEFAULT' => Loc::getMessage('FORM_MESS_ADD_REVIEW_VIZIBLE_DEFAULT'),
			'TYPE'    => 'STRING',
			'ROWS'    => 4,
	 ),
	 'FORM_MESS_ADD_REVIEW_MODERATION'  => Array(
			'PARENT'  => 'REVIEWS_FORM',
			'NAME'    => Loc::getMessage('FORM_MESS_ADD_REVIEW_MODERATION'),
			'DEFAULT' => Loc::getMessage('FORM_MESS_ADD_REVIEW_MODERATION_DEFAULT'),
			'TYPE'    => 'STRING',
			'ROWS'    => 4,
	 ),
	 'FORM_MESS_ADD_REVIEW_ERROR'       => Array(
			'PARENT'  => 'REVIEWS_FORM',
			'NAME'    => Loc::getMessage('FORM_MESS_ADD_REVIEW_ERROR'),
			'DEFAULT' => Loc::getMessage('FORM_MESS_ADD_REVIEW_ERROR_DEFAULT'),
			'TYPE'    => 'STRING',
			'ROWS'    => 4,
	 ),
	 'FORM_MESS_STAR_RATING_1'          => Array(
			'PARENT'  => 'REVIEWS_FORM',
			'NAME'    => Loc::getMessage('FORM_MESS_STAR_RATING_1'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('FORM_MESS_STAR_RATING_1_DEFAULT'),
	 ),
	 'FORM_MESS_STAR_RATING_2'          => Array(
			'PARENT'  => 'REVIEWS_FORM',
			'NAME'    => Loc::getMessage('FORM_MESS_STAR_RATING_2'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('FORM_MESS_STAR_RATING_2_DEFAULT'),
	 ),
	 'FORM_MESS_STAR_RATING_3'          => Array(
			'PARENT'  => 'REVIEWS_FORM',
			'NAME'    => Loc::getMessage('FORM_MESS_STAR_RATING_3'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('FORM_MESS_STAR_RATING_3_DEFAULT'),
	 ),
	 'FORM_MESS_STAR_RATING_4'          => Array(
			'PARENT'  => 'REVIEWS_FORM',
			'NAME'    => Loc::getMessage('FORM_MESS_STAR_RATING_4'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('FORM_MESS_STAR_RATING_4_DEFAULT'),
	 ),
	 'FORM_MESS_STAR_RATING_5'          => Array(
			'PARENT'  => 'REVIEWS_FORM',
			'NAME'    => Loc::getMessage('FORM_MESS_STAR_RATING_5'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('FORM_MESS_STAR_RATING_5_DEFAULT'),
	 ),
	 'FORM_MESS_ADD_REVIEW_EVENT_THEME' => Array(
			'PARENT'  => 'REVIEWS_FORM',
			'NAME'    => Loc::getMessage('FORM_MESS_ADD_REVIEW_EVENT_THEME'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('FORM_MESS_ADD_REVIEW_EVENT_THEME_DEFAULT'),
	 ),
	 'FORM_MESS_ADD_REVIEW_EVENT_TEXT'  => Array(
			'PARENT'  => 'REVIEWS_FORM',
			'NAME'    => Loc::getMessage('FORM_MESS_ADD_REVIEW_EVENT_TEXT'),
			'TYPE'    => 'STRING',
			'ROWS'    => 4,
			'DEFAULT' => Loc::getMessage('FORM_MESS_ADD_REVIEW_EVENT_TEXT_DEFAULT'),
	 ),

	 'FORM_USE_EULA'          => Array(
			'PARENT'  => 'REVIEWS_FORM',
			'NAME'    => Loc::getMessage('FORM_USE_EULA'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'Y',
	 ),
	 'FORM_MESS_EULA'         => Array(
			'PARENT'  => 'REVIEWS_FORM',
			'NAME'    => Loc::getMessage('FORM_MESS_EULA'),
			'TYPE'    => 'STRING',
			'ROWS'    => 4,
			'DEFAULT' => Loc::getMessage('FORM_MESS_EULA_DEFAULT'),
	 ),
	 'FORM_MESS_EULA_CONFIRM' => Array(
			'PARENT'  => 'REVIEWS_FORM',
			'NAME'    => Loc::getMessage('FORM_MESS_EULA_CONFIRM'),
			'TYPE'    => 'STRING',
			'ROWS'    => 4,
			'DEFAULT' => Loc::getMessage('FORM_MESS_EULA_CONFIRM_DEFAULT'),
	 ),

	 'FORM_USE_PRIVACY'          => Array(
			'PARENT'  => 'REVIEWS_FORM',
			'NAME'    => Loc::getMessage('FORM_USE_PRIVACY'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'Y',
	 ),
	 'FORM_MESS_PRIVACY'         => Array(
			'PARENT'  => 'REVIEWS_FORM',
			'NAME'    => Loc::getMessage('FORM_MESS_PRIVACY'),
			'TYPE'    => 'STRING',
			'ROWS'    => 4,
			'DEFAULT' => Loc::getMessage('FORM_MESS_PRIVACY_DEFAULT'),
	 ),
	 'FORM_MESS_PRIVACY_LINK'    => Array(
			'PARENT'  => 'REVIEWS_FORM',
			'NAME'    => Loc::getMessage('FORM_MESS_PRIVACY_LINK'),
			'TYPE'    => 'STRING',
			'DEFAULT' => '',
	 ),
	 'FORM_MESS_PRIVACY_CONFIRM' => Array(
			'PARENT'  => 'REVIEWS_FORM',
			'NAME'    => Loc::getMessage('FORM_MESS_PRIVACY_CONFIRM'),
			'TYPE'    => 'STRING',
			'ROWS'    => 4,
			'DEFAULT' => Loc::getMessage('FORM_MESS_PRIVACY_CONFIRM_DEFAULT'),
	 ),



	 'USE_FORM_MESS_FIELD_PLACEHOLDER' => Array(
			'PARENT'  => 'REVIEWS_FORM',
			'NAME'    => Loc::getMessage('USE_FORM_MESS_FIELD_PLACEHOLDER'),
			'TYPE'    => 'CHECKBOX',
			'REFRESH' => 'Y',
			'DEFAULT' => 'N',
	 ),

	 //REVIEWS_LIST
	 'LIST_SET_TITLE'                  => Array(
			'PARENT'  => 'REVIEWS_LIST',
			'NAME'    => Loc::getMessage('LIST_SET_TITLE'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'N',
	 ),
	 'LIST_SHOW_THUMBS'                => Array(
			'PARENT'  => 'REVIEWS_LIST',
			'NAME'    => Loc::getMessage('LIST_SHOW_THUMBS'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'Y',
	 ),
	 'LIST_SHOP_NAME_REPLY'            => Array(
			'PARENT'  => 'REVIEWS_LIST',
			'NAME'    => Loc::getMessage('LIST_SHOP_NAME_REPLY'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('LIST_SHOP_NAME_REPLY_DEFAULT'),
	 ),
	 'LIST_ITEMS_LIMIT'                => Array(
			'PARENT'  => 'REVIEWS_LIST',
			'NAME'    => Loc::getMessage('LIST_ITEMS_LIMIT'),
			'TYPE'    => 'STRING',
			'DEFAULT' => 10,
	 ),

	 'LIST_SORT_FIELD_1'                => Array(
			'PARENT'            => 'REVIEWS_LIST',
			'NAME'              => Loc::getMessage('LIST_SORT_FIELD_1'),
			'TYPE'              => 'LIST',
			'DEFAULT'           => 'ACTIVE_FROM',
			'VALUES'            => $arSort,
			'ADDITIONAL_VALUES' => 'Y',
	 ),
	 'LIST_SORT_ORDER_1'                => Array(
			'PARENT'            => 'REVIEWS_LIST',
			'NAME'              => Loc::getMessage('LIST_SORT_ORDER_1'),
			'TYPE'              => 'LIST',
			'DEFAULT'           => 'DESC',
			'VALUES'            => $arOrder,
			'ADDITIONAL_VALUES' => 'Y',
	 ),
	 'LIST_SORT_FIELD_2'                => Array(
			'PARENT'            => 'REVIEWS_LIST',
			'NAME'              => Loc::getMessage('LIST_SORT_FIELD_2'),
			'TYPE'              => 'LIST',
			'DEFAULT'           => 'DATE_CREATE',
			'VALUES'            => $arSort,
			'ADDITIONAL_VALUES' => 'Y',
	 ),
	 'LIST_SORT_ORDER_2'                => Array(
			'PARENT'            => 'REVIEWS_LIST',
			'NAME'              => Loc::getMessage('LIST_SORT_ORDER_2'),
			'TYPE'              => 'LIST',
			'DEFAULT'           => 'DESC',
			'VALUES'            => $arOrder,
			'ADDITIONAL_VALUES' => 'Y',
	 ),
	 'LIST_SORT_FIELD_3'                => Array(
			'PARENT'            => 'REVIEWS_LIST',
			'NAME'              => Loc::getMessage('LIST_SORT_FIELD_3'),
			'TYPE'              => 'LIST',
			'DEFAULT'           => 'ID',
			'VALUES'            => $arSort,
			'ADDITIONAL_VALUES' => 'Y',
	 ),
	 'LIST_SORT_ORDER_3'                => Array(
			'PARENT'            => 'REVIEWS_LIST',
			'NAME'              => Loc::getMessage('LIST_SORT_ORDER_3'),
			'TYPE'              => 'LIST',
			'DEFAULT'           => 'DESC',
			'VALUES'            => $arOrder,
			'ADDITIONAL_VALUES' => 'Y',
	 ),
	 'LIST_SORT_FIELDS'                 => Array(
			'PARENT'            => 'REVIEWS_LIST',
			'NAME'              => Loc::getMessage('LIST_SORT_FIELDS'),
			'TYPE'              => 'LIST',
			'VALUES'            => Loc::getMessage('LIST_SORT_FIELDS_VALUES'),
			'DEFAULT'           => Loc::getMessage('LIST_SORT_FIELDS_DEFAULT'),
			'MULTIPLE'          => 'Y',
			'SIZE'              => 5,
			'ADDITIONAL_VALUES' => 'N',
	 ),
	 'LIST_ACTIVE_DATE_FORMAT'          => Tools::addDateParameters(Loc::getMessage('LIST_ACTIVE_DATE_FORMAT'), 'REVIEWS_LIST'),
	 /*'LIST_ALLOW'                       => Array(
			'PARENT'            => 'REVIEWS_LIST',
			'NAME'              => Loc::getMessage('LIST_ALLOW'),
			'TYPE'              => 'LIST',
			'VALUES'            => Loc::getMessage('LIST_ALLOW_VALUES'),
			'DEFAULT'           => array('ANCHOR'),
			'MULTIPLE'          => 'Y',
			'SIZE'              => count(Loc::getMessage('LIST_ALLOW_VALUES')),
			'ADDITIONAL_VALUES' => 'N',
	 ),*/
	 'PICTURE'                          => array(
			'PARENT'   => 'REVIEWS_LIST',
			'NAME'     => Loc::getMessage('PICTURE'),
			'TYPE'     => 'LIST',
			'DEFAULT'  => "",
			'MULTIPLE' => 'Y',
			'VALUES'   => Loc::getMessage('PICTURE_VALUES'),
	 ),
	 'RESIZE_PICTURE'                   => array(
			'PARENT'  => 'REVIEWS_LIST',
			'NAME'    => Loc::getMessage('RESIZE_PICTURE'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('RESIZE_PICTURE_DEFAULT'),
	 ),
	 'LIST_MESS_ADD_UNSWER_EVENT_THEME' => Array(
			'PARENT'  => 'REVIEWS_LIST',
			'NAME'    => Loc::getMessage('LIST_MESS_ADD_UNSWER_EVENT_THEME'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('LIST_MESS_ADD_UNSWER_EVENT_THEME_DEFAULT'),
	 ),
	 'LIST_MESS_ADD_UNSWER_EVENT_TEXT'  => Array(
			'PARENT'  => 'REVIEWS_LIST',
			'NAME'    => Loc::getMessage('LIST_MESS_ADD_UNSWER_EVENT_TEXT'),
			'TYPE'    => 'STRING',
			'ROWS'    => 4,
			'DEFAULT' => Loc::getMessage('LIST_MESS_ADD_UNSWER_EVENT_TEXT_DEFAULT'),
	 ),
	 'LIST_MESS_TRUE_BUYER'             => Array(
			'PARENT'  => 'REVIEWS_LIST',
			'NAME'    => Loc::getMessage('LIST_MESS_TRUE_BUYER'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('LIST_MESS_TRUE_BUYER_DEFAULT'),
	 ),
	 'LIST_MESS_HELPFUL_REVIEW'         => Array(
			'PARENT'  => 'REVIEWS_LIST',
			'NAME'    => Loc::getMessage('LIST_MESS_HELPFUL_REVIEW'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('LIST_MESS_HELPFUL_REVIEW_DEFAULT'),
	 ),

	 //REVIEWS_DETAIL
	 /*'USE_LIST'                         => Array(
			'PARENT'  => 'REVIEWS_DETAIL',
			'NAME'    => Loc::getMessage('USE_LIST'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'Y',
	 ),*/
	 'DETAIL_HASH'                      => array(
			'PARENT'  => 'REVIEWS_DETAIL',
			'NAME'    => Loc::getMessage('DETAIL_HASH'),
			'DEFAULT' => Loc::getMessage('DETAIL_HASH_DEFAULT'),
			'TYPE'    => 'STRING',
	 ),

	 'USE_USER'                   => array(
			'PARENT'  => 'REVIEWS_USER',
			'NAME'    => Loc::getMessage('USE_USER'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'N',
	 ),


	 //REVIEWS_STAT
	 'USE_STAT'                   => Array(
			'PARENT'  => 'REVIEWS_STAT',
			'NAME'    => Loc::getMessage('USE_STAT'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'Y',
	 ),
	 'STAT_MESS_CUSTOMER_REVIEWS' => array(
			'PARENT'  => 'REVIEWS_STAT',
			'NAME'    => Loc::getMessage('STAT_MESS_CUSTOMER_REVIEWS'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('STAT_MESS_CUSTOMER_REVIEWS_DEFAULT'),
	 ),
	 'STAT_MESS_TOTAL_RATING'     => array(
			'PARENT'  => 'REVIEWS_STAT',
			'NAME'    => Loc::getMessage('STAT_MESS_TOTAL_RATING'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('STAT_MESS_TOTAL_RATING_DEFAULT'),
	 ),
	 'STAT_MESS_CUSTOMER_RATING'  => array(
			'PARENT'  => 'REVIEWS_STAT',
			'NAME'    => Loc::getMessage('STAT_MESS_CUSTOMER_RATING'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('STAT_MESS_CUSTOMER_RATING_DEFAULT'),
	 ),
	 'STAT_MIN_AVERAGE_RATING'    => array(
			'PARENT'  => 'REVIEWS_STAT',
			'NAME'    => Loc::getMessage('STAT_MIN_AVERAGE_RATING'),
			'TYPE'    => 'STRING',
			'DEFAULT' => 5,
	 ),


	 //REVIEWS_SUBSCRIBE
	 'USE_SUBSCRIBE'              => Array(
			'PARENT'  => 'REVIEWS_SUBSCRIBE',
			'NAME'    => Loc::getMessage('USE_SUBSCRIBE'),
			'TYPE'    => 'CHECKBOX',
			'REFRESH' => 'Y',
			'DEFAULT' => 'N',
	 ),

	 //REVIEWS_BASE_MESS
	 'USE_MESS_FIELD_NAME'        => Array(
			'PARENT'  => 'REVIEWS_BASE_MESS',
			'NAME'    => Loc::getMessage('USE_MESS_FIELD_NAME'),
			'TYPE'    => 'CHECKBOX',
			'REFRESH' => 'Y',
			'DEFAULT' => 'N',
	 ),
);


Tools::addPagerParameters(
	 $arComponentParameters,
	 Loc::getMessage('ARP_PAGER_TITLE'), //$pager_title
	 true, //$bDescNumbering
	 false, //$bShowAllParam
	 false, //$bBaseLink
	 false //$bBaseLinkEnabled
//$arCurrentValues["PAGER_BASE_LINK_ENABLE"]==="Y" //$bBaseLinkEnabled
);

Tools::add404Parameters($arComponentParameters, $arCurrentValues);


if($arCurrentValues['USE_MESS_FIELD_NAME'] == 'Y') {
	$arFieldsMess = array_merge($arBaseFields, $arDopFields, $arGuestFields);
	if($arFieldsMess) {
		foreach($arFieldsMess as $key => $val) {
			$arFNameMess = Loc::getMessage('DISPLAY_FIELDS_NAME_MESS');

			$arComponentParameters['PARAMETERS'][ 'MESS_FIELD_NAME_' . $key ] = array(
				 'PARENT' => 'REVIEWS_BASE_MESS',
				 'NAME'   => $arFNameMess[ $key ],
				 'TYPE'   => 'STRING',
			);
		}
	}
}

if($arCurrentValues['USE_FORM_MESS_FIELD_PLACEHOLDER'] == 'Y') {
	$arFieldsMess = array_merge($arBaseFields, $arDopFields, $arGuestFields);
	if($arFieldsMess) {
		foreach($arFieldsMess as $key => $val) {
			$arFNameMess = Loc::getMessage('DISPLAY_FIELDS_NAME_MESS');

			$arComponentParameters['PARAMETERS'][ 'FORM_MESS_FIELD_PLACEHOLDER_' . $key ] = array(
				 'PARENT' => 'REVIEWS_FORM',
				 'NAME'   => $arFNameMess[ $key ],
				 'TYPE'   => 'STRING',
			);
		}
	}
}

if($arCurrentValues['USE_SUBSCRIBE'] == 'Y') {
	$arComponentParameters['PARAMETERS']['SUBSCRIBE_AJAX_URL']               = array(
		 'PARENT'  => 'REVIEWS_SUBSCRIBE',
		 'NAME'    => Loc::getMessage('SUBSCRIBE_AJAX_URL'),
		 'DEFAULT' => Loc::getMessage('SUBSCRIBE_AJAX_URL_DEFAULT'),
		 'TYPE'    => 'STRING',
	);
	$arComponentParameters['PARAMETERS']['MESS_SUBSCRIBE_LINK']              = array(
		 'PARENT'  => 'REVIEWS_SUBSCRIBE',
		 'NAME'    => Loc::getMessage('MESS_SUBSCRIBE_LINK'),
		 'DEFAULT' => Loc::getMessage('MESS_SUBSCRIBE_LINK_DEFAULT'),
		 'TYPE'    => 'STRING',
	);
	$arComponentParameters['PARAMETERS']['MESS_SUBSCRIBE_FIELD_PLACEHOLDER'] = array(
		 'PARENT'  => 'REVIEWS_SUBSCRIBE',
		 'NAME'    => Loc::getMessage('MESS_SUBSCRIBE_FIELD_PLACEHOLDER'),
		 'DEFAULT' => Loc::getMessage('MESS_SUBSCRIBE_FIELD_PLACEHOLDER_DEFAULT'),
		 'TYPE'    => 'STRING',
	);
	$arComponentParameters['PARAMETERS']['MESS_SUBSCRIBE_BUTTON_TEXT']       = array(
		 'PARENT'  => 'REVIEWS_SUBSCRIBE',
		 'NAME'    => Loc::getMessage('MESS_SUBSCRIBE_BUTTON_TEXT'),
		 'DEFAULT' => Loc::getMessage('MESS_SUBSCRIBE_BUTTON_TEXT_DEFAULT'),
		 'TYPE'    => 'STRING',
	);
	$arComponentParameters['PARAMETERS']['MESS_SUBSCRIBE_SUBSCRIBE']         = array(
		 'PARENT'  => 'REVIEWS_SUBSCRIBE',
		 'NAME'    => Loc::getMessage('MESS_SUBSCRIBE_SUBSCRIBE'),
		 'DEFAULT' => Loc::getMessage('MESS_SUBSCRIBE_SUBSCRIBE_DEFAULT'),
		 'TYPE'    => 'STRING',
	);
	$arComponentParameters['PARAMETERS']['MESS_SUBSCRIBE_UNSUBSCRIBE']       = array(
		 'PARENT'  => 'REVIEWS_SUBSCRIBE',
		 'NAME'    => Loc::getMessage('MESS_SUBSCRIBE_UNSUBSCRIBE'),
		 'DEFAULT' => Loc::getMessage('MESS_SUBSCRIBE_UNSUBSCRIBE_DEFAULT'),
		 'TYPE'    => 'STRING',
	);
	$arComponentParameters['PARAMETERS']['MESS_SUBSCRIBE_ERROR']             = array(
		 'PARENT'  => 'REVIEWS_SUBSCRIBE',
		 'NAME'    => Loc::getMessage('MESS_SUBSCRIBE_ERROR'),
		 'DEFAULT' => Loc::getMessage('MESS_SUBSCRIBE_ERROR_DEFAULT'),
		 'TYPE'    => 'STRING',
	);
	$arComponentParameters['PARAMETERS']['MESS_SUBSCRIBE_ERROR_EMAIL']       = array(
		 'PARENT'  => 'REVIEWS_SUBSCRIBE',
		 'NAME'    => Loc::getMessage('MESS_SUBSCRIBE_ERROR_EMAIL'),
		 'DEFAULT' => Loc::getMessage('MESS_SUBSCRIBE_ERROR_EMAIL_DEFAULT'),
		 'TYPE'    => 'STRING',
	);
	$arComponentParameters['PARAMETERS']['MESS_SUBSCRIBE_ERROR_CHECK_EMAIL'] = array(
		 'PARENT'  => 'REVIEWS_SUBSCRIBE',
		 'NAME'    => Loc::getMessage('MESS_SUBSCRIBE_ERROR_CHECK_EMAIL'),
		 'DEFAULT' => Loc::getMessage('MESS_SUBSCRIBE_ERROR_CHECK_EMAIL_DEFAULT'),
		 'TYPE'    => 'STRING',
	);
}
?>
<style type='text/css'>
	.bxcompprop-content-table textarea{
		-webkit-box-sizing: border-box !important; -moz-box-sizing: border-box !important; box-sizing: border-box !important; width: 90% !important; min-height: 60px !important;
	}
</style>

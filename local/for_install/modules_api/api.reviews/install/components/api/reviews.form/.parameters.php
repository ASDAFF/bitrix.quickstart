<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

/**
 * @var array            $arCurrentValues
 * @var CUserTypeManager $USER_FIELD_MANAGER
 */

if(!CModule::IncludeModule('api.reviews'))
{
	ShowError(GetMessage('API_REVIEWS_MODULE_ERROR'));
	return;
}

$arFormFields = array(
	''             => GetMessage('CHOOSE'),
	'TITLE'        => GetMessage('TITLE'),
	'ADVANTAGE'    => GetMessage('ADVANTAGE'),
	'DISADVANTAGE' => GetMessage('DISADVANTAGE'),
	'ANNOTATION'   => GetMessage('ANNOTATION'),
);
$arDopFields  = array(
	'CITY'     => GetMessage('CITY'),
	'DELIVERY' => GetMessage('DELIVERY'),
);

$arGuestFields = GetMessage('FORM_GUEST_FIELDS');

if(CModule::IncludeModule('sale'))
{
	$dbRes = CSaleDelivery::GetList(
		array('SORT' => 'ASC', 'NAME' => 'ASC'),
		array('ACTIVE' => 'Y', 'SITE_ID' => SITE_ID),
		false,
		false,
		array('ID', 'NAME', 'DESCRIPTION')
	);
	while($delivery = $dbRes->Fetch())
	{
		$arDelivery[ $delivery['ID'] ] = '[' . $delivery['ID'] . ']' . $delivery['NAME'];
	}
	unset($dbRes, $delivery);

	$dbRes = CSaleDeliveryHandler::GetList(
		array('SORT' => 'ASC', 'NAME' => 'ASC'),
		array('ACTIVE' => 'Y', 'SITE_ID' => SITE_ID)
	);
	while($delivery = $dbRes->Fetch())
	{
		$arDelivery[ $delivery['ID'] ] = '[' . $delivery['ID'] . ']' . $delivery['NAME'];
	}
	unset($dbRes, $delivery);
}
else
{
	unset($arDopFields['DELIVERY']);
}

$arComponentParameters = array(
	'GROUPS'     => array(
		'RULES'        => array(
			'NAME' => GetMessage('RULES'),
			'SORT' => 310,
		),
		'MESSAGES'     => array(
			'NAME' => GetMessage('MESSAGES'),
			'SORT' => 320,
		),
		'MAIN_MESS'     => array(
			'NAME' => GetMessage('MAIN_MESS'),
			'SORT' => 330,
		),
	),
	'PARAMETERS' => array(

		//BASE
		'EMAIL_TO'             => Array(
			'PARENT'  => 'BASE',
			'NAME'    => GetMessage('EMAIL_TO'),
			'TYPE'    => 'STRING',
			'DEFAULT' => '',
		),
		'SHOP_NAME'             => Array(
			'PARENT'  => 'BASE',
			'NAME'    => GetMessage('SHOP_NAME'),
			'TYPE'    => 'STRING',
			'DEFAULT' => GetMessage('SHOP_NAME_DEFAULT'),
			'REFRESH' => 'Y',
		),
		'SHOP_TEXT'             => Array(
			'PARENT'  => 'BASE',
			'NAME'    => GetMessage('SHOP_TEXT'),
			'TYPE'    => 'STRING',
			'DEFAULT' => GetMessage('SHOP_TEXT_DEFAULT'),
		),
		'SHOP_BTN_TEXT'         => Array(
			'PARENT'  => 'BASE',
			'NAME'    => GetMessage('SHOP_BTN_TEXT'),
			'TYPE'    => 'STRING',
			'DEFAULT' => GetMessage('SHOP_BTN_TEXT_DEFAULT'),
		),
		'FORM_TITLE'            => Array(
			'PARENT'  => 'BASE',
			'NAME'    => GetMessage('FORM_TITLE'),
			'TYPE'    => 'STRING',
			'DEFAULT' => GetMessage('FORM_TITLE_DEFAULT'),
		),
		'PREMODERATION'         => Array(
			'PARENT'  => 'BASE',
			'NAME'    => GetMessage('PREMODERATION'),
			'TYPE'    => 'LIST',
			'VALUES'  => GetMessage('PREMODERATION_VALUES'),
			'DEFAULT' => 'N',
			'REFRESH' => 'N',
		),
		'DISPLAY_FIELDS'        => Array(
			'PARENT'   => 'BASE',
			'NAME'     => GetMessage('DISPLAY_FIELDS'),
			'TYPE'     => 'LIST',
			'VALUES'   => array_merge($arFormFields, $arDopFields, $arGuestFields),
			'DEFAULT'  => array('TITLE', 'ADVANTAGE', 'DISADVANTAGE', 'ANNOTATION'),
			'MULTIPLE' => 'Y',
			'SIZE'     => 9,
		),
		'REQUIRED_FIELDS'       => Array(
			'PARENT'   => 'BASE',
			'NAME'     => GetMessage('REQUIRED_FIELDS'),
			'TYPE'     => 'LIST',
			'VALUES'   => array_merge($arFormFields, $arDopFields, $arGuestFields),
			'DEFAULT'  => array('TITLE'),
			'MULTIPLE' => 'Y',
			'SIZE'     => 9,
		),
		'DELIVERY'              => Array(
			'PARENT'            => 'BASE',
			'NAME'              => GetMessage('DELIVERY'),
			'TYPE'              => 'LIST',
			'VALUES'            => $arDelivery,
			'DEFAULT'           => '',
			'MULTIPLE'          => 'Y',
			'SIZE'              => count($arDelivery) + 1,
			'ADDITIONAL_VALUES' => 'N',
		),
		'CITY_VIEW' => array(
			'PARENT'  => 'BASE',
			'NAME'    => GetMessage('CITY_VIEW'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'Y',
		),
		'USE_PLACEHOLDER' => array(
			'PARENT'  => 'BASE',
			'NAME'    => GetMessage('USE_PLACEHOLDER'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'Y',
		),
		'IBLOCK_ID'                 => array(
			'PARENT'  => 'BASE',
			'NAME'    => GetMessage('IBLOCK_ID'),
			'TYPE'    => 'STRING',
			'DEFAULT' => '', //={$_REQUEST["IBLOCK_ID"]}
		),
		'SECTION_ID'                 => array(
			'PARENT'  => 'BASE',
			'NAME'    => GetMessage('SECTION_ID'),
			'TYPE'    => 'STRING',
			'DEFAULT' => '', //={$_REQUEST["SECTION_ID"]}
		),
		'ELEMENT_ID'                 => array(
			'PARENT'  => 'BASE',
			'NAME'    => GetMessage('ELEMENT_ID'),
			'TYPE'    => 'STRING',
			'DEFAULT' => '', //={$_REQUEST["ELEMENT_ID"]}
		),
		'ORDER_ID'                 => array(
			'PARENT'  => 'BASE',
			'NAME'    => GetMessage('ORDER_ID'),
			'TYPE'    => 'STRING',
			'DEFAULT' => '', //={$_REQUEST["ORDER_ID"]}
		),
		'URL'                        => array(
			'PARENT'  => 'BASE',
			'NAME'    => GetMessage('URL'),
			'TYPE'    => 'STRING',
			'DEFAULT' => '',
		),

		//RULES
		'RULES_TEXT'            => Array(
			'PARENT'  => 'RULES',
			'NAME'    => GetMessage('RULES_TEXT'),
			'TYPE'    => 'STRING',
			'DEFAULT' => GetMessage('RULES_TEXT_DEFAULT'),
		),
		'RULES_LINK'            => Array(
			'PARENT'  => 'RULES',
			'NAME'    => GetMessage('RULES_LINK'),
			'TYPE'    => 'STRING',
			'DEFAULT' => GetMessage('RULES_LINK_DEFAULT'),
		),

		//MESSAGES
		'ADD_REVIEW_VIZIBLE'    => Array(
			'PARENT'  => 'MESSAGES',
			'NAME'    => GetMessage('ADD_REVIEW_VIZIBLE'),
			'TYPE'    => 'STRING',
			'DEFAULT' => GetMessage('ADD_REVIEW_VIZIBLE_DEFAULT'),
		),
		'ADD_REVIEW_HIDDEN'     => Array(
			'PARENT'  => 'MESSAGES',
			'NAME'    => GetMessage('ADD_REVIEW_HIDDEN'),
			'TYPE'    => 'STRING',
			'DEFAULT' => GetMessage('ADD_REVIEW_HIDDEN_DEFAULT'),
		),
		'ADD_REVIEW_MODERATION' => Array(
			'PARENT'  => 'MESSAGES',
			'NAME'    => GetMessage('ADD_REVIEW_MODERATION'),
			'TYPE'    => 'STRING',
			'DEFAULT' => GetMessage('ADD_REVIEW_MODERATION_DEFAULT'),
		),
		'ADD_REVIEW_ERROR'      => Array(
			'PARENT'  => 'MESSAGES',
			'NAME'    => GetMessage('ADD_REVIEW_ERROR'),
			'TYPE'    => 'STRING',
			'DEFAULT' => GetMessage('ADD_REVIEW_ERROR_DEFAULT'),
		),


		'MESS_STAR_RATING_1'      => Array(
			'PARENT'  => 'MAIN_MESS',
			'NAME'    => GetMessage('MESS_STAR_RATING_1'),
			'TYPE'    => 'STRING',
			'DEFAULT' => GetMessage('MESS_STAR_RATING_1_DEFAULT'),
		),
		'MESS_STAR_RATING_2'      => Array(
			'PARENT'  => 'MAIN_MESS',
			'NAME'    => GetMessage('MESS_STAR_RATING_2'),
			'TYPE'    => 'STRING',
			'DEFAULT' => GetMessage('MESS_STAR_RATING_2_DEFAULT'),
		),
		'MESS_STAR_RATING_3'      => Array(
			'PARENT'  => 'MAIN_MESS',
			'NAME'    => GetMessage('MESS_STAR_RATING_3'),
			'TYPE'    => 'STRING',
			'DEFAULT' => GetMessage('MESS_STAR_RATING_3_DEFAULT'),
		),
		'MESS_STAR_RATING_4'      => Array(
			'PARENT'  => 'MAIN_MESS',
			'NAME'    => GetMessage('MESS_STAR_RATING_4'),
			'TYPE'    => 'STRING',
			'DEFAULT' => GetMessage('MESS_STAR_RATING_4_DEFAULT'),
		),
		'MESS_STAR_RATING_5'      => Array(
			'PARENT'  => 'MAIN_MESS',
			'NAME'    => GetMessage('MESS_STAR_RATING_5'),
			'TYPE'    => 'STRING',
			'DEFAULT' => GetMessage('MESS_STAR_RATING_5_DEFAULT'),
		),

		'MESS_ADD_REVIEW_EVENT_THEME'      => Array(
			'PARENT'  => 'MAIN_MESS',
			'NAME'    => GetMessage('MESS_ADD_REVIEW_EVENT_THEME'),
			'TYPE'    => 'STRING',
			'DEFAULT' => GetMessage('MESS_ADD_REVIEW_EVENT_THEME_DEFAULT'),
		),
		'MESS_ADD_REVIEW_EVENT_TEXT'      => Array(
			'PARENT'  => 'MAIN_MESS',
			'NAME'    => GetMessage('MESS_ADD_REVIEW_EVENT_TEXT'),
			'TYPE'    => 'STRING',
			'ROWS'    => 4,
			'DEFAULT' => GetMessage('MESS_ADD_REVIEW_EVENT_TEXT_DEFAULT'),
		),


		//ADDITIONAL_SETTINGS
		'CACHE_TIME' => Array('DEFAULT' => 86400),
		'INCLUDE_CSS' => array(
			'PARENT'  => 'ADDITIONAL_SETTINGS',
			'NAME'    => GetMessage('INCLUDE_CSS'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'Y',
		),
		'INCLUDE_JQUERY' => array(
			'PARENT'  => 'ADDITIONAL_SETTINGS',
			'NAME'    => GetMessage('INCLUDE_JQUERY'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'N',
		),
	),
);
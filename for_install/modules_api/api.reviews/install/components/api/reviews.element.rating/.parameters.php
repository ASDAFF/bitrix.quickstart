<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

/**
 * @var array            $arCurrentValues
 * @var CUserTypeManager $USER_FIELD_MANAGER
 */

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if(!Loader::includeModule('api.reviews')) {
	ShowError(Loc::getMessage('API_REVIEWS_MODULE_ERROR'));
	return;
}

$arComponentParameters = array(
	 'GROUPS'     => array(
			'LANG' => array(
				 'NAME' => Loc::getMessage('ARCP_RER_GROUP_MESS'),
				 'SORT' => 300,
			),
	 ),
	 'PARAMETERS' => array(
			'INCLUDE_CSS' => array(
				 'PARENT'  => 'BASE',
				 'NAME'    => Loc::getMessage('ARCP_RER_INCLUDE_CSS'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'Y',
			),
			'SHOW_PROGRESS_BAR' => array(
				 'PARENT'  => 'BASE',
				 'NAME'    => Loc::getMessage('ARCP_RER_SHOW_PROGRESS_BAR'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'Y',
			),
			'HIDE_BORDER' => array(
				 'PARENT'  => 'BASE',
				 'NAME'    => Loc::getMessage('ARCP_RER_HIDE_BORDER'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'N',
			),
			'REVIEWS_LINK' => array(
				 'PARENT'  => 'BASE',
				 'NAME'    => Loc::getMessage('ARCP_RER_REVIEWS_LINK'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => Loc::getMessage('ARCP_RER_REVIEWS_LINK_DEFAULT'),
			),
			'IBLOCK_ID'   => array(
				 'PARENT'  => 'BASE',
				 'NAME'    => Loc::getMessage('ARCP_RER_IBLOCK_ID'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'REFRESH' => 'Y',
			),
			'SECTION_ID'  => array(
				 'PARENT'  => 'BASE',
				 'NAME'    => Loc::getMessage('ARCP_RER_SECTION_ID'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
			),
			'ELEMENT_ID'  => array(
				 'PARENT'  => 'BASE',
				 'NAME'    => Loc::getMessage('ARCP_RER_ELEMENT_ID'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
			),
			'ORDER_ID'    => array(
				 'PARENT'  => 'BASE',
				 'NAME'    => Loc::getMessage('ARCP_RER_ORDER_ID'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
			),
			'URL'         => array(
				 'PARENT'  => 'BASE',
				 'NAME'    => Loc::getMessage('ARCP_RER_URL'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
			),

			'MESS_FULL_RATING' => array(
				 'PARENT'  => 'LANG',
				 'NAME'    => Loc::getMessage('ARCP_RER_MESS_FULL_RATING'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => Loc::getMessage('ARCP_RER_MESS_FULL_RATING_DEFAULT'),
			),
			'MESS_EMPTY_RATING' => array(
				 'PARENT'  => 'LANG',
				 'NAME'    => Loc::getMessage('ARCP_RER_MESS_EMPTY_RATING'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => Loc::getMessage('ARCP_RER_MESS_EMPTY_RATING_DEFAULT'),
			),
	 ),
);
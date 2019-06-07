<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

/** @var array $arCurrentValues */

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if(!Loader::includeModule('api.feedbackex'))
	return;

$option_set = array('' => Loc::getMessage('API_FEX_PARAMS_OPTION_SET'));
$option_all = array('' => Loc::getMessage('API_FEX_PARAMS_OPTION_ALL'));

$arFields = CApiFeedbackEx::getFields(true, $arCurrentValues['CONFIG_PATH']);

$arDisplayFields  = array_merge($option_all, $arFields);
$arRequiredFields = array_merge($option_set, $arFields);

$arFieldsSize = count($arDisplayFields) < 10 ? count($arDisplayFields) : 10;


$arComponentParameters['GROUPS'] = array(
	 'FORM'                    => array(
			'NAME' => Loc::getMessage('GROUP_FORM'),
			'SORT' => 300,
	 ),
	 'MAIL'                    => array(
			'NAME' => Loc::getMessage('GROUP_MAIL'),
			'SORT' => 301,
	 ),
	 'JQUERY'                  => array(
			'NAME' => Loc::getMessage('GROUP_JQUERY'),
			'SORT' => 500,
	 ),
	 'YM_GOALS_SETTINGS'       => array(
			'NAME' => Loc::getMessage('YM_GOALS_SETTINGS'),
			'SORT' => 540,
	 ),
	 'SERVICE_MACROS_SETTINGS' => array(
			'NAME' => Loc::getMessage('SERVICE_MACROS_SETTINGS'),
			'SORT' => 1010,
	 ),
	 'MODAL_SETTINGS'          => array(
			'NAME' => Loc::getMessage('MODAL_SETTINGS'),
			'SORT' => 1020,
	 ),
	 'EULA'                    => array(
			'NAME' => Loc::getMessage('GROUP_EULA'),
			'SORT' => 1030,
	 ),
	 'PRIVACY'                 => array(
			'NAME' => Loc::getMessage('GROUP_PRIVACY'),
			'SORT' => 1040,
	 ),
);


$arComponentParameters['PARAMETERS'] = array(
	 'DISABLE_SEND_MAIL'    => array(
			'NAME'    => Loc::getMessage('DISABLE_SEND_MAIL'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'N',
			'PARENT'  => 'BASE',
	 ),
	 'DISABLE_CHECK_SESSID' => array(
			'NAME'    => Loc::getMessage('DISABLE_CHECK_SESSID'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'N',
			'PARENT'  => 'BASE',
	 ),
	 'REPLACE_FIELD_FROM'   => array(
			'NAME'    => Loc::getMessage('REPLACE_FIELD_FROM'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'Y',
			'PARENT'  => 'BASE',
	 ),

	 'USE_JQUERY'      => array(
			'NAME'    => Loc::getMessage('INCLUDE_JQUERY'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'N',
			'PARENT'  => 'JQUERY',
	 ),
	 'USE_PLACEHOLDER' => array(
			'NAME'    => Loc::getMessage('INCLUDE_PLACEHOLDER'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'N',
			'PARENT'  => 'JQUERY',
	 ),
	 'USE_AUTOSIZE'    => array(
			'NAME'    => Loc::getMessage('INCLUDE_AUTOSIZE'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'Y',
			'PARENT'  => 'JQUERY',
	 ),
	 'USE_FLATPICKR'   => array(
			'NAME'    => Loc::getMessage('INCLUDE_FLATPICKR'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'Y',
			'PARENT'  => 'JQUERY',
	 ),
	 'USE_SCROLL'      => array(
			'NAME'    => Loc::getMessage('SCROLL_TO_FORM_IF_MESSAGES'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'Y',
			'PARENT'  => 'JQUERY',
	 ),
	 'SCROLL_SPEED'    => array(
			'NAME'    => Loc::getMessage('SCROLL_TO_FORM_SPEED'),
			'TYPE'    => 'STRING',
			'DEFAULT' => 1000,
			'PARENT'  => 'JQUERY',
			'COLS'    => 5,
	 ),
	 'API_FEX_FORM_ID' => Array(
			'NAME'    => Loc::getMessage('UNIQUE_FORM_ID'),
			'TYPE'    => 'STRING',
			'DEFAULT' => 'FORM' . mt_rand(1, 10),
			'PARENT'  => 'BASE',
	 ),
	 'OK_TEXT'         => Array(
			'NAME'    => Loc::getMessage('MFP_OK_MESSAGE'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('MFP_OK_TEXT'),
			'PARENT'  => 'BASE',
			'COLS'    => 47,
			'ROWS'    => 4,
	 ),
	 'OK_TEXT_AFTER'   => Array(
			'NAME'    => Loc::getMessage('OK_TEXT_AFTER'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('OK_TEXT_AFTER_DEFAULT'),
			'PARENT'  => 'BASE',
			'COLS'    => 47,
			'ROWS'    => 4,
	 ),
	 'EMAIL_TO'        => Array(
			'NAME'    => Loc::getMessage('MFP_EMAIL_TO'),
			'TYPE'    => 'STRING',
			'DEFAULT' => '',
			'PARENT'  => 'BASE',
	 ),
	 'BCC'             => Array(
			'NAME'    => Loc::getMessage('MFP_BCC'),
			'TYPE'    => 'STRING',
			'DEFAULT' => '',
			'PARENT'  => 'BASE',
	 ),

	 'CONFIG_PATH'     => array(
		  'PARENT'  => 'FORM',
		  'NAME'    => Loc::getMessage('API_FEX_PARAMS_CONFIG_PATH'),
			'TYPE'    => 'STRING',
			'DEFAULT' => '',
			'REFRESH' => 'Y',
	 ),
	 'DISPLAY_FIELDS'  => array(
			'PARENT'            => 'FORM',
			'NAME'              => Loc::getMessage('API_FEX_PARAMS_DISPLAY_FIELDS'),
			'TYPE'              => 'LIST',
			'MULTIPLE'          => 'Y',
			'VALUES'            => $arDisplayFields,
			'ADDITIONAL_VALUES' => 'N',
			'SIZE'              => $arFieldsSize,
			'DEFAULT'           => array('TITLE', 'EMAIL', 'PHONE', 'MESSAGE'),
	 ),
	 'REQUIRED_FIELDS' => Array(
		  'PARENT'   => 'FORM',
		  'NAME'     => Loc::getMessage('API_FEX_PARAMS_REQUIRED_FIELDS'),
			'TYPE'     => 'LIST',
			'MULTIPLE' => 'Y',
			'VALUES'   => $arRequiredFields,
			'SIZE'     => $arFieldsSize,
			'DEFAULT'  => array('TITLE', 'EMAIL', 'PHONE', 'MESSAGE'),
	 ),

	 'MAIL_SUBJECT_ADMIN'           => Array(
			'NAME'    => Loc::getMessage('MAIL_SUBJECT_ADMIN'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('MAIL_SUBJECT_ADMIN_DEFAULT'),
			'COLS'    => 50,
			'PARENT'  => 'MAIL',
	 ),
	 'MAIL_SUBJECT_USER'            => Array(
			'NAME'    => Loc::getMessage('MAIL_SUBJECT_USER'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('MAIL_SUBJECT_USER_DEFAULT'),
			'COLS'    => 50,
			'PARENT'  => 'MAIL',
	 ),
	 'MAIL_SEND_USER'               => array(
			'NAME'    => Loc::getMessage('MAIL_SEND_USER'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'N',
			'PARENT'  => 'MAIL',
	 ),
	 'WRITE_MESS_DIV_STYLE'         => array(
			'NAME'    => Loc::getMessage('WRITE_MESS_DIV_STYLE'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('WRITE_MESS_DIV_STYLE_DEFAULT'),
			'COLS'    => 50,
			'PARENT'  => 'MAIL',
	 ),
	 'WRITE_MESS_DIV_STYLE_NAME'    => array(
			'NAME'    => Loc::getMessage('WRITE_MESS_DIV_STYLE_NAME'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('WRITE_MESS_DIV_STYLE_NAME_DEFAULT'),
			'COLS'    => 50,
			'PARENT'  => 'MAIL',
	 ),
	 'WRITE_MESS_DIV_STYLE_VALUE'   => array(
			'NAME'    => Loc::getMessage('WRITE_MESS_DIV_STYLE_VALUE'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('WRITE_MESS_DIV_STYLE_VALUE_DEFAULT'),
			'COLS'    => 50,
			'PARENT'  => 'MAIL',
	 ),
	 'WRITE_MESS_FILDES_TABLE'      => array(
			'NAME'    => Loc::getMessage('WRITE_MESS_FILDES_TABLE'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'N',
			'REFRESH' => 'N',
			'PARENT'  => 'MAIL',
	 ),
	 'WRITE_MESS_TABLE_STYLE'       => array(
			'NAME'    => Loc::getMessage('WRITE_MESS_TABLE_STYLE'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('WRITE_MESS_TABLE_STYLE_DEFAULT'),
			'COLS'    => 50,
			'PARENT'  => 'MAIL',
	 ),
	 'WRITE_MESS_TABLE_STYLE_NAME'  => array(
			'NAME'    => Loc::getMessage('WRITE_MESS_TABLE_STYLE_NAME'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('WRITE_MESS_TABLE_STYLE_NAME_DEFAULT'),
			'COLS'    => 50,
			'PARENT'  => 'MAIL',
	 ),
	 'WRITE_MESS_TABLE_STYLE_VALUE' => array(
			'NAME'    => Loc::getMessage('WRITE_MESS_TABLE_STYLE_VALUE'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('WRITE_MESS_TABLE_STYLE_VALUE_DEFAULT'),
			'COLS'    => 50,
			'PARENT'  => 'MAIL',
	 ),
	 'FORM_WIDTH'                   => array(
			'NAME'    => Loc::getMessage('FORM_WIDTH'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('FORM_WIDTH_DEFAULT'),
			'PARENT'  => 'VISUAL',
	 ),
	 'FORM_CLASS'                   => array(
			'NAME'    => Loc::getMessage('FORM_CLASS'),
			'TYPE'    => 'STRING',
			'DEFAULT' => '',
			'PARENT'  => 'VISUAL',
	 ),
	 'TITLE_DISPLAY'                => array(
			'NAME'    => Loc::getMessage('MFP_FORM_TITLE_DISPLAY'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'N',
			'PARENT'  => 'VISUAL',
	 ),
	 'FORM_TITLE'                   => array(
			'NAME'    => Loc::getMessage('MFP_FORM_TITLE'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('MFP_FORM_TITLE_VALUE'),
			'COLS'    => 50,
			'PARENT'  => 'VISUAL',
	 ),
	 'FORM_TITLE_LEVEL'             => array(
			'NAME'    => Loc::getMessage('MFP_FORM_TITLE_LEVEL'),
			'TYPE'    => 'LIST',
			'VALUES'  => Loc::getMessage('MFP_FORM_TITLE_LEVEL_VALUES'),
			'DEFAULT' => '3',
			'PARENT'  => 'VISUAL',
	 ),
	 'FIELD_ERROR_MESS'             => Array(
			'NAME'    => Loc::getMessage('FIELD_ERROR_MESS'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('FIELD_ERROR_MESS_VALUE'),
			'PARENT'  => 'VISUAL',
	 ),
	 'EMAIL_ERROR_MESS'             => Array(
			'NAME'    => Loc::getMessage('EMAIL_ERROR_MESS'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('EMAIL_ERROR_MESS_VALUE'),
			'PARENT'  => 'VISUAL',
	 ),

	 'FORM_SUBMIT_CLASS' => array(
			'NAME'    => Loc::getMessage('FORM_SUBMIT_CLASS'),
			'TYPE'    => 'STRING',
			'DEFAULT' => 'uk-button uk-width-1-1',
			'COLS'    => 50,
			'PARENT'  => 'VISUAL',
	 ),
	 'FORM_SUBMIT_VALUE' => array(
			'NAME'    => Loc::getMessage('FORM_SUBMIT_VALUE'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('FORM_SUBMIT_VALUE_DEFAULT'),
			'COLS'    => 50,
			'PARENT'  => 'VISUAL',
	 ),
	 'FORM_SUBMIT_STYLE' => array(
			'NAME'    => Loc::getMessage('FORM_SUBMIT_STYLE'),
			'TYPE'    => 'STRING',
			'DEFAULT' => '',
			'COLS'    => 50,
			'PARENT'  => 'VISUAL',
	 ),
	 'HIDE_FIELD_NAME'   => array(
			'NAME'    => Loc::getMessage('HIDE_FIELD_NAME'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => '',
			'PARENT'  => 'VISUAL',
	 ),
	 'HIDE_ASTERISK'     => array(
			'NAME'    => Loc::getMessage('HIDE_ASTERISK'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => '',
			'PARENT'  => 'VISUAL',
	 ),
	 'FORM_AUTOCOMPLETE' => array(
			'NAME'    => Loc::getMessage('FORM_AUTOCOMPLETE'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'Y',
			'PARENT'  => 'VISUAL',
	 ),

	 'FIELD_SIZE'            => array(
			'NAME'              => Loc::getMessage('FIELD_SIZE'),
			'TYPE'              => 'LIST',
			'VALUES'            => Array(
				 'default' => 'default',
				 'small'   => 'small',
				 'large'   => 'large',
			),
			'DEFAULT'           => 'default',
			'ADDITIONAL_VALUES' => 'Y',
			'REFRESH'           => 'N',
			'PARENT'            => 'VISUAL',
	 ),
	 'FIELD_NAME_POSITION'   => array(
			'NAME'              => Loc::getMessage('FIELD_NAME_POSITION'),
			'TYPE'              => 'LIST',
			'VALUES'            => Loc::getMessage('FIELD_NAME_POSITION_VALUES'),
			'DEFAULT'           => 'stacked',
			'ADDITIONAL_VALUES' => 'Y',
			'REFRESH'           => 'N',
			'PARENT'            => 'VISUAL',
	 ),
	 'FORM_LABEL_TEXT_ALIGN' => array(
			'NAME'    => Loc::getMessage('FORM_LABEL_TEXT_ALIGN'),
			'TYPE'    => 'LIST',
			'VALUES'  => Loc::getMessage('FORM_LABEL_TEXT_ALIGN_VALUES'),
			'DEFAULT' => 0,
			'PARENT'  => 'VISUAL',
	 ),
	 'FORM_LABEL_WIDTH'      => array(
			'NAME'    => Loc::getMessage('FORM_LABEL_WIDTH'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('FORM_LABEL_WIDTH_VALUE'),
			'PARENT'  => 'VISUAL',
	 ),
	 'FORM_FIELD_WIDTH'      => array(
			'NAME'    => Loc::getMessage('FORM_FIELD_WIDTH'),
			'TYPE'    => 'STRING',
			'DEFAULT' => Loc::getMessage('FORM_FIELD_WIDTH_VALUE'),
			'PARENT'  => 'VISUAL',
	 ),
	 'FORM_TEXTAREA_ROWS'    => array(
			'NAME'    => Loc::getMessage('FORM_TEXTAREA_ROWS'),
			'TYPE'    => 'STRING',
			'DEFAULT' => 5,
			'PARENT'  => 'VISUAL',
	 ),
	 /*'USE_YM_GOALS'                => Array(
		 'NAME'    => Loc::getMessage('USE_YM_GOALS'),
		 'TYPE'    => 'CHECKBOX',
		 'DEFAULT' => 'N',
		 'REFRESH' => 'Y',
		 'PARENT'  => 'YM_GOALS_SETTINGS',
	 ),*/

	 //SERVICE_MACROS_SETTINGS
	 'PAGE_TITLE'            => Array(
			'NAME'    => Loc::getMessage('PAGE_TITLE'),
			'TYPE'    => 'STRING',
			'DEFAULT' => '',
			'PARENT'  => 'SERVICE_MACROS_SETTINGS',
	 ),
	 'PAGE_URL'              => Array(
			'NAME'    => Loc::getMessage('PAGE_URL'),
			'TYPE'    => 'STRING',
			'DEFAULT' => '',
			'PARENT'  => 'SERVICE_MACROS_SETTINGS',
	 ),
	 'DIR_URL'               => Array(
			'NAME'    => Loc::getMessage('DIR_URL'),
			'TYPE'    => 'STRING',
			'DEFAULT' => '',
			'PARENT'  => 'SERVICE_MACROS_SETTINGS',
	 ),
	 'DATETIME'              => Array(
			'NAME'    => Loc::getMessage('DATETIME'),
			'TYPE'    => 'STRING',
			'DEFAULT' => '',
			'PARENT'  => 'SERVICE_MACROS_SETTINGS',
	 ),

	 //MODAL_SETTINGS
	 'USE_MODAL'             => Array(
			'NAME'    => Loc::getMessage('USE_MODAL'),
			'TYPE'    => 'CHECKBOX',
			'DEFAULT' => 'N',
			'REFRESH' => 'Y',
			'PARENT'  => 'MODAL_SETTINGS',
	 ),

	 //EULA
	 'USE_EULA'              => Array(
			'PARENT'  => 'EULA',
			'NAME'    => Loc::getMessage('USE_EULA'),
			'TYPE'    => 'CHECKBOX',
			'REFRESH' => 'Y',
	 ),

	 //PRIVACY
	 'USE_PRIVACY'           => Array(
			'PARENT'  => 'PRIVACY',
			'NAME'    => Loc::getMessage('USE_PRIVACY'),
			'TYPE'    => 'CHECKBOX',
			'REFRESH' => 'Y',
	 ),
);


if($arCurrentValues['USE_EULA'] == 'Y') {
	$arComponentParameters['PARAMETERS']['MESS_EULA']         = Array(
		 'PARENT'  => 'EULA',
		 'NAME'    => Loc::getMessage('MESS_EULA'),
		 'TYPE'    => 'STRING',
		 'ROWS'    => 4,
		 'DEFAULT' => Loc::getMessage('MESS_EULA_DEFAULT'),
	);
	$arComponentParameters['PARAMETERS']['MESS_EULA_CONFIRM'] = Array(
		 'PARENT'  => 'EULA',
		 'NAME'    => Loc::getMessage('MESS_EULA_CONFIRM'),
		 'TYPE'    => 'STRING',
		 'ROWS'    => 4,
		 'DEFAULT' => Loc::getMessage('MESS_EULA_CONFIRM_DEFAULT'),
	);
}

if($arCurrentValues['USE_PRIVACY'] == 'Y') {
	$arComponentParameters['PARAMETERS']['MESS_PRIVACY']         = Array(
		 'PARENT'  => 'PRIVACY',
		 'NAME'    => Loc::getMessage('MESS_PRIVACY'),
		 'TYPE'    => 'STRING',
		 'ROWS'    => 4,
		 'DEFAULT' => Loc::getMessage('MESS_PRIVACY_DEFAULT'),
	);
	$arComponentParameters['PARAMETERS']['MESS_PRIVACY_LINK']    = Array(
		 'PARENT'  => 'PRIVACY',
		 'NAME'    => Loc::getMessage('MESS_PRIVACY_LINK'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => '',
	);
	$arComponentParameters['PARAMETERS']['MESS_PRIVACY_CONFIRM'] = Array(
		 'PARENT'  => 'PRIVACY',
		 'NAME'    => Loc::getMessage('MESS_PRIVACY_CONFIRM'),
		 'TYPE'    => 'STRING',
		 'ROWS'    => 4,
		 'DEFAULT' => Loc::getMessage('MESS_PRIVACY_CONFIRM_DEFAULT'),
	);
}


//YM_GOALS_SETTINGS
if($arCurrentValues['USE_YM_GOALS'] == 'Y') {
	$arComponentParameters['PARAMETERS']['YM_COUNTER_ID']  = array(
		 'NAME'    => Loc::getMessage('YM_COUNTER_ID'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => '',
		 'COLS'    => 8,
		 'PARENT'  => 'YM_GOALS_SETTINGS',
	);
	$arComponentParameters['PARAMETERS']['YM_TARGET_NAME'] = array(
		 'NAME'    => Loc::getMessage('YM_TARGET_NAME'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => '',
		 'COLS'    => 42,
		 'PARENT'  => 'YM_GOALS_SETTINGS',
	);
}

//MODAL_SETTINGS
if($arCurrentValues['USE_MODAL'] == 'Y') {
	$arComponentParameters['PARAMETERS']['MODAL_ID']             = array(
		 'NAME'    => Loc::getMessage('MODAL_ID'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => '#API_FEX_MODAL_' . $arCurrentValues['API_FEX_FORM_ID'],
		 'COLS'    => 8,
		 'PARENT'  => 'MODAL_SETTINGS',
	);
	$arComponentParameters['PARAMETERS']['MODAL_BTN_TEXT']       = array(
		 'NAME'    => Loc::getMessage('MODAL_BTN_TEXT'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => Loc::getMessage('MODAL_BTN_TEXT_DEFAULT'),
		 'COLS'    => 8,
		 'PARENT'  => 'MODAL_SETTINGS',
	);
	$arComponentParameters['PARAMETERS']['MODAL_BTN_CLASS']      = array(
		 'NAME'    => Loc::getMessage('MODAL_BTN_CLASS'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => Loc::getMessage('MODAL_BTN_CLASS_DEFAULT'),
		 'COLS'    => 8,
		 'PARENT'  => 'MODAL_SETTINGS',
	);
	$arComponentParameters['PARAMETERS']['MODAL_BTN_ID']         = array(
		 'NAME'    => Loc::getMessage('MODAL_BTN_ID'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => Loc::getMessage('MODAL_BTN_ID_DEFAULT'),
		 'COLS'    => 8,
		 'PARENT'  => 'MODAL_SETTINGS',
	);
	$arComponentParameters['PARAMETERS']['MODAL_BTN_SPAN_CLASS'] = array(
		 'NAME'    => Loc::getMessage('MODAL_BTN_SPAN_CLASS'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => Loc::getMessage('MODAL_BTN_SPAN_CLASS_DEFAULT'),
		 'COLS'    => 8,
		 'PARENT'  => 'MODAL_SETTINGS',
	);
	$arComponentParameters['PARAMETERS']['MODAL_HEADER_TEXT']    = array(
		 'NAME'    => Loc::getMessage('MODAL_HEADER_TEXT'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => Loc::getMessage('MODAL_HEADER_TEXT_DEFAULT'),
		 'ROWS'    => 8,
		 'PARENT'  => 'MODAL_SETTINGS',
	);
	$arComponentParameters['PARAMETERS']['MODAL_FOOTER_TEXT']    = array(
		 'NAME'    => Loc::getMessage('MODAL_FOOTER_TEXT'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => '',
		 'ROWS'    => 8,
		 'PARENT'  => 'MODAL_SETTINGS',
	);
}

?>
<style type="text/css">
	.bxcompprop-content-table textarea{
		-webkit-box-sizing: border-box !important; -moz-box-sizing: border-box !important; box-sizing: border-box !important;
		width: 90% !important;
		min-height: 120px !important;
	}
</style>


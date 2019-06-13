<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();
?>
	<style>
		#bx-comp-params-wrap textarea{
			-webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;
			width: 90%;
			min-height: 60px;
		}
		font.errortext{
			color: #000 !important;
			position: absolute;
			top: 45px;
			left: 0;
			right: 0;
			z-index: 1000;
			font-weight: bold;
			font-size: 16px;
			text-align: center;
			padding: 5px 10px;
			border: 1px solid #f0f7fa;
			background-color: #c8de74 !important;
			border-color: #99b061 #b0c76a #c0d671 !important;
			-webkit-box-shadow: inset 0 1px 1px #a4ba63 !important;
			box-shadow: inset 0 1px 1px #a4ba63 !important;
			border-radius: 2px;
		}
	</style>
<?

$exp = CModule::IncludeModuleEx('api.feedback');

if($exp == 2){
	ShowError(GetMessage('API_MFP_MODULE_DEMO'));
	return;
}
if($exp == 3){
	ShowError(GetMessage('API_MFP_MODULE_EXPIRED'));
	return;
}
if(!$exp){
	ShowError(GetMessage('API_MFP_MODULE_ERROR'));
	return;
}


if(!CModule::IncludeModule("iblock")) {
	ShowError(GetMessage('API_MFP_IBLOCK_ERROR'));
	return;
}

$email_from = trim(COption::GetOptionString('main', 'email_from', "info@" . $GLOBALS["SERVER_NAME"]));

$arIBlockType = CIBlockParameters::GetIBlockTypes(Array('-' => GetMessage('NOT_SET')));
$arIBlock     = array();
$rsIBlock     = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE" => "Y"));
while($arr = $rsIBlock->Fetch()) {
	$arIBlock[ $arr["ID"] ] = "[" . $arr["ID"] . "] " . $arr["NAME"];
}

$site     = ($_REQUEST['site'] <> '' ? $_REQUEST['site'] : ($_REQUEST['src_site'] <> '' ? $_REQUEST['src_site'] : false));
$arFilter = Array(
	 'TYPE_ID' => 'API_FEEDBACK',
	 'ACTIVE'  => 'Y',
);
if($site !== false)
	$arFilter['LID'] = $site;

$arEvent = Array();
$dbType  = CEventMessage::GetList($by = 'ID', $order = 'DESC', $arFilter);
while($arType = $dbType->GetNext())
	$arEvent[ $arType['ID'] ] = '[' . $arType['ID'] . '] ' . $arType['SUBJECT'];


$arServerVars = array();
if(is_array($_SERVER) && !empty($_SERVER)) {
	$arExcludeServerVars = array('PATH', 'SystemRoot', 'COMSPEC', 'PATHEXT', 'WINDIR', 'argv', 'argc');
	foreach($_SERVER as $key => $val) {
		if(!in_array($key, $arExcludeServerVars))
			$arServerVars[ $key ] = $key;
	}
}

$arComponentParameters = array(
	 'GROUPS'     => array(
			'EVENT_MESSAGE_SETTINGS'  => array(
				 'NAME' => GetMessage('GROUP_EVENT_MESSAGE_SETTINGS'),
				 'SORT' => 300,
			),
			'ANTISPAM_SETTINGS'       => array(
				 'NAME' => GetMessage('GROUP_ANTISPAM_SETTINGS'),
				 'SORT' => 490,
			),
			'JS_VALIDATE_SETTINGS'    => array(
				 'NAME' => GetMessage('GROUPS_JS_VALIDATE_SETTINGS'),
				 'SORT' => 500,
			),
			'ICHECK_SETTINGS'         => array(
				 'NAME' => GetMessage('GROUP_ICHECK_SETTINGS'),
				 'SORT' => 501,
			),
			'TOOLTIPSTER_SETTINGS'    => array(
				 'NAME' => GetMessage('GROUP_TOOLTIPSTER_SETTINGS'),
				 'SORT' => 502,
			),
			'BRANCH_SETTINGS'         => array(
				 'NAME' => GetMessage('BRANCH_SETTINGS'),
				 'SORT' => 510,
			),
			'FILE_SETTINGS'           => array(
				 'NAME' => GetMessage('FILE_SETTINGS'),
				 'SORT' => 520,
			),
			'UUID_SETTINGS'           => array(
				 'NAME' => GetMessage('UUID_SETTINGS'),
				 'SORT' => 530,
			),
			'YM_GOALS_SETTINGS'       => array(
				 'NAME' => GetMessage('YM_GOALS_SETTINGS'),
				 'SORT' => 540,
			),
			'TITLE'                   => array(
				 'NAME' => GetMessage('MFP_USER_FIELDS_TITLE'),
				 'SORT' => 550,
			),
			'MODAL_SETTINGS'          => array(
				 'NAME' => GetMessage('GROUPS_MODAL_SETTINGS'),
				 'SORT' => 1001,
			),
			'CSS_MODAL_SETTINGS'      => array(
				 'NAME' => GetMessage('GROUPS_CSS_MODAL_SETTINGS'),
				 'SORT' => 1002,
			),
			'SERVICE_MACROS_SETTINGS' => array(
				 'NAME' => GetMessage('SERVICE_MACROS_SETTINGS'),
				 'SORT' => 1010,
			),
			'AGREEMENT_SETTINGS'      => array(
				 'NAME' => GetMessage('AGREEMENT_SETTINGS'),
				 'SORT' => 1020,
			),
			'SERVER_SETTINGS'         => array(
				 'NAME' => GetMessage('SERVER_SETTINGS'),
				 'SORT' => 1030,
			),
	 ),
	 'PARAMETERS' => array(
			'IBLOCK_TYPE'                  => array(
				 'PARENT'  => 'BASE',
				 'NAME'    => GetMessage('IBLOCK_TYPE'),
				 'TYPE'    => 'LIST',
				 'VALUES'  => $arIBlockType,
				 'REFRESH' => 'Y',
			),
			'IBLOCK_ID'                    => array(
				 'PARENT'            => 'BASE',
				 'NAME'              => GetMessage('IBLOCK_ID'),
				 'TYPE'              => 'LIST',
				 'ADDITIONAL_VALUES' => 'Y',
				 'VALUES'            => $arIBlock,
				 'REFRESH'           => 'Y',
			),
			'INSTALL_IBLOCK'               => Array(
				 "NAME"    => GetMessage("INSTALL_IBLOCK"),
				 "TYPE"    => "CHECKBOX",
				 "DEFAULT" => "N",
				 "PARENT"  => "BASE",
				 "REFRESH" => "Y",
			),
			'IBLOCK_ELEMENT_ACTIVE'        => Array(
				 "NAME"    => GetMessage("IBLOCK_ELEMENT_ACTIVE"),
				 "TYPE"    => "CHECKBOX",
				 "DEFAULT" => "N",
				 "PARENT"  => "BASE",
				 "REFRESH" => "N",
			),
			'USE_HIDDEN_PROTECTION'        => Array(
				 'NAME'    => GetMessage('USE_HIDDEN_PROTECTION'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'Y',
				 'PARENT'  => 'ANTISPAM_SETTINGS',
			),
			'USE_CAPTCHA'                  => Array(
				 'NAME'    => GetMessage('MFP_CAPTCHA'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'N',
				 'PARENT'  => 'ANTISPAM_SETTINGS',
			),
			'DISABLE_SEND_MAIL'            => array(
				 'NAME'    => GetMessage('DISABLE_SEND_MAIL'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'N',
				 'PARENT'  => 'BASE',
			),
			'REPLACE_FIELD_FROM'           => array(
				 'NAME'    => GetMessage('REPLACE_FIELD_FROM'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'Y',
				 'PARENT'  => 'BASE',
			),
			'HIDE_FORM_AFTER_SEND'         => array(
				 'NAME'    => GetMessage('HIDE_FORM_AFTER_SEND'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'Y',
				 'PARENT'  => 'BASE',
			),
			'INCLUDE_JQUERY'               => array(
				 'NAME'    => GetMessage('INCLUDE_JQUERY'),
				 'TYPE'    => 'LIST',
				 'DEFAULT' => 'jquery2',
				 'PARENT'  => 'JS_VALIDATE_SETTINGS',
				 'VALUES'  => GetMessage('INCLUDE_JQUERY_VALUES'),
			),
			'INCLUDE_CHOSEN'               => array(
				 'NAME'    => GetMessage('INCLUDE_CHOSEN'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'N',
				 "REFRESH" => "N",
				 'PARENT'  => 'JS_VALIDATE_SETTINGS',
			),
			'INCLUDE_INPUTMASK'            => array(
				 'NAME'    => GetMessage('INCLUDE_INPUTMASK'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'N',
				 "REFRESH" => "N",
				 'PARENT'  => 'JS_VALIDATE_SETTINGS',
			),
			'INCLUDE_PLACEHOLDER'          => array(
				 'NAME'    => GetMessage('INCLUDE_PLACEHOLDER'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'N',
				 'PARENT'  => 'JS_VALIDATE_SETTINGS',
			),
			'INCLUDE_AUTOSIZE'             => array(
				 'NAME'    => GetMessage('INCLUDE_AUTOSIZE'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'N',
				 'PARENT'  => 'JS_VALIDATE_SETTINGS',
			),
			/** @deprecated in v2.4.0 use INCLUDE_ICHECK */
			/*'INCLUDE_FORM_STYLER'         => array(
				'NAME'    => GetMessage('INCLUDE_FORM_STYLER'),
				'TYPE'    => 'CHECKBOX',
				'DEFAULT' => 'N',
				'PARENT'  => 'JS_VALIDATE_SETTINGS',
			),*/
			'SCROLL_TO_FORM_IF_MESSAGES'   => array(
				 'NAME'    => GetMessage('SCROLL_TO_FORM_IF_MESSAGES'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'N',
				 'PARENT'  => 'JS_VALIDATE_SETTINGS',
			),
			'SCROLL_TO_FORM_SPEED'         => array(
				 'NAME'    => GetMessage('SCROLL_TO_FORM_SPEED'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => 1000,
				 'PARENT'  => 'JS_VALIDATE_SETTINGS',
				 'COLS'    => 5,
			),
			'INCLUDE_VALIDATION'           => array(
				 'NAME'    => GetMessage('INCLUDE_VALIDATION'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'N',
				 "REFRESH" => "Y",
				 'PARENT'  => 'JS_VALIDATE_SETTINGS',
			),
			'INCLUDE_ICHECK'               => array(
				 'NAME'    => GetMessage('INCLUDE_ICHECK'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'N',
				 'REFRESH' => 'Y',
				 'PARENT'  => 'ICHECK_SETTINGS',
			),
			'INCLUDE_TOOLTIPSTER'          => array(
				 'NAME'    => GetMessage('INCLUDE_TOOLTIPSTER'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'N',
				 'REFRESH' => 'Y',
				 'PARENT'  => 'TOOLTIPSTER_SETTINGS',
			),
			'UNIQUE_FORM_ID'               => Array(
				 'NAME'    => GetMessage('UNIQUE_FORM_ID'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => 'form' . mt_rand(1, 10),
				 'PARENT'  => 'BASE',
			),
			'OK_TEXT'                      => Array(
				 'NAME'    => GetMessage('MFP_OK_MESSAGE'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => GetMessage('MFP_OK_TEXT'),
				 'PARENT'  => 'BASE',
				 'COLS'    => 47,
				 'ROWS'    => 4,
			),
			'OK_TEXT_AFTER'                => Array(
				 'NAME'    => GetMessage('OK_TEXT_AFTER'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => GetMessage('OK_TEXT_AFTER_DEFAULT'),
				 'PARENT'  => 'BASE',
				 'COLS'    => 47,
				 'ROWS'    => 4,
			),
			'EMAIL_TO'                     => Array(
				 'NAME'    => GetMessage('MFP_EMAIL_TO'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => $email_from,
				 'PARENT'  => 'BASE',
			),
			'BCC'                          => Array(
				 'NAME'    => GetMessage('MFP_BCC'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'PARENT'  => 'BASE',
			),
			'REDIRECT_PAGE'                => Array(
				 'NAME'    => GetMessage('REDIRECT_PAGE'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'PARENT'  => 'BASE',
			),
			'DISPLAY_FIELDS'               => array(
				 'PARENT'            => 'BASE',
				 'NAME'              => GetMessage('MFP_DISPLAY_FIELDS'),
				 'TYPE'              => 'LIST',
				 'MULTIPLE'          => 'Y',
				 'VALUES'            => array(
						'AUTHOR_FIO'             => GetMessage('MFP_AUTHOR_FIO'),
						'AUTHOR_NAME'            => GetMessage('MFP_AUTHOR_NAME'),
						'AUTHOR_LAST_NAME'       => GetMessage('MFP_AUTHOR_LAST_NAME'),
						'AUTHOR_SECOND_NAME'     => GetMessage('MFP_AUTHOR_SECOND_NAME'),
						'AUTHOR_EMAIL'           => GetMessage('MFP_AUTHOR_EMAIL'),
						'AUTHOR_PERSONAL_MOBILE' => GetMessage('MFP_AUTHOR_PERSONAL_MOBILE'),
						'AUTHOR_WORK_COMPANY'    => GetMessage('MFP_AUTHOR_WORK_COMPANY'),
						'AUTHOR_POSITION'        => GetMessage('MFP_AUTHOR_POSITION'),
						'AUTHOR_PROFESSION'      => GetMessage('MFP_AUTHOR_PROFESSION'),
						'AUTHOR_STATE'           => GetMessage('MFP_AUTHOR_STATE'),
						'AUTHOR_CITY'            => GetMessage('MFP_AUTHOR_CITY'),
						'AUTHOR_WORK_CITY'       => GetMessage('MFP_AUTHOR_WORK_CITY'),
						'AUTHOR_STREET'          => GetMessage('MFP_AUTHOR_STREET'),
						'AUTHOR_ADRESS'          => GetMessage('MFP_AUTHOR_ADRESS'),
						'AUTHOR_PERSONAL_PHONE'  => GetMessage('MFP_AUTHOR_PERSONAL_PHONE'),
						'AUTHOR_WORK_PHONE'      => GetMessage('MFP_AUTHOR_WORK_PHONE'),
						'AUTHOR_FAX'             => GetMessage('MFP_AUTHOR_FAX'),
						'AUTHOR_MAILBOX'         => GetMessage('MFP_AUTHOR_MAILBOX'),
						'AUTHOR_WORK_MAILBOX'    => GetMessage('MFP_AUTHOR_WORK_MAILBOX'),
						'AUTHOR_SKYPE'           => GetMessage('MFP_AUTHOR_SKYPE'),
						'AUTHOR_ICQ'             => GetMessage('MFP_AUTHOR_ICQ'),
						'AUTHOR_WWW'             => GetMessage('MFP_AUTHOR_WWW'),
						'AUTHOR_WORK_WWW'        => GetMessage('MFP_AUTHOR_WORK_WWW'),
						'AUTHOR_MESSAGE_THEME'   => GetMessage('MFP_AUTHOR_MESSAGE_THEME'),
						'AUTHOR_MESSAGE'         => GetMessage('MFP_AUTHOR_MESSAGE'),
						'AUTHOR_NOTES'           => GetMessage('MFP_AUTHOR_NOTES'),
				 ),
				 'ADDITIONAL_VALUES' => 'N',
				 'SIZE'              => 10,
				 'DEFAULT'           => '',
				 'COLS'              => 25,
			),
			'REQUIRED_FIELDS'              => Array(
				 'NAME'     => GetMessage('MFP_REQUIRED_FIELDS'),
				 'TYPE'     => 'LIST',
				 'MULTIPLE' => 'Y',
				 'VALUES'   => Array(
						'NONE'                   => GetMessage('MFP_ALL_REQ'),
						'AUTHOR_FIO'             => GetMessage('MFP_AUTHOR_FIO'),
						'AUTHOR_NAME'            => GetMessage('MFP_AUTHOR_NAME'),
						'AUTHOR_LAST_NAME'       => GetMessage('MFP_AUTHOR_LAST_NAME'),
						'AUTHOR_SECOND_NAME'     => GetMessage('MFP_AUTHOR_SECOND_NAME'),
						'AUTHOR_EMAIL'           => GetMessage('MFP_AUTHOR_EMAIL'),
						'AUTHOR_PERSONAL_MOBILE' => GetMessage('MFP_AUTHOR_PERSONAL_MOBILE'),
						'AUTHOR_WORK_COMPANY'    => GetMessage('MFP_AUTHOR_WORK_COMPANY'),
						'AUTHOR_POSITION'        => GetMessage('MFP_AUTHOR_POSITION'),
						'AUTHOR_PROFESSION'      => GetMessage('MFP_AUTHOR_PROFESSION'),
						'AUTHOR_STATE'           => GetMessage('MFP_AUTHOR_STATE'),
						'AUTHOR_CITY'            => GetMessage('MFP_AUTHOR_CITY'),
						'AUTHOR_WORK_CITY'       => GetMessage('MFP_AUTHOR_WORK_CITY'),
						'AUTHOR_STREET'          => GetMessage('MFP_AUTHOR_STREET'),
						'AUTHOR_ADRESS'          => GetMessage('MFP_AUTHOR_ADRESS'),
						'AUTHOR_PERSONAL_PHONE'  => GetMessage('MFP_AUTHOR_PERSONAL_PHONE'),
						'AUTHOR_WORK_PHONE'      => GetMessage('MFP_AUTHOR_WORK_PHONE'),
						'AUTHOR_FAX'             => GetMessage('MFP_AUTHOR_FAX'),
						'AUTHOR_MAILBOX'         => GetMessage('MFP_AUTHOR_MAILBOX'),
						'AUTHOR_WORK_MAILBOX'    => GetMessage('MFP_AUTHOR_WORK_MAILBOX'),
						'AUTHOR_SKYPE'           => GetMessage('MFP_AUTHOR_SKYPE'),
						'AUTHOR_ICQ'             => GetMessage('MFP_AUTHOR_ICQ'),
						'AUTHOR_WWW'             => GetMessage('MFP_AUTHOR_WWW'),
						'AUTHOR_WORK_WWW'        => GetMessage('MFP_AUTHOR_WORK_WWW'),
						'AUTHOR_MESSAGE_THEME'   => GetMessage('MFP_AUTHOR_MESSAGE_THEME'),
						'AUTHOR_MESSAGE'         => GetMessage('MFP_AUTHOR_MESSAGE'),
						'AUTHOR_NOTES'           => GetMessage('MFP_AUTHOR_NOTES'),
				 ),
				 'SIZE'     => 10,
				 'DEFAULT'  => '',
				 'COLS'     => 25,
				 'PARENT'   => 'BASE',
			),
			"CUSTOM_FIELDS"                => Array(
				 "NAME"     => GetMessage("CUSTOM_FIELDS"),
				 "TYPE"     => "STRING",
				 "MULTIPLE" => "Y",
				 "COLS"     => 50,
				 "PARENT"   => "BASE",
				 "DEFAULT"  => GetMessage("CUSTOM_FIELDS_VALUES"),
			),
			'BRANCH_ACTIVE'                => Array(
				 "NAME"    => GetMessage("BRANCH_ACTIVE"),
				 "TYPE"    => "CHECKBOX",
				 "DEFAULT" => "N",
				 "PARENT"  => "BRANCH_SETTINGS",
				 "REFRESH" => "Y",
			),
			'SHOW_FILES'                   => array(
				 'NAME'    => GetMessage('SHOW_FILES'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'N',
				 'PARENT'  => 'FILE_SETTINGS',
				 'REFRESH' => 'Y',
			),
			'ADMIN_EVENT_MESSAGE_ID'       => Array(
				 'NAME'     => GetMessage('MFP_EMAIL_TEMPLATES'),
				 'TYPE'     => 'LIST',
				 'VALUES'   => $arEvent,
				 'DEFAULT'  => '',
				 'MULTIPLE' => 'Y',
				 'COLS'     => 50,
				 'PARENT'   => 'EVENT_MESSAGE_SETTINGS',
			),
			'USER_EVENT_MESSAGE_ID'        => Array(
				 'NAME'     => GetMessage('MFP_USER_EMAIL_TEMPLATES'),
				 'TYPE'     => 'LIST',
				 'VALUES'   => $arEvent,
				 'DEFAULT'  => '',
				 'MULTIPLE' => 'Y',
				 'COLS'     => 50,
				 'PARENT'   => 'EVENT_MESSAGE_SETTINGS',
			),
			'WRITE_ONLY_FILLED_VALUES'     => array(
				 'NAME'    => GetMessage('WRITE_ONLY_FILLED_VALUES'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'Y',
				 'PARENT'  => 'EVENT_MESSAGE_SETTINGS',
			),
			'WRITE_MESS_DIV_STYLE'         => array(
				 'NAME'    => GetMessage('WRITE_MESS_DIV_STYLE'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => GetMessage('WRITE_MESS_DIV_STYLE_DEFAULT'),
				 'COLS'    => 50,
				 'PARENT'  => 'EVENT_MESSAGE_SETTINGS',
			),
			'WRITE_MESS_DIV_STYLE_NAME'    => array(
				 'NAME'    => GetMessage('WRITE_MESS_DIV_STYLE_NAME'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => GetMessage('WRITE_MESS_DIV_STYLE_NAME_DEFAULT'),
				 'COLS'    => 50,
				 'PARENT'  => 'EVENT_MESSAGE_SETTINGS',
			),
			'WRITE_MESS_DIV_STYLE_VALUE'   => array(
				 'NAME'    => GetMessage('WRITE_MESS_DIV_STYLE_VALUE'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => GetMessage('WRITE_MESS_DIV_STYLE_VALUE_DEFAULT'),
				 'COLS'    => 50,
				 'PARENT'  => 'EVENT_MESSAGE_SETTINGS',
			),
			'WRITE_MESS_FILDES_TABLE'      => array(
				 'NAME'    => GetMessage('WRITE_MESS_FILDES_TABLE'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'N',
				 'REFRESH' => 'N',
				 'PARENT'  => 'EVENT_MESSAGE_SETTINGS',
			),
			'WRITE_MESS_TABLE_STYLE'       => array(
				 'NAME'    => GetMessage('WRITE_MESS_TABLE_STYLE'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => GetMessage('WRITE_MESS_TABLE_STYLE_DEFAULT'),
				 'COLS'    => 50,
				 'PARENT'  => 'EVENT_MESSAGE_SETTINGS',
			),
			'WRITE_MESS_TABLE_STYLE_NAME'  => array(
				 'NAME'    => GetMessage('WRITE_MESS_TABLE_STYLE_NAME'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => GetMessage('WRITE_MESS_TABLE_STYLE_NAME_DEFAULT'),
				 'COLS'    => 50,
				 'PARENT'  => 'EVENT_MESSAGE_SETTINGS',
			),
			'WRITE_MESS_TABLE_STYLE_VALUE' => array(
				 'NAME'    => GetMessage('WRITE_MESS_TABLE_STYLE_VALUE'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => GetMessage('WRITE_MESS_TABLE_STYLE_VALUE_DEFAULT'),
				 'COLS'    => 50,
				 'PARENT'  => 'EVENT_MESSAGE_SETTINGS',
			),
			'FORM_CLASS'                   => array(
				 'NAME'    => GetMessage('FORM_CLASS'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'PARENT'  => 'VISUAL',
			),
			'TITLE_DISPLAY'                => array(
				 'NAME'    => GetMessage('MFP_FORM_TITLE_DISPLAY'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'Y',
				 'PARENT'  => 'VISUAL',
			),
			'FORM_TITLE'                   => array(
				 'NAME'    => GetMessage('MFP_FORM_TITLE'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => GetMessage('MFP_FORM_TITLE_VALUE'),
				 'COLS'    => 50,
				 'PARENT'  => 'VISUAL',
			),
			'FORM_TITLE_LEVEL'             => array(
				 'NAME'    => GetMessage('MFP_FORM_TITLE_LEVEL'),
				 'TYPE'    => 'LIST',
				 'VALUES'  => GetMessage('MFP_FORM_TITLE_LEVEL_VALUES'),
				 'DEFAULT' => '2',
				 'PARENT'  => 'VISUAL',
			),
			'FIELD_ERROR_MESS'             => Array(
				 'NAME'    => GetMessage('FIELD_ERROR_MESS'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => GetMessage('FIELD_ERROR_MESS_VALUE'),
				 'PARENT'  => 'VISUAL',
			),
			'EMAIL_ERROR_MESS'             => Array(
				 'NAME'    => GetMessage('EMAIL_ERROR_MESS'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => GetMessage('EMAIL_ERROR_MESS_VALUE'),
				 'PARENT'  => 'VISUAL',
			),
			'DEFAULT_OPTION_TEXT'          => array(
				 'NAME'    => GetMessage('DEFAULT_OPTION_TEXT'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => GetMessage('DEFAULT_OPTION_VALUE'),
				 'COLS'    => 50,
				 'PARENT'  => 'VISUAL',
			),
			'FORM_SUBMIT_CLASS'            => array(
				 'NAME'    => GetMessage('FORM_SUBMIT_CLASS'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => 'uk-button',
				 'COLS'    => 50,
				 'PARENT'  => 'VISUAL',
			),
			'FORM_SUBMIT_VALUE'            => array(
				 'NAME'    => GetMessage('MFP_FORM_SUBMIT_VALUE'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => GetMessage('FORM_SUBMIT_VALUE_DEFAULT'),
				 'COLS'    => 50,
				 'PARENT'  => 'VISUAL',
			),
			'FORM_SUBMIT_STYLE'            => array(
				 'NAME'    => GetMessage('FORM_SUBMIT_STYLE'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'COLS'    => 50,
				 'PARENT'  => 'VISUAL',
			),
			'BUTTON_TEXT_BEFORE'           => array(
				 'NAME'    => GetMessage('BUTTON_TEXT_BEFORE'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'COLS'    => 50,
				 'ROWS'    => 4,
				 'PARENT'  => 'VISUAL',
			),
			'FORM_TEXT_BEFORE'             => array(
				 'NAME'    => GetMessage('FORM_TEXT_BEFORE'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'COLS'    => 50,
				 'ROWS'    => 4,
				 'PARENT'  => 'VISUAL',
			),
			'FORM_TEXT_AFTER'              => array(
				 'NAME'    => GetMessage('FORM_TEXT_AFTER'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'COLS'    => 50,
				 'ROWS'    => 4,
				 'PARENT'  => 'VISUAL',
			),

			'HIDE_FIELD_NAME'   => array(
				 'NAME'    => GetMessage('HIDE_FIELD_NAME'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => '',
				 'PARENT'  => 'VISUAL',
			),
			'HIDE_ASTERISK'     => array(
				 'NAME'    => GetMessage('HIDE_ASTERISK'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => '',
				 'PARENT'  => 'VISUAL',
			),
			'FORM_AUTOCOMPLETE' => array(
				 'NAME'    => GetMessage('FORM_AUTOCOMPLETE'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'Y',
				 'PARENT'  => 'VISUAL',
			),

			'FIELD_BOX_SHADOW_ACTIVE' => array(
				 'NAME'    => GetMessage('FIELD_BOX_SHADOW_ACTIVE'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'COLS'    => 50,
				 'ROWS'    => 4,
				 'PARENT'  => 'VISUAL',
			),
			'FIELD_BORDER_ACTIVE'     => array(
				 'NAME'    => GetMessage('FIELD_BORDER_ACTIVE'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'COLS'    => 50,
				 'ROWS'    => 1,
				 'PARENT'  => 'VISUAL',
			),

			'FIELD_SIZE'                  => array(
				 'NAME'              => GetMessage('FIELD_SIZE'),
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
			'FIELD_NAME_POSITION'         => array(
				 'NAME'              => GetMessage('FIELD_NAME_POSITION'),
				 'TYPE'              => 'LIST',
				 'VALUES'            => GetMessage('FIELD_NAME_POSITION_VALUES'),
				 'DEFAULT'           => 'horizontal',
				 'ADDITIONAL_VALUES' => 'Y',
				 'REFRESH'           => 'N',
				 'PARENT'            => 'VISUAL',
			),
			'FORM_LABEL_TEXT_ALIGN'       => array(
				 'NAME'    => GetMessage('FORM_LABEL_TEXT_ALIGN'),
				 'TYPE'    => 'LIST',
				 'VALUES'  => GetMessage('FORM_LABEL_TEXT_ALIGN_VALUES'),
				 'DEFAULT' => 0,
				 'PARENT'  => 'VISUAL',
			),
			'FORM_LABEL_WIDTH'            => array(
				 'NAME'    => GetMessage('FORM_LABEL_WIDTH'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => GetMessage('FORM_LABEL_WIDTH_VALUE'),
				 'PARENT'  => 'VISUAL',
			),
			'FORM_FIELD_WIDTH'            => array(
				 'NAME'    => GetMessage('FORM_FIELD_WIDTH'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => GetMessage('FORM_FIELD_WIDTH_VALUE'),
				 'PARENT'  => 'VISUAL',
			),
			'USE_YM_GOALS'                => Array(
				 'NAME'    => GetMessage('USE_YM_GOALS'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'N',
				 'REFRESH' => 'Y',
				 'PARENT'  => 'YM_GOALS_SETTINGS',
			),
			'USER_AUTHOR_FIO'             => array(
				 'NAME'    => GetMessage('MFP_AUTHOR_FIO'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'COLS'    => 30,
				 'PARENT'  => 'TITLE',
			),
			'USER_AUTHOR_NAME'            => array(
				 'NAME'    => GetMessage('MFP_AUTHOR_NAME'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'COLS'    => 30,
				 'PARENT'  => 'TITLE',
			),
			'USER_AUTHOR_LAST_NAME'       => array(
				 'NAME'    => GetMessage('MFP_AUTHOR_LAST_NAME'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'COLS'    => 30,
				 'PARENT'  => 'TITLE',
			),
			'USER_AUTHOR_SECOND_NAME'     => array(
				 'NAME'    => GetMessage('MFP_AUTHOR_SECOND_NAME'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'COLS'    => 30,
				 'PARENT'  => 'TITLE',
			),
			'USER_AUTHOR_EMAIL'           => array(
				 'NAME'    => GetMessage('MFP_AUTHOR_EMAIL'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'COLS'    => 30,
				 'PARENT'  => 'TITLE',
			),
			'USER_AUTHOR_PERSONAL_MOBILE' => array(
				 'NAME'    => GetMessage('MFP_AUTHOR_PERSONAL_MOBILE'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'COLS'    => 30,
				 'PARENT'  => 'TITLE',
			),
			'USER_AUTHOR_WORK_COMPANY'    => array(
				 'NAME'    => GetMessage('MFP_AUTHOR_WORK_COMPANY'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'COLS'    => 30,
				 'PARENT'  => 'TITLE',
			),
			'USER_AUTHOR_POSITION'        => array(
				 'NAME'    => GetMessage('MFP_AUTHOR_POSITION'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'COLS'    => 30,
				 'PARENT'  => 'TITLE',
			),
			'USER_AUTHOR_PROFESSION'      => array(
				 'NAME'    => GetMessage('MFP_AUTHOR_PROFESSION'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'COLS'    => 30,
				 'PARENT'  => 'TITLE',
			),
			'USER_AUTHOR_STATE'           => array(
				 'NAME'    => GetMessage('MFP_AUTHOR_STATE'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'COLS'    => 30,
				 'PARENT'  => 'TITLE',
			),
			'USER_AUTHOR_CITY'            => array(
				 'NAME'    => GetMessage('MFP_AUTHOR_CITY'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'COLS'    => 30,
				 'PARENT'  => 'TITLE',
			),
			'USER_AUTHOR_WORK_CITY'       => array(
				 'NAME'    => GetMessage('MFP_AUTHOR_WORK_CITY'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'COLS'    => 30,
				 'PARENT'  => 'TITLE',
			),
			'USER_AUTHOR_STREET'          => array(
				 'NAME'    => GetMessage('MFP_AUTHOR_STREET'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'COLS'    => 30,
				 'PARENT'  => 'TITLE',
			),
			'USER_AUTHOR_ADRESS'          => array(
				 'NAME'    => GetMessage('MFP_AUTHOR_ADRESS'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'COLS'    => 30,
				 'PARENT'  => 'TITLE',
			),
			'USER_AUTHOR_PERSONAL_PHONE'  => array(
				 'NAME'    => GetMessage('MFP_AUTHOR_PERSONAL_PHONE'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'COLS'    => 30,
				 'PARENT'  => 'TITLE',
			),
			'USER_AUTHOR_WORK_PHONE'      => array(
				 'NAME'    => GetMessage('MFP_AUTHOR_WORK_PHONE'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'COLS'    => 30,
				 'PARENT'  => 'TITLE',
			),
			'USER_AUTHOR_FAX'             => array(
				 'NAME'    => GetMessage('MFP_AUTHOR_FAX'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'COLS'    => 30,
				 'PARENT'  => 'TITLE',
			),
			'USER_AUTHOR_MAILBOX'         => array(
				 'NAME'    => GetMessage('MFP_AUTHOR_MAILBOX'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'COLS'    => 30,
				 'PARENT'  => 'TITLE',
			),
			'USER_AUTHOR_WORK_MAILBOX'    => array(
				 'NAME'    => GetMessage('MFP_AUTHOR_WORK_MAILBOX'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'COLS'    => 30,
				 'PARENT'  => 'TITLE',
			),
			'USER_AUTHOR_SKYPE'           => array(
				 'NAME'    => GetMessage('MFP_AUTHOR_SKYPE'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'COLS'    => 30,
				 'PARENT'  => 'TITLE',
			),
			'USER_AUTHOR_ICQ'             => array(
				 'NAME'    => GetMessage('MFP_AUTHOR_ICQ'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'COLS'    => 30,
				 'PARENT'  => 'TITLE',
			),
			'USER_AUTHOR_WWW'             => array(
				 'NAME'    => GetMessage('MFP_AUTHOR_WWW'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'COLS'    => 30,
				 'PARENT'  => 'TITLE',
			),
			'USER_AUTHOR_WORK_WWW'        => array(
				 'NAME'    => GetMessage('MFP_AUTHOR_WORK_WWW'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'COLS'    => 30,
				 'PARENT'  => 'TITLE',
			),
			'USER_AUTHOR_MESSAGE_THEME'   => array(
				 'NAME'    => GetMessage('MFP_AUTHOR_MESSAGE_THEME'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'COLS'    => 30,
				 'PARENT'  => 'TITLE',
			),
			'USER_AUTHOR_MESSAGE'         => array(
				 'NAME'    => GetMessage('MFP_AUTHOR_MESSAGE'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'COLS'    => 30,
				 'PARENT'  => 'TITLE',
			),
			'USER_AUTHOR_NOTES'           => array(
				 'NAME'    => GetMessage('MFP_AUTHOR_NOTES'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'COLS'    => 30,
				 'PARENT'  => 'TITLE',
			),
			/** @deprecated in v2.7.1 used CUSTOM COMPONENT AJAX */
			"AJAX_MODE"                   => array(),

			'INCLUDE_CSSMODAL'   => array(
				 'PARENT'            => 'MODAL_SETTINGS',
				 'NAME'              => GetMessage('INCLUDE_CSSMODAL'),
				 'TYPE'              => 'LIST',
				 'VALUES'            => array(
						'N'          => GetMessage('NOT_SET'),
						'cssmodal'   => 'CSS Modal 1',
						'uikit2'     => 'Uikit 2',
						'bootstrap3' => 'Bootstrap 3',
				 ),
				 'DEFAULT'           => '',
				 'ADDITIONAL_VALUES' => 'N',
				 'REFRESH'           => 'Y',
			),
			'MODAL_WIDTH'        => Array(
				 'NAME'    => GetMessage('MODAL_WIDTH'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'COLS'    => 50,
				 'PARENT'  => 'MODAL_SETTINGS',
			),
			'MODAL_BUTTON_CLASS' => array(
				 'NAME'    => GetMessage('MODAL_BUTTON_CLASS'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => 'uk-button uk-button-danger',
				 'COLS'    => 50,
				 'PARENT'  => 'MODAL_SETTINGS',
			),
			'MODAL_BUTTON_HTML'  => Array(
				 'NAME'    => GetMessage('MODAL_BUTTON_HTML'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => GetMessage('MODAL_BUTTON_HTML_DEFAULT'),
				 'ROWS'    => 5,
				 'COLS'    => 50,
				 'PARENT'  => 'MODAL_SETTINGS',
			),
			'MODAL_HEADER_HTML'  => Array(
				 'NAME'    => GetMessage('MODAL_HEADER_HTML'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => GetMessage('MODAL_HEADER_HTML_DEFAULT'),
				 'ROWS'    => 6,
				 'COLS'    => 50,
				 'PARENT'  => 'MODAL_SETTINGS',
			),
			'MODAL_FOOTER_HTML'  => Array(
				 'NAME'    => GetMessage('MODAL_FOOTER_HTML'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => GetMessage('MODAL_FOOTER_HTML_DEFAULT'),
				 'ROWS'    => 6,
				 'COLS'    => 50,
				 'PARENT'  => 'MODAL_SETTINGS',
			),

			'UUID_LENGTH'   => Array(
				 'NAME'    => GetMessage('UUID_LENGTH'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => 10,
				 'COLS'    => 3,
				 'PARENT'  => 'UUID_SETTINGS',
			),
			'UUID_PREFIX'   => Array(
				 'NAME'    => GetMessage('UUID_PREFIX'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'PARENT'  => 'UUID_SETTINGS',
			),

			//SERVICE_MACROS_SETTINGS
			'SUBJECT'       => Array(
				 'NAME'    => GetMessage('SUBJECT'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'PARENT'  => 'SERVICE_MACROS_SETTINGS',
			),
			'PAGE_TITLE'    => Array(
				 'NAME'    => GetMessage('PAGE_TITLE'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'PARENT'  => 'SERVICE_MACROS_SETTINGS',
			),
			'PAGE_URI'      => Array(
				 'NAME'    => GetMessage('PAGE_URI'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'PARENT'  => 'SERVICE_MACROS_SETTINGS',
			),
			'PAGE_URL'      => Array(
				 'NAME'    => GetMessage('PAGE_URL'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'PARENT'  => 'SERVICE_MACROS_SETTINGS',
			),
			'DIR_URL'       => Array(
				 'NAME'    => GetMessage('DIR_URL'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'PARENT'  => 'SERVICE_MACROS_SETTINGS',
			),
			'DATETIME'      => Array(
				 'NAME'    => GetMessage('DATETIME'),
				 'TYPE'    => 'STRING',
				 'DEFAULT' => '',
				 'PARENT'  => 'SERVICE_MACROS_SETTINGS',
			),


			//AGREEMENT_SETTINGS
			'USE_AGREEMENT' => array(
				 'PARENT'  => 'AGREEMENT_SETTINGS',
				 'NAME'    => GetMessage('USE_AGREEMENT'),
				 'TYPE'    => 'CHECKBOX',
				 'DEFAULT' => 'N',
				 'REFRESH' => 'Y',
			),

			//SERVER_SETTINGS
			'SERVER_VARS'   => array(
				 'PARENT'            => 'SERVER_SETTINGS',
				 'NAME'              => GetMessage('SERVER_VARS'),
				 'TYPE'              => 'LIST',
				 'VALUES'            => $arServerVars,
				 'MULTIPLE'          => 'Y',
				 'SIZE'              => 10,
				 'DEFAULT'           => '',
				 'ADDITIONAL_VALUES' => 'Y',
				 'REFRESH'           => 'N',
			),
			'REQUEST_VARS'  => array(
				 'PARENT'            => 'SERVER_SETTINGS',
				 'NAME'              => GetMessage('REQUEST_VARS'),
				 'TYPE'              => 'LIST',
				 'VALUES'            => array(
						'utm_source'   => 'utm_source',
						'utm_medium'   => 'utm_medium',
						'utm_campaign' => 'utm_campaign',
						'utm_content'  => 'utm_content',
						'utm_term'     => 'utm_term',
				 ),
				 'MULTIPLE'          => 'Y',
				 'SIZE'              => 6,
				 'DEFAULT'           => '',
				 'ADDITIONAL_VALUES' => 'Y',
				 'REFRESH'           => 'N',
			),
	 ),
);


if($arCurrentValues["USE_AGREEMENT"] == "Y") {
	$arComponentParameters["PARAMETERS"]["AGREEMENT_TEXT"]  = array(
		 "PARENT"  => "AGREEMENT_SETTINGS",
		 "NAME"    => GetMessage("AGREEMENT_TEXT"),
		 "TYPE"    => "STRING",
		 "DEFAULT" => GetMessage("AGREEMENT_TEXT_DEFAULT"),
	);
	$arComponentParameters["PARAMETERS"]["AGREEMENT_ERROR"] = array(
		 "PARENT"  => "AGREEMENT_SETTINGS",
		 "NAME"    => GetMessage("AGREEMENT_ERROR"),
		 "TYPE"    => "STRING",
		 "DEFAULT" => GetMessage("AGREEMENT_ERROR_DEFAULT"),
	);
	$arComponentParameters["PARAMETERS"]["AGREEMENT_LINK"]  = array(
		 "PARENT"  => "AGREEMENT_SETTINGS",
		 "NAME"    => GetMessage("AGREEMENT_LINK"),
		 "TYPE"    => "STRING",
		 "DEFAULT" => GetMessage("AGREEMENT_LINK_DEFAULT"),
	);
}

if($arCurrentValues["BRANCH_ACTIVE"] == "Y") {
	$arComponentParameters["PARAMETERS"]["BRANCH_BLOCK_NAME"]       = array(
		 "NAME"    => GetMessage("BRANCH_BLOCK_NAME"),
		 "TYPE"    => "STRING",
		 "DEFAULT" => GetMessage("BRANCH_BLOCK_NAME_OTDEL"),
		 "PARENT"  => "BRANCH_SETTINGS",
	);
	$arComponentParameters["PARAMETERS"]["BRANCH_FIELDS"]           = array(
		 "NAME"     => GetMessage("BRANCH_FIELDS"),
		 "TYPE"     => "STRING",
		 "MULTIPLE" => "Y",
		 "COLS"     => 50,
		 "PARENT"   => "BRANCH_SETTINGS",
		 "DEFAULT"  => Array(
				0 => GetMessage("BRANCH_FIELDS_0") . '###admin1@' . $GLOBALS["SERVER_NAME"] . '###admin2@' . $GLOBALS["SERVER_NAME"],
				1 => GetMessage("BRANCH_FIELDS_1") . '###buh@' . $GLOBALS["SERVER_NAME"],
				2 => GetMessage("BRANCH_FIELDS_2") . '###sale@' . $GLOBALS["SERVER_NAME"],
		 ),
	);
	$arComponentParameters["PARAMETERS"]["MSG_PRIORITY"]            = array(
		 "NAME"    => GetMessage("MSG_PRIORITY"),
		 "TYPE"    => "CHECKBOX",
		 "DEFAULT" => "N",
		 "PARENT"  => "BRANCH_SETTINGS",
	);
	$arComponentParameters["PARAMETERS"]["MSG_PRIORITY_BLOCK_NAME"] = array(
		 "NAME"    => GetMessage("BRANCH_BLOCK_NAME"),
		 "TYPE"    => "STRING",
		 "DEFAULT" => GetMessage("MSG_PRIORITY_BLOCK_NAME_VAJNOSTQ"),
		 "PARENT"  => "BRANCH_SETTINGS",
	);
}


if($arCurrentValues['SHOW_FILES'] == 'Y') {

	$arComponentParameters['PARAMETERS']['DELETE_FILES_AFTER_UPLOAD']  = array(
		 'NAME'    => GetMessage('DELETE_FILES_AFTER_UPLOAD'),
		 'TYPE'    => 'CHECKBOX',
		 'DEFAULT' => 'N',
		 'PARENT'  => 'FILE_SETTINGS',
	);
	$arComponentParameters['PARAMETERS']['SEND_ATTACHMENT']            = array(
		 'NAME'    => GetMessage('SEND_ATTACHMENT'),
		 'TYPE'    => 'CHECKBOX',
		 'DEFAULT' => 'Y',
		 'PARENT'  => 'FILE_SETTINGS',
	);
	$arComponentParameters['PARAMETERS']['SET_ATTACHMENT_REQUIRED']    = array(
		 'NAME'    => GetMessage('SET_ATTACHMENT_REQUIRED'),
		 'TYPE'    => 'CHECKBOX',
		 'DEFAULT' => 'N',
		 'PARENT'  => 'FILE_SETTINGS',
	);
	$arComponentParameters['PARAMETERS']['COUNT_INPUT_FILE']           = Array(
		 'NAME'    => GetMessage('COUNT_INPUT_FILE'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => '3',
		 'PARENT'  => 'FILE_SETTINGS',
	);
	$arComponentParameters['PARAMETERS']["FILE_DESCRIPTION"]           = Array(
		 "NAME"     => GetMessage("FILE_DESCRIPTION"),
		 "TYPE"     => "STRING",
		 "MULTIPLE" => "Y",
		 "PARENT"   => "FILE_SETTINGS",
		 "DEFAULT"  => Array(),
	);
	$arComponentParameters['PARAMETERS']['MAX_FILE_SIZE']              = Array(
		 'NAME'    => GetMessage('MAX_FILE_SIZE'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => CApiFeedback::getFileSizeInBytes(ini_get('upload_max_filesize')),
		 'PARENT'  => 'FILE_SETTINGS',
	);
	$arComponentParameters['PARAMETERS']['SHOW_ATTACHMENT_EXTENSIONS'] = array(
		 'NAME'    => GetMessage('SHOW_ATTACHMENT_EXTENSIONS'),
		 'TYPE'    => 'CHECKBOX',
		 'DEFAULT' => 'N',
		 'PARENT'  => 'FILE_SETTINGS',
	);
	$arComponentParameters['PARAMETERS']['FILE_EXTENSIONS']            = Array(
		 'NAME'    => GetMessage('FILE_EXTENSIONS'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => 'txt, rtf, doc, docx, xls, xlsx, ods, odt, jpg, jpeg, bmp, png',
		 'PARENT'  => 'FILE_SETTINGS',
	);
	$arComponentParameters['PARAMETERS']['UPLOAD_FOLDER']              = Array(
		 'NAME'    => GetMessage('UPLOAD_FOLDER'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => '/upload/feedback',
		 'COLS'    => 50,
		 'PARENT'  => 'FILE_SETTINGS',
	);
	$arComponentParameters['PARAMETERS']['CHOOSE_FILE_TEXT']           = Array(
		 'NAME'    => GetMessage('CHOOSE_FILE_TEXT'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => GetMessage('CHOOSE_FILE_TEXT_VALUE'),
		 'COLS'    => 50,
		 'PARENT'  => 'FILE_SETTINGS',
	);
	$arComponentParameters['PARAMETERS']['FILE_ERROR_MESS']            = Array(
		 'NAME'    => GetMessage('FILE_ERROR_MESS'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => GetMessage('FILE_ERROR_MESS_VALUE'),
		 'PARENT'  => 'FILE_SETTINGS',
	);
}

if($arCurrentValues["INSTALL_IBLOCK"] == "Y") {
	global $DB;
	$IBLOCK_TYPE      = 'tuning_soft';
	$bIblockTypeExist = false;
	$arErrors         = array();
	$arNotes          = array();


	$rs_sites = CSite::GetList($by = "sort", $order = "asc");
	while($ar_site = $rs_sites->Fetch())
		$arSites[] = $ar_site['ID'];


	//Find exist iblock type
	if(!array_key_exists($IBLOCK_TYPE, $arIBlockType)) {
		$arFields = Array(
			 'ID'       => $IBLOCK_TYPE,
			 'SECTIONS' => 'Y',
			 'IN_RSS'   => 'N',
			 'SORT'     => 5000,
			 'LANG'     => Array(
					'ru' => Array(
						 'NAME'         => GetMessage('IT_RU_NAME'),
						 'SECTION_NAME' => GetMessage('IT_RU_SECTION_NAME'),
						 'ELEMENT_NAME' => GetMessage('IT_RU_ELEMENT_NAME'),
					),
					'en' => Array(
						 'NAME'         => GetMessage('IT_EN_NAME'),
						 'SECTION_NAME' => GetMessage('IT_EN_SECTION_NAME'),
						 'ELEMENT_NAME' => GetMessage('IT_EN_ELEMENT_NAME'),
					),
			 ),
		);

		$obBlocktype = new CIBlockType;
		$DB->StartTransaction();
		$res = $obBlocktype->Add($arFields);
		if(!$res) {
			$DB->Rollback();
			$arErrors[] = $obBlocktype->LAST_ERROR;
		}
		else {
			$DB->Commit();
			$bIblockTypeExist = true;
			$arNotes[]        = GetMessage('IT_CREATE_SUCCESS');
		}
	}
	else
		$bIblockTypeExist = true;

	if($bIblockTypeExist) {
		if(!$arNotes)
			$arNotes[] = GetMessage('IT_FOUND');

		$res_iblock = CIBlock::GetList(
			 array(),
			 array(
					'TYPE'    => $IBLOCK_TYPE,
					'SITE_ID' => $arSites,
					'ACTIVE'  => 'Y',
					'CODE'    => 'main-feedback-statistic',
			 )
		);

		if(!$ar_res = $res_iblock->Fetch()) {
			$ib        = new CIBlock;
			$arFields  = Array(
				 "ACTIVE"           => "Y",
				 "NAME"             => GetMessage('IBLOCK_NAME'),
				 "CODE"             => "main-feedback-statistic",
				 "IBLOCK_TYPE_ID"   => $IBLOCK_TYPE,
				 "SITE_ID"          => $arSites,
				 "SORT"             => 10,
				 "LIST_PAGE_URL"    => "",
				 "SECTION_PAGE_URL" => "",
				 "DETAIL_PAGE_URL"  => "",
				 "DESCRIPTION_TYPE" => "html",
				 "RSS_ACTIVE"       => "N",
				 "INDEX_ELEMENT"    => "N",
				 "INDEX_SECTION"    => "N",
				 "WORKFLOW"         => "N",
				 "VERSION"          => 2,
				 "GROUP_ID"         => Array(
						"1" => "X",
						"2" => "D",
						"3" => "D",
						"4" => "D",
						"5" => "D",
				 ),
				 "FIELDS"           => array(
						"ACTIVE_FROM"  => array("DEFAULT_VALUE" => ""),
						"CODE"         => array(
							 "DEFAULT_VALUE" => array(
									"UNIQUE"          => "N",
									"TRANSLITERATION" => "N",
							 ),
						),
						"SECTION_CODE" => array(
							 "DEFAULT_VALUE" => array(
									"UNIQUE"          => "N",
									"TRANSLITERATION" => "N",
							 ),
						),
				 ),
			);
			$IBLOCK_ID = $ib->Add($arFields);

			if(intval($IBLOCK_ID)) {
				$arNotes[] = GetMessage('IBLOCK_CREATE_SUCCESS');

				$obProp  = new CIBlockProperty;
				$arProps = array(
					 0 => array(
							"CODE"          => "TICKET_ID",
							"NAME"          => GetMessage('PROP_NAME_TICKET_ID'),
							"ACTIVE"        => "Y",
							"IS_REQUIRED"   => "Y",
							"SORT"          => "10",
							"PROPERTY_TYPE" => "N",
							"MULTIPLE"      => "N",
							"SEARCHABLE"    => "N",
							"FILTRABLE"     => "Y",
							"VERSION"       => 2,
					 ),
					 1 => array(
							"CODE"             => "FILES",
							"NAME"             => GetMessage('PROP_NAME_FILES'),
							"ACTIVE"           => "Y",
							"IS_REQUIRED"      => "N",
							"SORT"             => "20",
							"PROPERTY_TYPE"    => "F",
							"MULTIPLE"         => "Y",
							"SEARCHABLE"       => "N",
							"FILTRABLE"        => "Y",
							"WITH_DESCRIPTION" => "Y",
							"VERSION"          => 2,
					 ),
				);

				$propsCount = count($arProps);

				foreach($arProps as $arProp) {
					$arProp["IBLOCK_ID"] = $IBLOCK_ID;
					if($obProp->Add($arProp))
						$propsCount--;
				}

				if(!$propsCount)
					$arNotes[] = GetMessage('ALL_PROP_CREATE_SUCCESS');
				else
					$arErrors[] = GetMessage('ALL_PROP_CREATE_ERROR');
			}
			else
				$arErrors[] = $ib->LAST_ERROR;
		}
		else
			$arNotes[] = GetMessage('IBLOCK_FOUND');
	}

	if(!empty($arErrors))
		ShowError(implode('<br />', $arErrors));

	if(!empty($arNotes))
		ShowNote(implode('<br />', $arNotes));
}

if($arCurrentValues['USE_YM_GOALS'] == 'Y') {
	$arComponentParameters['PARAMETERS']['YM_COUNTER_ID']  = array(
		 'NAME'    => GetMessage('YM_COUNTER_ID'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => '',
		 'COLS'    => 8,
		 'PARENT'  => 'YM_GOALS_SETTINGS',
	);
	$arComponentParameters['PARAMETERS']['YM_TARGET_ID']   = array(
		 'NAME'    => GetMessage('YM_TARGET_ID'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => '',
		 'COLS'    => 8,
		 'PARENT'  => 'YM_GOALS_SETTINGS',
	);
	$arComponentParameters['PARAMETERS']['YM_TARGET_NAME'] = array(
		 'NAME'    => GetMessage('YM_TARGET_NAME'),
		 'TYPE'    => 'STRING',
		 'DEFAULT' => '',
		 'COLS'    => 42,
		 'PARENT'  => 'YM_GOALS_SETTINGS',
	);
}

if($arCurrentValues['INCLUDE_VALIDATION'] == 'Y') {
	$arComponentParameters['PARAMETERS']["VALIDATION_MESSAGES"] = Array(
		 "PARENT"   => "JS_VALIDATE_SETTINGS",
		 "NAME"     => GetMessage('VALIDATION_MESSAGES'),
		 "TYPE"     => "STRING",
		 "MULTIPLE" => "Y",
		 "DEFAULT"  => Array(
				0  => GetMessage('VALIDATION_MESSAGE_default'),
				1  => GetMessage('VALIDATION_MESSAGE_NOTEMPTY'),
				2  => GetMessage('VALIDATION_MESSAGE_INTEGER'),
				3  => GetMessage('VALIDATION_MESSAGE_NUMERIC'),
				4  => GetMessage('VALIDATION_MESSAGE_MIXED'),
				5  => GetMessage('VALIDATION_MESSAGE_NAME'),
				6  => GetMessage('VALIDATION_MESSAGE_NOSPACE'),
				7  => GetMessage('VALIDATION_MESSAGE_TRIM'),
				8  => GetMessage('VALIDATION_MESSAGE_DATE'),
				9  => GetMessage('VALIDATION_MESSAGE_EMAIL'),
				10 => GetMessage('VALIDATION_MESSAGE_URL'),
				11 => GetMessage('VALIDATION_MESSAGE_PHONE'),
				12 => GetMessage('VALIDATION_MESSAGE_<'),
				13 => GetMessage('VALIDATION_MESSAGE_<='),
				14 => GetMessage('VALIDATION_MESSAGE_>'),
				15 => GetMessage('VALIDATION_MESSAGE_>='),
				16 => GetMessage('VALIDATION_MESSAGE_=='),
				17 => GetMessage('VALIDATION_MESSAGE_!='),
		 ),
	);
}


if($arCurrentValues['INCLUDE_ICHECK'] == 'Y') {
	$arComponentParameters['PARAMETERS']["ICHECK_THEME"] = Array(
		 'NAME'              => GetMessage('ICHECK_THEME'),
		 'TYPE'              => 'LIST',
		 'VALUES'            => array(
				'flat'     => 'flat',
				'futurico' => 'futurico',
				'line'     => 'line',
				'minimal'  => 'minimal',
				'polaris'  => 'polaris',
				'square'   => 'square',
		 ),
		 'DEFAULT'           => 'flat',
		 'ADDITIONAL_VALUES' => 'Y',
		 'REFRESH'           => 'Y',
		 'PARENT'            => 'ICHECK_SETTINGS',
	);

	$arComponentParameters['PARAMETERS']["ICHECK_THEME_COLOR"] = Array(
		 'NAME'              => GetMessage('ICHECK_THEME_COLOR'),
		 'TYPE'              => 'LIST',
		 'VALUES'            => array(
				'red'    => 'red',
				'green'  => 'green',
				'blue'   => 'blue',
				'aero'   => 'aero',
				'grey'   => 'grey',
				'orange' => 'orange',
				'yellow' => 'yellow',
				'pink'   => 'pink',
				'purple' => 'purple',
		 ),
		 'DEFAULT'           => 'blue',
		 'ADDITIONAL_VALUES' => 'N',
		 'REFRESH'           => 'N',
		 'PARENT'            => 'ICHECK_SETTINGS',
	);


	if($arCurrentValues['ICHECK_THEME'] == 'polaris' || $arCurrentValues['ICHECK_THEME'] == 'futurico') {
		$arComponentParameters['PARAMETERS']["ICHECK_THEME_COLOR"]["VALUES"] = array('' => '---');
	}
}


if($arCurrentValues['INCLUDE_TOOLTIPSTER'] == 'Y') {
	$arComponentParameters['PARAMETERS']["TOOLTIPSTER_OPTIONS"] = Array(
		 "PARENT"   => "TOOLTIPSTER_SETTINGS",
		 "NAME"     => GetMessage('TOOLTIPSTER_OPTIONS'),
		 "TYPE"     => "STRING",
		 "MULTIPLE" => "Y",
		 "DEFAULT"  => GetMessage('TOOLTIPSTER_OPTIONS_DEFAULT'),
	);
}
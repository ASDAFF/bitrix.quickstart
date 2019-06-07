<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

/** @var array $arCurrentValues */

use \Bitrix\Main\Loader;
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if(!Loader::includeModule('api.typo'))
{
	ShowError(Loc::getMessage('API_TYPO_MODULE_ERROR'));
	return;
}

//---------- ������ ���������� ����������� ----------//
//BASE                  (���������� 100). �������� ���������.
//DATA_SOURCE           (���������� 200). ��� � ID ���������.
//VISUAL                (���������� 300). ����� ������������ ������. ���� �������������� �������� ���������, ���������� �� ������� ���.
//URL_TEMPLATES         (���������� 400). ������� ������
//SEF_MODE              (���������� 500). ������ ��� ���� ����������, ��������� � �������������� ���.
//AJAX_SETTINGS         (���������� 550). ���, ��� �������� ajax.
//CACHE_SETTINGS        (���������� 600). ���������� ��� �������� ��������� CACHE_TIME.
//ADDITIONAL_SETTINGS   (���������� 700). ��� ������ ����������, ��������, ��� �������� ��������� SET_TITLE.

$arComponentParameters = array(
	 'GROUPS'     => array(
			'GROUP_MESSAGE' => array(
				 'NAME' => Loc::getMessage('API_TYPO_GROUP_MESSAGE'),
			),
	 ),
	 'PARAMETERS' => array(

		 //GROUP_BASE
		 'JQUERY_ON'             => array(
				'NAME'    => Loc::getMessage('API_TYPO_JQUERY_ON'),
				'PARENT'  => 'BASE',
				'TYPE'    => 'LIST',
				'DEFAULT' => 'N',
				'REFRESH' => 'Y',
				'VALUES'  => Loc::getMessage('API_TYPO_JQUERY_ON_VALUES'),
		 ),
		 'AJAX_URL'              => array(
				'NAME'    => Loc::getMessage('API_TYPO_AJAX_URL'),
				'PARENT'  => 'BASE',
				'TYPE'    => 'STRING',
				'DEFAULT' => Loc::getMessage('API_TYPO_AJAX_URL_DEFAULT'),
		 ),
		 'MAX_LENGTH'              => array(
				'NAME'    => Loc::getMessage('API_TYPO_MAX_LENGTH'),
				'PARENT'  => 'BASE',
				'TYPE'    => 'STRING',
				'DEFAULT' => Loc::getMessage('API_TYPO_MAX_LENGTH_DEFAULT'),
		 ),
		 'EMAIL_FROM'              => array(
				'NAME'    => Loc::getMessage('API_TYPO_EMAIL_FROM'),
				'PARENT'  => 'BASE',
				'TYPE'    => 'STRING',
				'DEFAULT' => '',
		 ),
		 'EMAIL_TO'              => array(
				'NAME'    => Loc::getMessage('API_TYPO_EMAIL_TO'),
				'PARENT'  => 'BASE',
				'TYPE'    => 'STRING',
				'DEFAULT' => '',
		 ),


		 //GROUP_MESSAGE
		 'MESS_TPL_CONTENT'      => array(
				'NAME'    => Loc::getMessage('API_TYPO_MESS_TPL_CONTENT'),
				'PARENT'  => 'GROUP_MESSAGE',
				'TYPE'    => 'STRING',
				'ROWS'    => 4,
				'DEFAULT' => Loc::getMessage('API_TYPO_MESS_TPL_CONTENT_DEFAULT'),
		 ),
		 'MESS_ALERT_TEXT_MAX'   => array(
				'NAME'    => Loc::getMessage('API_TYPO_MESS_ALERT_TEXT_MAX'),
				'PARENT'  => 'GROUP_MESSAGE',
				'TYPE'    => 'STRING',
				'ROWS'    => 4,
				'DEFAULT' => Loc::getMessage('API_TYPO_MESS_ALERT_TEXT_MAX_DEFAULT'),
		 ),
		 'MESS_ALERT_TEXT_EMPTY' => array(
				'NAME'    => Loc::getMessage('API_TYPO_MESS_ALERT_TEXT_EMPTY'),
				'PARENT'  => 'GROUP_MESSAGE',
				'TYPE'    => 'STRING',
				'ROWS'    => 4,
				'DEFAULT' => Loc::getMessage('API_TYPO_MESS_ALERT_TEXT_EMPTY_DEFAULT'),
		 ),
		 'MESS_ALERT_SEND_OK'    => array(
				'NAME'    => Loc::getMessage('API_TYPO_MESS_ALERT_SEND_OK'),
				'PARENT'  => 'GROUP_MESSAGE',
				'TYPE'    => 'STRING',
				'ROWS'    => 4,
				'DEFAULT' => Loc::getMessage('API_TYPO_MESS_ALERT_SEND_OK_DEFAULT'),
		 ),
		 'MESS_MODAL_TITLE'      => array(
				'NAME'    => Loc::getMessage('API_TYPO_MESS_MODAL_TITLE'),
				'PARENT'  => 'GROUP_MESSAGE',
				'TYPE'    => 'STRING',
				'ROWS'    => 4,
				'DEFAULT' => Loc::getMessage('API_TYPO_MESS_MODAL_TITLE_DEFAULT'),
		 ),
		 'MESS_MODAL_COMMENT'    => array(
				'NAME'    => Loc::getMessage('API_TYPO_MESS_MODAL_COMMENT'),
				'PARENT'  => 'GROUP_MESSAGE',
				'TYPE'    => 'STRING',
				'ROWS'    => 4,
				'DEFAULT' => Loc::getMessage('API_TYPO_MESS_MODAL_COMMENT_DEFAULT'),
		 ),
		 'MESS_MODAL_SUBMIT'     => array(
				'NAME'    => Loc::getMessage('API_TYPO_MESS_MODAL_SUBMIT'),
				'PARENT'  => 'GROUP_MESSAGE',
				'TYPE'    => 'STRING',
				'DEFAULT' => Loc::getMessage('API_TYPO_MESS_MODAL_SUBMIT_DEFAULT'),
		 ),
		 'MESS_MODAL_CLOSE'      => array(
				'NAME'    => Loc::getMessage('API_TYPO_MESS_MODAL_CLOSE'),
				'PARENT'  => 'GROUP_MESSAGE',
				'TYPE'    => 'STRING',
				'DEFAULT' => Loc::getMessage('API_TYPO_MESS_MODAL_CLOSE_DEFAULT'),
		 ),
	 ),
);

?>
<style type='text/css'>
	.bxcompprop-content-table textarea{
		-webkit-box-sizing: border-box !important; -moz-box-sizing: border-box !important; box-sizing: border-box !important;
		width: 90% !important;
		min-height: 60px !important;
	}
</style>

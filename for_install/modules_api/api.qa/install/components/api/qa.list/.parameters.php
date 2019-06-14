<?
/**
 * @var array            $arCurrentValues
 * @var CUserTypeManager $USER_FIELD_MANAGER
 */

use Bitrix\Main\Loader,
	 Bitrix\Main\Localization\Loc;

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

Loc::loadMessages(__FILE__);

if(!Loader::includeModule('api.qa')) {
	ShowError(GetMessage('API_QA_LIST_MODULE_ERROR'));
	return;
}

if(!Loader::includeModule('iblock')) {
	ShowError(GetMessage('IBLOCK_MODULE_ERROR'));
	return;
}

use Api\QA\Tools;

//---------- Группы параметров стандартные ----------//
//BASE                  (сортировка 100). Основные параметры.
//DATA_SOURCE           (сортировка 200). Источник данных. Тип и ID инфоблока.
//VISUAL                (сортировка 300). Редко используемая группа. Сюда предполагается загонять параметры, отвечающие за внешний вид.
//URL_TEMPLATES         (сортировка 400). Шаблоны ссылок
//SEF_MODE              (сортировка 500). Группа для всех параметров, связанных с использованием ЧПУ.
//AJAX_SETTINGS         (сортировка 550). Все, что касается ajax.
//CACHE_SETTINGS        (сортировка 600). Появляется при указании параметра CACHE_TIME.
//ADDITIONAL_SETTINGS   (сортировка 700). Эта группа появляется, например, при указании параметра SET_TITLE.


$arComponentParameters = array(
	 'GROUPS'     => array(
			'FORM_QUESTION' => array(
				 'NAME' => Loc::getMessage('FORM_QUESTION'),
				 'SORT' => 1000,
			),
			'FORM_ANSWER'   => array(
				 'NAME' => Loc::getMessage('FORM_ANSWER'),
				 'SORT' => 1001,
			),
			'LIST_QUESTION' => array(
				 'NAME' => Loc::getMessage('LIST_QUESTION'),
				 'SORT' => 1002,
			),
			'PRIVACY'       => array(
				 'NAME' => Loc::getMessage('GROUP_PRIVACY'),
				 'SORT' => 1003,
			),
	 ),
	 'PARAMETERS' => array(

		 //BASE
		 'INCLUDE_CSS'                       => array(
				'PARENT'  => 'BASE',
				'NAME'    => Loc::getMessage('INCLUDE_CSS'),
				'TYPE'    => 'CHECKBOX',
				'DEFAULT' => 'Y',
		 ),
		 'ADMIN_EMAIL'                       => array(
				'PARENT'  => 'BASE',
				'NAME'    => Loc::getMessage('ADMIN_EMAIL'),
				'TYPE'    => 'STRING',
				'DEFAULT' => '',
		 ),
		 'ACTIVE'                            => array(
				'PARENT'  => 'BASE',
				'NAME'    => Loc::getMessage('ACTIVE'),
				'TYPE'    => 'LIST',
				'VALUES'  => Loc::getMessage('ACTIVE_VALUES'),
				'DEFAULT' => 'Y',
		 ),
		 'MESS_ACTIVE'                       => array(
				'PARENT'  => 'BASE',
				'NAME'    => Loc::getMessage('MESS_ACTIVE'),
				'ROWS'    => 4,
				'TYPE'    => 'STRING',
				'DEFAULT' => Loc::getMessage('MESS_ACTIVE_DEFAULT'),
		 ),

		 'ALLOW'                            => array(
			  'PARENT'  => 'BASE',
			  'NAME'    => Loc::getMessage('ALLOW'),
			  'TYPE'    => 'LIST',
			  'VALUES'  => Loc::getMessage('ALLOW_VALUES'),
			  'DEFAULT' => 'ALL',
		 ),
		 'MESS_ALLOW_USER'                       => array(
			  'PARENT'  => 'BASE',
			  'NAME'    => Loc::getMessage('MESS_ALLOW_USER'),
			  'ROWS'    => 4,
			  'TYPE'    => 'STRING',
			  'DEFAULT' => Loc::getMessage('MESS_ALLOW_USER_DEFAULT'),
		 ),
		 'MESS_ALLOW_EDITOR'                       => array(
			  'PARENT'  => 'BASE',
			  'NAME'    => Loc::getMessage('MESS_ALLOW_EDITOR'),
			  'ROWS'    => 4,
			  'TYPE'    => 'STRING',
			  'DEFAULT' => Loc::getMessage('MESS_ALLOW_EDITOR_DEFAULT'),
		 ),

		 'PAGE_TITLE'                        => array(
				'PARENT'  => 'BASE',
				'NAME'    => Loc::getMessage('PAGE_TITLE'),
				'TYPE'    => 'STRING',
				'DEFAULT' => '',
		 ),
		 'PAGE_URL'                          => array(
				'PARENT'  => 'BASE',
				'NAME'    => Loc::getMessage('PAGE_URL'),
				'TYPE'    => 'STRING',
				'DEFAULT' => '={$arResult["DETAIL_PAGE_URL"]}',
		 ),
		 'HASH'                              => array(
				'PARENT'  => 'BASE',
				'NAME'    => Loc::getMessage('HASH'),
				'DEFAULT' => '',
				'TYPE'    => 'STRING',
		 ),
		 'DATE_FORMAT'  => Tools::addDateParameters(Loc::getMessage('DATE_FORMAT'), 'BASE'),

		 //DATA_SOURCE
		 'IBLOCK_ID'                         => array(
				'PARENT'  => 'DATA_SOURCE',
				'NAME'    => Loc::getMessage('IBLOCK_ID'),
				'TYPE'    => 'STRING',
				'DEFAULT' => '={$arResult["IBLOCK_ID"]}',
		 ),
		 'ELEMENT_ID'                        => array(
				'PARENT'  => 'DATA_SOURCE',
				'NAME'    => Loc::getMessage('ELEMENT_ID'),
				'TYPE'    => 'STRING',
				'DEFAULT' => '={$arResult["ID"]}',
		 ),
		 'XML_ID'                            => array(
				'PARENT'  => 'DATA_SOURCE',
				'NAME'    => Loc::getMessage('XML_ID'),
				'TYPE'    => 'STRING',
				'DEFAULT' => '',
		 ),
		 'CODE'                              => array(
				'PARENT'  => 'DATA_SOURCE',
				'NAME'    => Loc::getMessage('CODE'),
				'TYPE'    => 'STRING',
				'DEFAULT' => '',
		 ),

		 //FORM_QUESTION
		 'FORM_QUESTION_MESS_TITLE'          => array(
				'TYPE'    => 'STRING',
				'PARENT'  => 'FORM_QUESTION',
				'NAME'    => Loc::getMessage('FORM_QUESTION_MESS_TITLE'),
				'DEFAULT' => Loc::getMessage('FORM_QUESTION_MESS_TITLE_DEFAULT'),
		 ),
		 'FORM_QUESTION_MESS_SUBMIT'         => array(
				'TYPE'    => 'STRING',
				'PARENT'  => 'FORM_QUESTION',
				'NAME'    => Loc::getMessage('FORM_QUESTION_MESS_SUBMIT'),
				'DEFAULT' => Loc::getMessage('FORM_QUESTION_MESS_SUBMIT_DEFAULT'),
		 ),
		 'FORM_QUESTION_MESS_SUBMIT_AJAX'    => array(
				'TYPE'    => 'STRING',
				'PARENT'  => 'FORM_QUESTION',
				'NAME'    => Loc::getMessage('FORM_QUESTION_MESS_SUBMIT_AJAX'),
				'DEFAULT' => Loc::getMessage('FORM_QUESTION_MESS_SUBMIT_AJAX_DEFAULT'),
		 ),
		 'FORM_QUESTION_MESS_REPLY'          => array(
				'TYPE'    => 'STRING',
				'PARENT'  => 'FORM_QUESTION',
				'NAME'    => Loc::getMessage('FORM_QUESTION_MESS_REPLY'),
				'DEFAULT' => Loc::getMessage('FORM_QUESTION_MESS_REPLY_DEFAULT'),
		 ),
		 'FORM_QUESTION_MESS_NAME'           => array(
				'TYPE'    => 'STRING',
				'PARENT'  => 'FORM_QUESTION',
				'NAME'    => Loc::getMessage('FORM_QUESTION_MESS_NAME'),
				'DEFAULT' => Loc::getMessage('FORM_QUESTION_MESS_NAME_DEFAULT'),
		 ),
		 'FORM_QUESTION_MESS_EMAIL'          => array(
				'TYPE'    => 'STRING',
				'PARENT'  => 'FORM_QUESTION',
				'NAME'    => Loc::getMessage('FORM_QUESTION_MESS_EMAIL'),
				'DEFAULT' => Loc::getMessage('FORM_QUESTION_MESS_EMAIL_DEFAULT'),
		 ),
		 'FORM_QUESTION_MESS_TEXT'           => array(
				'TYPE'    => 'STRING',
				'PARENT'  => 'FORM_QUESTION',
				'NAME'    => Loc::getMessage('FORM_QUESTION_MESS_TEXT'),
				'DEFAULT' => Loc::getMessage('FORM_QUESTION_MESS_TEXT_DEFAULT'),
		 ),

		 //FORM_ANSWER
		 'FORM_ANSWER_MESS_TITLE'            => array(
				'TYPE'    => 'STRING',
				'PARENT'  => 'FORM_ANSWER',
				'NAME'    => Loc::getMessage('FORM_ANSWER_MESS_TITLE'),
				'DEFAULT' => Loc::getMessage('FORM_ANSWER_MESS_TITLE_DEFAULT'),
		 ),
		 'FORM_ANSWER_MESS_SUBMIT'           => array(
				'TYPE'    => 'STRING',
				'PARENT'  => 'FORM_ANSWER',
				'NAME'    => Loc::getMessage('FORM_ANSWER_MESS_SUBMIT'),
				'DEFAULT' => Loc::getMessage('FORM_ANSWER_MESS_SUBMIT_DEFAULT'),
		 ),
		 'FORM_ANSWER_MESS_SUBMIT_AJAX'      => array(
				'TYPE'    => 'STRING',
				'PARENT'  => 'FORM_QUESTION',
				'NAME'    => Loc::getMessage('FORM_ANSWER_MESS_SUBMIT_AJAX'),
				'DEFAULT' => Loc::getMessage('FORM_ANSWER_MESS_SUBMIT_AJAX_DEFAULT'),
		 ),
		 'FORM_ANSWER_MESS_REPLY'            => array(
				'TYPE'    => 'STRING',
				'PARENT'  => 'FORM_ANSWER',
				'NAME'    => Loc::getMessage('FORM_ANSWER_MESS_REPLY'),
				'DEFAULT' => Loc::getMessage('FORM_ANSWER_MESS_REPLY_DEFAULT'),
		 ),
		 'FORM_ANSWER_MESS_NAME'             => array(
				'TYPE'    => 'STRING',
				'PARENT'  => 'FORM_ANSWER',
				'NAME'    => Loc::getMessage('FORM_ANSWER_MESS_NAME'),
				'DEFAULT' => Loc::getMessage('FORM_ANSWER_MESS_NAME_DEFAULT'),
		 ),
		 'FORM_ANSWER_MESS_EMAIL'            => array(
				'TYPE'    => 'STRING',
				'PARENT'  => 'FORM_ANSWER',
				'NAME'    => Loc::getMessage('FORM_ANSWER_MESS_EMAIL'),
				'DEFAULT' => Loc::getMessage('FORM_ANSWER_MESS_EMAIL_DEFAULT'),
		 ),
		 'FORM_ANSWER_MESS_TEXT'             => array(
				'TYPE'    => 'STRING',
				'PARENT'  => 'FORM_ANSWER',
				'NAME'    => Loc::getMessage('FORM_ANSWER_MESS_TEXT'),
				'DEFAULT' => Loc::getMessage('FORM_ANSWER_MESS_TEXT_DEFAULT'),
		 ),


		 //LIST_QUESTION
		 'LIST_QUESTION_MESS_LINK'           => array(
				'TYPE'    => 'STRING',
				'PARENT'  => 'LIST_QUESTION',
				'NAME'    => Loc::getMessage('LIST_QUESTION_MESS_LINK'),
				'DEFAULT' => Loc::getMessage('LIST_QUESTION_MESS_LINK_DEFAULT'),
		 ),
		 'LIST_QUESTION_MESS_CONFIRM_DELETE' => array(
				'TYPE'    => 'STRING',
				'PARENT'  => 'LIST_QUESTION',
				'NAME'    => Loc::getMessage('LIST_QUESTION_MESS_CONFIRM_DELETE'),
				'DEFAULT' => Loc::getMessage('LIST_QUESTION_MESS_CONFIRM_DELETE_DEFAULT'),
		 ),
		 'LIST_QUESTION_MESS_CONFIRM_ERASE'  => array(
				'TYPE'    => 'STRING',
				'PARENT'  => 'LIST_QUESTION',
				'NAME'    => Loc::getMessage('LIST_QUESTION_MESS_CONFIRM_ERASE'),
				'DEFAULT' => Loc::getMessage('LIST_QUESTION_MESS_CONFIRM_ERASE_DEFAULT'),
		 ),
		 'LIST_QUESTION_MESS_TEXT_ERASE'     => array(
				'TYPE'    => 'STRING',
				'PARENT'  => 'LIST_QUESTION',
				'NAME'    => Loc::getMessage('LIST_QUESTION_MESS_TEXT_ERASE'),
				'DEFAULT' => Loc::getMessage('LIST_QUESTION_MESS_TEXT_ERASE_DEFAULT'),
		 ),
		 'LIST_QUESTION_MESS_EXPERT'         => array(
				'TYPE'    => 'STRING',
				'PARENT'  => 'LIST_QUESTION',
				'NAME'    => Loc::getMessage('LIST_QUESTION_MESS_EXPERT'),
				'DEFAULT' => Loc::getMessage('LIST_QUESTION_MESS_EXPERT_DEFAULT'),
		 ),
		 'LIST_QUESTION_MESS_BUTTON_ANSWER'  => array(
				'TYPE'    => 'STRING',
				'PARENT'  => 'LIST_QUESTION',
				'NAME'    => Loc::getMessage('LIST_QUESTION_MESS_BUTTON_ANSWER'),
				'DEFAULT' => Loc::getMessage('LIST_QUESTION_MESS_BUTTON_ANSWER_DEFAULT'),
		 ),
		 'LIST_QUESTION_MESS_BUTTON_EDIT'    => array(
				'TYPE'    => 'STRING',
				'PARENT'  => 'LIST_QUESTION',
				'NAME'    => Loc::getMessage('LIST_QUESTION_MESS_BUTTON_EDIT'),
				'DEFAULT' => Loc::getMessage('LIST_QUESTION_MESS_BUTTON_EDIT_DEFAULT'),
		 ),
		 'LIST_QUESTION_MESS_BUTTON_SAVE'    => array(
				'TYPE'    => 'STRING',
				'PARENT'  => 'LIST_QUESTION',
				'NAME'    => Loc::getMessage('LIST_QUESTION_MESS_BUTTON_SAVE'),
				'DEFAULT' => Loc::getMessage('LIST_QUESTION_MESS_BUTTON_SAVE_DEFAULT'),
		 ),
		 'LIST_QUESTION_MESS_BUTTON_CANCEL'  => array(
				'TYPE'    => 'STRING',
				'PARENT'  => 'LIST_QUESTION',
				'NAME'    => Loc::getMessage('LIST_QUESTION_MESS_BUTTON_CANCEL'),
				'DEFAULT' => Loc::getMessage('LIST_QUESTION_MESS_BUTTON_CANCEL_DEFAULT'),
		 ),
		 'LIST_QUESTION_MESS_BUTTON_ERASE'   => array(
				'TYPE'    => 'STRING',
				'PARENT'  => 'LIST_QUESTION',
				'NAME'    => Loc::getMessage('LIST_QUESTION_MESS_BUTTON_ERASE'),
				'DEFAULT' => Loc::getMessage('LIST_QUESTION_MESS_BUTTON_ERASE_DEFAULT'),
		 ),
		 'LIST_QUESTION_MESS_BUTTON_DELETE'  => array(
				'TYPE'    => 'STRING',
				'PARENT'  => 'LIST_QUESTION',
				'NAME'    => Loc::getMessage('LIST_QUESTION_MESS_BUTTON_DELETE'),
				'DEFAULT' => Loc::getMessage('LIST_QUESTION_MESS_BUTTON_DELETE_DEFAULT'),
		 ),
		 //'CACHE_TIME' => Array('DEFAULT' => 86400),

		 //PRIVACY
		 'USE_PRIVACY'                       => Array(
				'PARENT'  => 'PRIVACY',
				'NAME'    => Loc::getMessage('USE_PRIVACY'),
				'TYPE'    => 'CHECKBOX',
				'REFRESH' => 'Y',
		 ),
	 ),
);

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

?>
<style type='text/css'>
	.bxcompprop-content-table textarea{
		-webkit-box-sizing: border-box !important; -moz-box-sizing: border-box !important; box-sizing: border-box !important; width: 90% !important; min-height: 60px !important;
	}
</style>


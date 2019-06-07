<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

/**
 * @var array            $arCurrentValues
 * @var CUserTypeManager $USER_FIELD_MANAGER
 */

use Bitrix\Main\Loader,
	 Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);


if(!Loader::includeModule('api.auth')) {
	ShowError(GetMessage('API_AUTH_MODULE_ERROR'));
	return;
}

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
			/*'LIST_QUESTION' => array(
				 'NAME' => Loc::getMessage('LIST_QUESTION'),
				 'SORT' => 1002,
			),*/
	 ),
	 'PARAMETERS' => array(

		 //BASE
		 /*'INCLUDE_CSS'                       => array(
				'PARENT'  => 'BASE',
				'NAME'    => Loc::getMessage('INCLUDE_CSS'),
				'TYPE'    => 'CHECKBOX',
				'DEFAULT' => 'Y',
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
		 'LIST_QUESTION_MESS_TEXT_DELETE' => array(
				'TYPE'    => 'STRING',
				'PARENT'  => 'LIST_QUESTION',
				'NAME'    => Loc::getMessage('LIST_QUESTION_MESS_TEXT_DELETE'),
				'DEFAULT' => Loc::getMessage('LIST_QUESTION_MESS_TEXT_DELETE_DEFAULT'),
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
		 'LIST_QUESTION_MESS_BUTTON_DELETE'  => array(
				'TYPE'    => 'STRING',
				'PARENT'  => 'LIST_QUESTION',
				'NAME'    => Loc::getMessage('LIST_QUESTION_MESS_BUTTON_DELETE'),
				'DEFAULT' => Loc::getMessage('LIST_QUESTION_MESS_BUTTON_DELETE_DEFAULT'),
		 ),
		 //'CACHE_TIME' => Array('DEFAULT' => 86400),*/
	 ),
);
<?php
/**
 * Bitrix vars
 *
 * @var CDatabase $DB
 * @var CUser     $USER
 * @var CMain     $APPLICATION
 *
 * @var           $DBType
 * @var           $adminMenu
 * @var           $adminPage
 *
 */

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$aMenu = array();

if(Loader::includeModule('api.qa')) {

	if($APPLICATION->GetGroupRight('api.qa') >= 'W') {

		//$ALL       = Api\QA\QuestionTable::getCount();
		//$MODERATED = Api\QA\QuestionTable::getCount(array('=ACTIVE' => 'N'));

		//$QUESTION = Api\QA\QuestionTable::getCount(array('=TYPE' => 'Q'));
		//$ANSWER   = Api\QA\QuestionTable::getCount(array('=TYPE' => 'A'));
		//$COMMENTS = Api\QA\QuestionTable::getCount(array('=TYPE' => 'C'));


		$aMenu = array(
			 'parent_menu' => 'global_menu_services',
			 'section'     => 'api_qa',
			 'sort'        => 100,
			 'text'        => Loc::getMessage('API_QA_MENU_TEXT'),
			 'icon'        => 'forum_menu_icon',
			 'page_icon'   => '',
			 'items_id'    => 'menu_qa',
			 'items'       => array(
				  array(
						 'text'      => Loc::getMessage('API_QA_MENU_ITEM_LIST'),
						 'url'       => 'api_qa_list.php?lang=' . LANGUAGE_ID,
						 'more_url'  => array('api_qa_edit.php'),
				  ),
				  /*array(
							'text'     => Loc::getMessage('API_QA_MENU_ITEM_ALL', array('#CNT#' => $ALL)),
							'url'      => 'api_qa_list.php?lang=' . LANGUAGE_ID,
							'more_url' => array('api_qa_edit.php'),
					 ),
					 array(
							'text' => Loc::getMessage('API_QA_MENU_ITEM_MODERATED', array('#CNT#' => $MODERATED)),
							'url'  => 'api_qa_list.php?lang=' . LANGUAGE_ID,
					 ),

					 array(
							'text' => Loc::getMessage('API_QA_MENU_ITEM_QUESTION', array('#CNT#' => $QUESTION)),
							'url'  => 'api_qa_list.php?lang=' . LANGUAGE_ID,
					 ),
					 array(
							'text' => Loc::getMessage('API_QA_MENU_ITEM_ANSWER', array('#CNT#' => $ANSWER)),
							'url'  => 'api_qa_list.php?lang=' . LANGUAGE_ID,
					 ),
					 array(
							'text' => Loc::getMessage('API_QA_MENU_ITEM_COMMENTS', array('#CNT#' => $COMMENTS)),
							'url'  => 'api_qa_list.php?lang=' . LANGUAGE_ID,
					 ),*/
			 ),
		);
	}
}

return $aMenu;
?>
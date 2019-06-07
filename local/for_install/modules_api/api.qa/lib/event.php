<?php
namespace Api\QA;

use Bitrix\Main\UserTable,
	 Bitrix\Main\Application,
	 Bitrix\Main\Config\Option,
	 Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Event
{

	public static function sendAdd($arItem)
	{
		$typeId = $arItem['TYPE'];
		$siteId = $arItem['SITE_ID'];

		//Макросы почтового шаблона
		$arFields = array(
			'ADMIN_EMAIL' => $arItem['ADMIN_EMAIL'],
			'EMAIL_FROM'  => '',
			'EMAIL_TO'    => '',
			'BCC'         => '',
			'SITE_NAME'   => '',
			'SITE_HOST'   => '',
			'ID'          => $arItem['ID'],
			'USER_ID'     => $arItem['USER_ID'],
			'GUEST_NAME'  => $arItem['GUEST_NAME'],
			'GUEST_EMAIL' => $arItem['GUEST_EMAIL'],
			'TEXT'        => $arItem['TEXT'],
			'LINK'        => '<a href="' . $arItem['URL'] . '">' . $arItem['URL'] . '</a>',
			'URL'         => $arItem['URL'],
			'PAGE_URL'    => $arItem['PAGE_URL'],
			'PAGE_TITLE'  => $arItem['PAGE_TITLE'],
			'AUTHOR_NAME' => '',
		);

		if($arItem['ACTIVE'] != 'Y'){
			$adminUrl = $arItem['HTTP_HOST'] . '/bitrix/admin/api_qa_edit.php?ID=' . $arItem['ID'] . '&lang='. LANGUAGE_ID;
			$arFields['LINK'] = '<a href="'.$adminUrl.'">'.$adminUrl.'</a>';
		}

		$bNotify = true;
		if($typeId != 'Q' && $arItem['PARENT_ID']) {
			$arAuthorQuestion = QuestionTable::getRow(array(
				 'select' => array('ID', 'USER_ID', 'NOTIFY', 'GUEST_NAME', 'GUEST_EMAIL'),
				 'filter' => array('=IBLOCK_ID' => $arItem['IBLOCK_ID'], '=ID' => $arItem['PARENT_ID']),
			));

			static::checkUserFields($arAuthorQuestion);

			$arFields['AUTHOR_NAME'] = $arAuthorQuestion['GUEST_NAME'];
			$arFields['EMAIL_TO']    = $arAuthorQuestion['GUEST_EMAIL'];

			if($arAuthorQuestion['NOTIFY'] != 'Y')
				$bNotify = false;
		}


		//Если пользователь согласен "Получать ответы на почту"
		if($bNotify){
			//static::checkElementFields($arFields, $arItem);

			if($typeId == 'C')
				$event = 'API_QA_COMMENT_ADD';
			elseif($typeId == 'A')
				$event = 'API_QA_ANSWER_ADD';
			else
				$event = 'API_QA_QUESTION_ADD';

			Event::send($event, $siteId, $typeId, $arFields);
		}
	}

	public static function send($event, $siteId, $typeId, $arFields)
	{
		$arSite = \CSite::GetList($by = 'sort', $order = 'desc', array('ID' => $siteId))->Fetch();

		$arFields['SITE_NAME']  = ($arSite['SITE_NAME'] ? $arSite['SITE_NAME'] : trim(Option::get('main', 'site_name', SITE_SERVER_NAME)));
		$arFields['EMAIL_FROM'] = ($arSite['EMAIL'] ? $arSite['EMAIL'] : trim(Option::get('main', 'email_from', 'info@' . SITE_SERVER_NAME)));
		$arFields['BCC']        = $arFields['EMAIL_FROM'];

		if($typeId == 'Q') {
			$arFields['EMAIL_TO'] = $arFields['ADMIN_EMAIL'] ? $arFields['ADMIN_EMAIL'] : $arFields['EMAIL_FROM'];
		}

		static::checkUserFields($arFields);

		return \CEvent::Send($event, $siteId, $arFields, 'Y');
	}

	/**
	 * Запускает события модуля и обработчики в init.php
	 *
	 * @param $event
	 * @param $parameters
	 */
	public static function execute($event, $parameters)
	{
		foreach(GetModuleEvents('api.qa', $event, true) as $arEvent) {
			ExecuteModuleEventEx($arEvent, $parameters);
		}
	}

	protected static function checkUserFields(&$arFields)
	{
		if($arFields['USER_ID']) {

			$arUser = UserTable::getRow(array(
				 'filter' => array('=ID' => $arFields['USER_ID']),
				 'select' => array('TITLE', 'NAME', 'LAST_NAME', 'SECOND_NAME', 'LOGIN', 'EMAIL'),
			));

			if(!$arFields['GUEST_NAME']) {
				$siteNameFormat         = \CSite::GetNameFormat(false);
				$arFields['GUEST_NAME'] = \CUser::FormatName($siteNameFormat, $arUser, true, true);
			}

			if(!$arFields['GUEST_EMAIL']) {
				$arFields['GUEST_EMAIL'] = trim($arUser['EMAIL']);
			}

			if(!$arFields['EMAIL_TO']) {
				$arFields['EMAIL_TO'] = trim($arUser['EMAIL']);
			}
		}

		return $arFields;
	}

	/*protected static function checkElementFields(&$arFields, $arItem)
	{
		$request  = Application::getInstance()->getContext()->getRequest();
		$httpHost = ($request->isHttps() ? 'https://' : 'http://') . $request->getHttpHost();

		$arFilter = array(
			 '=ACTIVE'    => 'Y',
			 '=IBLOCK_ID' => $arItem['IBLOCK_ID'],
		);

		if($arItem['ELEMENT_ID'])
			$arFilter['=ID'] = $arItem['ELEMENT_ID'];

		if($arItem['XML_ID'])
			$arFilter['=XML_ID'] = $arItem['XML_ID'];

		if($arItem['CODE'])
			$arFilter['=CODE'] = $arItem['CODE'];


		if(count($arFilter) >= 3) {
			$rsElement = \CIBlockElement::GetList(false, $arFilter, false, array('nTopCount' => 1), array('ID', 'NAME', 'DETAIL_PAGE_URL'));
			if($arElement = $rsElement->GetNext(false, false)) {

				//$pageUrl = $httpHost . $arElement['DETAIL_PAGE_URL'] . $arFields['LINK'];
				$pageUrl = $arFields['LINK'];

				$arFields['LINK']       = '<a href="' . $pageUrl . '">' . $pageUrl . '</a>';
				$arFields['URL']        = $pageUrl;
				$arFields['PAGE_URL']   = $httpHost . $arElement['DETAIL_PAGE_URL'];
				$arFields['PAGE_TITLE'] = $arElement['NAME'];
			}
		}

		return $arFields;
	}*/
}
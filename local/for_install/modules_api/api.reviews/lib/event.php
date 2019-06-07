<?php
namespace Api\Reviews;

use Bitrix\Main\UserTable,
		Bitrix\Main\Application,
		Bitrix\Main\Config\Option,
		Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Event
{

	protected static function checkUserFields(&$arFields)
	{
		if($userId = $arFields['USER_ID']){

			if(!$arFields['EMAIL_TO'] || !$arFields['USER_NAME']){
				$arUser = UserTable::getRow(array(
					 'filter' => array('=ID' => $userId),
					 'select' => array('TITLE', 'NAME', 'LAST_NAME', 'SECOND_NAME', 'LOGIN', 'EMAIL'),
				));

				if(!$arFields['USER_NAME']){
					$siteNameFormat        = \CSite::GetNameFormat(false);
					$arFields['USER_NAME'] = \CUser::FormatName($siteNameFormat, $arUser, true, true);
				}

				if(!$arFields['EMAIL_TO']){
					$arFields['EMAIL_TO'] = trim($arUser['EMAIL']);
				}
			}
		}

		return $arFields;
	}

	/**
	 * @param $event
	 * @param $siteId
	 * @param $arFields (EMAIL_TO + USER_ID + SITE_NAME + EMAIL_FROM)
	 *
	 * @return bool|int
	 */
	public static function send($event, $siteId, $arFields)
	{
		//SITE_NAME + SITE_HOST + EMAIL_FROM
		if(!$arFields['SITE_NAME'] || !$arFields['SITE_HOST'] || !$arFields['EMAIL_FROM']) {

			$request  = Application::getInstance()->getContext()->getRequest();
			$protocol = $request->isHttps() ? 'https://' : 'http://';

			$arSite = \CSite::GetList($by = 'sort', $order = 'desc', array('ID' => $siteId))->Fetch();

			if(!$arFields['SITE_NAME']) {
				$arFields['SITE_NAME'] = ($arSite['SITE_NAME'] ? $arSite['SITE_NAME'] : trim(Option::get('main', 'site_name', SITE_SERVER_NAME)));
			}

			if(!$arFields['SITE_HOST']) {
				$arFields['SITE_HOST'] = $protocol . ($arSite['SERVER_NAME'] ? $arSite['SERVER_NAME'] : trim(Option::get('main', 'server_name', SITE_SERVER_NAME)));
			}

			if(!$arFields['EMAIL_FROM']) {
				$arFields['EMAIL_FROM'] = ($arSite['EMAIL'] ? $arSite['EMAIL'] : trim(Option::get('main', 'email_from', 'info@' . SITE_SERVER_NAME)));
			}

			unset($arSite);
		}

		//EMAIL_TO
		if(!$arFields['EMAIL_TO'] && $arFields['USER_ID']) {

			$arUser = UserTable::getRow(array(
				 'filter' => array('=ID' => $arFields['USER_ID']),
				 'select' => array('TITLE', 'NAME', 'LAST_NAME', 'SECOND_NAME', 'LOGIN', 'EMAIL'),
			));

			$arFields['EMAIL_TO'] = trim($arUser['EMAIL']);
			unset($arUser);
		}

		return \CEvent::Send($event, $siteId, $arFields, 'Y');
	}

	public static function sendReply($reviewId, $arParams)
	{
		if(!$arParams['MESS_ADD_UNSWER_EVENT_THEME']){
			$arParams['MESS_ADD_UNSWER_EVENT_THEME'] = Loc::getMessage('ARLE_MESS_ADD_UNSWER_EVENT_THEME');
		}

		if(!$arParams['MESS_ADD_UNSWER_EVENT_TEXT']){
			$arParams['MESS_ADD_UNSWER_EVENT_TEXT'] = Loc::getMessage('ARLE_MESS_ADD_UNSWER_EVENT_TEXT');
		}

		$arReview = ReviewsTable::getRow(array(
			 'select' => array('GUEST_NAME', 'GUEST_EMAIL', 'USER_ID', 'SITE_ID', 'PAGE_URL', 'PAGE_TITLE', 'RATING'),
			 'filter' => array('=ID' => $reviewId),
		));

		if($arReview) {

			$arReview['PAGE_URL']   = ($arParams['PAGE_URL'] ? $arParams['PAGE_URL'] : $arReview['PAGE_URL']);
			$arReview['PAGE_TITLE'] = ($arParams['PAGE_TITLE'] ? $arParams['PAGE_TITLE'] : $arReview['PAGE_TITLE']);

			$arFields = array(
				 'EMAIL_TO'   => trim($arReview['GUEST_EMAIL']),
				 'USER_ID'    => (int)$arReview['USER_ID'],
				 'USER_NAME'  => trim($arReview['GUEST_NAME']),
				 'THEME'      => trim($arParams['MESS_ADD_UNSWER_EVENT_THEME']),
				 'ID'         => $reviewId,
				 'RATING'     => $arReview['RATING'],
				 'LINK'       => '<br><a href="'. $arReview['PAGE_URL'] .'">'. $arReview['PAGE_URL'] .'</a>',
				 'PAGE_URL'   => $arReview['PAGE_URL'],
				 'PAGE_TITLE' => $arReview['PAGE_TITLE'],
				 'WORK_AREA'  => Tools::formatText($arParams['MESS_ADD_UNSWER_EVENT_TEXT'], false),
			);

			static::checkUserFields($arFields);

			foreach($arFields as $key=>$val){
				$arFields['THEME'] = str_replace('#'.$key.'#', $val, $arFields['THEME']);
				$arFields['WORK_AREA'] = str_replace('#'.$key.'#', $val, $arFields['WORK_AREA']);
			}

			if(Event::send('API_REVIEWS_REPLY', $arReview['SITE_ID'], $arFields))
				ReviewsTable::update($reviewId, array('REPLY_SEND' => 'Y'));
		}
	}

	public static function sendAdd($id, $fields, $arParams)
	{
		$arFields = array(
			 'EMAIL_TO'   => trim($arParams['EMAIL_TO']),
			 'USER_ID'    => (int)$fields['USER_ID'],
			 'USER_NAME'  => trim($fields['GUEST_NAME']),
			 'THEME'      => trim($arParams['MESS_ADD_REVIEW_EVENT_THEME']),
			 'ID'         => $id,
			 'RATING'     => $fields['RATING'],
			 'LINK'       => '<br><a href="'. $fields['PAGE_URL'] .'">'. $fields['PAGE_URL'] .'</a>',
			 'LINK_ADMIN' => '<br><a href="'. $arParams['ADMIN_URL'] .'">'. $arParams['ADMIN_URL'] .'</a>',
			 'PAGE_URL'   => trim($fields['PAGE_URL']),
			 'PAGE_TITLE' => trim($fields['PAGE_TITLE']),
			 'WORK_AREA'  => Tools::formatText($arParams['MESS_ADD_REVIEW_EVENT_TEXT'], false),
		);

		static::checkUserFields($arFields);

		foreach($arFields as $key=>$val){
			$arFields['THEME']     = str_replace('#'.$key.'#', $val, $arFields['THEME']);
			$arFields['WORK_AREA'] = str_replace('#'.$key.'#', $val, $arFields['WORK_AREA']);
		}


		Event::execute('onAfterReviewAddEventSend', array($id, &$arFields, $arParams));
		if(Event::send('API_REVIEWS_ADD', $arParams['SITE_ID'], $arFields)){
			if($arParams['USE_SUBSCRIBE'] == 'Y' && $fields['ACTIVE'] == 'Y'){
				ReviewsTable::update($id, array('SUBSCRIBE_SEND' => 'Y'));
			}
		}
	}


	//---------- Events ----------//

	/**
	 * Запускает события модуля и обработчики в init.php
	 *
	 * @param $event
	 * @param $parameters
	 */
	public static function execute($event, $parameters)
	{
		foreach(GetModuleEvents('api.reviews', $event, true) as $arEvent) {
			ExecuteModuleEventEx($arEvent, $parameters);
		}
	}

	/**
	 * После добавления отзыва запишет в базу Адрес страницы с ЧПУ отзыва и Заголовок страницы
	 *
	 * @param $id
	 * @param $arFields
	 * @param $arParams
	 */
	public static function onAfterReviewAdd($id, $arFields, $arParams){
		if($arFields['PAGE_URL'] || $arFields['PAGE_TITLE']){
			ReviewsTable::update($id, array(
				 'PAGE_URL'   => $arFields['PAGE_URL'],
				 'PAGE_TITLE' => $arFields['PAGE_TITLE'],
			));
		}
	}
}
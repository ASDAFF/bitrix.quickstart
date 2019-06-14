<?php

namespace Api\Mail;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Event
{
	//bitrix/modules/main/lib/mail/event.php::handleEvent() + $result = Main\Mail\Mail::send()
	//bitrix/modules/main/lib/mail/mail.php::send($mailParams)
	public static function OnBeforeMailSend(\Bitrix\Main\Event $event)
	{
		$params = $event->getParameters()[0];

		if(isset($params['BODY']) && !$params['TRACK_READ']) {
			$allSettings = SettingsTable::getFromFile();

			$siteId     = null;
			$serverName = $params['LINK_DOMAIN'] ? $params['LINK_DOMAIN'] : $_SERVER['SERVER_NAME'];
			if($serverName) {
				$siteList = SettingsTable::getSiteList();
				$siteId   = $siteList[ $serverName ];
			}

			if($siteId) {
				if($settings = $allSettings[ $siteId ]) {
					if($settings['MAIL_ON'] == 'Y') {

						if(Tools::isText($params['BODY'])){
							$params['CONTENT_TYPE'] = 'html';
							$params['BODY'] = nl2br($params['BODY']);
						}

						$params['BODY'] = str_replace('#WORK_AREA#', $params['BODY'], $settings['MAIL_HTML']);
					}
				}
			}
		}

		$event->addResult(
			 new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, $params)
		);
	}

	public static function OnBeforeEventAdd(&$event, &$lid, &$arFields, &$message_id = '', &$files = array(), &$languageId = '')
	{
		/*if($arFields['AOS_MAIL_REPLACE'] != 'Y') {
			$siteId      = $lid;
			$allSettings = SettingsTable::getFromFile();

			if(!array_key_exists($siteId, $allSettings)) {
				if($_SERVER['SERVER_NAME']) {
					$siteList = SettingsTable::getSiteList();
					$siteId   = $siteList[ $_SERVER['SERVER_NAME'] ];
				}
			}

			if($settings = $allSettings[ $siteId ]) {
				if($settings['MAIL_ON'] == 'Y') {
					$arFields['AM_MAIL_REPLACE'] = 'Y';
					$arFields['AM_MAIL_HTML']    = $settings['MAIL_HTML'];
				}
			}
		}*/

		//$ttfile=dirname(__FILE__).'/2_txt.php';
		//file_put_contents($ttfile, "<pre>".print_r($arFields,1)."</pre>\n");
	}

	//bitrix/modules/main/lib/mail/eventmessagecompiler.php::setMailHeaders()
	//bitrix/modules/main/lib/mail/event.php::handleEvent()
	public static function OnBeforeEventSend(&$arFields, &$eventMessage)
	{
		/*if($eventMessage['BODY_TYPE'] == 'text') {
			//$eventMessage['MESSAGE'] = str_replace(PHP_EOL, "<br />\n", $eventMessage['MESSAGE']);
			$eventMessage['MESSAGE']   = nl2br($eventMessage['MESSAGE']);
			$eventMessage['BODY_TYPE'] = 'html';
		}*/

		/*if($arFields['AM_MAIL_REPLACE'] == 'Y') {

			if($eventMessage['BODY_TYPE'] == 'text') {
				//$eventMessage['MESSAGE'] = str_replace(PHP_EOL, "<br />\n", $eventMessage['MESSAGE']);
				$eventMessage['MESSAGE']   = nl2br($eventMessage['MESSAGE']);
				$eventMessage['BODY_TYPE'] = 'html';
			}

			if($arFields['AM_MAIL_HTML']) {
				$eventMessage['MESSAGE']     = str_replace('#WORK_AREA#', $eventMessage['MESSAGE'], $arFields['AM_MAIL_HTML']);
				$eventMessage['MESSAGE_PHP'] = $eventMessage['MESSAGE'];
			}
		}*/

		//$ttfile=dirname(__FILE__).'/3_txt.php';
		//file_put_contents($ttfile, "<pre>".print_r($arFields,1)."</pre>\n");
	}
}

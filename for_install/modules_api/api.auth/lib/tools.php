<?php

namespace Api\Auth;

use \Bitrix\Main\HttpRequest,
	 \Bitrix\Main\Application,
	 \Bitrix\Main\UserConsent,
	 \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Tools
{
	public static function getDeleteParameters()
	{
		$params = array_merge(
			 array(
					'reg',
					'confirm',
					'restore',
					'change',
					'lang',
					'USER_CHECKWORD',
					'USER_LOGIN',
			 ),
			 HttpRequest::getSystemParameters()
		);

		return $params;
	}
	public static function setjQuery()
	{
		static $cfg;

		if(!$cfg) {
			$cfg = SettingsTable::getRow(array(
				 'filter' => array('=NAME' => 'USE_JQUERY'),
			));
		}

		if($useJquery = $cfg['VALUE']) {
			if($useJquery == '1.8')
				\CJSCore::Init(array('jquery'));

			if($useJquery == '2.1')
				\CJSCore::Init(array('jquery2'));
		}
	}
	public static function isSerialize($str)
	{
		if($str == '')
			return false;

		//preg_match('/s:([0-9]+):\"(.*?)\";/', $str); //Don't know empty array a:0:{}
		return preg_match('/a:([0-9]+):{(.*?)}/i', $str);
	}

	//---------- UserConsent ----------//
	public static function getUserConsent($arAgreement, $strButton, $strFields)
	{
		$result = array();

		if($arAgreement) {
			foreach($arAgreement as $agreementId) {

				$agreement = new UserConsent\Agreement($agreementId);

				if($agreement->isExist() && $agreement->isActive()) {

					$arReplace = array(
						 "button_caption" => $strButton,
						 "fields"         => $strFields,
					);

					$agreement->setReplace($arReplace);

					$agreementData = $agreement->getData();

					$config = array(
						 'id'       => $agreementId,
						 'sec'      => $agreementData['SECURITY_CODE'],
						 'autoSave' => 'N',
						 'replace'  => $arReplace,
					);

					$result[ $agreementId ] = array(
						 'ID'         => $agreementId,
						 'LABEL_TEXT' => $agreement->getLabelText(),
						 'CONFIG'     => $config,
						 'USER_VALUE' => '',
						 'ERROR'      => '',
					);
				}
			}

			unset($arAgreement, $agreementId, $agreement, $arReplace, $config);
		}

		return $result;
	}
	public static function logUserConsent($arAgreement, $originId)
	{
		$context = Application::getInstance()->getContext();
		$request = $context->getRequest();

		$httpHost = ($request->isHttps() ? 'https://' : 'http://') . $request->getHttpHost();

		foreach($arAgreement as $agreementId) {
			UserConsent\Consent::addByContext(
				 $agreementId,
				 $request->getHttpHost(),
				 $originId,
				 array(
						'IP'  => $request->getRemoteAddress(),
						'URL' => $httpHost . $request->getRequestUri(),
				 )
			);
		}
	}
}
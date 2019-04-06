<?php

namespace Api\Auth;

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Rsa extends \CRsaSecurity
{
	public function getFormData($formid, $arParams)
	{
		$formid = preg_replace('/[^a-z0-9_]/is', '', $formid);

		if(!isset($_SESSION['__STORED_RSA_RAND']))
			$_SESSION['__STORED_RSA_RAND'] = $this->GetNewRsaRand();

		$arSafeParams = array();
		foreach($arParams as $param)
			$arSafeParams[] = preg_replace('/[^a-z0-9_\\[\\]]/is', '', $param);

		$arData = array(
			 'formid'   => $formid,
			 'key'      => $this->provider->GetPublicKey(),
			 'rsa_rand' => $_SESSION['__STORED_RSA_RAND'],
			 'params'   => $arSafeParams,
		);

		\CJSCore::Init();
		$GLOBALS['APPLICATION']->AddHeadScript('/bitrix/js/main/rsasecurity.js');

		return $arData;
	}
}
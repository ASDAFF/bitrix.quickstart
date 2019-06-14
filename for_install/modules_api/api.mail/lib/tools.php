<?php

namespace Api\Mail;

use Bitrix\Main\HttpRequest,
	 Bitrix\Main\Localization\Loc;

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

	public static function isSerialize($str)
	{
		if($str == '')
			return false;

		//preg_match('/s:([0-9]+):\"(.*?)\";/', $str); //Don't know empty array a:0:{}
		return preg_match('/a:([0-9]+):{(.*?)}/i', $str);
	}

	public static function isText($str){
		if($str == '')
			return false;

		return !preg_match('/<[\/\!]*?[^<>]*?>/is',$str);
	}
}
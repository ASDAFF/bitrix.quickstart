<?php
namespace Api\OrderStatus;

use Bitrix\Main\Error;
use Api\OrderStatus\Sender;

//use Api\OrderStatus\SmsGatewayTable;
//use Api\OrderStatus\SmsHistoryTable;
use Bitrix\Main\Entity\Result;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class SMS
{

	public static function getBalance($params)
	{
		$result  = false;
		$gateway = $params['GATEWAY'];


		if($gateway == 'Devinotele') {
			$result = Sender\Devinotele::getBalance($params);
		}
		elseif($gateway == 'Redsms') {
			$result = Sender\Redsms::getBalance($params);
		}
		elseif($gateway == 'Redsms3') {
			$result = Sender\Redsms3::getBalance($params);
		}
		elseif($gateway == 'Turbosms') {
			$result = Sender\Turbosms::getBalance($params);
		}
		elseif($gateway == 'Smsclub') {
			$result = Sender\Smsclub::getBalance($params);
		}
		elseif($gateway == 'Smsru') {
			$result = Sender\Smsru::getBalance($params);
		}
		elseif($gateway == 'Smsint') {
			$result = Sender\Smsint::getBalance($params);
		}


		$result = Loc::getMessage('AOS_LSMS_BALANCE') . $result;

		return $result;
	}

	public static function sendImmediate($phone, $message, $siteId, $params)
	{
		$result  = false;
		$gateway = $params['GATEWAY'];


		//if(!Application::isUtfMode())
		//$message = Main\Text\Encoding::convertEncoding($message, 'UTF-8', $context->getCulture()->getCharset());


		if($gateway == 'Devinotele') {
			$result = Sender\Devinotele::send($phone, $message, $siteId, $params);
		}
		elseif($gateway == 'Redsms') {
			$result = Sender\Redsms::send($phone, $message, $siteId, $params);
		}
		elseif($gateway == 'Redsms3') {
			$result = Sender\Redsms3::send($phone, $message, $siteId, $params);
		}
		elseif($gateway == 'Turbosms') {
			$result = Sender\Turbosms::send($phone, $message, $siteId, $params);
		}
		elseif($gateway == 'Smsclub') {
			$result = Sender\Smsclub::send($phone, $message, $siteId, $params);
		}
		elseif($gateway == 'Smsru') {
			$result = Sender\Smsru::send($phone, $message, $siteId, $params);
		}
		elseif($gateway == 'Smsint') {
			$result = Sender\Smsint::send($phone, $message, $siteId, $params);
		}

		return $result;
	}

	public static function send($phone, $message, $siteId)
	{
		$result = new Result();

		$arGateway = SmsGatewayTable::getList(array(
			 'order'  => array('SORT' => 'ASC', 'ID' => 'ASC'),
			 'filter' => array('ACTIVE' => 'Y'),
		))->fetchAll();

		if($arGateway) {
			$smsId     = null;
			$gatewayId = 0;
			$errors    = '';
			foreach($arGateway as $gateway) {
				$errors    = '';
				$gatewayId = $gateway['ID'];
				$params    = unserialize($gateway['PARAMS']);

				$res = self::sendImmediate($phone, $message, $siteId, $params);
				if($res->isSuccess()) {
					$data  = $res->getData();
					$smsId = join("<br>", $data);;

					break;
				}
				else {
					$errors = join("<br>", $res->getErrorMessages());
				}
			}

			if(!$smsId && !$errors) {
				$errors .= "<br>" . Loc::getMessage('AOS_LSMS_SEND_ERROR');
			}

			if($errors)
				$result->addError(new Error($errors));

			$result->setData(array(
				 'SMS_ID'     => $smsId,
				 'SMS_ERROR'  => $errors,
				 'GATEWAY_ID' => $gatewayId,
			));
		}
		else {
			$result->setData(array(
				 'SMS_ID'     => 0,
				 'SMS_ERROR'  => Loc::getMessage('AOS_LSMS_GATEWAY_ERROR'),
				 'GATEWAY_ID' => 0,
			));
		}

		return $result;
	}
}
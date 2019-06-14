<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage mlife.smsservices
 * @copyright  2015 Zahalski Andrew
 */

namespace Mlife\Smsservices;

class Agent {
	
	public static function turnSms() {
		$ob = new \Mlife\Smsservices\Sender();
		$ob->getTurnSms();
		return '\\Mlife\\Smsservices\\Agent::turnSms();';
	}

	public static function statusSms() {
		$ob = new \Mlife\Smsservices\Sender();
		$ob->getStatusSms();
		return '\\Mlife\\Smsservices\\Agent::statusSms();';
	}
	
}
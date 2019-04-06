<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/mcart.extramail/classes/general/Net/SMTP.php');


function custom_mail($to, $subject, $message, $additionalHeaders = '')
{
$f=fopen($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mcart.extramail/log.txt", "a+");

	if (COption::GetOptionString("mcart.extramail","smtp_use") == "Y")
	{

		$smtpServerHost         = COption::GetOptionString("mcart.extramail","smtp_host");
		$smtpServerHostPort     = COption::GetOptionString("mcart.extramail","smtp_port");
		$smtpServerUser         = COption::GetOptionString("mcart.extramail","smtp_login");
		$smtpServerUserPassword = COption::GetOptionString("mcart.extramail","smtp_password");



   		if (!($smtp = new Net_SMTP($smtpServerHost, $smtpServerHostPort))) {
				fwrite($f, $e->getMessage()."\n========\n");
		      return false;
		}

   		if (PEAR::isError($e = $smtp->connect())) {fwrite($f, $e->getMessage()."\n========\n");
      		return false;
   		}

		if (PEAR::isError($e = $smtp->auth($smtpServerUser, $smtpServerUserPassword))) {fwrite($f, $e->getMessage()."\n========\n");
		return false;
		}
		fclose($f);
		preg_match('/From: (.+)\n/i', $additionalHeaders, $matches);
		list(, $from) = $matches;

		$smtp->mailFrom($from);
		$smtp->rcptTo($to);

		
		$eol = CAllEvent::GetMailEOL();

		$additionalHeaders .= $eol . 'Subject: ' . $subject;

		if (PEAR::isError($e = $smtp->data($additionalHeaders . "\r\n\r\n" . $message))) {
		return false;
		}

		$smtp->disconnect();

   		return true;
	}
}
?>
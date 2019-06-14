<?

if(!function_exists('custom_mail')) {
	function custom_mail($to, $subject, $message, $additional_headers = "", $additional_parameters = "", \Bitrix\Main\Mail\Context $context = null)
	{
		/*if(empty($siteID) && defined('SITE_ID') && preg_match('/^[A-Za-z0-9_]{2}$/', SITE_ID) === 1) {
			$siteID = SITE_ID;
		}*/

		$siteID      = SITE_ID;
		$allSettings = Api\Mail\SettingsTable::getFromFile();

		if(!array_key_exists($siteID, $allSettings)) {
			if($_SERVER['SERVER_NAME']) {
				$siteList = Api\Mail\SettingsTable::getSiteList();
				$siteID   = $siteList[ $_SERVER['SERVER_NAME'] ];
			}
		}

		$settings = $allSettings[ $siteID ];

		if($settings['DKIM_ON'] == 'Y') {
			$mailParams = array(
				 'TO'      => $to,
				 'SUBJECT' => $subject,
				 'MESSAGE' => $message,
				 'HEADERS' => $additional_headers,
				 //'PARAMETERS' => $additional_parameters,
			);

			$signer = new Api\Mail\Signer(array(
				 'PRIVATE_KEY' => $settings['RSA_PRIVATE_KEY'],
				 'PUBLIC_KEY'  => $settings['RSA_PUBLIC_KEY'],
				 'DKIM'        => $settings['DKIM_KEYS'],
				 'MAIL'        => $mailParams,
				 'DEBUG_DATA'  => false,
			));

			// Create DKIM-Signature Header
			$dkim               = $signer->getDKIM();
			$additional_headers = $dkim . $additional_headers;
		}
		unset($allSettings, $settings, $mailParams, $signer, $dkim, $siteID, $srvName);


		if($additional_parameters != "")
			return @mail($to, $subject, $message, $additional_headers, $additional_parameters);

		return @mail($to, $subject, $message, $additional_headers);
	}
}

?>
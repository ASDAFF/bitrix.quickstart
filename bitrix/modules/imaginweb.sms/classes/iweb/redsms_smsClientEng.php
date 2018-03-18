<?php

include_once("redsms_pest.php");

/**
 * Devino REST API SMS client
 *
 * Client can be used as static functions collection or like ordinary class.
 * Static methods has _St suffix. Session ID should be stored somewhere.
 * 
 * Class stores session ID inside itself.
 *
 */
class SMSClient {

	//////////////////////////////// Static methods ////////////////////////////////

	/**
	 * Root REST requests URL
	 * @const
	 */
	const m_baseURL = "https://integrationapi.net/rest";

	/**
	 * Session ID Queue
	 *
	 * @access public
	 * @static
	 *
	 * @param string $login User name
	 * @param string $password Password
	 *
	 * @return string Session ID
	 * @throws SMSError_Exception
	 */
	public static function getSessionID_St( $login, $password ) {
		$pest = new Pest(SMSClient::m_baseURL);
		$sessionID = "";
		try {
			$sessionID = str_replace('"', '',
				$pest->get('/User/SessionId?login='.$login.'&password='.$password)
			);
		} catch( Exception $e ) {
			$errorInfo = json_decode($e->getMessage());
			unset($pest);			
			throw( new SMSError_Exception( $errorInfo->Desc, $errorInfo->Code));
		}
		unset($pest);			
		return $sessionID;
	}
	
	/**
	 * User balance queue
	 *
	 * @access public
	 * @static	 
	 *
	 * @param string $sessionID Session ID. @see getSessionID_St
	 *
	 * @return double Balance
	 * @throws SMSError_Exception
	 */
	public static function getBalance_St( $sessionID ) {
		$pest = new Pest(SMSClient::m_baseURL);
		$balance = 0;
		try {
			$balance = str_replace('"', '',
				$pest->get('/User/Balance?sessionId='.$sessionID)
			);
		} catch( Exception $e ) {
			$errorInfo = json_decode($e->getMessage());
			unset($pest);			
			throw( new SMSError_Exception( $errorInfo->Desc, $errorInfo->Code));
		}
		unset($pest);			
		return $balance;
	}
	
	/**
	 * SMS send
	 *
	 * @access public
	 * @static
	 *
	 * @param string  $sessionID Session ID. @see getSessionID_St
	 * @param string  $sourceAddres sender name(up to 11 chars) or phone number(up to 15 digits)
	 * @param string  $destinationAddress destination phone. Country code+cellular code+phone number, E.g. 79031234567
	 * @param string  $data message
	 * @param mixed   $sendDate Delayed message send date and time. String format YYYY-MM-DDTHH:MM:SS or integer Timestamp. Optional.
	 * @param integer $validity Message life time in minutes. Optional.
	 * 
	 * @return array SMS IDs
	 * @throws SMSError_Exception
	 */
	public static function send_St( $sessionID, $sourceAddres, $destinationAddress, $data, $sendDate=null, $validity=0 ) {
		$pest = new Pest(SMSClient::m_baseURL);
		$result = array();
		try {
			$result = json_decode($pest->post('/Sms/Send', 
				SMSClient::createRequestParameters( $sessionID, $sourceAddres, $destinationAddress, $data, $sendDate, $validity )
			),true);
		} catch( Exception $e ) {
			$errorInfo = json_decode($e->getMessage());
			unset($pest);			
			throw( new SMSError_Exception( $errorInfo->Desc, $errorInfo->Code));
		}
		unset($pest);			
		return $result;
	}
	
	/**
	 * SMS send with receiver's local time.
	 *
	 * @access public
	 * @static
	 *
	 * @param string  $sessionID Session ID. @see getSessionID_St
	 * @param string  $sourceAddres sender name(up to 11 chars) or phone number(up to 15 digits)
	 * @param string  $destinationAddress destination phone. Country code+cellular code+phone number, E.g. 79031234567
	 * @param string  $data message
	 * @param mixed   $sendDate Delayed message send date and time in receiver's local time. String format YYYY-MM-DDTHH:MM:SS or integer Timestamp.
	 * @param integer $validity Message life time in minutes. Optional.
	 * 
	 * @return array SMS IDs
	 * @throws SMSError_Exception
	 */
	public static function sendByTimeZone_St( $sessionID,$sourceAddres, $destinationAddress, $data, $sendDate, $validity=0 ) {
		$pest = new Pest(SMSClient::m_baseURL);
		$result = array();
		try {
			$result = json_decode($pest->post('/Sms/SendByTimeZone', 
				SMSClient::createRequestParameters( $sessionID, $sourceAddres, $destinationAddress, $data, $sendDate, $validity )
			),true);
		} catch( Exception $e ) {
			$errorInfo = json_decode($e->getMessage());
			unset($pest);			
			throw( new SMSError_Exception( $errorInfo->Desc, $errorInfo->Code));
		}
		unset($pest);			
		return $result;
	}
	
	/**
	 * SMS send to several receivers
	 *
	 * @access public
	 * @static
	 *
	 * @param string  $sessionID Session ID. @see getSessionID_St
	 * @param string  $sourceAddres sender name(up to 11 chars) or phone number(up to 15 digits)
	 * @param array  $destinationAddresses destination phones. Country code+cellular code+phone number, E.g. 79031234567
	 * @param string  $data message
	 * @param mixed   $sendDate Delayed message send date and time. String format YYYY-MM-DDTHH:MM:SS or integer Timestamp. Optional.
	 * @param integer $validity Message life time in minutes. Optional.
	 * 
	 * @return array SMS IDs
	 * @throws SMSError_Exception
	 */	
	public static function sendBulk_St( $sessionID,$sourceAddres, $destinationAddresses, $data, $sendDate=null, $validity=0 ) {
	
		if (gettype($destinationAddresses) == "string") {
			$destinationAddresses = array($destinationAddresses);
		}
	
		$pest = new Pest(SMSClient::m_baseURL);
		$result = array();
		try {
			$result = json_decode($pest->post('/Sms/SendBulk', 
				SMSClient::createRequestParameters( $sessionID, $sourceAddres, $destinationAddresses, $data, $sendDate, $validity )
			),true);
		} catch( Exception $e ) {
			$errorInfo = json_decode($e->getMessage());
			unset($pest);			
			throw( new SMSError_Exception( $errorInfo->Desc, $errorInfo->Code));
		}
		unset($pest);			
		return $result;
	}
	
	/**
	 * SMS Status queue
	 *
	 * @access public
	 * @static
	 *
	 * @param string $sessionID Session ID. @see getSessionID_St
	 * @param string $messageID Message ID
	 *
	 * @return array массив полей:
	 *		State	- message status. @see SMSClientSMSStatus
	 *		TimeStampUtc		- Date and time of status update
	 *		StateDescription	- Status description
	 *		CreationDateUtc		- Creation date and time
	 *		SubmittedDateUtc	- Submit date and time
	 *		ReportedDateUtc		- Delivery date and time 
	 *		Price				- message price
	 * @throws SMSError_Exception
	 */
	public static function getSMSState_St( $sessionID, $messageID ) {
		$pest = new Pest(SMSClient::m_baseURL);
		$result = array(
			'State' => SMSClientSMSStatus::SMS_STATUS_Unknown,
			'TimeStampUtc' => time(),
			'StateDescription' => '',
			'CreationDateUtc' => null,
			'SubmittedDateUtc' => null,
			'ReportedDateUtc' => null,
			'Price' => null);
		try {
			$result = json_decode($pest->get('/Sms/State?sessionId='.$sessionID.'&messageId='.$messageID),true);
			$result['TimeStampUtc'] = substr(substr($result['TimeStampUtc'],6),0,-2) ;
		} catch( Exception $e ) {
			$errorInfo = json_decode($e->getMessage());
			unset($pest);			
			throw( new SMSError_Exception( $errorInfo->Desc, $errorInfo->Code));
		}
		unset($pest);			
		return $result;
	}
	
	/**
	 * Inbox SMS queue
	 *
	 * @access public
	 * @static
	 * 
	 * @param string $sessionID Session ID. @see getSessionID_St
	 * @param mixed  $minDateUTC queue interval start date. String with format YYYY-MM-DDTHH:MM:SS or integer Timestamp
	 * @param mixed  $maxDateUTC queue interval end date. String with format YYYY-MM-DDTHH:MM:SS or integer Timestamp
	 *
	 * @return array Array with fields:
	 * 		string Data				- Message text
	 *		string SourceAddress	- Sender Name or Phone
	 *		string DestinationAddress	- Receiver's phone
	 *		string ID	- Message ID
	 * @throws SMSError_Exception
	 */
	public static function getInbox_St( $sessionID, $minDateUTC, $maxDateUTC ) {
		$requestString = '/Sms/In?sessionId='.$sessionID;
		
		if (gettype($minDateUTC) == "string") {
			$requestString .= '&minDateUTC='.$minDateUTC;
		} else if (gettype($minDateUTC) == "integer") {
			$requestString .= '&minDateUTC='.date("Y-m-d",$minDateUTC).'T'.date("H:i:s",$minDateUTC);
		}
		
		if (gettype($maxDateUTC) == "string") {
			$requestString .= '&maxDateUTC='.$maxDateUTC;
		} else if (gettype($maxDateUTC) == "integer") {
			$requestString .= '&maxDateUTC='.date("Y-m-d",$maxDateUTC).'T'.date("H:i:s",$maxDateUTC);
		}

		$pest = new Pest(SMSClient::m_baseURL);
		
			$result = array(
				'Data' => '',
				'SourceAddress' => '',
				'DestinationAddress' => '',
				'ID' => null);
		try {
			$result = json_decode($pest->get($requestString),true);
		} catch( Exception $e ) {
			$errorInfo = json_decode($e->getMessage());
			unset($pest);			
			throw( new SMSError_Exception( $errorInfo->Desc, $errorInfo->Code));
		}
		unset($pest);
		
		return $result;
	}
	
	/**
	 * SMS distribution statistics
	 *
	 * @access public
	 * @static	 
	 *
	 * @param string $sessionID Session ID. @see getSessionID_St
	 * @param mixed  $startDate queue interval start date. String with format YYYY-MM-DDTHH:MM:SS or integer Timestamp
	 * @param mixed  $stopDate queue interval end date. String with format YYYY-MM-DDTHH:MM:SS or integer Timestamp
	 *
	 * @return array Array with statistics
	 * @throws SMSError_Exception
	 */
	public static function getStatistics_St( $sessionID, $startDate, $stopDate ) {
		$requestString = '/Sms/Statistics?sessionId='.$sessionID;

		if (gettype($startDate) == "string") {
			$requestString .= '&startDateTime='.$startDate;
		} else if (gettype($startDate) == "integer") {
			$requestString .= '&startDateTime='.date("Y-m-d",$startDate).'T'.date("H:i:s",$startDate);
		}
		
		if (gettype($stopDate) == "string") {
			$requestString .= '&endDateTime='.$stopDate;
		} else if (gettype($stopDate) == "integer") {
			$requestString .= '&endDateTime='.date("Y-m-d",$stopDate).'T'.date("H:i:s",$stopDate);
		}

		$pest = new Pest(SMSClient::m_baseURL);
		$result = array();
		try {
			$result = json_decode($pest->get($requestString),true);
		} catch( Exception $e ) {
			$errorInfo = json_decode($e->getMessage());
			unset($pest);			
			throw( new SMSError_Exception( $errorInfo->Desc, $errorInfo->Code));
		}
		unset($pest);
		return $result;
	}
	
	//////////////////////////////// Class methods ////////////////////////////////
	
	/**
	 * Stored session ID
	 * 
	 * @access protected
	 */
	protected $m_sessionID = "";
	
	/**
	 * Stored login
	 * 
	 * @access protected	 
	 */
	protected $m_login = "";
	/**
	 * Stored password
	 * 
	 * @access protected	 
	 */
	protected $m_password = "";
	
	/**
	 * Constructor. Login and password goes here.
	 * 
	 * @param string $login User name
	 * @param string $password Password
	 *
	 */
	function __construct( $login, $password ) {
		$this->m_login = $login;
		$this->m_password = $password;
	}
	
	/**
	 * Session ID Queue
	 *
	 * @access public
	 *
	 * @return string Session ID
	 * @throws SMSError_Exception
	 */
	public function getSessionID() {
		$this->m_sessionID = SMSClient::getSessionID_St( $this->m_login, $this->m_password );
		return $this->m_sessionID;
	}
	
/**
	 * User balance queue
	 *
	 * @access public
	 *
	 * @return double Balance
	 * @throws SMSError_Exception
	 */
	public function getBalance() {
		$balance = SMSClient::getBalance_St( $this->m_sessionID );
		return $balance;
	}
	
	/**
	 * SMS send
	 *
	 * @access public
	 *
	 * @param string  $sourceAddres sender name(up to 11 chars) or phone number(up to 15 digits)
	 * @param string  $destinationAddress destination phone. Country code+cellular code+phone number, E.g. 79031234567
	 * @param string  $data message
	 * @param mixed   $sendDate Delayed message send date and time. String format YYYY-MM-DDTHH:MM:SS or integer Timestamp. Optional.
	 * @param integer $validity Message life time in minutes. Optional.
	 * 
	 * @return array SMS IDs
	 * @throws SMSError_Exception
	 */
	public function send( $sourceAddres, $destinationAddress, $data, $sendDate=null, $validity=0 ) {
		$result = SMSClient::send_St( $this->m_sessionID, $sourceAddres, $destinationAddress, $data, $sendDate, $validity );
		return $result;
	}
	
	/**
	 * SMS send with receiver's local time.
	 *
	 * @access public
	 *
	 * @param string  $sourceAddres sender name(up to 11 chars) or phone number(up to 15 digits)
	 * @param string  $destinationAddress destination phone. Country code+cellular code+phone number, E.g. 79031234567
	 * @param string  $data message
	 * @param mixed   $sendDate Delayed message send date and time in receiver's local time. String format YYYY-MM-DDTHH:MM:SS or integer Timestamp.
	 * @param integer $validity Message life time in minutes. Optional.
	 * 
	 * @return array SMS IDs
	 * @throws SMSError_Exception
	 */
	public function sendByTimeZone( $sourceAddres, $destinationAddress, $data, $sendDate, $validity=0 ) {
		$result = SMSClient::sendByTimeZone_St( $this->m_sessionID, $sourceAddres, $destinationAddress, $data, $sendDate, $validity );
		return $result;		
	}

	/**
	 * SMS send to several receivers
	 *
	 * @access public
	 *
	 * @param string  $sourceAddres sender name(up to 11 chars) or phone number(up to 15 digits)
	 * @param array  $destinationAddresses destination phones. Country code+cellular code+phone number, E.g. 79031234567
	 * @param string  $data message
	 * @param mixed   $sendDate Delayed message send date and time. String format YYYY-MM-DDTHH:MM:SS or integer Timestamp. Optional.
	 * @param integer $validity Message life time in minutes. Optional.
	 * 
	 * @return array SMS IDs
	 * @throws SMSError_Exception
	 */	
	public function sendBulk( $sourceAddres, $destinationAddresses, $data, $sendDate=null, $validity=0 ) {
		$result = SMSClient::sendBulk_St( $this->m_sessionID, $sourceAddres, $destinationAddresses, $data, $sendDate, $validity );
		return $result;		
	}	
	
	/**
	 * SMS Status queue
	 *
	 * @access public
	 *
	 * @param string $messageID Message ID
	 *
	 * @return array массив полей:
	 *		State	- message status. @see SMSClientSMSStatus
	 *		TimeStampUtc		- Date and time of status update
	 *		StateDescription	- Status description
	 *		CreationDateUtc		- Creation date and time
	 *		SubmittedDateUtc	- Submit date and time
	 *		ReportedDateUtc		- Delivery date and time 
	 *		Price				- message price
	 * @throws SMSError_Exception
	 */
	public function getSMSState( $messageID ) {
		$result = SMSClient::getSMSState_St( $this->m_sessionID, $messageID );
		return $result;
	}
	
	/**
	 * Inbox SMS queue
	 *
	 * @access public
	 * 
	 * @param mixed  $minDateUTC queue interval start date. String with format YYYY-MM-DDTHH:MM:SS or integer Timestamp
	 * @param mixed  $maxDateUTC queue interval end date. String with format YYYY-MM-DDTHH:MM:SS or integer Timestamp
	 *
	 * @return array Array with fields:
	 * 		string Data				- Message text
	 *		string SourceAddress	- Sender Name or Phone
	 *		string DestinationAddress	- Receiver's phone
	 *		string ID	- Message ID
	 * @throws SMSError_Exception
	 */
	public function getInbox( $minDateUTC, $maxDateUTC ) {
		$result = SMSClient::getInbox_St( $this->m_sessionID, $minDateUTC, $maxDateUTC );
		return $result;
	}
	
	/**
	 * SMS distribution statistics
	 *
	 * @access public
	 *
	 * @param mixed  $startDate queue interval start date. String with format YYYY-MM-DDTHH:MM:SS or integer Timestamp
	 * @param mixed  $stopDate queue interval end date. String with format YYYY-MM-DDTHH:MM:SS or integer Timestamp
	 *
	 * @return array Array with statistics
	 * @throws SMSError_Exception
	 */
	public function getStatistics( $startDate, $stopDate ) {
		$result = SMSClient::getStatistics_St( $this->m_sessionID, $startDate, $stopDate );
		return $result;	
	}
	
	//////////////////////////////// Service methods ////////////////////////////////
	
	/**
	 * SMS send request parameters preparation
	 *
	 * @access public
	 * @static
	 *
	 * @param string  $sessionID Session ID. @see getSessionID_St
	 * @param string  $sourceAddres sender name(up to 11 chars) or phone number(up to 15 digits)
	 * @param string  $destinationAddress destination phone. Country code+cellular code+phone number, E.g. 79031234567
	 * @param string  $data message
	 * @param mixed   $sendDate Delayed message send date and time. String format YYYY-MM-DDTHH:MM:SS or integer Timestamp. 
	 * @param integer $validity Message life time in minutes.
	 * 
	 * @return array POST request parameters
	 * @throws SMSError_Exception
	 */
	protected static function createRequestParameters( $sessionID, $sourceAddres, $destinationAddress, $data, $sendDate, $validity ) {
		$parameters = array(
			'sessionId' => $sessionID,
			'sourceAddress' => $sourceAddres,
			'data' => $data
			);
		
		if (gettype($destinationAddress) == "string") {
			
			$parameters['destinationAddress'] = $destinationAddress;
			
		} else if (gettype($destinationAddress) == "array") {
			$parameters['destinationAddresses'] = $destinationAddress;//$destinationAddressesString;
		}	
		
		if (gettype($sendDate) == "string") {
			$parameters['sendDate'] = $sendDate;
		} else if (gettype($sendDate) == "integer") {
			$parameters['sendDate'] = date("Y-m-d",$sendDate).'T'.date("H:i:s",$sendDate);			
		}
		
		if ((gettype($validity) == "integer") && ($validity != 0)) {
			$parameters['validity'] = $validity;
		}
		return $parameters;
	}
}

/**
 * Error codes constaints
 */
class SMSClientError {
	const ERROR_OK							= 0;
	const ERROR_ArgumentCanNotBeNullOrEmpty	= 1;
	const ERROR_InvalidAgrument				= 2;
	const ERROR_InvalidSessionID			= 3;
	const ERROR_UnauthorizedAccess			= 4;
	const ERROR_NotEnoughCredits			= 5;
	const ERROR_InvalidOperation			= 6;
	const ERROR_Forbidden					= 7;
	const ERROR_GatewayError				= 8;
	const ERROR_InternalServerError			= 9;
}

/**
 * SMS status codes
 */
class SMSClientSMSStatus {
	const SMS_STATUS_Send		= -1;
	const SMS_STATUS_InQueue	= -2;
	const SMS_STATUS_Deleted	= 47;
	const SMS_STATUS_Stopped	= -98;
	const SMS_STATUS_Delivered	= 0;
	const SMS_STATUS_InvalidSourceAddress			= 10;
	const SMS_STATUS_InvalidDestinationAddress		= 11;
	const SMS_STATUS_UnallowedDestinationAddress	= 41;
	const SMS_STATUS_RejectedBySMSCenter			= 42;
	const SMS_STATUS_TimeOut	= 46;
	const SMS_STATUS_Rejected	= 69;
	const SMS_STATUS_Unknown	= 99;
	const SMS_STATUS_UnknownByTimeout = 255;
}

/**
 * SMS send-receive exception
 */
class SMSError_Exception extends Exception {}
<?php

include_once("redsms_pest.php");

/**
 * ������ ��� �������������� � Devino REST API
 *
 * ������ ����� �������������� ��� ����� ��������� ������� ��� ��� �����.
 * ���������� ������� ����� ������ _St. ���������� ��������� ID ������.
 * ��� ������������� ������, ������������� ������ �������� ������ ������.
 *
 */
class SMSClient {

	//////////////////////////////// ��������� ������ ////////////////////////////////

	/**
	 * ������� ����� ��� �������� ��������
	 * @const
	 */
	const m_baseURL = "https://integrationapi.net/rest";

	/**
	 * ������ ID ������
	 *
	 * @access public
	 * @static
	 *
	 * @param string $login ��� ������������
	 * @param string $password ������
	 *
	 * @return string ������������� ������
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
	 * ������� �������
	 *
	 * @access public
	 * @static	 
	 *
	 * @param string $sessionID ID ������. @see getSessionID_St
	 *
	 * @return double ������
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
	 * �������� SMS-���������
	 *
	 * @access public
	 * @static
	 *
	 * @param string  $sessionID ID ������. @see getSessionID_St
	 * @param string  $sourceAddres �����������. �� 11 ��������� �������� ��� �� 15 ��������.
	 * @param string  $destinationAddress ����� ����������. (��� ������+��� ����+����� ��������, ������: 79031234567
	 * @param string  $data ����� ���������
	 * @param mixed   $sendDate ���� �������� ���������. ������ ���� (YYYY-MM-DDTHH:MM:SS) ��� Timestamp. �������������� ��������.
	 * @param integer $validity ����� ����� ��������� � �������. �������������� ��������
	 * 
	 * @return array ������ ��������������� ���������
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
	 * �������� SMS-��������� � ������ �������� ����� ����������.
	 *
	 * @access public
	 * @static
	 *
	 * @param string  $sessionID ID ������. @see getSessionID_St
	 * @param string  $sourceAddres �����������. �� 11 ��������� �������� ��� �� 15 ��������.
	 * @param string  $destinationAddress ����� ����������. (��� ������+��� ����+����� ��������, ������: 79031234567
	 * @param string  $data ����� ���������
	 * @param mixed   $sendDate ���� �������� ��������� �� �������� ������� ����������. ������ ���� (YYYY-MM-DDTHH:MM:SS) ��� Timestamp
	 * @param integer $validity ����� ����� ��������� � �������. �������������� ��������
	 * 
	 * @return array ������ ��������������� ���������
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
	 * �������� SMS-��������� ���������� ����������
	 *
	 * @access public
	 * @static
	 *
	 * @param string  $sessionID ID ������. @see getSessionID_St
	 * @param string  $sourceAddres �����������. �� 11 ��������� �������� ��� �� 15 ��������.
	 * @param array   $destinationAddresses ������ ����� ������� ����������. (��� ������+��� ����+����� ��������, ������: 79031234567
	 * @param string  $data ����� ���������
	 * @param mixed   $sendDate ���� �������� ���������. ������ ���� (YYYY-MM-DDTHH:MM:SS) ��� Timestamp. �������������� ��������.
	 * @param integer $validity ����� ����� ��������� � �������. �������������� ��������
	 * 
	 * @return array ������ ��������������� ���������
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
	 * ������ ������� SMS-���������
	 *
	 * @access public
	 * @static
	 *
	 * @param string $sessionID ID ������. @see getSessionID_St
	 * @param string $messageID ID ���������.
	 *
	 * @return array ������ �����:
	 *		State	- ������ ���������. @see SMSClientSMSStatus
	 *		TimeStampUtc		- ���� � ����� ��������� ������
	 *		StateDescription	- �������� �������
	 *		CreationDateUtc		- ���� ��������
	 *		SubmittedDateUtc	- ���� ��������
	 *		ReportedDateUtc		- ���� ��������
	 *		Price	- ���� �� ���������
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
	 * ������ �������� SMS-���������
	 *
	 * @access public
	 * @static
	 * 
	 * @param string $sessionID ID ������. @see getSessionID_St
	 * @param mixed  $minDateUTC ������ ������� �������. ������ ���� (YYYY-MM-DDTHH:MM:SS) ��� Timestamp
	 * @param mixed  $maxDateUTC ����� ������� �������. ������ ���� (YYYY-MM-DDTHH:MM:SS) ��� Timestamp	 
	 *
	 * @return array ������ �������� � ������:
	 * 		string Data				- ����� ���������
	 *		string SourceAddress	- ����� �����������
	 *		string DestinationAddress	- ����� ������ �������� ���������
	 *		string ID	- ������������� ���������
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
	 * ������ ���������� �� SMS-���������
	 *
	 * @access public
	 * @static	 
	 *
	 * @param string $sessionID ID ������. @see getSessionID_St
	 * @param mixed  $startDate ������ ������� �������. ������ ���� (YYYY-MM-DDTHH:MM:SS) ��� Timestamp
	 * @param mixed  $stopDate ����� ������� �������. ������ ���� (YYYY-MM-DDTHH:MM:SS) ��� Timestamp	 
	 *
	 * @return array ������ � ����������� �� ����������
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
	
	//////////////////////////////// ������ ��� ������ � ������� ////////////////////////////////
	
	/**
	 * ����������� ������������� ������
	 * 
	 * @access protected
	 */
	protected $m_sessionID = "";
	
	/**
	 * ����������� �����
	 * 
	 * @access protected	 
	 */
	protected $m_login = "";
	/**
	 * ����������� ������
	 * 
	 * @access protected	 
	 */
	protected $m_password = "";
	
	/**
	 * �����������. � ���� ���������� ����� � ������.
	 * 
	 * @param string $login �����
	 * @param string $password ������
	 *
	 */
	function __construct( $login, $password ) {
		$this->m_login = $login;
		$this->m_password = $password;
	}
	
	/**
	 * ������ ID ������.
	 *
	 * @access public
	 *
	 * @return string ������������� ������
	 * @throws SMSError_Exception
	 */	
	public function getSessionID() {
		$this->m_sessionID = SMSClient::getSessionID_St( $this->m_login, $this->m_password );
		return $this->m_sessionID;
	}
	
	/**
	 * ������� �������
	 *
	 * @access public
	 *
	 * @return double ������
	 * @throws SMSError_Exception
	 */	
	public function getBalance() {
		$balance = SMSClient::getBalance_St( $this->m_sessionID );
		return $balance;
	}
	
	/**
	 * �������� SMS-���������
	 *
	 * @access public
	 *
	 * @param string  $sourceAddres �����������. �� 11 ��������� �������� ��� �� 15 ��������.
	 * @param string  $destinationAddress ����� ����������. (��� ������+��� ����+����� ��������, ������: 79031234567
	 * @param string  $data ����� ���������
	 * @param mixed   $sendDate ���� �������� ���������. ������ ���� (YYYY-MM-DDTHH:MM:SS) ��� Timestamp. �������������� ��������.
	 * @param integer $validity ����� ����� ��������� � �������. �������������� ��������
	 * 
	 * @return
	 * @throws SMSError_Exception
	 */	
	public function send( $sourceAddres, $destinationAddress, $data, $sendDate=null, $validity=0 ) {
		$result = SMSClient::send_St( $this->m_sessionID, $sourceAddres, $destinationAddress, $data, $sendDate, $validity );
		return $result;
	}
	
	/**
	 * �������� SMS-��������� � ������ �������� ����� ����������.
	 *
	 * @access public
	 *
	 * @param string  $sourceAddres �����������. �� 11 ��������� �������� ��� �� 15 ��������.
	 * @param string  $destinationAddress ����� ����������. (��� ������+��� ����+����� ��������, ������: 79031234567
	 * @param string  $data ����� ���������
	 * @param mixed   $sendDate ���� �������� ��������� �� �������� ������� ����������. ������ ���� (YYYY-MM-DDTHH:MM:SS) ��� Timestamp
	 * @param integer $validity ����� ����� ��������� � �������. �������������� ��������
	 * 
	 * @return
	 * @throws SMSError_Exception
	 */		
	public function sendByTimeZone( $sourceAddres, $destinationAddress, $data, $sendDate, $validity=0 ) {
		$result = SMSClient::sendByTimeZone_St( $this->m_sessionID, $sourceAddres, $destinationAddress, $data, $sendDate, $validity );
		return $result;		
	}

	/**
	 * �������� SMS-��������� � ������ �������� ����� ����������.
	 *
	 * @access public
	 *
	 * @param string  $sourceAddres �����������. �� 11 ��������� �������� ��� �� 15 ��������.
	 * @param array   $destinationAddresses ������ ����� ������� ����������. (��� ������+��� ����+����� ��������, ������: 79031234567
	 * @param string  $data ����� ���������
	 * @param mixed   $sendDate ���� �������� ���������. ������ ���� (YYYY-MM-DDTHH:MM:SS) ��� Timestamp. �������������� ��������.
	 * @param integer $validity ����� ����� ��������� � �������. �������������� ��������
	 * 
	 * @return
	 * @throws SMSError_Exception
	 */	
	public function sendBulk( $sourceAddres, $destinationAddresses, $data, $sendDate=null, $validity=0 ) {
		$result = SMSClient::sendBulk_St( $this->m_sessionID, $sourceAddres, $destinationAddresses, $data, $sendDate, $validity );
		return $result;		
	}	
	
	/**
	 * ������ ������� SMS-���������
	 *
	 * @access public
	 *
	 * @param string $messageID ID ���������.
	 *
	 * @return array ������ �����:
	 *		State	- ������ ���������. @see SMSClientSMSStatus
	 *		SMSClientSMSStatus	- ���� � ����� ��������� ������
	 *		StateDescription	- �������� �������
	 *		CreationDateUtc		- ���� ��������
	 *		SubmittedDateUtc	- ���� ��������
	 *		ReportedDateUtc		- ���� ��������
	 *		Price	- ���� �� ���������
	 * @throws SMSError_Exception
	 */
	public function getSMSState( $messageID ) {
		$result = SMSClient::getSMSState_St( $this->m_sessionID, $messageID );
		return $result;
	}
	
	/**
	 * ������ �������� SMS-���������
	 *
	 * @access public
	 * 
	 * @param mixed  $minDateUTC ������ ������� �������. ������ ���� (YYYY-MM-DDTHH:MM:SS) ��� Timestamp
	 * @param mixed  $maxDateUTC ����� ������� �������. ������ ���� (YYYY-MM-DDTHH:MM:SS) ��� Timestamp	 
	 *
	 * @return array ������ �������� � ������:
	 * 		string Data				- ����� ���������
	 *		string SourceAddress	- ����� �����������
	 *		string DestinationAddress	- ����� ������ �������� ���������
	 *		string ID	- ������������� ���������
	 * @throws SMSError_Exception
	 */
	public function getInbox( $minDateUTC, $maxDateUTC ) {
		$result = SMSClient::getInbox_St( $this->m_sessionID, $minDateUTC, $maxDateUTC );
		return $result;
	}
	
	/**
	 * ������ ���������� �� SMS-���������
	 *
	 * @access public
	 *
	 * @param mixed  $startDate ������ ������� �������. ������ ���� (YYYY-MM-DDTHH:MM:SS) ��� Timestamp
	 * @param mixed    $stopDate ����� ������� �������. ������ ���� (YYYY-MM-DDTHH:MM:SS) ��� Timestamp	 
	 *
	 * @return array ������ � ����������� �� ����������
	 * @throws SMSError_Exception
	 */	
	public function getStatistics( $startDate, $stopDate ) {
		$result = SMSClient::getStatistics_St( $this->m_sessionID, $startDate, $stopDate );
		return $result;	
	}
	
	//////////////////////////////// ��������� ������ ////////////////////////////////
	
	/**
	 * ������� ������� ������ ������� ��� ������� �������� ���������
	 *
	 * @access protected	 
	 * @static 
	 *
	 * @param string  $sessionID ID ������. @see getSessionID_St
	 * @param string  $sourceAddres �����������. �� 11 ��������� �������� ��� �� 15 ��������.
	 * @param mixed   $destinationAddress ����� ��� ������ ������� ����������. (��� ������+��� ����+����� ��������, ������: 79031234567
	 * @param string  $data ����� ���������
	 * @param mixed   $sendDate ���� �������� ���������. ������ ���� (YYYY-MM-DDTHH:MM:SS) ��� Timestamp
	 * @param integer $validity ����� ����� ��������� � �������
	 * 
	 * @return array ������ � �����������
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
 * ������ �������� � ������ ������
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
 * ������ �������� � ������ �������� SMS-���������
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
 * ������������ ���������� ��� ������ �������� SMS
 */
class SMSError_Exception extends Exception {}
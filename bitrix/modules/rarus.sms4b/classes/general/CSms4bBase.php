<?
class CSms4bBase {
	protected $log = true;
	protected $header = 'POST %addr% HTTP/1.1
Host: sms4b.ru
Content-Type: text/xml; charset=utf-8
Cache-Control: no-cache, must-revalidate
Pragma: no-cache
Content-Length: %lenght%
SOAPAction: "SMS %nameclient%/%func%"

';
	protected $arrHeader = array(
							"Content-Type" => 'Content-Type: text/xml; charset=utf-8',
							"CacheControl" => 'Cache-Control: no-cache, must-revalidate',
							"Pragma" => 'Pragma: no-cache',
							"ContentLength" => 'Content-Length: %lenght%',
							"SOAPAction" => 'SOAPAction: "SMS %nameclient%/%func%"',
							);

	//default address
	protected $defAddr = "/webservices/sms.asmx";
	//name of default client
	protected $defClient = "client";

	//xml header of the xml request, used in makerequest() function
	protected $xml_header = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
<soap:Body>
';

	//xml footer of the xml request, used in makerequest() function
	protected $xml_footer = "</soap:Body>
</soap:Envelope>";

		//address of server that give webservice
	protected $serv_addr = "https://sms4b.ru";
	//server port for connection
	protected $serv_port = 443;
	//proxy server address
	protected $proxy_serv_addr = "";
	//port of proxy server
	protected $proxy_serv_port = "";
	//flag of proxy active
	protected $proxy_use = false;
	//here would save errors on connection to server and other errors
	public $LastError = '';
	//last request to the server in xml
	public $LastReq = '';
	//last response from the server in xml
	public $LastRes = '';
	//balance of the current active user
	public	$arBalance = array();
	//max number of the symbols in SMS, no restriction on default
	public $sms_sym_count = '';
	//login for the SMS server
	protected $login = '';
	//version of script
	protected $version = 'p';
	//password for the SMS server
	protected $password = '';
	//gmt identifier
	protected $gmt = '';
	//session identifier
	protected $sid = 0;
	//default sender. Number for using as Sender by default.
	protected $DefSender = '';
	//transliteration
	protected $use_translit = false;
	//last time of loading incoming messages
	protected $inc_date = '';
	//max number of sms in one package
	public $maxPackage = 100;

	public $loadid = 0;
	protected $can_chpwd = false;

	function CSms4bBase($login = '', $password = '')
	{
		session_start();
		$this->login = " " .$this->version ." ". $login;
		$this->password = $password;
		$this->gmt = "3";
		$this->serv_addr = "https://sms4b.ru";
		$this->UpdateSID();
		return;
	}

	/**********************************
SMS functions
**********************************/

/************************************
returns ready state xml list of parameters
************************************/
	protected function getbodyrec($funcname='',$param=array(),$nameclient)
	{
		$bodyrec = '<'.$funcname.' xmlns="SMS '.$nameclient.'">'."\r\n";

		foreach ($param as $name => $val)
		{
			if ($funcname == "SaveMessages" && $name == "List")
			{
				$head_schema = '<List>
<xsd:schema id="NewDataSet" xmlns="" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:msdata="urn:schemas-microsoft-com:xml-msdata">
<xsd:element name="NewDataSet" msdata:IsDataSet="true" msdata:UseCurrentLocale="true">
<xsd:complexType>
<xsd:choice minOccurs="0" maxOccurs="unbounded">
<xsd:element name="Table1">
<xsd:complexType>
<xsd:sequence>
<xsd:element name="SessionID" type="xsd:int" minOccurs="0" />
<xsd:element name="guid" type="xsd:string" minOccurs="0" />
<xsd:element name="StartUp" type="xsd:string" minOccurs="0" />
<xsd:element name="Period" type="xsd:string" minOccurs="0" />
<xsd:element name="Destination" type="xsd:string" minOccurs="0" />
<xsd:element name="Source" type="xsd:string" minOccurs="0" />
<xsd:element name="Body" type="xsd:string" minOccurs="0" />
<xsd:element name="Encoded" type="xsd:unsignedByte" minOccurs="0" />
<xsd:element name="dton" type="xsd:unsignedByte" minOccurs="0" />
<xsd:element name="dnpi" type="xsd:unsignedByte" minOccurs="0" />
<xsd:element name="ston" type="xsd:unsignedByte" minOccurs="0" />
<xsd:element name="snpi" type="xsd:unsignedByte" minOccurs="0" />
<xsd:element name="TimeOff" type="xsd:string" minOccurs="0" />
<xsd:element name="Priority" type="xsd:unsignedByte" minOccurs="0" />
<xsd:element name="NoRequest" type="xsd:string" minOccurs="0" />
</xsd:sequence>
</xsd:complexType>
</xsd:element>
</xsd:choice>
</xsd:complexType>
</xsd:element>
</xsd:schema>
<diffgr:diffgram xmlns:msdata="urn:schemas-microsoft-com:xml-msdata" xmlns:diffgr="urn:schemas-microsoft-com:xml-diffgram-v1">
<NewDataSet xmlns="">
';
				$sms = array();
				$sms = $val;

				$i=1;
				$bodyrec.= $head_schema;
				foreach($sms as $key=>$value)
				{
					$bodyrec.= '<Table1 diffgr:id="Table1%table_num%" msdata:rowOrder="0" diffgr:hasChanges="inserted">'."\r\n";
					$bodyrec = str_replace("%table_num%",$i,$bodyrec);
					$i++;
					foreach($value as $xml_tag_name=>$xml_tag_value)
					{
						$bodyrec .= '<'.$xml_tag_name.'>'.$xml_tag_value.'</'.$xml_tag_name.'>'."\r\n";
					}
					$bodyrec.= '</Table1>'."\r\n";
				}

				$bodyrec.= '</NewDataSet>'."\r\n";
				$bodyrec.= '</diffgr:diffgram>'."\r\n";
				$bodyrec.= '</List>'."\r\n";
			}
			else if ($funcname == "SaveGroup" && $name == "List")
			{
				$head_schema = '<List>
	  <xsd:schema id="NewDataSet" xmlns="" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:msdata="urn:schemas-microsoft-com:xml-msdata">
	   <xsd:element name="NewDataSet" msdata:IsDataSet="true" msdata:UseCurrentLocale="true">
		<xsd:complexType>
		 <xsd:choice minOccurs="0" maxOccurs="unbounded">
		  <xsd:element name="Table1">
		   <xsd:complexType>
			<xsd:sequence>
			 <xsd:element name="G" type="xsd:string" minOccurs="0" />
			 <xsd:element name="D" type="xsd:string" minOccurs="0" />
			 <xsd:element name="T" type="xsd:int" minOccurs="0" />
			 <xsd:element name="N" type="xsd:int" minOccurs="0" />
			 <xsd:element name="E" type="xsd:int" minOccurs="0" />
			 <xsd:element name="B" type="xsd:string" minOccurs="0" />
			</xsd:sequence>
		   </xsd:complexType>
		  </xsd:element>
		 </xsd:choice>
		</xsd:complexType>
	   </xsd:element>
	  </xsd:schema>
	  <diffgr:diffgram xmlns:msdata="urn:schemas-microsoft-com:xml-msdata" xmlns:diffgr="urn:schemas-microsoft-com:xml-diffgram-v1">
		<NewDataSet xmlns="">
	';
				$sms = array();
				$sms = $val;

				$i=1;
				$bodyrec.= $head_schema;
				foreach($sms as $key=>$value)
				{
					$bodyrec.= '<Table1 diffgr:id="Table1%table_num%" msdata:rowOrder="0" diffgr:hasChanges="inserted">'."\r\n";
					$bodyrec = str_replace("%table_num%",$i,$bodyrec);
					$i++;
					foreach($value as $xml_tag_name=>$xml_tag_value)
					{
						$bodyrec .= '<'.$xml_tag_name.'>'.$xml_tag_value.'</'.$xml_tag_name.'>'."\r\n";
					}
					$bodyrec.= '</Table1>'."\r\n";
				}

				$bodyrec.= '</NewDataSet>'."\r\n";
				$bodyrec.= '</diffgr:diffgram>'."\r\n";
				$bodyrec.= '</List>'."\r\n";
				}

				else if ($funcname == "GroupSMS" && $name == "List")
				{
					$head_schema = '<List>'."\r\n";
				$sms = array();
				$sms = $val;

				$i=1;
				$bodyrec.= $head_schema;
				foreach($sms as $key=>$value)
				{
					$bodyrec.= '<GroupSMSList>'."\r\n";
					$bodyrec = str_replace("%table_num%",$i,$bodyrec);
					$i++;
					foreach($value as $xml_tag_name=>$xml_tag_value)
					{
						$bodyrec .= '<'.$xml_tag_name.'>'.$xml_tag_value.'</'.$xml_tag_name.'>'."\r\n";
					}
					$bodyrec.= '</GroupSMSList>'."\r\n";
				}
				$bodyrec.= '</List>'."\r\n";
				}
			else
			{
				$bodyrec .= '<'.$name.'>'.$val.'</'.$name.'>'."\r\n";
			}
		}
		$bodyrec .= '</'.$funcname.'>'."\r\n";
		$bodyrec = $this->xml_header.$bodyrec.$this->xml_footer;
		return $bodyrec;
	}

	/************************************
make request to the server and returns response
************************************/
	protected function makeRequest($funcname,$param,$nameclient,$address)
	{
		$xml = $this->getbodyrec($funcname,$param,$nameclient);
		$xmllen = strlen($xml);
		$arrHeader = $this->arrHeader;

		$arrHeader["ContentLength"] = str_replace('%lenght%',$xmllen,$arrHeader["ContentLength"]);
		$arrHeader["SOAPAction"] = str_replace('%nameclient%',$nameclient,$arrHeader["SOAPAction"]);
		$arrHeader["SOAPAction"] = str_replace('%func%',$funcname,$arrHeader["SOAPAction"]);

		$ch = curl_init();

		if (curl_errno($ch) > 0)
		{
			$this->LastError = 'Ваша версия PHP не поддерживает библиотеку CURL';
			return false;
		}
		else
		{
			if($this->proxy_use == "Y" && $this->proxy_host <> '' && $this->proxy_portt <> '')
			{
				curl_setopt ($ch, CURLOPT_PROXY, $this->proxy_host.':'.$this->proxy_portt);
				curl_setopt ($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
			}

			curl_setopt($ch, CURLOPT_URL, $this->serv_addr.$address);
			curl_setopt($ch, CURLOPT_HTTPHEADER,$arrHeader);
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
			curl_setopt($ch, CURLOPT_HTTP_VERSION, 1.1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 15);

			$response = '';
			$response = curl_exec($ch);

			$lerror = curl_errno($ch);
			if($lerror > 0 || strlen($response) < 1)
			{
				$this->LastError = 'Неизвестная ошибка подключения CURL['.$lerror.']';
				return false;
			}
			else
			{
				$this->LastReq = implode("
",$arrHeader)."\r\n\r\n".$xml;
				$this->LastRes = $response;

				return $response;
			}
			if($this->log)
			{
				$filename = $_SERVER["DOCUMENT_ROOT"].'/bitrix/sms.log';
				$somecontent = "==============\n";
				$somecontent .= date("d.m.Y H:i:s",time()).": Отправка запроса\n";
				$somecontent .= "==============\n";
				$somecontent .= $this->LastReq."\n";
				$somecontent = "==============\n";
				$somecontent .= date("d.m.Y H:i:s",time()).": Получение ответа\n";
				$somecontent .= "==============\n";
				$somecontent .= $this->LastRes."\n";
				if (is_writable($filename))
				{
					if ($handle = fopen($filename, 'a'))
						fwrite($handle, $somecontent);
					fclose($handle);
				}
			}
		}
		curl_close($ch);
	}

	/*******************************
Parsing table-state response of the server, and returnes values of parameters,
	where
	$xml - response of the server
	$params - array of needed params, for example - array("Login","Password")
*******************************/
	protected function ParserTableResp($xml,$params = array())
	{
		if($xml <> '' && count($params) > 1)
		{
			$pars_pref = substr(md5(time()+'qwe123'),0,10); //kick inters

			$xml = str_replace("\r\n",$pars_pref,$xml);
			$xml = str_replace("\n",$pars_pref,$xml);

			$this->LastError = '';

			preg_match_all("/<Table.+?>(.+?)<\/Table>/i",$xml,$find);

			foreach($find[1] as $key => $val)
			{
				$arReports[] = $this->ParserResp($val,$params);
			}

		}
		return $arReports;
	}

	/*******************************
Parsing simple-state response of the server, and returnes values of parameters,
	where
	$xml - response of the server
	$params - array of needed params, for example - array("Login","Password")
*******************************/
	protected function ParserResp($xml,$params = array())
	{
		$this->LastError = '';
		if($xml <> '' && count($params) > 1)
		{
			$pars_pref = substr(md5(time()+'qwe123'),0,10); //kick inters

			$xml = str_replace("\r\n",$pars_pref,$xml);
			$xml = str_replace("\n",$pars_pref,$xml);

			foreach ($params as $param)
			{
				if (preg_match("/<$param>(.+?)<\/$param>/",$xml,$find))
					$arResult[$param] = trim(str_replace($pars_pref,"\r\n",$find[1]));
			}
			if (count($arResult) > 0)
			{
				return $arResult;
			}
			else
			{
				$this->LastError = "Ошибка парсинга ответа сервера: требуемые поля не обнаружены";
				return false;
			}
		}
		else
		{
			$this->LastError = $xml == '' ? "Ошибка парсинга ответа сервера: пустой xml" : "Ошибка парсинга ответа сервера: не указан список параметров";
			return false;
		}
	}

	/*************************
Making request to the server
	where
	$funcname - name of function-webservice on the server,
	$param - params for $funcname-function
*************************/
	public function GetSOAP ($funcname='',$param=array())
	{
		$this->LastError = '';
		$response = $this->makeRequest( $funcname,
										$param,
										(array_key_exists($funcname,$this->arFuncAddr) ? $this->arFuncAddr[$funcname]["client"] : $this->defClient),
										(array_key_exists($funcname,$this->arFuncAddr) ? $this->arFuncAddr[$funcname]["addr"] : $this->defAddr)
									);
		if ($response != -1)
		{
			switch ($funcname)
			{
				case "StartSession":
						if ($this->sid > 0)
						{
							return true;
						}

						$this->sid = $this->StartSession($response);

						if (!$this->sid < 0)
						{
							$this->LastError = "Неизветсная ошибка установки сессии.";
							return false;
						}
						elseif($this->sid === 0)
						{
							$this->LastError = "Сесcия уже установлена, но номер сессии потерян.";
							return false;
						}
						else
						{
							$_SESSION["SMS_START_SESSION"] = $this->sid;
							$this->LastError = '';
							return true;
						}
				break;

				case "LoadMessage":
						$result = $this->LoadMessage($response);

						if ($result["Result"] < 0)
						{
							$this->LastError = "Ошибка вызова LoadMessage";
							return -1;
						}
						elseif ($result["Result"] == 0)
						{
							return 0;
						}
						else
						{
							return $result;
						}
				break;

				case "LoadResponse":
						$result = $this->LoadResponse($response);
						return $result;
				break;

				case "CloseSession":
					if ($this->GetSID > 1)
					{
						$closeid = $this->CloseSession($response);
						if($closeid > 0)
						{
							$this->LastError = "";
							return true;
						}
						elseif($closeid === 0)
						{
							$this->LastError = "";
							return true;
						}
						else
						{
							$this->LastError = "Не удалось закрыть сессию. Ошибка номер №".$closeid;
							return false;
						}
					}
					else
					{
						$this->LastError = "Нет открытой сессии";
						return false;
					}
				break;

				case "AccountParams":
						if($this->AccountParams($response))
							return true;
						else
							return false;
				break;

				//функция групповой отправки SMS (оптимизированная)
				case "SaveGroup":
					return $this->SaveGroup($response);
				break;

				case "SaveMessage":

						$saveMessageResult = $this->SaveMessage($response);

						if ($saveMessageResult > 0)
						{
							$ok = '';
							$ok = intval($saveMessageResult);
							$arrSaveres["SEND"] = 255 & $ok;
							$arrSaveres["OK"] = 255 & ($ok >> 8);

							return $arrSaveres;
						}
						else
						{
							$this->AnalyzeResultSaveMessage($saveMessageResult);
							return false;
						}

				break;

				//пакетная отправка
				case "SaveMessages":
					$saveMessagesResult = $this->SaveMessages($response);
					return $saveMessagesResult;
				break;

				/*case "LoadIn":
					$LoadInResult = $this->LoadIn($response);
						if(count($LoadInResult) > 0)
							return $LoadInResult;
						else
							return false;
				break;*/

				case "LoadSMS":
					$LoadInResult = $this->LoadSMS($response);
						if(count($LoadInResult) > 0)
							return $LoadInResult;
						else
							return false;
				break;


				case "GroupSMS":
					$GroupSMSResult = $this->GroupSMS($response);
						if(count($GroupSMSResult) > 0)
							return $GroupSMSResult;
						else
							return false;
				break;

				default:
				{
						$this->LastError = "Неизвестная функция";
						return false;
				}
			}//endswitch
		}
		else
		{
				$this->LastError = 'Не удалось подключится к серверу SMS4B';
				return false;
		}//endif
	}//endfunction


		/*checks user password and login*/
	public function IsRegUser($Login,$Password)
	{
		$props = array( "Login" => $Login,
						"Password"=>$Password);
		return $this->GetSOAP("CheckUser",$props);
	}


	/*************************
function-parsers
**************************/
	/*parse server response on GetSoap("StartSession",....) request*/
	protected function StartSession($xml)
	{
		$xml = str_replace("\r\n",'',$xml);
		$xml = str_replace("\n",'',$xml);
		preg_match("/<StartSessionResult>([0-9]+?)<\/StartSessionResult>/",$xml,$find);
		$sid = intval($find[1]);
		return $sid;
	}

		/*************************
function-parsers
**************************/
	/*parse server response on GetSoap("LoadIn",....) request*/
	/*protected function LoadIn($xml)
	{
		$xml = str_replace("\r\n",'',$xml);
		$xml = str_replace("\n",'',$xml);

		$this->LastError = '';

		preg_match_all("/<Table.+?>(.+?)<\/Table>/i",$xml,$find);

		foreach($find[1] as $key => $val)
		{
			$arReports[] = $this->ParserResp($val,array(
															"Source",
															"Destination",
															"Moment",
															"TimeOff",
															"Coding",
															"Body",
															"Part",
															"Total",
															//"GUID",
														)
											);
		}

		return $arReports;
	}*/
	protected function LoadSMS($xml)
	{
		$xml = str_replace("\r\n",'',$xml);
		$xml = str_replace("\n",'',$xml);

		$this->LastError = '';

		preg_match_all("/<SMSList>(.+?)<\/SMSList>/i",$xml,$find);

		foreach($find[1] as $key => $val)
		{
			$arReports[] = $this->ParserResp($val,array(
															"G",
															"D",
															"B",
															"E",
															"A",
															"P",
															"M",
															"T",
															"S",
														)
											);
		}

		return $arReports;
	}

		/*parse server response*/
	protected function CloseSession($xml)
	{
		$xml = str_replace("\r\n",'',$xml);
		$xml = str_replace("\n",'',$xml);

		preg_match("/<CloseSessionResult>([0-9]+?)<\/CloseSessionResult>/",$xml,$find);
		$sid = intval($find[1]);
		return $sid;
	}

		/*parse server response*/
	protected function AccountParams($xml)
	{
		$this->LastError = '';
		$this->arBalance = $this->ParserResp($xml,array("Result","Rest","Addresses"));

		if ($this->arBalance["Result"] < 1)
		{
				$this->LastError = "Не удалось запросить параметры аккаунта пользователя";
				return false;
		}
		else
		{
			$this->arBalance["Addresses"] = explode("\r\n",$this->arBalance["Addresses"]);
		}

		return true;
	}

		/*функция сохранения группы сообщений*/
	protected function SaveGroup($xml)
	{
		$xml = str_replace("\r\n",'',$xml);
		$xml = str_replace("\n",'',$xml);

		//получаем код группы
		preg_match("/<Code>(.+?)<\/Code>/",$xml,$find);
		//найдем код результата
		$resultArray['groupCode'] = intval($find[1]);

		//получаем Result
		preg_match("/<Result>(.+?)<\/Result>/",$xml,$result);
		$resultArray['result'] = intval($result[1]);

		return $resultArray;
	}

		protected function GroupSMS($xml)
	{
		$xml = str_replace("\r\n",'',$xml);
		$xml = str_replace("\n",'',$xml);

		//получаем код группы
		preg_match("/<Group>(.+?)<\/Group>/",$xml,$find);
		//найдем код результата
		$resultArray['groupCode'] = intval($find[1]);

		//получаем Result
		preg_match("/<Result>(.+?)<\/Result>/",$xml,$result);
		$resultArray['result'] = intval($result[1]);

		return $resultArray;
	}

		/*parse server response*/
	protected function SaveMessage($xml)
	{
		$xml = str_replace("\r\n",'',$xml);
		$xml = str_replace("\n",'',$xml);
		preg_match("/<SaveMessageResult>([0-9]+?)<\/SaveMessageResult>/",$xml,$find);
		$saveMessageResult = intval($find[1]);
		return $saveMessageResult;
	}

		/*parse server response after package send*/
	protected function SaveMessages($xml)
	{
		$xml = str_replace("\r\n",'',$xml);
		$xml = str_replace("\n",'',$xml);

		preg_match_all("/<SessionID>([0-9-]+?)<\/SessionID>/",$xml,$find);
		preg_match_all("/<Destination>(.+?)<\/Destination>/",$xml,$find_dest_num);
		$saveMessagesResult = $find[1];
		$dest_numbers = $find_dest_num[1];

		$result_array = array();
		$succes_send = 0;
		$not_send = 0;
		$i=0;
		$array_for_counts = array();

		foreach($saveMessagesResult as $arIndex)
		{
			if (intval($arIndex) > 0)
			{
				$ok = '';
				$ok = intval($arIndex);
				$arrSaveres = array();
				$arrSaveres["SEND"] = 255 & $ok;
				$arrSaveres["OK"] = 255 & ($ok >> 8);

				$array_for_counts[] = $arrSaveres;

				$succes_send++;
			}
			else
			{
				$arrSaveres["SEND"] = 0;
				$arrSaveres["OK"] = 0;
				$array_for_counts[] = $arrSaveres;

				$not_send++;
			}
		}
		//forming final array
		//here will be 	"WAS_SEND" - number of messages that was put to query
		//				"NOT_SEND" - number of messages that was not put to query
		//				"ARRAY_NUMBERS_ON_NOT_SEND" - array of dest numbers messages that was not put to query on server
		$result_array["WAS_SEND"] = $succes_send;
		$result_array["NOT_SEND"] = $not_send;
		$result_array["ARRAY_NUMBERS_ON_NOT_SEND"] = $dest_numbers;
		$result_array["FOR_ADDING_TO_BASE"] =  $array_for_counts;

		return $result_array;
	}

	/*returns current user Login*/
	public function getLogin()
	{
		$login =  explode(" ", $this->login);
		return $login['2'];
	}

	/*returns current user Password*/
	public function getPassword()
	{
		return $this->password;
	}

	/*returns current user GMT*/
	public function getUserGMT()
	{
		return $this->gmt;
	}

	/*returns current session ID*/
	public function GetSID()
	{
		return $this->sid;
	}

		/*creates GUID - global universal id, for identification of every part of sms*/
	public function CreateGuid()
	{
		if (function_exists('com_create_guid'))
		{
			$guid = $this->eraseBrackets(com_create_guid());
			return $guid;
		}
		else
		{
			mt_srand((double)microtime()*10000);
			$charid = strtoupper(md5(uniqid(rand(), true)));
			$hyphen = chr(45);// "-"
			$uuid = substr($charid, 0, 8).$hyphen
					.substr($charid, 8, 4).$hyphen
					.substr($charid,12, 4).$hyphen
					.substr($charid,16, 4).$hyphen
					.substr($charid,20,12);
			return $uuid;
		}
	}

		/*kills symbols '{' and '}' in GUID*/
	protected function eraseBrackets($str)
	{
		return str_replace(array("{","}"),"",$str);
	}

		/*from binary to hex number transformation*/
	public function bin_to_hex($str)
	{
		switch ($str)
		{
			case "0000": return "0";
			case "0001": return "1";
			case "0010": return "2";
			case "0011": return "3";
			case "0100": return "4";
			case "0101": return "5";
			case "0110": return "6";
			case "0111": return "7";
			case "1000": return "8";
			case "1001": return "9";
			case "1010": return "A";
			case "1011": return "B";
			case "1100": return "C";
			case "1101": return "D";
			case "1110": return "E";
			case "1111": return "F";
			default:
			{
				return false;
			}
		}
	}

		//how to code message
	//if exist symbol with code bigger than 127 or meet inadmissible symbols,
	//so we code text as UTF-16 (function returns 1).
	//Another way we code as DefaultAlphabet (function returns 0)
	public function get_type_of_encoding($message)
	{
		//недопустимые символы
		$inadmissible_symbols = array("[" , "]" , "\\" , "^" , "_" , "`" , "{", "}" , "|" , "~");

		if ($message == "")
		{
			$this->LastError = "В сообщении нету символов";
			return false;
		}
		else
		{
			$type_of_encoding = 0;

			for($i = 0; $i < strlen($message);$i++)
			{
				if (ord($message[$i]) > 127 || in_array($message[$i],$inadmissible_symbols))
				{
					$type_of_encoding = 1;
					break;
				}
			}
			return $type_of_encoding;
		}
	}

		/*codes one symbol
	$symbol: symbol for coding (for example 'a')
	$type_of_encoding:
	 0-DefaultAlphabet
	 1-UTF16*/
	public function enCoding($symbol,$type_of_encoding)
	{
		if (strlen($symbol)==0 || $type_of_encoding > 1  || $type_of_encoding < 0)
		{
			$this->LastError = "Неверно указаны параметры вызова функции enCoding";
			return false;
		}
		else
		{
			switch($type_of_encoding)
			{
				case 0:
					if ($symbol == "@")
					{
						return "00";
						break;
					}
					elseif ($symbol =="$")
					{
						return "02";
						break;
					}

					$code = ord($symbol);
					$str16x = "";

					for($i = 0; $i < 8; $i++)
					{
						$bit = $code & 1;
						switch ($bit)
						{
							case 0: $str16x.="0";break;
							case 1: $str16x.="1";break;
						}
						$code = $code >> 1;
					}

					$str16x = strrev($str16x);

					$high_part = $str16x[0].$str16x[1].$str16x[2].$str16x[3];
					$low_part  = $str16x[4].$str16x[5].$str16x[6].$str16x[7];

					$high_part = $this->bin_to_hex($high_part);
					$low_part = $this->bin_to_hex($low_part);

					$str16x = $high_part.$low_part;
					return $str16x;
					break;
				case 1:
					$symbol = mb_convert_encoding($symbol, "UTF-16", "Windows-1251");

					$code = (ord($symbol[0])*256+ord($symbol[1]));

					$str16x = "";

					for($i = 0; $i < 16; $i++)
					{
						$bit = $code & 1;
						switch ($bit)
						{
							case 0: $str16x.="0";break;
							case 1: $str16x.="1";break;
						}
						$code = $code >> 1;
					}


					$str16x = strrev($str16x);

					$first_part = $str16x[0].$str16x[1].$str16x[2].$str16x[3];
					$second_part  = $str16x[4].$str16x[5].$str16x[6].$str16x[7];
					$third_part = $str16x[8].$str16x[9].$str16x[10].$str16x[11];
					$fourth_part = $str16x[12].$str16x[13].$str16x[14].$str16x[15];

					$first_part = $this->bin_to_hex($first_part);
					$second_part = $this->bin_to_hex($second_part);
					$third_part = $this->bin_to_hex($third_part);
					$fourth_part = $this->bin_to_hex($fourth_part);

					$str16x = $first_part.$second_part.$third_part.$fourth_part;
					return $str16x;
					break;
			}//end switch
		}//end if
	}

		//decoding of messages  0 - DefaultAlphabet
	//						1 - Unicode-16
	public function decode($message,$type_of_enc)
	{
		$decoded_message = "";
		switch($type_of_enc)
		{
			case 0:
					for($i = 0; $i < strlen($message);$i = $i+2)
					{
						$symbol = $message[$i].$message[$i+1];

						if ($symbol == "00")
						{
							$decoded_message .= '@';
						}
						elseif ($symbol == "02")
						{
							$decoded_message .= '$';
						}
						else
						{
							$decoded_message .= chr(hexdec($symbol));
						}
					}
			break;
			case 1:
					$decoded_message = $this->hex2unicode($message);
			break;
		}
		return $decoded_message;
	}

	//function for coding the hole message
	public function enCodeMessage($message)
	{
		if ($message == "")
		{
			$this->LastError= "В сообщении ниодного символа!";
			return false;
		}
		else
		{

			$type_of_encoding=$this->get_type_of_encoding($message);

			for($i = 0; $i < strlen($message);$i++)
			{
				$encoded_string.=$this->enCoding($message[$i],$type_of_encoding);
			}

			return $encoded_string;
		}
	}

	//coding to unicode
	public function hex2unicode($str)
	{
		$returned_string = "";
		if (strlen($str) % 4 == 0)
		{
			for($i=0;$i < strlen($str);$i=$i+4)
			{
				$code = substr($str, $i, 4);
				$code = base_convert($code, 16, 10);
				$returned_string .= '&#'.$code.';';
			}
		}
		return $returned_string;
	}

		/*function for Analyze errors in SaveMessage request*/
	protected function AnalyzeResultSaveMessage($rezult)
	{
		switch ($rezult)
		{
			case 0:  $this->LastError = "Неразрешенная попытка доступа"; return;
			case -1: $this->LastError = "Неверный логин или пароль"; return;
			case -2: $this->LastError = "Потеря сеанса связи"; return;
			case -3:
			case -4:
			case -5:
			case -6:
			case -7:
			case -8:
			case -9:
			case -10:
			case -11:
			case -12:
			case -13:
			case -14:
			case -15:
			case -16:
			case -17:
			case -18:
			case -19:$this->LastError = "Cбой выполнения веб-метода. Обратитесь в ТП";return;
			case -20: $this->LastError = "Cбой сеанса связи";return;
			case -21: $this->LastError = "Cообщение уже подтверждено";return;
			case -22: $this->LastError = "Неверный идентификатор сообщения";return;
			case -30: $this->LastError = "Неизвестная кодировка сообщения";return;
			case -31: $this->LastError = "Неразрешенная зона тарификации";return;
			case -50: $this->LastError = "Неверный отправитель";return;
			case -52: $this->LastError = "Недостаточно средств на Вашем счете";return;
			case -68: $this->LastError = "Пользователь заблокирован";return;

			default:
			{
				$this->LastError = "Неопределенная ошибка в функции SaveMessage";
			}
		}
	}

		/*returns ton on address*/
	/*ton - one of parameters for using SaveMessage-service*/
	public function get_ton($addr)
	{
		$addr = htmlspecialchars($addr);
		if(preg_match('/^([0-9]{1,10})$/',$addr)) //short
			return 3;
		elseif (preg_match('/^8([0-9]{10})$/',$addr)) //federal
			return 2;
		elseif (preg_match('/^([0-9]{11,15})$/',$addr)) //general
			return 1;
		else// пїЅпїЅпїЅпїЅпїЅпїЅпїЅ
			return 5;
	}

	/*returns npi on address*/
	/*npi - one of parameters for using SaveMessage-service*/
	public function get_npi($addr)
	{
		$addr = htmlspecialchars($addr);
		if(preg_match('/^([0-9]{1,10})$/',$addr)) //пїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅ
			return 9;
		elseif (preg_match('/^8([0-9]{10})$/',$addr)) // пїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅ
			return 1;
		elseif (preg_match('/^([0-9]{11,15})$/',$addr)) // пїЅпїЅпїЅпїЅпїЅ
			return 1;
		else// пїЅпїЅпїЅпїЅпїЅпїЅпїЅ
			return 0;
	}

	/********************************
telephone number processing
********************************/
	public function is_phone($destination_numbers)
	{
		$destination_number = trim($destination_numbers);

		$arInd = trim($destination_numbers);
		$arInd = trim($destination_numbers,"+");

		$symbol = false;
		$spec_sym = array("+", "(", ")", " ", "-","_");
		for($i = 0; $i < strlen($arInd); $i++)
		{
			if (!is_numeric($arInd[$i]) && !in_array($arInd[$i],$spec_sym))
			{
				$symbol = true;
			}
		}

		if ($symbol)
		{
			return false;
		}
		else
		{
			$arInd = str_replace($spec_sym, "", $arInd);

			if (strlen($arInd) < 4 || strlen($arInd) > 11)
			{
				return false;
			}
			else
			{
				if (strlen($arInd) == 10)
				{
					$arInd = "7".$arInd;
				}

				if (strlen($arInd) == 11 && $arInd[0] == '8')
				{
					$arInd[0] = "7";
				}
				return $arInd;
			}
		}

		$numbers = array_unique($numbers);

		return $numbers;
	}

		//returns formatted date
	public function GetFormatDate($date)
	{
		$date = htmlspecialchars($date);
		if (preg_match("/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/",$date))
			return ConvertDateTime($date, "YYYYMMDD 23:59:59", "ru");
		if (preg_match("/^[0-9]{2}\.[0-9]{2}\.[0-9]{4} [0-9]{2}:[0-9]{2}:[0-9]{2}$/",$date))
			return ConvertDateTime($date, "YYYYMMDD HH:MI:SS", "ru");
		else
			return '';
	}

	/********************************
transliteration in MVD format
********************************/
public function Translit($cyr_str)
	{
		$tr = array(
				'а' =>	'a',
				'б'	=>	'b',
				'в'	=>	'v',
				'г'	=>	'g',
				'д'	=>	'd',
				'е'	=>	'e',
				'ё'	=>	'yo',
				'ж'	=>	'zh',
				'з'	=>	'z',
				'и'	=>	'i',
				'й'	=>	'j',
				'к'	=>	'k',
				'л'	=>	'l',
				'м'	=>	'm',
				'н'	=>	'n',
				'о'	=>	'o',
				'п'	=>	'p',
				'р'	=>	'r',
				'с'	=>	's',
				'т'	=>	't',
				'у'	=>	'u',
				'ф'	=>	'f',
				'х'	=>	'x',
				'ц'	=>	'c',
				'ч'	=>	'ch',
				'ш'=>	'sh',
				'щ'=>	'shh',
				'ъ'	=>	"\"",
				'ы'	=>	'y',
				'ь'	=>	"'",
				'э'	=>	'e',
				'ю'	=>	'yu',
				'я'	=>	'ya',
				'А' =>	'A',
				'Б'	=>	'B',
				'В'	=>	'V',
				'Г'	=>	'G',
				'Д'	=>	'D',
				'Е'	=>	'E',
				'Ё'	=>	'YO',
				'Ж'=>	'ZH',
				'З'	=>	'Z',
				'И'	=>	'I',
				'Й'	=>	'Y',
				'К'	=>	'K',
				'Л'	=>	'L',
				'М'	=>	'M',
				'Н'	=>	'N',
				'О'	=>	'O',
				'П'	=>	'P',
				'Р'	=>	'R',
				'С'	=>	'S',
				'Т'	=>	'T',
				'У'	=>	'U',
				'Ф'	=>	'F',
				'Х'	=>	'X',
				'Ц'	=>	'C',
				'Ч'	=>	'CH',
				'Ш'	=>	'SH',
				'Щ'	=>	'SHH',
				'Ъ'	=>	"\"",
				'Ы'	=>	'Y',
				'Ь'	=>	"'",
				'Э'	=>	'E',
				'Ю'	=>	'YU',
				'Я'	=>	'YA',
				'«' =>  '<',
				'»' =>  '>',
				'–' =>  '-'
		);

		$str = strtr($cyr_str,$tr);

		$str = str_replace(array('^','`'),"'",$str);
		$str = str_replace(array('”'),"\"",$str);
		$str = str_replace(array('{','['),"(",$str);
		$str = str_replace(array('}',']'),")",$str);
		$str = str_replace(array('\\'),"/",$str);
		$str = str_replace(array('_','~'),"-",$str);
		$str = str_replace(array('|'),"i",$str);
		$str = str_replace(array('№'),"N",$str);

		return $str;
	}
	


	/*
	sends sms
*/
	public function SendSMS($message, $to, $sender='', $IDOrder = 0, $Posting = 0, $TypeEvents = '')
	{
		if($sender == '')
			$sender = $this->DefSender;
		$to = $this->is_phone($to);
		if(strlen($sender) > 0 && $this->is_phone($to) && strlen($message) > 0)
		{
			$ston = $this->get_ton($sender);
			$snpi = $this->get_npi($sender);

			$dton = $this->get_ton($to);
			$dnpi = $this->get_npi($to);

			$body = $this->enCodeMessage($message);
			$encoded = $this->get_type_of_encoding($message);
			$sess_id = $this->GetSID();
			$date_actual = date("Ymd H:i:s",(time()+86400*7));
			$outsms_guid = $this->CreateGuid();


			$params_sms = 	array(
						"SessionID" => $this->GetSID(),
						"guid" => $outsms_guid,
						"Destination" => $to,
						"Source" => $sender,
						"Body" => $body,
						"Encoded" => $encoded,
						"dton" => $dton,
						"dnpi" => $dton,
						"ston" => $ston,
						"snpi" => $snpi,
						"TimeOff" =>$date_actual,
						"Priority" => 0,
						"NoRequest" => 0
				);

			$resSendMess = $this->GetSOAP("SaveMessage",$params_sms);

			if($resSendMess)
				return true;
			else
				return false;
		}

	}

	//--------------пїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅ пїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅпїЅ пїЅпїЅпїЅпїЅпїЅ пїЅпїЅпїЅпїЅпїЅ GroupSMS----------

	public function SendSmsPack($message, $to, $sender = '', $startUp_p='', $dateActual_p='', $period_p = '')
	{
		$session = $this->GetSID();
		$sendingError = array();
		$gmt = $this->gmt;
		$code = -1;
		$ston = $this->get_ton($sender);
		$snpi = $this->get_npi($sender);
		$encoded = $this->get_type_of_encoding($message);
		$body = $this->enCodeMessage($message);
		$dateActual = $dateActual_p;
		$startUp = $startUp_p;
		$period = $period_p;
		$destination = $this->parse_numbers($to);
		$numbersForSendCount = count($destination);

		if($sender == '')
			$sender = $this->DefSender;

		$sms_package = array();

		foreach($destination as $arInd)
		{
			$outsms_guid = $this->CreateGuid();

			$one_sms = '';
			$one_sms = array(
				"G"	=> $outsms_guid,
				"D"	=> $arInd,
				"B" => $body,
				"E" => $encoded,
			);

			$sms_package[] = $one_sms;
		}

		$results_of_package_send = array();

		$results_of_package_send["SEND"] = 0;
		$results_of_package_send["NOT_SEND"] = 0;

		if (count($sms_package) < $this->maxPackage)
		{
			/*$currentChunkLength = count($sms_package);*/

			$temp = array();
			$temp = $this->GetSOAP("GroupSMS", array(
				"SessionId"	=>	$session,
				"Group"		=>	$code,
				"Source"	=>  $sender,
				"Encoding"	=>  $encoded,
				"Body"		=>  $body,
				"Off"		=>  $dateActual,
				"Start"		=>  $startUp,
				"Period"	=>  $period,
				"List"		=>	$sms_package)
			);

			if (intval($temp['result']) > 0)
			{
				$results_of_package_send['SEND'] += $temp["result"];
				/*$results_of_package_send['NOT_SEND'] += $currentChunkLength - $temp["result"];*/
			}
			else
			{
				$results_of_package_send['NOT_SEND'] += count($sms_package);
			}
		}
		else
		{
			$big_array = array_chunk($sms_package, $this->maxPackage, true);

			$countAlreadySendNumbers = 0;

			foreach($big_array as $arIndex)
			{
				$currentChunkLength = count($arIndex);

					$temp = array();
					$temp = $this->GetSOAP("GroupSMS", array(
						"SessionId"	=>	$session,
						"Group"		=>	$code,
						"Source"	=>  $sender,
						"Encoding"	=>  $encoded,
						"Body"		=>  $body,
						"Off"		=>  $dateActual,
						"Start"		=>  $startUp,
						"Period"	=>  $period,
						"List"		=>	$arIndex)
					);

					if (intval($temp['result']) > 0)
					{
						$results_of_package_send['SEND'] += $temp["result"];
					}
					else
					{
						$results_of_package_send['NOT_SEND'] += count($sms_package);
					}
			}
		}
		return $results_of_package_send;
	}


	public function GetSender()
	{
		return $this->arBalance["Addresses"];
	}


	protected function UpdateSID()
	{
		if (!isset($_SESSION["SMS_START_SESSION"]) || $_SESSION["SMS_START_SESSION"] == '')
		{
			$this->MakeSID();
		}
		else
		{
			if(!$this->GetSOAP("AccountParams",array("SessionID" => $_SESSION["SMS_START_SESSION"])))
			{
				$this->MakeSID();
			}
			else
			{
				$this->sid = $_SESSION["SMS_START_SESSION"];
			}
		}
	}

	protected function MakeSID()
	{
		$arParam = array(
							"Login" => $this->login,
							"Password"=> $this->password,
							"Gmt" => $this->gmt
		);

		if($this->GetSOAP("StartSession",$arParam))
		{
			$this->GetSOAP("AccountParams",array("SessionID" => $this->sid));
			return true;
		}
		else
			return false;
	}

		/*parsing numbers for multiple sending of sms*/
	public function parse_numbers($destination_numbers)
	{
		if (!is_array($destination_numbers))
		{
			$destination_numbers = trim($destination_numbers);
			$dest_length = strlen($destination_numbers);

			$numbers = array();
			$sort_numbers = array();
			$destination_numbers = str_replace(array(",","\n"),";",$destination_numbers);
			$sort_numbers = explode(';',$destination_numbers);
		}
		else
		{
			$numbers = array();
			$sort_numbers = array();
			$sort_numbers = $destination_numbers;
		}

		foreach ($sort_numbers as $arInd)
		{
			$arInd = trim($arInd);

			$symbol = false;
			$spec_sym = array("+", "(", ")", " ", "-","_");
			for($i = 0; $i < strlen($arInd); $i++)
			{
				if (!is_numeric($arInd[$i]) && !in_array($arInd[$i],$spec_sym))
				{
					$symbol = true;
				}
			}

			if ($symbol)
			{
				$numbers[] = $arInd;
			}
			else
			{
				$arInd = str_replace($spec_sym, "", $arInd);

				if (strlen($arInd) < 4 || strlen($arInd) > 15)
				{
					continue;
				}
				else
				{
					if (strlen($arInd) == 10 && $arInd[0] == '9')
					{
						$arInd = '7'.$arInd;
					}
					if (strlen($arInd) == 11 && $arInd[0] == '8')
					{
						$arInd[0]="7";
					}
					$numbers[]=$arInd;
				}
			}
		}

		return array_unique($numbers);
	}

		public function GetFormatDateForSmsForm($date)
	{
		$date = htmlspecialchars($date);

		$forShortTime = date("H:i:s");
		if (preg_match("/^([0-9]{2})\-([0-9]{2})\-([0-9]{4})$/",$date, $matches))
		{
			if (checkdate($matches[2], $matches[1], $matches[3]))
			{
				return 	$matches[3].$matches[2].$matches[1].' '.$forShortTime;
			}
			else
			{
				return -1;
			}
		}

		if (preg_match("/^([0-9]{2})-([0-9]{2})-([0-9]{4}) [0-9]{2}:[0-9]{2}:[0-9]{2}$/",$date, $matches))
		{
			if (checkdate($matches[2], $matches[1], $matches[3]))
			{
				$daysHours = explode(' ', $date);
				return 	$matches[3].$matches[2].$matches[1].' '.$daysHours[1];
			}
			else
			{
				return -1;
			}
		}

		return -1;
	}

	public function GetTimeStamp($date)
	{
		if (preg_match("/^([0-9]{2})\-([0-9]{2})\-([0-9]{4})$/",$date, $matches))
		{
			return mktime(0, 0, 0, $matches[2], $matches[1], $matches[3]);
		}

		if (preg_match("/^([0-9]{2})-([0-9]{2})-([0-9]{4}) ([0-9]{2}):([0-9]{2}):([0-9]{2})$/",$date, $matches))
		{
			return mktime($matches[4], $matches[5], $matches[6], $matches[2], $matches[1], $matches[3]);
		}

		return -1;
	}

}
?>
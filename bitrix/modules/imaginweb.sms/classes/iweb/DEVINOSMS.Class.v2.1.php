<?php


Class DEVINOSMS {



    /**
    * ����������� ������ ������� �� ������
    *
    * @param $status int ������ �������� �� �������
    *
    * @return string ����������� ������ �������� �� �������
    */
    function GetCommandStatus($status){
      switch($status){
        case 0:
          return GetMessage("IMAGINWEB_SMS_OPERACIA_VYPOLNENA");
        break;

        case 10:
          return GetMessage("IMAGINWEB_SMS_OSIBKA_NEKORREKTNYY");
        break;

        case 11:
          return GetMessage("IMAGINWEB_SMS_OSIBKA_NEKORREKTNYY1");
        break;

        case 12:
          return GetMessage("IMAGINWEB_SMS_OSIBKA_NEKORREKTNYY2");
        break;

        case 14:
          return GetMessage("IMAGINWEB_SMS_OSIBKA_NEPRAVILQNYY");
        break;

        case 15:
          return GetMessage("IMAGINWEB_SMS_OSIBKA_NEPRAVILQNYY1");
        break;

        case 20:
          return GetMessage("IMAGINWEB_SMS_OSIBKA_OCEREDQ_SOOB");
        break;

        case 88:
          return GetMessage("IMAGINWEB_SMS_OSIBKA_NEDOSTATOCNO");
        break;

        case 1501:
          return GetMessage("IMAGINWEB_SMS_OSIBKA_NEKORREKTNYE");
        break;

        case 1502:
          return GetMessage("IMAGINWEB_SMS_OSIBKA_NEKORREKTNYY3");
        break;

        case 1503:
          return GetMessage("IMAGINWEB_SMS_NEAVTORIZOVANNYY");
        break;

        case 1504:
          return GetMessage("IMAGINWEB_SMS_OSIBKA_SERVIS_NEDOS");
        break;

        case 1505:
          return GetMessage("IMAGINWEB_SMS_OSIBKA_SERVER_ZANAT");
        break;

        case 1506:
          return GetMessage("IMAGINWEB_SMS_OSIBKA_SERVER_BAZY");
        break;

        case 1507:
          return GetMessage("IMAGINWEB_SMS_POLQZOVATELQ_ZABLOKI");
        break;

        case 1508:
          return GetMessage("IMAGINWEB_SMS_ZAPRESENNYY").' host address';
        break;

        case 1509:
          return GetMessage("IMAGINWEB_SMS_ZAPRESENNYY_TIP_DOST");
        break;

        default:
          return $status;
        break;

      }

    }


    /**
    * ����������� ������� ���������
    *
    * @param $status int ������ ���������
    *
    * @return string ����������� ������� ���������
    */
    function GetMessageStatus($status){
      switch($status){
        case -97:
          return GetMessage("IMAGINWEB_SMS_SOOBSENIE_UDVLENO");
        break;

        case -40:
          return GetMessage("IMAGINWEB_SMS_SOOBSENIE_NAHODITSA");
        break;

        case -30:
          return GetMessage("IMAGINWEB_SMS_SOOBSENIE_PEREDANO_N");
        break;

        case -20:
          return GetMessage("IMAGINWEB_SMS_SOOBSENIE_NAHODITSA1");
        break;

        case -10:
          return GetMessage("IMAGINWEB_SMS_SOOBSENIE_PEREDANO_V");
        break;

        case 0:
          return GetMessage("IMAGINWEB_SMS_SOOBSENIE_DOSTAVLENO");
        break;

        case 10:
          return GetMessage("IMAGINWEB_SMS_OSIBKA_NEKORREKTNYY");
        break;

        case 11:
          return GetMessage("IMAGINWEB_SMS_OSIBKA_NEKORREKTNYY1");
        break;

        case 41:
          return GetMessage("IMAGINWEB_SMS_OSIBKA_SOOBSENIE_NE");
        break;

        case 42:
          return GetMessage("IMAGINWEB_SMS_OSIBKA_SOOBSENIE_OT");
        break;

        case 46:
          return GetMessage("IMAGINWEB_SMS_OSIBKA_ISTEK_SROK_J");
        break;

        default:
          return GetMessage("IMAGINWEB_SMS_STATUS_NE_RASPOZNAN");
        break;

      }

    }


    /**
    * ������������ � �������� ������� �� ������ ����� cURL
    *
    * @param $xml_data string XML-������ � ������� (SOAP)
    * @param $headers string ��������� ������� � ������� (SOAP)
    *
    * @return string XML-����� �� ������� (SOAP)
    */
    function SendToServer($xml_data,$headers){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"http://ws.devinosms.com/SmsService.asmx");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_data);
        $data = curl_exec($ch);

        if (curl_errno($ch)) {
            die("Error: " . curl_error($ch));
        } else {
            curl_close($ch);
            return $data;
        }
    }



    /**
    * GetSessionID � ������ �� ��������� �������������� �����
    *
    * @param $UserLogin string ����� ������������
    * @param $Password string ������ ������������
    *
    * @return array("SessionID" => (string) ����� ������� � ���� ������� ������
    */

    function GetSessionID($UserLogin,$Password){
        $xml_data = '<?xml version="1.0" encoding="utf-8"?>
		<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  	    <soap:Body>
    	    <GetSessionID xmlns="http://ws.devinosms.com">
              <login>'.$UserLogin.'</login>
              <password>'.$Password.'</password>
            </GetSessionID>
  	  </soap:Body>
	</soap:Envelope>';

        $headers = array(
            "POST /SmsService.asmx HTTP/1.1",
            "Host: ws.devinosms.com",
            "Content-Type: text/xml; charset=utf-8",
            "Content-length: ".strlen($xml_data),
            "SOAPAction: http://ws.devinosms.com/GetSessionID"
        );

        $data = $this->SendToServer($xml_data,$headers);

        $p = xml_parser_create();
        xml_parse_into_struct($p,$data,$results);
        xml_parser_free($p);
		

        return array(
            "SessionID" => $results[3]['value']
        );
    }




    /**
    * SendTextMessage - �������� �������� ���������� SMS-���������
    *
    * @param $SessionID string ������������� ������
    * @param $DestinationAddresses string ��������� ���������� ����� ���������� ���������, � ������������� �������: ��� ������ + ��� ���� + ����� ��������./������ ��������� ���������
    * @param $Parameters string ������ ���������� ���������.
    * @param $Data string ����� ���������

    *
    * @return array("CommandStatus" => (string) ����� �������, "MessageID" => (decimal)) ID ��� ���������/������ ID ��� ���������
    */
    function SendMessage($SessionID, $Data, $DestinationAddresses,$SourceAddress,$ReceiptRequested, $CountDA){

        $xml_data = '<?xml version="1.0" encoding="utf-8"?>
        <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
          <soap:Body>
            <SendMessage xmlns="http://ws.devinosms.com">
              <sessionID>'.$SessionID.'</sessionID>
	      <message>
                <Data>'.$Data.'</Data>
                <DestinationAddresses>'.$DestinationAddresses.'</DestinationAddresses>
                <SourceAddress>'.$SourceAddress.'</SourceAddress>
                <ReceiptRequested>'.$ReceiptRequested.'</ReceiptRequested>
              </message>
            </SendMessage>
          </soap:Body>
        </soap:Envelope>';

        $headers = array(
            "POST /SmsService.asmx HTTP/1.1",
            "Host: ws.devinosms.com",
            "Content-Type: text/xml; charset=utf-8",
            "SOAPAction: http://ws.devinosms.com/SendMessage"
        );

		$data = $this->SendToServer($xml_data,$headers);
        // Show me the result
        $p = xml_parser_create();
        xml_parse_into_struct($p,$data,$results);
        xml_parser_free($p);
		
		$res["CommandStatus"] = $this->GetCommandStatus($results[3]['value']);
		$n=0;
		        
		while($n!=$CountDA)
		{
			$res["MessageID".($n+1).""] = $results[4+$n]['value'];
			$n++;
		}
				
		return $res;
    }


    /**
    * GetMessageState � ������ �� ��������� ������ ������������� SMS-���������
    *
    * @param $SessionID string ������������� ������
    * @param $MessageID string ������������� ���������
    *
    * @return array("CommandStatus" => (string) ����� �������, "MessageStatus" => (string) ������� ���������/������ �������� ���������, "Date" => (string)) ���� ��������� ������
    */
    function GetMessageState($SessionID,$MessageID){

        $xml_data = '<?xml version="1.0" encoding="utf-8"?>
        <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://
www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
        <soap:Body>
            <GetMessageState xmlns="http://ws.devinosms.com">
              <sessionID>'.$SessionID.'</sessionID>
              <messageID>'.$MessageID.'</messageID>
            </GetMessageState>
          </soap:Body>
        </soap:Envelope>';

	$headers = array(
            "POST /SmsService.asmx HTTP/1.1",
            "Host: ws.devinosms.com",
            "Content-Type: text/xml; charset=utf-8",
            "Content-length: ".strlen($xml_data),
            "SOAPAction: http://ws.devinosms.com/GetMessageState"
        );

        $data = $this->SendToServer($xml_data,$headers);

        $p = xml_parser_create();
        xml_parse_into_struct($p,$data,$results);
        xml_parser_free($p);
        return array(
          "State" => $this->GetCommandStatus($results[4]['value']),
          //"TimeStampUtc" => join(' ',split('T',$results[5]['value'])),
          "StateDescription" =>$results[6]['value']
        );
    }
	/**
    * GetMessageState � ������ �� ��������� ��������� �����
    *
    * @param $SessionID string ������������� ������
    * ������ ������:
	* $bal = $devino->GetBalance($result[SessionID]); // ���������� $bal ������� �������� �����.
    *
    * return array("GetBalanceResult" => iconv("UTF-8","WINDOWS-1251",$results[3]['value']) -- ��������� �����. ���� $results[3] ������� 
	* �� $results[4], ������ ������ �������(������ ����� ������ ����������� ���������)
    */
	function GetBalance($SessionID){

        $xml_data = '<?xml version="1.0" encoding="utf-8"?>
        <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://
www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
		  <soap:Body>
			<GetBalance xmlns="http://ws.devinosms.com">
				<sessionID>'.$SessionID.'</sessionID>
			</GetBalance>
		  </soap:Body>
        </soap:Envelope>';

	$headers = array(									//����� �� ��� � � GetMassageState
            "POST /SmsService.asmx HTTP/1.1",
            "Host: ws.devinosms.com",
            "Content-Type: text/xml; charset=utf-8",
            "Content-length: ".strlen($xml_data),
            "SOAPAction: http://ws.devinosms.com/GetBalance"
        );

        $data = $this->SendToServer($xml_data,$headers);
		
		$p = xml_parser_create();
        xml_parse_into_struct($p,$data,$results);
        xml_parser_free($p);
		//$NewString =$results[3]."med";
		return array(
            "GetBalanceResult" => $results[3]['value'],
        );

    }
}




?>
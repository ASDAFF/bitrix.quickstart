<?php
    /* 
        ������ 2.02
        	* codepage ������� � �����������
        ������ 2.01
            * ������� ����������� ������. ������ sender_id, ������� ������������, api_key � partner_key ����� ��������� ��� �������� ������ � �������� ���������
        ������ 2.00
            * �������� �������� partner_key
            * ����������� ������� ������� ������� ������������
            * ����������� �������� ������� �������� ������������ ��� API_KEY �� ��� �� ��� ����� ��������
    */
    
	class SMSkontakt
	{		
        
        //��������� �����
        var $codepage = "UTF-8";  
        
        //����� ��������
		var $url_message_send	= 'http://sms-kontakt.ru/api/message/send/';
		var $url_get_info		= 'http://sms-kontakt.ru/api/get_info/';
		var $url_send_api_key	= 'http://sms-kontakt.ru/api/send_api_key/';
        
        var $user_phone		= ''; 
        var $api_key		= '';         
        var $partner_key	= 'zeshji';
        
		var $sign;
		var $sender_id;
		
		function SMSkontakt(	$sender_id='SMS-kontakt', 
								$user_phone='', 
								$api_key='', 
								$codepage = false,
								$partner_key=''								
							)
		{
			$this->sender_id = $sender_id;
            if (!($user_phone === ''))
                $this->user_phone = $user_phone;
            if (!($api_key === ''))
                $this->api_key = $api_key;
            if (!($partner_key === ''))
                $this->partner_key = $partner_key;
            if (!$codepage)
                $this->codepage = LANG_CHARSET;
            
            $this->codepage = $codepage;
            
            $this->sign = md5($this->user_phone.$this->api_key);
		}
        
		function SendPostRequest($url, $headers, $post_body)
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url); // ��� ��������
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_body); // ������� post-������
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //
			$result = curl_exec($ch); // �������� ��������� � ����������
			curl_close($ch);
			return $result;
		}	
		
		function MessageSend($phone_to, $message, $test='0')
		{   
            $http_body = 'user_phone='.$this->user_phone.'&sign='.$this->sign.'&phone_to='.$phone_to.'&message='.$message.'&sender_id='.$this->sender_id.'&test='.$test.'&partner_key='.$this->partner_key;
			$headers[] = 'Content-Type: text/xml; charset=utf-8';
			$headers[] = 'Content-Length: ' . strlen($http_body);
			$server_answer = $this->SendPostRequest($this->url_message_send, $headers, $http_body);
			return $server_answer;
		}
        
		function GetInfo($info_type)
		{
            $http_body = 'user_phone='.$this->user_phone.'&sign='.$this->sign.'&info='.$info_type;
			$headers[] = 'Content-Type: text/xml; charset=utf-8';
			$headers[] = 'Content-Length: ' . strlen($http_body);
			$server_answer = $this->SendPostRequest($this->url_get_info, $headers, $http_body);
			return $server_answer;
		}
        
		function SendAPIKeyToUser()
		{
            $http_body = 'user_phone='.$this->user_phone.'&partner_key='.$this->partner_key;
			$headers[] = 'Content-Type: text/xml; charset=utf-8';
			$headers[] = 'Content-Length: ' . strlen($http_body);
			$server_answer = $this->SendPostRequest($this->url_send_api_key, $headers, $http_body);
			return $server_answer;
		}
	}
?>
<?

define("STOP_STATISTICS", true); 

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

	function post($host,$script,$data)
	{ 
		$fp = fsockopen($host,80,$errno, $errstr, 30 );  
	         
		if ($fp) { 
			fputs($fp, "POST $script HTTP/1.1\n");  
			fputs($fp, "Host: $host\n");  
			fputs($fp, "Content-type: application/x-www-form-urlencoded\n");  
			fputs($fp, "Content-length: " . strlen($data) . "\n");
			fputs($fp, "User-Agent: PHP Script\n");
			fputs($fp, "Connection: close\n\n");  
			fputs($fp, $data);  
			while(fgets($fp,2048) != "\r\n" && !feof($fp));
			unset($buf);
			while(!feof($fp)) $buf .= fread($fp,2048);
			fclose($fp); 
				
		}
		else{ 
			return "Сервер не отвечает"; 
		}
		return $buf; 
	}
	
	function utf2win($input){
		return iconv("UTF-16BE", "cp1251", pack("H4", $input[1]));
	}
		
	$text = str_replace("#a#", "&", $_REQUEST['text']); 

	$word = preg_replace_callback('!%u([\da-f]{4})!i', 'utf2win', $text);
	
	$response =  post('www.typograf.ru','/webservice/','text='.urlencode($word));

	if(defined("BX_UTF"))
		echo  iconv("cp1251", "utf8", $response);
	else
		echo $response;


?>
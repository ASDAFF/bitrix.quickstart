<?
IncludeModuleLangFile(__FILE__);

class DSocialPosterCUrlConnection implements DSocialPosterConnection
{
	private $curlHandle;
	private $bConnected = false;
	private $userAgent = "Mozilla/5.0 (Windows; U; Windows NT 6.1; ru; rv:1.9.2.13) Gecko/20101203 Firefox/3.6.13";
	private $postfix;
	private $timeout;
	private $maxredirs;
	private $cookieLifeTime;
	private $sLastError = "";
	private $lastResult = "";
	
	public function __construct($postfix="", $timeout=30, $maxredirs=15, $cookieLifeTime=3600)
	{
		$this->postfix = (string) $postfix; 
		$this->timeout = (int) $timeout; 
		$this->maxredirs = (int) $maxredirs; 
		$this->cookieLifeTime = (int) $cookieLifeTime; 
	}
	public function GetHandle()
	{
		if(!$this->bConnected)
			$this->Open();

		return $this->curlHandle;
	}
	public function SetHandleOption($name, $value)
	{
		return curl_setopt($this->GetHandle(), $name, $value);
	}
	public function Open()
	{
		if($this->bConnected)
			$this->Close();

		$this->curlHandle = curl_init();
		$this->bConnected = true;

		$this->SetHandleOption(CURLOPT_RETURNTRANSFER, 1);
		$this->SetHandleOption(CURLOPT_USERAGENT, $this->userAgent);
		$this->SetHandleOption(CURLOPT_FOLLOWLOCATION, 1);
		$this->SetHandleOption(CURLOPT_HEADER, 1);
		$this->SetHandleOption(CURLOPT_TIMEOUT, $this->timeout);
		$this->SetHandleOption(CURLOPT_CONNECTTIMEOUT, $this->timeout);
		$this->SetHandleOption(CURLOPT_MAXREDIRS, $this->maxredirs);
		$this->SetHandleOption(CURLOPT_SSL_VERIFYPEER, false);
		$this->SetHandleOption(CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0); /* fix bug for facebook (curl errno=18) */

	}
	public function Close()
	{
		if($this->bConnected)
		{
	                curl_close($this->curlHandle);
	                $this->bConnected = $this->curlHandle = false;
		}
                return true;
	}
	public function SetCookies(array $arCookies)
	{
		
	}
	public function GetLastResult()
	{
		return $this->lastResult;
	}
	public function Exec()
	{
		$loops = 0;

		if ($loops++ >= $this->maxredirs)
			return false;

		$data = $this->lastResult = curl_exec($this->GetHandle());

		list($header, $body) = explode("\r\n\r\n", $data, 2);
		$httpCode = curl_getinfo($this->GetHandle(), CURLINFO_HTTP_CODE);

		if (in_array($httpCode, array(301, 302))) {

			preg_match("/Location:(.*?)\n/i", $header, $matches);

			$url = @parse_url(trim(array_pop($matches)));
			if (!$url)
				return $body;

			$lastUrl = parse_url(curl_getinfo($this->GetHandle(), CURLINFO_EFFECTIVE_URL));

			if (empty($url["scheme"]))	$url["scheme"] = $lastUrl["scheme"];
			if (empty($url["host"]))	$url["host"] = $lastUrl["host"];
			if (empty($url["path"]))	$url["path"] = $lastUrl["path"];

			$this->SetHandleOption(CURLOPT_URL, implode("", array($url["scheme"]."://", $url["host"], $url["path"], $url["query"]?"?".$url["query"]:"")));
			$this->SetHandleOption(CURLOPT_HTTPGET, 1);

			return $this->Exec();
		}
		else
			return $data;
	}
	public function GetLastError()
	{
		return $this->sLastError;
	}
	public function Execute($returnType=false)
	{
		$this->sLastError = "";
		if(!$this->bConnected)
		{
			$this->sLastError = "Connection not opened";
			return false;
		}
		$this->SetHandleOption(CURLOPT_HEADER, 1);
		//$this->SetHandleOption(CURLOPT_NOBODY, 0);				
		
		switch($returnType)
		{
			case DSocialPosterConnectionReturnStates::HEADER_ONLY:
				
				$this->SetHandleOption(CURLOPT_HEADER, 1);
				//$this->SetHandleOption(CURLOPT_NOBODY, 1);
				break;
				
			case DSocialPosterConnectionReturnStates::BODY_ONLY:
				
				$this->SetHandleOption(CURLOPT_HEADER, 0);
				//$this->SetHandleOption(CURLOPT_NOBODY, 0);
				break;
		}

		$res = $this->Exec();

		if(curl_errno($this->GetHandle()) == 0) {
			$this->Open();
			return $res;
		}

		$this->sLastError = curl_error($this->GetHandle());
		return false;
	}
	public function Send($url, $arFields = "", $returnType = false)
	{

		$this->SetHandleOption(CURLOPT_URL, $url);

		preg_match_all("/\/\/([^\/]+)\//smi", $url, $_domain);
		$domain = $fullDomain = ToLower($_domain[1][0]);

		if (substr_count($domain, ".") > 1)
			$domain = substr($domain, strrpos_ex($domain, ".", -(strlen($domain)+1-strrpos_ex($domain, ".")))+1);

		if (substr($domain, 0, 4) == "www.")
			$domain = substr($domain, 4);

		if (strlen($domain) > 0) {

			CheckDirPath($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/".DSocialMediaPoster::$MODULE_ID."/tmp/");
			$cookieFile = $_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/".DSocialMediaPoster::$MODULE_ID."/tmp/".str_replace(".", "_", $domain)."_".$this->postfix.".txt";

			if (file_exists($cookieFile) && (time()-filemtime($cookieFile)) >= $this->cookieLifeTime)
				@unlink($cookieFile);

			$this->SetHandleOption(CURLOPT_COOKIEJAR, $cookieFile);
			$this->SetHandleOption(CURLOPT_COOKIEFILE, $cookieFile);
		}

		if (!empty($arFields)) {
			$this->SetHandleOption(CURLOPT_POST, 1);
			$this->SetHandleOption(CURLOPT_POSTFIELDS, $arFields);
		}

		return $this->Execute($returnType);
	}
}
?>

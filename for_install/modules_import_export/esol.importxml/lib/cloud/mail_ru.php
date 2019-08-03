<?php
namespace Bitrix\EsolImportxml\Cloud;

class MailRu
{
	protected static $moduleId = 'esol.importxml';
	protected static $instance = null;
	protected $lastLocation = '';
	
    function __construct($user, $pass)
    {
        $this->user = $user;
        $this->pass = $pass;
		$this->cookies = array();
        $this->dir = dirname(__FILE__);
        $this->token = '';
		$this->downloadToken = '';
        $this->x_page_id = '';
        $this->build = '';
        $this->upload_url = '';
        $this->ch = '';
		$this->weblink_get = '';
		
		$this->isReady = $this->login();
    }
	
	public static function GetInstance()
	{
		if(!isset(static::$instance))
		{
			$user = \Bitrix\Main\Config\Option::get(static::$moduleId, 'CLOUD_MAILRU_LOGIN', '');
			$pass = \Bitrix\Main\Config\Option::get(static::$moduleId, 'CLOUD_MAILRU_PASSWORD', '');
			static::$instance = new static($user, $pass);
		}
		return static::$instance;
	}

    function login()
    {
        $url = 'https://auth.mail.ru/cgi-bin/auth?lang=ru_RU&from=authpopup';

        $postData = array(
            "page" => "https://cloud.mail.ru/?from=promo",
            "FailPage" => "",
            "Domain" => "mail.ru",
            "Login" => $this->user,
            "Password" => $this->pass,
            "new_auth_form" => "1"
        );

        if ($this->Post($url, $postData) !== 'error') {
            if ($this->getToken()/* && $this->getDownloadToken()*/) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private function getToken()
    {
        $url = 'https://cloud.mail.ru/?from=promo&from=authpopup';
		
        $result = $this->Get($url);
        if ($result !== 'error') {
            $token = self::GetTokenFromText($result);
			$downloadToken = self::GetDownloadTokenFromText($result);
			/*New auth*/
			if (($token == '' || $downloadToken == '') && $this->lastLocation) {				
				while(strlen($this->lastLocation) > 0 && strpos($result, 'tokens')===false)
				{
					$result = $this->Get($this->lastLocation);
				}
				if ($result !== 'error') {
					$token = self::GetTokenFromText($result);
					$downloadToken = self::GetDownloadTokenFromText($result);
				}
			}
			/*/New auth*/
            if ($token == '' || $downloadToken == '') {				
				return false;
            } else {
                $this->token = $token;
				$this->downloadToken = $downloadToken;
                $this->x_page_id = self::GetXPageIdFromText($result);
                $this->build = self::GetBuildFromText($result);
                $this->upload_url = self::GetUploadUrlFromText($result);
				$this->weblink_get = self::GetWeblinkGetFromText($result);
                return true;
            }
        } else {
            return false;
        }
    }
	
    /*function getDownloadToken()
    {
        $url = 'https://cloud.mail.ru/api/v2/tokens/download';

        $postData = ''
                . 'api=2'
                . '&build=' . $this->build
                . '&email=' . $this->user //. '%40mail.ru'
                . '&token=' . $this->token
                . '&x-email=' . $this->user //. '%40mail.ru'
                . '&x-page-id=' . $this->x_page_id;

        $result = $this->Post($url, $postData);
        if ($result !== 'error') {
			$result = \CUtil::JsObjectToPhp($result);
			if($result['body']['token'])
			{
				$this->downloadToken = $result['body']['token'];
				return true;
			} else {
				return false;
			}
        } else {
            return false;
        }
    }*/
	
    function getZipLink($path, $name)
    {
        $url = 'https://cloud.mail.ru/api/v2/zip';

        $postData = ''
                . 'api=2'
                . '&build=' . $this->build
                . '&email=' . $this->user //. '%40mail.ru'
                . '&token=' . $this->token
                . '&x-email=' . $this->user //. '%40mail.ru'
                . '&x-page-id=' . $this->x_page_id
				. '&weblink_list=' . urlencode('["'.$path.'"]')
				. '&name=' . urlencode($name)
				. '&cp866=' . 'true';

        $result = $this->Post($url, $postData);
        if ($result !== 'error') {
			$result = \CUtil::JsObjectToPhp($result);
            return $result['body'];
        } else {
            return "error";
        }
    }
	
    function getLinkByMask($path, $pattern)
    {
		$link = preg_replace('/^https?:\/\/cloud\.mail\.ru\/public\//i', '/', $path);
		$url = 'https://cloud.mail.ru/api/v2/folder';

		$postData = ''
				. 'api=2'
				. '&build=' . $this->build
				. '&email=' . $this->user //. '%40mail.ru'
				. '&token=' . $this->token
				. '&x-email=' . $this->user //. '%40mail.ru'
				. '&x-page-id=' . $this->x_page_id
				. '&weblink=' . urlencode(trim($link, '/'))
				. '&offset=' . 0
				. '&limit=' . '9999';

		$result = $this->Post($url, $postData);
		if ($result !== 'error') {
			$result = \CUtil::JsObjectToPhp($result);
			if(is_array($result['body']['list']))
			{
				foreach($result['body']['list'] as $arItem)
				{
					if($arItem['type']=='file' && fnmatch($pattern, $arItem['name'], GLOB_BRACE))
					{
						return $arItem['weblink'];
					}
				}
			}
		}
		return '';
    }
	
	public function download(&$tmpPath, $path, $fragment='')
	{
		if(!$this->isReady) return false;
		$link = preg_replace('/^https?:\/\/cloud\.mail\.ru\/public\//i', '/', $path);
		$fileLink = $this->weblink_get.$link.'?key='.$this->downloadToken;
		
		if($this->DownloadFile($tmpPath, $fileLink))
		{
			return true;
		}
		else
		{
			if(strlen($fragment) > 0 && ($link2 = $this->getLinkByMask($path, $fragment)))
			{
				$fileLink = $this->weblink_get.'/'.$link2.'?key='.$this->downloadToken;
				if($this->DownloadFile($tmpPath, $fileLink))
				{
					return true;
				}
			}
			
			//$zipLink = 'https://cloud.mail.ru'.$this->getZipLink($link, 'folder').'?key='.$this->downloadToken;
			$zipLink = $this->getZipLink($link, 'folder').'?key='.$this->downloadToken;
			$tmpPath = \Bitrix\KdaImportexcel\Cloud::GetTmpFilePath('folder.zip');
			if($this->DownloadFile($tmpPath, $zipLink))
			{
				return true;
			}
		}
		return false;
	}
	
	public function DownloadFile($tmpPath, $link)
	{
		$client = new \Bitrix\Main\Web\HttpClient(array('socketTimeout'=>10, 'disableSslVerification'=>true));
		$client->setHeader('User-Agent', 'BitrixSM HttpClient class');
		
		if(is_callable(array($client, 'head')))
		{
			$headers = $client->head($link);
		}
		else
		{
			$client->get($link);
			$headers = $client->getHeaders();
		}
		if($client->getStatus()==200)
		{
			$fn = '';
			if($hcd = $headers->get('content-disposition'))
			{
				$arParts = preg_grep('/^filename=/i', array_map('trim', explode(';', $hcd)));
				if(count($arParts) > 0)
				{
					$fn = urldecode(trim(substr(current($arParts), 9), ' "'));
					if((!defined('BX_UTF') || !BX_UTF) && \CUtil::DetectUTF8($fn))
					{
						$fn = \Bitrix\Main\Text\Encoding::convertEncoding($fn, 'UTF-8', 'CP1251');
					}
				}
			}
			if($client->download($link, $tmpPath))
			{
				return true;
			}
		}
		return false;
	}
	
	private function Post($url, $data)
	{
		$client = $this->GetHttpClient(false);
		$result = $client->post($url, $data);
		/*$i = 0;
		while($i < 5 && ($result = $client->post($url, $data)) && in_array($client->getStatus(), array(301, 302)) && ($this->GetResult($result, $client)))
		{
			$url = $client->getHeaders()->get("Location");
			$client = $this->GetHttpClient(false);
			$i++;
		}*/
		return $this->GetResult($result, $client);
	}
	
	private function Get($url)
	{
		$client = $this->GetHttpClient(false);
		$result = $client->get($url);
		/*$client = $this->GetHttpClient(false);
		//$result = $client->get($url);
		$i = 0;
		while($i < 5 && ($result = $client->get($url)) && in_array($client->getStatus(), array(301, 302)) && ($this->GetResult($result, $client)))
		{
			$url = $client->getHeaders()->get("Location");
			$client = $this->GetHttpClient(false);
			$i++;
		}*/
		return $this->GetResult($result, $client);
	}
	
	private function GetResult($result, $client)
	{
		if(in_array($client->getStatus(), array(200, 301, 302)) && !empty($result))
		{
			$arCookies = $client->getCookies()->toArray();
			$this->cookies = array_merge($this->cookies, $arCookies);
			$this->lastLocation = $client->getHeaders()->get("Location");
			return $result;
		}
		else return "error";
	}
	
	private function GetHttpClient($redirect = true)
	{
		$client = new \Bitrix\Main\Web\HttpClient(array('socketTimeout'=>10, 'disableSslVerification'=>true, 'redirect'=>$redirect));
		$client->setHeader('User-Agent', 'BitrixSM HttpClient class');
		$client->setHeader('Cookie', implode('; ', array_map(create_function('$k,$v', 'return $k."=".$v;'),array_keys($this->cookies), $this->cookies)));
		//$client->setCookies($this->cookies);
		return $client;
	}

    private static function GetTokenFromText($str)
    {
		if(preg_match('/"tokens":[^\}]*"csrf":\s*"([^"]*)"/Uis', $str, $m)) {
            return $m[1];
        } else {
            return '';
        }
    }
	
    private static function GetDownloadTokenFromText($str)
    {
		if(preg_match('/"tokens":[^\}]*"download":\s*"([^"]*)"/Uis', $str, $m)) {
            return $m[1];
        } else {
            return '';
        }
    }

    private static function GetXPageIdFromText($str)
    {
        $start = strpos($str, '"x-page-id": "');
        if ($start > 0) {
            $start = $start + 14;
            $str_out = substr($str, $start, 11);
            return $str_out;
        } else {
            return '';
        }
    }

    private static function GetBuildFromText($str)
    {
        $start = strpos($str, '"BUILD": "');
        if ($start > 0) {
            $start = $start + 10;

            $str_temp = substr($str, $start, 100);

            $end = strpos($str, '"');

            $str_out = substr($str_temp, 0, $end - 1);
            return $str_out;
        } else {
            return '';
        }
    }

    private static function GetUploadUrlFromText($str)
    {
        $start = strpos($str, 'mail.ru/upload/"');
        if ($start > 0) {
            $start1 = $start - 50;
            $end1 = $start + 15;
            $lehgth = $end1 - $start1;
            $str_temp = substr($str, $start1, $lehgth);

            $start2 = strpos($str_temp, 'https://');
            $str_out = substr($str_temp, $start2, strlen($str_temp) - $start2);
            return $str_out;
        } else {
            return '';
        }
    }
	
    private static function GetWeblinkGetFromText($str)
    {
        if(preg_match('/"weblink_get":[^\]]*"url":[^\]]*"([^"]*)"/Uis', $str, $m)) {
            return $m[1];
        } else {
            return '';
        }
    }
}
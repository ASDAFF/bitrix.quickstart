<?php
namespace Bitrix\EsolImportxml;

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class Cloud
{
	protected static $moduleId = 'esol.importxml';
	protected $services = array(
		'yadisk' => '/^https?:\/\/yadi\.sk\//i',
		'mailru' => '/^https?:\/\/cloud\.mail\.ru\/public\//i',
		'gdrive' => array(
			'/^https?:\/\/drive\.google\.com\/open\?id=/i',
			'/^https?:\/\/drive\.google\.com\/file\/d\/[^\/]+(\/|$)/i',
			'/^https?:\/\/docs\.google\.com\/spreadsheets.*\/d\/[^\/]+(\/|$)/i',
			'/^https?:\/\/www\.google\.com\/.*https?:\/\/docs\.google\.com\/spreadsheets.*\/d\/[^\/]+(\/|$)/i'
		),
		'dropbox' => '/^https?:\/\/www\.dropbox\.com\/.*\?dl=[01](\D|$)/i'
	);
	
	public function GetService($link)
	{
		foreach($this->services as $k=>$v)
		{
			if(is_array($v))
			{
				foreach($v as $v2)
				{
					if(preg_match($v2, $link)) return $k;
				}
			}			
			elseif(preg_match($v, $link)) return $k;
		}
		return false;
	}
	
	public function MakeFileArray($service, $path)
	{
		$method = ucfirst($service).'GetFile';
		if(!is_callable(array($this, $method))) return false;
		
		$tmpPath = static::GetTmpFilePath($path);
		if(call_user_func_array(array($this, $method), array(&$tmpPath, $path)))
		{
			$arFile = \CFile::MakeFileArray($tmpPath);
			if(strlen($arFile["type"])<=0)
				$arFile["type"] = "unknown";
			return $arFile;
		}
		else
		{
			return false;
		}
	}
	
	public static function GetTmpFilePath($path)
	{
		$urlComponents = parse_url($path);
		if ($urlComponents && strlen($urlComponents["path"]) > 0)
		{
			$urlComponents["path"] = urldecode($urlComponents['path']);
			$tmpPath = \CFile::GetTempName('', bx_basename($urlComponents["path"]));
		}
		else
			$tmpPath = \CFile::GetTempName('', bx_basename($path));
		
		$dir = \Bitrix\Main\IO\Path::getDirectory($tmpPath);
		\Bitrix\Main\IO\Directory::createDirectory($dir);
		return $tmpPath;
	}
	
	public static function YadiskGetLinksByMask($path)
	{
		$token = \Bitrix\Main\Config\Option::get(static::$moduleId, 'YANDEX_APIKEY', '');
		if(!$token) return false;
		
		$arUrl = parse_url($path);
		$fragment = $arUrl['fragment'];
		
		$path = trim(preg_replace('/[#|\?].*$/', '', $path), '/');
		$pathOrig = rtrim($path, '/');
		$arUrl = parse_url($path);
		$subPath = '';
		if(strpos($arUrl['path'], '/d/')===0 && preg_match('/^\/d\/[^\/]*\/./', $arUrl['path']))
		{
			$subPath = preg_replace('/^\/d\/[^\/]*\//', '/', $arUrl['path']);
			if($subPath && strlen($subPath) < strlen($arUrl['path']))
			{
				$path = substr($path, 0, -strlen($subPath));
			}
		}
		
		$arFiles = array();
		if(strlen($fragment) > 0)
		{
			$pattern = preg_replace('/^\s*(\/\*)*\//', '', $fragment);
			$client = new \Bitrix\Main\Web\HttpClient(array('socketTimeout'=>15, 'disableSslVerification'=>true));
			$client->setHeader('Authorization', "OAuth ".$token);
			$res = $client->get('https://cloud-api.yandex.net/v1/disk/public/resources?public_key='.urlencode($path).(strlen($subPath) > 0 ? '&path='.urlencode($subPath) : '').'&limit=99999');
			$arRes = \CUtil::JsObjectToPhp($res);
			$arItems = $arRes['_embedded']['items'];
			if(is_array($arItems))
			{
				foreach($arItems as $arItem)
				{
					if($arItem['type']=='file' && fnmatch($pattern, $arItem['name'], GLOB_BRACE))
					{
						$arFiles[] = $pathOrig.$arItem['name'];
					}
				}
			}
		}
		return $arFiles;
	}
	
	public function YadiskGetFile(&$tmpPath, $path)
	{
		$token = \Bitrix\Main\Config\Option::get(static::$moduleId, 'YANDEX_APIKEY', '');
		if(!$token) return false;
		
		$arUrl = parse_url($path);
		$fragment = $arUrl['fragment'];
		$allowDirectLink = true;
		if(strpos($fragment, '#')===0)
		{
			$allowDirectLink = false;
			$fragment = ltrim($fragment, '#');
		}
		
		$path = trim(preg_replace('/[#|\?].*$/', '', $path), '/');
		$arUrl = parse_url($path);
		$subPath = '';
		if(strpos($arUrl['path'], '/d/')===0 && preg_match('/^\/d\/[^\/]*\/./', $arUrl['path']))
		{
			$subPath = preg_replace('/^\/d\/[^\/]*\//', '/', $arUrl['path']);
			if($subPath && strlen($subPath) < strlen($arUrl['path']))
			{
				$path = substr($path, 0, -strlen($subPath));
			}
		}
		
		$fileLink = '';
		if(strlen($fragment) > 0 && ((strpos($fragment, '*')!==false || (strpos($fragment, '{')!==false && strpos($fragment, '}')!==false))))
		{
			$pattern = preg_replace('/^\s*(\/\*)*\//', '', $fragment);
			$client = new \Bitrix\Main\Web\HttpClient(array('socketTimeout'=>10, 'disableSslVerification'=>true));
			$client->setHeader('Authorization', "OAuth ".$token);
			$res = $client->get('https://cloud-api.yandex.net/v1/disk/public/resources?public_key='.urlencode($path).(strlen($subPath) > 0 ? '&path='.urlencode($subPath) : '').'&limit=9999');
			$arRes = \CUtil::JsObjectToPhp($res);
			$arItems = $arRes['_embedded']['items'];
			if(is_array($arItems))
			{
				foreach($arItems as $arItem)
				{
					if($arItem['type']=='file' && fnmatch($pattern, $arItem['name'], GLOB_BRACE))
					{
						$fileLink = $arItem['file'];
						break;
					}
				}
			}
		}
		
		if(strlen($fileLink)==0 && $allowDirectLink)
		{
			$client = new \Bitrix\Main\Web\HttpClient(array('socketTimeout'=>10, 'disableSslVerification'=>true));
			$client->setHeader('Authorization', "OAuth ".$token);
			$res = $client->get('https://cloud-api.yandex.net/v1/disk/public/resources/download?public_key='.urlencode($path).(strlen($subPath) > 0 ? '&path='.urlencode($subPath) : ''));
			$arRes = \CUtil::JsObjectToPhp($res);
			if(is_array($arRes) && $arRes['href'])
			{
				$fileLink = $arRes['href'];
			}
		}
		
		if(strlen($fileLink) > 0)
		{
			$arUrl = parse_url($fileLink);
			$filename = preg_grep('/^filename=/', explode('&', $arUrl['query']));
			if(count($filename)==1)
			{
				$filename = urldecode(substr(current($filename), 9));
				if((!defined('BX_UTF') || !BX_UTF)) $filename = $GLOBALS['APPLICATION']->ConvertCharset($filename, 'UTF-8', 'CP1251');
				$tmpPath = static::GetTmpFilePath($filename);
			}
			if($client->download($fileLink, $tmpPath))
			{
				$tmpPath = \Bitrix\Main\IO\Path::convertLogicalToPhysical($tmpPath);
				return true;
			}
		}
		return false;
	}
	
	public function MailruGetFile(&$tmpPath, $path)
	{
		$arUrl = parse_url($path);
		if(isset($arUrl['fragment']) && strlen($arUrl['fragment']) > 0)
		{
			$path = substr($path, 0, -strlen($arUrl['fragment']) - 1);
		}
		$mr = \Bitrix\EsolImportxml\Cloud\MailRu::GetInstance();
		return $mr->download($tmpPath, $path, (isset($arUrl['fragment']) ? $arUrl['fragment'] : ''));
	}
	
	public function GdriveGetFile(&$tmpPath, $path)
	{
		$path2 = '';
		if(preg_match('/^https?:\/\/docs\.google\.com\/spreadsheets.*\/d\/([^\/]+)(\/|$)/i', $path, $m)
			|| preg_match('/^https?:\/\/www\.google\.com\/.*https?:\/\/docs\.google\.com\/spreadsheets.*\/d\/([^\/]+)(\/|$)/i', $path, $m))
		{
			$tmpPath = static::GetTmpFilePath($m[1].'.xlsx');
			$path = 'https://docs.google.com/spreadsheets/d/'.$m[1].'/export?format=xlsx';
			$path2 = 'https://drive.google.com/uc?id='.$m[1].'&export=download';
		}
		elseif(preg_match('/^https?:\/\/drive\.google\.com\/file.*\/d\/([^\/]+)(\/|$)/i', $path, $m))
		{
			$path = 'https://drive.google.com/uc?id='.$m[1].'&export=download';
		}
		elseif(preg_match('/id=([^&]+)/i', $path, $m))
		{
			$path = 'https://drive.google.com/uc?id='.$m[1].'&export=download';
		}
		$client = new \Bitrix\Main\Web\HttpClient(array('socketTimeout'=>30, 'disableSslVerification'=>true));
		$res = $client->download($path, $tmpPath);
		if((!$res || $client->getStatus()==404) && $path2)
		{
			$client = new \Bitrix\Main\Web\HttpClient(array('socketTimeout'=>30, 'disableSslVerification'=>true));
			$res = $client->download($path2, $tmpPath);
		}
		if($res && $client->getStatus()!=404)
		{
			$hcd = $client->getHeaders()->get('content-disposition');
			if($hcd && stripos($hcd, 'filename='))
			{
				$hcdParts = array_map('trim', explode(';', $hcd));
				$hcdParts1 = preg_grep('/filename\*=UTF\-8\'\'/i', $hcdParts);
				$hcdParts2 = preg_grep('/filename=/i', $hcdParts);
				if(count($hcdParts1) > 0)
				{
					$hcdParts1 = explode("''", current($hcdParts1));
					$fn = urldecode(trim(end($hcdParts1), '"\' '));
					if((!defined('BX_UTF') || !BX_UTF)) $fn = $GLOBALS['APPLICATION']->ConvertCharset($fn, 'UTF-8', 'CP1251');
					$fn = \Bitrix\Main\IO\Path::convertLogicalToPhysical($fn);
					if(strpos($tmpPath, $fn)===false)
					{
						$tmpPath = \Bitrix\EsolImportxml\Utils::ReplaceFile($tmpPath, preg_replace('/\/[^\/]+$/', '/'.$fn, $tmpPath));
					}
				}
				elseif(count($hcdParts2) > 0)
				{
					$hcdParts2 = explode('=', current($hcdParts2));
					$fn = trim(end($hcdParts2), '"\' ');
					if(strpos($tmpPath, $fn)===false)
					{
						$tmpPath = \Bitrix\EsolImportxml\Utils::ReplaceFile($tmpPath, preg_replace('/\/[^\/]+$/', '/'.$fn, $tmpPath));
					}
				}
			}
			return true;
		}
		return false;
	}
	
	public function DropboxGetFile(&$tmpPath, $path)
	{
		$path = preg_replace('/(\?dl=[01])(\D|$)/i', '?dl=1$2', $path);
		$client = new \Bitrix\Main\Web\HttpClient(array('socketTimeout'=>30, 'disableSslVerification'=>true, 'redirect'=>false));
		$client->setHeader('User-Agent', 'BitrixSM HttpClient class');
		$client->get($path);
		$arCookies = $client->getCookies()->toArray();
		if($client->getHeaders()->get('location'))
		{
			$path = preg_replace('/^([^\/]*\/\/[^\/]+\/).*$/', '$1', $path).trim($client->getHeaders()->get('location'), '/');
		}
		$client = new \Bitrix\Main\Web\HttpClient(array('socketTimeout'=>30, 'disableSslVerification'=>true));
		$client->setHeader('User-Agent', 'BitrixSM HttpClient class');
		$client->setCookies($arCookies);		
		if($client->download($path, $tmpPath))
		{
			$hcd = $client->getHeaders()->get('content-disposition');
			if($hcd && stripos($hcd, 'filename='))
			{
				$hcdParts = array_map('trim', explode(';', $hcd));
				$hcdParts1 = preg_grep('/filename\*=UTF\-8\'\'/i', $hcdParts);
				$hcdParts2 = preg_grep('/filename=/i', $hcdParts);
				if(count($hcdParts1) > 0)
				{
					$hcdParts1 = explode("''", current($hcdParts1));
					$fn = urldecode(trim(end($hcdParts1), '"\' '));
					if((!defined('BX_UTF') || !BX_UTF)) $fn = $GLOBALS['APPLICATION']->ConvertCharset($fn, 'UTF-8', 'CP1251');
					$fn = \Bitrix\Main\IO\Path::convertLogicalToPhysical($fn);
					if(strpos($tmpPath, $fn)===false)
					{
						$tmpPath = \Bitrix\EsolImportxml\Utils::ReplaceFile($tmpPath, preg_replace('/\/[^\/]+$/', '/'.$fn, $tmpPath));
					}
				}
				elseif(count($hcdParts2) > 0)
				{
					$hcdParts2 = explode('=', current($hcdParts2));
					$fn = trim(end($hcdParts2), '"\' ');
					if(strpos($tmpPath, $fn)===false)
					{
						$tmpPath = \Bitrix\EsolImportxml\Utils::ReplaceFile($tmpPath, preg_replace('/\/[^\/]+$/', '/'.$fn, $tmpPath));
					}
				}
			}
			return true;
		}
		return false;
	}
}
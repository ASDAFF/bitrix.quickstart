<?php
namespace Bitrix\EsolImportxml;

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class Sftp
{
	protected $connects = array();
	protected $curConnect = array();
	
	public function GetConnect($path, $ftptimeout = 15)
	{
		$ssl = preg_match("#^(ftps)://#", $path);
		$urlComponents = $this->ParseUrl($path);
		$filepath = $urlComponents['path'];
		$ftphost = $urlComponents['host']; 
		$ftpport = (isset($urlComponents['port']) ? $urlComponents['port'] : ($ssl ? 990 : 21));
		$ftpuser = (isset($urlComponents['user']) ? $urlComponents['user'] : 'anonymous');
		$ftppassword = (isset($urlComponents['pass']) ? $urlComponents['pass'] : '');
		
		$streamHash = md5($ftphost.'/'.$ftpport.'/'.((string)$ssl).'/'.$ftpuser.'/'.$ftppassword);
		if(!isset($this->connects[$streamHash]))
		{
			if($ssl) $stream = ftp_ssl_connect($ftphost, $ftpport, $ftptimeout);
			else $stream = ftp_connect($ftphost, $ftpport, $ftptimeout);
			if($stream)
			{
				if(ftp_login($stream, $ftpuser, $ftppassword))
				{
					ftp_pasv($stream, true);
					if($this->IsEmptyResult($stream) && ftp_pasv($stream, false) && $this->IsEmptyResult($stream))
					{
						ftp_pasv($stream, true);
					}
					$this->connects[$streamHash] = $stream;
				}
				else
				{
					$this->connects[$streamHash] = false;
					ftp_close($stream);
				}
			}
		}
		$this->curConnect = $this->connects[$streamHash];
		return $this->curConnect;
	}
	
	public function __destruct()
	{
		foreach($this->connects as $hash=>$stream)
		{
			if($stream!==false)
			{
				ftp_close($stream);
			}
		}
	}
	
	public function IsEmptyResult($stream)
	{
		$list = ftp_nlist($stream, '/');
		if(empty($list)) return true;
		else return false;
	}
	
	public function ParseUrl($path)
	{
		$urlComponents = parse_url($path);
		if(!$urlComponents && strpos($path, '#')!==false)
		{
			$path = str_replace('#', urlencode('#'), $path);
			$urlComponents = parse_url($path);
			if(isset($urlComponents['user'])) $urlComponents['user'] = urldecode($urlComponents['user']);
			if(isset($urlComponents['pass'])) $urlComponents['pass'] = urldecode($urlComponents['pass']);
		}
		if(isset($urlComponents["path"])) $urlComponents["path"] = urldecode($urlComponents['path']);
		return $urlComponents;
	}
	
	public function SaveFile($temp_path, $filepath)
	{
		if(!$this->curConnect) return false;
		$dir = \Bitrix\Main\IO\Path::getDirectory($temp_path);
		\Bitrix\Main\IO\Directory::createDirectory($dir);
		ftp_get($this->curConnect, $temp_path, $filepath, FTP_BINARY);
	}
	
	public function GetListFiles($path)
	{
		$arFiles = array();
		if(isset($this->currentDirPath) && $this->currentDirPath==$path)
		{
			$arFiles = $this->currentDirFiles;
		}
		else
		{
			if((preg_match("#^(ftp)://#", $path) && function_exists('ftp_connect')
				|| preg_match("#^(ftps)://#", $path) && function_exists('ftp_ssl_connect')))
			{
				if($this->GetConnect($path))
				{
					$urlComponents = $this->ParseUrl($path);				
					$dirpath = $urlComponents["path"];
					$arFiles = ftp_nlist($this->curConnect, $dirpath);
				}
			}
			$this->currentDirPath = $path;
			$this->currentDirFiles = $arFiles;
		}
		return $arFiles;
	}
	
	public function MakeFileArray($path, $arParams=array(), $bMultiple = false)
	{
		if((preg_match("#^(ftp)://#", $path) && function_exists('ftp_connect')
			|| preg_match("#^(ftps)://#", $path) && function_exists('ftp_ssl_connect')))
		{
			$bMultiple = (bool)($arParams['MULTIPLE']=='Y');
			$temp_path = '';
			$bExternalStorage = false;
			foreach(GetModuleEvents("main", "OnMakeFileArray", true) as $arEvent)
			{
				if(ExecuteModuleEventEx($arEvent, array($path, &$temp_path)))
				{
					$bExternalStorage = true;
					break;
				}
			}
			
			if(!$bExternalStorage)
			{				
				if($this->GetConnect($path, ($arParams['TIMEOUT'] ? $arParams['TIMEOUT'] : 15)))
				{
					$path = trim($path);
					if(substr($path, -1)=='/')
					{
						$fileTypes = array();
						$bNeedImage = (bool)($arParams['FILETYPE']=='IMAGE');
						if($bNeedImage) $fileTypes = array('jpg', 'jpeg', 'png', 'gif', 'bmp');
						elseif($arParams['FILE_TYPE']) $fileTypes = array_diff(array_map('trim', explode(',', ToLower($arParams['FILE_TYPE']))), array(''));
						$arDirFiles = array_values(array_map(create_function('$n', 'return end(explode("/", $n));'), $this->GetListFiles($path)));
						if($bMultiple)
						{
							$arFiles = array();
							foreach($arDirFiles as $file)
							{
								if(in_array(ToLower(end(explode('.', $file))), $fileTypes) || empty($fileTypes))
								{
									$arFiles[] = $this->MakeFileArray($path.$file, $arParams);
								}
							}
							return $arFiles;
						}
						$findFile = false;
						$i = 0;
						while(!$findFile && isset($arDirFiles[$i]))
						{
							if(in_array(ToLower(end(explode('.', $arDirFiles[$i]))), $fileTypes) || empty($fileTypes))
							{
								$findFile = true;
								$path .= $arDirFiles[$i];
							}
							$i++;
						}
					}
					$urlComponents = $this->ParseUrl($path);
					if ($urlComponents && strlen($urlComponents["path"]) > 0)
					{
						$temp_path = \CFile::GetTempName('', bx_basename($urlComponents["path"]));
					}
					else
						$temp_path = \CFile::GetTempName('', bx_basename($path));
					
					$filepath = $urlComponents["path"];
					$this->SaveFile($temp_path, $filepath);
				}
				$arFile = \CFile::MakeFileArray($temp_path);
			}
			elseif($temp_path)
			{
				$arFile = \CFile::MakeFileArray($temp_path);
			}
			
			if(strlen($arFile["type"])<=0)
				$arFile["type"] = "unknown";
		}
		else
		{
			$arFile = \CFile::MakeFileArray($path);
		}
		return $arFile;
	}
}
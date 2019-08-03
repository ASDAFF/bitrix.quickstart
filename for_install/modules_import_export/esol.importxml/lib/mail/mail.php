<?php
namespace Bitrix\EsolImportxml;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class SMail
{
	protected static $moduleId = 'esol.importxml';
	protected $paramsChecked = false;
	protected $paramsCheckRes = false;

	public function __construct($params=array())
	{
		$this->params = $params;
	}
	
	public function CheckParams()
	{
		if(!$this->paramsChecked)
		{
			$tls = false;
			$port = 143;
			if($this->params['SECURITY']=='ssl')
			{
				$port = 993;
				$tls = true;
			}
			elseif($this->params['SECURITY']=='tls')
			{
				$port = 143;
				$tls = true;
			}
			$charset = (defined('BX_UTF') && BX_UTF ? 'UTF-8' : 'CP1251');
			
			$this->imap = new \Bitrix\EsolImportxml\Imap($this->params['SERVER'], $port, $tls, false, $this->params['EMAIL'], $this->params['PASSWORD'], $charset);
			$this->paramsCheckRes = $this->imap->singin(($error = ''));
			$this->paramsChecked = true;
		}
		return $this->paramsCheckRes;
	}
	
	public function GetListingFolders()
	{
		$arFolders = array();
		if($this->CheckParams())
		{
			if($mailboxes = $this->imap->listMailboxes('*', ($error='')))
			{
				foreach($mailboxes as $mailbox)
				{
					if(strpos($mailbox['key'], 'INBOX')!==false)
					{
						$arFolders[$mailbox['key']] = $mailbox['name'];
					}
				}
				foreach($mailboxes as $mailbox)
				{
					if(strpos($mailbox['key'], 'INBOX')===false)
					{
						$arFolders[$mailbox['key']] = $mailbox['name'];
					}
				}
			}
		}
		foreach($arFolders as $k=>$v)
		{
			$arFolders[$k] = str_replace('INBOX', Loc::getMessage('ESOL_IX_INBOX_FOLDER'), $v);
		}
		if(!isset($arFolders['INBOX']))
		{
			$arFolders['INBOX'] = Loc::getMessage('ESOL_IX_INBOX_FOLDER');
		}
		return $arFolders;
	}
	
	public function GetFileId(&$arParams)
	{
		if($this->CheckParams($tmpdir))
		{
			$mailbox = $this->mailbox;
			
			//$arFolders = $mailbox->getListingFolders();
			if($this->params['FOLDER'])
			{
				$arFolders = array($this->params['FOLDER']);
			}
			else
			{
				$arFolders = array('INBOX');
			}
			
			$time = time() - 30*24*60*60;
			if($this->params['LAST_DATE'])
			{
				$time1 = strtotime($this->params['LAST_DATE']);
				if($time1 > $time) $time = $time1;
			}
			$time = mktime(0, 0, 0, date('n', $time), date('j', $time), date('Y', $time));
			//$arCriterias = array('SINCE' => date('r', $time));
			$arCriterias = array('SINCE' => date('j-M-Y', $time));
			if($this->params['UNSEEN_ONLY']!='N') $arCriterias['UNSEEN'] = 'Y';
			if($this->params['FROM']) $arCriterias['FROM'] = $this->params['FROM'];
			if($this->params['SUBJECT']) $arCriterias['SUBJECT'] = $this->params['SUBJECT'];
			if($this->params['FILENAME']) $arCriterias['FILENAME'] = $this->params['FILENAME'];
			
			$fid = 0;
			while(!empty($arFolders) && !$fid)
			{
				$folder = array_shift($arFolders);
				$mailsIds = $this->imap->getSearch($folder, $arCriterias, ($error=''));
				
				if(!empty($mailsIds))
				{
					$break = false;
					$i = count($mailsIds) - 1;
					while($i>=0 && !$break)
					{
						$mailId = $mailsIds[$i];
						if(($arMailFile = $this->imap->getMessageFile($folder, $mailId, $this->params['FILENAME'], array('xml', 'yml', 'zip'), ($error='')))!==false)
						{
							if(!$this->params['LAST_DATE'] || $arMailFile['DATE']!=$this->params['LAST_DATE'])
							{
								$fn = \Bitrix\Main\IO\Path::convertLogicalToPhysical($arMailFile['FILENAME']);
								if(strpos($fn, '.')===false) $fn .= '.csv';
								
								$dir = $_SERVER["DOCUMENT_ROOT"].'/upload/tmp/'.static::$moduleId.'/';
								CheckDirPath($dir);
								$i = 0;
								while(($tmpdir = $dir.'attachments_'.$i.'/') && file_exists($tmpdir)){$i++;}
								CheckDirPath($tmpdir);
								
								file_put_contents($tmpdir.$fn, $arMailFile['BODY']);
								$arFile = \Bitrix\EsolImportxml\Utils::MakeFileArray($tmpdir.$fn);
								$fid = \Bitrix\EsolImportxml\Utils::SaveFile($arFile, static::$moduleId);
								DeleteDirFilesEx(substr($tmpdir, strlen($_SERVER["DOCUMENT_ROOT"])));
								$arParams['LAST_DATE'] = $arMailFile['DATE'];
							}
							
							$break = true;
						}
						$i--;
					}
				}
			}
		}
		
		if($fid > 0) return $fid;
		else return false;
	}
	
	public static function GetNewFile(&$json)
	{
		$arParams = \CUtil::JsObjectToPhp($json);
		if(!is_array($arParams)) $arParams = array();
		$mail = new \Bitrix\EsolImportxml\SMail($arParams);
		$fileId = $mail->GetFileId($arParams);
		$json = \CUtil::PhpToJSObject($arParams);
		return $fileId;
	}
}
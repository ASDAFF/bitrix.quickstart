<?php
namespace Bitrix\EsolImportxml;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class Utils {
	protected static $moduleId = 'esol.importxml';
	protected static $fileSystemEncoding = null;
	protected static $siteEncoding = null;
	protected static $cpSpecCharLetters = null;
	protected static $arAgents = array();
	protected static $countAgents = 0;
	
	public static function GetOfferIblock($IBLOCK_ID, $retarray=false)
	{
		if(!$IBLOCK_ID || !Loader::includeModule('catalog')) return false;
		$dbRes = \CCatalog::GetList(array(), array('IBLOCK_ID'=>$IBLOCK_ID));
		$arFields = $dbRes->Fetch();
		if(!$arFields['OFFERS_IBLOCK_ID'])
		{
			$dbRes = \CCatalog::GetList(array(), array('PRODUCT_IBLOCK_ID'=>$IBLOCK_ID));
			if($arFields2 = $dbRes->Fetch())
			{
				$arFields = Array(
					'IBLOCK_ID' => $arFields2['PRODUCT_IBLOCK_ID'],
					'YANDEX_EXPORT' => $arFields2['YANDEX_EXPORT'],
					'SUBSCRIPTION' => $arFields2['SUBSCRIPTION'],
					'VAT_ID' => $arFields2['VAT_ID'],
					'PRODUCT_IBLOCK_ID' => 0,
					'SKU_PROPERTY_ID' => 0,
					'OFFERS_PROPERTY_ID' => $arFields2['SKU_PROPERTY_ID'],
					'OFFERS_IBLOCK_ID' => $arFields2['IBLOCK_ID'],
					'ID' => $arFields2['IBLOCK_ID'],
					'IBLOCK_TYPE_ID' => $arFields2['IBLOCK_TYPE_ID'],
					'IBLOCK_ACTIVE' => $arFields2['IBLOCK_ACTIVE'],
					'LID' => $arFields2['LID'],
					'NAME' => $arFields2['NAME']
				);
			}
		}
		if($arFields['OFFERS_IBLOCK_ID'])
		{
			if($retarray) return $arFields;
			else return $arFields['OFFERS_IBLOCK_ID'];
		}
		return false;
	}
	
	public static function GetFileName($fn)
	{
		global $APPLICATION;
		if(file_exists($_SERVER['DOCUMENT_ROOT'].$fn)) return $fn;
		
		if(defined("BX_UTF")) $tmpfile = $APPLICATION->ConvertCharsetArray($fn, LANG_CHARSET, 'CP1251');
		else $tmpfile = $APPLICATION->ConvertCharsetArray($fn, LANG_CHARSET, 'UTF-8');
		
		if(file_exists($_SERVER['DOCUMENT_ROOT'].$tmpfile)) return $tmpfile;
		
		return false;
	}
	
	public static function Win1251Utf8($str)
	{
		global $APPLICATION;
		return $APPLICATION->ConvertCharset($str, "Windows-1251", "UTF-8");
	}
	
	public static function GetFileLinesCount($fn)
	{
		if(!file_exists($fn)) return 0;
		
		$cnt = 0;
		$handle = fopen($fn, 'r');
		while (!feof($handle)) {
			$buffer = trim(fgets($handle));
			if($buffer) $cnt++;
		}
		fclose($handle);
		return $cnt;
	}
	
	public static function SortFileIds($fn)
	{
		if(!file_exists($fn)) return 0;

		$arIds = array();
		$handle = fopen($fn, 'r');
		while (!feof($handle)) {
			$buffer = trim(fgets($handle, 128));
			if($buffer) $arIds[] = (int)$buffer;
		}
		fclose($handle);
		sort($arIds, SORT_NUMERIC);

		unlink($fn);

		$handle = fopen($fn, 'a');
		$cnt = count($arIds);
		$step = 10000;
		for($i=0; $i<$cnt; $i+=$step)
		{
			fwrite($handle, implode("\r\n", array_slice($arIds, $i, $step))."\r\n");
		}
		fclose($handle);
		
		if($cnt > 0) return end($arIds);
		else return 0;
	}
	
	public static function GetPartIdsFromFile($fn, $min)
	{
		if(!file_exists($fn)) return array();

		$cnt = 0;
		$maxCnt = 5000;
		$arIds = array();
		$handle = fopen($fn, 'r');
		while (!feof($handle) && $maxCnt>$cnt) {
			$buffer = (int)trim(fgets($handle, 128));
			if($buffer > $min)
			{
				$arIds[] = (int)$buffer;
				$cnt++;
			}
		}
		fclose($handle);
		return $arIds;
	}
	
	public static function GetFileArray($id)
	{
		if(class_exists('\Bitrix\Main\FileTable'))
		{
			$arFile = \Bitrix\Main\FileTable::getList(array('filter'=>array('ID'=>$id)))->fetch();
			if(is_callable(array($arFile['TIMESTAMP_X'], 'toString'))) $arFile['TIMESTAMP_X'] = $arFile['TIMESTAMP_X']->toString();
			$arFile['SRC'] = \CFile::GetFileSRC($arFile, false, false);
		}
		else
		{
			$arFile = \CFile::GetFileArray($id);
		}
		return $arFile;
	}
	
	public static function SaveFile($arFile, $strSavePath, $bForceMD5=false, $bSkipExt=false)
	{
		$oProfile = \Bitrix\EsolImportxml\Profile::getInstance();
		$isUtf = (bool)(defined("BX_UTF") && BX_UTF);
		if(\CUtil::DetectUTF8($arFile["name"]))
		{
			if(!$isUtf) $arFile["name"] = \Bitrix\Main\Text\Encoding::convertEncoding($arFile["name"], 'utf-8', LANG_CHARSET);
		}
		else
		{
			if($isUtf) $arFile["name"] = \Bitrix\Main\Text\Encoding::convertEncoding($arFile["name"], 'windows-1251', LANG_CHARSET);
		}
		$strFileName = GetFileName($arFile["name"]);	/* filename.gif */
		if(strpos($strFileName, '.')===0) $strFileName = '_'.$strFileName;

		if(isset($arFile["del"]) && $arFile["del"] <> '')
		{
			\CFile::DoDelete($arFile["old_file"]);
			if($strFileName == '')
				return "NULL";
		}

		if($arFile["name"] == '')
		{
			if(isset($arFile["description"]) && intval($arFile["old_file"])>0)
			{
				\CFile::UpdateDesc($arFile["old_file"], $arFile["description"]);
			}
			return false;
		}

		if (isset($arFile["content"]))
		{
			if (!isset($arFile["size"]))
			{
				$arFile["size"] = \CUtil::BinStrlen($arFile["content"]);
			}
		}
		else
		{
			try
			{
				$file = new \Bitrix\Main\IO\File(\Bitrix\Main\IO\Path::convertPhysicalToLogical($arFile["tmp_name"]));
				$arFile["size"] = $file->getSize();
			}
			catch(IO\IoException $e)
			{
				$arFile["size"] = 0;
			}
		}

		$arFile["ORIGINAL_NAME"] = $strFileName;

		//translit, replace unsafe chars, etc.
		$strFileName = self::transformName($strFileName, $bForceMD5, $bSkipExt);

		//transformed name must be valid, check disk quota, etc.
		if (self::validateFile($strFileName, $arFile) !== "")
		{
			return false;
		}

		if($arFile["type"] == "image/pjpeg" || $arFile["type"] == "image/jpg")
		{
			$arFile["type"] = "image/jpeg";
		}

		$bExternalStorage = false;
		/*foreach(GetModuleEvents("main", "OnFileSave", true) as $arEvent)
		{
			if(ExecuteModuleEventEx($arEvent, array(&$arFile, $strFileName, $strSavePath, $bForceMD5, $bSkipExt)))
			{
				$bExternalStorage = true;
				break;
			}
		}*/

		if(!$bExternalStorage)
		{
			$upload_dir = \COption::GetOptionString("main", "upload_dir", "upload");
			$io = \CBXVirtualIo::GetInstance();
			if($bForceMD5 != true)
			{
				$dir_add = '';
				$i=0;
				while(true)
				{
					$dir_add = substr(md5(uniqid("", true)), 0, 3);
					if(!$io->FileExists($_SERVER["DOCUMENT_ROOT"]."/".$upload_dir."/".$strSavePath."/".$dir_add."/".$strFileName))
					{
						break;
					}
					if($i >= 25)
					{
						$j=0;
						while(true)
						{
							$dir_add = substr(md5(mt_rand()), 0, 3)."/".substr(md5(mt_rand()), 0, 3);
							if(!$io->FileExists($_SERVER["DOCUMENT_ROOT"]."/".$upload_dir."/".$strSavePath."/".$dir_add."/".$strFileName))
							{
								break;
							}
							if($j >= 25)
							{
								$dir_add = substr(md5(mt_rand()), 0, 3)."/".md5(mt_rand());
								break;
							}
							$j++;
						}
						break;
					}
					$i++;
				}
				if(substr($strSavePath, -1, 1) <> "/")
					$strSavePath .= "/".$dir_add;
				else
					$strSavePath .= $dir_add."/";
			}
			else
			{
				$strFileExt = ($bSkipExt == true || ($ext = GetFileExtension($strFileName)) == ''? '' : ".".$ext);
				while(true)
				{
					if(substr($strSavePath, -1, 1) <> "/")
						$strSavePath .= "/".substr($strFileName, 0, 3);
					else
						$strSavePath .= substr($strFileName, 0, 3)."/";

					if(!$io->FileExists($_SERVER["DOCUMENT_ROOT"]."/".$upload_dir."/".$strSavePath."/".$strFileName))
						break;

					//try the new name
					$strFileName = md5(uniqid("", true)).$strFileExt;
				}
			}

			$arFile["SUBDIR"] = $strSavePath;
			$arFile["FILE_NAME"] = $strFileName;
			$strDirName = $_SERVER["DOCUMENT_ROOT"]."/".$upload_dir."/".$strSavePath."/";
			$strDbFileNameX = $strDirName.$strFileName;
			$strPhysicalFileNameX = $io->GetPhysicalName($strDbFileNameX);

			CheckDirPath($strDirName);

			if(is_set($arFile, "content"))
			{
				$f = fopen($strPhysicalFileNameX, "ab");
				if(!$f)
					return false;
				if(fwrite($f, $arFile["content"]) === false)
					return false;
				fclose($f);
			}
			elseif(
				!copy($arFile["tmp_name"], $strPhysicalFileNameX)
				&& !move_uploaded_file($arFile["tmp_name"], $strPhysicalFileNameX)
			)
			{
				\CFile::DoDelete($arFile["old_file"]);
				return false;
			}

			if(isset($arFile["old_file"]))
				\CFile::DoDelete($arFile["old_file"]);

			@chmod($strPhysicalFileNameX, BX_FILE_PERMISSIONS);

			//flash is not an image
			$flashEnabled = !\CFile::IsImage($arFile["ORIGINAL_NAME"], $arFile["type"]);

			$imgArray = \CFile::GetImageSize($strDbFileNameX, false, $flashEnabled);

			if(is_array($imgArray))
			{
				$arFile["WIDTH"] = $imgArray[0];
				$arFile["HEIGHT"] = $imgArray[1];

				if($imgArray[2] == IMAGETYPE_JPEG)
				{
					$exifData = \CFile::ExtractImageExif($io->GetPhysicalName($strDbFileNameX));
					if ($exifData  && isset($exifData['Orientation']))
					{
						//swap width and height
						if ($exifData['Orientation'] >= 5 && $exifData['Orientation'] <= 8)
						{
							$arFile["WIDTH"] = $imgArray[1];
							$arFile["HEIGHT"] = $imgArray[0];
						}

						$properlyOriented = \CFile::ImageHandleOrientation($exifData['Orientation'], $io->GetPhysicalName($strDbFileNameX));
						if ($properlyOriented)
						{
							$jpgQuality = intval(\COption::GetOptionString('main', 'image_resize_quality', '95'));
							if($jpgQuality <= 0 || $jpgQuality > 100)
								$jpgQuality = 95;
							imagejpeg($properlyOriented, $io->GetPhysicalName($strDbFileNameX), $jpgQuality);
						}
					}
				}
			}
			else
			{
				$arFile["WIDTH"] = 0;
				$arFile["HEIGHT"] = 0;
			}
			
			/*Remove bad string*/
			$ext = GetFileExtension($strFileName);
			if(in_array(Tolower($ext), array('xml', 'yml')) && $strPhysicalFileNameX)
			{
				$break = false;
				$filesize = filesize($strPhysicalFileNameX);
				$handle = fopen($strPhysicalFileNameX, 'r');
				$buffer = '';
				while(!$break && !feof($handle)) 
				{
					$str = fgets($handle, 65536);
					if(trim($str) && strpos($str, '>')!==false && stripos($str, '<?xml')===false && stripos($str, '<!DOCTYPE')===false)
					{
						$break = true;
					}
					$buffer .= $str;
				}
				$pos1 = $pos2 = $pos3 = 0;
				if(preg_match('/<\?xml[^>]*>/Uis', $buffer, $m)){$pos1 = strpos($buffer, $m[0])+strlen($m[0]);}
				if(preg_match('/<!DOCTYPE[^>]*>/Uis', $buffer, $m)){$pos2 = strpos($buffer, $m[0])+strlen($m[0]);}
				if(preg_match('/<[^\?!][^>]*>/Uis', $buffer, $m)){$pos3 = strpos($buffer, $m[0])+strlen($m[0]);}
				$maxPos = max($pos1, $pos2, $pos3);
				$buffer = substr($buffer, 0, $maxPos);
				if(function_exists('mb_strlen')) $maxPos = mb_strlen($buffer, 'CP1251');
				fseek($handle, $maxPos);
				
				$updateFile = false;
				if(\COption::GetOptionString(static::$moduleId, 'AUTO_CORRECT_ENCODING', 'N')=='Y' && preg_match('/<\?xml[^>]*encoding=[\'"]([^\'"]*)[\'"][^>]*\?>/is', $buffer, $m))
				{
					$encoding = ToLower($m[1]);
					if($encoding=='cp1251') $encoding = 'windows-1251';
					if($encoding=='utf8') $encoding = 'utf-8';
					$curPos = ftell($handle);
					$partSize = 262144;
					fseek($handle, 0);
					$contents = fread($handle, $partSize);
					if($filesize > $partSize*2)
					{
						fseek($handle, max(($filesize - $partSize)/2, $partSize));
						$contents .= fread($handle, $partSize);
					}
					if($filesize > $partSize)
					{
						fseek($handle, max($filesize - $partSize, $partSize));
						$contents .= fread($handle, $partSize);
					}
					fseek($handle, $curPos);
					
					try{				
						$contents = preg_replace('/%[A-F0-9]{2}/', '', $contents);
						$fileEncoding = 'utf-8';
						if(!\CUtil::DetectUTF8($contents) && (!function_exists('iconv') || iconv('CP1251', 'CP1251', $contents)==$contents))
						{
							$fileEncoding = 'windows-1251';
						}
						if(in_array($encoding, array('windows-1251', 'utf-8')) && $encoding!=$fileEncoding)
						{
							$buffer = preg_replace('/(<\?xml[^>]*encoding=[\'"])([^\'"]*)([\'"][^>]*\?>)/is', '$1'.$fileEncoding.'$3', $buffer);
							$updateFile = true;
						}
					}catch(Exception $ex){}
				}
				
				if(preg_match('/<\?xml[^>]*version=[\'"]([^\'"]*)[\'"][^>]*\?>/is', $buffer, $m))
				{
					$version = ToLower($m[1]);
					if($version!='1.0')
					{
						$buffer = preg_replace('/(<\?xml[^>]*version=)([\'"][^\'"]*[\'"])([^>]*\?>)/is', '$1"1.0"$3', $buffer);
						$updateFile = true;
					}
				}
				
				if(preg_match('/\s+xmlns\s*=\s*"[^"]*"\s*/is', $buffer, $m))
				{
					$buffer = str_replace($m[0], ' ', $buffer);
					$updateFile = true;
				}
				
				if(preg_match('/^\s+/s', $buffer, $m))
				{
					$buffer = ltrim($buffer);
					$updateFile = true;
				}
				
				if($oProfile->GetParam('AUTO_FIX_XML_ERRORS')=='Y')
				{
					$updateFile = true;
				}
				
				if($updateFile)
				{
					$tmpFile = $strPhysicalFileNameX.'.tmp';
					$handle2 = fopen($tmpFile, 'a');
					fwrite($handle2, $buffer);
					if($oProfile->GetParam('AUTO_FIX_XML_ERRORS')=='Y')
					{
						$bNumTags = (bool)($oProfile->GetParam('AUTO_FIX_XML_NUMTAGS')=='Y');
						$tags = $oProfile->GetParam('AUTO_FIX_XML_CDATA');
						$arTags = array_diff(array_unique(array_map('trim', explode(',', $tags))), array(''));
						$bufferSize = 65536;
						$bufferEnd = '';
						while(!feof($handle)) 
						{
							$buffer2 = $bufferEnd.fgets($handle, $bufferSize);
							while(($pos = strrpos($buffer2, '<'))===false && !feof($handle))
							{
								$buffer2 .= fgets($handle, $bufferSize);
							}
							$bufferEnd = '';
							if($pos!==false && !feof($handle))
							{
								if(substr($buffer2, $pos, 1)!=='<' && function_exists('mb_strrpos'))
								{
									$encoding = self::getSiteEncoding();
									$pos = mb_strrpos($buffer2, '<', $encoding);
									if(mb_substr($buffer2, $pos, 1, $encoding)!=='<')
									{
										$encoding = ($encoding=='utf-8' ? 'windows-1251' : 'utf-8');
										$pos = mb_strrpos($buffer2, '<', $encoding);
									}
									$bufferEnd = mb_substr($buffer2, $pos, 2000000000, $encoding);
									$buffer2 = mb_substr($buffer2, 0, $pos, $encoding);
								}
								else
								{
									$bufferEnd = substr($buffer2, $pos);
									$buffer2 = substr($buffer2, 0, $pos);
								}
							}

							$buffer2 = preg_replace('/[\x00-\x04]/', '', $buffer2);
							$buffer2 = preg_replace('/&(?!(amp;|quot;|#039;|lt;|gt;))/', '&amp;', $buffer2);
							if($bNumTags)
							{
								$buffer2 = preg_replace('/(<\/?[^\s>]*[^\s>\d\_\-])[\_\-]*\d+(\s[^>]*>|>)/is', '$1$2', $buffer2);
							}
							foreach($arTags as $tag)
							{
								$buffer2 = preg_replace('/(<'.$tag.'[^>]*>)\s+(\S|$)/is', '$1$2', $buffer2);
								$buffer2 = preg_replace('/(<'.$tag.'[^>]*>)(?!<\!\[CDATA\[)/Uis', '$1<![CDATA[', $buffer2);
								$buffer2 = preg_replace('/(^|\S)\s+(<\/'.$tag.'>)/is', '$1$2', $buffer2);
								$buffer2 = preg_replace('/(?<!\]\]\>)(<\/'.$tag.'>)/Uis', ']]>$1', $buffer2);
							}
							fwrite($handle2, $buffer2);
						}
					}
					else
					{
						while(!feof($handle)) 
						{
							fwrite($handle2, fgets($handle));
						}
					}
					fclose($handle2);
					fclose($handle);
					
					unlink($strPhysicalFileNameX);
					copy($tmpFile, $strPhysicalFileNameX);
					unlink($tmpFile);
				}
				else
				{
					fclose($handle);
				}
			}
			/*/Remove bad string*/
		}

		if($arFile["WIDTH"] == 0 || $arFile["HEIGHT"] == 0)
		{
			//mock image because we got false from CFile::GetImageSize()
			if(strpos($arFile["type"], "image/") === 0)
			{
				$arFile["type"] = "application/octet-stream";
			}
		}

		if($arFile["type"] == '' || !is_string($arFile["type"]))
		{
			$arFile["type"] = "application/octet-stream";
		}

		/****************************** QUOTA ******************************/
		if (\COption::GetOptionInt("main", "disk_space") > 0)
		{
			\CDiskQuota::updateDiskQuota("file", $arFile["size"], "insert");
		}
		/****************************** QUOTA ******************************/

		$NEW_IMAGE_ID = \CFile::DoInsert(array(
			"HEIGHT" => $arFile["HEIGHT"],
			"WIDTH" => $arFile["WIDTH"],
			"FILE_SIZE" => $arFile["size"],
			"CONTENT_TYPE" => $arFile["type"],
			"SUBDIR" => $arFile["SUBDIR"],
			"FILE_NAME" => $arFile["FILE_NAME"],
			"MODULE_ID" => $arFile["MODULE_ID"],
			"ORIGINAL_NAME" => $arFile["ORIGINAL_NAME"],
			"DESCRIPTION" => isset($arFile["description"])? $arFile["description"]: '',
			"HANDLER_ID" => isset($arFile["HANDLER_ID"])? $arFile["HANDLER_ID"]: '',
			"EXTERNAL_ID" => isset($arFile["external_id"])? $arFile["external_id"]: md5(mt_rand()),
		));

		\CFile::CleanCache($NEW_IMAGE_ID);
		return $NEW_IMAGE_ID;
	}
	
	public static function CopyFile($FILE_ID, $bRegister = true, $newPath = "")
	{
		global $DB;

		$err_mess = "FILE: ".__FILE__."<br>LINE: ";
		$z = \CFile::GetByID($FILE_ID);
		if($zr = $z->Fetch())
		{
			/****************************** QUOTA ******************************/
			if (\COption::GetOptionInt("main", "disk_space") > 0)
			{
				$quota = new \CDiskQuota();
				if (!$quota->checkDiskQuota($zr))
					return false;
			}
			/****************************** QUOTA ******************************/

			$strNewFile = '';
			$bSaved = false;
			$bExternalStorage = false;
			foreach(GetModuleEvents("main", "OnFileCopy", true) as $arEvent)
			{
				if($bSaved = ExecuteModuleEventEx($arEvent, array(&$zr, $newPath)))
				{
					$bExternalStorage = true;
					break;
				}
			}

			$io = \CBXVirtualIo::GetInstance();

			if(!$bExternalStorage)
			{
				$strDirName = $_SERVER["DOCUMENT_ROOT"]."/".(\COption::GetOptionString("main", "upload_dir", "upload"));
				$strDirName = rtrim(str_replace("//","/",$strDirName), "/");

				$zr["SUBDIR"] = trim($zr["SUBDIR"], "/");
				$zr["FILE_NAME"] = ltrim($zr["FILE_NAME"], "/");

				$strOldFile = $strDirName."/".$zr["SUBDIR"]."/".$zr["FILE_NAME"];

				if(strlen($newPath))
					$strNewFile = $strDirName."/".ltrim($newPath, "/");
				else
				{
					$i = 1;
					while(($strNewFile = $strDirName."/".$zr["SUBDIR"]."/".preg_replace('/(\.[^\.]*)$/', '['.$i.']$1', $zr["FILE_NAME"])) && $io->FileExists($strNewFile) && $i<1000)
					{
						$i++;
					}
				}

				$zr["FILE_NAME"] = bx_basename($strNewFile);
				$zr["SUBDIR"] = substr($strNewFile, strlen($strDirName)+1, -(strlen(bx_basename($strNewFile)) + 1));

				if(strlen($newPath))
					CheckDirPath($strNewFile);

				$bSaved = copy($io->GetPhysicalName($strOldFile), $io->GetPhysicalName($strNewFile));
			}

			if($bSaved)
			{
				if($bRegister)
				{
					$arFields = array(
						"TIMESTAMP_X" => $DB->GetNowFunction(),
						"MODULE_ID" => "'".$DB->ForSql($zr["MODULE_ID"], 50)."'",
						"HEIGHT" => intval($zr["HEIGHT"]),
						"WIDTH" => intval($zr["WIDTH"]),
						"FILE_SIZE" => intval($zr["FILE_SIZE"]),
						"ORIGINAL_NAME" => "'".$DB->ForSql($zr["ORIGINAL_NAME"], 255)."'",
						"DESCRIPTION" => "'".$DB->ForSql($zr["DESCRIPTION"], 255)."'",
						"CONTENT_TYPE" => "'".$DB->ForSql($zr["CONTENT_TYPE"], 255)."'",
						"SUBDIR" => "'".$DB->ForSql($zr["SUBDIR"], 255)."'",
						"FILE_NAME" => "'".$DB->ForSql($zr["FILE_NAME"], 255)."'",
						"HANDLER_ID" => $zr["HANDLER_ID"]? intval($zr["HANDLER_ID"]): "null",
						"EXTERNAL_ID" => $zr["EXTERNAL_ID"] != ""? "'".$DB->ForSql($zr["EXTERNAL_ID"], 50)."'": "null",
					);
					$NEW_FILE_ID = $DB->Insert("b_file",$arFields, $err_mess.__LINE__);

					if (\COption::GetOptionInt("main", "disk_space") > 0)
						\CDiskQuota::updateDiskQuota("file", $zr["FILE_SIZE"], "copy");

					\CFile::CleanCache($NEW_FILE_ID);

					return $NEW_FILE_ID;
				}
				else
				{
					if(!$bExternalStorage)
						return substr($strNewFile, strlen(rtrim($_SERVER["DOCUMENT_ROOT"], "/")));
					else
						return $bSaved;
				}
			}
			else
			{
				return false;
			}
		}
		return 0;
	}
	
	protected function transformName($name, $bForceMD5 = false, $bSkipExt = false)
	{
		//safe filename without path
		$fileName = GetFileName($name);

		$originalName = ($bForceMD5 != true);
		if($originalName)
		{
			//transforming original name:

			//transliteration
			if(\COption::GetOptionString("main", "translit_original_file_name", "N") == "Y")
			{
				$fileName = \CUtil::translit($fileName, LANGUAGE_ID, array("max_len"=>1024, "safe_chars"=>".", "replace_space" => '-'));
			}

			//replace invalid characters
			if(\COption::GetOptionString("main", "convert_original_file_name", "Y") == "Y")
			{
				$io = \CBXVirtualIo::GetInstance();
				$fileName = $io->RandomizeInvalidFilename($fileName);
			}
		}

		//.jpe is not image type on many systems
		if($bSkipExt == false && strtolower(GetFileExtension($fileName)) == "jpe")
		{
			$fileName = substr($fileName, 0, -4).".jpg";
		}

		//double extension vulnerability
		$fileName = RemoveScriptExtension($fileName);

		if(!$originalName)
		{
			//name is md5-generated:
			$fileName = md5(uniqid("", true)).($bSkipExt == true || ($ext = GetFileExtension($fileName)) == ''? '' : ".".$ext);
		}

		return $fileName;
	}

	protected function validateFile($strFileName, $arFile)
	{
		if($strFileName == '')
			return Loc::getMessage("FILE_BAD_FILENAME");

		$io = \CBXVirtualIo::GetInstance();
		if(!$io->ValidateFilenameString($strFileName))
			return Loc::getMessage("MAIN_BAD_FILENAME1");

		if(strlen($strFileName) > 255)
			return Loc::getMessage("MAIN_BAD_FILENAME_LEN");

		//check .htaccess etc.
		if(IsFileUnsafe($strFileName))
			return Loc::getMessage("FILE_BAD_TYPE");

		//nginx returns octet-stream for .jpg
		if(GetFileNameWithoutExtension($strFileName) == '')
			return Loc::getMessage("FILE_BAD_FILENAME");

		if (\COption::GetOptionInt("main", "disk_space") > 0)
		{
			$quota = new \CDiskQuota();
			if (!$quota->checkDiskQuota($arFile))
				return Loc::getMessage("FILE_BAD_QUOTA");
		}

		return "";
	}
	
	function GetFilesByExt($path, $arExt=array())
	{
		$arFiles = array();
		$arDirFiles = array_diff(scandir($path), array('.', '..'));
		foreach($arDirFiles as $file)
		{
			if(is_file($path.$file) && (empty($arExt) || preg_match('/\.('.implode('|', $arExt).')$/i', ToLower($file))))
			{
				$arFiles[] = $path.$file;
			}
		}
		foreach($arDirFiles as $file)
		{
			if(is_dir($path.$file))
			{
				$arFiles = array_merge($arFiles, self::GetFilesByExt($path.$file.'/', $arExt));
			}
		}
		return $arFiles;
	}
	
	public static function GetFileSystemEncoding()
	{
		if(!isset(static::$fileSystemEncoding))
		{
			$fileSystemEncoding = strtolower(defined("BX_FILE_SYSTEM_ENCODING") ? BX_FILE_SYSTEM_ENCODING : "");

			if (empty($fileSystemEncoding))
			{
				if (strtoupper(substr(PHP_OS, 0, 3)) === "WIN")
					$fileSystemEncoding =  "windows-1251";
				else
					$fileSystemEncoding = "utf-8";
			}
			static::$fileSystemEncoding = $fileSystemEncoding;
		}
		return static::$fileSystemEncoding;
	}
	
	public static function CorrectEncodingForExtractDir($path)
	{
		$fileSystemEncoding = self::GetFileSystemEncoding();
		$arFiles = array();
		$arDirFiles = array_diff(scandir($path), array('.', '..'));
		foreach($arDirFiles as $file)
		{
			if(preg_match('/[^A-Za-z0-9_\-]/', $file))
			{
				$newfile = \Bitrix\Main\Text\Encoding::convertEncoding($file, $fileSystemEncoding, "cp866");
				$isUtf8 = \CUtil::DetectUTF8($newfile);
				if($isUtf8 && $fileSystemEncoding!='utf-8')
				{
					$newfile = \Bitrix\Main\Text\Encoding::convertEncoding($newfile, 'utf-8', $fileSystemEncoding);
				}
				elseif(!$isUtf8 && $fileSystemEncoding=='utf-8')
				{
					$newfile = \Bitrix\Main\Text\Encoding::convertEncoding($newfile, 'windows-1251', $fileSystemEncoding);
				}
				$res = rename($path.$file, $path.$newfile);
				$file = $newfile;
			}
			if(is_dir($path.$file))
			{
				self::CorrectEncodingForExtractDir($path.$file.'/');
			}
		}
	}
	
	public static function GetDateFormat($m)
	{
		$format = str_replace('_', ' ', $m[1]);
		return ToLower(\CIBlockFormatProperties::DateFormat($format, time()));
	}
	
	public static function MergeCookie(&$arCookies, $arNewCookies)
	{
		if(!is_array($arCookies)) $arCookies = array();
		if(!is_array($arNewCookies)) $arNewCookies = array();
		foreach($arNewCookies as $k=>$v)
		{
			/*if(!isset($arCookies[$k]) || strpos(Tolower($k), 'session')===false)
			{
				$arCookies[$k] = $v;
			}*/
			$arCookies[$k] = $v;
		}
	}
	
	public static function GetNewLocation(&$location, $newLoc)
	{
		$arUrl = parse_url($location);
		$newLoc = trim($newLoc);
		$location = $newLoc;
		if(strlen($newLoc) > 0 && stripos($newLoc, 'http')!==0)
		{
			if(strpos($newLoc, '/')===0)
			{
				$location = $arUrl['scheme'].'://'.$arUrl['host'].$newLoc;
			}
			else
			{
				$dir = preg_replace('/[\/]+/', '/', preg_replace('/(^|\/)[^\/]*$/', '', $arUrl['path']).'/');
				$location = $arUrl['scheme'].'://'.$arUrl['host'].$dir.$newLoc;
			}
		}
	}
	
	public static function MakeFileArray($path, $maxTime = 0)
	{
		$arExt = array('xml', 'yml', 'json');
		if(is_array($path))
		{
			$arFile = $path;
			$temp_path = \CFile::GetTempName('', \Bitrix\Main\IO\Path::convertLogicalToPhysical($arFile["name"]));
			CheckDirPath($temp_path);
			if(!copy($arFile["tmp_name"], $temp_path)
				&& !move_uploaded_file($arFile["tmp_name"], $temp_path))
			{
				return false;
			}
			$arFile = \CFile::MakeFileArray($temp_path);
		}
		else
		{
			$path = trim($path);
			
			$arCookies = array();
			$arHeaders = array('User-Agent' => 'BitrixSM HttpClient class');
			if(preg_match('/^\{.*\}$/s', $path))
			{
				$arParams = \CUtil::JsObjectToPhp($path);
				if(is_array($arParams['HEADERS'])) $arHeaders = array_merge($arHeaders, $arParams['HEADERS']);
				$ctHeaderKeys = preg_grep('/content\-type/i', array_keys($arHeaders));
				if(count($ctHeaderKeys) > 0)
				{
					$ctHeaderKey = current($ctHeaderKeys);
					$contentType = $arHeaders[$ctHeaderKey];
					if(ToLower($contentType)=='application/json')
					{
						if(function_exists('json_encode')) $arParams['VARS'] = json_encode($arParams['VARS']);
						else $arParams['VARS'] = '{'.implode(',', array_map(create_function('$k,$v', 'return "\"".addcslashes($k, "\"")."\":\"".addcslashes($v, "\"")."\"";'), array_keys($arParams['VARS']), array_values($arParams['VARS']))).'}';
					}
				}
				if(isset($arParams['FILELINK']))
				{
					$path = $arParams['FILELINK'];
					
					if(!empty($arParams['VARS']) && $arParams['PAGEAUTH'])
					{
						$redirectCount = 0;
						$location = $arParams['PAGEAUTH'];
						while(strlen($location)>0 && $redirectCount<=5)
						{
							$client = new \Bitrix\Main\Web\HttpClient(array('disableSslVerification'=>true, 'redirect'=>false));
							$client->setCookies($arCookies);
							foreach($arHeaders as $hk=>$hv) $client->setHeader($hk, $hv);
							$res = $client->get($location);
							static::MergeCookie($arCookies, $client->getCookies()->toArray());
							$arHeaders['Referer'] = $location;
							$location = $client->getHeaders()->get("Location");
							$status = $client->getStatus();
							if(!in_array($status, array(301, 302, 303))) $location = '';
							$redirectCount++;
						}
						$needEncoding = $siteEncoding = self::getSiteEncoding();
						if(preg_match('/charset=(.*)(;|$)/', $client->getHeaders()->get("Content-Type"), $m) && strlen(trim($m[1])) > 0)
						{
							$needEncoding = ToLower(trim($m[1]));
						}
						if(is_array($arParams['VARS']))
						{
							if(strlen(trim($v)) > 0 && $needEncoding!=$siteEncoding)
							{
								$arParams['VARS'][$k] = \Bitrix\Main\Text\Encoding::convertEncoding($v, $siteEncoding, $needEncoding);
							}
							foreach($arParams['VARS'] as $k=>$v)
							{
								if(strlen(trim($v))==0 
									&& preg_match('/<input[^>]*name=[\'"]'.addcslashes($k, '-').'[\'"][^>]*>/Uis', $res, $m1)
									&& preg_match('/value=[\'"]([^\'"]*)[\'"]/Uis', $m1[0], $m2))
								{
										$arParams['VARS'][$k] = html_entity_decode($m2[1], ENT_COMPAT, $siteEncoding);
								}
							}
						}
						
						$redirectCount = 0;
						$location = ($arParams['POSTPAGEAUTH'] ? $arParams['POSTPAGEAUTH'] : $arParams['PAGEAUTH']);
						while(strlen($location)>0 && $redirectCount<=5)
						{
							$client = new \Bitrix\Main\Web\HttpClient(array('disableSslVerification'=>true, 'redirect'=>false));
							$client->setCookies($arCookies);
							foreach($arHeaders as $hk=>$hv) $client->setHeader($hk, $hv);
							$res = $client->post($location, $arParams['VARS']);
							$status = $client->getStatus();
							if($status==404)
							{
								$client = new \Bitrix\Main\Web\HttpClient(array('disableSslVerification'=>true, 'redirect'=>false));
								$client->setCookies($arCookies);
								foreach($arHeaders as $hk=>$hv) $client->setHeader($hk, $hv);
								$res = $client->get($location);
								$status = $client->getStatus();
							}
							static::MergeCookie($arCookies, $client->getCookies()->toArray());
							$arHeaders['Referer'] = $location;
							$location = $client->getHeaders()->get("Location");
							if(!in_array($status, array(301, 302, 303))) $location = '';
							$redirectCount++;
						}
					}
					
					if(strlen($arParams['HANDLER_FOR_LINK_BASE64']) > 0) $handler = base64_decode(trim($arParams['HANDLER_FOR_LINK_BASE64']));
					else $handler = trim($arParams['HANDLER_FOR_LINK']);
					if(strlen($handler) > 0)
					{
						$val = '';
						if($path)
						{
							$client = new \Bitrix\Main\Web\HttpClient(array('disableSslVerification'=>true));
							$client->setCookies($arCookies);
							foreach($arHeaders as $hk=>$hv) $client->setHeader($hk, $hv);
							$val = $client->get($path);
						}
						$res = self::ExecuteFilterExpression($val, $handler, '');
						if(is_array($res))
						{
							if(isset($res['PATH'])) $path = $res['PATH'];
							if(isset($res['COOKIES']) && is_array($res['COOKIES'])) $arCookies = array_merge($arCookies, $res['COOKIES']);
						}
						else
						{
							$path = $res;
						}
					}
				}
			}
			
			$path = preg_replace_callback('/\{DATE_(\S*)\}/', array('\Bitrix\EsolImportxml\Utils', 'GetDateFormat'), $path);
			if(!$maxTime) $maxTime = min(intval(ini_get('max_execution_time')) - 5, 1800);
			if(ini_get('max_execution_time')==='0') $maxTime = 300;
			elseif($maxTime<=0) $maxTime = 50;
			$cloud = new \Bitrix\EsolImportxml\Cloud();
			if($service = $cloud->GetService($path))
			{
				$arFile = $cloud->MakeFileArray($service, $path);
			}
			elseif(($maxTime > 15 || !empty($arCookies)) && preg_match("#^(http[s]?)://#", $path) && class_exists('\Bitrix\Main\Web\HttpClient'))
			{
				$path = rawurldecode($path);
				$arUrl = parse_url($path);
				//Cyrillic domain
				if(preg_match('/[^A-Za-z0-9\-\.]/', $arUrl['host']))
				{
					if(!class_exists('idna_convert')) require_once(dirname(__FILE__).'/idna_convert.class.php');
					if(class_exists('idna_convert'))
					{
						$idn = new \idna_convert();
						$oldHost = $arUrl['host'];
						if(!\CUtil::DetectUTF8($oldHost)) $oldHost = \Bitrix\EsolImportxml\Utils::Win1251Utf8($oldHost);
						$path = str_replace($arUrl['host'], $idn->encode($oldHost), $path);
					}
				}

				$temp_path = '';
				$bExternalStorage = false;
				/*foreach(GetModuleEvents("main", "OnMakeFileArray", true) as $arEvent)
				{
					if(ExecuteModuleEventEx($arEvent, array($path, &$temp_path)))
					{
						$bExternalStorage = true;
						break;
					}
				}*/
				
				if(!$bExternalStorage)
				{
					$urlComponents = parse_url($path);
					$postBody = '';
					if(isset($urlComponents['fragment']) && stripos($urlComponents['fragment'], 'postbody=')===0)
					{
						$path = substr($path, 0, -strlen($urlComponents['fragment'])-1);
						$postBody = substr($urlComponents['fragment'], 9);
					}
					if ($urlComponents && strlen($urlComponents["path"]) > 0) $baseName = bx_basename($urlComponents["path"]);
					else $baseName = bx_basename($path);
					$basename = preg_replace('/\?.*$/', '', $baseName);
					if(preg_match('/^[_+=!?]*\./', $baseName) || strlen(trim($baseName))==0) $baseName = 'f'.$baseName;
					$temp_path2 = \CFile::GetTempName('', $baseName);
					$temp_path = \Bitrix\Main\IO\Path::convertLogicalToPhysical($temp_path2);
					
					if(!\CUtil::DetectUTF8($path)) $path = self::Win1251Utf8($path);
					$path = preg_replace_callback('/[^:@\/?=&#]+/', create_function('$m', 'return rawurlencode($m[0]);'), $path);

					$ob = new \Bitrix\Main\Web\HttpClient(array('socketTimeout'=>$maxTime, 'streamTimeout'=>$maxTime, 'disableSslVerification'=>true));
					$ob->setCookies($arCookies);
					foreach($arHeaders as $hk=>$hv) $ob->setHeader($hk, $hv);
					if(strlen($postBody) > 0)
					{
						if(strpos($postBody, '<?xml')!==false) $ob->setHeader('content-type', 'application/xml');
						if($dRes = $ob->post($path, $postBody))
						{
							$dir = \Bitrix\Main\IO\Path::getDirectory($temp_path2);
							\Bitrix\Main\IO\Directory::createDirectory($dir);
							file_put_contents($temp_path, $dRes);
						}
					}
					else
					{
						$dRes = $ob->download($path, $temp_path2);
					}
					if($dRes)
					{
						if($ob->getStatus()!=200)
						{
							$ob = new \Bitrix\Main\Web\HttpClient(array('socketTimeout'=>$maxTime, 'streamTimeout'=>$maxTime, 'disableSslVerification'=>true, 'redirect'=>false));
							foreach($arHeaders as $hk=>$hv) $ob->setHeader($hk, $hv);
							$ob->get($path);
							if(in_array($ob->getStatus(), array(301, 302, 303)))
							{
								$arCookies = $ob->getCookies()->toArray();
								$ob = new \Bitrix\Main\Web\HttpClient(array('socketTimeout'=>10, 'streamTimeout'=>10, 'disableSslVerification'=>true));
								foreach($arHeaders as $hk=>$hv) $ob->setHeader($hk, $hv);
								$ob->setCookies($arCookies);
								$ob->download($path, $temp_path2);
							}
						}
						
						$i = 0;
						$handle = fopen($temp_path, 'r');
						while(!($str = trim(fgets($handle, 1024))) && !feof($handle) && ++$i<10) {}
						fclose($handle);
						$isXmlHeader = (bool)(stripos(trim($str), '<?xml')!==false);
						$isJsonHeader = (bool)(in_array(substr(trim($str), 0, 1), array('[', '{')));

						$hcd = $ob->getHeaders()->get('content-disposition');
						$hct = $ob->getHeaders()->get('content-type');
						if($hcd && stripos($hcd, 'filename=')!==false)
						{
							$hcdParts = preg_grep('/filename=/i', array_map('trim', explode(';', $hcd)));
							if(count($hcdParts) > 0)
							{
								$hcdParts = explode('=', current($hcdParts));
								$fn = end(explode('/', trim(end($hcdParts), '"\' ')));
								if(strlen($fn) > 0 && strpos($temp_path, $fn)===false)
								{
									$old_temp_path = $temp_path;
									$temp_path = preg_replace('/\/[^\/]+$/', '/'.$fn, $old_temp_path);
									rename($old_temp_path, $temp_path);
								}
							}
						}
						elseif((ToLower(substr($temp_path, -4))=='.php' && strpos(ToLower($path), 'xml')!==false)
							|| (stripos($hct, 'text/xml')!==false) || (stripos($hct, 'application/xml')!==false) || $isXmlHeader)
						{
							$old_temp_path = $temp_path;
							//$temp_path = $temp_path.'.xml';
							$temp_path2 = \CFile::GetTempName('', bx_basename($temp_path2).'.xml');
							$dir = \Bitrix\Main\IO\Path::getDirectory($temp_path2);
							\Bitrix\Main\IO\Directory::createDirectory($dir);
							$temp_path = \Bitrix\Main\IO\Path::convertLogicalToPhysical($temp_path2);
							rename($old_temp_path, $temp_path);
						}
						elseif((stripos($hct, 'application/json')!==false || $isJsonHeader) && !in_array(ToLower(self::GetFileExtension($temp_path)), array('xml', 'yml', 'json')))
						{
							$old_temp_path = $temp_path;
							$temp_path2 = \CFile::GetTempName('', bx_basename($temp_path2).'.json');
							$dir = \Bitrix\Main\IO\Path::getDirectory($temp_path2);
							\Bitrix\Main\IO\Directory::createDirectory($dir);
							$temp_path = \Bitrix\Main\IO\Path::convertLogicalToPhysical($temp_path2);
							rename($old_temp_path, $temp_path);
						}
						$arFile = \CFile::MakeFileArray($temp_path);
					}
				}
				elseif($temp_path)
				{
					$arFile = \CFile::MakeFileArray($temp_path);
				}
				
				if(strlen($arFile["type"])<=0)
					$arFile["type"] = "unknown";
			}
			elseif(preg_match('/ftp(s)?:\/\//', $path))
			{
				$sftp = new \Bitrix\EsolImportxml\Sftp();
				$arFile = $sftp->MakeFileArray($path, array('TIMEOUT'=>20));
			}
			else
			{
				$arFile = \CFile::MakeFileArray($path);
			}
		}
		
		$ext = ToLower(self::GetFileExtension($arFile['tmp_name']));
		if(in_array($arFile['type'], array('application/zip', 'application/x-zip-compressed', 'application/gzip', 'application/x-gzip', 'application/rar', 'application/x-rar', 'application/x-rar-compressed', 'application/octet-stream')) && !in_array($ext, $arExt))
		{
			$tmpsubdir = dirname($arFile['tmp_name']).'/zip/';
			CheckDirPath($tmpsubdir);	
			if(substr($ext, -3)=='.gz' && $ext!='tar.gz' && function_exists('gzopen'))
			{
				$handle1 = gzopen($arFile['tmp_name'], 'rb');
				$handle2 = fopen($tmpsubdir.substr(basename($arFile['tmp_name']), 0, -3), 'wb');
				while(!gzeof($handle1)) {
					fwrite($handle2, gzread($handle1, 4096));
				}
				fclose($handle2);
				gzclose($handle1);
			}
			elseif($ext=='rar' && class_exists('\RarArchive'))
			{
				$rar = \RarArchive::open($arFile['tmp_name']);
				$entries = $rar->getEntries();
				foreach($entries as $entry)
				{
					$entry->extract($tmpsubdir);
				}
				$rar->close();
			}
			else
			{
				$type = (in_array($ext, array('tar.gz', 'tgz')) ? 'TAR.GZ' : 'ZIP');
				$zipObj = \CBXArchive::GetArchive($arFile['tmp_name'], $type);
				$zipObj->Unpack($tmpsubdir);
			}
			if($arFile['type']=='application/zip') self::CorrectEncodingForExtractDir($tmpsubdir);
			$arFile = array();
			if(!is_array($path)) $urlComponents = parse_url($path);
			else $urlComponents = array();
			if(isset($urlComponents['fragment']) && strlen($urlComponents['fragment']) > 0)
			{
				$fn = $tmpsubdir.ltrim($urlComponents['fragment'], '/');
				$arFiles = array($fn);
				if((strpos($fn, '*')!==false || (strpos($fn, '{')!==false && strpos($fn, '}')!==false)) && !file_exists($fn))
				{
					$arFiles = glob($fn, GLOB_BRACE);
				}
			}
			else
			{
				$arFiles = self::GetFilesByExt($tmpsubdir, $arExt);
			}
			if(count($arFiles) > 0)
			{
				$tmpfile = current($arFiles);
				$temp_path = \CFile::GetTempName('', bx_basename($tmpfile));
				$dir = \Bitrix\Main\IO\Path::getDirectory($temp_path);
				\Bitrix\Main\IO\Directory::createDirectory($dir);
				copy($tmpfile, $temp_path);
				$arFile = \CFile::MakeFileArray($temp_path);
			}
			DeleteDirFilesEx(substr($tmpsubdir, strlen($_SERVER['DOCUMENT_ROOT'])));
		}
		
		self::CheckJsonFile($arFile);
		return $arFile;
	}
	
	public static function CheckJsonFile(&$arFile)
	{
		$ext = ToLower(self::GetFileExtension($arFile['tmp_name']));
		if($ext=='json')
		{
			$tempPath = \CFile::GetTempName('', \Bitrix\Main\IO\Path::convertLogicalToPhysical($arFile['name']).'.xml');
			$dir = \Bitrix\Main\IO\Path::getDirectory($tempPath);
			\Bitrix\Main\IO\Directory::createDirectory($dir);
			$j2x = new \Bitrix\EsolImportxml\Json2Xml();
			$j2x->Convert($arFile['tmp_name'], $tempPath);
			$arFile = \CFile::MakeFileArray($tempPath);
		}
	}
	
	public static function GetNewFile($newName)
	{
		$temp_path = \CFile::GetTempName('', bx_basename($newName));
		$temp_dir = \Bitrix\Main\IO\Path::getDirectory($temp_path);
		\Bitrix\Main\IO\Directory::createDirectory($temp_dir);
		return $temp_path;
	}
	
	public static function RemoveOldFile($old_temp_path)
	{
		unlink($old_temp_path);
		$dir = dirname($old_temp_path);
		if(count(array_diff(scandir($dir), array('.', '..')))==0)
		{
			rmdir($dir);
		}
	}
	
	public static function ReplaceFile($old_temp_path, $newName)
	{
		$temp_path = self::GetNewFile($newName);
		copy($old_temp_path, $temp_path);
		self::RemoveOldFile($old_temp_path);
		return $temp_path;
	}
	
	public static function GetFileExtension($filename)
	{
		$filename = end(explode('/', $filename));
		$arParts = explode('.', $filename);
		if(count($arParts) > 1) 
		{
			$ext = array_pop($arParts);
			if(ToLower($ext)=='gz' && count($arParts) > 1)
			{
				$ext = array_pop($arParts).'.'.$ext;
			}
			return $ext;
		}
		else return '';
	}
	
	public static function GetShowFileBySettings($SETTINGS_DEFAULT)
	{
		$path = $link = '';
		if($SETTINGS_DEFAULT["EXT_DATA_FILE"])
		{
			if(preg_match('/^\{.*\}$/s', $SETTINGS_DEFAULT["EXT_DATA_FILE"]))
			{
				$arParams = \CUtil::JsObjectToPhp($SETTINGS_DEFAULT["EXT_DATA_FILE"]);
				if(isset($arParams['FILELINK']))
				{
					$path = $arParams['FILELINK'];
				}
			}
			else
			{
				$path = $SETTINGS_DEFAULT["EXT_DATA_FILE"];
			}
			if($path) $link = $path;
		}
		elseif($SETTINGS_DEFAULT["EMAIL_DATA_FILE"])
		{
			$arParams = \CUtil::JsObjectToPhp($SETTINGS_DEFAULT["EMAIL_DATA_FILE"]);
			if(isset($arParams['EMAIL']))
			{
				$path = $arParams['EMAIL'];
			}
		}
		return array('link'=>$link, 'path'=>$path);
	}
	
	public static function AddFileInputActions()
	{
		//AddEventHandler("main", "OnEndBufferContent", Array("\Bitrix\EsolImportxml\Utils", "AddFileInputActionsHandler"));
	}
	
	public static function AddFileInputActionsHandler(&$content)
	{
		return;
		//if(!function_exists('imap_open')) return;
		
		$comment = 'ESOL_IX_CHOOSE_FILE';
		$commentBegin = '<!--'.$comment.'-->';
		$commentEnd = '<!--/'.$comment.'-->';
		$pos1 = strpos($content, $commentBegin);
		$pos2 = strpos($content, $commentEnd);
		if($pos1!==false && $pos2!==false)
		{
			$partContent = substr($content, $pos1, $pos2 + strlen($commentEnd) - $pos1);
			if(preg_match_all('/<script[^>]*>.*<\/script>/Uis', $partContent, $m))
			{
				$arScripts = preg_grep('/BX\.file_input\((\{.*\'bx_file_data_file\'.*\})\)[;<]/Uis', $m[0]);
				while(count($arScripts) > 1)
				{
					$script = array_pop($arScripts);
					if($pos = strrpos($partContent, $script))
					{
						$newPartContent = substr($partContent, 0, $pos).substr($partContent, $pos+strlen($script));
						$content = str_replace($partContent, $newPartContent, $content);
						$partContent = $newPartContent;
					}
				}
			}
			if(preg_match('/BX\.file_input\((\{.*\})\)\s*[:;<]/Us', $partContent, $m))
			{
				$json = $m[1];
				$arConfig = \CUtil::JsObjectToPhp($json);
				array_walk_recursive($arConfig, create_function('&$n, $k', 'if($n=="true"){$n=true;}elseif($n=="false"){$n=false;}'));
				$arConfigEmail = array(
					'TEXT' => Loc::getMessage("ESOL_IX_FILE_SOURCE_EMAIL"),
					'GLOBAL_ICON' => 'adm-menu-upload-email',
					'ONCLICK' => 'EProfile.ShowEmailForm();'
				);
				$arConfig['menuNew'][] = $arConfigEmail;
				$arConfig['menuExist'][] = $arConfigEmail;
				$arConfigLinkAuth = array(
					'TEXT' => Loc::getMessage("ESOL_IX_FILE_SOURCE_LINKAUTH"),
					'GLOBAL_ICON' => 'adm-menu-upload-linkauth',
					'ONCLICK' => 'EProfile.ShowFileAuthForm();'
				);
				$arConfig['menuNew'][] = $arConfigLinkAuth;
				$arConfig['menuExist'][] = $arConfigLinkAuth;
				$newJson = \CUtil::PHPToJSObject($arConfig);
				$newPartContent = str_replace($json, $newJson, $partContent);
				$content = str_replace($partContent, $newPartContent, $content);
			}
		}
	}
	
	public static function ExecuteFilterExpression($val, $expression, $altReturn = true)
	{
		$expression = trim($expression);
		try{				
			if(stripos($expression, 'return')===0)
			{
				return eval($expression.';');
			}
			elseif(preg_match('/\$val\s*=/', $expression))
			{
				eval($expression.';');
				return $val;
			}
			else
			{
				return eval('return '.$expression.';');
			}
		}catch(Exception $ex){
			return $altReturn;
		}
	}
	
	public static function ShowFilter($sTableID, $IBLOCK_ID, $FILTER)
	{
		global $APPLICATION;
		\CJSCore::Init('file_input');
		$sf = 'FILTER';

		Loader::includeModule('iblock');
		$bCatalog = Loader::includeModule('catalog');
		if($bCatalog)
		{
			$arCatalog = \CCatalog::GetByID($IBLOCK_ID);
			if($arCatalog)
			{
				if(is_callable(array('\CCatalogAdminTools', 'getIblockProductTypeList')))
				{
					$productTypeList = \CCatalogAdminTools::getIblockProductTypeList($IBLOCK_ID, true);
				}
				
				$arStores = array();
				$dbRes = \CCatalogStore::GetList(array("SORT"=>"ID"), array(), false, false, array("ID", "TITLE", "ADDRESS"));
				while($arStore = $dbRes->Fetch())
				{
					if(strlen($arStore['TITLE'])==0 && $arStore['ADDRESS']) $arStore['TITLE'] = $arStore['ADDRESS'];
					$arStores[] = $arStore;
				}
				
				$arPrices = array();
				$dbPriceType = \CCatalogGroup::GetList(array("SORT" => "ASC"));
				while($arPriceType = $dbPriceType->Fetch())
				{
					if(strlen($arPriceType["NAME_LANG"])==0 && $arPriceType['NAME']) $arPriceType['NAME_LANG'] = $arPriceType['NAME'];
					$arPrices[] = $arPriceType;
				}
			}
			if(!$arCatalog) $bCatalog = false;
		}
		
		$arFields = (is_array($FILTER) ? $FILTER : array());
		$dbrFProps = \CIBlockProperty::GetList(
			array(
				"SORT"=>"ASC",
				"NAME"=>"ASC"
			),
			array(
				"IBLOCK_ID"=>$IBLOCK_ID,
				"CHECK_PERMISSIONS"=>"N",
			)
		);

		$arProps = array();
		while ($arProp = $dbrFProps->GetNext())
		{
			if ($arProp["ACTIVE"] == "Y")
			{
				$arProp["PROPERTY_USER_TYPE"] = ('' != $arProp["USER_TYPE"] ? \CIBlockProperty::GetUserType($arProp["USER_TYPE"]) : array());
				$arProps[] = $arProp;
			}
		}
		
		?>
		<!--<form method="GET" name="find_form" id="find_form" action="">-->
		<div class="find_form_inner">
		<?
		$arFindFields = Array();
		//$arFindFields["IBEL_A_F_ID"] = Loc::getMessage("ESOL_IX_IBEL_A_F_ID");
		$arFindFields["IBEL_A_F_PARENT"] = Loc::getMessage("ESOL_IX_IBEL_A_F_PARENT");

		$arFindFields["IBEL_A_F_MODIFIED_WHEN"] = Loc::getMessage("ESOL_IX_IBEL_A_F_MODIFIED_WHEN");
		$arFindFields["IBEL_A_F_MODIFIED_BY"] = Loc::getMessage("ESOL_IX_IBEL_A_F_MODIFIED_BY");
		$arFindFields["IBEL_A_F_CREATED_WHEN"] = Loc::getMessage("ESOL_IX_IBEL_A_F_CREATED_WHEN");
		$arFindFields["IBEL_A_F_CREATED_BY"] = Loc::getMessage("ESOL_IX_IBEL_A_F_CREATED_BY");

		$arFindFields["IBEL_A_F_ACTIVE_FROM"] = Loc::getMessage("ESOL_IX_IBEL_A_ACTFROM");
		$arFindFields["IBEL_A_F_ACTIVE_TO"] = Loc::getMessage("ESOL_IX_IBEL_A_ACTTO");
		$arFindFields["IBEL_A_F_ACT"] = Loc::getMessage("ESOL_IX_IBEL_A_F_ACT");
		$arFindFields["IBEL_A_F_NAME"] = Loc::getMessage("ESOL_IX_IBEL_A_F_NAME");
		$arFindFields["IBEL_A_F_DESC"] = Loc::getMessage("ESOL_IX_IBEL_A_F_DESC");
		$arFindFields["IBEL_A_CODE"] = Loc::getMessage("ESOL_IX_IBEL_A_CODE");
		$arFindFields["IBEL_A_EXTERNAL_ID"] = Loc::getMessage("ESOL_IX_IBEL_A_EXTERNAL_ID");
		$arFindFields["IBEL_A_PREVIEW_PICTURE"] = Loc::getMessage("ESOL_IX_IBEL_A_PREVIEW_PICTURE");
		$arFindFields["IBEL_A_DETAIL_PICTURE"] = Loc::getMessage("ESOL_IX_IBEL_A_DETAIL_PICTURE");
		$arFindFields["IBEL_A_TAGS"] = Loc::getMessage("ESOL_IX_IBEL_A_TAGS");
		
		if ($bCatalog)
		{
			if(is_array($productTypeList)) $arFindFields["CATALOG_TYPE"] = Loc::getMessage("ESOL_IX_CATALOG_TYPE");
			$arFindFields["CATALOG_BUNDLE"] = Loc::getMessage("ESOL_IX_CATALOG_BUNDLE");
			$arFindFields["CATALOG_AVAILABLE"] = Loc::getMessage("ESOL_IX_CATALOG_AVAILABLE");
			$arFindFields["CATALOG_QUANTITY"] = Loc::getMessage("ESOL_IX_CATALOG_QUANTITY");
			if(is_array($arStores))
			{
				foreach($arStores as $arStore)
				{
					$arFindFields["CATALOG_STORE".$arStore['ID']."_QUANTITY"] = sprintf(Loc::getMessage("ESOL_IX_CATALOG_STORE_QUANTITY"), $arStore['TITLE']);
				}
			}
			if(is_array($arPrices))
			{
				foreach($arPrices as $arPrice)
				{
					$arFindFields["CATALOG_PRICE_".$arPrice['ID']] = sprintf(Loc::getMessage("ESOL_IX_CATALOG_PRICE"), $arPrice['NAME_LANG']);
				}
			}
		}

		foreach($arProps as $arProp)
			if($arProp["FILTRABLE"]=="Y" && $arProp["PROPERTY_TYPE"]!="F")
				$arFindFields["IBEL_A_PROP_".$arProp["ID"]] = $arProp["NAME"];
		
		$oFilter = new \CAdminFilter($sTableID."_filter", $arFindFields);
		
		$oFilter->Begin();
		?>
			<?/*?><tr>
				<td><?echo Loc::getMessage("ESOL_IX_FILTER_FROMTO_ID")?>:</td>
				<td nowrap>
					<input type="text" name="<?echo $sf;?>[find_el_id_start]" size="10" value="<?echo htmlspecialcharsex($arFields['find_el_id_start'])?>">
					...
					<input type="text" name="<?echo $sf;?>[find_el_id_end]" size="10" value="<?echo htmlspecialcharsex($arFields['find_el_id_end'])?>">
				</td>
			</tr><?*/?>

			<tr>
				<td><?echo Loc::getMessage("ESOL_IX_FIELD_SECTION_ID")?>:</td>
				<td>
					<select name="<?echo $sf;?>[find_section_section][]" multiple size="5">
						<option value="-1"<?if((is_array($arFields['find_section_section']) && in_array("-1", $arFields['find_section_section'])) || $arFields['find_section_section']=="-1")echo" selected"?>><?echo Loc::getMessage("ESOL_IX_VALUE_ANY")?></option>
						<option value="0"<?if((is_array($arFields['find_section_section']) && in_array("0", $arFields['find_section_section'])) || $arFields['find_section_section']=="0")echo" selected"?>><?echo Loc::getMessage("ESOL_IX_UPPER_LEVEL")?></option>
						<?
						$bsections = \CIBlockSection::GetTreeList(Array("IBLOCK_ID"=>$IBLOCK_ID), array("ID", "NAME", "DEPTH_LEVEL"));
						while($ar = $bsections->GetNext()):
							?><option value="<?echo $ar["ID"]?>"<?if((is_array($arFields['find_section_section']) && in_array($ar["ID"], $arFields['find_section_section'])) || $ar["ID"]==$arFields['find_section_section'])echo " selected"?>><?echo str_repeat("&nbsp;.&nbsp;", $ar["DEPTH_LEVEL"])?><?echo $ar["NAME"]?></option><?
						endwhile;
						?>
					</select><br>
					<input type="checkbox" name="<?echo $sf;?>[find_el_subsections]" value="Y"<?if($arFields['find_el_subsections']=="Y")echo" checked"?>> <?echo Loc::getMessage("ESOL_IX_INCLUDING_SUBSECTIONS")?>
				</td>
			</tr>

			<tr>
				<td><?echo Loc::getMessage("ESOL_IX_FIELD_TIMESTAMP_X")?>:</td>
				<td><?echo CalendarPeriod($sf."[find_el_timestamp_from]", htmlspecialcharsex($arFields['find_el_timestamp_from']), $sf."[find_el_timestamp_to]", htmlspecialcharsex($arFields['find_el_timestamp_to']), "filter_form", "Y")?></font></td>
			</tr>

			<tr>
				<td><?=Loc::getMessage("ESOL_IX_FIELD_MODIFIED_BY")?>:</td>
				<td>
					<?echo FindUserID(
						$sf."[find_el_modified_user_id]",
						$arFields['find_el_modified_user_id'],
						"",
						"filter_form",
						"5",
						"",
						" ... ",
						"",
						""
					);?>
				</td>
			</tr>

			<tr>
				<td><?echo Loc::getMessage("ESOL_IX_EL_ADMIN_DCREATE")?>:</td>
				<td><?echo CalendarPeriod($sf."[find_el_created_from]", htmlspecialcharsex($arFields['find_el_created_from']), $sf."[find_el_created_to]", htmlspecialcharsex($arFields['find_el_created_to']), "filter_form", "Y")?></td>
			</tr>

			<tr>
				<td><?echo Loc::getMessage("ESOL_IX_EL_ADMIN_WCREATE")?></td>
				<td>
					<?echo FindUserID(
						$sf."[find_el_created_user_id]",
						$arFields['find_el_created_user_id'],
						"",
						"filter_form",
						"5",
						"",
						" ... ",
						"",
						""
					);?>
				</td>
			</tr>

			<tr>
				<td><?echo Loc::getMessage("ESOL_IX_EL_A_ACTFROM")?>:</td>
				<td><?echo CalendarPeriod($sf."[find_el_date_active_from_from]", htmlspecialcharsex($arFields['find_el_date_active_from_from']), $sf."[find_el_date_active_from_to]", htmlspecialcharsex($arFields['find_el_date_active_from_to']), "filter_form")?></td>
			</tr>

			<tr>
				<td><?echo Loc::getMessage("ESOL_IX_EL_A_ACTTO")?>:</td>
				<td><?echo CalendarPeriod($sf."[find_el_date_active_to_from]", htmlspecialcharsex($arFields['find_el_date_active_to_from']), $sf."[find_el_date_active_to_to]", htmlspecialcharsex($arFields['find_el_date_active_to_to']), "filter_form")?></td>
			</tr>

			<tr>
				<td><?echo Loc::getMessage("ESOL_IX_FIELD_ACTIVE")?>:</td>
				<td>
					<select name="<?echo $sf;?>[find_el_active]">
						<option value=""><?=htmlspecialcharsex(Loc::getMessage('ESOL_IX_VALUE_ANY'))?></option>
						<option value="Y"<?if($arFields['find_el_active']=="Y")echo " selected"?>><?=htmlspecialcharsex(Loc::getMessage("ESOL_IX_YES"))?></option>
						<option value="N"<?if($arFields['find_el_active']=="N")echo " selected"?>><?=htmlspecialcharsex(Loc::getMessage("ESOL_IX_NO"))?></option>
					</select>
				</td>
			</tr>

			<tr>
				<td><?echo Loc::getMessage("ESOL_IX_FIELD_NAME")?>:</td>
				<td><input type="text" name="<?echo $sf;?>[find_el_name]" value="<?echo htmlspecialcharsex($arFields['find_el_name'])?>" size="30"></td>
			</tr>
			<tr>
				<td><?echo Loc::getMessage("ESOL_IX_EL_ADMIN_DESC")?></td>
				<td><input type="text" name="<?echo $sf;?>[find_el_intext]" value="<?echo htmlspecialcharsex($arFields['find_el_intext'])?>" size="30"></td>
			</tr>

			<tr>
				<td><?=Loc::getMessage("ESOL_IX_EL_A_CODE")?>:</td>
				<td><input type="text" name="<?echo $sf;?>[find_el_code]" value="<?echo htmlspecialcharsex($arFields['find_el_code'])?>" size="30"></td>
			</tr>
			<tr>
				<td><?=Loc::getMessage("ESOL_IX_EL_A_EXTERNAL_ID")?>:</td>
				<td><input type="text" name="<?echo $sf;?>[find_el_external_id]" value="<?echo htmlspecialcharsex($arFields['find_el_external_id'])?>" size="30"></td>
			</tr>
			<tr>
				<td><?=Loc::getMessage("ESOL_IX_EL_A_PREVIEW_PICTURE")?>:</td>
				<td>
					<select name="<?echo $sf;?>[find_el_preview_picture]">
						<option value=""><?=htmlspecialcharsex(Loc::getMessage('ESOL_IX_VALUE_ANY'))?></option>
						<option value="Y"<?if($arFields['find_el_preview_picture']=="Y")echo " selected"?>><?=htmlspecialcharsex(Loc::getMessage("ESOL_IX_IS_NOT_EMPTY"))?></option>
						<option value="N"<?if($arFields['find_el_preview_picture']=="N")echo " selected"?>><?=htmlspecialcharsex(Loc::getMessage("ESOL_IX_IS_EMPTY"))?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td><?=Loc::getMessage("ESOL_IX_EL_A_DETAIL_PICTURE")?>:</td>
				<td>
					<select name="<?echo $sf;?>[find_el_detail_picture]">
						<option value=""><?=htmlspecialcharsex(Loc::getMessage('ESOL_IX_VALUE_ANY'))?></option>
						<option value="Y"<?if($arFields['find_el_detail_picture']=="Y")echo " selected"?>><?=htmlspecialcharsex(Loc::getMessage("ESOL_IX_IS_NOT_EMPTY"))?></option>
						<option value="N"<?if($arFields['find_el_detail_picture']=="N")echo " selected"?>><?=htmlspecialcharsex(Loc::getMessage("ESOL_IX_IS_EMPTY"))?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td><?=Loc::getMessage("ESOL_IX_EL_A_TAGS")?>:</td>
				<td>
					<input type="text" name="<?echo $sf;?>[find_el_tags]" value="<?echo htmlspecialcharsex($arFields['find_el_tags'])?>" size="30">
				</td>
			</tr>
			<?
			if ($bCatalog)
			{
				if(is_array($productTypeList))
				{
				?><tr>
					<td><?=Loc::getMessage("ESOL_IX_CATALOG_TYPE"); ?>:</td>
					<td>
						<select name="<?echo $sf;?>[find_el_catalog_type][]" multiple>
							<option value=""><?=htmlspecialcharsex(Loc::getMessage('ESOL_IX_VALUE_ANY'))?></option>
							<?
							$catalogTypes = (!empty($arFields['find_el_catalog_type']) ? $arFields['find_el_catalog_type'] : array());
							foreach ($productTypeList as $productType => $productTypeName)
							{
								?>
								<option value="<? echo $productType; ?>"<? echo (in_array($productType, $catalogTypes) ? ' selected' : ''); ?>><? echo htmlspecialcharsex($productTypeName); ?></option><?
							}
							unset($productType, $productTypeName, $catalogTypes);
							?>
						</select>
					</td>
				</tr>
				<?
				}
				?>
				<tr>
					<td><?echo Loc::getMessage("ESOL_IX_CATALOG_BUNDLE")?>:</td>
					<td>
						<select name="<?echo $sf;?>[find_el_catalog_bundle]">
							<option value=""><?=htmlspecialcharsex(Loc::getMessage('ESOL_IX_VALUE_ANY'))?></option>
							<option value="Y"<?if($arFields['find_el_catalog_bundle']=="Y")echo " selected"?>><?=htmlspecialcharsex(Loc::getMessage("ESOL_IX_YES"))?></option>
							<option value="N"<?if($arFields['find_el_catalog_bundle']=="N")echo " selected"?>><?=htmlspecialcharsex(Loc::getMessage("ESOL_IX_NO"))?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td><?echo Loc::getMessage("ESOL_IX_CATALOG_AVAILABLE")?>:</td>
					<td>
						<select name="<?echo $sf;?>[find_el_catalog_available]">
							<option value=""><?=htmlspecialcharsex(Loc::getMessage('ESOL_IX_VALUE_ANY'))?></option>
							<option value="Y"<?if($arFields['find_el_catalog_available']=="Y")echo " selected"?>><?=htmlspecialcharsex(Loc::getMessage("ESOL_IX_YES"))?></option>
							<option value="N"<?if($arFields['find_el_catalog_available']=="N")echo " selected"?>><?=htmlspecialcharsex(Loc::getMessage("ESOL_IX_NO"))?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td><?echo Loc::getMessage("ESOL_IX_CATALOG_QUANTITY")?>:</td>
					<td>
						<select name="<?echo $sf;?>[find_el_catalog_quantity_comp]">
							<option value="eq" <?if($arFields['find_el_catalog_quantity_comp']=='eq'){echo 'selected';}?>><?=Loc::getMessage('ESOL_IX_COMPARE_EQ')?></option>
							<option value="gt" <?if($arFields['find_el_catalog_quantity_comp']=='gt'){echo 'selected';}?>><?=Loc::getMessage('ESOL_IX_COMPARE_GT')?></option>
							<option value="geq" <?if($arFields['find_el_catalog_quantity_comp']=='geq'){echo 'selected';}?>><?=Loc::getMessage('ESOL_IX_COMPARE_GEQ')?></option>
							<option value="lt" <?if($arFields['find_el_catalog_quantity_comp']=='lt'){echo 'selected';}?>><?=Loc::getMessage('ESOL_IX_COMPARE_LT')?></option>
							<option value="leq" <?if($arFields['find_el_catalog_quantity_comp']=='leq'){echo 'selected';}?>><?=Loc::getMessage('ESOL_IX_COMPARE_LEQ')?></option>
						</select>
						<input type="text" name="<?echo $sf;?>[find_el_catalog_quantity]" value="<?echo htmlspecialcharsex($arFields['find_el_catalog_quantity'])?>" size="10">
					</td>
				</tr>
				
				<?
				if(is_array($arStores))
				{
					foreach($arStores as $arStore)
					{
						?>
						<tr>
							<td><?echo sprintf(Loc::getMessage("ESOL_IX_CATALOG_STORE_QUANTITY"), $arStore['TITLE'])?>:</td>
							<td>
								<select name="<?echo $sf;?>[find_el_catalog_store<?echo $arStore['ID'];?>_quantity_comp]">
									<option value="eq" <?if($arFields['find_el_catalog_store'.$arStore['ID'].'_quantity_comp']=='eq'){echo 'selected';}?>><?=Loc::getMessage('ESOL_IX_COMPARE_EQ')?></option>
									<option value="gt" <?if($arFields['find_el_catalog_store'.$arStore['ID'].'_quantity_comp']=='gt'){echo 'selected';}?>><?=Loc::getMessage('ESOL_IX_COMPARE_GT')?></option>
									<option value="geq" <?if($arFields['find_el_catalog_store'.$arStore['ID'].'_quantity_comp']=='geq'){echo 'selected';}?>><?=Loc::getMessage('ESOL_IX_COMPARE_GEQ')?></option>
									<option value="lt" <?if($arFields['find_el_catalog_store'.$arStore['ID'].'_quantity_comp']=='lt'){echo 'selected';}?>><?=Loc::getMessage('ESOL_IX_COMPARE_LT')?></option>
									<option value="leq" <?if($arFields['find_el_catalog_store'.$arStore['ID'].'_quantity_comp']=='leq'){echo 'selected';}?>><?=Loc::getMessage('ESOL_IX_COMPARE_LEQ')?></option>
								</select>
								<input type="text" name="<?echo $sf;?>[find_el_catalog_store<?echo $arStore['ID'];?>_quantity]" value="<?echo htmlspecialcharsex($arFields['find_el_catalog_store'.$arStore['ID'].'_quantity'])?>" size="10">
							</td>
						</tr>
						<?
					}
				}
				
				if(is_array($arPrices))
				{
					foreach($arPrices as $arPrice)
					{
						?>
						<tr>
							<td><?echo sprintf(Loc::getMessage("ESOL_IX_CATALOG_PRICE"), $arPrice['NAME_LANG'])?>:</td>
							<td>
								<select name="<?echo $sf;?>[find_el_catalog_price_<?echo $arPrice['ID'];?>_comp]">
									<option value="eq" <?if($arFields['find_el_catalog_price_'.$arPrice['ID'].'_comp']=='eq'){echo 'selected';}?>><?=Loc::getMessage('ESOL_IX_COMPARE_EQ')?></option>
									<option value="empty" <?if($arFields['find_el_catalog_price_'.$arPrice['ID'].'_comp']=='empty'){echo 'selected';}?>><?=Loc::getMessage('ESOL_IX_COMPARE_EMPTY')?></option>
									<option value="gt" <?if($arFields['find_el_catalog_price_'.$arPrice['ID'].'_comp']=='gt'){echo 'selected';}?>><?=Loc::getMessage('ESOL_IX_COMPARE_GT')?></option>
									<option value="geq" <?if($arFields['find_el_catalog_price_'.$arPrice['ID'].'_comp']=='geq'){echo 'selected';}?>><?=Loc::getMessage('ESOL_IX_COMPARE_GEQ')?></option>
									<option value="lt" <?if($arFields['find_el_catalog_price_'.$arPrice['ID'].'_comp']=='lt'){echo 'selected';}?>><?=Loc::getMessage('ESOL_IX_COMPARE_LT')?></option>
									<option value="leq" <?if($arFields['find_el_catalog_price_'.$arPrice['ID'].'_comp']=='leq'){echo 'selected';}?>><?=Loc::getMessage('ESOL_IX_COMPARE_LEQ')?></option>
								</select>
								<input type="text" name="<?echo $sf;?>[find_el_catalog_price_<?echo $arPrice['ID'];?>]" value="<?echo htmlspecialcharsex($arFields['find_el_catalog_price_'.$arPrice['ID']])?>" size="10">
							</td>
						</tr>
						<?
					}
				}
			}
			
		foreach($arProps as $arProp):
			if($arProp["FILTRABLE"]=="Y" && $arProp["PROPERTY_TYPE"]!="F"):
		?>
		<tr>
			<td><?=$arProp["NAME"]?>:</td>
			<td>
				<?if(array_key_exists("GetAdminFilterHTML", $arProp["PROPERTY_USER_TYPE"])):
					$fieldName = "filter1_find_el_property_".$arProp["ID"];
					if(isset($arFields["find_el_property_".$arProp["ID"]."_from"])) $GLOBALS[$fieldName."_from"] = $arFields["find_el_property_".$arProp["ID"]."_from"];
					if(isset($arFields["find_el_property_".$arProp["ID"]."_to"])) $GLOBALS[$fieldName."_to"] = $arFields["find_el_property_".$arProp["ID"]."_to"];
					$GLOBALS[$fieldName] = $arFields["find_el_property_".$arProp["ID"]];
					$GLOBALS['set_filter'] = 'Y';
					echo call_user_func_array($arProp["PROPERTY_USER_TYPE"]["GetAdminFilterHTML"], array(
						$arProp,
						array(
							"VALUE" => $fieldName,
							"TABLE_ID" => $sTableID,
						),
					));
				elseif($arProp["PROPERTY_TYPE"]=='S'):?>
					<select class="esol-ix-filter-chval" name="<?echo $sf;?>[find_el_vtype_property_<?=$arProp["ID"]?>]"><option value=""><?echo Loc::getMessage("ESOL_IX_IS_VALUE")?></option><option value="empty"<?if($arFields["find_el_vtype_property_".$arProp["ID"]]=='empty'){echo ' selected';}?>><?echo Loc::getMessage("ESOL_IX_IS_EMPTY")?></option><option value="not_empty"<?if($arFields["find_el_vtype_property_".$arProp["ID"]]=='not_empty'){echo ' selected';}?>><?echo Loc::getMessage("ESOL_IX_IS_NOT_EMPTY")?></option></select><input type="text" name="<?echo $sf;?>[find_el_property_<?=$arProp["ID"]?>]" value="<?echo htmlspecialcharsex($arFields["find_el_property_".$arProp["ID"]])?>" size="30">
				<?elseif($arProp["PROPERTY_TYPE"]=='N' || $arProp["PROPERTY_TYPE"]=='E'):?>
					<select class="esol-ix-filter-chval" name="<?echo $sf;?>[find_el_vtype_property_<?=$arProp["ID"]?>]"><option value=""><?echo Loc::getMessage("ESOL_IX_IS_VALUE")?></option><option value="empty"<?if($arFields["find_el_vtype_property_".$arProp["ID"]]=='empty'){echo ' selected';}?>><?echo Loc::getMessage("ESOL_IX_IS_EMPTY")?></option><option value="not_empty"<?if($arFields["find_el_vtype_property_".$arProp["ID"]]=='not_empty'){echo ' selected';}?>><?echo Loc::getMessage("ESOL_IX_IS_NOT_EMPTY")?></option></select><input type="text" name="<?echo $sf;?>[find_el_property_<?=$arProp["ID"]?>]" value="<?echo htmlspecialcharsex($arFields["find_el_property_".$arProp["ID"]])?>" size="30">
				<?elseif($arProp["PROPERTY_TYPE"]=='L'):?>
					<?
					$propVal = $arFields["find_el_property_".$arProp["ID"]];
					if(!is_array($propVal)) $propVal = array($propVal);
					?>
					<select name="<?echo $sf;?>[find_el_property_<?=$arProp["ID"]?>][]" multiple size="5">
						<option value=""><?echo Loc::getMessage("ESOL_IX_VALUE_ANY")?></option>
						<option value="NOT_REF"<?if(in_array("NOT_REF", $propVal))echo " selected"?>><?echo Loc::getMessage("ESOL_IX_ELEMENT_EDIT_NOT_SET")?></option><?
						$dbrPEnum = \CIBlockPropertyEnum::GetList(Array("SORT"=>"ASC", "NAME"=>"ASC"), Array("PROPERTY_ID"=>$arProp["ID"]));
						while($arPEnum = $dbrPEnum->GetNext()):
						?>
							<option value="<?=$arPEnum["ID"]?>"<?if(in_array($arPEnum["ID"], $propVal))echo " selected"?>><?=$arPEnum["VALUE"]?></option>
						<?
						endwhile;
				?></select>
				<?
				elseif($arProp["PROPERTY_TYPE"]=='G'):
					echo self::ShowGroupPropertyField2($sf.'[find_el_property_'.$arProp["ID"].']', $arProp, $arFields["find_el_property_".$arProp["ID"]]);
				endif;
				?>
			</td>
		</tr>
		<?
			endif;
		endforeach;

		$oFilter->Buttons();
		/*?><span class="adm-btn-wrap"><input type="submit"  class="adm-btn" name="set_filter" value="<? echo Loc::getMessage("admin_lib_filter_set_butt"); ?>" title="<? echo Loc::getMessage("admin_lib_filter_set_butt_title"); ?>" onClick="return EProfile.ApplyFilter(this);"></span>
		<span class="adm-btn-wrap"><input type="submit"  class="adm-btn" name="del_filter" value="<? echo Loc::getMessage("admin_lib_filter_clear_butt"); ?>" title="<? echo Loc::getMessage("admin_lib_filter_clear_butt_title"); ?>" onClick="return EList.DeleteFilter(this);"></span>
		<?*/
		$oFilter->End();
		
		?>
		<script>var arClearHiddenFields = null;</script>
		<!--</form>-->
		</div>
		<?
	}
	
	public static function ShowGroupPropertyField2($name, $property_fields, $values)
	{
		if(!is_array($values)) $values = Array();

		$res = "";
		$result = "";
		$bWas = false;
		$sections = \CIBlockSection::GetTreeList(Array("IBLOCK_ID"=>$property_fields["LINK_IBLOCK_ID"]), array("ID", "NAME", "DEPTH_LEVEL"));
		while($ar = $sections->GetNext())
		{
			$res .= '<option value="'.$ar["ID"].'"';
			if(in_array($ar["ID"], $values))
			{
				$bWas = true;
				$res .= ' selected';
			}
			$res .= '>'.str_repeat(" . ", $ar["DEPTH_LEVEL"]).$ar["NAME"].'</option>';
		}
		$result .= '<select name="'.$name.'[]" size="'.($property_fields["MULTIPLE"]=="Y" ? "5":"1").'" '.($property_fields["MULTIPLE"]=="Y"?"multiple":"").'>';
		$result .= '<option value=""'.(!$bWas?' selected':'').'>'.Loc::getMessage("IBLOCK_ELEMENT_EDIT_NOT_SET").'</option>';
		$result .= $res;
		$result .= '</select>';
		return $result;
	}
	
	public static function AddFilter(&$arFilter, $arAddFilter)
	{
		$arAddFilter = unserialize(base64_decode($arAddFilter));
		if(!is_array($arFilter) || !is_array($arAddFilter)) return;
		
		$dbrFProps = \CIBlockProperty::GetList(array(), array("IBLOCK_ID"=>$arFilter['IBLOCK_ID'],"CHECK_PERMISSIONS"=>"N"));
		$arProps = array();
		while ($arProp = $dbrFProps->GetNext())
		{
			if ($arProp["ACTIVE"] == "Y")
			{
				$arProp["PROPERTY_USER_TYPE"] = ('' != $arProp["USER_TYPE"] ? \CIBlockProperty::GetUserType($arProp["USER_TYPE"]) : array());
				$arProps[] = $arProp;
			}
		}
		
		if(is_array($arAddFilter['find_section_section']))
		{
			if(count(array_diff($arAddFilter['find_section_section'], array('', '0' ,'-1'))) > 0)
			{
				$arFilter['SECTION_ID'] = array_diff($arAddFilter['find_section_section'], array('', '0' ,'-1'));
			}
			elseif(in_array('-1', $arAddFilter['find_section_section']))
			{
				unset($arFilter["SECTION_ID"]);
			}
		}
		elseif(strlen($arAddFilter['find_section_section']) > 0 && (int)$arAddFilter['find_section_section'] >= 0) 
			$arFilter['SECTION_ID'] = $arAddFilter['find_section_section'];
		if($arAddFilter['find_el_subsections']=='Y')
		{
			if($arFilter['SECTION_ID']==0) unset($arFilter["SECTION_ID"]);
			else $arFilter["INCLUDE_SUBSECTIONS"] = "Y";
		}
		if(strlen($arAddFilter['find_el_modified_user_id']) > 0) $arFilter['MODIFIED_USER_ID'] = $arAddFilter['find_el_modified_user_id'];
		if(strlen($arAddFilter['find_el_modified_by']) > 0) $arFilter['MODIFIED_BY'] = $arAddFilter['find_el_modified_by'];
		if(strlen($arAddFilter['find_el_created_user_id']) > 0) $arFilter['CREATED_USER_ID'] = $arAddFilter['find_el_created_user_id'];
		if(strlen($arAddFilter['find_el_active']) > 0) $arFilter['ACTIVE'] = $arAddFilter['find_el_active'];
		if(strlen($arAddFilter['find_el_code']) > 0) $arFilter['?CODE'] = $arAddFilter['find_el_code'];
		if(strlen($arAddFilter['find_el_external_id']) > 0) $arFilter['EXTERNAL_ID'] = $arAddFilter['find_el_external_id'];
		if(strlen($arAddFilter['find_el_tags']) > 0) $arFilter['?TAGS'] = $arAddFilter['find_el_tags'];
		if(strlen($arAddFilter['find_el_name']) > 0) $arFilter['?NAME'] = $arAddFilter['find_el_name'];
		if(strlen($arAddFilter['find_el_intext']) > 0) $arFilter['?DETAIL_TEXT'] = $arAddFilter['find_el_intext'];
		if($arAddFilter['find_el_preview_picture']=='Y') $arFilter['!PREVIEW_PICTURE'] =  false;
		elseif($arAddFilter['find_el_preview_picture']=='N') $arFilter['PREVIEW_PICTURE'] =  false;
		if($arAddFilter['find_el_detail_picture']=='Y') $arFilter['!DETAIL_PICTURE'] =  false;
		elseif($arAddFilter['find_el_detail_picture']=='N') $arFilter['DETAIL_PICTURE'] =  false;
		
		if(!empty($arAddFilter['find_el_id_start'])) $arFilter[">=ID"] = $arAddFilter['find_el_id_start'];
		if(!empty($arAddFilter['find_el_id_end'])) $arFilter["<=ID"] = $arAddFilter['find_el_id_end'];
		if(!empty($arAddFilter['find_el_timestamp_from'])) $arFilter["DATE_MODIFY_FROM"] = $arAddFilter['find_el_timestamp_from'];
		if(!empty($arAddFilter['find_el_timestamp_to'])) $arFilter["DATE_MODIFY_TO"] = \CIBlock::isShortDate($arAddFilter['find_el_timestamp_to'])? ConvertTimeStamp(AddTime(MakeTimeStamp($arAddFilter['find_el_timestamp_to']), 1, "D"), "FULL"): $arAddFilter['find_el_timestamp_to'];
		if(!empty($arAddFilter['find_el_created_from'])) $arFilter[">=DATE_CREATE"] = $arAddFilter['find_el_created_from'];
		if(!empty($arAddFilter['find_el_created_to'])) $arFilter["<=DATE_CREATE"] = \CIBlock::isShortDate($arAddFilter['find_el_created_to'])? ConvertTimeStamp(AddTime(MakeTimeStamp($arAddFilter['find_el_created_to']), 1, "D"), "FULL"): $arAddFilter['find_el_created_to'];
		if(!empty($arAddFilter['find_el_created_by']) && strlen($arAddFilter['find_el_created_by'])>0) $arFilter["CREATED_BY"] = $arAddFilter['find_el_created_by'];
		if(!empty($arAddFilter['find_el_date_active_from_from'])) $arFilter[">=DATE_ACTIVE_FROM"] = $arAddFilter['find_el_date_active_from_from'];
		if(!empty($arAddFilter['find_el_date_active_from_to'])) $arFilter["<=DATE_ACTIVE_FROM"] = $arAddFilter['find_el_date_active_from_to'];
		if(!empty($arAddFilter['find_el_date_active_to_from'])) $arFilter[">=DATE_ACTIVE_TO"] = $arAddFilter['find_el_date_active_to_from'];
		if(!empty($arAddFilter['find_el_date_active_to_to'])) $arFilter["<=DATE_ACTIVE_TO"] = $arAddFilter['find_el_date_active_to_to'];
		if (!empty($arAddFilter['find_el_catalog_type'])) $arFilter['CATALOG_TYPE'] = $arAddFilter['find_el_catalog_type'];
		if (!empty($arAddFilter['find_el_catalog_available'])) $arFilter['CATALOG_AVAILABLE'] = $arAddFilter['find_el_catalog_available'];
		if (!empty($arAddFilter['find_el_catalog_bundle'])) $arFilter['CATALOG_BUNDLE'] = $arAddFilter['find_el_catalog_bundle'];
		if (strlen($arAddFilter['find_el_catalog_quantity']) > 0)
		{
			$op = static::GetNumberOperation($arAddFilter['find_el_catalog_quantity'], $arAddFilter['find_el_catalog_quantity_comp']);
			$arFilter[$op.'CATALOG_QUANTITY'] = $arAddFilter['find_el_catalog_quantity'];
		}
		
		$arStoreKeys = preg_grep('/^find_el_catalog_store\d+_/', array_keys($arAddFilter));
		$arStoreKeys = array_unique(array_map(create_function('$n', 'return preg_replace("/^find_el_catalog_store(\d+)_.*$/", "$1", $n);'), $arStoreKeys));
		if(!empty($arStoreKeys))
		{
			foreach($arStoreKeys as $storeKey)
			{
				if(strlen($arAddFilter['find_el_catalog_store'.$storeKey.'_quantity']) > 0)
				{
					$op = static::GetNumberOperation($arAddFilter['find_el_catalog_store'.$storeKey.'_quantity'], $arAddFilter['find_el_catalog_store'.$storeKey.'_quantity_comp']);
					$arFilter[$op.'CATALOG_STORE_AMOUNT_'.$storeKey] = $arAddFilter['find_el_catalog_store'.$storeKey.'_quantity'];
				}
			}
		}
		
		$arPriceKeys = preg_grep('/^find_el_catalog_price_\d+$/', array_keys($arAddFilter));
		$arPriceKeys = array_unique(array_map(create_function('$n', 'return preg_replace("/^find_el_catalog_price_(\d+)$/", "$1", $n);'), $arPriceKeys));
		if(!empty($arPriceKeys))
		{
			foreach($arPriceKeys as $priceKey)
			{
				if(strlen($arAddFilter['find_el_catalog_price_'.$priceKey]) > 0
					|| $arAddFilter['find_el_catalog_price_'.$priceKey.'_comp']=='empty')
				{
					$op = static::GetNumberOperation($arAddFilter['find_el_catalog_price_'.$priceKey], $arAddFilter['find_el_catalog_price_'.$priceKey.'_comp']);
					$arFilter[$op.'CATALOG_PRICE_'.$priceKey] = $arAddFilter['find_el_catalog_price_'.$priceKey];
				}
			}
		}
		
		foreach ($arProps as $arProp)
		{
			if ('Y' == $arProp["FILTRABLE"] && 'F' != $arProp["PROPERTY_TYPE"])
			{
				if (!empty($arProp['PROPERTY_USER_TYPE']) && isset($arProp["PROPERTY_USER_TYPE"]["AddFilterFields"]))
				{
					$fieldName = "filter_".$listIndex."_find_el_property_".$arProp["ID"];
					$GLOBALS[$fieldName] = $arAddFilter["find_el_property_".$arProp["ID"]];
					$GLOBALS['set_filter'] = 'Y';
					call_user_func_array($arProp["PROPERTY_USER_TYPE"]["AddFilterFields"], array(
						$arProp,
						array("VALUE" => $fieldName),
						&$arFilter,
						&$filtered,
					));
				}
				else
				{
					$value = $arAddFilter["find_el_property_".$arProp["ID"]];
					$vtype = $arAddFilter["find_el_vtype_property_".$arProp["ID"]];
					if(is_array($value)) $value = array_diff(array_map('trim', $value), array(''));
					if(strlen($vtype) > 0)
					{
						if($vtype=='empty') $arFilter["PROPERTY_".$arProp["ID"]] = false;
						elseif($vtype=='not_empty') $arFilter["!PROPERTY_".$arProp["ID"]] = false;
					}
					elseif((is_array($value) && count($value)>0) || (!is_array($value) && strlen($value)))
					{
						if(is_array($value))
						{
							foreach($value as $k=>$v)
							{
								if($v === "NOT_REF") $value[$k] = false;
							}
						}
						elseif($value === "NOT_REF") $value = false;
						if($arProp["PROPERTY_TYPE"]=='E' && $arProp["USER_TYPE"]=='')
						{
							$value = trim($value);
							if(preg_match('/[,;\s\|]/', $value)) $arFilter["PROPERTY_".$arProp["ID"]] = array_diff(array_map('trim', preg_split('/[,;\s\|]/', $value)), array(''));
							else $arFilter["=PROPERTY_".$arProp["ID"]] = $value;
						}
						else
						{
							$arFilter["=PROPERTY_".$arProp["ID"]] = $value;
						}
					}
				}
			}
		}
	}
	
	public static function GetNumberOperation(&$val, $op)
	{
		if($op=='eq') return '=';
		elseif($op=='gt') return '>';
		elseif($op=='geq') return '>=';
		elseif($op=='lt') return '<';
		elseif($op=='leq') return '<=';
		elseif($op=='empty')
		{
			$val = false;
			return '';
		}
		else return '';
	}
	
	public static function RemoveTmpFiles($maxTime = 5, $suffix='')
	{
		$oProfile = \Bitrix\EsolImportxml\Profile::getInstance($suffix);
		$timeBegin = time();
		$docRoot = $_SERVER["DOCUMENT_ROOT"];
		$tmpDir = $docRoot.'/upload/tmp/'.static::$moduleId.'/';
		$arOldDirs = array();
		$arActDirs = array();
		if(file_exists($tmpDir) && ($dh = opendir($tmpDir))) 
		{
			while(($file = readdir($dh)) !== false) 
			{
				if(in_array($file, array('.', '..'))) continue;
				if(is_dir($tmpDir.$file))
				{
					if(!in_array($file, $arActDirs) && (time() - filemtime($tmpDir.$file) > 24*60*60))
					{
						$arOldDirs[] = $file;
					}
				}
				elseif(substr($file, -4)=='.txt')
				{
					$arParams = $oProfile->GetProfileParamsByFile($tmpDir.$file);
					if(is_array($arParams) && isset($arParams['tmpdir']))
					{
						$actDir = preg_replace('/^.*\/([^\/]+)$/', '$1', trim($arParams['tmpdir'], '/'));
						$arActDirs[] = $actDir;
					}
				}
			}
			$arOldDirs = array_diff($arOldDirs, $arActDirs);
			foreach($arOldDirs as $subdir)
			{
				$oldDir = substr($tmpDir, strlen($docRoot)).$subdir;
				DeleteDirFilesEx($oldDir);
				if(($maxTime > 0) && (time() - $timeBegin >= $maxTime)) return;
			}
			closedir($dh);
		}
		
		$tmpDir = $docRoot.'/upload/tmp/';
		if(file_exists($tmpDir) && ($dh = opendir($tmpDir))) 
		{
			while(($file = readdir($dh)) !== false) 
			{
				if(!preg_match('/^[0-9a-f]{3}$/', $file)) continue;
				$subdir = $tmpDir.$file;
				if(is_dir($subdir))
				{
					$subdir .= '/';
					if(time() - filemtime($subdir) > 24*60*60)
					{
						if($dh2 = opendir($subdir))
						{
							$emptyDir = true;
							while(($file2 = readdir($dh2)) !== false)
							{
								if(in_array($file2, array('.', '..'))) continue;
								if(time() - filemtime($subdir) > 24*60*60)
								{
									if(is_dir($subdir.$file2))
									{
										$oldDir = substr($subdir.$file2, strlen($docRoot));
										DeleteDirFilesEx($oldDir);
									}
									else
									{
										unlink($subdir.$file2);
									}
								}
								else
								{
									$emptyDir = false;
								}
							}
							closedir($dh2);
							if($emptyDir)
							{
								unlink($subdir);
							}
						}
						
						if(($maxTime > 0) && (time() - $timeBegin >= $maxTime)) return;
					}
				}
			}
			closedir($dh);
		}
	}
	
	public static function GetXmlEncoding($fn)
	{
		$encoding = 'utf-8';
		$handle = fopen($fn, "r");
		while(!($str = trim(fgets($handle, 4096))) && (!feof($handle))) {}
		if(preg_match('/<\?xml[^>]*encoding\s*=\s*[\'"]([^\'"]*)[\'"]/Uis', $str, $m))
		{
			$encoding = ToLower($m[1]);
		}
		else
		{
			fseek($handle, 0);
			$contents = fread($handle, 262144);
			if(!\CUtil::DetectUTF8($contents) && (!function_exists('iconv') || iconv('CP1251', 'CP1251', $contents)==$contents))
			{
				$encoding = 'windows-1251';
			}
		}
		fclose($handle);
		if($encoding=='cp1251') $encoding = 'windows-1251';
		//if($encoding=='utf8') $encoding = 'utf-8';
		if($encoding != 'windows-1251') $encoding = 'utf-8';
		return $encoding;
	}
	
	public static function ConvertDataEncoding($val, $fileEncoding, $siteEncoding)
	{
		if($siteEncoding==$fileEncoding) return $val;
		$val = \Bitrix\EsolImportxml\Utils::ReplaceCpSpecChars($val, $siteEncoding);
		$val = \Bitrix\Main\Text\Encoding::convertEncodingArray($val, $fileEncoding, $siteEncoding);
		return $val;
	}
	
	public static function ReplaceCpSpecChars($val, $toEncoding)
	{
		if(!in_array($toEncoding, array('windows-1251', 'cp1251'))) return $val;
		$specChars = array('Ø'=>'&#216;', '™'=>'&#153;', '®'=>'&#174;', '©'=>'&#169;', 'Ö'=>'&#214;');
		if(!isset(static::$cpSpecCharLetters))
		{
			$cpSpecCharLetters = array();
			foreach($specChars as $char=>$code)
			{
				$letter = false;
				$pos = 0;
				for($i=192; $i<255; $i++)
				{
					$tmpLetter = \Bitrix\Main\Text\Encoding::convertEncoding(chr($i), 'CP1251', 'UTF-8');
					$tmpPos = strpos($tmpLetter, $char);
					if($tmpPos!==false)
					{
						$letter = $tmpLetter;
						$pos = $tmpPos;
					}
				}
				$cpSpecCharLetters[$char] = array('letter'=>$letter, 'pos'=>$pos);
			}
			static::$cpSpecCharLetters = $cpSpecCharLetters;
		}
		
		foreach($specChars as $char=>$code)
		{
			if(strpos($val, $char)===false) continue;
			$letter = static::$cpSpecCharLetters[$char]['letter'];
			$pos = static::$cpSpecCharLetters[$char]['pos'];

			if($letter!==false)
			{
				if($pos==0) $val = preg_replace('/'.substr($letter, 0, 1).'(?!'.substr($letter, 1, 1).')/', $code, $val);
				elseif($pos==1) $val = preg_replace('/(?<!'.substr($letter, 0, 1).')'.substr($letter, 1, 1).'/', $code, $val);
			}
			else
			{
				$val = str_replace($char, $code, $val);
			}
		}
		return $val;
	}
	
	public static function getSiteEncoding()
	{
		if(!isset(static::$siteEncoding))
		{
			if (defined('BX_UTF'))
				$logicalEncoding = "utf-8";
			elseif (defined("SITE_CHARSET") && (strlen(SITE_CHARSET) > 0))
				$logicalEncoding = SITE_CHARSET;
			elseif (defined("LANG_CHARSET") && (strlen(LANG_CHARSET) > 0))
				$logicalEncoding = LANG_CHARSET;
			elseif (defined("BX_DEFAULT_CHARSET"))
				$logicalEncoding = BX_DEFAULT_CHARSET;
			else
				$logicalEncoding = "windows-1251";

			static::$siteEncoding = strtolower($logicalEncoding);
		}
		return static::$siteEncoding;
	}
	
	public function GetUserAgent()
	{
		if(empty(self::$arAgents))
		{
			self::$arAgents = array(
				'Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:62.0) Gecko/20100101 Firefox/62.0',
				'Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:61.0) Gecko/20100101 Firefox/61.0',
				'Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0',
				'Mozilla/5.0 (Windows NT 6.2; Win64; x64; rv:56.0) Gecko/20100101 Firefox/56.0',
				'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:46.0) Gecko/20100101 Firefox/46.0',
			);
			self::$countAgents = count(self::$arAgents);
		}
		return self::$arAgents[rand(0, self::$countAgents - 1)];
	}
}
?>
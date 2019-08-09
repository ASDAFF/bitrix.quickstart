<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class CKDAExportUtils {
	protected static $moduleId = 'kda.exportexcel';
	protected static $moduleSubDir = '';
	protected static $currencyRates = null;
	protected static $zipArchiveOption = 'ZIPARCHIVE_WRITE_MODE';
	
	public static function GetOfferIblock($IBLOCK_ID, $retarray=false)
	{
		if(!$IBLOCK_ID || !Loader::includeModule('catalog')) return false;
		$dbRes = CCatalog::GetList(array(), array('IBLOCK_ID'=>$IBLOCK_ID));
		$arFields = $dbRes->Fetch();
		if(!$arFields['OFFERS_IBLOCK_ID'])
		{
			$dbRes = CCatalog::GetList(array(), array('PRODUCT_IBLOCK_ID'=>$IBLOCK_ID));
			if($arFields2 = $dbRes->Fetch())
			{
				$arFields = array_merge($arFields2, array(
					'IBLOCK_ID' => $arFields2['PRODUCT_IBLOCK_ID'],
					'YANDEX_EXPORT' => $arFields2['YANDEX_EXPORT'],
					'SUBSCRIPTION' => $arFields2['SUBSCRIPTION'],
					'VAT_ID' => $arFields2['VAT_ID'],
					'PRODUCT_IBLOCK_ID' => 0,
					'SKU_PROPERTY_ID' => 0,
					'OFFERS_PROPERTY_ID' => $arFields2['SKU_PROPERTY_ID'],
					'OFFERS_IBLOCK_ID' => $arFields2['IBLOCK_ID'],
					'ID' => $arFields2['PRODUCT_IBLOCK_ID'],
				));
			}
		}
		if(!$arFields['OFFERS_IBLOCK_ID'])
		{
			$arFields = array();
			foreach(GetModuleEvents(static::$moduleId, "OnGetOfferIblock", true) as $arEvent)
			{
				ExecuteModuleEventEx($arEvent, array($arFields, $IBLOCK_ID));
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
		$strFileName = GetFileName($arFile["name"]);	/* filename.gif */

		if(isset($arFile["del"]) && $arFile["del"] <> '')
		{
			CFile::DoDelete($arFile["old_file"]);
			if($strFileName == '')
				return "NULL";
		}

		if($arFile["name"] == '')
		{
			if(isset($arFile["description"]) && intval($arFile["old_file"])>0)
			{
				CFile::UpdateDesc($arFile["old_file"], $arFile["description"]);
			}
			return false;
		}

		if (isset($arFile["content"]))
		{
			if (!isset($arFile["size"]))
			{
				$arFile["size"] = CUtil::BinStrlen($arFile["content"]);
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
			$upload_dir = COption::GetOptionString("main", "upload_dir", "upload");
			$io = CBXVirtualIo::GetInstance();
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
				CFile::DoDelete($arFile["old_file"]);
				return false;
			}

			if(isset($arFile["old_file"]))
				CFile::DoDelete($arFile["old_file"]);

			@chmod($strPhysicalFileNameX, BX_FILE_PERMISSIONS);

			//flash is not an image
			$flashEnabled = !CFile::IsImage($arFile["ORIGINAL_NAME"], $arFile["type"]);

			$imgArray = CFile::GetImageSize($strDbFileNameX, false, $flashEnabled);

			if(is_array($imgArray))
			{
				$arFile["WIDTH"] = $imgArray[0];
				$arFile["HEIGHT"] = $imgArray[1];

				if($imgArray[2] == IMAGETYPE_JPEG)
				{
					$exifData = CFile::ExtractImageExif($io->GetPhysicalName($strDbFileNameX));
					if ($exifData  && isset($exifData['Orientation']))
					{
						//swap width and height
						if ($exifData['Orientation'] >= 5 && $exifData['Orientation'] <= 8)
						{
							$arFile["WIDTH"] = $imgArray[1];
							$arFile["HEIGHT"] = $imgArray[0];
						}

						$properlyOriented = CFile::ImageHandleOrientation($exifData['Orientation'], $io->GetPhysicalName($strDbFileNameX));
						if ($properlyOriented)
						{
							$jpgQuality = intval(COption::GetOptionString('main', 'image_resize_quality', '95'));
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
		if (COption::GetOptionInt("main", "disk_space") > 0)
		{
			CDiskQuota::updateDiskQuota("file", $arFile["size"], "insert");
		}
		/****************************** QUOTA ******************************/

		$NEW_IMAGE_ID = CFile::DoInsert(array(
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

		CFile::CleanCache($NEW_IMAGE_ID);
		return $NEW_IMAGE_ID;
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
			if(COption::GetOptionString("main", "translit_original_file_name", "N") == "Y")
			{
				$fileName = CUtil::translit($fileName, LANGUAGE_ID, array("max_len"=>1024, "safe_chars"=>".", "replace_space" => '-'));
			}

			//replace invalid characters
			if(COption::GetOptionString("main", "convert_original_file_name", "Y") == "Y")
			{
				$io = CBXVirtualIo::GetInstance();
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

		$io = CBXVirtualIo::GetInstance();
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

		if (COption::GetOptionInt("main", "disk_space") > 0)
		{
			$quota = new CDiskQuota();
			if (!$quota->checkDiskQuota($arFile))
				return Loc::getMessage("FILE_BAD_QUOTA");
		}

		return "";
	}
	
	public static function ShowFilter($sTableID, $listIndex, $SETTINGS, $SETTINGS_DEFAULT)
	{
		global $APPLICATION;
		CJSCore::Init('file_input');
		$IBLOCK_ID = $SETTINGS_DEFAULT['IBLOCK_ID'];
		$changeIblockId = (bool)($SETTINGS['CHANGE_IBLOCK_ID'][$listIndex]=='Y');
		if($changeIblockId && $SETTINGS['LIST_IBLOCK_ID'][$listIndex])
		{
			$IBLOCK_ID = $SETTINGS['LIST_IBLOCK_ID'][$listIndex];
		}
		
		Loader::includeModule('iblock');
		$bCatalog = Loader::includeModule('catalog');
		$bSale = Loader::includeModule('sale');
		if($bCatalog)
		{
			$arCatalog = CCatalog::GetByID($IBLOCK_ID);
			if($arCatalog)
			{
				if(is_callable(array('CCatalogAdminTools', 'getIblockProductTypeList')))
				{
					$productTypeList = CCatalogAdminTools::getIblockProductTypeList($IBLOCK_ID, true);
				}
				
				$arStores = array();
				$dbRes = CCatalogStore::GetList(array("SORT"=>"ID"), array(), false, false, array("ID", "TITLE", "ADDRESS"));
				while($arStore = $dbRes->Fetch())
				{
					if(strlen($arStore['TITLE'])==0 && $arStore['ADDRESS']) $arStore['TITLE'] = $arStore['ADDRESS'];
					$arStores[] = $arStore;
				}
				
				$arPrices = array();
				$dbPriceType = CCatalogGroup::GetList(array("SORT" => "ASC"));
				while($arPriceType = $dbPriceType->Fetch())
				{
					if(strlen($arPriceType["NAME_LANG"])==0 && $arPriceType['NAME']) $arPriceType['NAME_LANG'] = $arPriceType['NAME'];
					$arPrices[] = $arPriceType;
				}
			}
			if(!$arCatalog) $bCatalog = false;
		}
		
		$dbrFProps = CIBlockProperty::GetList(
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
				$arProp["PROPERTY_USER_TYPE"] = ('' != $arProp["USER_TYPE"] ? CIBlockProperty::GetUserType($arProp["USER_TYPE"]) : array());
				$arProps[] = $arProp;
			}
		}
		
		$boolSKU = false;
		$strSKUName = '';
		if($OFFERS_IBLOCK_ID = self::GetOfferIblock($IBLOCK_ID))
		{
			$boolSKU = true;
			$strSKUName = Loc::getMessage('KDA_EE_IBLIST_A_OFFERS');
			
			$dbrFProps = CIBlockProperty::GetList(
				array(
					"SORT"=>"ASC",
					"NAME"=>"ASC"
				),
				array(
					"IBLOCK_ID"=>$OFFERS_IBLOCK_ID,
					"CHECK_PERMISSIONS"=>"N",
				)
			);

			$arSKUProps = array();
			while ($arProp = $dbrFProps->GetNext())
			{
				if ($arProp["ACTIVE"] == "Y")
				{
					$arProp["PROPERTY_USER_TYPE"] = ('' != $arProp["USER_TYPE"] ? CIBlockProperty::GetUserType($arProp["USER_TYPE"]) : array());
					$arSKUProps[] = $arProp;
				}
			}
		}
		
		$arFields = (is_array($SETTINGS['FILTER'][$listIndex]) ? $SETTINGS['FILTER'][$listIndex] : array());
		
		?>
		<!--<form method="GET" name="find_form" id="find_form" action="">-->
		<div class="find_form_inner">
		<?
		$arFindFields = Array();
		$arFindFields["IBEL_A_F_ID"] = Loc::getMessage("KDA_EE_IBEL_A_F_ID");
		$arFindFields["IBEL_A_F_PARENT"] = Loc::getMessage("KDA_EE_IBEL_A_F_PARENT");

		$arFindFields["IBEL_A_F_MODIFIED_WHEN"] = Loc::getMessage("KDA_EE_IBEL_A_F_MODIFIED_WHEN");
		$arFindFields["IBEL_A_F_MODIFIED_BY"] = Loc::getMessage("KDA_EE_IBEL_A_F_MODIFIED_BY");
		$arFindFields["IBEL_A_F_CREATED_WHEN"] = Loc::getMessage("KDA_EE_IBEL_A_F_CREATED_WHEN");
		$arFindFields["IBEL_A_F_CREATED_BY"] = Loc::getMessage("KDA_EE_IBEL_A_F_CREATED_BY");

		$arFindFields["IBEL_A_F_ACTIVE_FROM"] = Loc::getMessage("KDA_EE_IBEL_A_ACTFROM");
		$arFindFields["IBEL_A_F_ACTIVE_TO"] = Loc::getMessage("KDA_EE_IBEL_A_ACTTO");
		$arFindFields["IBEL_A_F_ACT"] = Loc::getMessage("KDA_EE_IBEL_A_F_ACT");
		$arFindFields["IBEL_A_F_SORT"] = Loc::getMessage("KDA_EE_IBEL_A_F_SORT");
		$arFindFields["IBEL_A_F_NAME"] = Loc::getMessage("KDA_EE_IBEL_A_F_NAME");
		$arFindFields["IBEL_A_F_PREDESC"] = Loc::getMessage("KDA_EE_IBEL_A_F_PREDESC");
		$arFindFields["IBEL_A_F_DESC"] = Loc::getMessage("KDA_EE_IBEL_A_F_DESC");
		$arFindFields["IBEL_A_CODE"] = Loc::getMessage("KDA_EE_IBEL_A_CODE");
		$arFindFields["IBEL_A_EXTERNAL_ID"] = Loc::getMessage("KDA_EE_IBEL_A_EXTERNAL_ID");
		$arFindFields["IBEL_A_PREVIEW_PICTURE"] = Loc::getMessage("KDA_EE_IBEL_A_PREVIEW_PICTURE");
		$arFindFields["IBEL_A_DETAIL_PICTURE"] = Loc::getMessage("KDA_EE_IBEL_A_DETAIL_PICTURE");
		$arFindFields["IBEL_A_TAGS"] = Loc::getMessage("KDA_EE_IBEL_A_TAGS");
		
		if ($bCatalog)
		{
			if(is_array($productTypeList)) $arFindFields["CATALOG_TYPE"] = Loc::getMessage("KDA_EE_CATALOG_TYPE");
			$arFindFields["CATALOG_BUNDLE"] = Loc::getMessage("KDA_EE_CATALOG_BUNDLE");
			$arFindFields["CATALOG_AVAILABLE"] = Loc::getMessage("KDA_EE_CATALOG_AVAILABLE");
			$arFindFields["CATALOG_QUANTITY"] = Loc::getMessage("KDA_EE_CATALOG_QUANTITY");
			if(is_array($arStores))
			{
				foreach($arStores as $arStore)
				{
					$arFindFields["CATALOG_STORE".$arStore['ID']."_QUANTITY"] = sprintf(Loc::getMessage("KDA_EE_CATALOG_STORE_QUANTITY"), $arStore['TITLE']);
				}
				if(count($arStores) > 0) $arFindFields["CATALOG_STORE_ANY_QUANTITY"] = Loc::getMessage("KDA_EE_CATALOG_STORE_ANY_QUANTITY");
			}
			$arFindFields["CATALOG_PURCHASING_PRICE"] = Loc::getMessage("KDA_EE_CATALOG_PURCHASING_PRICE");
			if(is_array($arPrices))
			{
				foreach($arPrices as $arPrice)
				{
					$arFindFields["CATALOG_PRICE_".$arPrice['ID']] = sprintf(Loc::getMessage("KDA_EE_CATALOG_PRICE"), $arPrice['NAME_LANG']);
				}
			}
			
			if($bSale)
			{
				$arFindFields["SALE_ORDER"] = Loc::getMessage("KDA_EE_EL_A_SALE_ORDER");
			}
		}

		foreach($arProps as $arProp)
			if($arProp["FILTRABLE"]=="Y" || $arProp["PROPERTY_TYPE"]=="F")
				$arFindFields["IBEL_A_PROP_".$arProp["ID"]] = $arProp["NAME"];

		if($boolSKU)
		{
			$arFindFields["IBEL_A_SUB_F_ID"] = ('' != $strSKUName ? $strSKUName.' - ' : '').Loc::getMessage("KDA_EE_IBEL_A_F_ID");
			$arFindFields["IBEL_A_SUB_F_MODIFIED_WHEN"] = ('' != $strSKUName ? $strSKUName.' - ' : '').Loc::getMessage("KDA_EE_IBEL_A_F_MODIFIED_WHEN");
			$arFindFields["IBEL_A_SUB_F_ACT"] = ('' != $strSKUName ? $strSKUName.' - ' : '').Loc::getMessage("KDA_EE_IBEL_A_F_ACT");
			$arFindFields["IBEL_A_SUB_F_SORT"] = ('' != $strSKUName ? $strSKUName.' - ' : '').Loc::getMessage("KDA_EE_IBEL_A_F_SORT");
			if(1 || $bCatalog)
			{
				$arFindFields["SUB_CATALOG_QUANTITY"] = ('' != $strSKUName ? $strSKUName.' - ' : '').Loc::getMessage("KDA_EE_CATALOG_QUANTITY");
				if(is_array($arStores))
				{
					foreach($arStores as $arStore)
					{
						$arFindFields["SUB_CATALOG_STORE".$arStore['ID']."_QUANTITY"] = ('' != $strSKUName ? $strSKUName.' - ' : '').sprintf(Loc::getMessage("KDA_EE_CATALOG_STORE_QUANTITY"), $arStore['TITLE']);
					}
					if(count($arStores) > 0) $arFindFields["SUB_CATALOG_STORE_ANY_QUANTITY"] = ('' != $strSKUName ? $strSKUName.' - ' : '').Loc::getMessage("KDA_EE_CATALOG_STORE_ANY_QUANTITY");
				}
				$arFindFields["SUB_CATALOG_PURCHASING_PRICE"] = ('' != $strSKUName ? $strSKUName.' - ' : '').Loc::getMessage("KDA_EE_CATALOG_PURCHASING_PRICE");
				if(is_array($arPrices))
				{
					foreach($arPrices as $arPrice)
					{
						$arFindFields["SUB_CATALOG_PRICE_".$arPrice['ID']] = ('' != $strSKUName ? $strSKUName.' - ' : '').sprintf(Loc::getMessage("KDA_EE_CATALOG_PRICE"), $arPrice['NAME_LANG']);
					}
				}
			}
			
			if (isset($arSKUProps) && is_array($arSKUProps))
			{
				foreach($arSKUProps as $arProp)
					if($arProp["FILTRABLE"]=="Y" && $arProp["PROPERTY_TYPE"]!="F")
						$arFindFields["IBEL_A_SUB_PROP_".$arProp["ID"]] = ('' != $strSKUName ? $strSKUName.' - ' : '').$arProp["NAME"];
			}
		}
		
		$oFilter = new CAdminFilter($sTableID."_filter", $arFindFields);
		
		$oFilter->Begin();
		?>
			<tr>
				<td><?echo Loc::getMessage("KDA_EE_FILTER_FROMTO_ID")?>:</td>
				<td nowrap>
					<input type="text" name="SETTINGS[FILTER][<?=$listIndex?>][find_el_id_start]" size="10" value="<?echo htmlspecialcharsex($arFields['find_el_id_start'])?>">
					...
					<input type="text" name="SETTINGS[FILTER][<?=$listIndex?>][find_el_id_end]" size="10" value="<?echo htmlspecialcharsex($arFields['find_el_id_end'])?>">
				</td>
			</tr>

			<tr>
				<td><?echo Loc::getMessage("KDA_EE_FIELD_SECTION_ID")?>:</td>
				<td>
					<select name="SETTINGS[FILTER][<?=$listIndex?>][find_section_section][]" multiple size="5">
						<option value="-1"><?echo Loc::getMessage("KDA_EE_VALUE_ANY")?></option>
						<option value="0"<?if((is_array($arFields['find_section_section']) && in_array("0", $arFields['find_section_section'])) || $arFields['find_section_section']=="0")echo" selected"?>><?echo Loc::getMessage("KDA_EE_UPPER_LEVEL")?></option>
						<?
						$bsections = CIBlockSection::GetTreeList(Array("IBLOCK_ID"=>$IBLOCK_ID), array("ID", "NAME", "DEPTH_LEVEL"));
						while($ar = $bsections->GetNext()):
							?><option value="<?echo $ar["ID"]?>"<?if((is_array($arFields['find_section_section']) && in_array($ar["ID"], $arFields['find_section_section'])) || $ar["ID"]==$arFields['find_section_section'])echo " selected"?>><?echo str_repeat("&nbsp;.&nbsp;", $ar["DEPTH_LEVEL"])?><?echo $ar["NAME"]?></option><?
						endwhile;
						?>
					</select><br>
					<input type="checkbox" name="SETTINGS[FILTER][<?=$listIndex?>][find_el_subsections]" value="Y"<?if($arFields['find_el_subsections']=="Y")echo" checked"?>> <?echo Loc::getMessage("KDA_EE_INCLUDING_SUBSECTIONS")?>
				</td>
			</tr>

			<?
			$GLOBALS["SETTINGS[FILTER][".$listIndex."][find_el_timestamp_from]_FILTER_PERIOD"] = $arFields['find_el_timestamp_from_FILTER_PERIOD'];
			$GLOBALS["SETTINGS[FILTER][".$listIndex."][find_el_timestamp_from]_FILTER_DIRECTION"] = $arFields['find_el_timestamp_from_FILTER_DIRECTION'];
			?>
			<tr>
				<td><?echo Loc::getMessage("KDA_EE_FIELD_TIMESTAMP_X")?>:</td>
				<td data-filter-period="<?echo htmlspecialcharsex($arFields['find_el_timestamp_from_FILTER_PERIOD'])?>" data-filter-last-days="<?echo htmlspecialcharsex($arFields['find_el_timestamp_from_FILTER_LAST_DAYS'])?>"><?echo CalendarPeriod("SETTINGS[FILTER][".$listIndex."][find_el_timestamp_from]", htmlspecialcharsex($arFields['find_el_timestamp_from']), "SETTINGS[FILTER][".$listIndex."][find_el_timestamp_to]", htmlspecialcharsex($arFields['find_el_timestamp_to']), "dataload", "Y")?></font></td>
			</tr>

			<tr>
				<td><?=Loc::getMessage("KDA_EE_FIELD_MODIFIED_BY")?>:</td>
				<td>
					<?echo FindUserID(
						"SETTINGS[FILTER][".$listIndex."][find_el_modified_user_id]",
						$arFields['find_el_modified_user_id'],
						"",
						"dataload",
						"5",
						"",
						" ... ",
						"",
						""
					);?>
				</td>
			</tr>

			<?
			$GLOBALS["SETTINGS[FILTER][".$listIndex."][find_el_created_from]_FILTER_PERIOD"] = $arFields['find_el_created_from_FILTER_PERIOD'];
			$GLOBALS["SETTINGS[FILTER][".$listIndex."][find_el_created_from]_FILTER_DIRECTION"] = $arFields['find_el_created_from_FILTER_DIRECTION'];
			?>
			<tr>
				<td><?echo Loc::getMessage("KDA_EE_EL_ADMIN_DCREATE")?>:</td>
				<td data-filter-period="<?echo htmlspecialcharsex($arFields['find_el_created_from_FILTER_PERIOD'])?>" data-filter-last-days="<?echo htmlspecialcharsex($arFields['find_el_created_from_FILTER_LAST_DAYS'])?>"><?echo CalendarPeriod("SETTINGS[FILTER][".$listIndex."][find_el_created_from]", htmlspecialcharsex($arFields['find_el_created_from']), "SETTINGS[FILTER][".$listIndex."][find_el_created_to]", htmlspecialcharsex($arFields['find_el_created_to']), "dataload", "Y")?></td>
			</tr>

			<tr>
				<td><?echo Loc::getMessage("KDA_EE_EL_ADMIN_WCREATE")?></td>
				<td>
					<?echo FindUserID(
						"SETTINGS[FILTER][".$listIndex."][find_el_created_user_id]",
						$arFields['find_el_created_user_id'],
						"",
						"dataload",
						"5",
						"",
						" ... ",
						"",
						""
					);?>
				</td>
			</tr>

			<tr class="kda-ee-filter-date-wrap">
				<td><?echo Loc::getMessage("KDA_EE_EL_A_ACTFROM")?>:</td>
				<td>
					<select name="SETTINGS[FILTER][<?=$listIndex?>][find_el_vtype_active_from]"><option value=""><?echo Loc::getMessage("KDA_EE_IS_VALUE_FROM_TO")?></option><option value="empty"<?if($arFields['find_el_vtype_active_from']=='empty'){echo ' selected';}?>><?echo Loc::getMessage("KDA_EE_IS_EMPTY")?></option><option value="not_empty"<?if($arFields['find_el_vtype_active_from']=='not_empty'){echo ' selected';}?>><?echo Loc::getMessage("KDA_EE_IS_NOT_EMPTY")?></option></select></select>
					<?echo CalendarPeriod("SETTINGS[FILTER][".$listIndex."][find_el_date_active_from_from]", htmlspecialcharsex($arFields['find_el_date_active_from_from']), "SETTINGS[FILTER][".$listIndex."][find_el_date_active_from_to]", htmlspecialcharsex($arFields['find_el_date_active_from_to']), "dataload")?>
				</td>
			</tr>

			<tr class="kda-ee-filter-date-wrap">
				<td><?echo Loc::getMessage("KDA_EE_EL_A_ACTTO")?>:</td>
				<td>
					<select name="SETTINGS[FILTER][<?=$listIndex?>][find_el_vtype_date_active_to]"><option value=""><?echo Loc::getMessage("KDA_EE_IS_VALUE_FROM_TO")?></option><option value="empty"<?if($arFields['find_el_vtype_date_active_to']=='empty'){echo ' selected';}?>><?echo Loc::getMessage("KDA_EE_IS_EMPTY")?></option><option value="not_empty"<?if($arFields['find_el_vtype_date_active_to']=='not_empty'){echo ' selected';}?>><?echo Loc::getMessage("KDA_EE_IS_NOT_EMPTY")?></option></select></select>
					<?echo CalendarPeriod("SETTINGS[FILTER][".$listIndex."][find_el_date_active_to_from]", htmlspecialcharsex($arFields['find_el_date_active_to_from']), "SETTINGS[FILTER][".$listIndex."][find_el_date_active_to_to]", htmlspecialcharsex($arFields['find_el_date_active_to_to']), "dataload")?>
				</td>
			</tr>

			<tr>
				<td><?echo Loc::getMessage("KDA_EE_FIELD_ACTIVE")?>:</td>
				<td>
					<select name="SETTINGS[FILTER][<?=$listIndex?>][find_el_active]">
						<option value=""><?=htmlspecialcharsex(Loc::getMessage('KDA_EE_VALUE_ANY'))?></option>
						<option value="Y"<?if($arFields['find_el_active']=="Y")echo " selected"?>><?=htmlspecialcharsex(Loc::getMessage("KDA_EE_YES"))?></option>
						<option value="N"<?if($arFields['find_el_active']=="N")echo " selected"?>><?=htmlspecialcharsex(Loc::getMessage("KDA_EE_NO"))?></option>
					</select>
				</td>
			</tr>
			
			<tr>
				<td><?echo Loc::getMessage("KDA_EE_FIELD_SORT")?>:</td>
				<td>
					<select name="SETTINGS[FILTER][<?=$listIndex?>][find_el_sort_comp]">
						<option value="eq" <?if($arFields['find_el_sort_comp']=='eq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_EQ')?></option>
						<option value="gt" <?if($arFields['find_el_sort_comp']=='gt'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_GT')?></option>
						<option value="geq" <?if($arFields['find_el_sort_comp']=='geq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_GEQ')?></option>
						<option value="lt" <?if($arFields['find_el_sort_comp']=='lt'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_LT')?></option>
						<option value="leq" <?if($arFields['find_el_sort_comp']=='leq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_LEQ')?></option>
					</select>
					<input type="text" name="SETTINGS[FILTER][<?=$listIndex?>][find_el_sort]" value="<?echo htmlspecialcharsex($arFields['find_el_sort'])?>" size="10">
				</td>
			</tr>

			<tr>
				<td><?echo Loc::getMessage("KDA_EE_FIELD_NAME")?>:</td>
				<td><input type="text" name="SETTINGS[FILTER][<?=$listIndex?>][find_el_name]" value="<?echo htmlspecialcharsex($arFields['find_el_name'])?>" size="30">&nbsp;<?=ShowFilterLogicHelp()?></td>
			</tr>
			<tr>
				<td><?echo Loc::getMessage("KDA_EE_EL_ADMIN_PREDESC")?></td>
				<td><select class="kda-ee-filter-chval" name="SETTINGS[FILTER][<?=$listIndex?>][find_el_vtype_pretext]"><option value=""><?echo Loc::getMessage("KDA_EE_IS_VALUE")?></option><option value="empty"<?if($arFields['find_el_vtype_pretext']=='empty'){echo ' selected';}?>><?echo Loc::getMessage("KDA_EE_IS_EMPTY")?></option><option value="not_empty"<?if($arFields['find_el_vtype_pretext']=='not_empty'){echo ' selected';}?>><?echo Loc::getMessage("KDA_EE_IS_NOT_EMPTY")?></option></select><input type="text" name="SETTINGS[FILTER][<?=$listIndex?>][find_el_pretext]" value="<?echo htmlspecialcharsex($arFields['find_el_pretext'])?>" size="30">&nbsp;<?=ShowFilterLogicHelp()?></td>
			</tr>
			<tr>
				<td><?echo Loc::getMessage("KDA_EE_EL_ADMIN_DESC")?></td>
				<td><select class="kda-ee-filter-chval" name="SETTINGS[FILTER][<?=$listIndex?>][find_el_vtype_intext]"><option value=""><?echo Loc::getMessage("KDA_EE_IS_VALUE")?></option><option value="empty"<?if($arFields['find_el_vtype_intext']=='empty'){echo ' selected';}?>><?echo Loc::getMessage("KDA_EE_IS_EMPTY")?></option><option value="not_empty"<?if($arFields['find_el_vtype_intext']=='not_empty'){echo ' selected';}?>><?echo Loc::getMessage("KDA_EE_IS_NOT_EMPTY")?></option></select><input type="text" name="SETTINGS[FILTER][<?=$listIndex?>][find_el_intext]" value="<?echo htmlspecialcharsex($arFields['find_el_intext'])?>" size="30">&nbsp;<?=ShowFilterLogicHelp()?></td>
			</tr>

			<tr>
				<td><?=Loc::getMessage("KDA_EE_EL_A_CODE")?>:</td>
				<td><input type="text" name="SETTINGS[FILTER][<?=$listIndex?>][find_el_code]" value="<?echo htmlspecialcharsex($arFields['find_el_code'])?>" size="30">&nbsp;<?=ShowFilterLogicHelp()?></td>
			</tr>
			<tr>
				<td><?=Loc::getMessage("KDA_EE_EL_A_EXTERNAL_ID")?>:</td>
				<td><input type="text" name="SETTINGS[FILTER][<?=$listIndex?>][find_el_external_id]" value="<?echo htmlspecialcharsex($arFields['find_el_external_id'])?>" size="30"></td>
			</tr>
			<tr>
				<td><?=Loc::getMessage("KDA_EE_EL_A_PREVIEW_PICTURE")?>:</td>
				<td>
					<select name="SETTINGS[FILTER][<?=$listIndex?>][find_el_preview_picture]">
						<option value=""><?=htmlspecialcharsex(Loc::getMessage('KDA_EE_VALUE_ANY'))?></option>
						<option value="Y"<?if($arFields['find_el_preview_picture']=="Y")echo " selected"?>><?=htmlspecialcharsex(Loc::getMessage("KDA_EE_IS_NOT_EMPTY"))?></option>
						<option value="N"<?if($arFields['find_el_preview_picture']=="N")echo " selected"?>><?=htmlspecialcharsex(Loc::getMessage("KDA_EE_IS_EMPTY"))?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td><?=Loc::getMessage("KDA_EE_EL_A_DETAIL_PICTURE")?>:</td>
				<td>
					<select name="SETTINGS[FILTER][<?=$listIndex?>][find_el_detail_picture]">
						<option value=""><?=htmlspecialcharsex(Loc::getMessage('KDA_EE_VALUE_ANY'))?></option>
						<option value="Y"<?if($arFields['find_el_detail_picture']=="Y")echo " selected"?>><?=htmlspecialcharsex(Loc::getMessage("KDA_EE_IS_NOT_EMPTY"))?></option>
						<option value="N"<?if($arFields['find_el_detail_picture']=="N")echo " selected"?>><?=htmlspecialcharsex(Loc::getMessage("KDA_EE_IS_EMPTY"))?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td><?=Loc::getMessage("KDA_EE_EL_A_TAGS")?>:</td>
				<td>
					<input type="text" name="SETTINGS[FILTER][<?=$listIndex?>][find_el_tags]" value="<?echo htmlspecialcharsex($arFields['find_el_tags'])?>" size="30">
				</td>
			</tr>
			<?
			if ($bCatalog)
			{
				if(is_array($productTypeList))
				{
				?><tr>
					<td><?=Loc::getMessage("KDA_EE_CATALOG_TYPE"); ?>:</td>
					<td>
						<select name="SETTINGS[FILTER][<?=$listIndex?>][find_el_catalog_type][]" multiple>
							<option value=""><?=htmlspecialcharsex(Loc::getMessage('KDA_EE_VALUE_ANY'))?></option>
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
					<td><?echo Loc::getMessage("KDA_EE_CATALOG_BUNDLE")?>:</td>
					<td>
						<select name="SETTINGS[FILTER][<?=$listIndex?>][find_el_catalog_bundle]">
							<option value=""><?=htmlspecialcharsex(Loc::getMessage('KDA_EE_VALUE_ANY'))?></option>
							<option value="Y"<?if($arFields['find_el_catalog_bundle']=="Y")echo " selected"?>><?=htmlspecialcharsex(Loc::getMessage("KDA_EE_YES"))?></option>
							<option value="N"<?if($arFields['find_el_catalog_bundle']=="N")echo " selected"?>><?=htmlspecialcharsex(Loc::getMessage("KDA_EE_NO"))?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td><?echo Loc::getMessage("KDA_EE_CATALOG_AVAILABLE")?>:</td>
					<td>
						<select name="SETTINGS[FILTER][<?=$listIndex?>][find_el_catalog_available]">
							<option value=""><?=htmlspecialcharsex(Loc::getMessage('KDA_EE_VALUE_ANY'))?></option>
							<option value="Y"<?if($arFields['find_el_catalog_available']=="Y")echo " selected"?>><?=htmlspecialcharsex(Loc::getMessage("KDA_EE_YES"))?></option>
							<option value="N"<?if($arFields['find_el_catalog_available']=="N")echo " selected"?>><?=htmlspecialcharsex(Loc::getMessage("KDA_EE_NO"))?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td><?echo Loc::getMessage("KDA_EE_CATALOG_QUANTITY")?>:</td>
					<td>
						<select name="SETTINGS[FILTER][<?=$listIndex?>][find_el_catalog_quantity_comp]">
							<option value="eq" <?if($arFields['find_el_catalog_quantity_comp']=='eq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_EQ')?></option>
							<option value="gt" <?if($arFields['find_el_catalog_quantity_comp']=='gt'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_GT')?></option>
							<option value="geq" <?if($arFields['find_el_catalog_quantity_comp']=='geq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_GEQ')?></option>
							<option value="lt" <?if($arFields['find_el_catalog_quantity_comp']=='lt'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_LT')?></option>
							<option value="leq" <?if($arFields['find_el_catalog_quantity_comp']=='leq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_LEQ')?></option>
						</select>
						<input type="text" name="SETTINGS[FILTER][<?=$listIndex?>][find_el_catalog_quantity]" value="<?echo htmlspecialcharsex($arFields['find_el_catalog_quantity'])?>" size="10">
					</td>
				</tr>
				
				<?
				if(is_array($arStores))
				{
					foreach($arStores as $arStore)
					{
						?>
						<tr>
							<td><?echo sprintf(Loc::getMessage("KDA_EE_CATALOG_STORE_QUANTITY"), $arStore['TITLE'])?>:</td>
							<td>
								<select name="SETTINGS[FILTER][<?=$listIndex?>][find_el_catalog_store<?echo $arStore['ID'];?>_quantity_comp]">
									<option value="eq" <?if($arFields['find_el_catalog_store'.$arStore['ID'].'_quantity_comp']=='eq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_EQ')?></option>
									<option value="gt" <?if($arFields['find_el_catalog_store'.$arStore['ID'].'_quantity_comp']=='gt'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_GT')?></option>
									<option value="geq" <?if($arFields['find_el_catalog_store'.$arStore['ID'].'_quantity_comp']=='geq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_GEQ')?></option>
									<option value="lt" <?if($arFields['find_el_catalog_store'.$arStore['ID'].'_quantity_comp']=='lt'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_LT')?></option>
									<option value="leq" <?if($arFields['find_el_catalog_store'.$arStore['ID'].'_quantity_comp']=='leq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_LEQ')?></option>
								</select>
								<input type="text" name="SETTINGS[FILTER][<?=$listIndex?>][find_el_catalog_store<?echo $arStore['ID'];?>_quantity]" value="<?echo htmlspecialcharsex($arFields['find_el_catalog_store'.$arStore['ID'].'_quantity'])?>" size="10">
							</td>
						</tr>
						<?
					}
					
					if(count($arStores) > 0)
					{
					?>
						<tr>
							<td><?echo Loc::getMessage("KDA_EE_CATALOG_STORE_ANY_QUANTITY")?>:</td>
							<td>
								<select name="SETTINGS[FILTER][<?=$listIndex?>][find_el_catalog_store_any_quantity_stores][]" multiple>
									<?foreach($arStores as $arStore){?>
										<option value="<?echo $arStore['ID']?>" <?if(is_array($arFields['find_el_catalog_store_any_quantity_stores']) && in_array($arStore['ID'], $arFields['find_el_catalog_store_any_quantity_stores'])){echo 'selected';}?>><?echo $arStore['TITLE']?></option>
									<?}?>
								</select>
								<select name="SETTINGS[FILTER][<?=$listIndex?>][find_el_catalog_store_any_quantity_comp]">
									<option value="eq" <?if($arFields['find_el_catalog_store_any_quantity_comp']=='eq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_EQ')?></option>
									<option value="gt" <?if($arFields['find_el_catalog_store_any_quantity_comp']=='gt'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_GT')?></option>
									<option value="geq" <?if($arFields['find_el_catalog_store_any_quantity_comp']=='geq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_GEQ')?></option>
									<option value="lt" <?if($arFields['find_el_catalog_store_any_quantity_comp']=='lt'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_LT')?></option>
									<option value="leq" <?if($arFields['find_el_catalog_store_any_quantity_comp']=='leq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_LEQ')?></option>
								</select>
								<input type="text" name="SETTINGS[FILTER][<?=$listIndex?>][find_el_catalog_store_any_quantity]" value="<?echo htmlspecialcharsex($arFields['find_el_catalog_store_any_quantity'])?>" size="10">
							</td>
						</tr>
					<?
					}
				}
				?>
				<tr>
					<td><?echo Loc::getMessage("KDA_EE_CATALOG_PURCHASING_PRICE")?>:</td>
					<td>
						<select name="SETTINGS[FILTER][<?=$listIndex?>][find_el_catalog_purchasing_price_comp]">
							<option value="eq" <?if($arFields['find_el_catalog_purchasing_price_comp']=='eq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_EQ')?></option>
							<option value="gt" <?if($arFields['find_el_catalog_purchasing_price_comp']=='gt'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_GT')?></option>
							<option value="geq" <?if($arFields['find_el_catalog_purchasing_price_comp']=='geq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_GEQ')?></option>
							<option value="lt" <?if($arFields['find_el_catalog_purchasing_price_comp']=='lt'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_LT')?></option>
							<option value="leq" <?if($arFields['find_el_catalog_purchasing_price_comp']=='leq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_LEQ')?></option>
							<option value="from_to" <?if($arFields['find_el_catalog_purchasing_price_comp']=='from_to'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_FROM_TO')?></option>
						</select>
						<input type="text" name="SETTINGS[FILTER][<?=$listIndex?>][find_el_catalog_purchasing_price]" value="<?echo htmlspecialcharsex($arFields['find_el_catalog_purchasing_price'])?>" size="10">
					</td>
				</tr>
				<?
				if(is_array($arPrices))
				{
					foreach($arPrices as $arPrice)
					{
						?>
						<tr>
							<td><?echo sprintf(Loc::getMessage("KDA_EE_CATALOG_PRICE"), $arPrice['NAME_LANG'])?>:</td>
							<td>
								<select name="SETTINGS[FILTER][<?=$listIndex?>][find_el_catalog_price_<?echo $arPrice['ID'];?>_comp]">
									<option value="eq" <?if($arFields['find_el_catalog_price_'.$arPrice['ID'].'_comp']=='eq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_EQ')?></option>
									<option value="empty" <?if($arFields['find_el_catalog_price_'.$arPrice['ID'].'_comp']=='empty'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_EMPTY')?></option>
									<option value="gt" <?if($arFields['find_el_catalog_price_'.$arPrice['ID'].'_comp']=='gt'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_GT')?></option>
									<option value="geq" <?if($arFields['find_el_catalog_price_'.$arPrice['ID'].'_comp']=='geq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_GEQ')?></option>
									<option value="lt" <?if($arFields['find_el_catalog_price_'.$arPrice['ID'].'_comp']=='lt'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_LT')?></option>
									<option value="leq" <?if($arFields['find_el_catalog_price_'.$arPrice['ID'].'_comp']=='leq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_LEQ')?></option>
									<option value="from_to" <?if($arFields['find_el_catalog_price_'.$arPrice['ID'].'_comp']=='from_to'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_FROM_TO')?></option>
								</select>
								<input type="text" name="SETTINGS[FILTER][<?=$listIndex?>][find_el_catalog_price_<?echo $arPrice['ID'];?>]" value="<?echo htmlspecialcharsex($arFields['find_el_catalog_price_'.$arPrice['ID']])?>" size="10">
							</td>
						</tr>
						<?
					}
				}
				
				if($bSale)
				{
					?>
					<tr>
						<td><?=Loc::getMessage("KDA_EE_EL_A_SALE_ORDER")?>:</td>
						<td>
							<input type="text" name="SETTINGS[FILTER][<?=$listIndex?>][find_el_sale_order]" value="<?echo htmlspecialcharsex($arFields['find_el_sale_order'])?>" size="30">
						</td>
					</tr>
					<?
				}
			}
			
		foreach($arProps as $arProp):
			if($arProp["FILTRABLE"]=="Y" || $arProp["PROPERTY_TYPE"]=="F"):
		?>
		<tr>
			<td><?=$arProp["NAME"]?>:</td>
			<td>
				<?if(array_key_exists("GetAdminFilterHTML", $arProp["PROPERTY_USER_TYPE"])):
					$fieldName = "filter_".$listIndex."_find_el_property_".$arProp["ID"];
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
					<select name="SETTINGS[FILTER][<?=$listIndex?>][find_el_property_<?=$arProp["ID"]?>_comp]">
						<option value="eq" <?if($arFields['find_el_property_'.$arProp["ID"].'_comp']=='eq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_EQ')?></option>
						<option value="neq" <?if($arFields['find_el_property_'.$arProp["ID"].'_comp']=='neq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_NEQ')?></option>
						<option value="contain" <?if($arFields['find_el_property_'.$arProp["ID"].'_comp']=='contain'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_CONTAIN')?></option>
						<option value="not_contain" <?if($arFields['find_el_property_'.$arProp["ID"].'_comp']=='not_contain'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_NOT_CONTAIN')?></option>
						<option value="empty" <?if($arFields['find_el_property_'.$arProp["ID"].'_comp']=='empty'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_IS_EMPTY')?></option>
						<option value="not_empty" <?if($arFields['find_el_property_'.$arProp["ID"].'_comp']=='not_empty'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_IS_NOT_EMPTY')?></option>
						<option value="logical" <?if($arFields['find_el_property_'.$arProp["ID"].'_comp']=='logical'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_LOGICAL')?></option>
					</select>
					<input type="text" name="SETTINGS[FILTER][<?=$listIndex?>][find_el_property_<?=$arProp["ID"]?>]" value="<?echo htmlspecialcharsex($arFields["find_el_property_".$arProp["ID"]])?>" size="30">&nbsp;<?=ShowFilterLogicHelp()?>
				<?elseif($arProp["PROPERTY_TYPE"]=='N'):?>
					<select name="SETTINGS[FILTER][<?=$listIndex?>][find_el_property_<?=$arProp["ID"]?>_comp]">
						<option value="eq" <?if($arFields['find_el_property_'.$arProp["ID"].'_comp']=='eq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_EQ')?></option>
						<option value="gt" <?if($arFields['find_el_property_'.$arProp["ID"].'_comp']=='gt'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_GT')?></option>
						<option value="geq" <?if($arFields['find_el_property_'.$arProp["ID"].'_comp']=='geq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_GEQ')?></option>
						<option value="lt" <?if($arFields['find_el_property_'.$arProp["ID"].'_comp']=='lt'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_LT')?></option>
						<option value="leq" <?if($arFields['find_el_property_'.$arProp["ID"].'_comp']=='leq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_LEQ')?></option>
						<option value="from_to" <?if($arFields['find_el_property_'.$arProp["ID"].'_comp']=='from_to'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_FROM_TO')?></option>
					</select>
					<input type="text" name="SETTINGS[FILTER][<?=$listIndex?>][find_el_property_<?=$arProp["ID"]?>]" value="<?echo htmlspecialcharsex($arFields["find_el_property_".$arProp["ID"]])?>" size="10">
				<?elseif($arProp["PROPERTY_TYPE"]=='E'):?>
					<input type="text" name="SETTINGS[FILTER][<?=$listIndex?>][find_el_property_<?=$arProp["ID"]?>]" value="<?echo htmlspecialcharsex($arFields["find_el_property_".$arProp["ID"]])?>" size="30">
				<?elseif($arProp["PROPERTY_TYPE"]=='L'):?>
					<?
					$propVal = $arFields["find_el_property_".$arProp["ID"]];
					if(!is_array($propVal)) $propVal = array($propVal);
					?>
					<select name="SETTINGS[FILTER][<?=$listIndex?>][find_el_property_<?=$arProp["ID"]?>][]" multiple size="5">
						<option value=""><?echo Loc::getMessage("KDA_EE_VALUE_ANY")?></option>
						<option value="NOT_REF"<?if(in_array("NOT_REF", $propVal))echo " selected"?>><?echo Loc::getMessage("KDA_EE_ELEMENT_EDIT_NOT_SET")?></option><?
						$dbrPEnum = CIBlockPropertyEnum::GetList(Array("SORT"=>"ASC", "NAME"=>"ASC"), Array("PROPERTY_ID"=>$arProp["ID"]));
						while($arPEnum = $dbrPEnum->GetNext()):
						?>
							<option value="<?=$arPEnum["ID"]?>"<?if(in_array($arPEnum["ID"], $propVal))echo " selected"?>><?=$arPEnum["VALUE"]?></option>
						<?
						endwhile;
				?></select>
				<?
				elseif($arProp["PROPERTY_TYPE"]=='G'):
					echo self::ShowGroupPropertyField2('SETTINGS[FILTER]['.$listIndex.'][find_el_property_'.$arProp["ID"].']', $arProp, $arFields["find_el_property_".$arProp["ID"]]);
				elseif($arProp["PROPERTY_TYPE"]=='F'):
				?>
					<select name="SETTINGS[FILTER][<?=$listIndex?>][find_el_property_<?=$arProp["ID"]?>]">
						<option value=""><?=htmlspecialcharsex(Loc::getMessage('KDA_EE_VALUE_ANY'))?></option>
						<option value="Y"<?if($arFields["find_el_property_".$arProp["ID"]]=="Y")echo " selected"?>><?=htmlspecialcharsex(Loc::getMessage("KDA_EE_IS_NOT_EMPTY"))?></option>
						<option value="N"<?if($arFields["find_el_property_".$arProp["ID"]]=="N")echo " selected"?>><?=htmlspecialcharsex(Loc::getMessage("KDA_EE_IS_EMPTY"))?></option>
					</select>
				<?
				endif;
				?>
			</td>
		</tr>
		<?
			endif;
		endforeach;

		if($boolSKU){
		?>
			<tr>
				<td><?echo ('' != $strSKUName ? $strSKUName.' - ' : '').Loc::getMessage("KDA_EE_FILTER_FROMTO_ID")?>:</td>
				<td nowrap>
					<input type="text" name="SETTINGS[FILTER][<?=$listIndex?>][find_sub_el_id_start]" size="10" value="<?echo htmlspecialcharsex($arFields['find_sub_el_id_start'])?>">
					...
					<input type="text" name="SETTINGS[FILTER][<?=$listIndex?>][find_sub_el_id_end]" size="10" value="<?echo htmlspecialcharsex($arFields['find_sub_el_id_end'])?>">
				</td>
			</tr>
			<?
			$GLOBALS["SETTINGS[FILTER][".$listIndex."][find_sub_el_timestamp_from]_FILTER_PERIOD"] = $arFields['find_sub_el_timestamp_from_FILTER_PERIOD'];
			$GLOBALS["SETTINGS[FILTER][".$listIndex."][find_sub_el_timestamp_from]_FILTER_DIRECTION"] = $arFields['find_sub_el_timestamp_from_FILTER_DIRECTION'];
			?>
			<tr>
				<td><?echo ('' != $strSKUName ? $strSKUName.' - ' : '').Loc::getMessage("KDA_EE_FIELD_TIMESTAMP_X")?>:</td>
				<td data-filter-period="<?echo htmlspecialcharsex($arFields['find_sub_el_timestamp_from_FILTER_PERIOD'])?>" data-filter-last-days="<?echo htmlspecialcharsex($arFields['find_sub_el_timestamp_from_FILTER_LAST_DAYS'])?>"><?echo CalendarPeriod("SETTINGS[FILTER][".$listIndex."][find_sub_el_timestamp_from]", htmlspecialcharsex($arFields['find_sub_el_timestamp_from']), "SETTINGS[FILTER][".$listIndex."][find_sub_el_timestamp_to]", htmlspecialcharsex($arFields['find_sub_el_timestamp_to']), "dataload", "Y")?></font></td>
			</tr>
			<tr>
				<td><?echo ('' != $strSKUName ? $strSKUName.' - ' : '').Loc::getMessage("KDA_EE_FIELD_ACTIVE")?>:</td>
				<td>
					<select name="SETTINGS[FILTER][<?=$listIndex?>][find_sub_el_active]">
						<option value=""><?=htmlspecialcharsex(Loc::getMessage('KDA_EE_VALUE_ANY'))?></option>
						<option value="Y"<?if($arFields['find_sub_el_active']=="Y")echo " selected"?>><?=htmlspecialcharsex(Loc::getMessage("KDA_EE_YES"))?></option>
						<option value="N"<?if($arFields['find_sub_el_active']=="N")echo " selected"?>><?=htmlspecialcharsex(Loc::getMessage("KDA_EE_NO"))?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td><?echo ('' != $strSKUName ? $strSKUName.' - ' : '').Loc::getMessage("KDA_EE_FIELD_SORT")?>:</td>
				<td>
					<select name="SETTINGS[FILTER][<?=$listIndex?>][find_sub_el_sort_comp]">
						<option value="eq" <?if($arFields['find_sub_el_sort_comp']=='eq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_EQ')?></option>
						<option value="gt" <?if($arFields['find_sub_el_sort_comp']=='gt'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_GT')?></option>
						<option value="geq" <?if($arFields['find_sub_el_sort_comp']=='geq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_GEQ')?></option>
						<option value="lt" <?if($arFields['find_sub_el_sort_comp']=='lt'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_LT')?></option>
						<option value="leq" <?if($arFields['find_sub_el_sort_comp']=='leq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_LEQ')?></option>
					</select>
					<input type="text" name="SETTINGS[FILTER][<?=$listIndex?>][find_sub_el_sort]" value="<?echo htmlspecialcharsex($arFields['find_sub_el_sort'])?>" size="10">
				</td>
			</tr>
			<tr>
				<td><?echo ('' != $strSKUName ? $strSKUName.' - ' : '').Loc::getMessage("KDA_EE_CATALOG_QUANTITY")?>:</td>
				<td>
					<select name="SETTINGS[FILTER][<?=$listIndex?>][find_sub_el_catalog_quantity_comp]">
						<option value="eq" <?if($arFields['find_sub_el_catalog_quantity_comp']=='eq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_EQ')?></option>
						<option value="gt" <?if($arFields['find_sub_el_catalog_quantity_comp']=='gt'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_GT')?></option>
						<option value="geq" <?if($arFields['find_sub_el_catalog_quantity_comp']=='geq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_GEQ')?></option>
						<option value="lt" <?if($arFields['find_sub_el_catalog_quantity_comp']=='lt'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_LT')?></option>
						<option value="leq" <?if($arFields['find_sub_el_catalog_quantity_comp']=='leq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_LEQ')?></option>
					</select>
					<input type="text" name="SETTINGS[FILTER][<?=$listIndex?>][find_sub_el_catalog_quantity]" value="<?echo htmlspecialcharsex($arFields['find_sub_el_catalog_quantity'])?>" size="10">
				</td>
			</tr>
			
			<?
			if(is_array($arStores))
			{
				foreach($arStores as $arStore)
				{
					?>
					<tr>
						<td><?echo ('' != $strSKUName ? $strSKUName.' - ' : '').sprintf(Loc::getMessage("KDA_EE_CATALOG_STORE_QUANTITY"), $arStore['TITLE'])?>:</td>
						<td>
							<select name="SETTINGS[FILTER][<?=$listIndex?>][find_sub_el_catalog_store<?echo $arStore['ID'];?>_quantity_comp]">
								<option value="eq" <?if($arFields['find_sub_el_catalog_store'.$arStore['ID'].'_quantity_comp']=='eq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_EQ')?></option>
								<option value="gt" <?if($arFields['find_sub_el_catalog_store'.$arStore['ID'].'_quantity_comp']=='gt'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_GT')?></option>
								<option value="geq" <?if($arFields['find_sub_el_catalog_store'.$arStore['ID'].'_quantity_comp']=='geq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_GEQ')?></option>
								<option value="lt" <?if($arFields['find_sub_el_catalog_store'.$arStore['ID'].'_quantity_comp']=='lt'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_LT')?></option>
								<option value="leq" <?if($arFields['find_sub_el_catalog_store'.$arStore['ID'].'_quantity_comp']=='leq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_LEQ')?></option>
							</select>
							<input type="text" name="SETTINGS[FILTER][<?=$listIndex?>][find_sub_el_catalog_store<?echo $arStore['ID'];?>_quantity]" value="<?echo htmlspecialcharsex($arFields['find_sub_el_catalog_store'.$arStore['ID'].'_quantity'])?>" size="10">
						</td>
					</tr>
					<?
				}
				
				if(count($arStores) > 0) 
				{
				?>
					<tr>
						<td><?echo ('' != $strSKUName ? $strSKUName.' - ' : '').Loc::getMessage("KDA_EE_CATALOG_STORE_ANY_QUANTITY")?>:</td>
						<td>
							<select name="SETTINGS[FILTER][<?=$listIndex?>][find_sub_el_catalog_store_any_quantity_stores][]" multiple>
								<?foreach($arStores as $arStore){?>
									<option value="<?echo $arStore['ID']?>" <?if(is_array($arFields['find_sub_el_catalog_store_any_quantity_stores']) && in_array($arStore['ID'], $arFields['find_sub_el_catalog_store_any_quantity_stores'])){echo 'selected';}?>><?echo $arStore['TITLE']?></option>
								<?}?>
							</select>
							<select name="SETTINGS[FILTER][<?=$listIndex?>][find_sub_el_catalog_store_any_quantity_comp]">
								<option value="eq" <?if($arFields['find_sub_el_catalog_store_any_quantity_comp']=='eq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_EQ')?></option>
								<option value="gt" <?if($arFields['find_sub_el_catalog_store_any_quantity_comp']=='gt'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_GT')?></option>
								<option value="geq" <?if($arFields['find_sub_el_catalog_store_any_quantity_comp']=='geq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_GEQ')?></option>
								<option value="lt" <?if($arFields['find_sub_el_catalog_store_any_quantity_comp']=='lt'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_LT')?></option>
								<option value="leq" <?if($arFields['find_sub_el_catalog_store_any_quantity_comp']=='leq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_LEQ')?></option>
							</select>
							<input type="text" name="SETTINGS[FILTER][<?=$listIndex?>][find_sub_el_catalog_store_any_quantity]" value="<?echo htmlspecialcharsex($arFields['find_sub_el_catalog_store_any_quantity'])?>" size="10">
						</td>
					</tr>
				<?
				}
			}
			?>
			<tr>
				<td><?echo ('' != $strSKUName ? $strSKUName.' - ' : '').Loc::getMessage("KDA_EE_CATALOG_PURCHASING_PRICE")?>:</td>
				<td>
					<select name="SETTINGS[FILTER][<?=$listIndex?>][find_sub_el_catalog_purchasing_price_comp]">
						<option value="eq" <?if($arFields['find_sub_el_catalog_purchasing_price_comp']=='eq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_EQ')?></option>
						<option value="gt" <?if($arFields['find_sub_el_catalog_purchasing_price_comp']=='gt'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_GT')?></option>
						<option value="geq" <?if($arFields['find_sub_el_catalog_purchasing_price_comp']=='geq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_GEQ')?></option>
						<option value="lt" <?if($arFields['find_sub_el_catalog_purchasing_price_comp']=='lt'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_LT')?></option>
						<option value="leq" <?if($arFields['find_sub_el_catalog_purchasing_price_comp']=='leq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_LEQ')?></option>
						<option value="from_to" <?if($arFields['find_sub_el_catalog_purchasing_price_comp']=='from_to'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_FROM_TO')?></option>
					</select>
					<input type="text" name="SETTINGS[FILTER][<?=$listIndex?>][find_sub_el_catalog_purchasing_price]" value="<?echo htmlspecialcharsex($arFields['find_sub_el_catalog_purchasing_price'])?>" size="10">
				</td>
			</tr>
			<?
			if(is_array($arPrices))
			{
				foreach($arPrices as $arPrice)
				{
					?>
					<tr>
						<td><?echo ('' != $strSKUName ? $strSKUName.' - ' : '').sprintf(Loc::getMessage("KDA_EE_CATALOG_PRICE"), $arPrice['NAME_LANG'])?>:</td>
						<td>
							<select name="SETTINGS[FILTER][<?=$listIndex?>][find_sub_el_catalog_price_<?echo $arPrice['ID'];?>_comp]">
								<option value="eq" <?if($arFields['find_sub_el_catalog_price_'.$arPrice['ID'].'_comp']=='eq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_EQ')?></option>
								<option value="empty" <?if($arFields['find_sub_el_catalog_price_'.$arPrice['ID'].'_comp']=='empty'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_EMPTY')?></option>
								<option value="gt" <?if($arFields['find_sub_el_catalog_price_'.$arPrice['ID'].'_comp']=='gt'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_GT')?></option>
								<option value="geq" <?if($arFields['find_sub_el_catalog_price_'.$arPrice['ID'].'_comp']=='geq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_GEQ')?></option>
								<option value="lt" <?if($arFields['find_sub_el_catalog_price_'.$arPrice['ID'].'_comp']=='lt'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_LT')?></option>
								<option value="leq" <?if($arFields['find_sub_el_catalog_price_'.$arPrice['ID'].'_comp']=='leq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_LEQ')?></option>
								<option value="from_to" <?if($arFields['find_sub_el_catalog_price_'.$arPrice['ID'].'_comp']=='from_to'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_FROM_TO')?></option>
							</select>
							<input type="text" name="SETTINGS[FILTER][<?=$listIndex?>][find_sub_el_catalog_price_<?echo $arPrice['ID'];?>]" value="<?echo htmlspecialcharsex($arFields['find_sub_el_catalog_price_'.$arPrice['ID']])?>" size="10">
						</td>
					</tr>
					<?
				}
			}
			
			if(isset($arSKUProps) && is_array($arSKUProps))
			{
				foreach($arSKUProps as $arProp):
					if($arProp["FILTRABLE"]=="Y" && $arProp["PROPERTY_TYPE"]!="F"):
				?>
				<tr>
					<td><? echo ('' != $strSKUName ? $strSKUName.' - ' : ''), $arProp["NAME"]; ?>:</td>
					<td>
						<?if(array_key_exists("GetAdminFilterHTML", $arProp["PROPERTY_USER_TYPE"])):
							$fieldName = "filter_".$listIndex."_find_sub_el_property_".$arProp["ID"];
							if(isset($arFields["find_sub_el_property_".$arProp["ID"]."_from"])) $GLOBALS[$fieldName."_from"] = $arFields["find_sub_el_property_".$arProp["ID"]."_from"];
							if(isset($arFields["find_sub_el_property_".$arProp["ID"]."_to"])) $GLOBALS[$fieldName."_to"] = $arFields["find_sub_el_property_".$arProp["ID"]."_to"];
							$GLOBALS[$fieldName] = $arFields["find_sub_el_property_".$arProp["ID"]];
							$GLOBALS['set_filter'] = 'Y';
							echo call_user_func_array($arProp["PROPERTY_USER_TYPE"]["GetAdminFilterHTML"], array(
								$arProp,
								array(
									"VALUE" => $fieldName,
									"TABLE_ID" => $sTableID,
								),
							));
						elseif($arProp["PROPERTY_TYPE"]=='S'):?>
						<select name="SETTINGS[FILTER][<?=$listIndex?>][find_sub_el_property_<?=$arProp["ID"]?>_comp]">
							<option value="eq" <?if($arFields['find_sub_el_property_'.$arProp["ID"].'_comp']=='eq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_EQ')?></option>
							<option value="neq" <?if($arFields['find_sub_el_property_'.$arProp["ID"].'_comp']=='neq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_NEQ')?></option>
							<option value="contain" <?if($arFields['find_sub_el_property_'.$arProp["ID"].'_comp']=='contain'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_CONTAIN')?></option>
							<option value="not_contain" <?if($arFields['find_sub_el_property_'.$arProp["ID"].'_comp']=='not_contain'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_NOT_CONTAIN')?></option>
							<option value="empty" <?if($arFields['find_sub_el_property_'.$arProp["ID"].'_comp']=='empty'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_IS_EMPTY')?></option>
							<option value="not_empty" <?if($arFields['find_sub_el_property_'.$arProp["ID"].'_comp']=='not_empty'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_IS_NOT_EMPTY')?></option>
							<option value="logical" <?if($arFields['find_sub_el_property_'.$arProp["ID"].'_comp']=='logical'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_LOGICAL')?></option>
						</select>
							<input type="text" name="SETTINGS[FILTER][<?=$listIndex?>][find_sub_el_property_<?=$arProp["ID"]?>]" value="<?echo htmlspecialcharsex($arFields["find_sub_el_property_".$arProp["ID"]])?>" size="30">&nbsp;<?=ShowFilterLogicHelp()?>
						<?elseif($arProp["PROPERTY_TYPE"]=='N'):?>
							<select name="SETTINGS[FILTER][<?=$listIndex?>][find_sub_el_property_<?=$arProp["ID"]?>_comp]">
								<option value="eq" <?if($arFields['find_sub_el_property_'.$arProp["ID"].'_comp']=='eq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_EQ')?></option>
								<option value="gt" <?if($arFields['find_sub_el_property_'.$arProp["ID"].'_comp']=='gt'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_GT')?></option>
								<option value="geq" <?if($arFields['find_sub_el_property_'.$arProp["ID"].'_comp']=='geq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_GEQ')?></option>
								<option value="lt" <?if($arFields['find_sub_el_property_'.$arProp["ID"].'_comp']=='lt'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_LT')?></option>
								<option value="leq" <?if($arFields['find_sub_el_property_'.$arProp["ID"].'_comp']=='leq'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_LEQ')?></option>
								<option value="from_to" <?if($arFields['find_sub_el_property_'.$arProp["ID"].'_comp']=='from_to'){echo 'selected';}?>><?=Loc::getMessage('KDA_EE_COMPARE_FROM_TO')?></option>
							</select>
							<input type="text" name="SETTINGS[FILTER][<?=$listIndex?>][find_sub_el_property_<?=$arProp["ID"]?>]" value="<?echo htmlspecialcharsex($arFields["find_sub_el_property_".$arProp["ID"]])?>" size="10">
						<?elseif($arProp["PROPERTY_TYPE"]=='E'):?>
							<input type="text" name="SETTINGS[FILTER][<?=$listIndex?>][find_sub_el_property_<?=$arProp["ID"]?>]" value="<?echo htmlspecialcharsex($arFields["find_sub_el_property_".$arProp["ID"]])?>" size="30">
						<?elseif($arProp["PROPERTY_TYPE"]=='L'):?>
							<?
							$propVal = $arFields["find_sub_el_property_".$arProp["ID"]];
							if(!is_array($propVal)) $propVal = array($propVal);
							?>
							<select name="SETTINGS[FILTER][<?=$listIndex?>][find_sub_el_property_<?=$arProp["ID"]?>][]" multiple size="5">
								<option value=""><?echo Loc::getMessage("KDA_EE_VALUE_ANY")?></option>
								<option value="NOT_REF"<?if(in_array("NOT_REF", $propVal))echo " selected"?>><?echo Loc::getMessage("KDA_EE_ELEMENT_EDIT_NOT_SET")?></option><?
								$dbrPEnum = CIBlockPropertyEnum::GetList(Array("SORT"=>"ASC", "NAME"=>"ASC"), Array("PROPERTY_ID"=>$arProp["ID"]));
								while($arPEnum = $dbrPEnum->GetNext()):
								?>
									<option value="<?=$arPEnum["ID"]?>"<?if(in_array($arPEnum["ID"], $propVal))echo " selected"?>><?=$arPEnum["VALUE"]?></option>
								<?
								endwhile;
						?></select>
						<?
						elseif($arProp["PROPERTY_TYPE"]=='G'):
							echo self::ShowGroupPropertyField2('SETTINGS[FILTER]['.$listIndex.'][find_sub_el_property_'.$arProp["ID"].']', $arProp, $arFields["find_sub_el_property_".$arProp["ID"]]);
						endif;
						?>
					</td>
				</tr>
				<?
					endif;
				endforeach;
			}
		}

		$oFilter->Buttons();
		?><span class="adm-btn-wrap"><input type="submit"  class="adm-btn" name="set_filter" value="<? echo Loc::getMessage("admin_lib_filter_set_butt"); ?>" title="<? echo Loc::getMessage("admin_lib_filter_set_butt_title"); ?>" onClick="return EList.ApplyFilter(this);"></span>
		<span class="adm-btn-wrap"><input type="submit"  class="adm-btn" name="del_filter" value="<? echo Loc::getMessage("admin_lib_filter_clear_butt"); ?>" title="<? echo Loc::getMessage("admin_lib_filter_clear_butt_title"); ?>" onClick="return EList.DeleteFilter(this);"></span>
		<?
		$oFilter->End();

		?>
		<!--</form>-->
		</div>
		<?
	}
	
	public static function ShowFilterHighload($sTableID, $listIndex, $SETTINGS, $SETTINGS_DEFAULT)
	{
		global $APPLICATION, $USER_FIELD_MANAGER;
		CJSCore::Init('file_input');
		$HLBL_ID = $SETTINGS_DEFAULT['HIGHLOADBLOCK_ID'];
		
		$arFields = (is_array($SETTINGS['FILTER'][$listIndex]) ? $SETTINGS['FILTER'][$listIndex] : array());
		
		$ufEntityId = 'HLBLOCK_'.$HLBL_ID;
		
		?>
		<!--<form method="GET" name="find_form" id="find_form" action="">-->
		<div class="find_form_inner">
		<?
			
		$filterValues = array();
		$arFindFields = array('ID');
		
		$USER_FIELD_MANAGER->AdminListAddFilterFields($ufEntityId, $filterFields);
		$USER_FIELD_MANAGER->AddFindFields($ufEntityId, $arFindFields);

		
		$oFilter = new CAdminFilter($sTableID."_filter", $arFindFields);
		
		$oFilter->Begin();
		
		?>
		<tr>
			<td>ID</td>
			<td><input type="text" name="SETTINGS[FILTER][<?=$listIndex?>][find_ID]" size="47" value="<?echo htmlspecialcharsbx($arFields['find_ID'])?>"><?=ShowFilterLogicHelp()?></td>
		</tr>
		<?
		//$USER_FIELD_MANAGER->AdminListShowFilter($ufEntityId);
		$arUserFields = $USER_FIELD_MANAGER->GetUserFields($ufEntityId, 0, LANGUAGE_ID);
		foreach($arUserFields as $FIELD_NAME=>$arUserField)
		{
			if($arUserField["SHOW_FILTER"]!="N" && $arUserField["USER_TYPE"]["BASE_TYPE"]!="file")
			{
				echo $USER_FIELD_MANAGER->GetFilterHTML($arUserField, 'SETTINGS[FILTER]['.$listIndex.'][find_'.$FIELD_NAME.']', $arFields['find_'.$FIELD_NAME]);
			}
		}
	
		$oFilter->Buttons();
		?><span class="adm-btn-wrap"><input type="submit"  class="adm-btn" name="set_filter" value="<? echo Loc::getMessage("admin_lib_filter_set_butt"); ?>" title="<? echo Loc::getMessage("admin_lib_filter_set_butt_title"); ?>" onClick="return EList.ApplyFilter(this);"></span>
		<span class="adm-btn-wrap"><input type="submit"  class="adm-btn" name="del_filter" value="<? echo Loc::getMessage("admin_lib_filter_clear_butt"); ?>" title="<? echo Loc::getMessage("admin_lib_filter_clear_butt_title"); ?>" onClick="return EList.DeleteFilter(this);"></span>
		<?
		$oFilter->End();

		?>
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
		$sections = CIBlockSection::GetTreeList(Array("IBLOCK_ID"=>$property_fields["LINK_IBLOCK_ID"]), array("ID", "NAME", "DEPTH_LEVEL"));
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
	
	public static function GetCellStyleFormatted($arStyles = array(), $arParams = array())
	{
		if(!is_array($arStyles)) $arStyles = array();
		//if(empty($arStyles)) return '';
		$style = '';
		if(!$arStyles['FONT_FAMILY'] && $arParams['FONT_FAMILY']) $arStyles['FONT_FAMILY'] = $arParams['FONT_FAMILY'];
		if(!$arStyles['FONT_SIZE'] && $arParams['FONT_SIZE']) $arStyles['FONT_SIZE'] = $arParams['FONT_SIZE'];
		if(!$arStyles['FONT_COLOR'] && $arParams['FONT_COLOR']) $arStyles['FONT_COLOR'] = $arParams['FONT_COLOR'];
		if(!$arStyles['STYLE_BOLD'] && $arParams['STYLE_BOLD']) $arStyles['STYLE_BOLD'] = $arParams['STYLE_BOLD'];
		if(!$arStyles['STYLE_ITALIC'] && $arParams['STYLE_ITALIC']) $arStyles['STYLE_ITALIC'] = $arParams['STYLE_ITALIC'];
		
		if($arStyles['FONT_FAMILY']) $style .= 'font-family:'.htmlspecialcharsex($arStyles['FONT_FAMILY']).';';
		if((int)$arStyles['FONT_SIZE'] > 0) $style .= 'font-size:'.((int)$arStyles['FONT_SIZE'] + 2).'px;';
		if($arStyles['FONT_COLOR']) $style .= 'color:'.htmlspecialcharsex($arStyles['FONT_COLOR']).';';
		if($arStyles['STYLE_BOLD']=='Y') $style .= 'font-weight:bold;';
		if($arStyles['STYLE_ITALIC']=='Y') $style .= 'font-style:italic;';
		if($arStyles['BACKGROUND_COLOR']) $style .= 'background-color:'.htmlspecialcharsex($arStyles['BACKGROUND_COLOR']).';';
		if($arStyles['INDENT']) $style .= 'padding-left:'.(intval($arStyles['INDENT'])*15).'px;';
		
		$textAlign = ToLower($arStyles['TEXT_ALIGN'] ? $arStyles['TEXT_ALIGN'] : $arParams['DISPLAY_TEXT_ALIGN']);
		if(!$textAlign) $textAlign = 'left';
		$style .= 'text-align:'.htmlspecialcharsex($textAlign).';';
		$verticalAlign = ToLower($arStyles['VERTICAL_ALIGN'] ? $arStyles['VERTICAL_ALIGN'] : $arParams['DISPLAY_VERTICAL_ALIGN']);
		if(!$verticalAlign) $verticalAlign = 'top';
		$style .= 'vertical-align:'.htmlspecialcharsex($verticalAlign).';';
		
		if(strlen($style) > 0) $style = 'style="'.$style.'"';
		return $style;
	}
	
	public static function PrepareTextRows(&$rows, $arParams=array(), $arStepParams=array())
	{
		if(is_array($rows))
		{
			foreach($rows as $listIndex=>$arRows)
			{
				if(is_array($rows[$listIndex]))
				{
					$rowsCount = (int)$arStepParams['rows2'][$listIndex];
					if(is_array($arParams['TEXT_ROWS_TOP'])) $rowsCount += count($arParams['TEXT_ROWS_TOP']);
					if($arParams['HIDE_COLUMN_TITLES']!='Y') $rowsCount += 1;
					if(is_array($arParams['TEXT_ROWS_TOP2'])) $rowsCount += count($arParams['TEXT_ROWS_TOP2']);
					
					foreach($rows[$listIndex] as $k=>$row)
					{
						$row = str_replace('{MAX_ROW_NUM}', $rowsCount, $row);
						$row = preg_replace_callback('/\{DATE_(\S*)\}/', array('CKDAExportUtils', 'GetDateFormat'), $row);
						$row = preg_replace_callback('/\{RATE_SITE\.(\S*)\}/', array('CKDAExportUtils', 'GetCurrenyRateSite'), $row);
						$row = preg_replace_callback('/\{RATE_CBR\.(\S*)\}/', array('CKDAExportUtils', 'GetCurrenyRateCbr'), $row);
						$rows[$listIndex][$k] = $row;
					}
				}
			}
		}
	}
	
	public static function PrepareExportFileName($name)
	{
		return preg_replace_callback('/\{DATE_(\S*)\}/', array('CKDAExportUtils', 'GetDateFormat'), $name);
	}
	
	public static function GetDateFormat($m)
	{
		$format = str_replace('_', ' ', $m[1]);
		return ToLower(CIBlockFormatProperties::DateFormat($format, time()));
	}
	
	public static function GetCurrenyRateSite($m)
	{
		if(Loader::includeModule("currency"))
		{
			$dbRes = \CCurrencyRates::GetList(($by="date"), ($order="desc"), array("CURRENCY" => $m[1]));
			if($arr = $dbRes->Fetch())
			{
				return $arr['RATE'];
			}
		}
		return '';
	}
	
	public static function GetFileExtension($filename)
	{
		$filename = end(explode('/', $filename));
		$arParts = explode('.', $filename);
		if(count($arParts) > 1) 
		{
			$ext = trim(array_pop($arParts));
			if(strlen($ext)==0 || strlen($ext)>4 || preg_match('/^(\d+)$/', $ext)) return '';
			if(ToLower($ext)=='gz' && count($arParts) > 1)
			{
				$ext = array_pop($arParts).'.'.$ext;
			}
			return $ext;
		}
		else return '';
	}
	
	public static function GetCurrenyRateCbr($m)
	{
		$arRates = static::GetCurrencyRates();
		if(isset($arRates[$m[1]])) return $arRates[$m[1]];
		return '';
	}
	
	public static function GetCurrencyRates()
	{
		if(!isset(static::$currencyRates))
		{
			$arRates = unserialize(\Bitrix\Main\Config\Option::get(static::$moduleId, 'CURRENCY_RATES', ''));
			if(!is_array($arRates)) $arRates = array();
			if(!isset($arRates['TIME']) || $arRates['TIME'] < time() - 6*60*60)
			{
				$arRates2 = array();
				$client = new \Bitrix\Main\Web\HttpClient(array('socketTimeout'=>20));
				$res = $client->get('http://www.cbr.ru/scripts/XML_daily.asp');
				if($res)
				{
					$xml = simplexml_load_string($res);
					if($xml->Valute)
					{
						foreach($xml->Valute as $val)
						{
							$numVal = static::GetFloatVal((string)$val->Value);
							if($numVal > 0)$arRates2[(string)$val->CharCode] = (string)$numVal;
						}
					}
				}
				if(count($arRates2) > 1)
				{
					$arRates = $arRates2;
					$arRates['TIME'] = time();
					\Bitrix\Main\Config\Option::set(static::$moduleId, 'CURRENCY_RATES', serialize($arRates));
				}
			}
			if(Loader::includeModule('currency'))
			{
				if(!isset($arRates['USD'])) $arRates['USD'] = CCurrencyRates::ConvertCurrency(1, 'USD', 'RUB');
				if(!isset($arRates['EUR'])) $arRates['EUR'] = CCurrencyRates::ConvertCurrency(1, 'EUR', 'RUB');
			}
			static::$currencyRates = $arRates;
		}
		return static::$currencyRates;
	}
	
	public static function GetFloatVal($val, $precision=0)
	{
		$val = floatval(preg_replace('/[^\d\.\-]+/', '', str_replace(',', '.', $val)));
		if($precision > 0) $val = round($val, $precision);
		return $val;
	}
	
	public static function RemoveTmpFiles($maxTime = 5)
	{
		$timeBegin = time();
		$docRoot = $_SERVER["DOCUMENT_ROOT"];
		$tmpDir = $docRoot.'/upload/tmp/'.static::$moduleId.'/'.static::$moduleSubDir;
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
					$arParams = CUtil::JsObjectToPhp(file_get_contents($tmpDir.$file));
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
	
	public static function CheckZipArchive()
	{
		$optionName = static::$zipArchiveOption;
		if(class_exists('\ZipArchive'))
		{
			$tmpDir = $_SERVER["DOCUMENT_ROOT"].'/upload/tmp/'.static::$moduleId.'/'.static::$moduleSubDir;
			CheckDirPath($tmpDir);
			$tempPathZip = $tmpDir.'test.zip';
			$tempPathTxt = $tmpDir.'test.txt';
			file_put_contents($tempPathTxt, 'test');
			\Bitrix\Main\Config\Option::set(static::$moduleId, $optionName, 'NONE');
			if(($zipObj = new \ZipArchive()) && $zipObj->open($tempPathZip, \ZipArchive::OVERWRITE|\ZipArchive::CREATE)===true)
			{
				$zipObj->addFile($tempPathTxt, 'test.txt');
				$zipObj->close();
				if(file_exists($tempPathZip))
				{
					\Bitrix\Main\Config\Option::set(static::$moduleId, $optionName, 'OVERWRITE_CREATE');
					unlink($tempPathZip);
				}
			}
			unlink($tempPathTxt);
		}
	}
	
	public static function CanUseZipArchive()
	{
		if(!class_exists('\ZipArchive')) return false;
		$optionName = static::$zipArchiveOption;
		if(\Bitrix\Main\Config\Option::get(static::$moduleId, $optionName)=='NONE') return false;
		return true;
	}
	
	public static function getSiteEncoding()
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

		return strtolower($logicalEncoding);
	}
	
	public static function getfileSystemEncoding()
	{
		$fileSystemEncoding = strtolower(defined("BX_FILE_SYSTEM_ENCODING") ? BX_FILE_SYSTEM_ENCODING : "");

		if (empty($fileSystemEncoding))
		{
			if (strtoupper(substr(PHP_OS, 0, 3)) === "WIN")
				$fileSystemEncoding =  "windows-1251";
			else
				$fileSystemEncoding = "utf-8";
		}

		return $fileSystemEncoding;
	}
}
?>
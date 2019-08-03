<?php
namespace Bitrix\EsolImportxml;

class ZipArchive
{
	private $tmpDir = '';
	
	public function __construct()
	{

	}
	
	public function __destruct()
	{
		$this->close();
	}
	
	public function close()
	{
		if(strlen($this->tmpDir) > 0 && file_exists($this->tmpDir))
		{
			DeleteDirFilesEx(substr($this->tmpDir, strlen($_SERVER['DOCUMENT_ROOT'])));
			$this->tmpDir = '';
		}
	}
	
	public function open($pFilename)
	{
		$temp_path = \CFile::GetTempName('', bx_basename($pFilename));
		$this->tmpDir = \Bitrix\Main\IO\Path::getDirectory($temp_path);
		\Bitrix\Main\IO\Directory::createDirectory($this->tmpDir);
		$this->tmpDir .= '/';
		
		if(class_exists('\ZipArchive'))
		{
			$zipObj = new \ZipArchive;
			if ($zipObj->open($pFilename) === true)
			{
				$zipObj->extractTo($this->tmpDir);
				$zipObj->close();
			}
		}
		else
		{
			$zipObj = \CBXArchive::GetArchive($pFilename, 'ZIP');
			$zipObj->Unpack($this->tmpDir);
		}
		return true;
	}
	
	public function getFromName($name, $length=0, $flags=0)
	{
		$content = file_get_contents($this->tmpDir.$name);
		if($length > 0) $content = substr($content, 0, $length);
		return $content;
	}
	
	public function getSimpleXmlForSheet($name, $readFilter = null)
	{
		$fn = $this->tmpDir.$name;

		if(!file_exists($fn))
		{
			return new \SimpleXMLElement('<d></d>');
		}
		
		$xml = new \XMLReader();
		$res = $xml->open($fn);

		$xmlObj = new \SimpleXMLElement('<d></d>');
		$arObjects = array();
		$arObjectNames = array();
		$curDepth = 0;
		$arObjects[$curDepth] = &$xmlObj;
		$rowNum = 0;
		$isRead = false;
		while (($isRead || $xml->read())) {
			$isRead = false;
			if($xml->nodeType == \XMLReader::ELEMENT) 
			{
				if($arObjectNames[1]=='sheetData' && $xml->name=='row' && $xml->depth==2)
				{
					$arObjectNames[$xml->depth] = $xml->name;
					$rowNum++;
				}
				if($arObjectNames[1]=='sheetData' && $arObjectNames[2]=='row' && $xml->depth>=2)
				{
					if(is_callable(array($readFilter, 'readCell')) && !$readFilter->readCell(1, $rowNum)) continue;
				}

				$arAttributes = array();
				if($xml->moveToFirstAttribute())
				{
					$arAttributes[] = array('name'=>$xml->name, 'value'=>$xml->value, 'namespaceURI'=>$xml->namespaceURI);
					while($xml->moveToNextAttribute ())
					{
						$arAttributes[] = array('name'=>$xml->name, 'value'=>$xml->value, 'namespaceURI'=>$xml->namespaceURI);
					}
				}
				$xml->moveToElement();


				if($xml->depth > 0)
				{
					$curDepth = $xml->depth;
					$arObjectNames[$curDepth] = $xml->name;
					$curName = $xml->name;
					$curValue = null;
					$curNamespace = ($xml->namespaceURI ? $xml->namespaceURI : null);

					$xml->read();
					if($xml->nodeType == \XMLReader::TEXT)
					{
						$curValue = $xml->value;
					}
					else
					{
						$isRead = true;
					}


					$arObjects[$curDepth] = $arObjects[$curDepth - 1]->addChild($curName, $curValue, $curNamespace);
				}

				foreach($arAttributes as $arAttr)
				{
					if(strpos($arAttr['name'], ':')!==false && $arAttr['namespaceURI']) $arObjects[$curDepth]->addAttribute($arAttr['name'], $arAttr['value'], $arAttr['namespaceURI']);
					else $arObjects[$curDepth]->addAttribute($arAttr['name'], $arAttr['value']);
				}
			}
		}
		$xml->close();
		
		return $xmlObj;
	}
	
	public function locateName($name, $flags=0)
	{
		if(file_exists($this->tmpDir.$name))
		{
			return 1;
		}
		return false;
	}
	
	public function statName($name, $flags=0)
	{
		if(file_exists($this->tmpDir.$name))
		{
			return array(
				'name' => $name,
				'index' => 1,
				'crc' => crc32(file_get_contents($this->tmpDir.$name)),
				'size' => filesize($this->tmpDir.$name),
				'mtime' => filemtime($this->tmpDir.$name),
				'comp_size' => filesize($this->tmpDir.$name),
				'comp_method' => 8
			);
		}
		return false;
	}
}
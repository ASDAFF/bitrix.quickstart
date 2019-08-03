<?php
namespace Bitrix\EsolImportxml;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class XMLViewer 
{
	protected $arXPathsMulti = array();
	
	public function __construct($DATA_FILE_NAME='', $SETTINGS_DEFAULT=array())
	{
		$this->filename = $DATA_FILE_NAME;
		$this->params = $SETTINGS_DEFAULT;
		//$this->fl = new \Bitrix\EsolImportxml\FieldList($SETTINGS_DEFAULT);
	}
	
	public function GetXPathsMulti()
	{
		return $this->arXPathsMulti;
	}
	
	public function GetFileStructure()
	{
		$this->arXPathsMulti = array();
		$file = $_SERVER['DOCUMENT_ROOT'].$this->filename;
		//$arXml = simplexml_load_file($file);
		$arXml = $this->getLigthSimpleXml($file);
		$arStruct = array();
		$this->GetStructureFromSimpleXML($arStruct, $arXml);
		
		//$fileEncoding = \Bitrix\EsolImportxml\Utils::GetXmlEncoding($file);
		$fileEncoding = 'utf-8';
		$siteEncoding = \Bitrix\EsolImportxml\Utils::getSiteEncoding();
		if($siteEncoding!=$fileEncoding)
		{
			$arStruct = \Bitrix\Main\Text\Encoding::convertEncodingArray($arStruct, $fileEncoding, $siteEncoding);
		}
		
		return $arStruct;
	}
	
	public function getLigthSimpleXml($fn)
	{
		if(!file_exists($fn))
		{
			return new \SimpleXMLElement('<d></d>');
		}

		if(!class_exists('\XMLReader'))
		{
			return simplexml_load_file($fn);
		}
		
		$xml = new \XMLReader();
		$res = $xml->open($fn);

		$arObjects = array();
		$arObjectNames = array();
		$arXPaths = array();
		$arValues = array();
		$arXPathsMulti = array();
		$curDepth = 0;
		$isRead = false;
		$maxTime = 10;
		$beginTime = time();
		while(($isRead || $xml->read()) && $endTime-$beginTime < $maxTime) 
		{
			$isRead = false;
			if($xml->nodeType == \XMLReader::ELEMENT) 
			{
				$curDepth = $xml->depth;
				$arObjectNames[$curDepth] = $xml->name;
				$extraDepth = $curDepth + 1;
				while(isset($arObjectNames[$extraDepth]))
				{
					unset($arObjectNames[$extraDepth]);
					$extraDepth++;
				}
				$xPath = implode('/', $arObjectNames);
				
				$arAttributes = array();
				if($xml->moveToFirstAttribute())
				{
					if(!isset($arXPaths[$xPath.'/@'.$xml->name]))
					{
						$arXPaths[$xPath.'/@'.$xml->name] = $xPath.'/@'.$xml->name;
						$arAttributes[] = array('name'=>$xml->name, 'value'=>$xml->value, 'namespaceURI'=>$xml->namespaceURI);
					}
					while($xml->moveToNextAttribute ())
					{
						if(!isset($arXPaths[$xPath.'/@'.$xml->name]))
						{
							$arXPaths[$xPath.'/@'.$xml->name] = $xPath.'/@'.$xml->name;
							$arAttributes[] = array('name'=>$xml->name, 'value'=>$xml->value, 'namespaceURI'=>$xml->namespaceURI);
						}
					}
				}
				$xml->moveToElement();
				$xmlName = $xml->name;
				$xmlNamespaceURI = $xml->namespaceURI;
				$xmlValue = null;
				$isSubRead = false;
				while(($xml->read() && ($isSubRead = true)) && ($xml->nodeType == \XMLReader::SIGNIFICANT_WHITESPACE)){}
				if($xml->nodeType == \XMLReader::TEXT || $xml->nodeType == \XMLReader::CDATA)
				{
					$xmlValue = $xml->value;
				}
				else
				{
					$isRead = $isSubRead;
				}
				
				$setObj = false;
				if(!isset($arXPaths[$xPath]) || (isset($xmlValue) && !isset($arValues[$xPath])))
				{
					$setObj = true;
					$arXPaths[$xPath] = $xPath;
					$curName = $xmlName;
					$curValue = null;
					$curNamespace = null;
					$nsPrefix = '';
					if($xmlNamespaceURI && strpos($curName, ':')!==false)
					{
						$curNamespace = $xmlNamespaceURI;
						$nsPrefix = substr($curName, 0, strpos($curName, ':'));
					}
					if(isset($xmlValue))
					{
						$curValue = $xmlValue;
						if(strlen(trim($curValue)) > 0) $arValues[$xPath] = true;
					}

					if($curDepth == 0)
					{
						if(strlen($nsPrefix) > 0)
							$xmlObj = new \SimpleXMLElement('<'.$nsPrefix.':'.$curName.'></'.$nsPrefix.':'.$curName.'>');
						else
							$xmlObj = new \SimpleXMLElement('<'.$curName.'></'.$curName.'>');
						$arObjects[$curDepth] = &$xmlObj;
					}
					else
					{
						$parentXPath = implode('/', array_slice(explode('/', $xPath), 0, -1));
						$parentDepth = $curDepth - 1;
						/*$arObjects[$parentDepth] = $xmlObj->xpath('/'.$parentXPath);
						if(is_array($arObjects[$parentDepth])) $arObjects[$parentDepth] = current($arObjects[$parentDepth]);*/
						if($curNamespace) $xmlObj->registerXPathNamespace($nsPrefix, $curNamespace);
						$arParentObject = $xmlObj->xpath('/'.$parentXPath);
						if(is_array($arParentObject) && !empty($arParentObject))
						{
							$arObjects[$parentDepth] = current($arParentObject);
						}
						/*else
						{
							$arParentPath = explode('/', $parentXPath);
							array_shift($arParentPath);
							$subObj = $xmlObj;
							while((count($arParentPath) > 0) && ($subPath = array_shift($arParentPath)) && isset($subObj->{$subPath}))
							{
								$subObj = $subObj->{$subPath};
							}
							if(empty($arParentPath) && is_object($subObj) && !empty($subObj))
							{
								$arObjects[$parentDepth] = $subObj;
							}
						}*/
						
						$curValue = str_replace('&', '&amp;', $curValue);
						$arObjects[$curDepth] = $arObjects[$parentDepth]->addChild($curName, $curValue, $curNamespace);
					}
				}
				elseif(!isset($arXPathsMulti[$xPath]))
				{
					$arXPathsMulti[$xPath] = true;
				}

				if(!empty($arAttributes))
				{
					if(!$setObj)
					{
						$arObjects[$curDepth] = $xmlObj->xpath('/'.$xPath);
						if(is_array($arObjects[$curDepth])) $arObjects[$curDepth] = current($arObjects[$curDepth]);
					}
					foreach($arAttributes as $arAttr)
					{
						if(strpos($arAttr['name'], ':')!==false && $arAttr['namespaceURI']) $arObjects[$curDepth]->addAttribute($arAttr['name'], $arAttr['value'], $arAttr['namespaceURI']);
						else $arObjects[$curDepth]->addAttribute($arAttr['name'], $arAttr['value']);
					}
				}
				$endTime = time();
			}
		}
		$xml->close();
		$this->arXPathsMulti = array_keys($arXPathsMulti);
		return $xmlObj;
	}
	
	public function GetStructureFromSimpleXML(&$arStruct, $simpleXML, $level = 0, $nsKey = false)
	{
		if(!($simpleXML instanceof \SimpleXMLElement)) return;
		if($level==0)
		{
			$k = $simpleXML->getName();
			while(count(explode(':', $k)) > 2) $k = substr($k, strpos($k, ':') + 1);
			$arStruct[$k] = array();
			$attrs = $simpleXML->attributes();
			if(!empty($attrs) && $attrs instanceof \Traversable)
			{
				$arStruct[$k]['@attributes'] = array();
				foreach($attrs as $k2=>$v2)
				{
					$arStruct[$k]['@attributes'][$k2] = (string)$v2;
				}
			}
			$this->GetStructureFromSimpleXML($arStruct[$k], $simpleXML, ($level + 1));
			return;
		}
		
		$nss = $simpleXML->getNamespaces(true);
		if($nsKey!==false && isset($nss[$nsKey])) $nss = array($nsKey => $nss[$nsKey]);
		foreach($nss as $key=>$ns)
		{
			foreach($simpleXML->children($ns) as $k=>$v)
			{
				$k = $key.':'.$k;
				
				if(!isset($arStruct[$k]))
				{
					$arStruct[$k] = array();
				}
				$attrs = $v->attributes();
				if(!empty($attrs) && $attrs instanceof \Traversable)
				{
					if(!isset($arStruct[$k]['@attributes']))
					{
						$arStruct[$k]['@attributes'] = array();
					}
					foreach($attrs as $k2=>$v2)
					{
						if(!isset($arStruct[$k]['@attributes'][$k2]))
						{
							$arStruct[$k]['@attributes'][$k2] = (string)$v2;
						}
					}
				}
				if(strlen((string)$v) > 0 && !isset($arStruct[$k]['@value']))
				{
					$arStruct[$k]['@value'] = trim((string)$v);
				}
				if($v instanceof \Traversable)
				{
					$this->GetStructureFromSimpleXML($arStruct[$k], $v, ($level + 1), $key);
				}
			}
		}
		
		//$arCounts = array();
		if($nsKey===false)
		{
			foreach($simpleXML as $k=>$v)
			{
				/*if(!isset($arCounts[$k])) $arCounts[$k] = 0;
				$arCounts[$k]++;*/
				
				if(!isset($arStruct[$k]))
				{
					$arStruct[$k] = array();
				}
				$attrs = $v->attributes();
				if(!empty($attrs) && $attrs instanceof \Traversable)
				{
					if(!isset($arStruct[$k]['@attributes']))
					{
						$arStruct[$k]['@attributes'] = array();
					}
					foreach($attrs as $k2=>$v2)
					{
						if(!isset($arStruct[$k]['@attributes'][$k2]))
						{
							$arStruct[$k]['@attributes'][$k2] = (string)$v2;
						}
					}
				}
				if(strlen((string)$v) > 0 && !isset($arStruct[$k]['@value']))
				{
					$arStruct[$k]['@value'] = trim((string)$v);
				}
				if($v instanceof \Traversable)
				{
					$this->GetStructureFromSimpleXML($arStruct[$k], $v, ($level + 1));
				}
			}
		}
		
		/*foreach($arCounts as $k=>$cnt)
		{
			if(!isset($arStruct[$k]['@count']) || $cnt > $arStruct[$k]['@count'])
			{
				$arStruct[$k]['@count'] = $cnt;
			}
		}*/
		return $arStruct;
	}
	
	public function ShowXmlTag($arStruct)
	{
		foreach($arStruct as $k=>$v)
		{
			echo '<div class="esol_ix_xml_struct_item" data-name="'.htmlspecialcharsex($k).'">';
			echo '&lt;<a href="javascript:void(0)" onclick="EIXPreview.ShowBaseElements(this)" class="esol_ix_open_tag">'.$k.'</a>';
			if(is_array($v) && !empty($v['@attributes']))
			{
				foreach($v['@attributes'] as $k2=>$v2)
				{
					echo ' '.$k2.'="<span class="esol_ix_str_value" data-attr="'.htmlspecialcharsex($k2).'"><span class="esol_ix_str_value_val" title="'.htmlspecialcharsex($v2).'">'.$this->GetShowVal($v2).'</span></span>"';
				}
				unset($v['@attributes']);
			}
			echo '&gt;';
			/*if(is_array($v) && isset($v['@value']))
			{
				echo '<span class="esol_ix_str_value"><span class="esol_ix_str_value_val">'.$this->GetShowVal($v['@value']).'</span></span>';
				unset($v['@value']);
			}*/
			if((is_array($v) && isset($v['@value'])) || empty($v))
			{
				$val = ((is_array($v) && isset($v['@value'])) ? $v['@value'] : '');
				echo '<span class="esol_ix_str_value"><span class="esol_ix_str_value_val" title="'.htmlspecialcharsex($val).'">'.$this->GetShowVal($val).'</span></span>';
			}
			if(is_array($v) && isset($v['@value'])) 
			{
				unset($v['@value']);
			}
			
			if(is_array($v) && !empty($v))
			{
				$this->ShowXmlTagChoose();
				foreach($v as $k2=>$v2)
				{
					if(substr($k2, 0, 1)!='@')
					{
						$this->ShowXmlTag(array($k2=>$v2));
					}
				}
				echo '&lt;/'.$k.'&gt;';
			}
			else
			{
				echo '&lt;/'.$k.'&gt;';
				$this->ShowXmlTagChoose();
			}
			echo '</div>';
		}
	}
	
	public function GetShowVal($v)
	{
		if(strlen(trim($v)) > 50) $v = substr($v, 0, 50).'...';
		elseif(strlen(trim($v)) == 0) $v = '...';
		if($this->params['HTML_ENTITY_DECODE']=='Y')
		{
			$v = html_entity_decode($v);
		}
		$v = htmlspecialcharsex($v);
		return $v;
	}
	
	public function ShowXmlTagChoose()
	{
		//echo '<a href="javascript:void(0)" onclick="" class="esol_ix_dropdown_btn"></a>';
		echo '<span class="esol_ix_group_value"></span>';
	}
	
	public function GetAvailableTags(&$arTags, $path, $arStruct)
	{
		$arTags[$path] = Loc::getMessage("ESOL_IX_VALUE").' '.$path;
		foreach($arStruct as $k=>$v)
		{
			if($k == '@attributes')
			{
				foreach($v as $k2=>$v2)
				{
					$arTags[$path.'/@'.$k2] = Loc::getMessage("ESOL_IX_ATTRIBUTE").' '.$path.'/@'.$k2;
				}
				continue;
			}
			
			if(substr($k, 0, 1)=='@')
			{
				continue;
			}
			
			$this->GetAvailableTags($arTags, $path.'/'.$k, $arStruct[$k]);
		}
	}
}
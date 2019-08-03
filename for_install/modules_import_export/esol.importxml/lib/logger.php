<?php
namespace Bitrix\EsolImportxml;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class Logger {
	protected static $moduleId = 'esol.importxml';
	
	function __construct($saveLog = false, $profileId = 0)
	{
		$this->saveLog = (bool)$saveLog;
		$this->profileId = (int)$profileId;
	}
	
	public function SetNewElement($ID, $type="update")
	{
		if(!$this->saveLog) return false;
		
		$this->elementID = $ID;
		$this->typeChanges = $type;
		$this->elemFields = array();
	}
	
	public function AddElementChanges($type, $arFields, $arOldFields=array())
	{
		if(!$this->saveLog) return false;
		if(!is_array($arOldFields)) $arOldFields = array();
		
		if(is_array($arFields))
		{
			foreach($arFields as $k=>$v)
			{
				$key = $type.$k;
				$this->elemFields[$key] = array(
					'OLDVALUE' => (isset($arOldFields[$k]) ? $arOldFields[$k] : ''),
					'VALUE' => $v
				);
			}
		}
	}
	
	public function SaveElementChanges()
	{
		if(!$this->saveLog) return false;
		if((!is_array($this->elemFields) || empty($this->elemFields)) && (ToUpper($this->typeChanges)!='DELETE')) return false;
		\CEventLog::Add(array(
			"SEVERITY" => "INFO_".$this->profileId,
			"AUDIT_TYPE_ID" => "ESOL_IX_PROFILE_".$this->profileId,
			"MODULE_ID" => static::$moduleId,
			"ITEM_ID" => 'ELEMENT_'.ToUpper($this->typeChanges).'_'.$this->elementID,
			"DESCRIPTION" => serialize($this->elemFields),
		));
	}
	
	public function SaveElementNotFound($arFilter)
	{
		if(!$this->saveLog) return false;
		\CEventLog::Add(array(
			"SEVERITY" => "INFO_".$this->profileId,
			"AUDIT_TYPE_ID" => "ESOL_IX_PROFILE_".$this->profileId,
			"MODULE_ID" => static::$moduleId,
			"ITEM_ID" => 'ELEMENT_NOT_FOUND',
			"DESCRIPTION" => serialize($arFilter)
		));
	}
	
	public function PrepareFieldList()
	{
		if(isset($this->fl)) return;
		$this->fl = new \Bitrix\EsolImportxml\FieldList();
	}
	
	public function GetElementDescriptionArray($description)
	{
		if(!$description) return '';
		$arFields = unserialize(htmlspecialcharsback($description));
		$val = '<pre>'.print_r($arFields, true).'</pre>';
		//$val = str_replace("\t", '<span style="display: inline-block; width: 15px;"></span>', $val);
		return $val;
	}
	
	public function GetElementDescription($description)
	{
		if(!$description) return '';
		$this->PrepareFieldList();
		
		$arFields = unserialize(htmlspecialcharsback($description));
		
		$arFieldsElement = array();
		$arFieldsProduct = array();
		$arFieldsProductStores = array();
		$arFieldsProductDiscount = array();
		$arFieldsProps = array();
		$arFieldsSections = array();
		$arFieldsIpropTemp = array();
		foreach($arFields as $fk=>$fv)
		{
			if(strpos($fk, 'IE_')===0)
			{
				$arFieldsElement[$fk] = $fv;
			}
			elseif(strpos($fk, 'ISECT')===0)
			{
				
			}
			elseif(strpos($fk, 'ICAT_DISCOUNT_')===0)
			{
				$arFieldsProductDiscount[$fk] = $fv;
			}
			elseif(strpos($fk, 'ICAT_')===0)
			{
				$arFieldsProduct[$fk] = $fv;
			}
			elseif(strpos($fk, 'IP_PROP')===0)
			{
				$arFieldsProps[$fk] = $fv;
			}
			elseif(strpos($fk, 'IPROP_TEMP_')===0)
			{
				$arFieldsIpropTemp[$fk] = $fv;
			}
		}
		
		$newDesc = '';
		if(!empty($arFieldsElement))
		{
			$arFieldNames = $this->fl->GetIblockElementFields();
			$newDesc .= '<p><b>'.Loc::getMessage("ESOL_IX_EVENTRES_GROUP_FIELDS").'</b></p><ul>';
			foreach($arFieldsElement as $k=>$v)
			{
				if(!isset($arFieldNames[$k]))
				{
					if($k=='IE_IBLOCK_SECTION')
					{
						$value = $v['VALUE'];
						if(!is_array($value)) $value = array($value);
						$value = implode(', ', $value);
						$oldvalue = $arFieldsElement['IE_IBLOCK_SECTION_ID']['OLDVALUE'];
						$newDesc .= '<li><b>'.Loc::getMessage("ESOL_IX_EVENTRES_SECTION_ID").':</b> ';
						if($value) $newDesc .= $value;
						if($oldvalue)
						{
							$newDesc .= '<div><b>'.Loc::getMessage("ESOL_IX_EVENTRES_OLD_VALUE").'</b> '.$oldvalue.'</div>';
						}
						$newDesc .= '</li>';
					}
					continue;
				}
				$value = (!is_array($v['VALUE']) ? $v['VALUE'] : print_r($v['VALUE'], true));
				$oldvalue = (!is_array($v['OLDVALUE']) ? $v['OLDVALUE'] : print_r($v['OLDVALUE'], true));
				
				$newDesc .= '<li><b>'.$arFieldNames[$k]['name'].':</b> ';
				if($value) $newDesc .= $value;
				if($oldvalue)
				{
					$newDesc .= '<div><b>'.Loc::getMessage("ESOL_IX_EVENTRES_OLD_VALUE").'</b> '.$oldvalue.'</div>';
				}
				$newDesc .= '</li>';
			}
			$newDesc .= '</ul>';
		}
		if(!empty($arFieldsProps))
		{
			$arFieldProps = $this->fl->GetAllIblockProperties();
			$newDesc .= '<p><b>'.Loc::getMessage("ESOL_IX_EVENTRES_GROUP_PROPERTIES").'</b></p><ul>';
			foreach($arFieldsProps as $k=>$v)
			{
				if(!isset($arFieldProps[$k])) continue;
				$propName = $arFieldProps[$k]["NAME"].' ['.$arFieldProps[$k]["CODE"].']';
				$arProp = $arFieldProps[$k];
				
				$value = $this->GetPropertyValue($arProp, $v['VALUE']);
				$oldvalue = $this->GetPropertyValue($arProp, $v['OLDVALUE']);
				
				$value = (!is_array($value) ? $value : print_r($value, true));
				$oldvalue = (!is_array($oldvalue) ? $oldvalue : print_r($oldvalue, true));
				
				$newDesc .= '<li><b>'.$propName.':</b> ';
				if($value) $newDesc .= $value;
				if($oldvalue)
				{
					$newDesc .= '<div><b>'.Loc::getMessage("ESOL_IX_EVENTRES_OLD_VALUE").'</b> '.$oldvalue.'</div>';
				}
				$newDesc .= '</li>';
			}
			$newDesc .= '</ul>';
		}
		if(!empty($arFieldsProduct))
		{
			$arFieldNames = $this->fl->GetCatalogFieldsCached();
			$newDesc .= '<p><b>'.Loc::getMessage("ESOL_IX_EVENTRES_GROUP_CATALOG").'</b></p><ul>';
			foreach($arFieldsProduct as $k=>$v)
			{
				if(!isset($arFieldNames[$k])) continue;
				$value = (!is_array($v['VALUE']) ? $v['VALUE'] : print_r($v['VALUE'], true));
				$oldvalue = (!is_array($v['OLDVALUE']) ? $v['OLDVALUE'] : print_r($v['OLDVALUE'], true));
				
				$newDesc .= '<li><b>'.$arFieldNames[$k].':</b> ';
				if($value) $newDesc .= $value;
				if($oldvalue)
				{
					$newDesc .= '<div><b>'.Loc::getMessage("ESOL_IX_EVENTRES_OLD_VALUE").'</b> '.$oldvalue.'</div>';
				}
				$newDesc .= '</li>';
			}
			$newDesc .= '</ul>';
		}
		
		if(strlen($newDesc) > 0) $newDesc = '<div style="min-width: 500px;">'.$newDesc.'</div>';
		return $newDesc;
	}
	
	public function GetPropertyValue($arProp, $val)
	{
		if(is_array($val))
		{
			if(in_array($arProp['PROPERTY_TYPE'], array('L', 'E', 'G'))
			|| ($arProp['PROPERTY_TYPE']=='S' && $arProp['USER_TYPE']=='directory'))
			{
				foreach($val as $k=>$v)
				{
					$val[$k] = $this->GetPropertyValue($arProp, $v);
				}
			}
		}
		else
		{
			if($arProp['PROPERTY_TYPE']=='L')
			{
				$val = $this->GetPropertyListValue($arProp, $val);
			}
			elseif($arProp['PROPERTY_TYPE']=='E')
			{
				$val = $this->GetPropertyElementValue($arProp, $val);
			}
			elseif($arProp['PROPERTY_TYPE']=='G')
			{
				$val = $this->GetPropertySectionValue($arProp, $val);
			}
			/*elseif($arProp['PROPERTY_TYPE']=='F')
			{
				$val = $this->GetFileValue($val);
			}*/
			elseif($arProp['PROPERTY_TYPE']=='S' && $arProp['USER_TYPE']=='directory')
			{
				$val = $this->GetHighloadBlockValue($arProp, $val);
			}
		}

		return $val;
	}
	
	public function GetHighloadBlockValue($arProp, $val)
	{
		if($val && Loader::includeModule('highloadblock') && $arProp['USER_TYPE_SETTINGS']['TABLE_NAME'])
		{
			if(!isset($this->propVals[$arProp['ID']][$val]))
			{
				if(!$this->hlbl[$arProp['ID']])
				{
					$hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('TABLE_NAME'=>$arProp['USER_TYPE_SETTINGS']['TABLE_NAME'])))->fetch();
					$entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
					$this->hlbl[$arProp['ID']] = $entity->getDataClass();
				}
				$entityDataClass = $this->hlbl[$arProp['ID']];
				
				$dbRes2 = $entityDataClass::GetList(array('filter'=>array("UF_XML_ID"=>$val), 'select'=>array('ID', 'UF_NAME'), 'limit'=>1));
				if($arr2 = $dbRes2->Fetch())
				{
					$this->propVals[$arProp['ID']][$val] = $arr2['UF_NAME'];
				}
				else
				{
					$this->propVals[$arProp['ID']][$val] = '';
				}
			}
			return $this->propVals[$arProp['ID']][$val];
		}
		return $val;
	}
	
	public function GetFileValue($val)
	{
		if($val)
		{
			$arFile = \Bitrix\EsolImportxml\Utils::GetFileArray($val);
			if($arFile)
			{
				$val = $arFile['SRC'];
			}
			else
			{
				$val = '';
			}
		}
		return $val;
	}
	
	public function GetPropertySectionValue($arProp, $val)
	{
		if($val)
		{
			if(!isset($this->propVals[$arProp['ID']][$val]))
			{
				$dbRes = \CIBlockSection::GetList(array(), array("ID"=>$val), false, array('NAME'));
				if($arSect = $dbRes->Fetch())
				{
					$this->propVals[$arProp['ID']][$val] = $arSect['NAME'];
				}
				else
				{
					$this->propVals[$arProp['ID']][$val] = '';
				}
			}
			$val = $this->propVals[$arProp['ID']][$val];
		}
		return $val;
	}
	
	public function GetPropertyElementValue($arProp, $val)
	{
		if($val)
		{
			if(!isset($this->propVals[$arProp['ID']][$val]))
			{
				$dbRes = \CIBlockElement::GetList(array(), array("ID"=>$val), false, false, array('NAME'));
				if($arElem = $dbRes->Fetch())
				{
					$this->propVals[$arProp['ID']][$val] = $arElem['NAME'];
				}
				else
				{
					$this->propVals[$arProp['ID']][$val] = '';
				}
			}
			$val = $this->propVals[$arProp['ID']][$val];
		}
		return $val;
	}
	
	public function GetPropertyListValue($arProp, $val)
	{
		if($val)
		{
			if(!isset($this->propVals[$arProp['ID']][$val]))
			{
				$dbRes = \CIBlockPropertyEnum::GetList(array(), array("PROPERTY_ID"=>$arProp['ID'], "ID"=>$val));
				if($arPropEnum = $dbRes->Fetch())
				{
					$this->propVals[$arProp['ID']][$val] = $arPropEnum['VALUE'];
				}
				else
				{
					$this->propVals[$arProp['ID']][$val] = '';
				}
			}
			$val = $this->propVals[$arProp['ID']][$val];
		}
		return $val;
	}
}
?>
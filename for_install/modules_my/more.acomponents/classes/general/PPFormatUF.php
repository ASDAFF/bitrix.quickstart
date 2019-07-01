<?
/**
 * Copyright (c) 2019 Created by ASDAFF asdaff.asad@yandex.ru
 */

IncludeModuleLangFile(__FILE__);

class CPPFormatUF extends CPPFormat {
	protected $arEntityMeta;
	protected $UF_MANAGER;
	protected $binit;
	public $mseparator;
	
	public function __construct() {
		parent::__construct();
		$this->mseparator = " / ";
		$this->UF_MANAGER = $GLOBALS["USER_FIELD_MANAGER"];
		$this->binit = false;
	}
	
	public function Init($entity_id,$lang=LANGUAGE_ID) {
		if (!isset($entity_id)) {
			$this->binit = false;
		} else {
			$this->arEntityMeta = $this->UF_MANAGER->GetUserFields($entity_id,0,$lang);
			$this->binit = true;
		}
		return $this->binit;
	}
	
	public function IsInit() {
		return $this->binit;
	}
	
	public function SetFormatted($arFormatted=Array()) {
		if ($this->IsInit() && is_array($arFormatted) && count($arFormatted) > 0) {
			$this->arFormatted = $arFormatted;
			$this->ProccessFormattedUF();
			return true;
		}
		return false;
	}
	
	protected function ProccessFormattedUF() {
		if ($this->IsInit() && count($this->arEntityMeta) > 0 && count($this->arFormatted) > 0) {
			foreach ($this->arFormatted as $pid) {
				$arUFProps = $this->arEntityMeta[$pid];
				if ($arUFProps['USER_TYPE_ID'] == "enumeration") {
					$rsfieldsenum = CUserFieldEnum::GetList(array("SORT"=>"ASC"),array("USER_FIELD_ID"=>$arUFProps['ID']));
					while ($arFieldEnum = $rsfieldsenum->GetNext()) {
						$this->arEntityMeta[$pid]['PP_VALUES'][$arFieldEnum['ID']] = $arFieldEnum['VALUE'];
					}
				}		
			}			
			return true;
		}
		return false;
	}
			
	public function GetEntityMeta() {
		if ($this->IsInit()) {
			return $this->arEntityMeta;
		}
		return false;
	}
	
	public function GetDispayFields(&$arElement) {
		$arElement['DISPLAY_PROPERTIES'] = Array();
		if ($this->IsInit() && count($this->arEntityMeta) > 0 && count($this->arFormatted) > 0) {
			foreach ($this->arFormatted as $pid) {
				$arValue['VALUE'] = $arElement[$pid];
				$arValue['~VALUE'] = $arElement["~".$pid];
				$arUFProps = $this->arEntityMeta[$pid];
				if((is_array($arValue['VALUE']) && count($arValue['VALUE']) > 0) || (!is_array($arValue['VALUE']) && strlen($arValue['VALUE']) > 0)) {
					if ($arUFProps['MULTIPLE'] == "Y") {
						if (!is_array($arValue['VALUE'])) $arValue['VALUE'] = Array($arValue['VALUE']);
						if (!is_array($arValue['~VALUE'])) $arValue['~VALUE'] = Array($arValue['~VALUE']);						
					}
										
					$arElement["DISPLAY_PROPERTIES"][$pid] = $this->__GetDispayProperties($arValue,$arUFProps,$pid);
				}
			}
		}
	}
	
	private function __GetDispayProperties($arValue,$arUFProps,$pid) {
		$arParams = $this->paramformatclass->GetParam($pid);
		$events = GetModuleEvents("more.acomponents", "OnFormatUF");
		while ($arEvent = $events->Fetch()) {
			$arEventRes = ExecuteModuleEventEx($arEvent, array($arValue,$arUFProps,$pid,$arParams));
		}
		if (is_array($arEventRes)) {
			return $arEventRes;
		}
		
		if ($arUFProps['LIST_COLUMN_LABEL']) {
			$arReturn['NAME'] = $arUFProps['LIST_COLUMN_LABEL'];
		} else {
			$arReturn['NAME'] = $pid;
		}
		if ($arUFProps['USER_TYPE_ID'] == "enumeration") {
			if ($arUFProps['MULTIPLE'] == "Y") {
				foreach ($arValue['VALUE'] as $key=>$value) {
					$arReturn['FORMATTED_VALUE'][$key] = $arUFProps['PP_VALUES'][$value];
				}
				$arReturn['DISPLAY_VALUE'] = implode($this->mseparator,$arReturn['FORMATTED_VALUE']);
			} else {
				$arReturn['FORMATTED_VALUE'] = $arUFProps['PP_VALUES'][$arValue['VALUE']];
				$arReturn['DISPLAY_VALUE'] = $arReturn['FORMATTED_VALUE'];
			}	
		} elseif ($arUFProps['USER_TYPE_ID'] == "iblock_section") {
			$arFilter = Array();
			if ($arUFProps['SETTINGS']['ACTIVE_FILTER'] == "Y") {
				$arFilter['ACTIVE'] = "Y";
			}
			if ($arUFProps['SETTINGS']['IBLOCK_ID']) {
				$arFilter['IBLOCK_ID'] = $arUFProps['SETTINGS']['IBLOCK_ID'];
			}
			if ($arUFProps['MULTIPLE'] == "Y") {
				foreach ($arValue['VALUE'] as $key=>$value) {
					$value = intval($value);
					if ($value > 0) {
						if(!array_key_exists($value,  CPPFormatUF::$CACHE["G"])) {
							$arFilter["=ID"] = $value;								
							$rsSection = CIBlockSection::GetList(Array(), $arFilter, false, array("ID","NAME","IBLOCK_ID","SECTION_PAGE_URL"));
							 CPPFormatUF::$CACHE["G"][$value] = $rsSection->GetNext();
						}
				
						if(is_array(CPPFormatUF::$CACHE["G"][$value])) {
							$arReturn['FORMATTED_VALUE'][$key] =  CPPFormatUF::$CACHE["G"][$value]["NAME"];
							$arReturn['VALUE_LINK'][$key]='<a href="'. CPPFormatUF::$CACHE["G"][$value]["SECTION_PAGE_URL"].'">'. CPPFormatUF::$CACHE["G"][$value]["NAME"].'</a>';
						}
					}
				}
				$arReturn['DISPLAY_VALUE'] = implode($this->mseparator,$arReturn['VALUE_LINK']);	
			} else {
				$value = intval($arValue['VALUE']);
				if ($value > 0) {
					if(!array_key_exists($value, CPPFormatUF::$CACHE["G"])) {
						$arFilter["=ID"] = $value;
						$rsSection = CIBlockSection::GetList(Array(), $arFilter, false, array("ID","NAME","IBLOCK_ID","SECTION_PAGE_URL"));
						 CPPFormatUF::$CACHE["G"][$value] = $rsSection->GetNext();
					}
					if(is_array( CPPFormatUF::$CACHE["G"][$value])) {
						$arReturn['FORMATTED_VALUE'] =  CPPFormatUF::$CACHE["G"][$value]["NAME"];
						$arReturn['VALUE_LINK']='<a href="'. CPPFormatUF::$CACHE["G"][$value]["SECTION_PAGE_URL"].'">'. CPPFormatUF::$CACHE["G"][$value]["NAME"].'</a>';
						$arReturn['DISPLAY_VALUE'] = $arReturn['VALUE_LINK'];
					}
				}
			}
		} elseif ($arUFProps['USER_TYPE_ID'] == "iblock_element") {
			$arFilter = Array();
			if ($arUFProps['SETTINGS']['ACTIVE_FILTER'] == "Y") {
				$arFilter['ACTIVE'] = "Y";
			}
			if ($arUFProps['SETTINGS']['IBLOCK_ID']) {
				$arFilter['IBLOCK_ID'] = $arUFProps['SETTINGS']['IBLOCK_ID'];
			}
			if ($arUFProps['MULTIPLE'] == "Y") {
				foreach ($arValue['VALUE'] as $key=>$value) {
					$value = intval($value);
					if ($value > 0) {
						if(!array_key_exists($value,  CPPFormatUF::$CACHE["E"])) {
							$arFilter["=ID"] = $value;						
							$rsElement = CIBlockElement::GetList(Array(), $arFilter, false, false, array("ID","NAME","IBLOCK_ID","DETAIL_PAGE_URL"));
							 CPPFormatUF::$CACHE["E"][$value] = $rsElement->GetNext();
						}
					
						if(is_array( CPPFormatUF::$CACHE["E"][$value])) {
							$arReturn['FORMATTED_VALUE'][$key] =  CPPFormatUF::$CACHE["E"][$value]["NAME"];
							$arReturn['VALUE_LINK'][$key]='<a href="'. CPPFormatUF::$CACHE["E"][$value]["DETAIL_PAGE_URL"].'">'. CPPFormatUF::$CACHE["E"][$value]["NAME"].'</a>';
						}
					}
				}
				$arReturn['DISPLAY_VALUE'] = implode($this->mseparator,$arReturn['VALUE_LINK']);	
			} else {
				$value = intval($arValue['VALUE']);
				if ($value > 0) {
					if(!array_key_exists($value, CPPFormatUF::$CACHE["E"])) {
						$arFilter["=ID"] = $value;							
						$rsElement = CIBlockElement::GetList(Array(), $arFilter, false, false, array("ID","NAME","IBLOCK_ID","DETAIL_PAGE_URL"));
						 CPPFormatUF::$CACHE["E"][$value] = $rsElement->GetNext();
					}
					if(is_array( CPPFormatUF::$CACHE["E"][$value])) {
						$arReturn['FORMATTED_VALUE'] =  CPPFormatUF::$CACHE["E"][$value]["NAME"];
						$arReturn['VALUE_LINK']='<a href="'. CPPFormatUF::$CACHE["E"][$value]["DETAIL_PAGE_URL"].'">'. CPPFormatUF::$CACHE["E"][$value]["NAME"].'</a>';
						$arReturn['DISPLAY_VALUE'] = $arReturn['VALUE_LINK'];
					}
				}
			}
		}  elseif ($arUFProps['USER_TYPE_ID'] == "boolean") {
			if ($arUFProps['MULTIPLE'] == "Y") {
				foreach ($arValue['VALUE'] as $key=>$value) {
					$value = intval($value);
					if ($value === 1) {
						$arReturn['FORMATTED_VALUE'][$key] = getMessage('PP_UF_MOD_YES');
					} elseif ($value === 0) {
						$arReturn['FORMATTED_VALUE'][$key] =  getMessage('PP_UF_MOD_NO');
					}
				}
				$arReturn['DISPLAY_VALUE'] = implode($this->mseparator,$arReturn['FORMATTED_VALUE']);
			} else {
				$value = intval($arValue['VALUE']);
				if ($value === 1) {
					$arReturn['FORMATTED_VALUE'] = getMessage('PP_UF_MOD_YES');
				} elseif ($value === 0) {
					$arReturn['FORMATTED_VALUE'] = getMessage('PP_UF_MOD_NO');
				}
				$arReturn['DISPLAY_VALUE'] = $arReturn['FORMATTED_VALUE'];
			}
		} elseif ($arUFProps['USER_TYPE_ID'] == "video") {				
			if ($arUFProps['MULTIPLE'] == "Y") {
				foreach ($arValue['~VALUE'] as $key=>$value) {
					$arReturn['FORMATTED_VALUE'][$key] = call_user_func(array($arUFProps['USER_TYPE']['CLASS_NAME'], 'GetPublicViewHTML'),$arUFProps,Array("VALUE"=>$value));
				}
				$arReturn['DISPLAY_VALUE'] = implode($this->mseparator,$arReturn['FORMATTED_VALUE']);
			} else {
				$value = $arValue['~VALUE'];
				$arReturn['FORMATTED_VALUE'] = call_user_func(array($arUFProps['USER_TYPE']['CLASS_NAME'], 'GetPublicViewHTML'),$arUFProps,Array("VALUE"=>$value));
				$arReturn['DISPLAY_VALUE'] = $arReturn['FORMATTED_VALUE'];
			}			
		} elseif ($arUFProps['USER_TYPE_ID'] == "file") {				
			if ($arUFProps['MULTIPLE'] == "Y") {
				foreach ($arValue['VALUE'] as $key=>$value) {
					$arReturn['FILE_VALUE'][$key] = CFile::GetFileArray($value);
					$arReturn['FORMATTED_VALUE'][$key] =  "<a href=\"".$arReturn['FILE_VALUE'][$key]['SRC']."\">[".htmlSpecialCharsEx($arReturn['FILE_VALUE'][$key]['ORIGINAL_NAME'])."]</a>";
				}
				$arReturn['DISPLAY_VALUE'] = implode($this->mseparator,$arReturn['FORMATTED_VALUE']);
			} else {
				$value = $arValue['VALUE'];
				$arReturn['FILE_VALUE'] = CFile::GetFileArray($value);
				$arReturn['FORMATTED_VALUE'] = "<a href=\"".$arReturn['FILE_VALUE']['SRC']."\">[".htmlSpecialCharsEx($arReturn['FILE_VALUE']['ORIGINAL_NAME'])."]</a>";
				$arReturn['DISPLAY_VALUE'] = $arReturn['FORMATTED_VALUE'];
			}
		} else {
			//string,double,integer,string_formatted,datetime
			if ($arUFProps['MULTIPLE'] == "Y") {
				foreach ($arValue['VALUE'] as $key=>$value) {
					$arReturn['FORMATTED_VALUE'][$key] = $value;
				}
				$arReturn['DISPLAY_VALUE'] = implode($this->mseparator,$arReturn['FORMATTED_VALUE']);
			} else {
				$value = $arValue['VALUE'];
				$arReturn['FORMATTED_VALUE'] = $value;
				$arReturn['DISPLAY_VALUE'] = $arReturn['FORMATTED_VALUE'];
			}					
		}
		return $arReturn;
	}
}
?>
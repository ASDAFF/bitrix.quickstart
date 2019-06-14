<?
	namespace MHT;
	CModule::IncludeModule('iblock');
	class CustomCML2 extends CIBlockCMLImport{
		
		private $previousXMLIDs = array();

		function ImportProperties($XML_PROPERTIES_PARENT, $IBLOCK_ID)
		{
			$obProperty = new CIBlockProperty;
			$sort = 100;

			// Стандартные свойства.
			$arElementFields = array(
				"CML2_ACTIVE" => $this->mess["IBLOCK_XML2_BX_ACTIVE"],
				"CML2_CODE" => $this->mess["IBLOCK_XML2_SYMBOL_CODE"],
				"CML2_SORT" => $this->mess["IBLOCK_XML2_SORT"],
				"CML2_ACTIVE_FROM" => $this->mess["IBLOCK_XML2_START_TIME"],
				"CML2_ACTIVE_TO" => $this->mess["IBLOCK_XML2_END_TIME"],
				"CML2_PREVIEW_TEXT" => $this->mess["IBLOCK_XML2_ANONS"],
				"CML2_DETAIL_TEXT" => $this->mess["IBLOCK_XML2_DETAIL"],
				"CML2_PREVIEW_PICTURE" => $this->mess["IBLOCK_XML2_PREVIEW_PICTURE"],
			);

			$rs = $this->_xml_file->GetList(
				array("ID" => "asc"),
				array("PARENT_ID" => $XML_PROPERTIES_PARENT),
				array("ID")
			);

			// $ar - <Свойство>
			while($ar = $rs->Fetch())
			{
				$XML_ENUM_PARENT = false;
				$isExternal = false;
				$arProperty = array(
				);
				$rsP = $this->_xml_file->GetList(
					array("ID" => "asc"),
					array("PARENT_ID" => $ar["ID"])
				);

				// $arP - дети тега <Свойство>: <Ид>, <ТипЗначений>, <ВариантыЗначений> и т. д.
				while($arP = $rsP->Fetch())
				{
					if(isset($arP["VALUE_CLOB"]))
						$arP["VALUE"] = $arP["VALUE_CLOB"];

					if($arP["NAME"]==$this->mess["IBLOCK_XML2_ID"])
					{
						$arProperty["XML_ID"] = $arP["VALUE"];
						if(array_key_exists($arProperty["XML_ID"], $arElementFields))
							break;
					}
					elseif($arP["NAME"]==$this->mess["IBLOCK_XML2_NAME"])
						$arProperty["NAME"] = $arP["VALUE"];
					elseif($arP["NAME"]==$this->mess["IBLOCK_XML2_MULTIPLE"])
						$arProperty["MULTIPLE"] = ($arP["VALUE"]=="true") || intval($arP["VALUE"])? "Y": "N";
					elseif($arP["NAME"]==$this->mess["IBLOCK_XML2_BX_SORT"])
						$arProperty["SORT"] = $arP["VALUE"];
					elseif($arP["NAME"]==$this->mess["IBLOCK_XML2_BX_CODE"])
						$arProperty["CODE"] = $arP["VALUE"];
					elseif($arP["NAME"]==$this->mess["IBLOCK_XML2_BX_DEFAULT_VALUE"])
						$arProperty["DEFAULT_VALUE"] = $arP["VALUE"];
					elseif($arP["NAME"]==$this->mess["IBLOCK_XML2_SERIALIZED"])
						$arProperty["SERIALIZED"] = ($arP["VALUE"]=="true") || intval($arP["VALUE"])? "Y": "N";
					elseif($arP["NAME"]==$this->mess["IBLOCK_XML2_BX_PROPERTY_TYPE"])
					{
						$arProperty["PROPERTY_TYPE"] = $arP["VALUE"];
						$arProperty["USER_TYPE"] = "";
					}
					elseif($arP["NAME"]==$this->mess["IBLOCK_XML2_BX_ROWS"])
						$arProperty["ROW_COUNT"] = $arP["VALUE"];
					elseif($arP["NAME"]==$this->mess["IBLOCK_XML2_BX_COLUMNS"])
						$arProperty["COL_COUNT"] = $arP["VALUE"];
					elseif($arP["NAME"]==$this->mess["IBLOCK_XML2_BX_LIST_TYPE"])
						$arProperty["LIST_TYPE"] = $arP["VALUE"];
					elseif($arP["NAME"]==$this->mess["IBLOCK_XML2_BX_FILE_EXT"])
						$arProperty["FILE_TYPE"] = $arP["VALUE"];
					elseif($arP["NAME"]==$this->mess["IBLOCK_XML2_BX_FIELDS_COUNT"])
						$arProperty["MULTIPLE_CNT"] = $arP["VALUE"];
					elseif($arP["NAME"]==$this->mess["IBLOCK_XML2_BX_USER_TYPE"])
						$arProperty["USER_TYPE"] = $arP["VALUE"];
					elseif($arP["NAME"]==$this->mess["IBLOCK_XML2_BX_WITH_DESCRIPTION"])
						$arProperty["WITH_DESCRIPTION"] = ($arP["VALUE"]=="true") || intval($arP["VALUE"])? "Y": "N";
					elseif($arP["NAME"]==$this->mess["IBLOCK_XML2_BX_SEARCH"])
						$arProperty["SEARCHABLE"] = ($arP["VALUE"]=="true") || intval($arP["VALUE"])? "Y": "N";
					elseif($arP["NAME"]==$this->mess["IBLOCK_XML2_BX_FILTER"])
						$arProperty["FILTRABLE"] = ($arP["VALUE"]=="true") || intval($arP["VALUE"])? "Y": "N";
					elseif($arP["NAME"]==$this->mess["IBLOCK_XML2_BX_LINKED_IBLOCK"])
						$arProperty["LINK_IBLOCK_ID"] = $this->GetIBlockByXML_ID($arP["VALUE"]);
					elseif($arP["NAME"]==$this->mess["IBLOCK_XML2_CHOICE_VALUES"]) // <ВариантыЗначений>
						$XML_ENUM_PARENT = $arP["ID"];
					elseif($arP["NAME"]==$this->mess["IBLOCK_XML2_BX_IS_REQUIRED"])
						$arProperty["IS_REQUIRED"] = ($arP["VALUE"]=="true") || intval($arP["VALUE"])? "Y": "N";
					elseif($arP["NAME"]==$this->mess["IBLOCK_XML2_VALUES_TYPE"]) // <ТипЗначений>
					{
						if(
							$arP["VALUE"] == $this->mess["IBLOCK_XML2_TYPE_LIST"] // Справочник
							&& !$isExternal
						)
						{
							$arProperty["PROPERTY_TYPE"] = "L";
							$arProperty["USER_TYPE"] = "";
						}
						elseif($arP["VALUE"] == $this->mess["IBLOCK_XML2_TYPE_NUMBER"])
						{
							$arProperty["PROPERTY_TYPE"] = "N";
							$arProperty["USER_TYPE"] = "";
						}
						elseif($arP["VALUE"] == $this->mess["IBLOCK_XML2_TYPE_STRING"])
						{
							$arProperty["PROPERTY_TYPE"] = "S";
							$arProperty["USER_TYPE"] = "";
						}
					}
					elseif($arP["NAME"]==$this->mess["IBLOCK_XML2_VALUES_TYPES"]) // ??
					{
						//This property metadata contains information about it's type
						$rsTypes = $this->_xml_file->GetList(
							array("ID" => "asc"),
							array("PARENT_ID" => $arP["ID"]),
							array("ID", "LEFT_MARGIN", "RIGHT_MARGIN", "NAME")
						);
						$arType = $rsTypes->Fetch();
						//We'll process only properties with NOT composing types
						//composed types will be supported only as simple string properties
						if($arType && !$rsTypes->Fetch())
						{
							$rsType = $this->_xml_file->GetList(
								array("ID" => "asc"),
								array("PARENT_ID" => $arType["ID"]),
								array("ID", "LEFT_MARGIN", "RIGHT_MARGIN", "NAME", "VALUE")
							);
							while($arType = $rsType->Fetch())
							{
								if($arType["NAME"] == $this->mess["IBLOCK_XML2_TYPE"])
								{
									if($arType["VALUE"] == $this->mess["IBLOCK_XML2_TYPE_LIST"])
										$arProperty["PROPERTY_TYPE"] = "L";
									elseif($arType["VALUE"] == $this->mess["IBLOCK_XML2_TYPE_NUMBER"])
										$arProperty["PROPERTY_TYPE"] = "N";
								}
								elseif($arType["NAME"] == $this->mess["IBLOCK_XML2_CHOICE_VALUES"])
								{
									$XML_ENUM_PARENT = $arType["ID"];
								}
							}
						}
					}
					elseif($arP["NAME"]==$this->mess["IBLOCK_XML2_BX_USER_TYPE_SETTINGS"])
					{
						$arProperty["USER_TYPE_SETTINGS"] = unserialize($arP["VALUE"]);
					}
					elseif($arP["NAME"]==$this->mess["IBLOCK_XML2_EXTERNAL"])
					{
						$isExternal = ($arP["VALUE"]=="true") || intval($arP["VALUE"])? true: false;
						if ($isExternal)
						{
							$arProperty["PROPERTY_TYPE"] = "S";
							$arProperty["USER_TYPE"] = "directory";
						}
					}
				}

				if(array_key_exists($arProperty["XML_ID"], $arElementFields))
					continue;

				// Skip properties with no choice values
				// http://jabber.bx/view.php?id=30476
				$arEnumXmlNodes = array();
				if($XML_ENUM_PARENT) // получаем варианты значений <ВариантыЗначений>
				{
					$rsE = $this->_xml_file->GetList(
						array("ID" => "asc"),
						array("PARENT_ID" => $XML_ENUM_PARENT)
					);
					while($arE = $rsE->Fetch())
					{
						if(isset($arE["VALUE_CLOB"]))
							$arE["VALUE"] = $arE["VALUE_CLOB"];
						$arEnumXmlNodes[] = $arE;
					}

					if (empty($arEnumXmlNodes))
						continue;
				}

				if($arProperty["SERIALIZED"] == "Y")
					$arProperty["DEFAULT_VALUE"] = unserialize($arProperty["DEFAULT_VALUE"]);

				$propertyCode = CUtil::translit($arProperty["NAME"], LANGUAGE_ID, array(
					"max_len" => 50,
					"change_case" => 'U', // 'L' - toLower, 'U' - toUpper, false - do not change
					"replace_space" => '_',
					"replace_other" => '_',
					"delete_repeat_replace" => true,
				));

				if(isset($this->previousXMLIDs[$propertyCode])){
					$arProperty['XML_ID'] = $this->previousXMLIDs[$propertyCode];
				}
				else{
					$this->previousXMLIDs[$propertyCode] = $arProperty['XML_ID'];
				}

				$rsProperty = $obProperty->GetList(array(), array("IBLOCK_ID"=>$IBLOCK_ID, "XML_ID"=>$arProperty["XML_ID"]));
				// если есть свойство
				if($arDBProperty = $rsProperty->Fetch())
				{
					$bChanged = false;
					foreach($arProperty as $key=>$value)
					{
						if($arDBProperty[$key] !== $value)
						{
							$bChanged = true;
							break;
						}
					}
					if(!$bChanged)
						$arProperty["ID"] = $arDBProperty["ID"];
					elseif($obProperty->Update($arDBProperty["ID"], $arProperty))
						$arProperty["ID"] = $arDBProperty["ID"];
					else
						return $obProperty->LAST_ERROR;
				}
				// если свойства нет
				else
				{
					$arProperty["IBLOCK_ID"] = $IBLOCK_ID;
					$arProperty["ACTIVE"] = "Y";
					if(!array_key_exists("PROPERTY_TYPE", $arProperty))
						$arProperty["PROPERTY_TYPE"] = "S";
					if(!array_key_exists("SORT", $arProperty))
						$arProperty["SORT"] = $sort;
					if(!array_key_exists("CODE", $arProperty))
					{
						$arProperty["CODE"] = $propertyCode;
						if(preg_match('/^[0-9]/', $arProperty["CODE"]))
							$arProperty["CODE"] = '_'.$arProperty["CODE"];
					}
					$arProperty["ID"] = $obProperty->Add($arProperty);
					if(!$arProperty["ID"])
						return $obProperty->LAST_ERROR;
				}

				if($XML_ENUM_PARENT)
				{
					if ($isExternal)
						$result = $this->ImportPropertyDirectory($arProperty, $arEnumXmlNodes);
					else
						$result = $this->ImportPropertyEnum($arProperty, $arEnumXmlNodes);

					if ($result !== true)
						return $result;
				}
				$sort += 100;
			}
			return true;
		}
	}
?>
<?
IncludeModuleLangFile(__FILE__);

Class CQubeCombine
{	
	protected function GetPropertyFields()
    {
        $arFields = COption::GetOptionString('qube.combine', 'COMBINE_FIELDS', '');
		if(strlen($arFields) > 0)
			$arFields = unserialize($arFields);
		else
			$arFields = array('CODE');

		if(!$arFields)
			$arFields = array('CODE');
			
        return $arFields;
    }
	protected function SetOtherProperties($ID, $arFields, $arData)
	{
		if (COption::GetOptionString('qube.combine', 'ADMIN_ACTIVE')!="Y")
			return;
			
		if (isset($arData["PAY_SYSTEM_ID"]) && is_array($arData["PAY_SYSTEM_ID"]) && isset($arData["DELIVERY_SYSTEM_ID"]) && is_array($arData["DELIVERY_SYSTEM_ID"]))
		{
			$arData["PAY_SYSTEM_ID"] = array_filter($arData["PAY_SYSTEM_ID"]);
			$arData["DELIVERY_SYSTEM_ID"] = array_filter($arData["DELIVERY_SYSTEM_ID"]);

			if ((count($arData["PAY_SYSTEM_ID"]) > 0) || count($arData["DELIVERY_SYSTEM_ID"]) > 0)
			{
				if ($IS_LOCATION4TAX == "Y") 
				{
					$strError .= GetMessage("ERROR_LOCATION4TAX_RELATION_NOT_ALLOWED")."<br>";
				}
				else if ($IS_EMAIL == "Y")
				{
					$strError .= GetMessage("ERROR_EMAIL_RELATION_NOT_ALLOWED")."<br>";
				}
				else if ($IS_PROFILE_NAME == "Y")
				{
					$strError .= GetMessage("ERROR_PROFILE_NAME_RELATION_NOT_ALLOWED")."<br>";
				}
			}

			if (strlen($strError) <= 0)
			{
				CSaleOrderProps::UpdateOrderPropsRelations($ID, $arData["PAY_SYSTEM_ID"], "P");
				CSaleOrderProps::UpdateOrderPropsRelations($ID, $arData["DELIVERY_SYSTEM_ID"], "D");
			}
			else
				CAdminMessage::ShowMessage($strError);
		}
		if (strlen($strError)<=0)
		{
			$TYPE = $arFields['TYPE'];
			if ($TYPE=="SELECT" || $TYPE=="MULTISELECT" || $TYPE=="RADIO")
			{
				CSaleOrderPropsVariant::DeleteAll($ID);
				$db_vars = CSaleOrderPropsVariant::GetList(
					array("SORT" => "ASC"),
					array("ORDER_PROPS_ID" => $ID)
				);
				$arVariants = false;
				while ($vars = $db_vars->Fetch())
				{
					$arVariants[] = $vars;
				}
				
				$numpropsvals = IntVal($arData['numpropsvals']);
				
				for ($i = 0; $i<=$numpropsvals; $i++)
				{
					$strError1 = "";
					
					$CF_ID = $arVariants[$i]['ID'];
					$CF_DEL = $arData["DELETE_".$i];
					if ($CF_DEL != 'Y')
					{
						unset($arFieldsV);
						$arFieldsV = array(
							"ORDER_PROPS_ID" => $ID,
							"VALUE" => Trim($arData["VALUE_".$i]),
							"NAME" => Trim($arData["NAME_".$i]),
							"SORT" => ( (IntVal($arData["SORT_".$i])>0) ? IntVal($arData["SORT_".$i]) : 100 ),
							"DESCRIPTION" => Trim($arData["DESCRIPTION_".$i])
							);

						if ($CF_ID<=0)
						{
							if (strlen($arFieldsV["VALUE"])>0 && strlen($arFieldsV["NAME"])>0)
							{
								if (!CSaleOrderPropsVariant::Add($arFieldsV))
								{
									$strError1 .= GetMessage("ERROR_ADD_VARIANT")." (".$arFieldsV["VALUE"].", ".$arFieldsV["NAME"].", ".$arFieldsV["SORT"].", ".$arFieldsV["DESCRIPTION"].").<br>";
								}
							}
						}
						else
						{
							if (strlen($arFieldsV["VALUE"])<=0)
								$strError1 .= GetMessage("ERROR_EMPTY_VAR_CODE")." (".$arFieldsV["NAME"].").<br>";

							if (strlen($arFieldsV["NAME"])<=0)
								$strError1 .= GetMessage("ERROR_EMPTY_VAR_NAME")." (".$arFieldsV["VALUE"].").<br>";
						}
						$strError .= $strError1;
					}
				}
			}
		}
		return true;
	}
	function OnAdminContextMenuShowHandler(&$items)
	{
		if($GLOBALS["APPLICATION"]->GetCurPage(true) == "/bitrix/admin/sale_order_props.php" && COption::GetOptionString('qube.combine', 'ADMIN_ACTIVE')=="Y")
		{
			$db_ptype = CSalePersonType::GetList(array("SORT" => "ASC"));
			if ($ptype = $db_ptype->Fetch())
			{
				$PERSON_TYPE_ID = $ptype["ID"];
				$arItems[] = array(
					"TEXT"=> GetMessage('QC_GP_BUTTON_TITLE'),
					"ICON"=>'btn_new',
					"TITLE"=>GetMessage('QC_GP_BUTTON_DESCRIPTION'),
					"LINK"=>"sale_order_props_edit.php?lang=".LANGUAGE_ID.'&PERSON_TYPE_ID='.$PERSON_TYPE_ID,
				);
				$items = array_merge($arItems, $items);
			}	
		}
		return true;
	}
	function OnAdminTabControlBeginHandler(&$form)
	{
	   if($GLOBALS['APPLICATION']->GetCurPage()=='/bitrix/admin/sale_order_props_edit.php' && COption::GetOptionString('qube.combine', 'ADMIN_ACTIVE')=="Y")
	   {
			$bNew = $selected =  false;
			$arTypes = $arPersonTypes = array();

			if (intval($_REQUEST['PERSON_TYPE_ID'])>0 && ((intval($_REQUEST['ID'])==0 || !array_key_exists('ID',$_REQUEST))))
				$bNew = true;
			elseif (intval($_REQUEST['ID'])>0)
			{
				$db_props = CSaleOrderProps::GetList(
					array("SORT" => "ASC"),
					array("ID"=>intval($_REQUEST['ID'])),
					false,
					false,
					array('*')
				);
				
				$arFilter = array();
				$arFields = CQubeCombine::GetPropertyFields(); 
				
				if($arNowProps = $db_props->Fetch())
					foreach ($arFields as $FIELD)
						$arFilter[$FIELD] = $arNowProps[$FIELD];
				
				$db_props = CSaleOrderProps::GetList(
					array("SORT" => "ASC"),
					$arFilter,
					false,
					false,
					array('ID','PERSON_TYPE_ID')
				);
				while ($arProps = $db_props->Fetch())
					if (!in_array($arProps["PERSON_TYPE_ID"],$arTypes))
						$arTypes[] = $arProps['PERSON_TYPE_ID'];
			}
		
			$db_ptype = CSalePersonType::GetList(array("SORT" => "ASC"),array(),false,false,array('ID','NAME'));
			while ($ptype = $db_ptype->Fetch())
				$arPersonTypes[] = $ptype;
			
			//‚ывод контента
			if (count($arPersonTypes)>0)
			{
				if ($bNew || count($arPersonTypes)==count($arTypes))
					$selected = 'selected = "selected"';
					
				$ptForm .= '<select multiple="multiple" size="5" name="PERSON_TYPE_SYSTEM_ID[]">
										<option value="0" '.$selected.'>'.GetMessage('QC_GP_ALL_PERSON_TYPES').'</option>';
										
				foreach  ($arPersonTypes as $ptype)
				{
					if (in_array($ptype["ID"],$arTypes) && count($arPersonTypes) != count($arTypes))
						$selected = 'selected = "selected"';
					else 
						$selected = '';
					$ptForm .=  '<option '.$selected.' value="'.$ptype["ID"].'">'.$ptype["NAME"].'</option>';
				}
				$ptForm .= '</select>';
				$form->tabs[] = array(
					'DIV' 				=> 'person_types', 
					'TAB' 				=> GetMessage('QC_GP_TAB_TITLE'),
					'TITLE' 			=> GetMessage('QC_GP_TAB_DESCRIPTION'),
					'ICON' 			=> 'sale',
					'CONTENT'		=>
						 '<tr >
							<td width="40%">'.GetMessage('QC_GP_TAB_TITLE').':</td>
							<td width="60%">'.$ptForm.'</td>
						 </tr>'
				);
			}
	   }
	   return true;
	}
	function OnBeforeOrderPropsUpdateHandler($ID,&$arFields)
	{
		if (COption::GetOptionString('qube.combine', 'ADMIN_ACTIVE')!="Y")
			return;
			
		$arOpFields = CQubeCombine::GetPropertyFields();
		session_start();

		$db_props = CSaleOrderProps::GetList(
			array("SORT" => "ASC"),
			array('ID'=>$ID),
			false,
			false,
			array()
		);
		$_SESSION['COMBINE'] = array();
		if ($arProp= $db_props->Fetch())
			foreach ($arOpFields as $field)
				$_SESSION['COMBINE'][$field] = $arProp[$field];
				
		return true;
	}
	function OnOrderPropsEdit($ID,$arFields)
	{
		if ($_REQUEST['ID'] == 0)
				$_REQUEST['ID'] =$ID;

		if ($_REQUEST['ID'] ==$ID && $_SERVER['REQUEST_METHOD']=='POST' 
			&& $GLOBALS['APPLICATION']->GetCurPage()=='/bitrix/admin/sale_order_props_edit.php' && intval($ID)>0
				&& COption::GetOptionString('qube.combine', 'ADMIN_ACTIVE')=="Y")
		{
			$arOpFields = CQubeCombine::GetPropertyFields();
			$arFilter = $arTypes = $arTypesAll = array();
			
			if (count($_SESSION['COMBINE'])>0)
				$arFilter = $_SESSION['COMBINE'];
			else
				foreach ($arFields as $field => $v)
					if (in_array($field,$arOpFields))
						$arFilter[$field] = $v;
			
			if (count($arFilter)>0)
			{
				$arFilter["!ID"] = $ID;
				
				$arNoFields = array('ID','PROPS_GROUP_ID','PERSON_TYPE_ID');

				$db_props = CSaleOrderProps::GetList(
					array("SORT" => "ASC"),
					$arFilter,
					false,
					false,
					array()
				);
				$arProperties = $arDelProperty =  array();
				$backId = false;
				while ($arProp= $db_props->Fetch())
				{
					if (is_null($_POST["PERSON_TYPE_SYSTEM_ID"]) 
						|| in_array(0,$_POST["PERSON_TYPE_SYSTEM_ID"])
						|| in_array($arProp['PERSON_TYPE_ID'],$_POST["PERSON_TYPE_SYSTEM_ID"]))
					{
						$arProperties[] = $arProp;
						$arLoadProps	= false;
						$arPersonTypes[] = $arProperty['PERSON_TYPE_ID'];
						foreach ($arFields as $field => $val)
						{
							if ($val != $arProperty[$field] && !in_array($field,$arNoFields))
							{
								$pr = true;
								$arLoadProps[$field] = $val;
							}
						}
						if ($pr == true)
						{
							CSaleOrderProps::Update($arProperty['ID'], $arLoadProps);
							CQubeCombine::SetOtherProperties($arProperty['ID'],$arFields,$_POST);
						}
					}
					else
						CSaleOrderProps::Delete($arProp['ID']);
				}
				
				$arPersonTypes = array();
				foreach ($arProperties as $arProperty)
				{
					$pr = $arLoadProps	= false;
					$arPersonTypes[] = $arProperty['PERSON_TYPE_ID'];
					foreach ($arFields as $field => $val)
					{
						if ($val != $arProperty[$field] && !in_array($field,$arNoFields))
						{
							$pr = true;
							$arLoadProps[$field] = $val;
						}
					}
					if ($pr == true)
					{
						$backId = $arProperty['ID'];
						CSaleOrderProps::Update($arProperty['ID'], $arLoadProps);
						
						CQubeCombine::SetOtherProperties($arProperty['ID'],$arFields,$_POST);
					}
				}
				if (is_null($_POST["PERSON_TYPE_SYSTEM_ID"]) || (in_array(0,$_POST["PERSON_TYPE_SYSTEM_ID"]) && count($_POST["PERSON_TYPE_SYSTEM_ID"])==1))
				{
					$db_ptype = CSalePersonType::GetList(array("SORT" => "ASC"));
					while ($ptype = $db_ptype->Fetch())
					{
						$arTypesAll[] = $ptype["ID"];
						if ($_POST['PERSON_TYPE_ID'] != $ptype["ID"])
							$arTypes[] = $ptype["ID"];
					}
				}
				else
					$arTypesAll = $arTypes = $_POST["PERSON_TYPE_SYSTEM_ID"];
				
				if (count($arTypes)>0)
				{
					foreach ($arTypes as $type)
					{
						if (intval($type)>0 && !in_array($type,$arPersonTypes) && $type != $_POST['PERSON_TYPE_ID'])
						{
							foreach ($arFields as $field => $val)
								if (!in_array($field,$arNoFields))
									$arLoadProps[$field] = $val;
							$arLoadProps['PERSON_TYPE_ID'] = $type;
							 
							$db_propsGroup = CSaleOrderPropsGroup::GetList(
								array("SORT" => "ASC"),
								array("PERSON_TYPE_ID" => $type),
								false,
								false,
								array('ID')
							);

							if ($propsGroup = $db_propsGroup->Fetch())
								$arLoadProps['PROPS_GROUP_ID'] = intval($propsGroup['ID']);
							else
							{
								$arLoadProps['PROPS_GROUP_ID'] = CSaleOrderPropsGroup::Add(array(
									'PERSON_TYPE_ID' => $type,
									'NAME' 			 => "[$type] ".GetMessage('QC_GP_AUTO_CREATE'),
									'SORT'			 => 100,
								));
							}
							$backId = $NEW_ID = CSaleOrderProps::Add($arLoadProps);
							if (intval($NEW_ID)>0)
								CQubeCombine::SetOtherProperties($NEW_ID, $arFields,$_POST);
						}
					}
				}
				
				$bDel = false;
				if (count($arTypesAll)>0)
				{
					foreach ($arTypesAll as  $type)
						if ($type == $_POST['PERSON_TYPE_ID'])
							$bDel = true;
						
					if (!$bDel)
					{
						CSaleOrderProps::Delete($ID);
						if (!empty($_POST['apply']))
							LocalRedirect('/bitrix/admin/sale_order_props_edit.php?ID='.$backId.'&lang='.LANGUAGE_ID);
						if (!empty($_POST['save']))
							LocalRedirect('/bitrix/admin/sale_order_props.php?ID='.$backId.'&lang='.LANGUAGE_ID);
					}
				}
			}
			unset($_SESSION['COMBINE']);
		}
		return true;
	}
	
	function OnSaleComponentOrderOneStepProcessHandler(&$arResult,&$arUserResult,$arParams)
	{
		if (is_array($arUserResult['ORDER_PROP']) && COption::GetOptionString('qube.combine', 'COMPONENT_ACTIVE')=="Y")
		{	
			$arPropValue = array();
			foreach ($arUserResult['ORDER_PROP'] as $key=> $arItem)
				$arPropValue[] = $key;
			
			if (count($arPropValue)>0)
			{
				$arFields = CQubeCombine::GetPropertyFields();
				
				$db_props = CSaleOrderProps::GetList(
					array("PERSON_TYPE_ID" => "ASC","ID" => "ASC"),
					array("ACTIVE" => "Y","ID" => $arPropValue),
					false,
					false,
					array()
				);		
				
				$arOldProperties = $arProperties = array();
				while ($arProp = $db_props->Fetch())
				{
					foreach ($arResult[ 'ORDER_PROP'] as $key=> &$arItem)
					{						
						if ($key == 'USER_PROPS_N' || $key == 'USER_PROPS_Y')
						{
							foreach ($arItem as &$arProperties)
							{
								$bPr = true;
								foreach ($arFields as $field)
								{
									if ($arProperties[$field] != $arProp[$field])
										$bPr = false;
								}
								if ($bPr == true)
								{
									switch ($arProperties['TYPE']) 
									{
										case 'CHECKBOX':
											if ($arUserResult['ORDER_PROP'][$arProp["ID"]]=='Y')
												$arProperties['CHECKED'] = 'Y';
											break;
										case 'RADIO':
											foreach ($arProperties['VARIANTS'] as &$arVariant)
											{
												if ($arVariant['VALUE']==$arUserResult['ORDER_PROP'][$arProp["ID"]])
													$arVariant['CHECKED'] = 'Y';
												else
													unset($arVariant['CHECKED']);
											}
											break;
										case 'SELECT':
											foreach ($arProperties['VARIANTS'] as &$arVariant)
											{
												if ($arVariant['VALUE']==$arUserResult['ORDER_PROP'][$arProp["ID"]])
													$arVariant['SELECTED'] = 'Y';
												else
													unset($arVariant['SELECTED']);
											}
											break;
										case 'MULTISELECT':
											if (is_array($arUserResult['ORDER_PROP'][$arProp["ID"]]))
											{
												foreach ($arUserResult['ORDER_PROP'][$arProp["ID"]] as $val)
												{
													foreach ($arProperties['VARIANTS'] as &$arVariant)
													{
														if ($arVariant['VALUE']==$val)
															$arVariant['SELECTED'] = 'Y';
														elseif (!in_array($val,$arUserResult['ORDER_PROP'][$arProp["ID"]]))
															unset($arVariant['SELECTED']);
													}
												}
											}
											break;
										case 'LOCATION':										
											foreach ($arProperties['VARIANTS'] as &$arVariant)
											{
												if ($arVariant['ID']==$arUserResult['ORDER_PROP'][$arProp["ID"]])
												{
													$arVariant['SELECTED'] = 'Y';
													$arProperties['VALUE'] = $arVariant['NAME'];
												}
												else
													unset($arVariant['SELECTED']);
											}
											$arProperties['VALUE'] = $arUserResult['ORDER_PROP'][$arProp["ID"]];
											break;
										case 'FILE':
											//TODO
											break;
										default:
											$arProperties['VALUE'] = $arUserResult['ORDER_PROP'][$arProp["ID"]];
									}
								}
							}
						}
					}
				}
			}
		}
  
		return true;
	}
}
?>
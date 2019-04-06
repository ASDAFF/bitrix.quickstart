<?
IncludeModuleLangFile(__FILE__);

$GLOBALS["err_m"] = false;

class CCustomTypeSimaiComplex
{
	function GetUserTypeDescription() 
	{
		return array(
			'PROPERTY_TYPE' => 'S',
			'USER_TYPE' => 'simai_complex',
			'DESCRIPTION' => GetMessage('SMCP_COMPLEX_PROP'),
			'PrepareSettings' => array('CCustomTypeSimaiComplex', 'PrepareSettings'),
			'GetSettingsHTML' => array('CCustomTypeSimaiComplex', 'GetSettingsHTML'),
			'GetPropertyFieldHtml' => array('CCustomTypeSimaiComplex', 'GetPropertyFieldHtml'),
			'GetPropertyFieldHtmlMulty' => array('CCustomTypeSimaiComplex', 'GetPropertyFieldHtmlMulty'),
			//'GetPublicEditHTML' => array('CCustomTypeSimaiComplex', 'GetPublicEditHTML'),
			'ConvertToDB' => array('CCustomTypeSimaiComplex', 'ConvertToDB'),
			'ConvertFromDB' => array('CCustomTypeSimaiComplex', 'ConvertFromDB'),
			'GetPublicViewHTML' => array('CCustomTypeSimaiComplex', 'GetPublicViewHTML'),			
			'GetSearchContent' => array('CCustomTypeSimaiComplex', 'GetSearchContent'),
			'GetAdminFilterHTML' => array('CCustomTypeSimaiComplex', 'GetAdminFilterHTML'),
			'GetAdminListViewHTML' => array('CCustomTypeSimaiComplex', 'GetAdminListViewHTML'),
			'GetPublicFilterHTML' => array('CCustomTypeSimaiComplex', 'GetPublicFilterHTML'),
		);
    }
	
    function PrepareSettings($arFields)
    {
        $props = Array();
		$reqs = Array();
		for ($i = 0; $i < 7; $i++)
		{
			$prop = IntVal($arFields["USER_TYPE_SETTINGS"]["SUBPROPS"][$i]);
			$req = IntVal($arFields["USER_TYPE_SETTINGS"]["SUBPROPS_REQ"][$i]);
			if ($prop && !in_array($prop,$props))
			{
				$props[] = $prop;
				$reqs[] = $req;
			}
		}
        return array("SUBPROPS" => $props, "SUBPROPS_REQ" => $reqs);
    }
	
	function GetAllProps($IBLOCK_ID, $PID)
	{
		CModule::IncludeModule("iblock");
		
		$IBLOCK_ID = IntVal($IBLOCK_ID);
		$PID = IntVal($PID);
		$props_ = Array();
		$props_f = Array();
		$props = Array();
		$res = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$IBLOCK_ID));
		while ($arr = $res->Fetch())
		{
			if ($arr["USER_TYPE"] == "simai_complex" && $arr["ID"] != $PID)
			{
				if (isset($arr["USER_TYPE_SETTINGS"]["SUBPROPS"]))
				{
					if (is_array($arr["USER_TYPE_SETTINGS"]["SUBPROPS"]))
					{
						foreach($arr["USER_TYPE_SETTINGS"]["SUBPROPS"] as $p_id)
							$props_f[$p_id] = $p_id;
					}
				}
			}			
			if (!in_array($arr["USER_TYPE"], $GLOBALS["SIMAI_COMPLEXPROP_FORBIDDEN_UT"]))
				$props_[$arr["ID"]] = $arr;
		}
		foreach ($props_ as $p_id => $arr)
		{
			if (!$props_f[$p_id])
				$props[$arr["ID"]] = $arr;
		}		
		return $props;
	}	
	
	function GetSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields)
	{
		$arPropertyFields = array(
			"HIDE" => array("FILTRABLE", "ROW_COUNT", "COL_COUNT", "DEFAULT_VALUE", "WITH_DESCRIPTION", "SEARCHABLE"),
			"SET" => array("FILTRABLE" => "N", "SEARCHABLE" => "N", "SMART_FILTER" => "N", "MULTIPLE_CNT"=>"1"),
			"USER_TYPE_SETTINGS_TITLE" => GetMessage('SMCP_SELECT_SUBPROPS')
		);
		
		$props = self::GetAllProps($_REQUEST["PARAMS"]["IBLOCK_ID"],$_REQUEST["PARAMS"]["ID"]);
		
		$return = "";
		
		for ($i = 0; $i < 7; $i++)
		{
			$return .= '
			<tr>
			<td>'.GetMessage('SMCP_PROP_NUM').($i + 1).':</td>
			<td>
			<select name="'.$strHTMLControlName["NAME"].'[SUBPROPS]['.$i.']" style="width:270px;">
				<option value="">---</option>';
			foreach ($props as $pid => $parr)
			{
				$sel = "";
				if (isset($arProperty["USER_TYPE_SETTINGS"]["SUBPROPS"][$i]))
				{
					$sel = ($pid == $arProperty["USER_TYPE_SETTINGS"]["SUBPROPS"][$i] ? " selected" : "");
				}
				$return .= '<option value="'.$pid.'"'.$sel.'>['.$parr["CODE"].'] '.$parr["NAME"].'</option>';
			}
			$return .= '</select>';
			$ch = "";
			if (isset($arProperty["USER_TYPE_SETTINGS"]["SUBPROPS_REQ"][$i]))
			{
				$ch = ($arProperty["USER_TYPE_SETTINGS"]["SUBPROPS_REQ"][$i] ? " checked" : "");
			}
			$return .= ' <input type="checkbox" value="1" id="subprop_req_'.$arProperty["ID"].'_'.$i.'" name="'.$strHTMLControlName["NAME"].'[SUBPROPS_REQ]['.$i.']"'.$ch.'> <label for="subprop_req_'.$arProperty["ID"].'_'.$i.'">'.GetMessage('SMCP_PROP_REQ').'</label>
			</td>
			</tr>';
		}
		return $return;
	}
	
	function ShowListPropertyField($name, $property_fields, $values, $bInitDef = false, $def_text = false)
	{
		$name = htmlspecialcharsbx($name);

		if (!is_array($values))
			$values = array();

		foreach($values as $key => $value)
		{
			if(is_array($value) && array_key_exists("VALUE", $value))
				$values[$key] = $value["VALUE"];
		}

		$id = $property_fields["ID"];
		$multiple = $property_fields["MULTIPLE"];
		$res = "";
		if($property_fields["LIST_TYPE"]=="C") //list property as checkboxes
		{
			$cnt = 0;
			$wSel = false;
			$prop_enums = CIBlockProperty::GetPropertyEnum($id);
			while($ar_enum = $prop_enums->Fetch())
			{
				$cnt++;
				if($bInitDef)
					$sel = ($ar_enum["DEF"]=="Y");
				else
					$sel = in_array($ar_enum["ID"], $values);
				if($sel)
					$wSel = true;

				$uniq = md5(uniqid(rand(), true));
				if($multiple=="Y") //multiple
					$res .= '<input type="checkbox" name="'.$name.'[]" value="'.htmlspecialcharsbx($ar_enum["ID"]).'"'.($sel?" checked":"").' id="'.$uniq.'"><label for="'.$uniq.'">'.htmlspecialcharsex($ar_enum["VALUE"]).'</label><br>';
				else //if(MULTIPLE=="Y")
					$res .= '<input type="radio" name="'.$name.'[]" id="'.$uniq.'" value="'.htmlspecialcharsbx($ar_enum["ID"]).'"'.($sel?" checked":"").'><label for="'.$uniq.'">'.htmlspecialcharsex($ar_enum["VALUE"]).'</label><br>';

				if($cnt==1)
					$res_tmp = '<input type="checkbox" name="'.$name.'[]" value="'.htmlspecialcharsbx($ar_enum["ID"]).'"'.($sel?" checked":"").' id="'.$uniq.'"><br>';
			}


			$uniq = md5(uniqid(rand(), true));

			if($cnt==1)
				$res = $res_tmp;
			elseif($multiple!="Y")
				$res = '<input type="radio" name="'.$name.'[]" value=""'.(!$wSel?" checked":"").' id="'.$uniq.'"><label for="'.$uniq.'">'.htmlspecialcharsex(($def_text ? $def_text : GetMessage("IBLOCK_AT_PROP_NO") )).'</label><br>'.$res;

			/*if($multiple=="Y" || $cnt==1)
				$res = '<input type="hidden" name="'.$name.'" value="">'.$res;*/
		}
		else //list property as list
		{
			$bNoValue = true;
			$prop_enums = CIBlockProperty::GetPropertyEnum($id);
			while($ar_enum = $prop_enums->Fetch())
			{
				if($bInitDef)
					$sel = ($ar_enum["DEF"]=="Y");
				else
					$sel = in_array($ar_enum["ID"], $values);
				if($sel)
					$bNoValue = false;
				$res .= '<option value="'.htmlspecialcharsbx($ar_enum["ID"]).'"'.($sel?" selected":"").'>'.htmlspecialcharsex($ar_enum["VALUE"]).'</option>';
			}

			if($property_fields["MULTIPLE"]=="Y" && (int)$property_fields["ROW_COUNT"]<2)
				$property_fields["ROW_COUNT"] = 5;
			if($property_fields["MULTIPLE"]=="Y")
				$property_fields["ROW_COUNT"]++;
			$res = '<select name="'.$name.'[]" size="'.$property_fields["ROW_COUNT"].'" '.($property_fields["MULTIPLE"]=="Y"?"multiple":"").'>'.
					'<option value=""'.($bNoValue?' selected':'').'>'.htmlspecialcharsex(($def_text ? $def_text : GetMessage("IBLOCK_AT_PROP_NA") )).'</option>'.
					$res.
					'</select>';
		}
		echo $res;
	}	
	
	function ShowFilePropertyField($name, $property_fields, $values, $max_file_size_show=50000, $bVarsFromForm = false)
	{
		global $bCopy, $historyId;

		$name = htmlspecialcharsbx($name);

		static $maxSize = array();
		if (empty($maxSize))
		{
			//$detailImageSize = (int)Main\Config\Option::get('iblock', 'detail_image_size');
			$maxSize = array(
				'W' => "200",
				'H' => "200"
			);
		}

		CModule::IncludeModule('fileman');

		if (empty($values) || $bCopy || !is_array($values))
		{
			$values = array(
				"n0" => 0,
			);
		}

		if ($historyId > 0)
		{
			$inputParams = array(
				'upload' => false,
				'medialib' => false,
				'file_dialog' => false,
				'cloud' => false,
				'del' => false,
				'description' => false
			);
			$newUploaderParams = array(
				'delete' => false,
				'edit' => false
			);
		}
		else
		{
			$inputParams = array(
				'upload' => true,
				'medialib' => true,
				'file_dialog' => true,
				'cloud' => true,
				'del' => true,
				'description' => $property_fields["WITH_DESCRIPTION"] == "Y",
			);
			$newUploaderParams = array(
				"upload" => true,
				"medialib" => true,
				"fileDialog" => true,
				"cloud" => true
			);
		}
		$oldUploaderParams = array(
			"IMAGE" => "Y",
			"PATH" => "Y",
			"FILE_SIZE" => "Y",
			"DIMENSIONS" => "Y",
			"IMAGE_POPUP" => "Y",
			"MAX_SIZE" => $maxSize
		);

		foreach($values as $key => $val)
		{
			if(is_array($val))
				$file_id = $val["VALUE"];
			else
				$file_id = $val;

			echo CFileInput::Show($name."[".$key."]", $file_id, $oldUploaderParams, $inputParams);
			
			break;
		}
	}

	function ShowPropertyField($name, $property_fields, $values, $bInitDef = false, $bVarsFromForm = false, $max_file_size_show = 50000, $form_name = "form_element", $bCopy = false)
	{
		$type = $property_fields["PROPERTY_TYPE"];
		if($property_fields["USER_TYPE"]!="")
			_ShowUserPropertyField($name, $property_fields, $values, $bInitDef, $bVarsFromForm, $max_file_size_show, $form_name, $bCopy);
		elseif($type=="L") //list property
			self::ShowListPropertyField($name, $property_fields, $values, $bInitDef);
		elseif($type=="F") //file property
			self::ShowFilePropertyField($name, $property_fields, $values, $max_file_size_show, $bVarsFromForm);
		elseif($type=="G") //section link
		{
			if(function_exists("_ShowGroupPropertyField_custom"))
				_ShowGroupPropertyField_custom($name, $property_fields, $values, $bVarsFromForm);
			else
				_ShowGroupPropertyField($name, $property_fields, $values, $bVarsFromForm);
		}
		elseif($type=="E") //element link
			_ShowElementPropertyField($name, $property_fields, $values, $bVarsFromForm);
		else
			_ShowStringPropertyField($name, $property_fields, $values, $bInitDef, $bVarsFromForm);
	}	
	
	function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName) 
	{
		global $bVarsFromForm, $bCopy, $PROP;
		$ID = IntVal($_REQUEST['ID']);
		
		$strResult = "";
		
		$value_id = "n0";
		
		$value_arr = $value;
		
		$subprops = false;
		
		ob_start();
		
		$sel_pvals = Array();
		
		if (is_array($arProperty["USER_TYPE_SETTINGS"]["SUBPROPS"]) && is_array($PROP) && count($PROP) > 0)
		{	
			echo '<table style="width:100%; background:#e0e8ea; margin-bottom:20px;">';
			foreach ($arProperty["USER_TYPE_SETTINGS"]["SUBPROPS"] as $sp_num => $sprop)
			{	
				$subprops = true;
				
				$pval_id = false;
				
				if (isset($value_arr["VALUE"]["SUB_VAL_IDS"][$sprop]))
					$pval_id = $value_arr["VALUE"]["SUB_VAL_IDS"][$sprop];
					
				if (!$pval_id && is_array($value_arr["VALUE"]["SUBPROPS"]))
					$pval_id = $value_arr["VALUE"]["SUBPROPS"][$sprop];
					
				if (!$pval_id)
					$pval_id = $value_id;
					
				$sel_pvals[$sprop] = "sel_".$value_id;
				
				foreach($PROP as $prop_code_ => $prop_fields_)
				{
					if ($prop_fields_["ID"] == $sprop && $prop_fields_["MULTIPLE"] != "Y" && !in_array($prop_fields_["USER_TYPE"], $GLOBALS["SIMAI_COMPLEXPROP_FORBIDDEN_UT"]))
					{
						$prop_fields__ = $prop_fields_;
						
						if (in_array($prop_fields__["PROPERTY_TYPE"], Array("L","E","G")))
							$pval_id_ = "sel_".$value_id;
						else
							$pval_id_ = $pval_id;
						
						$prop_fields__["MULTIPLE"] = "N";
						$prop_fields__["WITH_DESCRIPTION"] = "N";
						
						if ($pval_id)
							$pvalue = Array($pval_id_ => $prop_fields__["VALUE"][$pval_id]);
						else
							$pvalue = Array($value_id => false);
						
						$prop_fields__["VALUE"] = $pvalue;
						$prop_fields__["~VALUE"] = $pvalue;
						
						$req = ($prop_fields__["IS_REQUIRED"] == "Y" || $arProperty["USER_TYPE_SETTINGS"]["SUBPROPS_REQ"][$sp_num]);
						$fl = ($prop_fields__["PROPERTY_TYPE"] == "F");
						
						echo '<tr><td class="adm-detail-valign-top" style="text-align:right; padding:10px 0 20px 10px;">'.($req ? '<b>' : '').'<i>'.$prop_fields__["NAME"].':</i>'.($req ? '</b>' : '').'</td><td style="padding:10px 20px;">';
						self::ShowPropertyField('PROP['.$prop_fields__["ID"].']', $prop_fields__, $pvalue, (($historyId <= 0) && (!$bVarsFromForm) && ($ID <= 0)), false, 50000, $strHTMLControlName["FORM_NAME"], $bCopy);
						
						echo '<input type="hidden" name="'.$strHTMLControlName["VALUE"].'[SUBPROPS]['.IntVal($sprop).']" value="'.htmlspecialcharsbx($pval_id_).'">';
						if ($req)
							echo '<input type="hidden" name="'.$strHTMLControlName["VALUE"].'[REQ]['.IntVal($sprop).']" value="y">';
						if ($fl)
						{
							echo '<input type="hidden" name="'.$strHTMLControlName["VALUE"].'[FL]['.IntVal($sprop).']" value="y">';
							
							if (is_array($prop_fields__["VALUE"][$pval_id]) && isset($prop_fields__["VALUE"][$pval_id]["VALUE"]))
								$flid = $prop_fields__["VALUE"][$pval_id]["VALUE"];
							else
								$flid = $prop_fields__["VALUE"][$pval_id];
							
							if (IntVal($flid))
							{
								echo '<input type="hidden" name="'.$strHTMLControlName["VALUE"].'[FL_VID]['.IntVal($sprop).']" value="'.htmlspecialcharsbx($pval_id).'">';
								echo '<input type="hidden" name="'.$strHTMLControlName["VALUE"].'[FL_ID]['.IntVal($sprop).']" value="'.IntVal($flid).'">';
							}
						}
						echo '</td></tr>';
					}
				}
			}
			/*if (is_array($value_arr["VALUE"]))
			{
				$ch = ($value_arr["VALUE"]["DEL"] == "y" ? " checked" : "");
				echo '<tr><td colspan="2" style="padding:10px 20px;"><input type="checkbox" name="'.$strHTMLControlName["VALUE"].'[DEL]" value="y" id="scp_del_'.$arProperty["ID"].'"'.$ch.'> <label for="scp_del_'.$arProperty["ID"].'">'.GetMessage('SMCP_DEL_CVAL').'</label></td>';
			}*/
			echo '</table>';
			
		}
		
		$strResult = ob_get_contents();
		ob_end_clean();
		
		if (is_array($arProperty["USER_TYPE_SETTINGS"]["SUBPROPS"]) && is_array($PROP) && count($PROP) > 0)
		{			
			foreach ($arProperty["USER_TYPE_SETTINGS"]["SUBPROPS"] as $sprop)
			{
				$strResult = str_replace('PROP['.$sprop.'][]', 'PROP['.$sprop.']['.$sel_pvals[$sprop].']', $strResult);
				$strResult = str_replace('PROP['.$sprop.']', 'SPROP['.$sprop.']', $strResult);
				$strResult = str_replace('PROP_'.$sprop, 'SPROP_'.$sprop, $strResult);
				$strResult = str_replace('_prop_'.$sprop, '_sprop_'.$sprop, $strResult);
				$strResult = str_replace('_'.$sprop, '_s'.$sprop, $strResult);
				$strResult = str_replace('map_yandex__s'.$sprop, 'map_yandex__s'.$sprop.'_'.$value_id, $strResult);
				$strResult = str_replace(md5('PROP['.$sprop.']'), md5('SPROP['.$sprop.']'), $strResult);
			}
		}
			
		if ($GLOBALS["err_m"] && is_array($PROP) && count($PROP) > 0)
			$strResult = '<span style="color:#a00">'.$GLOBALS["err_m"].'</span>';

		return $strResult;
	}
	
	function GetPropertyFieldHtmlMulty($arProperty, $value, $strHTMLControlName) 
	{
		global $historyId, $bVarsFromForm, $bCopy, $PROP;
		$ID = IntVal($_REQUEST['ID']);
		
		$hides = Array();
		$i = -1;
		$k = 0;
		
		if (is_array($GLOBALS["SCP_err_values"][$arProperty["ID"]]))
		{
			foreach ($GLOBALS["SCP_err_values"][$arProperty["ID"]] as $value_id => $value_arr)
			{
				if (substr($value_id, 0, 1) == "n")
				{
					if (IntVal(substr($value_id, 1)) > $i)
						$i = IntVal(substr($value_id, 1));
					$k++;
				}
			}
		}
		
		while ($k < $arProperty["MULTIPLE_CNT"])
		{
			$i++;
			$k++;
			if (!isset($GLOBALS["SCP_err_values"][$arProperty["ID"]]["n".$i]))
			{
				$value["n".$i] = Array("VALUE" => "", "DESCRIPTION"=>"");
				$hides["n".$i] = true;
			}
		}
		
		$strResult = "";
		
		foreach ($value as $value_id => $value_arr)
		{
			$subprops = false;
			
			ob_start();
			
			$sel_pvals = Array();
			
			if (is_array($arProperty["USER_TYPE_SETTINGS"]["SUBPROPS"]) && is_array($PROP) && count($PROP) > 0)
			{
				if ($hides[$value_id])
					echo '<div id="simai_complex_add_area_'.IntVal($arProperty["ID"]).'_'.htmlspecialcharsbx($value_id).'" style="display:none">';
				
				echo '<div style="background:#e0e8ea; margin-bottom:10px; padding: 10px 0;"><table style="width:100%;">';		
				foreach ($arProperty["USER_TYPE_SETTINGS"]["SUBPROPS"] as $sp_num => $sprop)
				{	
					$subprops = true;
					
					$pval_id = false;
					
					if (isset($value_arr["VALUE"]["SUB_VAL_IDS"][$sprop]))
						$pval_id = $value_arr["VALUE"]["SUB_VAL_IDS"][$sprop];
						
					if (isset($value_arr["VALUE"]["SUBPROPS"]))
					{
						if (!$pval_id && is_array($value_arr["VALUE"]["SUBPROPS"]))
							$pval_id = $value_arr["VALUE"]["SUBPROPS"][$sprop];
					}
						
					if (!$pval_id)
						$pval_id = $value_id;
						
					$sel_pvals[$sprop] = "sel_".$value_id;
					
					foreach($PROP as $prop_code_ => $prop_fields_)
					{
						if ($prop_fields_["ID"] == $sprop && $prop_fields_["MULTIPLE"] == "Y" && !in_array($prop_fields_["USER_TYPE"], $GLOBALS["SIMAI_COMPLEXPROP_FORBIDDEN_UT"]))
						{
							$prop_fields__ = $prop_fields_;
							
							if (in_array($prop_fields__["PROPERTY_TYPE"], Array("L","E","G")))
								$pval_id_ = "sel_".$value_id;
							else
								$pval_id_ = $pval_id;
							
							$prop_fields__["MULTIPLE"] = "N";
							$prop_fields__["WITH_DESCRIPTION"] = "N";
							
							if ($pval_id)
								$pvalue = Array($pval_id_ => $prop_fields__["VALUE"][$pval_id]);
							else
								$pvalue = Array($value_id => false);
							
							$prop_fields__["VALUE"] = $pvalue;
							$prop_fields__["~VALUE"] = $pvalue;
							
							$req = ($prop_fields__["IS_REQUIRED"] == "Y" || $arProperty["USER_TYPE_SETTINGS"]["SUBPROPS_REQ"][$sp_num]);
							$fl = ($prop_fields__["PROPERTY_TYPE"] == "F");
							
							echo '<tr><td class="adm-detail-valign-top" style="text-align:right;">'.($req ? '<b>' : '').$prop_fields__["NAME"].':'.($req ? '</b>' : '').'</td><td style="padding:4px 5px;">';
							self::ShowPropertyField('PROP['.$prop_fields__["ID"].']', $prop_fields__, $pvalue, (($historyId <= 0) && (!$bVarsFromForm) && ($ID <= 0)), false, 50000, $strHTMLControlName["FORM_NAME"], $bCopy);
							
							echo '<input type="hidden" name="'.$strHTMLControlName["VALUE"].'['.$value_id.'][VALUE][SUBPROPS]['.IntVal($sprop).']" value="'.htmlspecialcharsbx($pval_id_).'">';
							if ($req)
								echo '<input type="hidden" name="'.$strHTMLControlName["VALUE"].'['.$value_id.'][VALUE][REQ]['.IntVal($sprop).']" value="y">';
							if ($fl)
							{
								echo '<input type="hidden" name="'.$strHTMLControlName["VALUE"].'['.$value_id.'][VALUE][FL]['.IntVal($sprop).']" value="y">';
								
								if (is_array($prop_fields__["VALUE"][$pval_id]) && isset($prop_fields__["VALUE"][$pval_id]["VALUE"]))
									$flid = $prop_fields__["VALUE"][$pval_id]["VALUE"];
								else
									$flid = $prop_fields__["VALUE"][$pval_id];
								
								if (IntVal($flid) > 0)
								{
									echo '<input type="hidden" name="'.$strHTMLControlName["VALUE"].'['.$value_id.'][VALUE][FL_VID]['.IntVal($sprop).']" value="'.htmlspecialcharsbx($pval_id).'">';
									echo '<input type="hidden" name="'.$strHTMLControlName["VALUE"].'['.$value_id.'][VALUE][FL_ID]['.IntVal($sprop).']" value="'.IntVal($flid).'">';
								}
							}
							echo '</td></tr>';
						}
					}
				}
				if (is_array($value_arr["VALUE"]) && IntVal($value_id))
				{
					$ch = ($value_arr["VALUE"]["DEL"] == "y" ? " checked" : "");
					echo '<tr><td colspan="2" style="padding:4px 20px;"><input type="checkbox" name="'.$strHTMLControlName["VALUE"].'['.$value_id.'][VALUE][DEL]" value="y" id="scp_del_'.$arProperty["ID"].'_'.$value_id.'"'.$ch.'> <label for="scp_del_'.$arProperty["ID"].'_'.$value_id.'">'.GetMessage('SMCP_DEL_CVAL').'</label></td>';
				}
				echo '</table></div>';
				
				if ($hides[$value_id])
					echo '</div>';
					
			}
			
			$strResult_ = ob_get_contents();
			ob_end_clean();
			
			if (is_array($arProperty["USER_TYPE_SETTINGS"]["SUBPROPS"]) && is_array($PROP) && count($PROP) > 0)
			{		
				foreach ($arProperty["USER_TYPE_SETTINGS"]["SUBPROPS"] as $sprop)
				{
					$strResult_ = str_replace('PROP['.$sprop.'][]', 'PROP['.$sprop.']['.$sel_pvals[$sprop].']', $strResult_);
					$strResult_ = str_replace('PROP['.$sprop.']', 'SPROP['.$sprop.']', $strResult_);
					$strResult_ = str_replace('PROP_'.$sprop, 'SPROP_'.$sprop, $strResult_);
					$strResult_ = str_replace('_prop_'.$sprop, '_sprop_'.$sprop, $strResult_);
					$strResult_ = str_replace('_'.$sprop, '_s'.$sprop, $strResult_);
					$strResult_ = str_replace('map_yandex__s'.$sprop, 'map_yandex__s'.$sprop.'_'.$value_id, $strResult_);
					$strResult_ = str_replace(md5('PROP['.$sprop.']'), md5('SPROP['.$sprop.']'), $strResult_);
				}
			}
			
			$strResult .= $strResult_;
		}
		if (count($hides) > 0  && is_array($PROP) && count($PROP) > 0)
		{
			$strResult .= '
			<script type="text/javascript">
			function SimaiComplexAddValue'.IntVal($arProperty["ID"]).'(what)
			{
				var hides_js = new Array(';
			$hides_js = Array();
			foreach ($hides as $hid => $hval)
				$hides_js[$hid] = '"'.htmlspecialcharsbx($hid).'"';
			$hides_js = implode(",", $hides_js);
			$strResult .= $hides_js;
			$strResult .= ');
				var sc_show = false;
				var sc_bt_hide = 0;
				for (var i in hides_js) 
				{
					var aid = hides_js[i];
					var sc_area_id = "simai_complex_add_area_'.IntVal($arProperty["ID"]).'_" + aid;
					var sc_area = document.getElementById(sc_area_id);
					if (sc_area != null)
					{
						if (sc_area.style.display == "none")
						{
							sc_bt_hide++;
							if (sc_show == false)
							{
								sc_area.style.display = "block";
								sc_show = true;
							}
						}
					}
				}
				if (sc_bt_hide < 2)
					what.style.display = "none";
			}
			</script>
			<input type="button" name="apply" class="adm-btn" value="'.GetMessage('SMCP_ADD_CVAL').'" onclick="SimaiComplexAddValue'.IntVal($arProperty["ID"]).'(this)">';
		}
		
		if ($GLOBALS["err_m"] && is_array($PROP) && count($PROP) > 0)
			$strResult = '<span style="color:#a00">'.$GLOBALS["err_m"].'</span>';
		
		return $strResult;
	}
	
	/*function GetPublicEditHTML($arProperty, $value, $strHTMLControlName)
	{
		
	}*/
	
	function ConvertToDB($arProperty, $value)
	{
		if (is_array($value["VALUE"]))
			$value["VALUE"] = serialize($value["VALUE"]);
		else
			$value["VALUE"] = false;
		return $value;
	}
	
	function ConvertFromDB($arProperty, $value)
	{
		if (strlen($value["VALUE"]) > 2)
		{
			$value_us = unserialize($value["VALUE"]);
			
			$value["VALUE"] = Array();
			if (is_array($value_us))
			{
				foreach ($value_us as $el_id => $subprops)
				{
					$value["VALUE"]["ELEMENT"] = $el_id;
					if ($el_id > 0)
					{
						$res = CIBlockElement::GetById($el_id);
						if ($obRes = $res->GetNextElement())
						{
							if (is_array($subprops))
							{
								foreach ($subprops as $sprop => $pval_vd)
								{
									if (is_array($pval_vd))
										$pval_vd = current($pval_vd);
									
									if ($arProperty["MULTIPLE"] == "Y"/* && $pval_vd*/)
									{
										$arr = $obRes->GetProperty($sprop);
										$pid = (trim($arr["CODE"]) ? $arr["CODE"] : $arr["ID"]);
										if (!is_array($arr["PROPERTY_VALUE_ID"]))
										{
											$arr["VALUE"] = Array("0" => $arr["VALUE"]);
											$arr["DESCRIPTION"] = Array("0" => $arr["DESCRIPTION"]);
											$arr["VALUE_ENUM"] = Array("0" => $arr["VALUE_ENUM"]);
											$arr["VALUE_XML_ID"] = Array("0" => $arr["VALUE_XML_ID"]);
											$arr["VALUE_SORT"] = Array("0" => $arr["VALUE_SORT"]);
											$arr["VALUE_ENUM_ID"] = Array("0" => $arr["VALUE_ENUM_ID"]);
											$arr["~VALUE"] = Array("0" => $arr["~VALUE"]);
											$arr["~DESCRIPTION"] = Array("0" => $arr["~DESCRIPTION"]);
											$arr["PROPERTY_VALUE_ID"] = Array("0" => $arr["PROPERTY_VALUE_ID"]);
										}
										foreach ($arr["VALUE"] as $val_id => $val)
										{
											$arr_ = $arr;
											$arr_["VALUE"] = $val;
											$arr_["DESCRIPTION"] = $arr["DESCRIPTION"][$val_id];
											$arr_["VALUE_ENUM"] = $arr["VALUE_ENUM"][$val_id];
											$arr_["VALUE_XML_ID"] = $arr["VALUE_XML_ID"][$val_id];
											$arr_["VALUE_SORT"] = $arr["VALUE_SORT"][$val_id];
											$arr_["VALUE_ENUM_ID"] = $arr["VALUE_ENUM_ID"][$val_id];
											$arr_["~VALUE"] = $arr["~VALUE"][$val_id];
											$arr_["~DESCRIPTION"] = $arr["~DESCRIPTION"][$val_id];
											$arr_["PROPERTY_VALUE_ID"] = $arr["PROPERTY_VALUE_ID"][$val_id];
											if ($arr_["PROPERTY_TYPE"] == "F")
											{
												if ($arr_["VALUE"] == $pval_vd)
												{
													$value["VALUE"]["SUB_VAL_IDS"][$sprop] = $arr_["PROPERTY_VALUE_ID"];
													$value["VALUE"]["SUB_VALUES"][$pid] = $arr_;
													$value["VALUE"]["FLEG"][$sprop] = $arr_["VALUE"];
												}
											}
											elseif ($arr_["PROPERTY_TYPE"] == "L")
											{
												if ($arr_["VALUE_ENUM_ID"] == $pval_vd)
												{
													$value["VALUE"]["SUB_VAL_IDS"][$sprop] = $arr_["PROPERTY_VALUE_ID"];
													$value["VALUE"]["FLEG"][$sprop] = $arr_["VALUE_ENUM_ID"];
													$value["VALUE"]["SUB_VALUES"][$pid] = $arr_;
												}
											}
											elseif (in_array($arr_["PROPERTY_TYPE"], Array("E","G")))
											{
												if ($arr_["VALUE"] == $pval_vd)
												{
													$value["VALUE"]["SUB_VAL_IDS"][$sprop] = $arr_["PROPERTY_VALUE_ID"];
													$value["VALUE"]["FLEG"][$sprop] = $arr_["VALUE"];
													$value["VALUE"]["SUB_VALUES"][$pid] = $arr_;
												}
											}
											elseif ($arr_["DESCRIPTION"] == $pval_vd)
											{
												$value["VALUE"]["SUB_VAL_IDS"][$sprop] = $arr_["PROPERTY_VALUE_ID"];
												$value["VALUE"]["SUB_VALUES"][$pid] = $arr_;
											}
										}
									}
									elseif ($arProperty["MULTIPLE"] != "Y")
									{
										$arr = $obRes->GetProperty($sprop);
										$pid = (trim($arr["CODE"]) ? $arr["CODE"] : $arr["ID"]);
										if (is_array($arr["PROPERTY_VALUE_ID"]))
										{
											$arr["VALUE"] = current($arr["VALUE"]);
											$arr["DESCRIPTION"] = current($arr["DESCRIPTION"]);
											$arr["VALUE_ENUM"] = current($arr["VALUE_ENUM"]);
											$arr["VALUE_XML_ID"] = current($arr["VALUE_XML_ID"]);
											$arr["VALUE_SORT"] = current($arr["VALUE_SORT"]);
											$arr["VALUE_ENUM_ID"] = current($arr["VALUE_ENUM_ID"]);
											$arr["~VALUE"] = current($arr["~VALUE"]);
											$arr["~DESCRIPTION"] = current($arr["~DESCRIPTION"]);
											$arr["PROPERTY_VALUE_ID"] = current($arr["PROPERTY_VALUE_ID"]);
										}
										$value["VALUE"]["SUB_VAL_IDS"][$sprop] = $arr["PROPERTY_VALUE_ID"];
										$value["VALUE"]["SUB_VALUES"][$pid] = $arr;
										if ($arr["PROPERTY_TYPE"] == "F")
											$value["VALUE"]["FLEG"][$sprop] = $arr["VALUE"];
										elseif ($arr["PROPERTY_TYPE"] == "L")
											$value["VALUE"]["FLEG"][$sprop] = $arr["VALUE_ENUM_ID"];
										elseif (in_array($arr["PROPERTY_TYPE"], Array("E","G")))
											$value["VALUE"]["FLEG"][$sprop] = $arr["VALUE"];
									}
								}
							}
						}
					}
				}
			}
		}
		unset($value["DESCRIPTION"]);
		return $value;
	}
	
	function GetPublicViewHTML($arProperty, $value, $strHTMLControlName)
	{
		$return = Array();
		if($value["VALUE"]["ELEMENT"] > 0 && isset($value["VALUE"]["SUB_VALUES"]))
		{
			$return = Array();
			$res = CIBlockElement::GetByID($value["VALUE"]["ELEMENT"]);
			if($arE = $res->Fetch())
			{
				$return["ELEMENT"] = $value["VALUE"]["ELEMENT"];
				if (is_array($value["VALUE"]["SUB_VALUES"]))
				{
					foreach ($value["VALUE"]["SUB_VALUES"] as $pid => $parr)
					{
						$p_mult = $parr["MULTIPLE"];
						$parr["MULTIPLE"] = "N";
						$return["SUB_VALUES"][$pid] = CIBlockFormatProperties::GetDisplayValue($arE, $parr, "simai_complex");
						$return["SUB_VALUES"][$pid]["MULTIPLE"] = $p_mult;
					}
				}
			}
		}
		else
			$return = Array();
		return $return;
	}
	
	function GetSearchContent($arProperty, $value, $strHTMLControlName)
	{
		return '';
	}
	
	function GetAdminFilterHTML($arProperty, $strHTMLControlName)
	{
		return '';
	}
	
	function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
	{
		return GetMessage('SMCP_COMPLEX_PROP_LIST');
	}
	
	function GetPublicFilterHTML($arProperty, $strHTMLControlName)
	{
		return '';
	}
}

class CIBEditSimaiComplexProp
{
	function OnBeforePrologHandler()
	{
		if (is_array($_POST["SPROP"]) || is_array($_FILES["SPROP"]))
		{
			foreach ($_POST["SPROP"] as $pid => $parr)
				$_POST["PROP"][$pid] = $parr;
			
			if (is_array($_FILES["SPROP"]))
			{
				foreach ($_FILES["SPROP"] as $fid => $farr)
				{			
					foreach ($farr as $pid => $parr)
					{
						foreach ($parr as $tid => $tval)
						{
							if ($_FILES["SPROP"]["name"][$pid][$tid] != "")
								$_FILES["PROP"][$fid][$pid][$tid] = $tval;
						}
					}
				}
			}
			
			foreach ($_POST as $pid => $parr)
			{
				if (substr_count($pid, "SPROP_"))
				{
					$pid2 = str_replace("SPROP_","PROP_",$pid);
					$_POST[$pid2] = $parr;
					/*$pid2 = str_replace("PROP_s","PROP_",$pid2);
					$_POST[$pid2] = $parr;*/
					$pid2 = str_replace("_s","_",$pid2);
					$_POST[$pid2] = $parr;
				}
			}		
		}
		
		$GLOBALS["SIMAI_COMPLEXPROP_FORBIDDEN_UT"] = Array(
			"simai_complex",
			"video",
			"map_yandex",
			"map_google",
			"ElementXmlID",
			"Sequence",
			"SKU",
		);
		$GLOBALS["SCP_PU"] = false;
		$GLOBALS["SCP_PVDesc"] = Array();
		$GLOBALS["SCP_req_all"] = Array();
		$GLOBALS["SCP_err_values"] = Array();
	}
	
	function OnStartIBlockElementUpdateHandler(&$arF)
	{	
		if(is_array($arF["PROPERTY_VALUES"]))
		{
			$GLOBALS["SCP_PU"] = true;
			foreach ($arF["PROPERTY_VALUES"] as $prop_id => $prop_values)
			{
				if (!is_array($prop_values))
					$prop_values = array();
				foreach ($prop_values as $prop_value_id => $prop_value)
				{
					$svalues = Array();
					$req = Array();
					
					$vals_subprops = false;
					
					if (isset($prop_value['SUBPROP_VALUES']))
					{
						$prop_value_id_rand = 'n'.rand(1, 9999);
						
						if (is_array($prop_value['SUBPROP_VALUES']))
						{
							foreach ($prop_value['SUBPROP_VALUES'] as $spv_pid_code => $spv_val)
							{
								$spv_pid = 0;
								
								/*if (isset($spv_val['VALUE']))
									$arF["PROPERTY_VALUES"][$spv_pid_code][$prop_value_id] = $spv_val['VALUE'];
								else
									$arF["PROPERTY_VALUES"][$spv_pid_code][$prop_value_id] = $spv_val;*/
								
								if (intval($spv_pid_code) > 0)
								{
									$res = CIBlockProperty::GetList(Array("sort" => "asc"), Array("ACTIVE" => "Y", "IBLOCK_ID" => intval($arF['IBLOCK_ID']), "ID" => $spv_pid_code));
									if ($arr = $res->Fetch())
									{
										$spv_pid = intval($arr['ID']);
									}
								}
								elseif(trim($spv_pid_code))
								{
									$res = CIBlockProperty::GetList(Array("sort" => "asc"), Array("ACTIVE" => "Y", "IBLOCK_ID" => intval($arF['IBLOCK_ID']), "CODE" => $spv_pid_code));
									if ($arr = $res->Fetch())
									{
										$spv_pid = intval($arr['ID']);
									}
								}
								
								if ($spv_pid)
								{
									$arF["PROPERTY_VALUES"][$spv_pid][$prop_value_id_rand]["VALUE"] = $spv_val;
									$vals_subprops[$spv_pid] = $prop_value_id_rand;
								}
							}	
							
						}
					}					
					
					
					if (isset($prop_value['VALUE']['SUBPROPS']))
					{
						if (is_array($prop_value['VALUE']['SUBPROPS']))
							$vals_subprops = $prop_value['VALUE']['SUBPROPS'];
					}
					elseif (isset($prop_value['VALUE']['SUB_VAL_IDS']))
					{
						if (is_array($prop_value['VALUE']['SUB_VAL_IDS']))
							$vals_subprops = $prop_value['VALUE']['SUB_VAL_IDS'];
					}
					
					if (is_array($vals_subprops))
					{			
						if (isset($prop_value['SUBPROP_VALUES']))
						{
							$pdesc = "scp_".$prop_value_id_rand;
						}
						elseif (IntVal($prop_value_id))
							$pdesc = "scp_".$prop_value_id;
						else
							$pdesc = "scp_".$prop_id.$prop_value_id;
							
						
						if (isset($prop_value['VALUE']['FLEG']))
						{
							if (is_array($prop_value['VALUE']['FLEG']))
							{
								foreach ($prop_value['VALUE']['FLEG'] as $subprop_id => $subprop_val)
									$GLOBALS["SCP_PVDesc"][$subprop_id][$subprop_val][$pdesc] = $pdesc;
							}
						}
						
						foreach ($vals_subprops as $subprop_id => $subprop_vid)
						{							
							if ($subprop_id > 0)
							{
								$subprop_val = Array("VALUE" => "");
								$rq = $prop_value['VALUE']['REQ'][$subprop_id];
								$fl = $prop_value['VALUE']['FL'][$subprop_id];
								$fl_vid = $prop_value['VALUE']['FL_VID'][$subprop_id];
								$fl_id = $prop_value['VALUE']['FL_ID'][$subprop_id];
								if (!$fl_id && $prop_value['VALUE']['FLEG'][$subprop_id])
									$fl_id = $prop_value['VALUE']['FLEG'][$subprop_id];
								
								if ($prop_value['VALUE']['DEL'] != "y")
								{
									$fl_name = "";
									$fl_del = "";
									$fl_arr = Array();
									
									$fextcheck = false;
									if (isset($prop_value['VALUE']['FL_ID'][$subprop_id]))
									{
										if ($prop_value['VALUE']['FL_ID'][$subprop_id])
											$fextcheck = true;
									}
									
									if (isset($arF["PROPERTY_VALUES"][$subprop_id][$subprop_vid]["VALUE"]) || $fextcheck)
									{
										if (isset($arF["PROPERTY_VALUES"][$subprop_id][$subprop_vid]["VALUE"]["name"]))
											$fl_name = $arF["PROPERTY_VALUES"][$subprop_id][$subprop_vid]["VALUE"]["name"];
										elseif (isset($arF["PROPERTY_VALUES"][$subprop_id][$subprop_vid]["name"]))
										{
											$fl_name = $arF["PROPERTY_VALUES"][$subprop_id][$subprop_vid]["name"];
											$fl_arr = $arF["PROPERTY_VALUES"][$subprop_id][$subprop_vid];
											$arF["PROPERTY_VALUES"][$subprop_id][$subprop_vid] = Array();
											$arF["PROPERTY_VALUES"][$subprop_id][$subprop_vid]["VALUE"] = $fl_arr;
										}
											
										if (isset($arF["PROPERTY_VALUES"][$subprop_id][$subprop_vid]["VALUE"]["del"]))
											$fl_del = $arF["PROPERTY_VALUES"][$subprop_id][$subprop_vid]["VALUE"]["del"];
										
										if (isset($arF["PROPERTY_VALUES"][$subprop_id]["sel_".$prop_value_id]["VALUE"]) && is_array($arF["PROPERTY_VALUES"][$subprop_id]["sel_".$prop_value_id]))
											$subprop_val = $arF["PROPERTY_VALUES"][$subprop_id]["sel_".$prop_value_id]["VALUE"];
										if (isset($arF["PROPERTY_VALUES"][$subprop_id]["sel_".$prop_value_id]))
											$subprop_val = $arF["PROPERTY_VALUES"][$subprop_id]["sel_".$prop_value_id];
										elseif ($arF["PROPERTY_VALUES"][$subprop_id][$subprop_vid])
											$subprop_val = $arF["PROPERTY_VALUES"][$subprop_id][$subprop_vid];
										
										if ($fl_id && strlen($fl_name) < 2 && $fl_del != "y" && $fl_del != "Y")
										{
											$arF["PROPERTY_VALUES"][$subprop_id][$subprop_vid]["VALUE"] = $fl_id;
											$svalues[$subprop_id][$subprop_vid] = $pdesc;
										}
										elseif (isset($subprop_val["VALUE"]["TEXT"]))
										{
											if ($subprop_val["VALUE"]["TEXT"] != "")
												$svalues[$subprop_id][$subprop_vid] = $pdesc;
											elseif ($rq)
												$req[$subprop_id] = $subprop_id;
										}
										elseif ($subprop_val["VALUE"] != "")
											$svalues[$subprop_id][$subprop_vid] = $pdesc;
										elseif($fl_name != "")
										{
											//unset($arF["PROPERTY_VALUES"][$subprop_id][$subprop_vid]["VALUE"]);
											$svalues[$subprop_id][$subprop_vid] = $pdesc;
										}
										elseif ($rq)
											$req[$subprop_id] = $subprop_id;
											
										if ($rq && ($fl_del == "Y" || $fl_del == "y"))
											$req[$subprop_id] = $subprop_id;
											
										$arF["PROPERTY_VALUES"][$subprop_id][$subprop_vid]["DESCRIPTION"] = $pdesc;
										
										$ncheck = false;
										if (isset($subprop_val["VALUE"]))
										{
											if (isset($subprop_val["VALUE"]['name']))
											{
												if (strlen($subprop_val["VALUE"]['name']) > 2)
												{
													$subprop_val = $subprop_val["VALUE"]['name'];
													$ncheck = true;
												}
											}
											
											if (isset($subprop_val["VALUE"]))
											{
												if ($subprop_val["VALUE"] && !$ncheck)
												{
													$subprop_val = $subprop_val["VALUE"];
												}
											}
										}
										if (is_array($subprop_val) && !$ncheck)
											$subprop_val = current($subprop_val);
										
										if ($subprop_val)
										{
											$GLOBALS["SCP_PVDesc"][$subprop_id][$subprop_val][$pdesc] = $pdesc;
										}
									}
								}
								else
								{	
									$arF["PROPERTY_VALUES"][$subprop_id][$subprop_vid] = Array(
										"VALUE" => false,
										"DESCRIPTION" => false
									);
									if ($fl)
									{
										$arF["PROPERTY_VALUES"][$subprop_id][$subprop_vid]["VALUE"]["del"] = "Y";
										$arF["PROPERTY_VALUES"][$subprop_id][$subprop_vid]["DESCRIPTION"] = "";
									}
								}
							}
						}
						if (count($svalues) > 0)
						{
							if (count($req) > 0)
								$GLOBALS["SCP_req_all"] = array_merge($GLOBALS["SCP_req_all"], $req);
							
							$GLOBALS["SCP_err_values"][$prop_id][$prop_value_id] = $prop_value_id;
							
							$arF["PROPERTY_VALUES"][$prop_id][$prop_value_id]["VALUE"] = Array();
							$arF["PROPERTY_VALUES"][$prop_id][$prop_value_id]["DESCRIPTION"] = $pdesc;
						}
						else
						{
							$arF["PROPERTY_VALUES"][$prop_id][$prop_value_id]["VALUE"] = false;
							$arF["PROPERTY_VALUES"][$prop_id][$prop_value_id]["DESCRIPTION"] = false;
						}
					}
				}
				
			}
		}
	}
	
	function OnBeforeIBlockElementUpdateHandler(&$arF)
	{
		if (count($GLOBALS["SCP_req_all"]) > 0)
		{	
			global $APPLICATION;
			$e = new CAdminException($msg);
			foreach ($GLOBALS["SCP_req_all"] as $pid)
			{
				$res = CIBlockProperty::GetByID($pid);
				if ($arr = $res->Fetch())
					$e->AddMessage(array("text" => GetMessage('SMCP_REQ_EMPTY')."&laquo;".$arr["NAME"]."&raquo;"));
			}
			$APPLICATION->ThrowException($e);
			return false;
		}
	}
	
	function OnAfterIBlockElementUpdateHandler(&$arF)
	{
		if ($GLOBALS["SCP_PU"])
		{
			if($arF["ID"] > 0 && $arF["RESULT"])
			{
				$sc_props = Array();
				$nsc_props = Array();
				$res = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arF["IBLOCK_ID"]));
				while ($arr = $res->Fetch())
				{
					if ($arr["USER_TYPE"] == "simai_complex")
					{
						$sc_props[$arr["ID"]] = $arr["USER_TYPE_SETTINGS"]["SUBPROPS"];
						$sc_props_m[$arr["ID"]] = ($arr["MULTIPLE"] == "Y");
					}
				}
				foreach ($sc_props as $scpid => $subprops)
				{
					$scvals = Array();
					$scval_s = Array();
					$res = CIBlockElement::GetProperty($arF["IBLOCK_ID"], $arF["ID"], "sort", "asc", array("ID"=>$scpid));
					while ($arr = $res->Fetch())
					{
						if ($arr["DESCRIPTION"])
							$scvals[$arr["DESCRIPTION"]] = $arr["PROPERTY_VALUE_ID"];
						$scval_s = $arr["PROPERTY_VALUE_ID"];
					}
					$subprops_vals = Array();
					$svalues_uf = Array();
					$svalues_ufs = Array();
					foreach ($subprops as $subprop_id)
					{
						$res = CIBlockElement::GetProperty($arF["IBLOCK_ID"], $arF["ID"], "sort", "asc", array("ID"=>$subprop_id));
						while ($arr = $res->Fetch())
						{
							if (isset ($GLOBALS["SCP_PVDesc"][$subprop_id][$arr["VALUE"]]))
								$s_desc_arr = $GLOBALS["SCP_PVDesc"][$subprop_id][$arr["VALUE"]];
							else
								$s_desc_arr = false;
							if (!in_array($arr["PROPERTY_TYPE"], Array("L","E","G")))
							{
								if ($arr["PROPERTY_TYPE"] == "F")
								{
									$svalues_uf[$arr["DESCRIPTION"]][$arF["ID"]][$subprop_id][$arr["PROPERTY_VALUE_ID"]] = $arr["VALUE"];
									$svalues_ufs[$arF["ID"]][$subprop_id][$arr["PROPERTY_VALUE_ID"]] = $arr["VALUE"];
								}
								else
								{
									$svalues_uf[$arr["DESCRIPTION"]][$arF["ID"]][$subprop_id][$arr["PROPERTY_VALUE_ID"]] = $arr["DESCRIPTION"];
									$svalues_ufs[$arF["ID"]][$subprop_id][$arr["PROPERTY_VALUE_ID"]] = $arr["DESCRIPTION"];
								}
							}
							elseif(is_array($s_desc_arr))
							{
								foreach ($s_desc_arr as $s_desc)
								{
									$svalues_uf[$s_desc][$arF["ID"]][$subprop_id][$arr["PROPERTY_VALUE_ID"]] = $arr["VALUE"];
									$svalues_ufs[$arF["ID"]][$subprop_id][$arr["PROPERTY_VALUE_ID"]] = $arr["VALUE"];
								}
							}
						}
					}
					$prop = Array();
					if ($sc_props_m[$scpid])
					{
						foreach ($scvals as $desc => $scvid)
						{
							$svalues = $svalues_uf[$desc];
							if (is_array($svalues) && count($svalues) > 0)
								$prop[$scpid][$scvid] = array("VALUE" => $svalues, "DESCRIPTION" => $desc);
						}	
					}
					else
					{					
						$svalues = $svalues_ufs;
						if (is_array($svalues) && count($svalues) > 0)
							$prop[$scpid][$scval_s] = array("VALUE" => $svalues, "DESCRIPTION" => "");
					}
					CIBlockElement::SetPropertyValuesEx($arF["ID"], $arF["IBLOCK_ID"], $prop);
				}
			}
		}
	}
}
?>
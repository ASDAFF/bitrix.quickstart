<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

function __WGetPROPSHiddens($PROPS, $p = '')
{
	$s = '';
	if(trim($_REQUEST['site_id'])!='')
		$s .= '<input type="hidden" name="site_id" value="'.htmlspecialchars($_REQUEST['site_id']).'">';

	if(!is_array($PROPS))
		return $s;

	foreach($PROPS as $k=>$v)
	{
		if(is_array($v))
			$s .= __WGetPROPSHiddens($v, $p.'['.htmlspecialchars($k).']');
		else
			$s .= '<input type="hidden" name="PROPS'.$p.'['.htmlspecialchars($k).']" value="'.htmlspecialchars($v).'">';
	}

	return $s;
}

class StepDescription extends CWizardStep
{
	function InitStep()
	{
		$this->SetTitle(GetMessage('CATWIZ_STEP_DESCRIPTION_TITLE'));
		$this->SetNextStep("step_settings");
		$this->SetStepID("step_description");
		$this->SetCancelStep("cancel");
		if(!CModule::IncludeModule('catalog'))
			$this->SetError(GetMessage('CATWIZ_NO_MODULE_ERROR'));
	}

	function OnPostForm()
	{
		$wizard = &$this->GetWizard();

		$PARAM = $wizard->GetVar("PARAM");
		if($PARAM['catalogId'] != $PARAM['oldcatalogId'])
			unset($_POST['PROPS']);
	}

	function ShowStep()
	{
		$wizard = &$this->GetWizard();

		if(isset($_GET['IBLOCK_ID'])){
			$wizard->SetDefaultVars(array(
			'PARAM' => array(
				'catalogId' => $_GET['IBLOCK_ID'],
			)));
		}
		
		$PARAM = $wizard->GetVar("PARAM");
		
		if(isset($_GET['editCBlock']) || ( isset($PARAM['catalogId']) && $PARAM['catalogId'] != 'new')){
			$this->content = GetMessage('CATWIZ_STEP_DESCRIPTION_CONTENT_EDIT');
		} else {
			$this->content = GetMessage('CATWIZ_STEP_DESCRIPTION_CONTENT');
		}
		$this->content .= __WGetPROPSHiddens($_POST["PROPS"]);
		
		if(trim($_REQUEST['site_id'])=='')
		{
			$dbsite = CSite::GetList($by="SORT", $order="ASC", Array("ACTIVE"=>"Y"));
			$arSites = Array();
			while($arsite = $dbsite->GetNext())
			{
				$arSites[] = $arsite;
			}
			
			if(count($arSites)==1)
				$this->content .= '<input type="hidden" name="site_id" value="'.$arSites[0]['ID'].'">';
			else
			{
				$this->content .= '<br><br>'.GetMessage("CATWIZ_STEP_SITE_SELECT").' ';
				$this->content .= '<select name="site_id">';
				foreach($arSites as $arSite)
					$this->content .= '<option value="'.$arSite['ID'].'">'.$arSite['NAME'].'</option>';
				$this->content .= '</select>';
			}
		}

		if($_GET['editCBlock'] == 'Y'){
			$dbIBlock = CIBlock::GetList(array('SORT' => 'ASC', 'ID' => 'DESC'), array("TYPE" => 'catalog',	"SITE_ID" => $_REQUEST['site_id']));
			$dbIBlock = new CIBlockResult($dbIBlock);

			while($arIBlock = $dbIBlock->Fetch())
			{
				$arIBlockSelect[$arIBlock['ID']] = $arIBlock['NAME'].' [' . $arIBlock['CODE'] .  ']';
			}
			if(count($arIBlockSelect) > 0){
				$this->content .= '<div class="wizard-input-form-block">
					<h4>'.GetMessage("CATWIZ_STEP_CATALOG_EDIT").'</h4>
					<div class="wizard-input-form-block-content">
						<div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowSelectField("PARAM[catalogId]", $arIBlockSelect).'</div>
					</div>
				</div>';
				
				if($PARAM['catalogId'])
					$this->content .= $this->ShowHiddenField("PARAM[oldcatalogId]", $PARAM['catalogId']);
			} else {
				$this->content .= '<br><br>'.GetMessage("CATWIZ_NO_CATALOG").' ';
				$this->content .= $this->ShowHiddenField('PARAM[catalogId]', 'new');
			}

		} else {
			$this->content .= $this->ShowHiddenField('PARAM[catalogId]', 'new');
		}
	}
}

class StepSettings extends CWizardStep
{
	function InitStep()
	{
		$this->SetTitle(GetMessage('CATWIZ_STEP_SETTINGS_TITLE'));
		$this->SetNextStep("step_props");
		$this->SetPrevStep("step_description");
		$this->SetStepID("step_settings");
		$this->SetCancelStep("cancel");
	}
	
	function OnPostForm()
	{
		$wizard = &$this->GetWizard();
		$PARAM = $wizard->GetVar("PARAM");

		if (strlen($PARAM['NAME']) <= 0)
			$this->SetError(GetMessage('CATWIZ_ERROR_NO_CAT_NAME'));
		elseif($PARAM["catalogId"] == 'new'){
			if (strlen($PARAM['CODE']) <= 0)
				$this->SetError(GetMessage('CATWIZ_ERROR_NO_CAT_CODE'));
			elseif (preg_match('/[^a-zA-Z0-9_\-]/', $PARAM['CODE']))
				$this->SetError(GetMessage('CATWIZ_ERROR_WRONG_CAT_CODE'));
			else
			{
				$dbr = CIBlock::GetList(Array(), Array("SITE_ID"=>$_REQUEST['site_id'], "CODE"=>$PARAM["CODE"]));
				if($dbr->Fetch())
					$this->SetError(str_replace("#IBLOCK_CODE#", htmlspecialchars($PARAM['CODE']), GetMessage("CATWIZ_ERROR_CODE_EXISTS")));
			}
		}
		
		$res = $this->SaveFile("catalogImg", Array("extensions" => "gif,jpg,jpeg,png", "max_height" => 100, "max_width" => 100, "make_preview" => "Y"));
	}
	
	function ShowStep()
	{

		$this->content = CUtil::InitJSCore(array('translit'), true);
	
		$wizard =& $this->GetWizard();
		$PARAM = $wizard->GetVar("PARAM");

		$res = CIBlock::GetByID($PARAM["catalogId"]);
		$arCatalogBlock = $res->Fetch();
		
		$CatWizRecommend = GetMessage("CATWIZ_PROP_RECOMMEND");
		
		if($PARAM["catalogId"] != 'new'){
			$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$PARAM["catalogId"], 'CODE'=>'RECOMMEND'));
			if ($prop_fields = $properties->GetNext()){
				$CatWizRecommend = $prop_fields['NAME'];	
			}
		}
		
		$wizard->SetDefaultVars(array(
			'PARAM' => array(
				'NAME' => ($PARAM["catalogId"]=='new'?GetMessage("CATWIZ_DEFAULT_NAME"):$arCatalogBlock['NAME']),
				'DESCRIPTION' => ($PARAM["catalogId"]=='new'?'':$arCatalogBlock['DESCRIPTION']),
				'RECOMMEND' => $CatWizRecommend,
				'catalogImg' => $arCatalogBlock['PICTURE'],
				'CODE' => 'products',
			),
			'filter_table'=>'Y',
			'compare_table'=>'Y'
		));

		$this->content .= '<div class="wizard-input-form">
		<div class="wizard-input-form-block">
			<h4>'.GetMessage("CATWIZ_CAT_NAME").'</h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'PARAM[NAME]', array("size" => "20")).'</div>
			</div>
		</div>';
		
		if($PARAM["catalogId"] == 'new'){
			$wizard->SetDefaultVar("PARAM[CODE]", 'products');
		
			$this->content .= '<div class="wizard-input-form-block">
				<h4>'.GetMessage("CATWIZ_CAT_CODE").'</h4>
				<div class="wizard-input-form-block-content">
					<div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'PARAM[CODE]', array("size" => "20")).'</div>
					<div class="wizard-input-form-field-desc">&mdash; '.GetMessage('CATWIZ_CODE_DESC').'</div>			
				</div>
			</div>';
		}
		
		$this->content .= '<div class="wizard-input-form-block">
			<h4>'.GetMessage("CATWIZ_CAT_DESCRIPTION").'</h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-textarea">'.$this->ShowInputField('textarea', 'PARAM[DESCRIPTION]', array("rows"=>"3")).'</div>
				<div class="wizard-input-form-field-desc"></div>			
			</div>
		</div>';
		
		$this->content .= '<div class="wizard-input-form-block">
			<h4><label for="catalogImg">'.GetMessage("CATWIZ_CAT_IMG").'</label></h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-text">'.
					$this->ShowFileField("catalogImg", 
						Array(
							"show_file_info"=> "N", 
							"id" => "catalogImg", )). '<br />'. 
					CFile::ShowImage($arCatalogBlock['PICTURE'], 100, 100, "border=0 vspace=5").'</div>
			</div>
		</div>';
					
		$this->content .= '<div class="wizard-input-form-block">
			<h4>'.GetMessage("CATWIZ_CAT_RECOMMEND").'</h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-text">'.$this->ShowInputField('text', 'PARAM[RECOMMEND]', array("size" => "20")).'</div>
				<div class="wizard-input-form-field-desc"></div>			
			</div>
		</div>';
		
		$this->content .= '<div class="wizard-input-form-block">
			<h4>'.GetMessage("CATWIZ_CAT_EXT").'</h4>
			<div class="wizard-input-form-block-content">
				<div class="wizard-input-form-field wizard-input-form-field-checkbox">'.$this->ShowCheckBoxField("filter_table", 'Y', array('id'=>'filter_table', 'checked'.($wizard->GetVar('filter_table')=='Y'?'':'_no')=>'Y')).'<label for="filter_table"> '.GetMessage("CATWIZ_CAT_FILT").'</label></div>
				<div class="wizard-input-form-field wizard-input-form-field-checkbox">'.$this->ShowCheckBoxField("compare_table", 'Y', array('id'=>'compare_table', 'checked'.($wizard->GetVar('compare_table')=='Y'?'':'_no')=>'Y')).'<label for="compare_table"> '.GetMessage("CATWIZ_CAT_COMP").'</label></div>
			</div>
		</div>';
	
		$this->content .= '</div>';
		$this->content .= __WGetPROPSHiddens($_POST["PROPS"]);
	}
}


class StepProps extends CWizardStep
{
	function InitStep()
	{
		$this->SetTitle(GetMessage("CATWIZ_CAT_PROPS"));
		$this->SetNextStep("step_run");
		$this->SetPrevStep("step_settings");
		$this->SetStepID("step_props");
		$this->SetCancelStep("cancel");
	}
	
	function OnPostForm()
	{
		$wizard = &$this->GetWizard();
		$PARAM = $wizard->GetVar("PARAM");

		if (strlen($PARAM['NAME']) <= 0)
			$this->SetError(GetMessage('CATWIZ_ERROR_NO_CAT_NAME'));
		elseif($PARAM["catalogId"] == 'new'){
			if (strlen($PARAM['CODE']) <= 0)
				$this->SetError(GetMessage('CATWIZ_ERROR_NO_CAT_CODE'));
			elseif (preg_match('/[^a-zA-Z0-9_\-]/', $PARAM['CODE']))
				$this->SetError(GetMessage('CATWIZ_ERROR_WRONG_CAT_CODE'));
			else
			{
				$dbr = CIBlock::GetList(Array(), Array("SITE_ID"=>$_REQUEST['site_id'], "CODE"=>$PARAM["CODE"]));
				if($dbr->Fetch())
					$this->SetError(str_replace("#IBLOCK_CODE#", htmlspecialchars($PARAM['CODE']), GetMessage("CATWIZ_ERROR_CODE_EXISTS")));
			}
		}
	}
	
	function ShowStep()
	{
		$wizard =& $this->GetWizard();
		$PARAM = $wizard->GetVar("PARAM");
		
		if($PARAM["catalogId"] != 'new'){
			$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$PARAM["catalogId"]));
			while ($prop_fields = $properties->Fetch())
			{
				if($prop_fields['CODE'] != 'MORE_PHOTO' && $prop_fields['CODE'] != 'RECOMMEND'){
					$PropsBlock[$prop_fields['CODE']]['NAME']          = $prop_fields['NAME'];
					$PropsBlock[$prop_fields['CODE']]['ID']            = $prop_fields['ID'];
					$PropsBlock[$prop_fields['CODE']]['PROPERTY_TYPE'] = $prop_fields['PROPERTY_TYPE'];
					$PropsBlock[$prop_fields['CODE']]['LIST_TYPE']     = $prop_fields['LIST_TYPE'];
					$PropsBlock[$prop_fields['CODE']]['CODE']          = $prop_fields['CODE'];
					
					$db_enum_list = CIBlockProperty::GetPropertyEnum($prop_fields['CODE'], Array(), Array("IBLOCK_ID"=>$PARAM["catalogId"]));
					while($ar_enum_list = $db_enum_list->GetNext())
					{
						$PropsBlock[$prop_fields['CODE']]['VALUES'][$ar_enum_list['ID']] =   $ar_enum_list['VALUE'];
					}
				}
			}
		}

		$this->content .= GetMessage("CATWIZ_HELP_PROPS");

		$this->content .= '<div class="wizard-input-form-block">
			<h4>'.GetMessage("CATWIZ_PROP_STEP_TITLE").'</h4>
		</div>';
		$this->content .= '<scr'.'ipt>
		function __DelRow(id)
		{
			var row = document.getElementById("r"+id);
			var c = "";
			c += "<input type=\"hidden\" name=\"PROPS["+id+"][DEL]\" value=\"Y\">";
			row.innerHTML = c;
		}

		function __AddRow()
		{
			var rnew = document.getElementById("rnew");
			var row = rnew.parentNode.insertBefore(document.createElement("TR"), rnew);
			var rnd = Math.random();
			row.className = "propsrow";
			row.id = "r"+ rnd;
			var c = "";
			row.insertCell(-1).innerHTML = "<input type=\"text\" size=\"20\" name=\"PROPS["+rnd+"][NAME]\" id=\"PROPS["+rnd+"][NAME]\" value=\"\">";
			var r = row.insertCell(-1);
			r.noWrap = true;
			c += "<input type=\"radio\" name=\"PROPS["+rnd+"][TYPE]\" id=\"PROPS["+rnd+"][TYPE]_str\" value=\"str\" checked onclick=\"__TypeChanged(this, "+rnd+")\"><label for=\"PROPS["+rnd+"][TYPE]_str\">'.GetMessage("CATWIZ_PROP_STR").'</label><br>";
			c += "<input type=\"radio\" name=\"PROPS["+rnd+"][TYPE]\" id=\"PROPS["+rnd+"][TYPE]_lst\" value=\"lst\" onclick=\"__TypeChanged(this, "+rnd+")\"><label for=\"PROPS["+rnd+"][TYPE]_lst\">'.GetMessage("CATWIZ_PROP_LST").'</label><br>";
			c += "<input type=\"radio\" name=\"PROPS["+rnd+"][TYPE]\" id=\"PROPS["+rnd+"][TYPE]_chk\" value=\"chk\" onclick=\"__TypeChanged(this, "+rnd+")\"><label for=\"PROPS["+rnd+"][TYPE]_chk\">'.GetMessage("CATWIZ_PROP_CHK").'</label>";
			r.innerHTML = c;
			row.insertCell(-1).innerHTML = "<span id=\"d"+rnd+"\" style=\"display:none;\"><input type=\"text\" size=\"20\" name=\"PROPS["+rnd+"][VALUES][]\"><br><a href=\"javascript:void(0);\" onclick=\"__AddVal(this, "+rnd+")\">'.GetMessage("CATWIZ_PROP_MORE").'</a></span>&nbsp;";
			row.insertCell(-1).innerHTML = "<a href=\"javascript:__DelRow("+rnd+");\"><img src=\"/bitrix/wizards/bitrix/store.catalog/images/delete.gif\" border=\"0\" title=\"'.GetMessage("CATWIZ_PROP_DEL").'\"></a>";
			document.getElementById("PROPS["+rnd+"][NAME]").focus();
		}
		function __TypeChanged(ob, id)
		{
			if(ob.value == "str")
				document.getElementById("d"+id).style.display = "none";
			else
				document.getElementById("d"+id).style.display = "inline";
		}
		
		function __AddVal(ob, id)
		{
			var sp = document.createElement("SPAN");
			var rnd = Math.random();
			sp.innerHTML = "<input type=\"text\" size=\"20\" name=\"PROPS["+id+"][VALUES][]\" id=\"in"+rnd+"\"><br>";
			ob.parentNode.insertBefore(sp, ob);
			document.getElementById("in"+rnd).focus();
		}
		</scri'.'pt>';

		$this->content .= '<table style="width: 100%;" class="propstable" id="propstable" cellspacing="0" cellpadding="0" border="0">';
		$this->content .= '<tr style="background-color:#F0F0F0; font-weight: bold;"><td>'.GetMessage("CATWIZ_PROP_NAME").'</td><td>'.GetMessage("CATWIZ_PROP_TYPE").'</td><td width="200">'.GetMessage("CATWIZ_PROP_VAL").'</td><td>&nbsp;</td></tr>';

		if(count($_POST['PROPS']) > 0){
			$PROPS = $_POST['PROPS'];
		} elseif($PARAM["catalogId"] != 'new'){
			$PROPS = $PropsBlock;
		}
		$PROPS['new'] = Array('TYPE'=>'str');

		foreach($PROPS as $rnd=>$prop)
		{
			if($rnd=='new')
				$rnd = rand();
			
			else if($prop['NAME']=='')
				continue;
			
			if($PARAM["catalogId"] != 'new'){
				$this->content .= '<input type="hidden" name="PROPS['.$rnd.'][CODE]" value="'.$rnd.'">';
				$this->content .= '<input type="hidden" name="PROPS['.$rnd.'][ID]" value="'.$prop['ID'].'">';

				if($prop['PROPERTY_TYPE'] == 'S') $prop['TYPE']='str';
				else if($prop['PROPERTY_TYPE'] == 'L' && $prop['LIST_TYPE'] == 'L') $prop['TYPE'] = 'lst';
				else if($prop['PROPERTY_TYPE'] == 'L' && $prop['LIST_TYPE'] == 'C') $prop['TYPE'] = 'chk';
			}	
						
			if(!is_array($prop['VALUES']) || count($prop['VALUES'])<1)
				$prop['VALUES'] = Array('');
			
			$this->content .= '<tr class="propsrow" id="r'.$rnd.'">';
			$this->content .= '<td><input type="text" size="20" name="PROPS['.$rnd.'][NAME]" id="PROPS['.$rnd.'][NAME]" value="'.htmlspecialchars($prop['NAME']).'"></td>';
			$this->content .= '<td nowrap="nowrap">';
			$this->content .= '<input type="radio" name="PROPS['.$rnd.'][TYPE]" id="PROPS['.$rnd.'][TYPE]_str" value="str" onclick="__TypeChanged(this, \''.$rnd.'\')"'.($prop['TYPE']=='str'?' checked':'').'><label for="PROPS['.$rnd.'][TYPE]_str">'.GetMessage("CATWIZ_PROP_STR").'</label><br>';
			$this->content .= '<input type="radio" name="PROPS['.$rnd.'][TYPE]" id="PROPS['.$rnd.'][TYPE]_lst" value="lst" onclick="__TypeChanged(this, \''.$rnd.'\')"'.($prop['TYPE']=='lst'?' checked':'').'><label for="PROPS['.$rnd.'][TYPE]_lst">'.GetMessage("CATWIZ_PROP_LST").'</label><br>';
			$this->content .= '<input type="radio" name="PROPS['.$rnd.'][TYPE]" id="PROPS['.$rnd.'][TYPE]_chk" value="chk" onclick="__TypeChanged(this, \''.$rnd.'\')"'.($prop['TYPE']=='chk'?' checked':'').'><label for="PROPS['.$rnd.'][TYPE]_chk">'.GetMessage("CATWIZ_PROP_CHK").'</label>';
			$this->content .= '</td>';
			$this->content .= '<td><span id="d'.$rnd.'"'.($prop['TYPE']=='str'?' style="display:none;"':'').'>';
			
			foreach($prop['VALUES'] as $key => $vv)
				$this->content .= '<input type="text" size="20" name="PROPS['.$rnd.'][VALUES]['.$key.']" value="'.htmlspecialchars($vv).'"><br>';

			$this->content .= '<a href="javascript:void(0);" onclick="__AddVal(this, \''.$rnd.'\')">'.GetMessage("CATWIZ_PROP_MORE").'</a></span>&nbsp;</td>';
			$this->content .= '<td><a href="javascript:__DelRow(\''.$rnd.'\');"><img src="/bitrix/wizards/bitrix/store.catalog/images/delete.gif" border="0" title="'.GetMessage("CATWIZ_PROP_DEL").'"></a></td>';
			$this->content .= '</tr>';
		}

		$this->content .= '<tr id="rnew"><td colspan="4"><a href="javascript:__AddRow();"><b>'.GetMessage("CATWIZ_PROP_ADD").'</b></a></td></tr>';

		$this->content .= '</table>';
	
		$this->content .= '<input type="hidden" name="site_id" value="'.htmlspecialchars($_REQUEST['site_id']).'">';
	}
}


class StepRun extends CWizardStep
{
	function InitStep()
	{
		$wizard =& $this->GetWizard();
		$PARAM = $wizard->GetVar('PARAM');
		if($PARAM['catalogId'] == 'new' ){
			$this->SetTitle(GetMessage('CATWIZ_STEP_RUN_TITLE'));
		} else {
			$this->SetTitle(GetMessage('CATWIZ_STEP_RUN_TITLE_EDIT'));
		}
//		$this->SetNextStep("final");
//		$this->SetPrevStep("step_props");
		$this->SetStepID("step_run");
		$this->SetCancelStep("cancel");
	}
	
	function ShowStep()
	{
		$wizard =& $this->GetWizard();
		$PARAM = $wizard->GetVar('PARAM');
		$PROPS = $_POST['PROPS'];

		CModule::IncludeModule('catalog');

		if($_REQUEST['site_id']=='')
		{
			$this->SetError(GetMessage("CATWIZ_ERROR_BAD_SITE_ID"));
			return;
		}

		// инфоблок
		$SITE_ID = $_REQUEST['site_id'];

		$dbRes = CSite::GetByID($SITE_ID);
		$arSite = $dbRes->Fetch();
		
		$catalogImg = $wizard->GetVar("catalogImg");
		$arPICTURE = '';
		if($catalogImg > 0)
		{
			$arPICTURE = CFile::MakeFileArray($catalogImg);
			$arPICTURE["del"] = '';
			$arPICTURE["MODULE_ID"] = "iblock";
		}

		$ib = new CIBlock();
		if($PARAM["catalogId"] == 'new'){
		
			$IBLOCK_ID = $ib->Add(array(
				'ACTIVE' => 'Y',
				'NAME' => $PARAM['NAME'],
				'CODE' => $PARAM['CODE'],
				'LIST_PAGE_URL' => '#SITE_DIR#/catalog/#IBLOCK_CODE#/',
				'SECTION_PAGE_URL' => '#SITE_DIR#/catalog/#IBLOCK_CODE#/#SECTION_CODE#/',
				'DETAIL_PAGE_URL' => '#SITE_DIR#/catalog/#IBLOCK_CODE#/#SECTION_CODE#/#ELEMENT_CODE#/',
				'SITE_ID' => array($SITE_ID), 
				'INDEX_SECTION' => 'Y',
				'ELEMENTS_NAME' => GetMessage("WZD_ELEMENTS_NAME"),
				'ELEMENT_NAME' => GetMessage("WZD_ELEMENT_NAME"),
				'ELEMENT_ADD' => GetMessage("WZD_ELEMENT_ADD"),
				'ELEMENT_EDIT' => GetMessage("WZD_ELEMENT_EDIT"),
				'ELEMENT_DELETE' => GetMessage("WZD_ELEMENT_DELETE"),
				'SECTIONS_NAME' => GetMessage("WZD_SECTIONS_NAME"),
				'SECTION_NAME' => GetMessage("WZD_SECTION_NAME"),
				'SECTION_ADD' => GetMessage("WZD_SECTION_ADD"),
				'SECTION_EDIT' => GetMessage("WZD_SECTION_EDIT"),
				'SECTION_DELETE' => GetMessage("WZD_SECTION_DELETE"),
				'GROUP_ID' => Array('2' => 'R', '1' => 'X'),
				'FIELDS' => array ( 'IBLOCK_SECTION' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => '', ), 'ACTIVE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'Y', ), 'ACTIVE_FROM' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'ACTIVE_TO' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'SORT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'NAME' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => '', ), 'PREVIEW_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'FROM_DETAIL' => 'N', 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => 95, 'DELETE_WITH_DETAIL' => 'N', 'UPDATE_WITH_DETAIL' => 'N', ), ), 'PREVIEW_TEXT_TYPE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text', ), 'PREVIEW_TEXT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'DETAIL_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => 95, ), ), 'DETAIL_TEXT_TYPE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text', ), 'DETAIL_TEXT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'XML_ID' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'CODE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => array ( 'UNIQUE' => 'Y', 'TRANSLITERATION' => 'Y', 'TRANS_LEN' => 100, 'TRANS_CASE' => 'L', 'TRANS_SPACE' => '_', 'TRANS_OTHER' => '_', 'TRANS_EAT' => 'Y', 'USE_GOOGLE' => 'Y', ), ), 'TAGS' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'SECTION_NAME' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => '', ), 'SECTION_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'FROM_DETAIL' => 'N', 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => 95, 'DELETE_WITH_DETAIL' => 'N', 'UPDATE_WITH_DETAIL' => 'N', ), ), 'SECTION_DESCRIPTION_TYPE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text', ), 'SECTION_DESCRIPTION' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'SECTION_DETAIL_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => 95, ), ), 'SECTION_XML_ID' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'SECTION_CODE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => array ( 'UNIQUE' => 'Y', 'TRANSLITERATION' => 'Y', 'TRANS_LEN' => 100, 'TRANS_CASE' => 'L', 'TRANS_SPACE' => '_', 'TRANS_OTHER' => '_', 'TRANS_EAT' => 'Y', 'USE_GOOGLE' => 'Y', ), ), ), 
				'IBLOCK_TYPE_ID' => 'catalog',
				'DESCRIPTION_TYPE' => 'text',
				'DESCRIPTION' => $PARAM['DESCRIPTION'],
			 	'PICTURE' => $arPICTURE, 
			));
			
		} else {
		
			$arFields = array(
				'ACTIVE' => 'Y',
				'NAME' => $PARAM['NAME'],
				'SITE_ID' => array($SITE_ID), 
				'DESCRIPTION' => $PARAM['DESCRIPTION'],
			 	'PICTURE' => $arPICTURE, 
			);
			
			if ($PARAM['catalogId'] > 0)
	  			$resUpdate = $ib->Update($PARAM['catalogId'], $arFields);
	  			
	  		if($resUpdate) $IBLOCK_ID = $PARAM['catalogId'];
	  			
		}
		
		if($IBLOCK_ID)
		{
			$fProps = '';
			$arProperty = Array();

			if($PARAM["catalogId"] == 'new'){
			
				// добавим типовые свойства
				$arProperties = array(
					'SPECIALOFFER' => array(
						'CODE' => 'SPECIALOFFER',
						'IBLOCK_ID' => $IBLOCK_ID,
						'NAME' => GetMessage("CATWIZ_SPEC"),
						'ACTIVE' => 'Y',
						'SORT' => 100,
						'PROPERTY_TYPE' => 'L',
						'MULTIPLE' => 'Y',
						'LIST_TYPE' => 'C',
						'SEARCHABLE' => 'Y',
						'FILTRABLE' => 'Y',
						'VERSION' => 1,
						'VALUES' => array(
							array(
								'XML_ID' => 'Y',
								'VALUE' => GetMessage("CATWIZ_PROP_YES"),
								'DEF' => 'N',
								'SORT' => 100,
							),
						),
					),
					'NEWPRODUCT' => array(
						'CODE' => 'NEWPRODUCT',
						'IBLOCK_ID' => $IBLOCK_ID,
						'NAME' => GetMessage("CATWIZ_PROP_NEW"),
						'ACTIVE' => 'Y',
						'SORT' => 110,
						'PROPERTY_TYPE' => 'L',
						'MULTIPLE' => 'Y',
						'LIST_TYPE' => 'C',
						'SEARCHABLE' => 'Y',
						'FILTRABLE' => 'Y',
						'VERSION' => 1,
						'VALUES' => array(
							array(
								'XML_ID' => 'Y',
								'VALUE' => GetMessage("CATWIZ_PROP_YES"),
								'DEF' => 'N',
								'SORT' => 100,
							),
						),
					),
					'SALELEADER' => array(
						'CODE' => 'SALELEADER',
						'IBLOCK_ID' => $IBLOCK_ID,
						'NAME' => GetMessage("CATWIZ_PROP_SALELEADER"),
						'ACTIVE' => 'Y',
						'SORT' => 120,
						'PROPERTY_TYPE' => 'L',
						'MULTIPLE' => 'Y',
						'LIST_TYPE' => 'C',
						'SEARCHABLE' => 'Y',
						'FILTRABLE' => 'Y',
						'VERSION' => 1,
						'VALUES' => array(
							array(
								'XML_ID' => 'Y',
								'VALUE' => GetMessage("CATWIZ_PROP_YES"),
								'DEF' => 'N',
								'SORT' => 100,
							),
						),
					),
				);	
					
				$ibp = new CIBlockProperty();
	 			foreach ($arProperties as $arProp)
				{
					$pid = $ibp->Add($arProp);
					if($pid>0)
					{
						$fProps .= '--PROPERTY_'.$pid.'--#--'.$arProp['NAME'].'--,';
						$arProperty[$arProp["CODE"]] = $pid;
					}
				}
				
			}
			
			$p = 0;
			// добавим пользовательские свойства
			if(is_array($PROPS))
			{
				foreach($PROPS as $prop)
				{
					if($prop["DEL"] == 'Y'){	
						$ibp = new CIBlockProperty;
						$ibp->Delete($prop["ID"]);
					} else {
						if(trim($prop["NAME"]) == '')
							continue;
						if($prop["ID"] > 0)
							$arProp['CODE'] = $prop["CODE"];
						else
							$arProp['CODE'] = 'PROP'.($p + 1);
							
						$arProp = array(
							'CODE' => $arProp['CODE'],
							'IBLOCK_ID' => $IBLOCK_ID,
							'NAME' => $prop["NAME"],
							'ACTIVE' => 'Y',
							'SORT' => 200 + $p*10,
							'PROPERTY_TYPE' => ($prop["TYPE"]=='str'?'S':'L'),
							'MULTIPLE' => ($prop["TYPE"]=='chk'?'Y':'N'),
							'SEARCHABLE' => 'Y',
							'LIST_TYPE' => ($prop["TYPE"]=='chk'?'C':'L'),
							'FILTRABLE' => 'Y',
							'VERSION' => 1,
						);
						
						if($prop["ID"] > 0 && ($prop["CODE"] != 'SPECIALOFFER' && $prop["CODE"] != 'NEWPRODUCT' && $prop["CODE"] != 'SALELEADER'))
							$strProps .= $p." => \"".$prop["CODE"]."\", \r\n"; 
						else 
							$strProps .= $p." => \"PROP".($p+1)."\", \r\n"; 
	
						$p++;
	
						if($prop["TYPE"]=='lst' || $prop["TYPE"]=='chk')
						{
							$nn = 0;
							$arProp['VALUES'] = array();
							foreach($prop["VALUES"]  as $key=>$vv)
							{
								if(trim($vv)!='')
									$arProp['VALUES'][$key] = array('VALUE'=>$vv,'DEF' => 'N', 'SORT' => 100 + $nn);
								$nn += 10;
							}
						}
	
						$arProperties[$arProp['CODE']] = $arProp;
						$ibp = new CIBlockProperty;
						if($prop["ID"] > 0){
							$ibp->Update($prop["ID"], $arProp);
							$fProps .= '--PROPERTY_'.$prop["ID"].'--#--'.$arProp['NAME'].'--,';
							$arProperty[$arProp['CODE']] = $prop["ID"];
						}else{
							$pid = $ibp->Add($arProp);
							if($pid > 0)
							{
								$fProps .= '--PROPERTY_'.$pid.'--#--'.$arProp['NAME'].'--,';
								$arProperty[$arProp["CODE"]] = $pid;
							}
						}
					}
				}
			}
			
			if($PARAM["catalogId"] == 'new'){
			
				// добавим типовые свойства RECOMMEND и MORE_PHOTO
				$arPropertiesRM = array(
					'RECOMMEND' => array(
							'CODE' => 'RECOMMEND',
							'IBLOCK_ID' => $IBLOCK_ID,
							'NAME' => GetMessage("CATWIZ_PROP_RECOMMEND"),
							'ACTIVE' => 'Y',
							'SORT' => 990,
							'PROPERTY_TYPE' => 'E',
							'LINK_IBLOCK_TYPE_ID' => 'catalog',
							'LINK_IBLOCK_ID' => $IBLOCK_ID,
							'MULTIPLE' => 'Y',
							'SEARCHABLE' => 'N',
							'FILTRABLE' => 'N',
							'MULTIPLE_CNT' => 1,
							'WITH_DESCRIPTION' => 'Y',
							'VERSION' => 1,
					),
					'MORE_PHOTO' => array(
							'CODE' => 'MORE_PHOTO',
							'IBLOCK_ID' => $IBLOCK_ID,
							'NAME' => GetMessage("CATWIZ_PROP_MORE_PHOTO"),
							'ACTIVE' => 'Y',
							'SORT' => 1000,
							'PROPERTY_TYPE' => 'F',
							'FILE_TYPE' => 'jpg,gif,bmp,png,jpeg',
							'MULTIPLE' => 'Y',
							'SEARCHABLE' => 'N',
							'FILTRABLE' => 'N',
							'MULTIPLE_CNT' => 1,
							'WITH_DESCRIPTION' => 'Y',
							'VERSION' => 1,
						)
				);
					
				$ibp = new CIBlockProperty();
	 			foreach ($arPropertiesRM as $arProp)
				{
					$pid = $ibp->Add($arProp);
					if($pid>0)
					{
						$fProps .= '--PROPERTY_'.$pid.'--#--'.$arProp['NAME'].'--,';
						$arProperty[$arProp["CODE"]] = $pid;
					}
				}
				
				//	добавим торговый каталог
				CCatalog::Add(array('IBLOCK_ID' => $IBLOCK_ID,  "SUBSCRIPTION" => 'N'));
							
			} else {
				$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$PARAM["catalogId"], 'CODE'=>'RECOMMEND'));
				if ($prop_fields = $properties->GetNext())
				{
					if($prop_fields['NAME'] != $PARAM['RECOMMEND']){
						$prop_fields['NAME'] = $PARAM['RECOMMEND'];
						$ibp = new CIBlockProperty();
						$sss = $ibp->Update($prop_fields["ID"], array('NAME'=>$prop_fields['NAME'] ));
					}
					$fProps .= '--PROPERTY_'.$prop_fields['ID'].'--#--'.$prop_fields['NAME'].'--,';
					$arProperty[$prop_fields['CODE']] = $prop_fields['ID'];

				}

				$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$PARAM["catalogId"], 'CODE'=>'MORE_PHOTO'));
				if ($prop_fields = $properties->GetNext())
				{
					$fProps .= '--PROPERTY_'.$prop_fields['ID'].'--#--'.$prop_fields['NAME'].'--,';
					$arProperty[$prop_fields['CODE']] = $prop_fields['ID'];
				}
				
			}
			
			// добавим настройки форм
			$f1 = 'edit1--#--'.GetMessage("WZD_OPTION_CATALOG_1").'--,--ACTIVE--#--'.GetMessage("WZD_OPTION_CATALOG_2").'--,--NAME--#--'.GetMessage("WZD_OPTION_CATALOG_3").'--,--CODE--#--'.GetMessage("WZD_OPTION_CATALOG_4").'--,--DETAIL_PICTURE--#--'.GetMessage("WZD_OPTION_CATALOG_5").'--,';
			$f1 .= $fProps;
			$f1 .= '--CATALOG--#--'.GetMessage("WZD_OPTION_CATALOG_20").'--;--cedit1--#--'.GetMessage("WZD_OPTION_CATALOG_27").'--,--PREVIEW_TEXT--#--'.GetMessage("WZD_OPTION_CATALOG_6").'--,--DETAIL_TEXT--#--'.GetMessage("WZD_OPTION_CATALOG_7").'--,--cedit1_csection1--#----'.GetMessage("WZD_OPTION_CATALOG_9").'--,--SECTIONS--#--'.GetMessage("WZD_OPTION_CATALOG_30").'--;--';

			CUserOptions::SetOption("form", "form_element_".$IBLOCK_ID, array ( 'tabs' => $f1, ));
			CUserOptions::SetOption("form", "form_section_".$IBLOCK_ID, array ( 'tabs' => 'edit1--#--'.GetMessage("WZD_OPTION_CATALOG_21").'--,--ACTIVE--#--'.GetMessage("WZD_OPTION_CATALOG_22").'--,--IBLOCK_SECTION_ID--#--'.GetMessage("WZD_OPTION_CATALOG_23").'--,--NAME--#--'.GetMessage("WZD_OPTION_CATALOG_24").'--,--CODE--#--'.GetMessage("WZD_OPTION_CATALOG_25").'--,--SORT--#--'.GetMessage("WZD_OPTION_CATALOG_28").'--,--PICTURE--#--'.GetMessage("WZD_OPTION_CATALOG_26").'--,--DESCRIPTION--#--'.GetMessage("WZD_OPTION_CATALOG_27").'--;--', ));
			CUserOptions::SetOption("list", "tbl_iblock_list_".md5("catalog".".".$IBLOCK_ID), array ( 'columns' => 'DETAIL_PICTURE,NAME,CATALOG_GROUP_1,PROPERTY_'.$arProperty["SPECIALOFFER"].',PROPERTY_'.$arProperty["NEWPRODUCT"].',PROPERTY_'.$arProperty["SALELEADER"].'', 'by' => 'timestamp_x', 'order' => 'desc', 'page_size' => '20', ));

			if($PARAM["catalogId"] == 'new'){
			
				$CatalogBlockCode = $PARAM['CODE'];
				
			} else {
			
				$res = CIBlock::GetByID($PARAM["catalogId"]);
				$arCatalogBlock = $res->GetNext();
				$CatalogBlockCode = $arCatalogBlock['CODE'];
				
			}
			
			// создадим/изменим файл для отображения
			$path_from = $_SERVER['DOCUMENT_ROOT'].$wizard->GetPath().'/public/'.LANGUAGE_ID;
			$path_to = $_SERVER['DOCUMENT_ROOT'].$arSite['DIR'].'catalog/'.$CatalogBlockCode;
			
			CheckDirPath($path_to.'/');
			
			$arFiles = array('index.php', '.section.php');
			
			foreach ($arFiles as $file)
			{
				//unlink($path_to.'/'.$file);
				copy($path_from.'/'.$file, $path_to.'/'.$file);
				CWizardUtil::ReplaceMacros(
					$path_to.'/'.$file, 
					array(
						"IBLOCK_ID" => $IBLOCK_ID,
						"IBLOCK_NAME" => addslashes($PARAM['NAME']), 
						"IBLOCK_CODE" => $CatalogBlockCode, 
						"SITE_DIR" => $arSite['DIR'],
						"USE_COMPARE" => ($wizard->GetVar("compare_table")=="Y"?"Y":"N"),
						"USE_FILTER" =>  ($wizard->GetVar("filter_table")=="Y"?"Y":"N"),
						"PROPS" => $strProps,
						"PROPS_DETAIL" => $strProps.$p." => \"MORE_PHOTO\"\r\n, ".($p+1)." => \"RECOMMEND\"\r\n",
					)
				);
			}
			
			$arFiles = array('m_index.php');
			$siteMobileFolder = COption::GetOptionString("bitrix.household", "siteMobileFolder", '', $SITE_ID);
			foreach ($arFiles as $file)
			{
				
				if(!empty($siteMobileFolder)){	
					$path_to = $_SERVER['DOCUMENT_ROOT'].$arSite['DIR']. $siteMobileFolder .'/catalog/'.$CatalogBlockCode;
				
					CheckDirPath($path_to.'/');
				
					copy($path_from.'/'.$file, $path_to.'/index.php');
					CWizardUtil::ReplaceMacros(
						$path_to.'/index.php', 
						array(
							"IBLOCK_ID" => $IBLOCK_ID,
							"IBLOCK_CODE" => $CatalogBlockCode, 
							"SITE_DIR" => $arSite['DIR']. $siteMobileFolder .'/',
							"PROPS_DETAIL" => $strProps.$p." => \"MORE_PHOTO\"\r\n, ".($p+1)." => \"RECOMMEND\"\r\n",
						)
					);
				}
			}


			if($PARAM["catalogId"] == 'new'){
				CUrlRewriter::Add(
					array(
						"SITE_ID" => $SITE_ID,
						"CONDITION" => "#^".$arSite['DIR']."catalog/".$PARAM['CODE']."/#",
						"ID" => 'bitrix:catalog',
						"PATH" => $arSite['DIR'].'catalog/'.$PARAM['CODE'].'/index.php'
					)
				);
				if(!empty($siteMobileFolder)){	
					CUrlRewriter::Add(
						array(
							"SITE_ID" => $SITE_ID,
							"CONDITION" => "#^".$arSite['DIR'] . $siteMobileFolder ."/catalog/".$PARAM['CODE']."/#",
							"ID" => 'bitrix:catalog',
							"PATH" => $arSite['DIR']. $siteMobileFolder .'/catalog/'.$PARAM['CODE'].'/index.php'
						)
					);
				}

				$this->content .= GetMessage("CATWIZ_OK").'<br><br>';
				
			} else {
				
				$this->content .= GetMessage("CATWIZ_OK_EDIT").'<br><br>';
				
			}

			$this->content .= '<a href="'.$arSite['DIR'].'catalog/'.$CatalogBlockCode.'/?clear_cache=Y">'.GetMessage("CATWIZ_GOTO").'</a>';

		}
		else
		{
			$this->content .= $ib->LAST_ERROR;
		}
		
		$this->content .= __WGetPROPSHiddens($_POST["PROPS"]);
	}
}

class StepCancel extends CWizardStep
{
	function InitStep()
	{
		$this->SetTitle(GetMessage('STATWIZ_STEP3_TITLE'));
		$this->SetNextStep("final");
		$this->SetPrevStep("step2");
		$this->SetStepID("step3");
		$this->SetCancelStep("cancel");
	}

	function ShowStep()
	{

	}
}
?>
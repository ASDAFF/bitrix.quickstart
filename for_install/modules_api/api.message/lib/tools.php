<?php

namespace Api\Message;

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Tools
{
	public static function findUserId($fieldName, $arUserId, $formName){

		$html = '<table cellpadding="0" cellspacing="0" border="0" class="nopadding" width="100%" id="tb'.md5($formName).'">';

		if($arUserId){
			foreach($arUserId as $i=>$userId){

				$value = array(
					 'VALUE' =>  intval($userId),
				);
				$params = array(
					 'VALUE' => $fieldName .'['. $i .']',
					 'FORM_NAME' => $formName,
				);

				$html .= '<tr><td>';
				$html .= self::GetPropertyFieldHtml($value, $params);
				$html .= '</td></tr>';
			}
		}
		else{
			for($i=0; $i<3; $i++){

				$value = array(
					 'VALUE' =>  '',
				);
				$params = array(
					 'VALUE' => $fieldName .'['. $i .']',
					 'FORM_NAME' => $formName,
				);

				$html .= '<tr><td>';
				$html .= self::GetPropertyFieldHtml($value, $params);
				$html .= '</td></tr>';
			}

		}

		//ƒл€ addNewRow можно задать регул€рку дл€ поиска особого названи€ пол€ /\[(n)([0-9]*)\]/g
		//bitrix/js/main/usertype.js
		$html .= '<tr><td>';
		$html .= '<input type="button" 
				value="'.Loc::getMessage('ASM_LT_ADDNEWROW_BTN').'" 
				onClick="addNewRow(\'tb'.md5($formName).'\',\'\')">';
		$html .= '</td></tr>';

		$html .= '</table>';

		return $html;
	}

	//bitrix/modules/iblock/admin_tools.php
	public static function GetPropertyFieldHtml($value, $strHTMLControlName)
	{
		global $USER;
		$default_value = intVal($value["VALUE"]);
		$res = "";
		if ($default_value == $USER->GetID())
		{
			$select = "CU";
			$res = "[<a title='".Loc::getMessage("MAIN_EDIT_USER_PROFILE")."'  href='/bitrix/admin/user_edit.php?ID=".$USER->GetID()."&lang=".LANG."'>".$USER->GetID()."</a>] (".htmlspecialcharsbx($USER->GetLogin()).") ".htmlspecialcharsbx($USER->GetFirstName())." ".htmlspecialcharsbx($USER->GetLastName());
		}
		elseif ($default_value > 0)
		{
			$select = "SU";
			$rsUsers = \CUser::GetList($by, $order, array("ID" => $default_value));
			if ($arUser = $rsUsers->Fetch())
				$res = "[<a title='".Loc::getMessage("MAIN_EDIT_USER_PROFILE")."'  href='/bitrix/admin/user_edit.php?ID=".$arUser["ID"]."&lang=".LANG."'>".$arUser["ID"]."</a>] (".htmlspecialcharsbx($arUser["LOGIN"]).") ".htmlspecialcharsbx($arUser["NAME"])." ".htmlspecialcharsbx($arUser["LAST_NAME"]);
			else
				$res = "&nbsp;".Loc::getMessage("MAIN_NOT_FOUND");
		}
		else
		{
			$select = "none";
			$default_value = "";
		}

		//!replace x to _ and usertype.js work fine
		$name_x = preg_replace("/([^a-z0-9])/is", "_", $strHTMLControlName["VALUE"]);
		if (strLen(trim($strHTMLControlName["FORM_NAME"])) <= 0)
			$strHTMLControlName["FORM_NAME"] = "form_element";

		ob_start();
		?><select id="SELECT<?=htmlspecialcharsbx($strHTMLControlName["VALUE"])?>" name="SELECT<?=htmlspecialcharsbx($strHTMLControlName["VALUE"])?>" onchange="if(this.value == 'none')
		 {
		 var v=document.getElementById('<?=htmlspecialcharsbx($strHTMLControlName["VALUE"])?>');
		 v.value = '';
		 v.readOnly = true;
		 document.getElementById('FindUser<?=$name_x?>').disabled = true;
		 }
		 else
		 {
		 var v=document.getElementById('<?=htmlspecialcharsbx($strHTMLControlName["VALUE"])?>');
		 v.value = this.value == 'CU'?'<?=$USER->GetID()?>':'';
		 v.readOnly = false;
		 document.getElementById('FindUser<?=$name_x?>').disabled = false;
		 }">
		<option value="none"<?if($select=="none")echo " selected"?>><?=Loc::getMessage("ASM_LT_USERID_NONE")?></option>
		<option value="CU"<?if($select=="CU")echo " selected"?>><?=Loc::getMessage("ASM_LT_USERID_CURR")?></option>
		<option value="SU"<?if($select=="SU")echo " selected"?>><?=Loc::getMessage("ASM_LT_USERID_OTHR")?></option>
		</select>&nbsp;
		<?echo self::FindUserIDNew(htmlspecialcharsbx($strHTMLControlName["VALUE"]), $value["VALUE"], $res, htmlspecialcharsEx($strHTMLControlName["FORM_NAME"]), $select);
		$return = ob_get_contents();
		ob_end_clean();

		return  $return;
	}

	public static function FindUserIDNew($tag_name, $tag_value, $user_name="", $form_name = "form1", $select="none", $tag_size = "3", $tag_maxlength="", $button_value = "...", $tag_class="typeinput", $button_class="tablebodybutton", $search_page="/bitrix/admin/user_search.php")
	{
		global $APPLICATION, $USER;

		//!replace x to _ and usertype.js work fine
		$tag_name_x = preg_replace("/([^a-z0-9])/is", "_", $tag_name);
		$tag_name_escaped = \CUtil::JSEscape($tag_name);

		if($APPLICATION->GetGroupRight("main") >= "R")
		{
			$strReturn = "
<input type=\"text\" name=\"".$tag_name."\" id=\"".$tag_name."\" value=\"".($select=="none"?"":$tag_value)."\" size=\"".$tag_size."\" maxlength=\"".$tag_maxlength."\" class=\"".$tag_class."\">
<IFRAME style=\"width:0px; height:0px; border: 0px\" src=\"javascript:void(0)\" name=\"hiddenframe".$tag_name."\" id=\"hiddenframe".$tag_name."\"></IFRAME>
<input class=\"".$button_class."\" type=\"button\" name=\"FindUser".$tag_name_x."\" id=\"FindUser".$tag_name_x."\" OnClick=\"window.open('".$search_page."?lang=".LANGUAGE_ID."&FN=".$form_name."&FC=".$tag_name_escaped."', '', 'scrollbars=yes,resizable=yes,width=760,height=500,top='+Math.floor((screen.height - 560)/2-14)+',left='+Math.floor((screen.width - 760)/2-5));\" value=\"".$button_value."\" ".($select=="none"?"disabled":"").">
<span id=\"div_".$tag_name."\">".$user_name."</span>
<script>
";
			if($user_name=="")
				$strReturn.= "var tv".$tag_name_x."='';\n";
			else
				$strReturn.= "var tv".$tag_name_x."='".\CUtil::JSEscape($tag_value)."';\n";

			$strReturn.= "
function Ch".$tag_name_x."()
{
	var DV_".$tag_name_x.";
	DV_".$tag_name_x." = document.getElementById(\"div_".$tag_name_escaped."\");
	if (!!DV_".$tag_name_x.")
	{
		if (
			document.".$form_name."
			&& document.".$form_name."['".$tag_name_escaped."']
			&& typeof tv".$tag_name_x." != 'undefined'
			&& tv".$tag_name_x." != document.".$form_name."['".$tag_name_escaped."'].value
		)
		{
			tv".$tag_name_x."=document.".$form_name."['".$tag_name_escaped."'].value;
			if (tv".$tag_name_x."!='')
			{
				DV_".$tag_name_x.".innerHTML = '<i>".Loc::getMessage("ASM_LT_FINDUSERID_WAIT")."</i>';

				if (tv".$tag_name_x."!=".intVal($USER->GetID()).")
				{
					document.getElementById(\"hiddenframe".$tag_name_escaped."\").src='/bitrix/admin/get_user.php?ID=' + tv".$tag_name_x."+'&strName=".$tag_name_escaped."&lang=".LANG.(defined("ADMIN_SECTION") && ADMIN_SECTION===true?"&admin_section=Y":"")."';
					document.getElementById('SELECT".$tag_name_escaped."').value = 'SU';
				}
				else
				{
					DV_".$tag_name_x.".innerHTML = '".\CUtil::JSEscape("[<a title=\"".Loc::getMessage("ASM_LT_USER_PROFILE")."\" class=\"tablebodylink\" href=\"/bitrix/admin/user_edit.php?ID=".$USER->GetID()."&lang=".LANG."\">".$USER->GetID()."</a>] (".htmlspecialcharsbx($USER->GetLogin()).") ".htmlspecialcharsbx($USER->GetFirstName())." ".htmlspecialcharsbx($USER->GetLastName()))."';
					document.getElementById('SELECT".$tag_name_escaped."').value = 'CU';
				}
			}
			else
			{
				DV_".$tag_name_x.".innerHTML = '';
				document.getElementById('SELECT".$tag_name_escaped."').value = 'SU';
			}
		}
		else if (
			DV_".$tag_name_x."
			&& DV_".$tag_name_x.".innerHTML.length > 0
			&& document.".$form_name."
			&& document.".$form_name."['".$tag_name_escaped."']
			&& document.".$form_name."['".$tag_name_escaped."'].value == ''
		)
		{
			document.getElementById('div_".$tag_name."').innerHTML = '';
		}
	}
	setTimeout(function(){Ch".$tag_name_x."()},1000);
}
Ch".$tag_name_x."();
//-->
</script>
";
		}
		else
		{
			$strReturn = "
			<input type=\"text\" name=\"$tag_name\" id=\"$tag_name\" value=\"$tag_value\" size=\"$tag_size\" maxlength=\"strMaxLenght\">
			<input type=\"button\" name=\"FindUser".$tag_name_x."\" id=\"FindUser".$tag_name_x."\" OnClick=\"window.open('".$search_page."?lang=".LANGUAGE_ID."&FN=$form_name&FC=$tag_name_escaped', '', 'scrollbars=yes,resizable=yes,width=760,height=560,top='+Math.floor((screen.height - 560)/2-14)+',left='+Math.floor((screen.width - 760)/2-5));\" value=\"$button_value\">
			$user_name
			";
		}
		return $strReturn;
	}
}

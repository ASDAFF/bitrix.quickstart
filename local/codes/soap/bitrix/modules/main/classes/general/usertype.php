<?
/**
 * usertype.php, ���������������� ��������
 *
 * �������� ������ ��� ��������� ���������������� �������.
 * @author Bitrix <support@bitrixsoft.com>
 * @version 1.0
 * @package usertype
 * @todo �������� ���������
 */

CModule::AddAutoloadClasses(
	"main",
	array(
		"CUserTypeString" => "classes/general/usertypestr.php",
		"CUserTypeInteger" => "classes/general/usertypeint.php",
		"CUserTypeDouble" => "classes/general/usertypedbl.php",
		"CUserTypeDateTime" => "classes/general/usertypetime.php",
		"CUserTypeBoolean" => "classes/general/usertypebool.php",
		"CUserTypeFile" => "classes/general/usertypefile.php",
		"CUserTypeEnum" => "classes/general/usertypeenum.php",
		"CUserTypeIBlockSection" => "classes/general/usertypesection.php",
		"CUserTypeIBlockElement" => "classes/general/usertypeelement.php",
		"CUserTypeStringFormatted" => "classes/general/usertypestrfmt.php",
	)
);

IncludeModuleLangFile(__FILE__);

/**
 * ������ ����� ������������ ��� ���������� ����������� ���������������� �������.
 *
 * <p>�������, �������� ���������� � ���������� ���������� ������� b_user_field.</p>
create table b_user_field (
	ID		int(11) not null auto_increment,
	ENTITY_ID 	varchar(20),
	FIELD_NAME	varchar(20),
	USER_TYPE_ID	varchar(50),
	XML_ID		varchar(255),
	SORT		int,
	MULTIPLE	char(1) not null default 'N',
	MANDATORY	char(1) not null default 'N',
	SHOW_FILTER	char(1) not null default 'N',
	SHOW_IN_LIST	char(1) not null default 'Y',
	EDIT_IN_LIST	char(1) not null default 'Y',
	IS_SEARCHABLE	char(1) not null default 'N',
	SETTINGS	text,
	PRIMARY KEY (ID),
	UNIQUE ux_user_type_entity(ENTITY_ID, FIELD_NAME)
)
------------------
ID
ENTITY_ID (example: IBLOCK_SECTION, USER ....)
FIELD_NAME (example: UF_EMAIL, UF_SOME_COUNTER ....)
SORT -- used to do check in the specified order
BASE_TYPE - String, Number, Integer, Enumeration, File, DateTime
USER_TYPE_ID
SETTINGS (blob) -- to store some settings which may be useful for an field instance
[some base settings comon to all types: mandatory or no, etc.]
 * <p>b_user_field</p>
 * <ul>
 * <li><b>ID</b> int(11) not null auto_increment
 * <li>ENTITY_ID varchar(20)
 * <li>FIELD_NAME varchar(20)
 * <li>USER_TYPE_ID varchar(50)
 * <li>XML_ID varchar(255)
 * <li>SORT int
 * <li>MULTIPLE char(1) not null default 'N'
 * <li>MANDATORY char(1) not null default 'N'
 * <li>SHOW_FILTER char(1) not null default 'N'
 * <li>SHOW_IN_LIST char(1) not null default 'Y'
 * <li>EDIT_IN_LIST char(1) not null default 'Y'
 * <li>IS_SEARCHABLE char(1) not null default 'N'
 * <li>SETTINGS text
 * <li>PRIMARY KEY (ID),
 * <li>UNIQUE ux_user_type_entity(ENTITY_ID, FIELD_NAME)
 * </ul>
create table b_user_field_lang (
	USER_FIELD_ID int(11) REFERENCES b_user_field(ID),
	LANGUAGE_ID char(2),
	EDIT_FORM_LABEL varchar(255),
	LIST_COLUMN_LABEL varchar(255),
	LIST_FILTER_LABEL varchar(255),
	ERROR_MESSAGE varchar(255),
	HELP_MESSAGE varchar(255),
	PRIMARY KEY (USER_FIELD_ID, LANGUAGE_ID)
)
 * <p>b_user_field_lang</p>
 * <ul>
 * <li><b>USER_FIELD_ID</b> int(11) REFERENCES b_user_field(ID)
 * <li><b>LANGUAGE_ID</b> char(2)
 * <li>EDIT_FORM_LABEL varchar(255)
 * <li>LIST_COLUMN_LABEL varchar(255)
 * <li>LIST_FILTER_LABEL varchar(255)
 * <li>ERROR_MESSAGE varchar(255)
 * <li>HELP_MESSAGE varchar(255)
 * <li>PRIMARY KEY (USER_FIELD_ID, LANGUAGE_ID)
 * </ul>
 * @package usertype
 * @subpackage classes
 */
class CAllUserTypeEntity extends CDBResult
{
	/**
	 * ������� ��� ������� ���������� ����������������� ��������.
	 *
	 * <p>���������� ������������� ������ ���������� ������� ����� �������� � Update.</p>
	 * @param integer $ID ������������� ��������
	 * @return array ���� �������� �� �������, �� ������������ false
	 * @static
	 */
	function GetByID($ID)
	{
		global $DB;
		static $arLabels = array("EDIT_FORM_LABEL", "LIST_COLUMN_LABEL", "LIST_FILTER_LABEL", "ERROR_MESSAGE", "HELP_MESSAGE");
		static $cache = array();

		if(!array_key_exists($ID, $cache))
		{
			$rsUserField = CUserTypeEntity::GetList(array(), array("ID"=>intval($ID)));
			if($arUserField = $rsUserField->Fetch())
			{
				$rs = $DB->Query("SELECT * FROM b_user_field_lang WHERE USER_FIELD_ID = ".intval($ID), false, "FILE: ".__FILE__."<br>LINE: ".__LINE__);
				while($ar = $rs->Fetch())
				{
					foreach($arLabels as $label)
						$arUserField[$label][$ar["LANGUAGE_ID"]] = $ar[$label];
				}
				$cache[$ID] = $arUserField;
			}
			else
				$cache[$ID] = false;
		}
		return $cache[$ID];
	}

	/**
	 * ������� ��� ������� ���������� ���������������� �������.
	 *
	 * <p>���������� CDBResult - ������� � ����������� �� ������� � ����������.</p>
	 * <p>�������� aSort �� ��������� ����� ��� array("SORT"=>"ASC", "ID"=>"ASC").</p>
	 * <p>���� � aFilter ���������� LANG, �� ������������� ���������� �������� ���������.</p>
	 * @param array $aSort ������������� ������ ���������� (ID, ENTITY_ID, FIELD_NAME, SORT, USER_TYPE_ID)
	 * @param array $aFilter ������������� ������ ������� �� ������� ������������� (<b>�����</b>) (ID, ENTITY_ID, FIELD_NAME, USER_TYPE_ID, SORT, MULTIPLE, MANDATORY, SHOW_FILTER)
	 * @return CDBResult
	 * @static
	 */
	function GetList($aSort=array(), $aFilter=array())
	{
		global $DB, $CACHE_MANAGER;

		if(CACHED_b_user_field!==false)
		{
			$cacheId = "b_user_type".md5(serialize($aSort).".".serialize($aFilter));
			if($CACHE_MANAGER->Read(CACHED_b_user_field, $cacheId, "b_user_field"))
			{
				$arResult = $CACHE_MANAGER->Get($cacheId);
				$res = new CDBResult;
				$res->InitFromArray($arResult);
				$res = new CUserTypeEntity($res);
				return $res;
			}
		}

		$bLangJoin = false;
		$arFilter = array();
		foreach($aFilter as $key=>$val)
		{
			if(is_array($val) || strlen($val) <= 0)
				continue;

			$key = strtoupper($key);
			$val = $DB->ForSql($val);

			switch($key)
			{
				case "ID":
				case "ENTITY_ID":
				case "FIELD_NAME":
				case "USER_TYPE_ID":
				case "XML_ID":
				case "SORT":
				case "MULTIPLE":
				case "MANDATORY":
				case "SHOW_FILTER":
				case "SHOW_IN_LIST":
				case "EDIT_IN_LIST":
				case "IS_SEARCHABLE":
					$arFilter[] = "UF.".$key." LIKE '".$val."'";
					break;
				case "LANG":
					$bLangJoin = $val;
					break;
			}
		}

		$arOrder = array();
		foreach($aSort as $key=>$val)
		{
			$key = strtoupper($key);
			$ord = (strtoupper($val) <> "ASC"? "DESC": "ASC");
			switch($key)
			{
				case "ID":
				case "ENTITY_ID":
				case "FIELD_NAME":
				case "USER_TYPE_ID":
				case "XML_ID":
				case "SORT":
					$arOrder[] = "UF.".$key." ".$ord;
					break;
			}
		}
		if(count($arOrder) == 0)
		{
			$arOrder[] = "UF.SORT asc";
			$arOrder[] = "UF.ID asc";
		}
		DelDuplicateSort($arOrder);
		$sOrder = "\nORDER BY ".implode(", ", $arOrder);

		if(count($arFilter) == 0)
			$sFilter = "";
		else
			$sFilter = "\nWHERE ".implode("\nAND ", $arFilter);

		$strSql = "
			SELECT
				UF.ID
				,UF.ENTITY_ID
				,UF.FIELD_NAME
				,UF.USER_TYPE_ID
				,UF.XML_ID
				,UF.SORT
				,UF.MULTIPLE
				,UF.MANDATORY
				,UF.SHOW_FILTER
				,UF.SHOW_IN_LIST
				,UF.EDIT_IN_LIST
				,UF.IS_SEARCHABLE
				,UF.SETTINGS
				".($bLangJoin? "
					,UFL.EDIT_FORM_LABEL
					,UFL.LIST_COLUMN_LABEL
					,UFL.LIST_FILTER_LABEL
					,UFL.ERROR_MESSAGE
					,UFL.HELP_MESSAGE
				": "")."
			FROM
				b_user_field UF
				".($bLangJoin? "LEFT JOIN b_user_field_lang UFL on UFL.LANGUAGE_ID = '".$bLangJoin."' AND UFL.USER_FIELD_ID = UF.ID": "")."
			".$sFilter.$sOrder;

		if(CACHED_b_user_field===false)
		{
			$res = $DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		}
		else
		{
			$arResult = array();
			$res = $DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
			while($ar = $res->Fetch())
				$arResult[]=$ar;

			$CACHE_MANAGER->Set($cacheId, $arResult);

			$res = new CDBResult;
			$res->InitFromArray($arResult);
		}

		return  new CUserTypeEntity($res);
	}

	/**
	 * ������� �������� ������������ �������� ���������� ���������������� �������.
	 *
	 * <p>���������� � ������� Add � Update ��� �������� ������������ ��������� ��������.</p>
	 * <p>��������:</p>
	 * <ul>
	 * <li>ENTITY_ID - ������������
	 * <li>ENTITY_ID - �� ����� 20-�� ��������
	 * <li>ENTITY_ID - �� ������ ��������� ������� �������� ����� 0-9 A-Z � _
	 * <li>FIELD_NAME - ������������
	 * <li>FIELD_NAME - �� ����� 4-� ��������
	 * <li>FIELD_NAME - �� ����� 20-�� ��������
	 * <li>FIELD_NAME - �� ������ ��������� ������� �������� ����� 0-9 A-Z � _
	 * <li>FIELD_NAME - ������ ���������� �� UF_
	 * <li>USER_TYPE_ID - ������������
	 * <li>USER_TYPE_ID - ������ ���� ���������������
	 * </ul>
	 * <p>� ������ ������ ������ ���������� ����������!</p>
	 * @param integer $ID - ������������� ��������. 0 - ��� ������.
	 * @param array $arFields ���������� ��������
	 * @return boolean false - ���� ���� ���� �������� �� ������.
	 */
	function CheckFields($ID, $arFields, $bCheckUserType = true)
	{
		global $DB, $APPLICATION, $USER_FIELD_MANAGER;
		$aMsg = array();
		$ID = intval($ID);

		if( ($ID<=0 || array_key_exists("ENTITY_ID", $arFields)) && strlen($arFields["ENTITY_ID"])<=0 )
			$aMsg[] = array("id"=>"ENTITY_ID", "text"=>GetMessage("USER_TYPE_ENTITY_ID_MISSING"));
		if(array_key_exists("ENTITY_ID", $arFields))
		{
			if(strlen($arFields["ENTITY_ID"])>20)
				$aMsg[] = array("id"=>"ENTITY_ID", "text"=>GetMessage("USER_TYPE_ENTITY_ID_TOO_LONG"));
			if(!preg_match('/^[0-9A-Z_]+$/', $arFields["ENTITY_ID"]))
				$aMsg[] = array("id"=>"ENTITY_ID", "text"=>GetMessage("USER_TYPE_ENTITY_ID_INVALID"));
		}

		if( ($ID<=0 || array_key_exists("FIELD_NAME", $arFields)) && strlen($arFields["FIELD_NAME"])<=0 )
			$aMsg[] = array("id"=>"FIELD_NAME", "text"=>GetMessage("USER_TYPE_FIELD_NAME_MISSING"));
		if(array_key_exists("FIELD_NAME", $arFields))
		{
			if(strlen($arFields["FIELD_NAME"])<4)
				$aMsg[] = array("id"=>"FIELD_NAME", "text"=>GetMessage("USER_TYPE_FIELD_NAME_TOO_SHORT"));
			if(strlen($arFields["FIELD_NAME"])>20)
				$aMsg[] = array("id"=>"FIELD_NAME", "text"=>GetMessage("USER_TYPE_FIELD_NAME_TOO_LONG"));
			if(strncmp($arFields["FIELD_NAME"], "UF_", 3)!==0)
				$aMsg[] = array("id"=>"FIELD_NAME", "text"=>GetMessage("USER_TYPE_FIELD_NAME_NOT_UF"));
			if(!preg_match('/^[0-9A-Z_]+$/', $arFields["FIELD_NAME"]))
				$aMsg[] = array("id"=>"FIELD_NAME", "text"=>GetMessage("USER_TYPE_FIELD_NAME_INVALID"));
		}

		if( ($ID<=0 || array_key_exists("USER_TYPE_ID", $arFields)) && strlen($arFields["USER_TYPE_ID"])<=0 )
			$aMsg[] = array("id"=>"USER_TYPE_ID", "text"=>GetMessage("USER_TYPE_USER_TYPE_ID_MISSING"));
		if(
			$bCheckUserType
			&& array_key_exists("USER_TYPE_ID", $arFields)
			&& !$USER_FIELD_MANAGER->GetUserType($arFields["USER_TYPE_ID"])
		)
			$aMsg[] = array("id"=>"USER_TYPE_ID", "text"=>GetMessage("USER_TYPE_USER_TYPE_ID_INVALID"));

		if(!empty($aMsg))
		{
			$e = new CAdminException($aMsg);
			$APPLICATION->ThrowException($e);
			return false;
		}
		return true;
	}

	/**
	 * ������� ��������� ���������������� ��������.
	 *
	 * <p>������� ���������� ����� ���������� ������� CheckFields (�.�. $this->CheckFields($arFields) ).</p>
	 * <p>���� �������� ������ �������, ����������� �������� �� ������������� ������ ���� ��� ������ ��������.</p>
	 * <p>����� ��� ������������� ��������� �������� ���� <b>b_uts_[ENTITY_ID]</b> � <b>b_utm_[ENTITY_ID]</b>.</p>
	 * <p>����� ���� ���������� ����������� � ��.</p>
	 * <p>� ������ ����� ����� <b>���������� �������� ������� b_uts_[ENTITY_ID]</b>.</p>
	 * <p>������ arFields:</p>
	 * <ul>
	 * <li>ENTITY_ID - ��������
	 * <li>FIELD_NAME - ���������� ��� ������� � �� � ������� ����� ��������� �������� ��������.
	 * <li>USER_TYPE_ID - ��� ��������
	 * <li>XML_ID - ������������� ��� ������������� ��� �������/��������
	 * <li>SORT - ������� ���������� (�� ��������� 100)
	 * <li>MULTIPLE - ������� ��������������� Y/N (�� ��������� N)
	 * <li>MANDATORY - ������� �������������� ����� �������� Y/N (�� ��������� N)
	 * <li>SHOW_FILTER - ���������� ��� ��� � ������� ����� ����� � ����� ��� ������������. ��. ����.
	 * <li>SHOW_IN_LIST - ���������� ��� ��� � ����� ����� (�� ��������� Y)
	 * <li>EDIT_IN_LIST - ��������� �������������� � ������, �� �� � API! (�� ��������� Y)
	 * <li>IS_SEARCHABLE - ���� ��������� � ������ (�� ��������� N)
	 * <li>SETTINGS - ������ � ����������� �������� ���������� �� ���� ��������. �������� "�������" ����� ���������� ���� PrepareSettings.
	 * <li>EDIT_FORM_LABEL - ������ �������� ��������� ���� array("ru"=>"������", "en"=>"hello")
	 * <li>LIST_COLUMN_LABEL
	 * <li>LIST_FILTER_LABEL
	 * <li>ERROR_MESSAGE
	 * <li>HELP_MESSAGE
	 * </ul>
	 * <p>� ������ ������ ������ ���������� ����������!</p>
	 * <p>�������� ��� SHOW_FILTER:</p>
	 * <ul>
	 * <li>N - �� ����������
	 * <li>I - ������ ����������
	 * <li>E - �����
	 * <li>S - ���������
	 * </ul>
	 * @param array $arFields ���������� ������ ��������
	 * @return integer - ������������ ������������ ��������, false - ���� �������� �� ���� ���������.
	 */
	function Add($arFields, $bCheckUserType = true)
	{
		global $DB, $APPLICATION, $USER_FIELD_MANAGER, $CACHE_MANAGER;
		if(!$this->CheckFields(0, $arFields, $bCheckUserType))
			return false;

		$rs = CUserTypeEntity::GetList(array(), array(
			"ENTITY_ID" => $arFields["ENTITY_ID"],
			"FIELD_NAME" => $arFields["FIELD_NAME"],
		));
		if($rs->Fetch())
		{
			$aMsg = array();
			$aMsg[] = array(
				"id"=>"FIELD_NAME",
				"text"=>GetMessage("USER_TYPE_ADD_ALREADY_ERROR", array(
						"#FIELD_NAME#"=>htmlspecialcharsbx($arFields["FIELD_NAME"]),
						"#ENTITY_ID#"=>htmlspecialcharsbx($arFields["ENTITY_ID"]),
				)),
			);
			$e = new CAdminException($aMsg);
			$APPLICATION->ThrowException($e);
			return false;
		}

		if(intval($arFields["SORT"]) <= 0)
			$arFields["SORT"]=100;
		if($arFields["MULTIPLE"]!=="Y")
			$arFields["MULTIPLE"]="N";
		if($arFields["MANDATORY"]!=="Y")
			$arFields["MANDATORY"]="N";
		$arFields["SHOW_FILTER"] = substr($arFields["SHOW_FILTER"], 0, 1);
		if($arFields["SHOW_FILTER"] == '' || strpos("NIES", $arFields["SHOW_FILTER"])===false)
			$arFields["SHOW_FILTER"]="N";
		if($arFields["SHOW_IN_LIST"]!=="N")
			$arFields["SHOW_IN_LIST"]="Y";
		if($arFields["EDIT_IN_LIST"]!=="N")
			$arFields["EDIT_IN_LIST"]="Y";
		if($arFields["IS_SEARCHABLE"]!=="Y")
			$arFields["IS_SEARCHABLE"]="N";

		if(!array_key_exists("SETTINGS", $arFields))
			$arFields["SETTINGS"] = array();
		$arFields["SETTINGS"] = serialize($USER_FIELD_MANAGER->PrepareSettings(0, $arFields, $bCheckUserType));

		// events
		foreach (GetModuleEvents("main", "OnBeforeUserTypeAdd", true) as $arEvent)
		{
			if (ExecuteModuleEventEx($arEvent, array(&$arFields))===false)
			{
				if($e = $APPLICATION->GetException())
				{
					return false;
				}

				$aMsg = array();
				$aMsg[] = array(
					"id"=>"FIELD_NAME",
					"text"=>GetMessage("USER_TYPE_ADD_ERROR", array(
						"#FIELD_NAME#"=>htmlspecialcharsbx($arFields["FIELD_NAME"]),
						"#ENTITY_ID#"=>htmlspecialcharsbx($arFields["ENTITY_ID"]),
					))
				);

				$e = new CAdminException($aMsg);
				$APPLICATION->ThrowException($e);

				return false;
			}
		}

		if(is_object($USER_FIELD_MANAGER))
			$USER_FIELD_MANAGER->CleanCache();

		if(!$this->CreatePropertyTables($arFields["ENTITY_ID"]))
			return false;

		if($ID = $DB->Add("b_user_field", $arFields, array("SETTINGS")))
		{
			if(CACHED_b_user_field!==false) $CACHE_MANAGER->CleanDir("b_user_field");

			$arLabels = array("EDIT_FORM_LABEL", "LIST_COLUMN_LABEL", "LIST_FILTER_LABEL", "ERROR_MESSAGE", "HELP_MESSAGE");
			$arLangs = array();
			foreach($arLabels as $label)
			{
				if(isset($arFields[$label]) && is_array($arFields[$label]))
				{
					foreach($arFields[$label] as $lang=>$value)
					{
						$arLangs[$lang][$label] = $value;
					}
				}
			}
			foreach($arLangs as $lang=>$arLangFields)
			{
				$arLangFields["USER_FIELD_ID"] = $ID;
				$arLangFields["LANGUAGE_ID"] = $lang;
				$DB->Add("b_user_field_lang", $arLangFields);
			}

			if($arFields["MULTIPLE"]=="N")
				$strType = $USER_FIELD_MANAGER->GetDBColumnType($arFields);
			elseif(strtolower($DB->type)=="oracle")
				$strType = "clob";
			else
				$strType = "text";

			$rs = $DB->DDL("
				ALTER TABLE b_uts_".strtolower($arFields["ENTITY_ID"])."
				ADD ".$arFields["FIELD_NAME"]." ".$strType."
			", false, "FILE: ".__FILE__."<br>LINE: ".__LINE__);
			if(!$rs)
			{
				$aMsg = array();
				$aMsg[] = array(
					"id"=>"FIELD_NAME",
					"text"=>GetMessage("USER_TYPE_ADD_ERROR", array(
							"#FIELD_NAME#"=>htmlspecialcharsbx($arFields["FIELD_NAME"]),
							"#ENTITY_ID#"=>htmlspecialcharsbx($arFields["ENTITY_ID"]),
					)),
				);
				$e = new CAdminException($aMsg);
				$APPLICATION->ThrowException($e);
				return false;
			}
		}
		return $ID;
	}

	/**
	 * ������� �������� ���������� ����������������� ��������.
	 *
	 * <p>���� �������, ��� ��� ���������� ���������� ���������� ���� ������ ���� �� �������������
	 * ����� �� �������� ��� � ���������� (��������� ���� ��� alter'�� � �������).</p>
	 * <p>������� ���������� ����� ���������� ������� CheckFields (�.�. $this->CheckFields($arFields) ).</p>
	 * <p>����� ���� ���������� ����������� � ��.</p>
	 * <p>������ arFields (������ �� ��� ����� ��������):</p>
	 * <ul>
	 * <li>SORT - ������� ����������
	 * <li>MANDATORY - ������� �������������� ����� �������� Y/N
	 * <li>SHOW_FILTER - ������� ������ � ������� ������ Y/N
	 * <li>SHOW_IN_LIST - ������� ������ � ������ Y/N
	 * <li>EDIT_IN_LIST - ��������� �������������� ���� � ������ ������� ��� ��� Y/N
	 * <li>IS_SEARCHABLE - ������� ������ Y/N
	 * <li>SETTINGS - ������ � ����������� �������� ���������� �� ���� ��������. �������� "�������" ����� ���������� ���� PrepareSettings.
	 * <li>EDIT_FORM_LABEL - ������ �������� ��������� ���� array("ru"=>"������", "en"=>"hello")
	 * <li>LIST_COLUMN_LABEL
	 * <li>LIST_FILTER_LABEL
	 * <li>ERROR_MESSAGE
	 * <li>HELP_MESSAGE
	 * </ul>
	 * <p>� ������ ������ ������ ���������� ����������!</p>
	 * @param array $ID ������������� ��������
	 * @param array $arFields ����� ���������� ��������
	 * @return boolean - true � ������ ��������� ����������, false - � ��������� ������.
	 */
	function Update($ID, $arFields)
	{
		global $DB, $USER_FIELD_MANAGER, $CACHE_MANAGER, $APPLICATION;
		$ID = intval($ID);

		unset($arFields["ENTITY_ID"]);
		unset($arFields["FIELD_NAME"]);
		unset($arFields["USER_TYPE_ID"]);
		unset($arFields["MULTIPLE"]);

		if(!$this->CheckFields($ID, $arFields))
			return false;

		if(array_key_exists("SETTINGS", $arFields))
			$arFields["SETTINGS"] = serialize($USER_FIELD_MANAGER->PrepareSettings($ID, $arFields));
		if(array_key_exists("MANDATORY", $arFields) && $arFields["MANDATORY"]!=="Y")
			$arFields["MANDATORY"]="N";
		if(array_key_exists("SHOW_FILTER", $arFields))
		{
			$arFields["SHOW_FILTER"] = substr($arFields["SHOW_FILTER"], 0, 1);
			if(strpos("NIES", $arFields["SHOW_FILTER"])===false)
				$arFields["SHOW_FILTER"]="N";
		}
		if(array_key_exists("SHOW_IN_LIST", $arFields) && $arFields["SHOW_IN_LIST"]!=="N")
			$arFields["SHOW_IN_LIST"]="Y";
		if(array_key_exists("EDIT_IN_LIST", $arFields) && $arFields["EDIT_IN_LIST"]!=="N")
			$arFields["EDIT_IN_LIST"]="Y";
		if(array_key_exists("IS_SEARCHABLE", $arFields) && $arFields["IS_SEARCHABLE"]!=="Y")
			$arFields["IS_SEARCHABLE"]="N";

		// events
		foreach (GetModuleEvents("main", "OnBeforeUserTypeUpdate", true) as $arEvent)
		{
			if (ExecuteModuleEventEx($arEvent, array(&$arFields))===false)
			{
				if($e = $APPLICATION->GetException())
				{
					return false;
				}

				$aMsg = array();
				$aMsg[] = array(
					"id"=>"FIELD_NAME",
					"text"=>GetMessage("USER_TYPE_UPDATE_ERROR", array(
						"#FIELD_NAME#"=>htmlspecialcharsbx($arFields["FIELD_NAME"]),
						"#ENTITY_ID#"=>htmlspecialcharsbx($arFields["ENTITY_ID"]),
					))
				);

				$e = new CAdminException($aMsg);
				$APPLICATION->ThrowException($e);

				return false;
			}
		}

		if(is_object($USER_FIELD_MANAGER))
			$USER_FIELD_MANAGER->CleanCache();

		$result = true;
		$strUpdate = $DB->PrepareUpdate("b_user_field", $arFields);
		if($strUpdate!="")
		{
			if(CACHED_b_user_field!==false) $CACHE_MANAGER->CleanDir("b_user_field");
			$strSql = "UPDATE b_user_field SET ".$strUpdate." WHERE ID = ".$ID;
			if(array_key_exists("SETTINGS", $arFields))
				$arBinds = array("SETTINGS" => $arFields["SETTINGS"]);
			else
				$arBinds = array();
			$result = $DB->QueryBind($strSql, $arBinds);
		}

		$arLabels = array("EDIT_FORM_LABEL", "LIST_COLUMN_LABEL", "LIST_FILTER_LABEL", "ERROR_MESSAGE", "HELP_MESSAGE");
		$arLangs = array();
		foreach($arLabels as $label)
		{
			if(is_array($arFields[$label]))
			{
				foreach($arFields[$label] as $lang=>$value)
				{
					$arLangs[$lang][$label] = $value;
				}
			}
		}

		if(count($arLangs)>0)
			$DB->Query("DELETE FROM b_user_field_lang WHERE USER_FIELD_ID = ".$ID, false, "FILE: ".__FILE__."<br>LINE: ".__LINE__);
		foreach($arLangs as $lang=>$arLangFields)
		{
			$arLangFields["USER_FIELD_ID"] = $ID;
			$arLangFields["LANGUAGE_ID"] = $lang;
			$DB->Add("b_user_field_lang", $arLangFields);
		}

		return true;
	}

	/**
	 * ������� ������� ���������������� �������� � ��� ��� ��������.
	 *
	 * <p>������� ��������� ���������� ��������.</p>
	 * <p>����� �� �������� ���� <b>b_utm_[ENTITY_ID]</b> ��������� ��� �������� ������������� �������.</p>
	 * <p>����� ���� � �������� ���� <b>b_uts_[ENTITY_ID]</b> ��������� �������.</p>
	 * <p>� ���� ��� ���� "���������" �������� ��� ��������, �� ��������� ���� �������� ��������� ��������.</p>
	 * @param array $ID ������������� ��������
	 * @return CDBResult - ��������� ���������� ���������� ������� �������.
	 */
	function Delete($ID)
	{
		global $DB, $CACHE_MANAGER, $USER_FIELD_MANAGER, $APPLICATION;
		$ID = intval($ID);

		$rs = $this->GetList(array(), array("ID"=>$ID));
		if($arField = $rs->Fetch())
		{
			// events
			foreach (GetModuleEvents("main", "OnBeforeUserTypeDelete", true) as $arEvent)
			{
				if (ExecuteModuleEventEx($arEvent, array(&$arField))===false)
				{
					if($e = $APPLICATION->GetException())
					{
						return false;
					}

					$aMsg = array();
					$aMsg[] = array(
						"id"=>"FIELD_NAME",
						"text"=>GetMessage("USER_TYPE_DELETE_ERROR", array(
							"#FIELD_NAME#"=>htmlspecialcharsbx($arField["FIELD_NAME"]),
							"#ENTITY_ID#"=>htmlspecialcharsbx($arField["ENTITY_ID"]),
						))
					);

					$e = new CAdminException($aMsg);
					$APPLICATION->ThrowException($e);

					return false;
				}
			}

			if(is_object($USER_FIELD_MANAGER))
				$USER_FIELD_MANAGER->CleanCache();

			$arType = $USER_FIELD_MANAGER->GetUserType($arField["USER_TYPE_ID"]);
			//We need special handling of file type properties
			if($arType)
			{
				if($arType["BASE_TYPE"]=="file")
				{
					if($arField["MULTIPLE"] == "Y")
						$strSql = "SELECT VALUE_INT VALUE FROM b_utm_".strtolower($arField["ENTITY_ID"])." WHERE FIELD_ID=".$arField["ID"];
					else
						$strSql = "SELECT ".$arField["FIELD_NAME"]." VALUE FROM b_uts_".strtolower($arField["ENTITY_ID"]);
					$rsFile = $DB->Query($strSql, false, "FILE: ".__FILE__."<br>LINE: ".__LINE__);
					while($arFile = $rsFile->Fetch())
					{
						CFile::Delete($arFile["VALUE"]);
					}
				}
				elseif($arType["BASE_TYPE"]=="enum")
				{
					$obEnum = new CUserFieldEnum;
					$obEnum->DeleteFieldEnum($arField["ID"]);
				}
			}

			if(CACHED_b_user_field!==false) $CACHE_MANAGER->CleanDir("b_user_field");
			$rs = $DB->Query("DELETE FROM b_user_field_lang WHERE USER_FIELD_ID = ".$ID, false, "FILE: ".__FILE__."<br>LINE: ".__LINE__);
			if($rs)
				$rs = $DB->Query("DELETE FROM b_user_field WHERE ID = ".$ID, false, "FILE: ".__FILE__."<br>LINE: ".__LINE__);
			if($rs)
			{
				$rs = $this->GetList(array(), array("ENTITY_ID" => $arField["ENTITY_ID"]));
				if($rs->Fetch()) // more than one
				{
					foreach($this->DropColumnSQL("b_uts_".strtolower($arField["ENTITY_ID"]), array($arField["FIELD_NAME"])) as $strSql)
						$DB->Query($strSql, false, "FILE: ".__FILE__."<br>LINE: ".__LINE__);
					$rs = $DB->Query("DELETE FROM b_utm_".strtolower($arField["ENTITY_ID"])." WHERE FIELD_ID = '".$ID."'", false, "FILE: ".__FILE__."<br>LINE: ".__LINE__);
				}
				else
				{
					$rs = $DB->Query("DROP SEQUENCE SQ_B_UTM_".$arField["ENTITY_ID"], true);
					$rs = $DB->Query("DROP TABLE b_uts_".strtolower($arField["ENTITY_ID"]), false, "FILE: ".__FILE__."<br>LINE: ".__LINE__);
					$rs = $DB->Query("DROP TABLE b_utm_".strtolower($arField["ENTITY_ID"]), false, "FILE: ".__FILE__."<br>LINE: ".__LINE__);
				}
			}
		}
		return $rs;
	}

	/**
	 * ������� ������� ��� ���������������� �������� ��������.
	 *
	 * <p>������� ��������� ���������� �������.</p>
	 * <p>����� ������� ��� �������� ��������� ��������.</p>
	 * <p>����� �������� ���� <b>b_utm_[ENTITY_ID]</b> � <b>b_uts_[ENTITY_ID]</b> ���������.</p>
	 * @param array $entity_id ������������� ��������
	 * @return CDBResult - ��������� ���������� ���������� ������� �������.
	 */
	function DropEntity($entity_id)
	{
		global $DB, $CACHE_MANAGER, $USER_FIELD_MANAGER;
		$entity_id = preg_replace("/[^0-9A-Z_]+/", "", $entity_id);

		if(CACHED_b_user_field !== false)
			$CACHE_MANAGER->CleanDir("b_user_field");

		if(is_object($USER_FIELD_MANAGER))
			$USER_FIELD_MANAGER->CleanCache();

		$rs = true;
		$rsFields = $this->GetList(array(), array("ENTITY_ID"=>$entity_id));
		//We need special handling of file and enum type properties
		while($arField = $rsFields->Fetch())
		{
			$arType = $USER_FIELD_MANAGER->GetUserType($arField["USER_TYPE_ID"]);
			if($arType && ($arType["BASE_TYPE"]=="file" || $arType["BASE_TYPE"]=="enum"))
			{
				$this->Delete($arField["ID"]);
			}
		}

		$bDropTable = false;
		$rsFields = $this->GetList(array(), array("ENTITY_ID"=>$entity_id));
		while($arField = $rsFields->Fetch())
		{
			$bDropTable = true;
			$rs = $DB->Query("DELETE FROM b_user_field_lang WHERE USER_FIELD_ID = ".$arField["ID"], false, "FILE: ".__FILE__."<br>LINE: ".__LINE__);
			$rs = $DB->Query("DELETE FROM b_user_field WHERE ID = ".$arField["ID"], false, "FILE: ".__FILE__."<br>LINE: ".__LINE__);
		}

		if($bDropTable)
		{
			$rs = $DB->Query("DROP SEQUENCE SQ_B_UTM_".$entity_id, true);
			$rs = $DB->Query("DROP TABLE b_uts_".strtolower($entity_id), false, "FILE: ".__FILE__."<br>LINE: ".__LINE__);
			$rs = $DB->Query("DROP TABLE b_utm_".strtolower($entity_id), false, "FILE: ".__FILE__."<br>LINE: ".__LINE__);
		}

		return $rs;
	}

	/**
	 * ������� Fetch.
	 *
	 * <p>������������� ���� SETTINGS.</p>
	 * @return array ���������� false � ������ ��������� ������ �������.
	 */
	function Fetch()
	{
		$res = parent::Fetch();
		if($res && strlen($res["SETTINGS"])>0)
		{
			$res["SETTINGS"] = unserialize($res["SETTINGS"]);
		}
		return $res;
	}
}

/**
 * ������ ����� ���������� �������� ������������ ���������� ����� ����������
 * ���������������� ������� � ��������� � ������� ��� ���������.
 * @package usertype
 * @subpackage classes
 */
class CAllUserTypeManager
{
	/**
	 * ������ ��� ���� ���������������� �������.
	 *
	 * <p>���������������� ��� ������ ������ ������ GetUserType.</p>
	 * @var array
	 */
	var $arUserTypes = false;
	var $arFieldsCache = array();
	var $arRightsCache = array();

	function CleanCache()
	{
		$this->arFieldsCache = array();
	}
	/**
	 * ������� ���������� ���������� ����.
	 *
	 * <p>���� ��� ������ ����� �������, �� ����������� ��������� ������� OnUserTypeBuildList (main).
	 * ������������������ ����������� ������ ������� ����� �������� ����. � ������ ������ ��������� ������� -
	 * ��� ��������� ��� � ����. (�� ������ ���� ���� ��� ���������� ������������ "���������" �������)</p>
	 * <p>��� ���������� ������� ���������� ������ ������ �����.<p>
	 * <p>��� �������� user_type_id - ���������� ������ ���� ����� ��� ��������������� � false ���� ���.<p>
	 * @param string $user_type_id ��������������. ������������� ���� ��������.
	 * @return array|boolean
	 */
	function GetUserType($user_type_id = false)
	{
		if(!is_array($this->arUserTypes))
		{
			$this->arUserTypes = array();
			foreach(GetModuleEvents("main", "OnUserTypeBuildList", true) as $arEvent)
			{
				$res = ExecuteModuleEventEx($arEvent);
				$this->arUserTypes[$res["USER_TYPE_ID"]] = $res;
			}
		}
		if($user_type_id !== false)
		{
			if(array_key_exists($user_type_id, $this->arUserTypes))
				return $this->arUserTypes[$user_type_id];
			else
				return false;
		}
		else
			return $this->arUserTypes;
	}

	function GetDBColumnType($arUserField)
	{
		if($arType = $this->GetUserType($arUserField["USER_TYPE_ID"]))
		{
			if(is_callable(array($arType["CLASS_NAME"], "getdbcolumntype")))
				return call_user_func_array(array($arType["CLASS_NAME"], "getdbcolumntype"), array($arUserField));
		}
		return "";
	}

	function PrepareSettings($ID, $arUserField, $bCheckUserType = true)
	{
		if($ID > 0)
		{
			$rsUserType = CUserTypeEntity::GetList(array(), array("ID"=>$ID));
			$arUserType = $rsUserType->Fetch();
			if($arUserType)
				$user_type_id = $arUserType["USER_TYPE_ID"];
		}
		else
				$user_type_id = $arUserField["USER_TYPE_ID"];

		if(!$bCheckUserType)
		{
			if(!isset($arUserField["SETTINGS"]))
				return array();

			if(!is_array($arUserField["SETTINGS"]))
				return array();

			if(empty($arUserField["SETTINGS"]))
				return array();
		}

		if($arType = $this->GetUserType($user_type_id))
		{
			if(is_callable(array($arType["CLASS_NAME"], "preparesettings")))
				return call_user_func_array(array($arType["CLASS_NAME"], "preparesettings"), array($arUserField));
		}
		else
			return array();
	}

	function OnEntityDelete($entity_id)
	{
		$obUserField  = new CUserTypeEntity;
		return $obUserField->DropEntity($entity_id);
	}

	/**
	 * ������� ���������� ���������� ����� ���������� ��� ��������.
	 *
	 * <p>�����! � $arUserField �������� ���� ENTITY_VALUE_ID - ��� ������������� ���������� ��������
	 * ����������� �������� ����� ������ �� ������ � ������������� ������������ �������� �� ���������.</p>
	*/
	function GetUserFields($entity_id, $value_id=0, $LANG=false)
	{
		$entity_id = preg_replace("/[^0-9A-Z_]+/", "", $entity_id);
		$value_id = intval($value_id);

		global $DB;

		$result = array();
		if(!array_key_exists($entity_id.".".$LANG, $this->arFieldsCache))
		{
			$arFilter = array("ENTITY_ID"=>$entity_id);
			if($LANG)
				$arFilter["LANG"]=$LANG;
			$rs = CUserTypeEntity::GetList(array(), $arFilter);
			while($arUserField = $rs->Fetch())
			{
				if($arType = $this->GetUserType($arUserField["USER_TYPE_ID"]))
				{
					if(is_callable(array($arType["CLASS_NAME"], "checkpermission")))
					{
						if(!call_user_func_array(array($arType["CLASS_NAME"], "checkpermission"), array($arUserField)))
							continue;
					}
					$arUserField["USER_TYPE"] = $arType;
					$arUserField["VALUE"] = false;
					if(!is_array($arUserField["SETTINGS"]) || empty($arUserField["SETTINGS"]))
						$arUserField["SETTINGS"] = $this->PrepareSettings(0, $arUserField);
					$result[$arUserField["FIELD_NAME"]] = $arUserField;
				}
			}
			$this->arFieldsCache[$entity_id.".".$LANG] = $result;
		}
		else
			$result = $this->arFieldsCache[$entity_id.".".$LANG];

		if(count($result)>0 && $value_id>0)
		{
			$select = "VALUE_ID";
			foreach($result as $FIELD_NAME=>$arUserField)
			{
				if($arUserField["USER_TYPE"]["BASE_TYPE"] == "datetime" && $arUserField["MULTIPLE"] == "N")
					$select .= ", ".$this->DateTimeToChar($FIELD_NAME)." ".$FIELD_NAME;
				else
					$select .= ", ".$FIELD_NAME;
			}

			$rs = $DB->Query("SELECT ".$select." FROM b_uts_".strtolower($entity_id)." WHERE VALUE_ID = ".$value_id, false, "FILE: ".__FILE__."<br>LINE: ".__LINE__);
			if($ar = $rs->Fetch())
			{
				foreach($ar as $key=>$value)
					if(array_key_exists($key, $result))
					{
						if($result[$key]["MULTIPLE"]=="Y")
							$result[$key]["VALUE"] = unserialize($value);
						else
							$result[$key]["VALUE"] = $value;

						$result[$key]["ENTITY_VALUE_ID"] = $value_id;
					}
			}
		}

		return $result;
	}

	function GetUserFieldValue($entity_id, $field_id, $value_id, $LANG=false)
	{
		global $DB;
		$entity_id = preg_replace("/[^0-9A-Z_]+/", "", $entity_id);
		$field_id = preg_replace("/[^0-9A-Z_]+/", "", $field_id);
		$value_id = intval($value_id);
		$strTableName = "b_uts_".strtolower($entity_id);
		$result = false;

		$arFilter = array(
			"ENTITY_ID" => $entity_id,
			"FIELD_NAME" => $field_id,
		);
		if($LANG)
			$arFilter["LANG"]=$LANG;
		$rs = CUserTypeEntity::GetList(array(), $arFilter);
		if($arUserField = $rs->Fetch())
		{
			static $tables = array();
			if($tables[$strTableName] || $DB->TableExists($strTableName))
			{
				$tables[$strTableName] = true;
				$arTableFields = $DB->GetTableFields($strTableName);
				if(array_key_exists($field_id, $arTableFields))
				{
					if($arUserField["USER_TYPE"]["BASE_TYPE"] == "datetime" && $arUserField["MULTIPLE"] == "N")
						$select = $this->DateTimeToChar($field_id);
					else
						$select = $field_id;

					$rs = $DB->Query("SELECT ".$select." VALUE FROM ".$strTableName." WHERE VALUE_ID = ".$value_id, false, "FILE: ".__FILE__."<br>LINE: ".__LINE__);
					if($ar = $rs->Fetch())
					{
						if($arUserField["MULTIPLE"]=="Y")
							$result = unserialize($ar["VALUE"]);
						else
							$result = $ar["VALUE"];
					}
				}
			}
		}

		return $result;
	}

	function EditFormTab($entity_id)
	{
		return array(
			"DIV" => "user_fields_tab",
			"TAB" => GetMessage("USER_TYPE_EDIT_TAB"),
			"ICON" => "none",
			"TITLE" => GetMessage("USER_TYPE_EDIT_TAB_TITLE"),
		);
	}

	function EditFormShowTab($entity_id, $bVarsFromForm, $ID)
	{
		global $APPLICATION;

		if($this->GetRights($entity_id) >= "W")
		{
			echo "<tr colspan=\"2\"><td align=\"left\"><a href=\"/bitrix/admin/userfield_edit.php?lang=".LANG."&ENTITY_ID=".urlencode($entity_id)."&back_url=".urlencode($APPLICATION->GetCurPageParam()."&tabControl_active_tab=user_fields_tab")."\">".GetMessage("USER_TYPE_EDIT_TAB_HREF")."</a></td></tr>";
		}

		$arUserFields = $this->GetUserFields($entity_id, $ID, LANGUAGE_ID);
		if(count($arUserFields)>0)
		{
			foreach($arUserFields as $FIELD_NAME=>$arUserField)
			{
				$arUserField["VALUE_ID"] = intval($ID);
				echo $this->GetEditFormHTML($bVarsFromForm, $GLOBALS[$FIELD_NAME], $arUserField);
			}
		}
	}

	function EditFormAddFields($entity_id, &$arFields)
	{
		global $HTTP_POST_FILES;
		$arUserFields = $this->GetUserFields($entity_id);
		foreach($arUserFields as $arUserField)
		{
			if($arUserField["EDIT_IN_LIST"]=="Y")
			{
				if($arUserField["USER_TYPE"]["BASE_TYPE"]=="file")
				{
					if (isset($HTTP_POST_FILES[$arUserField["FIELD_NAME"]]))
					{
						if(is_array($HTTP_POST_FILES[$arUserField["FIELD_NAME"]]["name"]))
						{
							$arFields[$arUserField["FIELD_NAME"]] = array();
							foreach($HTTP_POST_FILES[$arUserField["FIELD_NAME"]]["name"] as $key => $value)
							{
								$old_id = $GLOBALS[$arUserField["FIELD_NAME"]."_old_id"][$key];
								$arFields[$arUserField["FIELD_NAME"]][$key] = array(
									"name" => $HTTP_POST_FILES[$arUserField["FIELD_NAME"]]["name"][$key],
									"type" => $HTTP_POST_FILES[$arUserField["FIELD_NAME"]]["type"][$key],
									"tmp_name" => $HTTP_POST_FILES[$arUserField["FIELD_NAME"]]["tmp_name"][$key],
									"error" => $HTTP_POST_FILES[$arUserField["FIELD_NAME"]]["error"][$key],
									"size" => $HTTP_POST_FILES[$arUserField["FIELD_NAME"]]["size"][$key],
									"del" => is_array($GLOBALS[$arUserField["FIELD_NAME"]."_del"]) &&
											(	in_array($old_id, $GLOBALS[$arUserField["FIELD_NAME"]."_del"]) ||
												(
													array_key_exists($key, $GLOBALS[$arUserField["FIELD_NAME"]."_del"]) &&
													$GLOBALS[$arUserField["FIELD_NAME"]."_del"][$key] == "Y"
												)
											),
									"old_id" => $old_id
								);
							}
						}
						else
						{
							$arFields[$arUserField["FIELD_NAME"]] = $HTTP_POST_FILES[$arUserField["FIELD_NAME"]];
							$arFields[$arUserField["FIELD_NAME"]]["del"] = $GLOBALS[$arUserField["FIELD_NAME"]."_del"];
							$arFields[$arUserField["FIELD_NAME"]]["old_id"] = $GLOBALS[$arUserField["FIELD_NAME"]."_old_id"];
						}
					}
					else
					{
						if(isset($GLOBALS[$arUserField["FIELD_NAME"]]) || intval($GLOBALS[$arUserField["FIELD_NAME"]])>0)
							$arFields[$arUserField["FIELD_NAME"]] = $GLOBALS[$arUserField["FIELD_NAME"]];
					}
				}
				else
				{
					if (isset($HTTP_POST_FILES[$arUserField["FIELD_NAME"]]))
					{
						$arFile = array();
						CFile::ConvertFilesToPost($HTTP_POST_FILES[$arUserField["FIELD_NAME"]], $arFile);

						if(isset($GLOBALS[$arUserField["FIELD_NAME"]]))
						{
							if($arUserField["MULTIPLE"] == "Y")
							{
								foreach($_REQUEST[$arUserField["FIELD_NAME"]] as $key => $value)
									$arFields[$arUserField["FIELD_NAME"]][$key] = array_merge($value, $arFile[$key]);
							}
							else
							{
								$arFields[$arUserField["FIELD_NAME"]] = array_merge($_REQUEST[$arUserField["FIELD_NAME"]], $arFile);
							}
						}
						else
						{
							$arFields[$arUserField["FIELD_NAME"]] = $arFile;
						}
					}
					else
					{
						if(isset($GLOBALS[$arUserField["FIELD_NAME"]]))
							$arFields[$arUserField["FIELD_NAME"]] = $GLOBALS[$arUserField["FIELD_NAME"]];
					}

				}
			}
		}
	}

	function AdminListAddFilterFields($entity_id, &$arFilterFields)
	{
		$arUserFields = $this->GetUserFields($entity_id);
		foreach($arUserFields as $FIELD_NAME=>$arUserField)
			if($arUserField["SHOW_FILTER"]!="N" && $arUserField["USER_TYPE"]["BASE_TYPE"]!="file")
				$arFilterFields[]="find_".$FIELD_NAME;
	}

	function IsNotEmpty($value)
	{
		if(is_array($value))
		{
			foreach($value as $i => $v)
			{
				if(strlen($v) > 0)
					return true;
			}

			return false;
		}
		else
		{
			if(strlen($value) > 0)
				return true;
			else
				return false;
		}
	}

	function AdminListAddFilter($entity_id, &$arFilter)
	{
		$arUserFields = $this->GetUserFields($entity_id);
		foreach($arUserFields as $FIELD_NAME=>$arUserField)
		{
			$value = $GLOBALS["find_".$FIELD_NAME];
			if(
				$arUserField["SHOW_FILTER"] != "N"
				&& $arUserField["USER_TYPE"]["BASE_TYPE"] != "file"
				&& $this->IsNotEmpty($value)
			)
			{
				if($arUserField["SHOW_FILTER"]=="I")
					$arFilter["=".$FIELD_NAME]=$value;
				elseif($arUserField["SHOW_FILTER"]=="S")
					$arFilter["%".$FIELD_NAME]=$value;
				else
					$arFilter[$FIELD_NAME]=$value;
			}
		}
	}

	function AdminListPrepareFields($entity_id, &$arFields)
	{
		$arUserFields = $this->GetUserFields($entity_id);
		foreach($arUserFields as $FIELD_NAME=>$arUserField)
			if($arUserField["EDIT_IN_LIST"]!="Y")
				unset($arFields[$FIELD_NAME]);
	}

	function AdminListAddHeaders($entity_id, &$arHeaders)
	{
		$arUserFields = $this->GetUserFields($entity_id, 0, $GLOBALS["lang"]);
		foreach($arUserFields as $FIELD_NAME=>$arUserField)
		{
			if($arUserField["SHOW_IN_LIST"]=="Y")
			{
				$arHeaders[] = array(
					"id" => $FIELD_NAME,
					"content" => $arUserField["LIST_COLUMN_LABEL"]? $arUserField["LIST_COLUMN_LABEL"]: $arUserField["FIELD_NAME"],
					"sort" => $arUserField["MULTIPLE"]=="N"? $FIELD_NAME: false,
				);
			}
		}
	}

	function AddUserFields($entity_id, $arRes, &$row)
	{
		$arUserFields = $this->GetUserFields($entity_id);
		foreach($arUserFields as $FIELD_NAME=>$arUserField)
			if($arUserField["SHOW_IN_LIST"]=="Y" && array_key_exists($FIELD_NAME, $arRes))
				$this->AddUserField($arUserField, $arRes[$FIELD_NAME], $row);
	}

	function AddFindFields($entity_id, &$arFindFields)
	{
		$arUserFields = $this->GetUserFields($entity_id, 0, $GLOBALS["lang"]);
		foreach($arUserFields as $FIELD_NAME=>$arUserField)
			if($arUserField["SHOW_FILTER"]!="N" && $arUserField["USER_TYPE"]["BASE_TYPE"]!="file")
			{
				if($arUserField["LIST_FILTER_LABEL"])
					$arFindFields[$FIELD_NAME]=$arUserField["LIST_FILTER_LABEL"];
				else
					$arFindFields[$FIELD_NAME]=$arUserField["FIELD_NAME"];
			}
	}

	function AdminListShowFilter($entity_id)
	{
		$arUserFields = $this->GetUserFields($entity_id, 0, $GLOBALS["lang"]);
		foreach($arUserFields as $FIELD_NAME=>$arUserField)
			if($arUserField["SHOW_FILTER"]!="N" && $arUserField["USER_TYPE"]["BASE_TYPE"]!="file")
				echo $this->GetFilterHTML($arUserField, "find_".$FIELD_NAME, $GLOBALS["find_".$FIELD_NAME]);
	}

	function ShowScript()
	{
		static $first=true;
		if($first)
		{
			$first = false;
			return "
				<script type=\"text/javascript\">
				<!--
				function addNewRow(tableID, regexp, rindex)
				{
					var tbl = document.getElementById(tableID);
					var cnt = tbl.rows.length;
					var oRow = tbl.insertRow(cnt-1);
					var oCell = oRow.insertCell(0);
					var html = tbl.rows[cnt-2].cells[0].innerHTML;

					oCell.innerHTML =  html.replace(regexp,
						function(html)
						{
							return html.replace(new RegExp('([\\\[x])'+arguments[rindex]+'([\\\]x])', 'gi'), '$1'+(1+parseInt(arguments[rindex]))+'$2');
						}
					);

					setTimeout(function() {
						var r = BX.findChildren(oCell, {tag: /^(input|select|textarea)$/i});
						if (r && r.length > 0)
						{
							for (var i=0,l=r.length;i<l;i++)
							{
								if (r[i].form && r[i].form.BXAUTOSAVE)
									r[i].form.BXAUTOSAVE.RegisterInput(r[i]);
								else
									break;
							}
						}
					}, 10);
					//**//
					var re = /<script[^>]*?>([\w\s\S]*?)<\/script>/gmi;
					var otv = '';
					while (otv = re.exec(oCell.innerHTML))
					{
						if (otv[1])
						{
							BX.evalGlobal(otv[1]);
						}
					}
				}
				//-->
				</script>
			";
		}
		else
		{
			return "";
		}
	}

	function GetEditFormHTML($bVarsFromForm, $form_value, $arUserField)
	{
		global $APPLICATION;

		if($arUserField["USER_TYPE"])
		{
			if($this->GetRights($arUserField["ENTITY_ID"]) >= "W")
				$edit_link = ($arUserField["HELP_MESSAGE"]? htmlspecialcharsex($arUserField["HELP_MESSAGE"]).'<br>': '').'<a href="'.htmlspecialcharsbx('/bitrix/admin/userfield_edit.php?lang='.LANG.'&ID='.$arUserField["ID"].'&back_url='.urlencode($APPLICATION->GetCurPageParam().'&tabControl_active_tab=user_fields_tab')).'">'.htmlspecialcharsex(GetMessage("MAIN_EDIT")).'</a>';
			else
				$edit_link = '';

			$hintHTML = '<span id="hint_'.$arUserField["FIELD_NAME"].'"></span><script>BX.hint_replace(BX(\'hint_'.$arUserField["FIELD_NAME"].'\'), \''.CUtil::JSEscape($edit_link).'\');</script>&nbsp;';

			if ($arUserField["MANDATORY"]=="Y")
				$strLabelHTML = $hintHTML.'<span class="adm-required-field">'.htmlspecialcharsbx($arUserField["EDIT_FORM_LABEL"]? $arUserField["EDIT_FORM_LABEL"]: $arUserField["FIELD_NAME"]).'</span>'.':';
			else
				$strLabelHTML = $hintHTML.htmlspecialcharsbx($arUserField["EDIT_FORM_LABEL"]? $arUserField["EDIT_FORM_LABEL"]: $arUserField["FIELD_NAME"]).':';

			if(is_callable(array($arUserField["USER_TYPE"]["CLASS_NAME"], "geteditformhtml")))
			{
				$js = $this->ShowScript();

				if(!$bVarsFromForm)
					$form_value = $arUserField["VALUE"];
				elseif($arUserField["USER_TYPE"]["BASE_TYPE"]=="file")
					$form_value = $GLOBALS[$arUserField["FIELD_NAME"]."_old_id"];
				elseif($arUserField["EDIT_IN_LIST"]=="N")
					$form_value = $arUserField["VALUE"];

				if($arUserField["MULTIPLE"] == "N")
				{
					$valign = "";
					$rowClass = "";
					$html = call_user_func_array(
						array($arUserField["USER_TYPE"]["CLASS_NAME"], "geteditformhtml"),
						array(
							$arUserField,
							array(
								"NAME" => $arUserField["FIELD_NAME"],
								"VALUE" => is_array($form_value)? $form_value: htmlspecialcharsbx($form_value),
								"VALIGN" => &$valign,
								"ROWCLASS" => &$rowClass
							),
						)
					);
					return '<tr'.($rowClass != '' ? ' class="'.$rowClass.'"' : '').'><td'.($valign <> 'middle'? ' class="adm-detail-valign-top"':'').' width="40%">'.$strLabelHTML.'</td><td width="60%">'.$html.'</td></tr>'.$js;
				}
				elseif(is_callable(array($arUserField["USER_TYPE"]["CLASS_NAME"], "geteditformhtmlmulty")))
				{
					if(!is_array($form_value))
						$form_value = array();
					foreach($form_value as $key=>$value)
					{
						$form_value[$key] = htmlspecialcharsbx($value);
					}

					$rowClass = "";
					$html = call_user_func_array(
						array($arUserField["USER_TYPE"]["CLASS_NAME"], "geteditformhtmlmulty"),
						array(
							$arUserField,
							array(
								"NAME" => $arUserField["FIELD_NAME"]."[]",
								"VALUE" => $form_value,
								"ROWCLASS" => &$rowClass
							),
						)
					);
					return '<tr'.($rowClass != '' ? ' class="'.$rowClass.'"' : '').'><td class="adm-detail-valign-top">'.$strLabelHTML.'</td><td>'.$html.'</td></tr>'.$js;
				}
				else
				{
					if(!is_array($form_value))
						$form_value = array();
					$html = "";
					$i = -1;
					foreach($form_value as $i=>$value)
					{

						if(
							(is_array($value) && (strlen(implode("", $value)) > 0))
							|| ((!is_array($value)) && (strlen($value) > 0))
						)
						{
							$html .= '<tr><td>'.call_user_func_array(
								array($arUserField["USER_TYPE"]["CLASS_NAME"], "geteditformhtml"),
								array(
									$arUserField,
									array(
										"NAME" => $arUserField["FIELD_NAME"]."[".$i."]",
										"VALUE" => htmlspecialcharsbx($value),
									),
								)
							).'</td></tr>';
						}
					}
					//Add multiple values support
					$rowClass = "";
					$FIELD_NAME_X = str_replace('_', 'x', $arUserField["FIELD_NAME"]);
					$fieldHtml = call_user_func_array(
						array($arUserField["USER_TYPE"]["CLASS_NAME"], "geteditformhtml"),
						array(
							$arUserField,
							array(
								"NAME" => $arUserField["FIELD_NAME"]."[".($i+1)."]",
								"VALUE" => "",
								"ROWCLASS" => &$rowClass
							),
						)
					);
					return '<tr'.($rowClass != '' ? ' class="'.$rowClass.'"' : '').'><td class="adm-detail-valign-top">'.$strLabelHTML.'</td><td>'.
						'<table id="table_'.$arUserField["FIELD_NAME"].'">'.$html.'<tr><td>'.$fieldHtml.'</td></tr>'.
					'<tr><td style="padding-top: 6px;"><input type="button" value="'.GetMessage("USER_TYPE_PROP_ADD").'" onClick="addNewRow(\'table_'.$arUserField["FIELD_NAME"].'\', /('.$FIELD_NAME_X.'|'.$arUserField["FIELD_NAME"].'|'.$arUserField["FIELD_NAME"].'_old_id)[x\[]([0-9]*)[x\]]/gi, 2)"></td></tr>'.
					"<script type=\"text/javascript\">BX.addCustomEvent('onAutoSaveRestore', function(ob, data) {for (var i in data){if (i.substring(0,".(strlen($arUserField['FIELD_NAME'])+1).")=='".CUtil::JSEscape($arUserField['FIELD_NAME'])."['){".
						'addNewRow(\'table_'.$arUserField["FIELD_NAME"].'\', /('.$FIELD_NAME_X.'|'.$arUserField["FIELD_NAME"].'|'.$arUserField["FIELD_NAME"].'_old_id)[x\[]([0-9]*)[x\[]/gi, 2)'.
					"}}})</script>".
					'</table>'.
					'</td></tr>'.$js;
				}
			}
		}
	}

	function GetFilterHTML($arUserField, $filter_name, $filter_value)
	{
		if($arUserField["USER_TYPE"])
		{
			if(is_callable(array($arUserField["USER_TYPE"]["CLASS_NAME"], "getfilterhtml")))
			{
				$html = call_user_func_array(
					array($arUserField["USER_TYPE"]["CLASS_NAME"], "getfilterhtml"),
					array(
						$arUserField,
						array(
							"NAME" => $filter_name,
							"VALUE" => htmlspecialcharsex($filter_value),
						),
					)
				).CAdminCalendar::ShowScript();
				return '<tr><td>'.htmlspecialcharsbx($arUserField["LIST_FILTER_LABEL"]? $arUserField["LIST_FILTER_LABEL"]: $arUserField["FIELD_NAME"]).':</td><td>'.$html.'</td></tr>';
			}
		}
	}

	function AddUserField($arUserField, $value, &$row)
	{
		if($arUserField["USER_TYPE"])
		{
			$js = $this->ShowScript();
			if(is_callable(array($arUserField["USER_TYPE"]["CLASS_NAME"], "getadminlistviewhtml")))
			{
				if($arUserField["MULTIPLE"] == "N")
				{
					$html = call_user_func_array(
						array($arUserField["USER_TYPE"]["CLASS_NAME"], "getadminlistviewhtml"),
						array(
							$arUserField,
							array(
								"NAME" => "FIELDS[".$row->id."][".$arUserField["FIELD_NAME"]."]",
								"VALUE" => htmlspecialcharsbx($value),
							),
						)
					);
					if($html == '')
						$html = '&nbsp;';
					$row->AddViewField($arUserField["FIELD_NAME"], $html.$js.CAdminCalendar::ShowScript());
				}
				elseif(is_callable(array($arUserField["USER_TYPE"]["CLASS_NAME"], "getadminlistviewhtmlmulty")))
				{
					if(is_array($value))
						$form_value = $value;
					else
						$form_value = unserialize($value);

					if(!is_array($form_value))
						$form_value = array();

					foreach($form_value as $key=>$val)
						$form_value[$key] = htmlspecialcharsbx($val);

					$html = call_user_func_array(
						array($arUserField["USER_TYPE"]["CLASS_NAME"], "getadminlistviewhtmlmulty"),
						array(
							$arUserField,
							array(
								"NAME" => "FIELDS[".$row->id."][".$arUserField["FIELD_NAME"]."]"."[]",
								"VALUE" => $form_value,
							),
						)
					);
					if($html == '')
						$html = '&nbsp;';
					$row->AddViewField($arUserField["FIELD_NAME"], $html.$js.CAdminCalendar::ShowScript());
				}
				else
				{
					$html = "";

					if(is_array($value))
						$form_value = $value;
					else
						$form_value = strlen($value) > 0? unserialize($value): false;

					if(!is_array($form_value))
						$form_value = array();

					$i = -1;
					foreach($form_value as $i=>$val)
					{
						if($html!="")
							$html .= " / ";
						$html .= call_user_func_array(
							array($arUserField["USER_TYPE"]["CLASS_NAME"], "getadminlistviewhtml"),
							array(
								$arUserField,
								array(
									"NAME" => "FIELDS[".$row->id."][".$arUserField["FIELD_NAME"]."]"."[".$i."]",
									"VALUE" => htmlspecialcharsbx($val),
								),
							)
						);
					}
					if($html == '')
						$html = '&nbsp;';
					$row->AddViewField($arUserField["FIELD_NAME"], $html.$js.CAdminCalendar::ShowScript());
				}
			}
			if($arUserField["EDIT_IN_LIST"]=="Y" && is_callable(array($arUserField["USER_TYPE"]["CLASS_NAME"], "getadminlistedithtml")))
			{
				if($arUserField["MULTIPLE"] == "N")
				{
					$html = call_user_func_array(
						array($arUserField["USER_TYPE"]["CLASS_NAME"], "getadminlistedithtml"),
						array(
							$arUserField,
							array(
								"NAME" => "FIELDS[".$row->id."][".$arUserField["FIELD_NAME"]."]",
								"VALUE" => htmlspecialcharsbx($value),
							),
						)
					);
					if($html == '')
						$html = '&nbsp;';
					$row->AddEditField($arUserField["FIELD_NAME"], $html.$js.CAdminCalendar::ShowScript());
				}
				elseif(is_callable(array($arUserField["USER_TYPE"]["CLASS_NAME"], "getadminlistedithtmlmulty")))
				{
					if(is_array($value))
						$form_value = $value;
					else
						$form_value = strlen($value) > 0? unserialize($value): false;

					if(!is_array($form_value))
						$form_value = array();

					foreach($form_value as $key=>$val)
						$form_value[$key] = htmlspecialcharsbx($val);

					$html = call_user_func_array(
						array($arUserField["USER_TYPE"]["CLASS_NAME"], "getadminlistedithtmlmulty"),
						array(
							$arUserField,
							array(
								"NAME" => "FIELDS[".$row->id."][".$arUserField["FIELD_NAME"]."][]",
								"VALUE" => $form_value,
							),
						)
					);
					if($html == '')
						$html = '&nbsp;';
					$row->AddEditField($arUserField["FIELD_NAME"], $html.$js.CAdminCalendar::ShowScript());
				}
				else
				{
					$html = "<table border=\"1\" id=\"table_".$arUserField["FIELD_NAME"]."_".$row->id."\">";
					if(is_array($value))
						$form_value = $value;
					else
						$form_value = unserialize($value);

					if(!is_array($form_value))
						$form_value = array();

					$i = -1;
					foreach($form_value as $i=>$val)
					{
						$html .= '<tr><td>'.call_user_func_array(
							array($arUserField["USER_TYPE"]["CLASS_NAME"], "getadminlistedithtml"),
							array(
								$arUserField,
								array(
									"NAME" => "FIELDS[".$row->id."][".$arUserField["FIELD_NAME"]."]"."[".$i."]",
									"VALUE" => htmlspecialcharsbx($val),
								),
							)
						).'</td></tr>';
					}
					$html .= '<tr><td>'.call_user_func_array(
						array($arUserField["USER_TYPE"]["CLASS_NAME"], "getadminlistedithtml"),
						array(
							$arUserField,
							array(
								"NAME" => "FIELDS[".$row->id."][".$arUserField["FIELD_NAME"]."]"."[".($i+1)."]",
								"VALUE" => "",
							),
						)
					).'</td></tr>';
					$html .= '<tr><td><input type="button" value="'.GetMessage("USER_TYPE_PROP_ADD").'" onClick="addNewRow(\'table_'.$arUserField["FIELD_NAME"].'_'.$row->id.'\', /'.'FIELDS\['.$row->id.'\]\['.$arUserField["FIELD_NAME"].'\]'.'\[([0-9]*)\]/g, 1)"></td></tr>'.
					'</table>';
					$row->AddEditField($arUserField["FIELD_NAME"], $html.$js.CAdminCalendar::ShowScript());
				}
			}
		}
	}

	function GetSettingsHTML($arUserField, $bVarsFromForm = false)
	{
		if(!is_array($arUserField)) // New field
		{
			if($arType = $this->GetUserType($arUserField))
				if(is_callable(array($arType["CLASS_NAME"], "getsettingshtml")))
					return call_user_func_array(array($arType["CLASS_NAME"], "getsettingshtml"), array(false, array("NAME" => "SETTINGS"), $bVarsFromForm));
		}
		else
		{
			if(!is_array($arUserField["SETTINGS"]) || empty($arUserField["SETTINGS"]))
				$arUserField["SETTINGS"] = $this->PrepareSettings(0, $arUserField);

			if($arType = $this->GetUserType($arUserField["USER_TYPE_ID"]))
				if(is_callable(array($arType["CLASS_NAME"], "getsettingshtml")))
					return call_user_func_array(array($arType["CLASS_NAME"], "getsettingshtml"), array($arUserField, array("NAME" => "SETTINGS"), $bVarsFromForm));
		}
	}

	function CheckFields($entity_id, $ID, &$arFields)
	{
		global $APPLICATION;

		$aMsg = array();
		//1 Get user typed fields list for entity
		$arUserFields = $this->GetUserFields($entity_id, $ID, LANGUAGE_ID);
		//2 For each field
		foreach($arUserFields as $FIELD_NAME=>$arUserField)
		{
			$EDIT_FORM_LABEL = strLen($arUserField["EDIT_FORM_LABEL"]) > 0 ? $arUserField["EDIT_FORM_LABEL"] : $arUserField["FIELD_NAME"];
			//common Check for all fields
			if($arUserField["MANDATORY"]=="Y" && ($ID<=0 || isset($arFields[$FIELD_NAME])))
			{
				if($arUserField["USER_TYPE"]["BASE_TYPE"] == "file")
				{
					$bWasInput = false;
					if(is_array($arUserField["VALUE"]))
						$arDBFiles = array_flip($arUserField["VALUE"]);
					elseif($arUserField["VALUE"] > 0)
						$arDBFiles = array($arUserField["VALUE"] => 0);
					else
						$arDBFiles = array();

					if($arUserField["MULTIPLE"]=="N")
					{
						$value = $arFields[$FIELD_NAME];
						if(is_array($value) && array_key_exists("tmp_name", $value))
						{
							if(array_key_exists("del", $value) && $value["del"])
								unset($arDBFiles[$value["old_id"]]);
							elseif(array_key_exists("size", $value) && $value["size"] > 0)
								$bWasInput = true;
						}
					}
					else
					{
						if(is_array($arFields[$FIELD_NAME]))
						{
							foreach($arFields[$FIELD_NAME] as $key=>$value)
							{
								if(is_array($value) && array_key_exists("tmp_name", $value))
								{
									if(array_key_exists("del", $value) && $value["del"])
										unset($arDBFiles[$value["old_id"]]);
									elseif(array_key_exists("size", $value) && $value["size"] > 0)
										$bWasInput = true;
								}
							}
						}
					}

					if(!$bWasInput && empty($arDBFiles))
					{
						$aMsg[] = array("id"=>$FIELD_NAME, "text"=>str_replace("#FIELD_NAME#", $EDIT_FORM_LABEL, GetMessage("USER_TYPE_FIELD_VALUE_IS_MISSING")));
					}
				}
				elseif($arUserField["MULTIPLE"]=="N")
				{
					if(strlen($arFields[$FIELD_NAME])<=0)
					{
						$aMsg[] = array("id"=>$FIELD_NAME, "text"=>str_replace("#FIELD_NAME#", $EDIT_FORM_LABEL, GetMessage("USER_TYPE_FIELD_VALUE_IS_MISSING")));
					}
				}
				else
				{
					if(!is_array($arFields[$FIELD_NAME]))
					{
						$aMsg[] = array("id"=>$FIELD_NAME, "text"=>str_replace("#FIELD_NAME#", $EDIT_FORM_LABEL, GetMessage("USER_TYPE_FIELD_VALUE_IS_MISSING")));
					}
					else
					{
						$bFound = false;
						foreach($arFields[$FIELD_NAME] as $key=>$value)
						{
							if(
								(is_array($value) && (strlen(implode("", $value)) > 0))
								|| ((!is_array($value)) && (strlen($value) > 0))
							)
							{
								$bFound = true;
								break;
							}
						}
						if(!$bFound)
						{
							$aMsg[] = array("id"=>$FIELD_NAME, "text"=>str_replace("#FIELD_NAME#", $EDIT_FORM_LABEL, GetMessage("USER_TYPE_FIELD_VALUE_IS_MISSING")));
						}
					}
				}
			}
			//identify user type
			if($arUserField["USER_TYPE"])
			{
				$CLASS_NAME = $arUserField["USER_TYPE"]["CLASS_NAME"];
				if(array_key_exists($FIELD_NAME, $arFields) && is_callable(array($CLASS_NAME, "checkfields")))
				{
					if($arUserField["MULTIPLE"]=="N")
					{
						//apply appropriate check function
						$ar = call_user_func_array(
							array($CLASS_NAME, "checkfields"),
							array($arUserField, $arFields[$FIELD_NAME])
						);
						$aMsg = array_merge($aMsg, $ar);
					}
					elseif(is_array($arFields[$FIELD_NAME]))
					{
						foreach($arFields[$FIELD_NAME] as $key=>$value)
						{
							if(!empty($value))
							{
								//apply appropriate check function
								$ar = call_user_func_array(
									array($CLASS_NAME, "checkfields"),
									array($arUserField, $value)
								);
								$aMsg = array_merge($aMsg, $ar);
							}
						}
					}
				}
			}
		}
		//3 Return succsess/fail flag
		if(!empty($aMsg))
		{
			$e = new CAdminException($aMsg);
			$APPLICATION->ThrowException($e);
			return false;
		}
		return true;
	}

	function Update($entity_id, $ID, $arFields)
	{
		$result = false;

		$entity_id = preg_replace("/[^0-9A-Z_]+/", "", $entity_id);

		global $DB;
		$arUpdate = array();
		$arBinds = array();
		$arInsert = array();
		$arInsertType = array();
		$arDelete = array();
		$arUserFields = $this->GetUserFields($entity_id, $ID);
		foreach($arUserFields as $FIELD_NAME=>$arUserField)
		{
			if(array_key_exists($FIELD_NAME, $arFields))
			{
				if($arUserField["MULTIPLE"] == "N")
				{
					if(is_callable(array($arUserField["USER_TYPE"]["CLASS_NAME"], "onbeforesave")))
						$arFields[$FIELD_NAME] = call_user_func_array(array($arUserField["USER_TYPE"]["CLASS_NAME"], "onbeforesave"), array($arUserField, $arFields[$FIELD_NAME]));
					if(strlen($arFields[$FIELD_NAME])>0)
						$arUpdate[$FIELD_NAME] = $arFields[$FIELD_NAME];
					else
						$arUpdate[$FIELD_NAME] = false;
				}
				elseif(is_array($arFields[$FIELD_NAME]))
				{
					$arInsert[$arUserField["ID"]] = array();
					$arInsertType[$arUserField["ID"]] = $arUserField["USER_TYPE"];
					foreach($arFields[$FIELD_NAME] as $key=>$value)
					{
						if(is_callable(array($arUserField["USER_TYPE"]["CLASS_NAME"], "onbeforesave")))
							$value = call_user_func_array(array($arUserField["USER_TYPE"]["CLASS_NAME"], "onbeforesave"), array($arUserField, $value));
						if(strlen($value)>0)
						{
							switch($arInsertType[$arUserField["ID"]]["BASE_TYPE"])
							{
								case "int":
								case "file":
								case "enum":
									$value = intval($value);
									break;
								case "double":
									$value = doubleval($value);
									break;
								case "datetime":
									//TODO: convert to valid site date/time
									//$value = $DB->CharToDateFunction($value);
									break;
								default:
									// For SQL will follow
									$value = substr($value, 0, 2000);
							}
							$arInsert[$arUserField["ID"]][] = $value;
						}
					}
					$arBinds[$FIELD_NAME] = $arUpdate[$FIELD_NAME] = serialize($arInsert[$arUserField["ID"]]);
					$arDelete[$arUserField["ID"]] = true;
				}
			}
		}

		if($DB->TableExists("b_uts_".strtolower($entity_id)) || $DB->TableExists("B_UTS_".strtoupper($entity_id)))
			$strUpdate = $DB->PrepareUpdate("b_uts_".strtolower($entity_id), $arUpdate);
		else
			return $result;

		if(strlen($strUpdate) > 0)
		{
			$result = true;
			$rs = $DB->QueryBind("UPDATE b_uts_".strtolower($entity_id)." SET ".$strUpdate." WHERE VALUE_ID = ".intval($ID), $arBinds);
			$rows = $rs->AffectedRowsCount();
		}
		else
		{
			$rows = 0;
		}

		if(intval($rows)<=0)
		{
			$rs = $DB->Query("SELECT 'x' FROM b_uts_".strtolower($entity_id)." WHERE VALUE_ID = ".intval($ID), false, "FILE: ".__FILE__."<br>LINE: ".__LINE__);
			if($rs->Fetch())
				$rows = 1;
		}

		if($rows <= 0)
		{
			$arUpdate["ID"] = $arUpdate["VALUE_ID"] = $ID;
			$DB->Add("b_uts_".strtolower($entity_id), $arUpdate, array_keys($arBinds));
		}
		else
		{
			foreach($arDelete as $key=>$value)
			{
				$DB->Query("DELETE from b_utm_".strtolower($entity_id)." WHERE FIELD_ID = ".intval($key)." AND VALUE_ID = ".intval($ID), false, "FILE: ".__FILE__."<br>LINE: ".__LINE__);
			}
		}

		foreach($arInsert as $FieldId=>$arField)
		{
			switch($arInsertType[$FieldId]["BASE_TYPE"])
			{
				case "int":
				case "file":
				case "enum":
					$COLUMN = "VALUE_INT";
					break;
				case "double":
					$COLUMN = "VALUE_DOUBLE";
					break;
				case "datetime":
					$COLUMN = "VALUE_DATE";
					break;
				default:
					$COLUMN = "VALUE";
			}
			foreach($arField as $key=>$value)
			{
				switch($arInsertType[$FieldId]["BASE_TYPE"])
				{
					case "int":
					case "file":
					case "enum":
						break;
					case "double":
						break;
					case "datetime":
						$value = $DB->CharToDateFunction($value);
						break;
					default:
						$value = "'".$DB->ForSql($value)."'";
				}
				$DB->Query("INSERT INTO b_utm_".strtolower($entity_id)." (VALUE_ID, FIELD_ID, ".$COLUMN.")
					VALUES (".intval($ID).", '".$FieldId."', ".$value.")", false, "FILE: ".__FILE__."<br>LINE: ".__LINE__);
			}
		}

		return $result;
	}

	function Delete($entity_id, $ID)
	{
		global $DB;
		if($arUserFields = $this->GetUserFields($entity_id, $ID))
		{
			foreach($arUserFields as $FIELD_NAME => $arUserField)
			{
				if($arUserField["USER_TYPE"]["BASE_TYPE"]=="file")
				{
					if(is_array($arUserField["VALUE"]))
					{
						foreach($arUserField["VALUE"] as $value)
							CFile::Delete($value);
					}
					else
						CFile::Delete($arUserField["VALUE"]);
				}
			}
			$DB->Query("DELETE FROM b_utm_".strtolower($entity_id)." WHERE VALUE_ID = ".intval($ID), false, "FILE: ".__FILE__."<br>LINE: ".__LINE__);
			$DB->Query("DELETE FROM b_uts_".strtolower($entity_id)." WHERE VALUE_ID = ".intval($ID), false, "FILE: ".__FILE__."<br>LINE: ".__LINE__);
		}
	}

	function OnSearchIndex($entity_id, $ID)
	{
		$result = "";
		if($arUserFields = $this->GetUserFields($entity_id, $ID))
		{
			foreach($arUserFields as $FIELD_NAME => $arUserField)
			{
				if($arUserField["IS_SEARCHABLE"]=="Y")
				{
					if($arUserField["USER_TYPE"])
						if(is_callable(array($arUserField["USER_TYPE"]["CLASS_NAME"], "onsearchindex")))
							$result .= "\r\n".call_user_func_array(array($arUserField["USER_TYPE"]["CLASS_NAME"], "onsearchindex"), array($arUserField));
				}
			}
		}
		return $result;
	}

	function GetRights($ENTITY_ID=false, $ID=false)
	{
		if(($ID !== false) && array_key_exists("ID:".$ID, $this->arRightsCache))
		{
			return $this->arRightsCache["ID:".$ID];
		}
		if(($ENTITY_ID !== false) && array_key_exists("ENTITY_ID:".$ENTITY_ID, $this->arRightsCache))
		{
			return $this->arRightsCache["ENTITY_ID:".$ENTITY_ID];
		}

		global $USER;
		if(is_object($USER) && $USER->CanDoOperation('edit_other_settings'))
		{
			$RIGHTS = "X";
		}
		else
		{
			$RIGHTS = "D";
			if($ID !== false)
			{
				$ar = CUserTypeEntity::GetByID($ID);
				if($ar)
					$ENTITY_ID = $ar["ENTITY_ID"];
			}

			foreach(GetModuleEvents("main", "OnUserTypeRightsCheck", true) as $arEvent)
			{
				$res = ExecuteModuleEventEx($arEvent, array($ENTITY_ID));
				if($res > $RIGHTS)
					$RIGHTS = $res;
			}
		}

		if($ID !== false)
		{
			$this->arRightsCache["ID:".$ID] = $RIGHTS;
		}
		if($ENTITY_ID !== false)
		{
			$this->arRightsCache["ENTITY_ID:".$ENTITY_ID] = $RIGHTS;
		}

		return $RIGHTS;
	}

}

class CUserTypeSQL
{
	var $table_alias = "BUF";
	var $entity_id = false;
	var $user_fields = array();

	var $select = array();
	var $filter = array();
	var $order = array();

	/** @var CSQLWhere */
	var $obWhere = false;

	function SetEntity($entity_id, $ID)
	{
		global $USER_FIELD_MANAGER;

		$this->user_fields = $USER_FIELD_MANAGER->GetUserFields($entity_id);
		$this->entity_id = strtolower(preg_replace("/[^0-9A-Z_]+/", "", $entity_id));
		$this->select = array();
		$this->filter = array();
		$this->order = array();

		$this->obWhere = new CSQLWhere;
		$num = 0;
		$arFields = array();
		foreach($this->user_fields as $FIELD_NAME=>$arField)
		{
			if($arField["MULTIPLE"]=="Y")
				$num++;
			$table_alias = $arField["MULTIPLE"]=="N"? $this->table_alias: $this->table_alias.$num;
			$arType = $this->user_fields[$FIELD_NAME]["USER_TYPE"];

			if($arField["MULTIPLE"]=="N")
				$TABLE_FIELD_NAME = $table_alias.".".$FIELD_NAME;
			elseif($arType["BASE_TYPE"]=="int")
				$TABLE_FIELD_NAME = $table_alias.".VALUE_INT";
			elseif($arType["BASE_TYPE"]=="file")
				$TABLE_FIELD_NAME = $table_alias.".VALUE_INT";
			elseif($arType["BASE_TYPE"]=="enum")
				$TABLE_FIELD_NAME = $table_alias.".VALUE_INT";
			elseif($arType["BASE_TYPE"]=="double")
				$TABLE_FIELD_NAME = $table_alias.".VALUE_DOUBLE";
			elseif($arType["BASE_TYPE"]=="datetime")
				$TABLE_FIELD_NAME = $table_alias.".VALUE_DATE";
			else
				$TABLE_FIELD_NAME = $table_alias.".VALUE";

			$arFields[$FIELD_NAME] =  array(
				"TABLE_ALIAS" => $table_alias,
				"FIELD_NAME" => $TABLE_FIELD_NAME,
				"FIELD_TYPE" => $arType["BASE_TYPE"],
				"MULTIPLE" => $arField["MULTIPLE"],
				"JOIN" => $arField["MULTIPLE"]=="N"?
					"INNER JOIN b_uts_".$this->entity_id." ".$table_alias." ON ".$table_alias.".VALUE_ID = ".$ID:
					"INNER JOIN b_utm_".$this->entity_id." ".$table_alias." ON ".$table_alias.".FIELD_ID = ".$arField["ID"]." AND ".$table_alias.".VALUE_ID = ".$ID,
				"LEFT_JOIN" => $arField["MULTIPLE"]=="N"?
					"LEFT JOIN b_uts_".$this->entity_id." ".$table_alias." ON ".$table_alias.".VALUE_ID = ".$ID:
					"LEFT JOIN b_utm_".$this->entity_id." ".$table_alias." ON ".$table_alias.".FIELD_ID = ".$arField["ID"]." AND ".$table_alias.".VALUE_ID = ".$ID,
			);

			if($arType["BASE_TYPE"]=="enum")
			{
				$arFields[$FIELD_NAME."_VALUE"] =  array(
					"TABLE_ALIAS" => $table_alias."EN",
					"FIELD_NAME" => $table_alias."EN.VALUE",
					"FIELD_TYPE" => "string",
					"MULTIPLE" => $arField["MULTIPLE"],
					"JOIN" => $arField["MULTIPLE"]=="N"?
						"INNER JOIN b_uts_".$this->entity_id." ".$table_alias."E ON ".$table_alias."E.VALUE_ID = ".$ID."
						INNER JOIN b_user_field_enum ".$table_alias."EN ON ".$table_alias."EN.ID = ".$table_alias."E.".$FIELD_NAME:
						"INNER JOIN b_utm_".$this->entity_id." ".$table_alias."E ON ".$table_alias."E.FIELD_ID = ".$arField["ID"]." AND ".$table_alias."E.VALUE_ID = ".$ID."
						INNER JOIN b_user_field_enum ".$table_alias."EN ON ".$table_alias."EN.ID = ".$table_alias."E.VALUE_INT",
					"LEFT_JOIN" => $arField["MULTIPLE"]=="N"?
						"LEFT JOIN b_uts_".$this->entity_id." ".$table_alias."E ON ".$table_alias."E.VALUE_ID = ".$ID."
						LEFT JOIN b_user_field_enum ".$table_alias."EN ON ".$table_alias."EN.ID = ".$table_alias."E.".$FIELD_NAME:
						"LEFT JOIN b_utm_".$this->entity_id." ".$table_alias."E ON ".$table_alias."E.FIELD_ID = ".$arField["ID"]." AND ".$table_alias."E.VALUE_ID = ".$ID."
						LEFT JOIN b_user_field_enum ".$table_alias."EN ON ".$table_alias."EN.ID = ".$table_alias."E.VALUE_INT",
				);
			}
		}
		$this->obWhere->SetFields($arFields);
	}

	function SetSelect($arSelect)
	{
		$this->obWhere->bDistinctReqired = false;
		if(is_array($arSelect))
		{
			$this->select = array();
			if(in_array("UF_*", $arSelect))
			{
				foreach($this->user_fields as $FIELD_NAME=>$arField)
				{
					$this->select[$FIELD_NAME] = true;
				}
			}
			else
			{
				foreach($arSelect as $field)
				{
					if(array_key_exists($field, $this->user_fields))
					{
						$this->select[$field] = true;
					}
				}
			}
		}
	}

	function GetDistinct()
	{
		return $this->obWhere->bDistinctReqired;
	}

	function GetSelect()
	{
		global $DB, $USER_FIELD_MANAGER;
		$result = "";
		foreach($this->select as $key=>$value)
		{
			if($this->user_fields[$key]["USER_TYPE"]["BASE_TYPE"] == "datetime" && $this->user_fields[$key]["MULTIPLE"] == "N")
				$result .= ", ".$USER_FIELD_MANAGER->DateTimeToChar($this->table_alias.".".$key)." ".$key;
			else
				$result .= ", ".$this->table_alias.".".$key;
		}
		return $result;
	}

	function GetJoin($ID)
	{
		$result = $this->obWhere->GetJoins();
		if((count($this->select)>0 || count($this->order)>0) && strpos($result, "b_uts_")===false)
			$result .= "\nLEFT JOIN b_uts_".$this->entity_id." ".$this->table_alias." ON ".$this->table_alias.".VALUE_ID = ".$ID;
		return $result;
	}

	function SetOrder($arOrder)
	{
		if(is_array($arOrder))
		{
			$this->order = array();
			foreach($arOrder as $field=>$order)
			{
				if(array_key_exists($field, $this->user_fields))
					$this->order[$field] = $order!="ASC"? "DESC": "ASC";
			}
		}
	}

	function GetOrder($field)
	{
		$field = strtoupper($field);
		if(isset($this->order[$field]))
			$result = $this->table_alias.".".$field;
		else
			$result = "";
		return $result;
	}

	function SetFilter($arFilter)
	{
		if(is_array($arFilter))
			$this->filter = $arFilter;
	}

	function GetFilter()
	{
		return $this->obWhere->GetQuery($this->filter);
	}
}

class CAllSQLWhere
{
	var $fields = array(
	/*
		"ID" => array(
			"FIELD_NAME" => "UF.ID",
		),
	*/
	);
	var $c_joins = array();
	var $l_joins = array();
	var $bDistinctReqired = false;

	static $triple_char = array(
		"!><"=>"NB", //not between
		"!=%"=>"NM", //not Identical by like
		"!%="=>"NM", //not Identical by like
	);

	static $double_char = array(
		"!="=>"NI", //not Identical
		"!%"=>"NS", //not substring
		"><"=>"B",  //between
		">="=>"GE", //greater or equal
		"<="=>"LE", //less or equal
		"=%"=>"M", //Identical by like
		"%="=>"M", //Identical by like
	);

	static $single_char = array(
		"="=>"I", //Identical
		"%"=>"S", //substring
		"?"=>"?", //logical
		">"=>"G", //greater
		"<"=>"L", //less
		"!"=>"N", // not field LIKE val
	);

	function AddFields($arFields)
	{
		if(is_array($arFields))
		{
			foreach($arFields as $key=>$arField)
			{
				$key = strtoupper($key);
				if(!isset($this->fields[$key]) && is_array($arField) && strlen($arField["FIELD_NAME"])>0)
				{
					$ar = array();
					$ar["TABLE_ALIAS"] = $arField["TABLE_ALIAS"];
					$ar["FIELD_NAME"] = $arField["FIELD_NAME"];
					$ar["FIELD_TYPE"] = $arField["FIELD_TYPE"];
					$ar["MULTIPLE"] = isset($arField["MULTIPLE"])? $arField["MULTIPLE"]: "N";
					$ar["JOIN"] = $arField["JOIN"];
					if(isset($arField["LEFT_JOIN"]))
						$ar["LEFT_JOIN"] = $arField["LEFT_JOIN"];
					if(isset($arField["CALLBACK"]))
						$ar["CALLBACK"] = $arField["CALLBACK"];
					$this->fields[$key] = $ar;
				}
			}
		}
	}

	function SetFields($arFields)
	{
		$this->fields = array();
		$this->AddFields($arFields);
	}

	function MakeOperation($key)
	{
		if(isset(self::$triple_char[$op = substr($key,0,3)]))
			return Array("FIELD"=>substr($key,3), "OPERATION"=>self::$triple_char[$op]);
		elseif(isset(self::$double_char[$op = substr($key,0,2)]))
			return Array("FIELD"=>substr($key,2), "OPERATION"=>self::$double_char[$op]);
		elseif(isset(self::$single_char[$op = substr($key,0,1)]))
			return Array("FIELD"=>substr($key,1), "OPERATION"=>self::$single_char[$op]);
		else
			return Array("FIELD"=>$key, "OPERATION"=>"E"); // field LIKE val
	}

	function getOperationByCode($code)
	{
		$all_operations = array_flip(self::$single_char + self::$double_char + self::$triple_char);

		return $all_operations[$code];
	}

	function GetQuery($arFilter)
	{
		$this->l_joins = array();
		$this->c_joins = array();
		foreach($this->fields as $key=>$field)
		{
			$this->l_joins[$field["TABLE_ALIAS"]] = isset($field['LEFT_JOIN']);
			$this->c_joins[$key] = 0;
		}
		return $this->GetQueryEx($arFilter, $this->l_joins);
	}

	function GetQueryEx($arFilter, &$arJoins, $level=0)
	{
		global $DB;

		if(!is_array($arFilter))
			return "";

		$length_func = ($DB->type=="MSSQL" ? "LEN" : "LENGTH");

		$logic = false;
		if(isset($arFilter['LOGIC']))
		{
			$logic = $arFilter["LOGIC"];
			unset($arFilter["LOGIC"]);
		}
		if($logic !== "OR")
			$logic = "AND";

		$result = array();
		foreach($arFilter as $key=>$value)
		{
			if(is_numeric($key))
			{
				$arRecursiveJoins = $arJoins;
				$value = $this->GetQueryEx($value, $arRecursiveJoins, $level+1);
				if(strlen($value)>0)
					$result[] = "(".$value."\n".str_repeat("\t", $level).")";
				foreach($arRecursiveJoins as $TABLE_ALIAS=>$bLeftJoin)
				{
					if($bLeftJoin)
					{
						if($logic == "OR")
							$arJoins[$TABLE_ALIAS] |= true;
						else
							$arJoins[$TABLE_ALIAS] &= true;
					}
					else
					{
						if($logic == "OR")
							$arJoins[$TABLE_ALIAS] |= false;
						else
							$arJoins[$TABLE_ALIAS] &= false;
					}
				}
			}
			else
			{
				$operation = $this->MakeOperation($key);
				$key = strtoupper($operation["FIELD"]);
				$operation = $operation["OPERATION"];

				if(isset($this->fields[$key]))
				{
					$FIELD_NAME = $this->fields[$key]["FIELD_NAME"];
					$FIELD_TYPE = $this->fields[$key]["FIELD_TYPE"];
					if($operation=="G")
						$FIELD_OPERATION = " > ";
					elseif($operation=="L")
						$FIELD_OPERATION = " < ";
					elseif($operation=="GE")
						$FIELD_OPERATION = " >= ";
					elseif($operation=="LE")
						$FIELD_OPERATION = " <= ";
					//Handle joins logic
					$this->c_joins[$key]++;
					if(
						(
							($operation=="I" || $operation=="E" || $operation=="S" || $operation=="M")
							&& (
								is_scalar($value)
								&& (
									($FIELD_TYPE=="int" && intval($value)==0)
									|| ($FIELD_TYPE=="double" && doubleval($value)==0)
									|| strlen($value)<=0
								)
							)
						)
						|| (
							($operation=="NI" || $operation=="N" || $operation=="NS" || $operation=="NB" || $operation=="NM")
							&& (
								is_array($value)
								|| (
									($FIELD_TYPE=="int" && intval($value)!=0)
									|| ($FIELD_TYPE=="double" && doubleval($value)!=0)
									|| ($FIELD_TYPE!="int" && $FIELD_TYPE!="double" && strlen($value)>0)
								)
							)
						)
					)
					{
						if($logic == "OR")
							$arJoins[$this->fields[$key]["TABLE_ALIAS"]] |= true;
						else
							$arJoins[$this->fields[$key]["TABLE_ALIAS"]] &= true;
					}
					else
					{
						if($logic == "OR")
							$arJoins[$this->fields[$key]["TABLE_ALIAS"]] |= false;
						else
							$arJoins[$this->fields[$key]["TABLE_ALIAS"]] &= false;
					}

					switch($FIELD_TYPE)
					{
						case "file":
						case "enum":
						case "int":
							if(is_array($value))
							{
								$FIELD_VALUE = array();
								foreach($value as $val)
									$FIELD_VALUE[] = intval($val);
							}
							elseif (is_object($value))
							{
								$FIELD_VALUE = $value;
							}
							else
								$FIELD_VALUE = intval($value);
							switch($operation)
							{
								case "I":
								case "E":
								case "S":
								case "M":
									if(is_array($FIELD_VALUE))
									{
										if(count($FIELD_VALUE))
										{
											$_result = $FIELD_NAME." in (".implode(", ", $FIELD_VALUE).")";

											if (in_array(0, $FIELD_VALUE, true))
											{
												$_result = '('.$_result.' OR '.$FIELD_NAME.' IS NULL)';
											}

											$result[] = $_result;
										}
										else
											$result[] = "1=0";

										if($this->fields[$key]["MULTIPLE"] === "Y")
											$this->bDistinctReqired = true;
									}
									elseif (is_object($FIELD_VALUE))
										$result[] = $FIELD_NAME." = ".$FIELD_VALUE->compile();
									elseif($FIELD_VALUE==0)
										$result[] = "(".$FIELD_NAME." IS NULL OR ".$FIELD_NAME." = 0)";
									else
										$result[] = $FIELD_NAME." = ".$FIELD_VALUE;
									break;
								case "NI":
								case "N":
								case "NS":
								case "NM":
									if(is_array($FIELD_VALUE))
									{
										if(count($FIELD_VALUE))
											$result[] = $FIELD_NAME." not in (".implode(", ", $FIELD_VALUE).")";
										else
											$result[] = "1=1";
									}
									elseif($FIELD_VALUE==0)
										$result[] = "(".$FIELD_NAME." IS NOT NULL AND ".$FIELD_NAME." <> 0)";
									else
										$result[] = $FIELD_NAME." <> ".$FIELD_VALUE;

									if($this->fields[$key]["MULTIPLE"] === "Y")
										$this->bDistinctReqired = true;
									break;
								case "G":
								case "L":
								case "GE":
								case "LE":
									if(is_array($FIELD_VALUE))
										$result[] = $FIELD_NAME.$FIELD_OPERATION.$FIELD_VALUE[0];
									else
										$result[] = $FIELD_NAME.$FIELD_OPERATION.$FIELD_VALUE;

									if($this->fields[$key]["MULTIPLE"] === "Y")
										$this->bDistinctReqired = true;
									break;
								case "B":
									if(is_array($FIELD_VALUE) && count($FIELD_VALUE)>1)
										$result[] = $FIELD_NAME." between ".$FIELD_VALUE[0]." AND ".$FIELD_VALUE[1];

									if($this->fields[$key]["MULTIPLE"] === "Y")
										$this->bDistinctReqired = true;
									break;
								case "NB":
									if(is_array($FIELD_VALUE) && count($FIELD_VALUE)>1)
										$result[] = $FIELD_NAME." not between ".$FIELD_VALUE[0]." AND ".$FIELD_VALUE[1];

									if($this->fields[$key]["MULTIPLE"] === "Y")
										$this->bDistinctReqired = true;
									break;
							}
							break;
						case "double":
							if(is_array($value))
							{
								$FIELD_VALUE = array();
								foreach($value as $val)
									$FIELD_VALUE[] = doubleval($val);
							}
							else
							{
								$FIELD_VALUE = doubleval($value);
							}

							switch($operation)
							{
								case "I":
								case "E":
								case "S":
								case "M":
									if(is_array($FIELD_VALUE))
										$result[] = $FIELD_NAME." in (".implode(", ", $FIELD_VALUE).")";
									elseif($FIELD_VALUE==0)
										$result[] = "(".$FIELD_NAME." IS NULL OR ".$FIELD_NAME." = 0)";
									else
										$result[] = $FIELD_NAME." = ".$FIELD_VALUE;

									if($this->fields[$key]["MULTIPLE"] === "Y")
										$this->bDistinctReqired = true;
									break;
								case "NI":
								case "N":
								case "NS":
								case "NM":
									if(is_array($FIELD_VALUE))
										$result[] = $FIELD_NAME." not in (".implode(", ", $FIELD_VALUE).")";
									elseif($FIELD_VALUE==0)
										$result[] = "(".$FIELD_NAME." IS NOT NULL AND ".$FIELD_NAME." <> 0)";
									else
										$result[] = $FIELD_NAME." <> ".$FIELD_VALUE;

									if($this->fields[$key]["MULTIPLE"] === "Y")
										$this->bDistinctReqired = true;
									break;
								case "G":
								case "L":
								case "GE":
								case "LE":
									if(is_array($FIELD_VALUE))
										$result[] = $FIELD_NAME.$FIELD_OPERATION.$FIELD_VALUE[0];
									else
										$result[] = $FIELD_NAME.$FIELD_OPERATION.$FIELD_VALUE;

									if($this->fields[$key]["MULTIPLE"] === "Y")
										$this->bDistinctReqired = true;
									break;
								case "B":
									if(is_array($FIELD_VALUE) && count($FIELD_VALUE)>1)
										$result[] = $FIELD_NAME." between ".$FIELD_VALUE[0]." AND ".$FIELD_VALUE[1];

									if($this->fields[$key]["MULTIPLE"] === "Y")
										$this->bDistinctReqired = true;
									break;
								case "NB":
									if(is_array($FIELD_VALUE) && count($FIELD_VALUE)>1)
										$result[] = $FIELD_NAME." not between ".$FIELD_VALUE[0]." AND ".$FIELD_VALUE[1];

									if($this->fields[$key]["MULTIPLE"] === "Y")
										$this->bDistinctReqired = true;
									break;
							}
							break;
						case "string":
							if(is_array($value))
							{
								$FIELD_VALUE = array();
								if($operation=="S" || $operation=="NS")
								{
									foreach($value as $val)
										$FIELD_VALUE[] = $this->ForLIKE(toupper($val));
								}
								else
								{
									foreach($value as $val)
										$FIELD_VALUE[] = $DB->ForSQL($val);
								}
							}
							elseif (is_object($value))
							{
								$FIELD_VALUE = $value;
							}
							else
							{
								if($operation=="S" || $operation=="NS")
									$FIELD_VALUE = $this->ForLIKE(toupper($value));
								else
									$FIELD_VALUE = $DB->ForSQL($value);
							}

							switch($operation)
							{
								case "I":
									if(is_array($FIELD_VALUE))
									{
										$result[] = $this->_StringIN($FIELD_NAME, $FIELD_VALUE);
										if($this->fields[$key]["MULTIPLE"] === "Y")
											$this->bDistinctReqired = true;
									}
									elseif (is_object($FIELD_VALUE))
									{
										$result[] = $this->_ExprEQ($FIELD_NAME, $FIELD_VALUE);
									}
									elseif(strlen($FIELD_VALUE)<=0)
										$result[] = $this->_Empty($FIELD_NAME);
									else
										$result[] = $this->_StringEQ($FIELD_NAME, $FIELD_VALUE);
									break;
								case "E":
									if(is_array($FIELD_VALUE))
										$result[] = "(".$this->_Upper($FIELD_NAME)." like '".implode("' OR ".$this->_Upper($FIELD_NAME)." like '", $FIELD_VALUE)."')";
									elseif (is_object($FIELD_VALUE))
										$result[] = $this->_ExprEQ($FIELD_NAME, $FIELD_VALUE);
									elseif(strlen($FIELD_VALUE)<=0)
										$result[] = $this->_Empty($FIELD_NAME);
									else
									{
										//kinda optimization for digits only
										if(preg_match("/[^0-9]/", $FIELD_VALUE))
											$result[] = $this->_Upper($FIELD_NAME)." like '".$FIELD_VALUE."'";
										else
											$result[] = $this->_StringEQ($FIELD_NAME, $FIELD_VALUE);
									}

									if($this->fields[$key]["MULTIPLE"] === "Y")
										$this->bDistinctReqired = true;
									break;
								case "S":
									if(is_array($FIELD_VALUE))
										$result[] = "(".$this->_Upper($FIELD_NAME)." like '%".implode("%' ESCAPE '!' OR ".$this->_Upper($FIELD_NAME)." like '%", $FIELD_VALUE)."%' ESCAPE '!')";
									elseif (is_object($FIELD_VALUE))
										$result[] = $this->_Upper($FIELD_NAME)." like ".$FIELD_VALUE->compile()." ESCAPE '!'";
									elseif(strlen($FIELD_VALUE)<=0)
										$result[] = $this->_Empty($FIELD_NAME);
									else
										$result[] = $this->_Upper($FIELD_NAME)." like '%".$FIELD_VALUE."%' ESCAPE '!'";

									if($this->fields[$key]["MULTIPLE"] === "Y")
										$this->bDistinctReqired = true;
									break;
								case "M":
									if(is_array($FIELD_VALUE))
										$result[] = "(".$FIELD_NAME." like '".implode("' OR ".$FIELD_NAME." like '", $FIELD_VALUE)."')";
									elseif (is_object($FIELD_VALUE))
										$result[] = $this->_ExprEQ($FIELD_NAME, $FIELD_VALUE);
									elseif(strlen($FIELD_VALUE)<=0)
										$result[] = $this->_Empty($FIELD_NAME);
									else
									{
										//kinda optimization for digits only
										if(preg_match("/[^0-9]/", $FIELD_VALUE))
											$result[] = $FIELD_NAME." like '".$FIELD_VALUE."'";
										else
											$result[] = $this->_StringEQ($FIELD_NAME, $FIELD_VALUE);
									}

									if($this->fields[$key]["MULTIPLE"] === "Y")
										$this->bDistinctReqired = true;
									break;
								case "NI":
									if(is_array($FIELD_VALUE))
										$result[] = $this->_StringNotIN($FIELD_NAME, $FIELD_VALUE);
									elseif (is_object($FIELD_VALUE))
										$result[] = $this->_ExprNotEQ($FIELD_NAME, $FIELD_VALUE);
									elseif(strlen($FIELD_VALUE)<=0)
										$result[] = $this->_NotEmpty($FIELD_NAME);
									else
										$result[] = $this->_StringNotEQ($FIELD_NAME, $FIELD_VALUE);

									if($this->fields[$key]["MULTIPLE"] === "Y")
										$this->bDistinctReqired = true;
									break;
								case "N":
									if(is_array($FIELD_VALUE))
										$result[] = "(".$this->_Upper($FIELD_NAME)." not like '".implode("' AND ".$this->_Upper($FIELD_NAME)." not like '", $FIELD_VALUE)."')";
									elseif (is_object($FIELD_VALUE))
										$result[] = $this->_Upper($FIELD_NAME)." not like ".$FIELD_VALUE->compile();
									elseif(strlen($FIELD_VALUE)<=0)
										$result[] = $this->_NotEmpty($FIELD_NAME);
									else
										$result[] = $this->_Upper($FIELD_NAME)." not like '".$FIELD_VALUE."'";

									if($this->fields[$key]["MULTIPLE"] === "Y")
										$this->bDistinctReqired = true;
									break;
								case "NS":
									if(is_array($FIELD_VALUE))
										$result[] = "(".$this->_Upper($FIELD_NAME)." not like '%".implode("%' ESCAPE '!' AND ".$this->_Upper($FIELD_NAME)." not like '%", $FIELD_VALUE)."%' ESCAPE '!')";
									elseif (is_object($FIELD_VALUE))
										$result[] = $this->_Upper($FIELD_NAME)." not like ".$FIELD_VALUE->compile();
									elseif(strlen($FIELD_VALUE)<=0)
										$result[] = $this->_NotEmpty($FIELD_NAME);
									else
										$result[] = $this->_Upper($FIELD_NAME)." not like '%".$FIELD_VALUE."%' ESCAPE '!'";

									if($this->fields[$key]["MULTIPLE"] === "Y")
										$this->bDistinctReqired = true;
									break;
								case "NM":
									if(is_array($FIELD_VALUE))
										$result[] = "(".$FIELD_NAME." not like '".implode("' AND ".$FIELD_NAME." not like '", $FIELD_VALUE)."')";
									elseif (is_object($FIELD_VALUE))
										$result[] = $FIELD_NAME." not like ".$FIELD_VALUE->compile();
									elseif(strlen($FIELD_VALUE)<=0)
										$result[] = $this->_NotEmpty($FIELD_NAME);
									else
										$result[] = $FIELD_NAME." not like '".$FIELD_VALUE."'";

									if($this->fields[$key]["MULTIPLE"] === "Y")
										$this->bDistinctReqired = true;
									break;
								case "G":
								case "L":
								case "GE":
								case "LE":
									if(is_array($FIELD_VALUE))
										$result[] = $FIELD_NAME.$FIELD_OPERATION."'".$FIELD_VALUE[0]."'";
									elseif (is_object($FIELD_VALUE))
										$result[] = $FIELD_NAME.$FIELD_OPERATION.$FIELD_VALUE->compile();
									else
										$result[] = $FIELD_NAME.$FIELD_OPERATION."'".$FIELD_VALUE."'";

									if($this->fields[$key]["MULTIPLE"] === "Y")
										$this->bDistinctReqired = true;
									break;
								case "B":
									if(is_array($FIELD_VALUE) && count($FIELD_VALUE)>1)
										$result[] = $FIELD_NAME." between '".$FIELD_VALUE[0]."' AND '".$FIELD_VALUE[1]."'";

									if($this->fields[$key]["MULTIPLE"] === "Y")
										$this->bDistinctReqired = true;
									break;
								case "NB":
									if(is_array($FIELD_VALUE) && count($FIELD_VALUE)>1)
										$result[] = $FIELD_NAME." not between '".$FIELD_VALUE[0]."' AND '".$FIELD_VALUE[1]."'";

									if($this->fields[$key]["MULTIPLE"] === "Y")
										$this->bDistinctReqired = true;
									break;
								case "?":
									if(is_scalar($FIELD_VALUE) && strlen($FIELD_VALUE))
									{
										$q = GetFilterQuery($FIELD_NAME, $FIELD_VALUE);
										// Check if error ("0" was returned)
										if($q !== '0')
											$result[] = $q;
									}
									break;
							}
							break;
						case "date":
						case "datetime":
							if($FIELD_TYPE=="date")
								$format = "SHORT";
							else
								$format = "FULL";
							if(is_array($value))
							{
								$FIELD_VALUE = array();
								foreach($value as $val)
									$FIELD_VALUE[] = $DB->CharToDateFunction($val, $format);
							}
							elseif (strlen($value))
							{
								$FIELD_VALUE = $DB->CharToDateFunction($value, $format);
							}
							else
							{
								$FIELD_VALUE = '';
							}

							switch($operation)
							{
								case "I":
								case "E":
								case "S":
								case "M":
									if(is_array($FIELD_VALUE))
									{
										$result[] = $FIELD_NAME." in (".implode(", ", $FIELD_VALUE).")";
										if($this->fields[$key]["MULTIPLE"] === "Y")
											$this->bDistinctReqired = true;
									}
									elseif(strlen($value)<=0)
										$result[] = "(".$FIELD_NAME." IS NULL)";
									else
										$result[] = $FIELD_NAME." = ".$FIELD_VALUE;
									break;
								case "NI":
								case "N":
								case "NS":
								case "NM":
									if(is_array($FIELD_VALUE))
										$result[] = $FIELD_NAME." not in (".implode(", ", $FIELD_VALUE).")";
									elseif(strlen($value)<=0)
										$result[] = "(".$FIELD_NAME." IS NOT NULL)";
									else
										$result[] = $FIELD_NAME." <> ".$FIELD_VALUE;

									if($this->fields[$key]["MULTIPLE"] === "Y")
										$this->bDistinctReqired = true;
									break;
								case "G":
								case "L":
								case "GE":
								case "LE":
									if(is_array($FIELD_VALUE))
										$result[] = $FIELD_NAME.$FIELD_OPERATION.$FIELD_VALUE[0];
									else
										$result[] = $FIELD_NAME.$FIELD_OPERATION.$FIELD_VALUE;

									if($this->fields[$key]["MULTIPLE"] === "Y")
										$this->bDistinctReqired = true;
									break;
								case "B":
									if(is_array($FIELD_VALUE) && count($FIELD_VALUE)>1)
										$result[] = $FIELD_NAME." between ".$FIELD_VALUE[0]." AND ".$FIELD_VALUE[1];

									if($this->fields[$key]["MULTIPLE"] === "Y")
										$this->bDistinctReqired = true;
									break;
								case "NB":
									if(is_array($FIELD_VALUE) && count($FIELD_VALUE)>1)
										$result[] = $FIELD_NAME." not between ".$FIELD_VALUE[0]." AND ".$FIELD_VALUE[1];

									if($this->fields[$key]["MULTIPLE"] === "Y")
										$this->bDistinctReqired = true;
									break;
							}
							break;
						case "callback":
							$res = call_user_func_array($this->fields[$key]["CALLBACK"], array(
								$FIELD_NAME,
								$operation,
								$value,
							));
							if(strlen($res))
								$result[] = $res;
							break;
					}
				}
			}
		}

		if(count($result)>0)
			return "\n".str_repeat("\t", $level).implode("\n".str_repeat("\t", $level).$logic." ", $result);
		else
			return "";
	}

	function GetJoins()
	{
		$result = array();

		foreach($this->c_joins as $key => $counter)
		{
			if($counter > 0)
			{
				$TABLE_ALIAS = $this->fields[$key]["TABLE_ALIAS"];
				if($this->l_joins[$TABLE_ALIAS])
					$result[$TABLE_ALIAS] = $this->fields[$key]["LEFT_JOIN"];
				else
					$result[$TABLE_ALIAS] = $this->fields[$key]["JOIN"];
			}
		}
		return implode("\n", $result);
	}

	function ForLIKE($str)
	{
		global $DB;
		static $search  = array( "!",  "_",  "%");
		static $replace = array("!!", "!_", "!%");
		return str_replace($search, $replace, $DB->ForSQL($str));
	}
}

class CSQLWhereExpression
{
	protected
		$expression,
		$args;

	protected
		$i;

	protected
		$DB;

	public function __construct($expression, $args = null)
	{
		$this->expression = $expression;

		if (!is_null($args))
		{
			$this->args =  is_array($args) ? $args : array($args);
		}

		global $DB;
		$this->DB = $DB;
	}

	public function compile()
	{
		$this->i = -1;

		// string (default), integer (i), float (f), numeric (n), date (d), time (t)
		$value = preg_replace_callback('/(?:[^\\\\]|^)(\?[#sif]?)/', array($this, 'execPlaceholders'), $this->expression);
		$value = str_replace('\?', '?', $value);

		return $value;
	}

	protected function execPlaceholders($matches)
	{
		$this->i++;

		$id = $matches[1];

		if (isset($this->args[$this->i]))
		{
			$value = $this->args[$this->i];

			if ($id == '?' || $id == '?s')
			{
				return "'" . $this->DB->ForSql($value) . "'";
			}
			elseif ($id == '?#')
			{
				$value = str_replace('.', $this->DB->escR.'.'.$this->DB->escL, $value);
				return $this->DB->escL . $value . $this->DB->escR;
			}
			elseif ($id == '?i')
			{
				return (int) $value;
			}
			elseif ($id == '?f')
			{
				return (float) $value;
			}
		}

		return $id;
	}
}

/*
		array("LOGIC"=>"AND",
			"="."K1" => value,
			"="."K2" => value,
			array("LOGIC"=>"OR",
				"="."K3" => value,
				"="."K3" => value,
			),
			array("LOGIC"=>"OR",
				"="."K4" => value,
				"="."K4" => value,
			),
		)
		K1=value and K2=value and (k3=value or k3=value) and (k4=value or k4=value)
*/

class CUserFieldEnum
{
	function SetEnumValues($FIELD_ID, $values)
	{
		global $DB, $CACHE_MANAGER;
		$aMsg = array();

		/*check unique XML_ID*/
		$arAdded = array();
		foreach($values as $key=>$value)
		{
			if(strncmp($key, "n", 1)===0 && $value["DEL"]!="Y" && strlen($value["VALUE"])>0)
			{
				if(strlen($value["XML_ID"])<=0)
					$value["XML_ID"] = md5($value["VALUE"]);

				if(array_key_exists($value["XML_ID"], $arAdded))
				{
					$aMsg[] = array("text"=>GetMessage("USER_TYPE_XML_ID_UNIQ", array("#XML_ID#"=>$value["XML_ID"])));
				}
				else
				{
					$rsEnum = $this->GetList(array(), array("USER_FIELD_ID"=>$FIELD_ID, "XML_ID"=>$value["XML_ID"]));
					if($arEnum = $rsEnum->Fetch())
					{
						$aMsg[] = array("text"=>GetMessage("USER_TYPE_XML_ID_UNIQ", array("#XML_ID#"=>$value["XML_ID"])));
					}
					else
					{
						$arAdded[$value["XML_ID"]]++;
					}
				}
			}
		}

		$rsEnum = $this->GetList(array(), array("USER_FIELD_ID"=>$FIELD_ID));
		while($arEnum = $rsEnum->Fetch())
		{
			if(array_key_exists($arEnum["ID"], $values))
			{
				$value = $values[$arEnum["ID"]];
				if(strlen($value["VALUE"])<=0 || $value["DEL"]=="Y")
				{
				}
				elseif(
					$arEnum["VALUE"] != $value["VALUE"] ||
					$arEnum["DEF"] != $value["DEF"] ||
					$arEnum["SORT"] != $value["SORT"] ||
					$arEnum["XML_ID"] != $value["XML_ID"]
				)
				{
					if(strlen($value["XML_ID"])<=0)
						$value["XML_ID"] = md5($value["VALUE"]);

					$bUnique = true;
					if($arEnum["XML_ID"] != $value["XML_ID"])
					{
						if(array_key_exists($value["XML_ID"], $arAdded))
						{
							$aMsg[] = array("text"=>GetMessage("USER_TYPE_XML_ID_UNIQ", array("#XML_ID#"=>$value["XML_ID"])));
							$bUnique = false;
						}
						else
						{
							$rsEnumXmlId = $this->GetList(array(), array("USER_FIELD_ID"=>$FIELD_ID, "XML_ID"=>$value["XML_ID"]));
							if($arEnumXmlId = $rsEnumXmlId->Fetch())
							{
								$aMsg[] = array("text"=>GetMessage("USER_TYPE_XML_ID_UNIQ", array("#XML_ID#"=>$value["XML_ID"])));
								$bUnique = false;
							}
						}
					}
					if($bUnique)
					{
						$arAdded[$value["XML_ID"]]++;
					}
				}
			}
		}

		if(!empty($aMsg))
		{
			$e = new CAdminException($aMsg);
			$GLOBALS['APPLICATION']->ThrowException($e);
			return false;
		}

		if(CACHED_b_user_field_enum!==false)
			$CACHE_MANAGER->CleanDir("b_user_field_enum");

		foreach($values as $key=>$value)
		{
			if(strncmp($key, "n", 1)===0 && $value["DEL"]!="Y" && strlen($value["VALUE"])>0)
			{
				if(strlen($value["XML_ID"])<=0)
					$value["XML_ID"] = md5($value["VALUE"]);

				if($value["DEF"]!="Y")
					$value["DEF"]="N";
				$value["USER_FIELD_ID"] = $FIELD_ID;
				$DB->Add("b_user_field_enum", $value);
				unset($values[$key]);
			}
		}
		$rsEnum = $this->GetList(array(), array("USER_FIELD_ID"=>$FIELD_ID));
		while($arEnum = $rsEnum->Fetch())
		{
			if(array_key_exists($arEnum["ID"], $values))
			{
				$value = $values[$arEnum["ID"]];
				if(strlen($value["VALUE"])<=0 || $value["DEL"]=="Y")
				{
					$DB->Query("DELETE FROM b_user_field_enum WHERE ID = ".$arEnum["ID"], false, "FILE: ".__FILE__."<br>LINE: ".__LINE__);
				}
				elseif($arEnum["VALUE"] != $value["VALUE"] ||
					$arEnum["DEF"] != $value["DEF"] ||
					$arEnum["SORT"] != $value["SORT"] ||
					$arEnum["XML_ID"] != $value["XML_ID"])
				{
					if(strlen($value["XML_ID"])<=0)
						$value["XML_ID"] = md5($value["VALUE"]);

					unset($value["ID"]);
					$strUpdate = $DB->PrepareUpdate("b_user_field_enum", $value);
					if(strlen($strUpdate)>0)
						$DB->Query("UPDATE b_user_field_enum SET ".$strUpdate." WHERE ID = ".$arEnum["ID"], false, "FILE: ".__FILE__."<br>LINE: ".__LINE__);
				}
			}
		}
		if(CACHED_b_user_field_enum!==false)
			$CACHE_MANAGER->CleanDir("b_user_field_enum");

		return true;
	}

	function GetList($aSort=array(), $aFilter=array())
	{
		global $DB, $CACHE_MANAGER;

		if(CACHED_b_user_field_enum !== false)
		{
			$cacheId = "b_user_field_enum".md5(serialize($aSort).".".serialize($aFilter));
			if($CACHE_MANAGER->Read(CACHED_b_user_field_enum, $cacheId, "b_user_field_enum"))
			{
				$arResult = $CACHE_MANAGER->Get($cacheId);
				$res = new CDBResult;
				$res->InitFromArray($arResult);
				return $res;
			}
		}
		else
		{
			$cacheId = '';
		}

		$bJoinUFTable = false;
		$arFilter = array();
		foreach($aFilter as $key=>$val)
		{
			if(is_array($val))
			{
				if(count($val) <= 0)
					continue;
				$val = array_map(array($DB, "ForSQL"), $val);
				$val = "('".implode("', '", $val)."')";
			}
			else
			{
				if(strlen($val) <= 0)
					continue;
				$val = "('".$DB->ForSql($val)."')";
			}

			$key = strtoupper($key);
			switch($key)
			{
			case "ID":
			case "USER_FIELD_ID":
			case "VALUE":
			case "DEF":
			case "SORT":
			case "XML_ID":
				$arFilter[] = "UFE.".$key." in ".$val;
				break;
			case "USER_FIELD_NAME":
				$bJoinUFTable = true;
				$arFilter[] = "UF.FIELD_NAME in ".$val;
				break;
			}
		}

		$arOrder = array();
		foreach($aSort as $key=>$val)
		{
			$key = strtoupper($key);
			$ord = (strtoupper($val) <> "ASC"? "DESC": "ASC");
			switch($key)
			{
				case "ID":
				case "USER_FIELD_ID":
				case "VALUE":
				case "DEF":
				case "SORT":
				case "XML_ID":
					$arOrder[] = "UFE.".$key." ".$ord;
					break;
			}
		}
		if(count($arOrder) == 0)
		{
			$arOrder[] = "UFE.SORT asc";
			$arOrder[] = "UFE.ID asc";
		}
		DelDuplicateSort($arOrder);
		$sOrder = "\nORDER BY ".implode(", ", $arOrder);

		if(count($arFilter) == 0)
			$sFilter = "";
		else
			$sFilter = "\nWHERE ".implode("\nAND ", $arFilter);

		$strSql = "
			SELECT
				UFE.ID
				,UFE.USER_FIELD_ID
				,UFE.VALUE
				,UFE.DEF
				,UFE.SORT
				,UFE.XML_ID
			FROM
				b_user_field_enum UFE
				".($bJoinUFTable? "INNER JOIN b_user_field UF ON UF.ID = UFE.USER_FIELD_ID": "")."
			".$sFilter.$sOrder;

		if($cacheId == '')
		{
			$res = $DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		}
		else
		{
			$arResult = array();
			$res = $DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
			while($ar = $res->Fetch())
				$arResult[]=$ar;

			$CACHE_MANAGER->Set($cacheId, $arResult);

			$res = new CDBResult;
			$res->InitFromArray($arResult);
		}

		return  $res;
	}

	function DeleteFieldEnum($FIELD_ID)
	{
		global $DB, $CACHE_MANAGER;
		$DB->Query("DELETE FROM b_user_field_enum WHERE USER_FIELD_ID = ".intval($FIELD_ID), false, "FILE: ".__FILE__."<br>LINE: ".__LINE__);
		if(CACHED_b_user_field_enum!==false) $CACHE_MANAGER->CleanDir("b_user_field_enum");
	}
}
?>
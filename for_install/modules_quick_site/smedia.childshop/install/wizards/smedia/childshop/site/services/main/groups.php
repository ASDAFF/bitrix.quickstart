<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

	
$groupsMacros=array();		
$arGroups = Array(
		Array(
				'ACTIVE' => 'Y',
				'C_SORT' => '300',
				'ANONYMOUS' => 'N',
				'NAME' => GetMessage('content_editor_NAME'),
				'DESCRIPTION' => GetMessage('content_editor_DESCRIPTION'),
				'STRING_ID' => 'content_editor',
				'STRING_ID_MACROS' => 'content_editor',
				),
		Array(
				'ACTIVE' => 'Y',
				'C_SORT' => '200',
				'ANONYMOUS' => 'N',
				'NAME' => GetMessage('sale_administrator_NAME'),
				'DESCRIPTION' => GetMessage('sale_administrator_DESCRIPTION'),
				'STRING_ID' => 'sale_administrator',
				'STRING_ID_MACROS' => 'sale_administrator',
				),

);
			
	$SiteGroup = array();
	$SiteGroups = array();
	$group = new CGroup;
	foreach ($arGroups as $arGroup)
	{		
		//Add Group
		$dbResult = CGroup::GetList($by, $order, Array("STRING_ID" => $arGroup["STRING_ID"], "STRING_ID_EXACT_MATCH" => "Y"));
		if ($arExistsGroup = $dbResult->Fetch())
			$groupID = $arExistsGroup["ID"];
		else
			$groupID = $group->Add($arGroup);
		if ($groupID <= 0)
			continue;
		
		$groupsMacros[$arGroups['STRING_ID_MACROS']]=$groupID;
		
		$SiteGroup["STRING_ID"] = $arGroup["STRING_ID"];
		$SiteGroups[$arGroup["STRING_ID"]] = $groupID;
		

		if (WIZARD_IS_RERUN === false)
		{
			if ($arGroup["STRING_ID"] == "EMPLOYEES_".WIZARD_SITE_ID)
			{
				COption::SetOptionString("main", "new_user_registration_def_group", $groupID);	
			}
	
		}
	}
	
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/.access.php", Array(
														"content_editor"=>$groupsMacros["content_editor"],
														)
													);
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/usr/Publisher/public/tirage_solutions/igrushka/TEST/upload/sm_wizard/s1/site/public/ru/.access.php", Array(
														"content_editor"=>$groupsMacros["content_editor"],
														"sale_administrator"=>$groupsMacros["sale_administrator"],
														)
													);

$APPLICATION->SetGroupRight("main", $groupsMacros["sale_administrator"], "Q");
$APPLICATION->SetGroupRight("sale", $groupsMacros["sale_administrator"], "U");
$APPLICATION->SetGroupRight("catalog", $groupsMacros["sale_administrator"], "T");
$APPLICATION->SetGroupRight("main", $groupsMacros["content_editor"], "P");
$APPLICATION->SetGroupRight("fileman", $groupsMacros["content_editor"], "F");

	
if(!WIZARD_IS_RERUN)
{		
	//admin security policy
	$z = CGroup::GetByID(1);
	if($res = $z->Fetch())
	{
		if($res["SECURITY_POLICY"] == "")
		{
			$group = new CGroup;
			$arGroupPolicy = array(
				"SESSION_TIMEOUT" => 15, //minutes
				"SESSION_IP_MASK" => "255.255.255.255",
				"MAX_STORE_NUM" => 1,
				"STORE_IP_MASK" => "255.255.255.255",
				"STORE_TIMEOUT" => 60*24*3, //minutes
				"CHECKWORD_TIMEOUT" => 60,  //minutes
				"PASSWORD_LENGTH" => 10,
				"PASSWORD_UPPERCASE" => "Y",
				"PASSWORD_LOWERCASE" => "Y",
				"PASSWORD_DIGITS" => "Y",
				"PASSWORD_PUNCTUATION" => "Y",
				"LOGIN_ATTEMPTS" => 3,
			);
			$arFields = array(
				"SECURITY_POLICY" => serialize($arGroupPolicy)
			);
			$group->Update(1, $arFields);
		}
	}
	
		$dbResult = CGroup::GetList($by, $order, Array("STRING_ID" => "EMPLOYEES_".WIZARD_SITE_ID, "STRING_ID_EXACT_MATCH" => "Y"));
		if ($arExistsGroup = $dbResult->Fetch())
			$groupID = $arExistsGroup["ID"];
			
		if($groupID)
		{
			$dbResult = CGroup::GetList($by, $order, Array("STRING_ID" => "PERSONNEL_DEPARTMENT", "STRING_ID_EXACT_MATCH" => "Y"));
			if ($arExistsGroup = $dbResult->Fetch()){
				$groupID = $arExistsGroup["ID"];
				$arSubordinateGroups = CGroup::GetSubordinateGroups($groupID);
				$arSubordinateGroups[] = $SiteGroups["EMPLOYEES_".WIZARD_SITE_ID];
				CGroup::SetSubordinateGroups($groupID, $arSubordinateGroups);
			}
			CGroup::SetSubordinateGroups($SiteGroups["PORTAL_ADMINISTRATION_".WIZARD_SITE_ID], Array($SiteGroups["EMPLOYEES_".WIZARD_SITE_ID]));
			
		}
		
		$allowGuests = COption::GetOptionString("main", "wizard_allow_group", "N", WIZARD_SITE_ID);
		if($allowGuests == "Y")
		{
			$dbResult = CGroup::GetList($by, $order, Array("STRING_ID_EXACT_MATCH" => "Y"));
			while ($arExistsGroup = $dbResult->Fetch())
			{
				if($arExistsGroup["ID"] != 1 && $arExistsGroup["ID"] !=2)
				{
					 
					if(!in_array($arExistsGroup["STRING_ID"], $SiteGroup["STRING_ID"]))
					{
						$allowGuests = COption::GetOptionString("main", "wizard_allow_group", "N", $site_id);
						WizardServices::SetFilePermission(Array(WIZARD_SITE_ID, WIZARD_SITE_DIR), Array($arExistsGroup["ID"] => "D"));
					}
				}
			}
		}
	
}

?>
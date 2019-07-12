<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (!defined("WIZARD_SITE_ID"))
	return;

if (COption::GetOptionString("main", "site_personal_create", "N") == "Y")
{
	$site_id = COption::GetOptionString("main", "site_personal_id"); 
	
	$db_res = CSite::GetList($by="sort", $order="desc", array("LID" => $site_id));
	if (!($db_res && $res = $db_res->Fetch()))
	{
		$arFields = array(
			"LID"				=> $site_id, 
			"ACTIVE"			=> "Y",
			"SORT"				=> 100,
			"DEF"				=> "N",
			"NAME"				=> GetMessage("wiz_site_personal_name"),
			"DIR"				=> COption::GetOptionString("main", "site_personal_folder"),
			"FORMAT_DATE"		=> "DD.MM.YYYY",
			"FORMAT_DATETIME"	=> "DD.MM.YYYY HH:MI:SS",
			"CHARSET"			=> (defined("BX_UTF") ? "UTF-8" : "windows-1251"),
			"SITE_NAME"			=> GetMessage("wiz_site_personal_name"),
			"SERVER_NAME"		=> $_SERVER["SERVER_NAME"],
			"EMAIL"				=> COption::GetOptionString("main", "email_from"),
			"LANGUAGE_ID"		=> LANGUAGE_ID,
			"DOC_ROOT"			=> "",
		);
		$obSite = new CSite;
		$result = $obSite->Add($arFields);
		if ($result)
		{
			COption::SetOptionString("main", "site_personal_create", "N"); 
		}
		else 
		{
			echo $obSite->LAST_ERROR; 
			die(); 
		}
	}
}

COption::SetOptionString("main", "new_user_registration", "N"); 
?>
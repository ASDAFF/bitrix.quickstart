<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<?
$action = htmlspecialchars($_REQUEST['action']);

if ($action == 'getChildSections')
{
	$parentSectionId = intval($_REQUEST['id']);
	
	$arFilter = Array("IBLOCK_CODE" => "departments", "SECTION_ID" => $parentSectionId);
	$db_list = CIBlockSection::GetList(Array($by=>$order), $arFilter, true);
	
	
	while($ar_result = $db_list->GetNext())
	{
		$arFilterSubsection = Array("IBLOCK_CODE" => "departments", "SECTION_ID" => $ar_result["ID"]);
		$arFilterSubsectionResult = CIBlockSection::GetList(Array($by=>$order), $arFilterSubsection, false);
		if ($arFilterSubsectionResult->Fetch() == false)
		{
			$isFolder = 0;
		}
		else
		{
		    $isFolder = 1;
		}
		
		$arResult[] = "{id:".$ar_result['ID'].", title:".'\''.$ar_result['NAME'].'\''.", isFolder:".$isFolder."}";
	}
	
	echo '[';
		echo implode(',', $arResult);
	echo ']';
}
elseif ($action == 'getNumbersBySections')
{
	$arraySections = $_REQUEST['selectedSections'];
	
	$arFilter = array("IBLOCK_CODE" => "departments", "ID" => $arraySections); 
	$cdbDepartments = CIBlockSection::GetList(array("SORT"=>"ASC"), $arFilter);
	
	while($section = $cdbDepartments->Fetch())
	{
		$arSections[$section['ID']] = $section['NAME'];
	}
					
	$filter = array("UF_DEPARTMENT" => $arraySections);
	$defUserProperty = COption::GetOptionString('rarus.sms4b', 'user_property_phone', '', SITE_ID);
	if (empty($defUserProperty))
	{
		$defUserProperty = "PERSONAL_MOBILE";
	}
	$rsUsers = CUser::GetList(($by="LAST_NAME"), ($order="asc"), $filter, array("SELECT"=>array("UF_*"))); // selecting users
	
	while($rsUser = $rsUsers->Fetch())
	{
		if (!empty($rsUser[$defUserProperty]))
		{
			$destination[] = "{phone:"."'".$rsUser[$defUserProperty]. "'" . 
			", name: ". "'". $rsUser['NAME']."'" . 
			", secondName:" . "'". $rsUser['LAST_NAME'] . "'" .
			", department:" . "'". $arSections[$rsUser['UF_DEPARTMENT'][0]] . "'" . 
			"}";
		}
	}
	echo '[';
		echo implode(',', $destination);
	echo ']';
		
}

function GetIBlockSectionChildren($arSections)
{
	if (!is_array($arSections))
		$arSections = array($arSections);
	
	$dbRes = CIBlockSection::GetList(array('LEFT_MARGIN' => 'asc'), array('ID' => $arSections));
	
	$arChildren = array();
	while ($arSection = $dbRes->Fetch())
	{
		if ($arSection['RIGHT_MARGIN']-$arSection['LEFT_MARGIN'] > 1 && !in_array($arSection['ID'], $arChildren))
		{
			$dbChildren = CIBlockSection::GetList(
				array('id' => 'asc'), 
				array(
					'IBLOCK_ID' => $arSection['IBLOCK_ID'],
					'ACTIVE' => 'Y',
					'>LEFT_BORDER' => $arSection['LEFT_MARGIN'], 
					'<RIGHT_BORDER'=>$arSection['RIGHT_MARGIN']
				)
			);
			while ($arChild = $dbChildren->Fetch())
			{
				$arChildren[] = $arChild['ID'];
			}
		}
	}
	return array_unique(array_merge($arSections, $arChildren));
}
?>
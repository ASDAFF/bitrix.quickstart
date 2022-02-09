<?
$GLOBALS["SOCNET_LOG_DESTINATION"] = Array();
class CSocNetLogDestination
{
	public static function GetLastUser()
	{
		global $USER;

		if(!isset($GLOBALS["SOCNET_LOG_DESTINATION"]["GetLastUser"][$USER->GetID()]))
		{
			$arLastSelected = CUserOptions::GetOption("socialnetwork", "log_destination", array());
			if (is_array($arLastSelected) && strlen($arLastSelected['users']) > 0)
			{
				$arLastSelected = array_reverse(CUtil::JsObjectToPhp($arLastSelected['users']));
			}
			else
				$arLastSelected = array();

			if (is_array($arLastSelected))
			{
				if (!isset($arLastSelected[$USER->GetID()]))
					$arLastSelected['U'.$USER->GetID()] = 'U'.$USER->GetID();
			}
			else
			{
				$arLastSelected['U'.$USER->GetID()] = 'U'.$USER->GetID();
			}

			$count = 0;
			$arUsers = Array();
			foreach ($arLastSelected as $userId)
			{
				if ($count < 5)
					$count++;
				else
					break;

				$arUsers[$userId] = $userId;
			}
			$GLOBALS["SOCNET_LOG_DESTINATION"]["GetLastUser"][$USER->GetID()] = array_reverse($arUsers);
		}

		return $GLOBALS["SOCNET_LOG_DESTINATION"]["GetLastUser"][$USER->GetID()];
	}

	public static function GetLastSocnetGroup()
	{
		$arLastSelected = CUserOptions::GetOption("socialnetwork", "log_destination", array());
		if (is_array($arLastSelected) && strlen($arLastSelected['sonetgroups']) > 0)
		{
			$arLastSelected = array_reverse(CUtil::JsObjectToPhp($arLastSelected['sonetgroups']));
		}
		else
			$arLastSelected = array();

		$count = 0;
		$arSocnetGroups = Array();
		foreach ($arLastSelected as $sgId)
		{
			if ($count <= 4)
				$count++;
			else
				break;

			$arSocnetGroups[$sgId] = $sgId;
		}
		return array_reverse($arSocnetGroups);
	}

	public static function GetLastDepartment()
	{
		$arLastSelected = CUserOptions::GetOption("socialnetwork", "log_destination", array());
		if (is_array($arLastSelected) && strlen($arLastSelected['department']) > 0)
		{
			$arLastSelected = array_reverse(CUtil::JsObjectToPhp($arLastSelected['department']));
		}
		else
			$arLastSelected = array();

		$count = 0;
		$arDepartment = Array();
		foreach ($arLastSelected as $depId)
		{
			if ($count < 4)
				$count++;
			else
				break;

			$arDepartment[$depId] = $depId;
		}

		return array_reverse($arDepartment);
	}

	public static function GetStucture($arParams = Array())
	{
		global $USER;

		$bIntranetEnable = false;
		if(IsModuleInstalled('intranet') && CModule::IncludeModule('iblock'))
			$bIntranetEnable = true;

		$arDepartment = Array();
		$arDepartmentRelation = Array();
		if($bIntranetEnable)
		{
			if (!(CModule::IncludeModule('extranet') && !CExtranet::IsIntranetUser()))
			{
				if(($iblock_id = COption::GetOptionInt('intranet', 'iblock_structure', 0)) > 0)
				{
					global $CACHE_MANAGER;

					$cache_id = 'sonet_structure_'.$iblock_id;
					$obCache = new CPHPCache;
					$cache_dir = '/sonet/structure';

					if($obCache->InitCache($ttl, $cache_id, $cache_dir))
					{
						$tmpVal = $obCache->GetVars();
						$arDepartment = $tmpVal['DEPARTMENT'];
						$arDepartmentRelation = $tmpVal['DEPARTMENT_RELATION'];
						unset($tmpVal);
					}
					else
					{
						$CACHE_MANAGER->StartTagCache($cache_dir);

						$arDepartmentRelationTmp = Array();
						$dbRes = CIBlockSection::GetTreeList(Array("IBLOCK_ID"=>$iblock_id));
						while ($ar = $dbRes->Fetch())
						{
							$iblockSectionID = 'DR'.intval($ar['IBLOCK_SECTION_ID']);
							if (!is_array($arDepartmentRelationTmp[$iblockSectionID]))
								$arDepartmentRelationTmp[$iblockSectionID] = array('DR'.$ar['ID']);
							else
								$arDepartmentRelationTmp[$iblockSectionID][] = 'DR'.$ar['ID'];

							$arDepartment['DR'.$ar['ID']] = Array('id' => 'DR'.$ar['ID'], 'entityId' => $ar["ID"], 'name' => htmlspecialcharsbx($ar['NAME']));
						}
						$arDepartmentRelation = self::GetTreeList('DR0', $arDepartmentRelationTmp);

						$CACHE_MANAGER->RegisterTag('iblock_id_'.$iblock_id);
						$CACHE_MANAGER->EndTagCache();

						if($obCache->StartDataCache())
						{
							$obCache->EndDataCache(array(
								'DEPARTMENT' => $arDepartment,
								'DEPARTMENT_RELATION' => $arDepartmentRelation,
							));
						}
					}
					unset($obCache);
				}
			}
		}
		return Array('department' => $arDepartment, 'department_relation' => $arDepartmentRelation);
	}

	public static function GetExtranetUser()
	{
		global $USER;

		if(!isset($GLOBALS["SOCNET_LOG_DESTINATION"]["GetExtranetUser"][$USER->GetID()]))
		{
			$arUsers = Array();
			$arExtParams = Array("FIELDS" => Array("ID", "LAST_NAME", "NAME", "SECOND_NAME", "LOGIN", "PERSONAL_PHOTO", "WORK_POSITION", "PERSONAL_PROFESSION"));

			if (CModule::IncludeModule('extranet') && !CExtranet::IsIntranetUser())
			{
				$arSelect = Array($USER->GetId());
				$rsGroups = CSocNetUserToGroup::GetList(
					array("GROUP_NAME" => "ASC"),
					array(
						"USER_ID" => $USER->GetID(),
						"<=ROLE" => SONET_ROLES_USER,
						"GROUP_SITE_ID" => SITE_ID,
						"GROUP_ACTIVE" => "Y",
						"!GROUP_CLOSED" => "Y"
					),
					false,
					array("nPageSize" => 500, "bDescPageNumbering" => false),
					array("ID", "GROUP_ID")
				);
				while($arGroup = $rsGroups->Fetch())
				{
					$arGroupTmp = array(
						"id" => $arGroup["GROUP_ID"],
						"entityId" => $arGroup["GROUP_ID"]
					);
					$arSocnetGroups[$arGroup["GROUP_ID"]] = $arGroupTmp;
				}

				if (count($arSocnetGroups) > 0)
				{
					$arUserSocNetGroups = Array();
					foreach ($arSocnetGroups as $groupId => $ar)
						$arUserSocNetGroups[] = $groupId;

					$dbUsersInGroup = CSocNetUserToGroup::GetList(
						array(),
						array(
							"GROUP_ID" => $arUserSocNetGroups,
							"<=ROLE" => SONET_ROLES_USER,
							"USER_ACTIVE" => "Y"
						),
						false,
						false,
						array("ID", "USER_ID", "GROUP_ID")
					);
					while ($ar = $dbUsersInGroup->GetNext(true, false))
						$arSelect[] = intval($ar["USER_ID"]);
				}
				$arFilter['ID'] = implode('|', $arSelect);
			}

			$arUsers = Array();	
			$dbUsers = CUser::GetList(($sort_by = Array('last_name'=>'asc', 'IS_ONLINE'=>'desc')), ($dummy=''), $arFilter, $arExtParams);
			while ($arUser = $dbUsers->GetNext())
			{
				$sName = trim(CUser::FormatName(CSite::GetNameFormat(), $arUser));

				if (empty($sName))
					$sName = $arUser["~LOGIN"];

				$arFileTmp = CFile::ResizeImageGet(
					$arUser["PERSONAL_PHOTO"],
					array('width' => 32, 'height' => 32),
					BX_RESIZE_IMAGE_EXACT,
					false
				);

				$arUsers['U'.$arUser["ID"]] = Array(
					'id' => 'U'.$arUser["ID"],
					'entityId' => $arUser["ID"],
					'name' => $sName,
					'avatar' => empty($arFileTmp['src'])? '': $arFileTmp['src'],
					'desc' => $arUser['WORK_POSITION'] ? $arUser['WORK_POSITION'] : ($arUser['PERSONAL_PROFESSION']?$arUser['PERSONAL_PROFESSION']:'&nbsp;'),
				);			
			}
			$GLOBALS["SOCNET_LOG_DESTINATION"]["GetExtranetUser"][$USER->GetID()] = $arUsers;
		}
		return $GLOBALS["SOCNET_LOG_DESTINATION"]["GetExtranetUser"][$USER->GetID()];
	}

	public static function GetUsers($arParams = Array())
	{
		global $USER;

		if(!isset($GLOBALS["SOCNET_LOG_DESTINATION"]["GetUsers"][$USER->GetID()]))
		{
			$arFilter = Array('ACTIVE' => 'Y');
			$arExtParams = Array("FIELDS" => Array("ID", "LAST_NAME", "NAME", "SECOND_NAME", "LOGIN", "PERSONAL_PHOTO", "WORK_POSITION", "PERSONAL_PROFESSION"));

			if (isset($arParams['id']))
			{
				if (empty($arParams['id']))
				{
					$arFilter['ID'] = $USER->GetId();
				}
				else
				{
					$arSelect = Array($USER->GetId());
					foreach ($arParams['id'] as $value)
						$arSelect[] = intval($value);
					$arFilter['ID'] = implode('|', $arSelect);
				}		
			} 
			elseif (isset($arParams['deportament_id']))
			{
				$arFilter['UF_DEPARTMENT'] = intval($arParams['deportament_id']);
				$arExtParams['SELECT'] = array('UF_DEPARTMENT');
			}

			$arUsers = Array();	
			$dbUsers = CUser::GetList(($sort_by = Array('last_name'=>'asc', 'IS_ONLINE'=>'desc')), ($dummy=''), $arFilter, $arExtParams);
			while ($arUser = $dbUsers->GetNext())
			{
				$sName = trim(CUser::FormatName(empty($arParams["NAME_TEMPLATE"]) ? CSite::GetNameFormat(false) : $arParams["NAME_TEMPLATE"], $arUser));

				if (empty($sName))
					$sName = $arUser["~LOGIN"];

				$arFileTmp = CFile::ResizeImageGet(
					$arUser["PERSONAL_PHOTO"],
					array('width' => 32, 'height' => 32),
					BX_RESIZE_IMAGE_EXACT,
					false
				);

				$arUsers['U'.$arUser["ID"]] = Array(
					'id' => 'U'.$arUser["ID"],
					'entityId' => $arUser["ID"],
					'name' => $sName,
					'avatar' => empty($arFileTmp['src'])? '': $arFileTmp['src'],
					'desc' => $arUser['WORK_POSITION'] ? $arUser['WORK_POSITION'] : ($arUser['PERSONAL_PROFESSION']?$arUser['PERSONAL_PROFESSION']:'&nbsp;'),
				);			
			}
			$GLOBALS["SOCNET_LOG_DESTINATION"]["GetUsers"][$USER->GetID()] = $arUsers;
		}

		return $GLOBALS["SOCNET_LOG_DESTINATION"]["GetUsers"][$USER->GetID()];
	}

	public static function SearchUsers($search, $nameTemplate = "")
	{
		CUtil::JSPostUnescape();

		$bIntranetEnable = false;
		if(IsModuleInstalled('intranet'))
			$bIntranetEnable = true;

		$arUsers = array();
		$arTmpUsers = array();
		$arExtranetTestUsers = array();
		
		$search = trim($search);
		if (strlen($search) <= 0)
			return $arUsers;
	
		$strUserIDs = '';
		$arUserSearch = explode(' ', urldecode($search));
		if (empty($arUserSearch))
			return $arUsers;
		
		$dbRes = CUser::SearchUserByName($arUserSearch, '', true);
		while ($arRes = $dbRes->Fetch())
			$strUserIDs .= ($strUserIDs == '' ? '' : '|').$arRes['ID'];

		$arFilter = array('ACTIVE' => 'Y', 'NAME_SEARCH' => $search, 'ID' => $strUserIDs);

		$arExtParams = Array("FIELDS" => Array("ID", "LAST_NAME", "NAME", "SECOND_NAME", "LOGIN", "PERSONAL_PHOTO", "WORK_POSITION", "PERSONAL_PROFESSION"));
		if ($bIntranetEnable)
			$arExtParams['SELECT'] = array('UF_DEPARTMENT');
		$dbUsers = CUser::GetList(($sort_by = Array('last_name'=>'asc', 'IS_ONLINE'=>'desc')), ($dummy=''), $arFilter, $arExtParams);
		$dbUsers->NavStart(20);
		while ($arUser = $dbUsers->NavNext(false))
		{
			$arTmpUsers[$arUser["ID"]] = $arUser;
			if($bIntranetEnable && (!is_array($arUser["UF_DEPARTMENT"]) || empty($arUser["UF_DEPARTMENT"])))
				$arExtranetTestUsers[$arUser["ID"]] = $arUser["ID"];
		}

		if (!empty($arExtranetTestUsers) && CModule::IncludeModule('extranet') && CExtranet::IsIntranetUser())
		{
			global $USER;
			$arUserSocNetGroups	= Array();
			$rsGroups = CSocNetUserToGroup::GetList(
				array("GROUP_NAME" => "ASC"),
				array(
					"USER_ID" => $USER->GetID(),
					"<=ROLE" => SONET_ROLES_USER,
					"GROUP_SITE_ID" => SITE_ID,
					"GROUP_ACTIVE" => "Y",
					"!GROUP_CLOSED" => "Y"
				),
				false,
				array("nPageSize" => 500, "bDescPageNumbering" => false),
				array("ID", "GROUP_ID")
			);
			while($arGroup = $rsGroups->Fetch())
				$arUserSocNetGroups[] = $arGroup["GROUP_ID"];

			if (count($arUserSocNetGroups) > 0)
			{
				$dbUsersInGroup = CSocNetUserToGroup::GetList(
					array(),
					array(
						"GROUP_ID" => $arUserSocNetGroups,
						"<=ROLE" => SONET_ROLES_USER,
						"USER_ACTIVE" => "Y"
					),
					false,
					false,
					array("ID", "USER_ID", "GROUP_ID")
				);
				while ($ar = $dbUsersInGroup->GetNext(true, false))
					$arSelect[$ar["USER_ID"]] = $ar["USER_ID"];

				foreach ($arExtranetTestUsers as $userId) 
				{					
					if (!isset($arSelect[$userId]))
						unset($arTmpUsers[$userId]);
				}
			} 
			else 
			{
				foreach ($arExtranetTestUsers as $userId) 
					unset($arTmpUsers[$userId]);
			}
		}

		foreach ($arTmpUsers as $arUser) 
		{
			$sName = CUser::FormatName(empty($nameTemplate) ? CSite::GetNameFormat(false) : $nameTemplate, $arUser, true);
				
			$arFileTmp = CFile::ResizeImageGet(
				$arUser["PERSONAL_PHOTO"],
				array('width' => 32, 'height' => 32),
				BX_RESIZE_IMAGE_EXACT,
				false
			);

			$arUsers['U'.$arUser["ID"]] = Array(
				'id' => 'U'.$arUser["ID"],
				'entityId' => $arUser["ID"],
				'name' => $sName,
				'avatar' => empty($arFileTmp['src'])? '': $arFileTmp['src'],
				'desc' => $arUser['WORK_POSITION'] ? $arUser['WORK_POSITION'] : ($arUser['PERSONAL_PROFESSION']?$arUser['PERSONAL_PROFESSION']:'&nbsp;'),
			);
		}
		return $arUsers;
	}

	public static function GetSocnetGroup($arParams = Array())
	{
		global $USER;

		$arSocnetGroups = array();
		$arSelect = Array();
		if (isset($arParams['id']))
		{
			if (empty($arParams['id']))
				return $arSocnetGroups;
			else
				foreach ($arParams['id'] as $value)
					$arSelect[] = intval($value);
		}
		
		$arSocnetGroupsTmp = array();
		$rsGroups = CSocNetUserToGroup::GetList(
			array("GROUP_NAME" => "ASC"),
			array(
				"USER_ID" => $USER->GetID(),
				"ID" => $arSelect,
				"<=ROLE" => SONET_ROLES_USER,
				"GROUP_SITE_ID" => SITE_ID,
				"GROUP_ACTIVE" => "Y"
			),
			false,
			array("nPageSize" => 500, "bDescPageNumbering" => false),
			array("ID", "GROUP_ID", "GROUP_NAME", "GROUP_DESCRIPTION", "GROUP_IMAGE_ID")
		);
		while($arGroup = $rsGroups->Fetch())
		{
			$arGroupTmp = array(
				"id" => $arGroup["GROUP_ID"],
				"entityId" => $arGroup["GROUP_ID"],
				"name" => htmlspecialcharsbx($arGroup["GROUP_NAME"]),
				"desc" => htmlspecialcharsbx($arGroup["GROUP_DESCRIPTION"])
			);
			if($arGroup["GROUP_IMAGE_ID"])
			{
				$imageFile = CFile::GetFileArray($arGroup["GROUP_IMAGE_ID"]);
				if ($imageFile !== false)
				{
					$arFileTmp = CFile::ResizeImageGet(
						$imageFile,
						array("width" => 30, "height" => 30),
						BX_RESIZE_IMAGE_PROPORTIONAL,
						false
					);
					$arGroupTmp["avatar"] = $arFileTmp["src"];
				}
			}
			$arSocnetGroupsTmp[$arGroupTmp['id']] = $arGroupTmp;
		}
		if (isset($arParams['features']) && !empty($arParams['features']))
			self::GetSocnetGroupFilteredByFeaturePerms($arSocnetGroupsTmp, $arParams['features']);

		foreach ($arSocnetGroupsTmp as $key => $value)
		{
			$value['id'] = 'SG'.$value['id'];
			$arSocnetGroups[$value['id']] = $value;
		}

		return $arSocnetGroups;
	}

	public static function GetTreeList($id, $relation)
	{
		$arRelations = Array();
		foreach ($relation[$id] as $relId)
		{
			$arItems = Array();
			if (isset($relation[$relId]) && !empty($relation[$relId]))
				$arItems = self::GetTreeList($relId, $relation);

			$arRelations[$relId] = Array('id'=>$relId, 'type' => 'category', 'items' => $arItems);
		}

		return $arRelations;
	}


	private static function GetSocnetGroupFilteredByFeaturePerms(&$arGroups, $arFeaturePerms)
	{
		$arGroupsIDs = array();
		foreach($arGroups as $value)
		{
			$arGroupsIDs[] = $value["id"];
		}

		if (sizeof($arGroupsIDs) > 0)
		{
			$feature = $arFeaturePerms[0];
			$operations = $arFeaturePerms[1];
			if (!is_array($operations))
				$operations = explode(",", $operations);
			$arGroupsPerms = array();
			foreach($operations as $operation)
			{
				$tmpOps = CSocNetFeaturesPerms::CurrentUserCanPerformOperation(SONET_ENTITY_GROUP, $arGroupsIDs, $feature, $operation);
				foreach($tmpOps as $key=>$val)
					if (!$arGroupsPerms[$key])
						$arGroupsPerms[$key] = $val;
			}
			$arGroupsActive = CSocNetFeatures::IsActiveFeature(SONET_ENTITY_GROUP, $arGroupsIDs, $arFeaturePerms[0]);
			foreach ($arGroups as $key=>$group)
				if (!$arGroupsActive[$group["id"]] || !$arGroupsPerms[$group["id"]])
					unset($arGroups[$key]);
		}
	}
}
?>
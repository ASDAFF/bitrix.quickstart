<?
IncludeModuleLangFile(__FILE__);

class DSocialMediaPosterShedule {

	static $EXECUTE_FUNCTION = "Execute";
	static $AGENT_INTERVAL = 600;
	static $AGENT_EXECUTE_MAX_CNT = 10;
	private static $USERFIELD_NAME = "UF_SOCNET_EXT_ID";

	function Add($ID) {

		global $USER;

		$ID = intval($ID);
		if ($ID <= 0)
			return false;

		$rsAgent = CAgent::GetList(Array("ID" => "DESC"), array("MODULE_ID" => DSocialMediaPoster::$MODULE_ID, "NAME" => __CLASS__."::".self::$EXECUTE_FUNCTION."(".$ID));

		while ($resAgent = $rsAgent->GetNext())
			CAgent::Delete($resAgent["ID"]);

		$agentExecuteMaxCnt = COption::GetOptionInt(DSocialMediaPoster::$MODULE_ID, "AGENT_EXECUTE_MAX_CNT", self::$AGENT_EXECUTE_MAX_CNT);
		$agentInterval = COption::GetOptionInt(DSocialMediaPoster::$MODULE_ID, "AGENT_INTERVAL", self::$AGENT_INTERVAL);
		$nextExec = $dateCheck = ConvertTimeStamp(AddToTimeStamp(array("SS" => $agentInterval)), "FULL");

		if (!is_object($USER))
			$USER = new CUser;

		$cnt = intval($cnt);
		$ID = intval($ID);

		if ($ID <= 0)
			return false;

		if (!CModule::IncludeModule("iblock"))
			return false;

		$resElement = CIBlockElement::GetByID($ID)->GetNext();
		if (intval($resElement["ID"]) <= 0)
			return false;

		$obListEntity = DSocialPosterEntityFactory::GetEntityList();
		for($obListEntity->rewind(); $obListEntity->valid(); $obListEntity->next())
		{
			$rsSettings = DSocialPosterEntityFactory::GetIBlockSettings($resElement["IBLOCK_ID"], $resElement["ID"], $obListEntity->current());
			for($rsSettings->rewind(); $rsSettings->valid(); $rsSettings->next())
			{

				CAgent::AddAgent(
					__CLASS__."::".self::$EXECUTE_FUNCTION."(".$ID.", \"".$obListEntity->current()->GetID()."\", ".$agentExecuteMaxCnt.");",
					DSocialMediaPoster::$MODULE_ID,
					"N",
					$agentInterval,
					$dateCheck,
					"Y",
					$nextExec
				);

			}
		}

	}

	function Execute($ID, $entityID, $cnt) {
		global $USER;

		if (!is_object($USER))
			$USER = new CUser;

		$cnt = intval($cnt);
		$ID = intval($ID);

		do {

			if ($cnt <= 0)
				return false;

			if ($ID <= 0)
				return false;

			if (empty($entityID))
				return false;

			if (!CModule::IncludeModule("iblock"))
				return false;

			$rsElementObj = CIBlockElement::GetByID($ID);

			if ($rsElement = $rsElementObj->GetNextElement()) {
				$resElement = $rsElement->GetFields();
				$resElement["PROPERTIES"] = $rsElement->GetProperties();
			}
			else
				// LOG
				return false;

			$resElement["SECTION_NAME"] = $resElement["SECTION_DESCRIPTION"] = "";
			if (intval($resElement["IBLOCK_SECTION_ID"]) > 0) {
				$resSection = CIBlockSection::GetByID($resElement["IBLOCK_SECTION_ID"])->Fetch();
				$resElement["SECTION_NAME"] = $resSection["NAME"];
				$resElement["SECTION_DESCRIPTION"] = $resSection["DESCRIPTION"];
			}

			if (intval($resElement["ID"]) <= 0)
				return false;

			if ($resElement["WF_NEW"] == "Y")
				break;

			if ($resElement["BP_PUBLISHED"] != "Y")
				break;

			if ($resElement["WF_STATUS_ID"] != 1)
				break;

			if ($resElement["ACTIVE"] != "Y")
				break;

			$resElement["HOST"] = DSocialMediaPoster::GetHost($resElement["IBLOCK_ID"]);

			$events = GetModuleEvents(DSocialMediaPoster::$MODULE_ID, "OnBuildPostParamsPrepare");
			while($arEvent = $events->Fetch())
				$resElement = ExecuteModuleEvent($arEvent, $resElement);

			if (empty($resElement["HOST"]))
				// LOG
				break;

			$resElement["DETAIL_PAGE_URL"] = urldecode($resElement["DETAIL_PAGE_URL"]);

			$obListEntity = DSocialPosterEntityFactory::GetEntityList();
			$obEntity = $obListEntity->GetByID($entityID);

			$rsSettings = DSocialPosterEntityFactory::GetIBlockSettings($resElement["IBLOCK_ID"], $resElement["ID"], $obEntity);

			for($rsSettings->rewind(); $rsSettings->valid(); $rsSettings->next())
			{
				if (
					($rsSettings->current()->CHECK_DATES == "Y" || !$rsSettings->current()->HasParam("CHECK_DATES"))
					&&
					!(
						(empty($resElement["ACTIVE_FROM"]) || (!empty($resElement["ACTIVE_FROM"]) && $GLOBALS["DB"]->CompareDates($resElement["ACTIVE_FROM"], ConvertTimeStamp(time(), "FULL")) <= 0))
						&&
						(empty($resElement["ACTIVE_TO"]) || (!empty($resElement["ACTIVE_TO"]) && $GLOBALS["DB"]->CompareDates($resElement["ACTIVE_TO"], ConvertTimeStamp(time(), "FULL")) >= 0))
					)
				)
					break 2;

				switch($rsSettings->current()->POST_TYPE) {
					case "PHOTO":
						$sCreatedAlbum = "";
						$sectionParams = self::GetEntityPostParams($obEntity, $rsSettings->current(), $resElement, true);
						$postResult = $obEntity->PostPhoto(DSocialPosterEntityFactory::GetConnection($resElement["IBLOCK_ID"]), $rsSettings->current(), $sectionParams, self::GetEntityPostParams($obEntity, $rsSettings->current(), $resElement), $sCreatedAlbum);
						if(!empty($sCreatedAlbum))
							self::MarkIBlockSection($obEntity, $sectionParams->GetID(), $resElement["IBLOCK_ID"], $sCreatedAlbum);
					break;
					case "VIDEO":
						$sCreatedAlbum = "";
						$sectionParams = self::GetEntityPostParams($obEntity, $rsSettings->current(), $resElement, true);
						$postResult = $obEntity->PostVideo(DSocialPosterEntityFactory::GetConnection($resElement["IBLOCK_ID"]), $rsSettings->current(), $sectionParams, self::GetEntityPostParams($obEntity, $rsSettings->current(), $resElement), $sCreatedAlbum);
						if(!empty($sCreatedAlbum))
							self::MarkIBlockSection($obEntity, $sectionParams->GetID(), $resElement["IBLOCK_ID"], $sCreatedAlbum);
					break;
					default:
						$postResult = $obEntity->PostMessage(DSocialPosterEntityFactory::GetConnection($resElement["IBLOCK_ID"]), $rsSettings->current(), self::GetEntityPostParams($obEntity, $rsSettings->current(), $resElement));
					break;
				}

				if ($postResult === true)
					return false;
				else
					//LOG
					break;

			}
		}
		while (false);

		if (isset($postResult) && --$cnt <= 0)
			return false;

		return __CLASS__."::".__FUNCTION__."(".$ID.", \"".$entityID."\", ".$cnt.");";

	}

	protected static function GetIBlockSection($sid, $iblock_id)
	{
		if (!CModule::IncludeModule("iblock"))
			return false;

		$rsSection = CIBlockSection::GetList(array(), array("ID" => $sid, "IBLOCK_ID" => $iblock_id), false, array(self::$USERFIELD_NAME));
		if($arSection = $rsSection->Fetch())
		{
			$arLabels = array(LANGUAGE_ID => GetMessage("SOCIALMEDIAPOSTER_SHEDULE_UF_EXT_TITLE"));
			$arUserField = array(
				"ENTITY_ID" => "IBLOCK_".$arSection["IBLOCK_ID"]."_SECTION",
				"FIELD_NAME" => self::$USERFIELD_NAME,
				"USER_TYPE_ID" => "string",
				"XML_ID" => "",
				"SORT" => 500,
				"MULTIPLE" => "Y",
				"MANDATORY" => "N",
				"SHOW_FILTER" => "N",
				"SHOW_IN_LIST" => "Y",
				"EDIT_IN_LIST" => "N",
				"IS_SEARCHABLE" => "N",
				"SETTINGS" => array(),
				"EDIT_FORM_LABEL" => $arLabels,
				"LIST_COLUMN_LABEL" => $arLabels,
				"LIST_FILTER_LABEL" => $arLabels,
				"ERROR_MESSAGE" => array(),
				"HELP_MESSAGE" => array(),
			);
			if(!is_set($arSection, $arUserField["FIELD_NAME"]))
			{
				$obUserField  = new CUserTypeEntity();
				if(!$obUserField->Add($arUserField))
					throw new Exception("error create userfield");

				$arSection[$arUserField["FIELD_NAME"]] = array();
			}
			return $arSection;
		}
		return false;
	}

	protected static function MarkIBlockSection(DSocialPosterEntity $obEntity, $sid, $iblock_id, $extID)
	{
		$arSection = self::GetIBlockSection($sid, $iblock_id);

		$ar = $arSection[self::$USERFIELD_NAME];
		if(!is_array($ar)) $ar = array();

		$arIDs = array(); $bFound = false;
		foreach($ar as $key => $val)
		{
			$arr = explode(":", $val);
			if(!$bFound && (count($arr) == 2) && ($obEntity->GetID() == $arr[0]))
			{
				$bFound = true;
				$val = join(":", array($obEntity->GetID(), $extID));
			}
			$arIDs[$key] = $val;
		}
		if(!$bFound)
			$arIDs[] = join(":", array($obEntity->GetID(), $extID));

		$ob = new CIBlockSection();
		return $ob->Update($arSection["ID"], array(self::$USERFIELD_NAME => $arIDs), false, false, false);
	}

	protected static function GetIBlockSectionExtID(array $arSection, DSocialPosterEntity $obEntity)
	{
		$result = "";
		$ar = $arSection[self::$USERFIELD_NAME];
		if(!is_array($ar)) $ar = array();

		foreach($ar as $key => $val)
		{
			$arr = explode(":", $val);
			if((count($arr) == 2) && ($obEntity->GetID() == $arr[0]))
				$result = $arr[1];
		}
		return $result;
	}

	protected static function GetEntityPostParams(DSocialPosterEntity $obEntity, DSocialPosterParams $settings, array $arIBlockItem, $bReturnSection=false)
	{
		$sExtId = $sDetailText = $_sImgUrl = $sImgUrl = "";

		$arSection = null;
		if($bReturnSection)
		{
			if($arSection = self::GetIBlockSection(intval($arIBlockItem["IBLOCK_SECTION_ID"]), $arIBlockItem["IBLOCK_ID"]))
			{
				$sUrl = $arIBlockItem["HOST"].$arSection["SECTION_PAGE_URL"];
				$sName = strip_tags($arSection["NAME"]);
				$sPreviewText = strip_tags($arSection["DESCRIPTION"]);
				$sExtId = self::GetIBlockSectionExtID($arSection, $obEntity);

				if (!empty($arIBlockItem["DETAIL_PICTURE"])) {
					$_sImgUrl = $arIBlockItem["DETAIL_PICTURE"];
				}
				elseif (!empty($arIBlockItem["PICTURE"])) {
					$_sImgUrl = $arIBlockItem["PICTURE"];
				}
			}
		}
		else
		{
			$sUrl = $arIBlockItem["HOST"].$arIBlockItem["DETAIL_PAGE_URL"];
			$sName = strip_tags($arIBlockItem["NAME"]);
			$sPreviewText = strip_tags($arIBlockItem["PREVIEW_TEXT"]);
			$sDetailText = strip_tags($arIBlockItem["DETAIL_TEXT"]);

			if (!empty($arIBlockItem["DETAIL_PICTURE"])) {
				$_sImgUrl = $arIBlockItem["DETAIL_PICTURE"];
			}
			elseif (!empty($arIBlockItem["PREVIEW_PICTURE"])) {
				$_sImgUrl = $arIBlockItem["PREVIEW_PICTURE"];
			}
		}

		if ($_sImgUrl > 0 && ($arFile = CFile::GetFileArray($_sImgUrl)))
		{
			$sImgUrl = $arFile["SRC"];
			if (substr($sImgUrl, 0, 1) == "/")
				$sImgUrl = $arIBlockItem["HOST"].$sImgUrl;
		}

		$events = GetModuleEvents(DSocialMediaPoster::$MODULE_ID, "OnBuildPostParams");
		while($arEvent = $events->Fetch()) {
			/**
			 *  AddEventHandler("defa.socialmediaposter", "OnBuildPostParams", "OnBuildPostParams");
			 *  function OnBuildPostParams($oEntity, $aElement, $sUrl, $sName, $sPreviewText, $sDetailText, $sImgUrl) {}
			 */
			ExecuteModuleEvent($arEvent, $obEntity, $arIBlockItem, $sUrl, $sName, $sPreviewText, $sDetailText, $sImgUrl);
		}

		if($bReturnSection && is_array($arSection)) {
			return DSocialPosterEntityFactory::GetPostParams(
				$settings, $arSection, $arSection["ID"], $sUrl, $sName, $sPreviewText, $sDetailText, $sImgUrl, $sExtId, $obEntity->GetID());

		} elseif ($bReturnSection) {
			return DSocialPosterEntityFactory::GetPostParams($settings);

		} else {
			return DSocialPosterEntityFactory::GetPostParams(
				$settings, $arIBlockItem, $arIBlockItem["ID"], $sUrl, $sName, $sPreviewText, $sDetailText, $sImgUrl, $sExtId, $obEntity->GetID());
		}
	}

}

?>

<?
IncludeModuleLangFile(__FILE__);

class DSocialMediaPosterEvent {

	function OnEventLogGetAuditTypes() {

		$types = array();

		$obListEntity = DSocialPosterEntityFactory::GetEntityList();
		while ($entity = $obListEntity->GetNext())
		{
			$eType = ToUpper($entity->GetID());
			$eName = $entity->GetName();
			$aType = DSocialMediaPosterEventLog::$PREFIX."_".$eType;
			$types[$aType] = "[".$aType."] ".GetMessage(DSocialMediaPosterEventLog::$PREFIX."_LOG", array("#NAME#" => $eName));
		}

		return $types;
	}
}

class DSocialMediaPosterCIBlockEvent {

	function OnBeforeIBlockPropertyAddUpdate(&$arFields) {
		global $APPLICATION;

		$arDescrip = DSocialMediaPosterCIBlockProperty::GetUserTypeDescription();
		if($arFields["PROPERTY_TYPE"] == $arDescrip["PROPERTY_TYPE"] && $arFields["USER_TYPE"] == $arDescrip["USER_TYPE"])
		{

			$cookieFiles = $_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/".DSocialMediaPoster::$MODULE_ID."/tmp/*_".$arFields["IBLOCK_ID"].".txt";
			foreach (glob($cookieFiles) as $file)
				@unlink($file);

			if($arFields["MULTIPLE"] == "Y")
			{
				$APPLICATION->ThrowException(GetMessage("ERROR_IBLOCK_PROP_SOCIALMEDIAPOSTER_IS_MULTIPLE", array("#TYPE#" => $arDescrip["DESCRIPTION"])));
				return false;
			}

			$rsExists = CIBlockProperty::GetList(array(), Array("IBLOCK_ID" => $arFields["IBLOCK_ID"], "ACTIVE" => "Y", "PROPERTY_TYPE" => $arDescrip["PROPERTY_TYPE"], "USER_TYPE" => $arDescrip["USER_TYPE"]));

			$bPropertyExists = $rsExists->SelectedRowsCount() > 0;

			if ($bPropertyExists && $arFields["ID"] <= 0) {
				$APPLICATION->ThrowException(GetMessage("ERROR_IBLOCK_PROP_SOCIALMEDIAPOSTER_PROPERTY_EXISTS", array("#TYPE#" => $arDescrip["DESCRIPTION"])));
				return false;
			}
		}
		
		return true;
	}

	function OnAfterIBlockElementAdd(&$arFields) {

		$rsSettings = DSocialPosterEntityFactory::GetIBlockSettings($arFields["IBLOCK_ID"], $arFields["ID"]); //       
		
		$publishID = $arFields["ID"];

		$bCheckDates = $rsSettings->current() && ($rsSettings->current()->CHECK_DATES == "Y" || !$rsSettings->current()->HasParam("CHECK_DATES"));
		$bPublish = $rsSettings->valid()
					&&
					(
						!$bCheckDates
						||
						(
							$bCheckDates
/*							&&
							(
								empty($arFields["ACTIVE_FROM"])
								||
								(
									!empty($arFields["ACTIVE_FROM"])
									&&
									$GLOBALS["DB"]->CompareDates($arFields["ACTIVE_FROM"], ConvertTimeStamp(time(), "FULL")) < 0
								)
							)*/
							&&
							(
								empty($arFields["ACTIVE_TO"])
								||
								(
									!empty($arFields["ACTIVE_TO"])
									&&
									$GLOBALS["DB"]->CompareDates($arFields["ACTIVE_TO"], ConvertTimeStamp(time(), "FULL")) > 0
								)
							)
						)
					)
					&&
					(
						$arFields["WF"] != "Y"
						||
						(
							$arFields["WF"] == "Y"
							&&
							!empty($arFields["WF_PARENT_ELEMENT_ID"])
						)
					);

		if ($arFields["WF_STATUS_ID"] != 1 && $arFields["WF"] == "Y" && !empty($arFields["WF_PARENT_ELEMENT_ID"])) {
		
			$rs = CIBlockElement::GetById($arFields["WF_PARENT_ELEMENT_ID"]);
			if ($res = $rs->GetNext()) {
				$publishID = $res["ID"];

				if ($res["WF_NEW"] != "Y")
					$bPublish = false;
			}
			else
				$bPublish = false;
		}
		elseif (!empty($arFields["WF_PARENT_ELEMENT_ID"]))
			$publishID = $arFields["WF_PARENT_ELEMENT_ID"];

		$events = GetModuleEvents(DSocialMediaPoster::$MODULE_ID, "OnBeforeSmpAgentAdd");
		while($arEvent = $events->Fetch()) {
			if (ExecuteModuleEvent($arEvent, $arFields) === false)
			{
				$bPublish = false;
				break;
			}
		}

		if ($bPublish)
			DSocialMediaPosterShedule::Add($publishID);
	}	
}

?>
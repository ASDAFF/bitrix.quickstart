<?if(!check_bitrix_sessid()) return;?>
<?
	/*
	if (CModule::IncludeModule("catalog"))
	{
		$dbExtraList = CExtra::GetList(($b = ""), ($o = ""));
		if (!($arExtraList = $dbExtraList->Fetch()))
		{
			CExtra::Add(
					array(
							"NAME" => "Retail price (57%)",
							"PERCENTAGE" => 57.00
						)
				);
		}

		$dbGroupList = CCatalogGroup::GetList(array(), array());
		if (!($arGroupList = $dbGroupList->Fetch()))
		{
			$arLandData = array();

			$dbLangs = CLanguage::GetList(($b = ""), ($o = ""), array("ACTIVE" => "Y"));
			while ($arLangs = $dbLangs->Fetch())
			{
				if ($arLangs["LID"] == "ru")
					$arLandData["ru"] = "Базовая цена";
				else
					$arLandData[$arLangs["LID"]] = "Base price";
			}

			CCatalogGroup::Add(
					array(
							"NAME" => "Base Group",
							"BASE" => "Y",
							"SORT" => 10,
							"USER_LANG" => $arLandData,
							"USER_GROUP" => array(1),
							"USER_GROUP_BUY" => array(1)
						)
				);
		}
	}
	*/

	if($errors===false):
		echo CAdminMessage::ShowNote(GetMessage("MOD_INST_OK"));
	else:
		//for($i=0; $i<count($errors); $i++)
			//$alErrors .= $errors[$i]."<br>";
		echo CAdminMessage::ShowMessage(Array("TYPE"=>"ERROR", "MESSAGE" =>GetMessage("MOD_INST_ERR"), "DETAILS"=>$errors, "HTML"=>true));
	endif;
	?>
	<form action="<?echo $APPLICATION->GetCurPage()?>">
		<input type="hidden" name="lang" value="<?echo LANG?>">
		<input type="submit" name="" value="<?echo GetMessage("MOD_BACK")?>">
	<form>
	<?

?>
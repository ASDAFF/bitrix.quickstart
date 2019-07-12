<?
$iblock_id = "#SOCIALS_ID#";
if (CModule::IncludeModule("iblock")) {
	$db_get = CIBlockElement::GetList(Array("SORT" => "ASC"), Array("IBLOCK_ID" => $iblock_id, "ACTIVE" => "Y", "ACTIVE_DATE" => "Y"), false, false, Array("ID", "NAME", "CODE", "PROPERTY_LINK"));
	while ($ar_get = $db_get->GetNext()) {
		?><a href="<?=$ar_get["PROPERTY_LINK_VALUE"];?>" target="_blank" class="social_button <?=$ar_get["CODE"];?>" title="<?=$ar_get["NAME"];?>"></a><?
	}
}
?>
<?
class CIBlockFindTools
{
	function GetElementID($element_id, $element_code, $section_id, $section_code, $arFilter)
	{
		$element_id = intval($element_id);
		if($element_id > 0)
		{
			return $element_id;
		}
		elseif(strlen($element_code) > 0)
		{
			$arFilter["=CODE"] = $element_code;

			$section_id = intval($section_id);
			if($section_id > 0)
				$arFilter["SECTION_ID"] = $section_id;
			elseif(strlen($section_code) > 0)
				$arFilter["SECTION_CODE"] = $section_code;

			$rsElement = CIBlockElement::GetList(array(), $arFilter, false, false, array("ID"));
			if($arElement = $rsElement->Fetch())
				return intval($arElement["ID"]);
		}
		return 0;
	}

	function GetSectionID($section_id, $section_code, $arFilter)
	{
		$section_id = intval($section_id);
		if($section_id > 0)
		{
			return $section_id;
		}
		elseif(strlen($section_code) > 0)
		{
			$arFilter["=CODE"] = $section_code;

			$rsSection = CIBlockSection::GetList(array(), $arFilter);
			if($arSection = $rsSection->Fetch())
				return intval($arSection["ID"]);
		}
		return 0;
	}
}
?>
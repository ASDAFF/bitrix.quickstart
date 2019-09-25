<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!function_exists("getColumnName"))
{
	function getColumnName($arHeader)
	{
		return (strlen($arHeader["name"]) > 0) ? $arHeader["name"] : GetMessage("SALE_".$arHeader["id"]);
	}
}

if (!function_exists("getColumnId"))
{
	function getColumnId($arHeader)
	{
		return $arHeader["id"];
	}
}

if (!function_exists("getMobileQuantityControl"))
{
	function getMobileQuantityControl($id, $name, $curVal, $maxQuantity, $useFloatQuantity, $ratio, $measureText = "")
	{
		$maxQuantity = doubleval($maxQuantity);
		$ratio = (doubleval($ratio) != 0) ? doubleval($ratio) : 1;
		$startValue = ($ratio > 0 && $ratio < 1) ? $ratio : 1;

		$basketId = str_replace("QUANTITY_SELECT_", "", $id);

		$disabled = ""; // if not available for buying
		if ($maxQuantity == 0)
		{
			$disabled = "disabled";
			$maxQuantity = 1;
		}

		$res = "<div class=\"some-class\">";
		$res .= "<select id=\"".$id."\" name=\"".$name."\" onchange=\"updateQuantity('".$id."',".$basketId.",".$ratio.",".$useFloatQuantity.");\" ".$disabled.">";

		for ($i = $startValue; $i <= $maxQuantity; $i = $i + $ratio)
		{
			$selected = ($i == $curVal) ? "selected" : "";

			$res .= "<option value=".$i." ".$selected.">".$i."</option>";
		}

		$res .= "</select>".$measureText."</div>";

		return $res;
	}
}

?>
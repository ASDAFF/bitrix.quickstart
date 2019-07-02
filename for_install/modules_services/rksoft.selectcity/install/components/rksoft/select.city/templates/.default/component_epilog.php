<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
__IncludeLang(dirname(__FILE__)."/lang/".LANGUAGE_ID."/".basename(__FILE__));

if(count($_SESSION) == 0) session_start();

if($_SERVER["REQUEST_METHOD"] == "POST" and $_POST["city_id"])
{
	if($arParams["USE_LOC_GROUPS"] != "Y") $_SESSION["city_id"] = intval($_POST["city_id"]);
	else
	{
		$arData = explode("_", $_POST["city_id"]);
		$_SESSION["group_id"] = intval($arData["0"]);
		$_SESSION["city_id"] = intval($arData["1"]);
	}
}
?>

<form action="<?=$_SERVER["PHP_SELF"];?>" method="post">
	<select name="city_id">

<?
$res = "";
foreach($arResult["ITEMS"] as $item)
{
	if($arParams["USE_LOC_GROUPS"] != "Y")
	{
		if(!isset($_SESSION["city_id"]))
		{
			if($arParams["ID_LOC_DEFAULT"] > 0 and $arParams["ID_LOC_DEFAULT"] == $item["CITY_ID"]) $res .= "<option value=\"".$item["CITY_ID"]."\" selected=\"selected\">".$item["CITY_NAME"]."</option>";
			else $res .= "<option value=\"".$item["CITY_ID"]."\">".$item["CITY_NAME"]."</option>";
		}
		else
		{
			if($_SESSION["city_id"] == $item["CITY_ID"]) $res .= "<option value=\"".$item["CITY_ID"]."\" selected=\"selected\">".$item["CITY_NAME"]."</option>";
			else $res .= "<option value=\"".$item["CITY_ID"]."\">".$item["CITY_NAME"]."</option>";
		}
	}
	else
	{
		$arParams["ID_LOC_GROUP_DEFAULT"] = 1;
		if(!isset($_SESSION["city_id"]))
		{
			if($arParams["ID_LOC_DEFAULT"] > 0 and $arParams["ID_LOC_DEFAULT"] == $item["CITY_ID"]) $res .= "<option value=\"".$arParams["ID_LOC_GROUP_DEFAULT"]."_".$item["CITY_ID"]."\" selected=\"selected\">".$item["CITY_NAME"]."</option>";
			else $res .= "<option value=\"".$arParams["ID_LOC_GROUP_DEFAULT"]."_".$item["CITY_ID"]."\">".$item["CITY_NAME"]."</option>";
		}
		else
		{
			if($_SESSION["city_id"] == $item["CITY_ID"]) $res .= "<option value=\"".$arParams["ID_LOC_GROUP_DEFAULT"]."_".$item["CITY_ID"]."\" selected=\"selected\">".$item["CITY_NAME"]."</option>";
			else $res .= "<option value=\"".$arParams["ID_LOC_GROUP_DEFAULT"]."_".$item["CITY_ID"]."\">".$item["CITY_NAME"]."</option>";
		}
	}
}
if(count($arResult["ITEMS"]) == 0) $res .= "<option value=\"0\">empty</option>";
echo $res;
?>
	</select>
	<input type="submit" value="Ok" />
</form>
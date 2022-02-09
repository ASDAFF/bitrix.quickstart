<?
class CIBlockPropertyResult extends CDBResult
{
	function Fetch()
	{
		$res = parent::Fetch();
		if($res && $res["USER_TYPE"]!="")
		{
			$arUserType = CIBlockProperty::GetUserType($res["USER_TYPE"]);
			if(array_key_exists("ConvertFromDB", $arUserType))
			{
				if(array_key_exists("VALUE", $res))
				{
					$value = array("VALUE"=>$res["VALUE"],"DESCRIPTION"=>"");
					$value = call_user_func_array($arUserType["ConvertFromDB"],array($res,$value));
					$res["VALUE"] = $value["VALUE"];
				}

				if(array_key_exists("DEFAULT_VALUE", $res))
				{
					$value = array("VALUE"=>$res["DEFAULT_VALUE"],"DESCRIPTION"=>"");
					$value = call_user_func_array($arUserType["ConvertFromDB"],array($res,$value));
					$res["DEFAULT_VALUE"] = $value["VALUE"];
				}
			}
			if(strlen($res["USER_TYPE_SETTINGS"]))
				$res["USER_TYPE_SETTINGS"] = unserialize($res["USER_TYPE_SETTINGS"]);
		}
		return $res;
	}
}
?>
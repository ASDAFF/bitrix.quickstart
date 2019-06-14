<?

define("STOP_STATISTICS", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if(CModule::IncludeModule("iblock"))
{
	if ($GLOBALS["USER"]->IsAuthorized())
	{
		if (!Function_Exists("__UnEscapeTmp"))
		{
			function __UnEscapeTmp(&$item, $key)
			{
				if (Is_Array($item))
					Array_Walk($item, '__UnEscapeTmp');
				else
				{
                    if(!defined("BX_UTF"))
                            $item = $GLOBALS["APPLICATION"]->ConvertCharset($item, "UTF-8", "WINDOWS-1251");

					if (StrPos($item, "%u") !== false)
					{
						$item = $GLOBALS["APPLICATION"]->UnJSEscape($item);
                    }
				}
			}
		}

		Array_Walk($_REQUEST, '__UnEscapeTmp');

		$arParams = is_array($_REQUEST["params"]) ? $_REQUEST["params"] : array();

		$arParams['NAME_TEMPLATE'] = !empty($arParams["nt"]) ? $arParams["nt"] : DefaTools_IBProp_ElemCompliter::DEFAULT_NAME_FORMAT;
            if(is_array($arParams["IBLOCK_ID"]))
                    foreach($arParams["IBLOCK_ID"] as $k=>$v)
                            $arParams["IBLOCK_ID"][$k] = intval($v);
            else
                    $arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);

            $arParams["TOP_COUNT"] = $arParams['tc'] > 0 ? $arParams["tc"] : 20;
            $arResult = array();
		
            if(!empty($_REQUEST["search"]))
            {
                    $rsElement = CIBlockElement::GetList(
                            array(
                                    "NAME" => "ASC"
                            ),
                            array(
                                    "CHECK_PERMISSIONS" => "Y",
                                    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                                    "NAME" => $_REQUEST["search"].'%'
                            ),
                            false,
                            array(
                                    "nTopCount" => $arParams["TOP_COUNT"]
                            ),
                            array(
                                    "ID",
                                    "NAME",
                                    "IBLOCK_ID"
                            )
                    );
                    while($arElement = $rsElement->Fetch())
                    {
                            $arElement["SHORT_NAME"] = $arElement["NAME"];
                            $arElement["NAME"] = CComponentEngine::MakePathFromTemplate($arParams['NAME_TEMPLATE'], $arElement);
                            $arResult[] = $arElement;
                    }
            }
		?><?=CUtil::PhpToJSObject($arResult)?><?
		require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_after.php");
		die();
	}
}
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if (CModule::IncludeModule("sale") && CModule::IncludeModule("catalog"))
{
    if (($_REQUEST['action'] == "additem" || $_REQUEST['action'] == "buy") && IntVal($_REQUEST['id'])>0 && IntVal($_REQUEST['count']>0))
    {
           Add2BasketByProductID(intval($_REQUEST['id']),intval($_REQUEST['count']),array(),array());
    $GLOBALS["APPLICATION"]->RestartBuffer();
    $APPLICATION->IncludeComponent("lenal:basket.line", "line_ajax", array(
                                    "PATH_TO_BASKET" => SITE_DIR . "personal/cart/",
                                    "PATH_TO_PERSONAL" => SITE_DIR . "personal/",
                                    "SHOW_PERSONAL_LINK" => "N"
                                        ), false, array()
                                );
   }
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>
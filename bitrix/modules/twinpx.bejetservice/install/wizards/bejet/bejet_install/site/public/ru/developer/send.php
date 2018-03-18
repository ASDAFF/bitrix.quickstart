<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("iblock");

$el = new CIBlockElement;

$arLoadProductArray = Array(
  "IBLOCK_ID" => #ORDERS_IBLOCK_ID#,
  "NAME"           => $_REQUEST['email'],
  "ACTIVE"         => "Y",
  "PREVIEW_TEXT"   => $_REQUEST['text'],
  );

if($PRODUCT_ID = $el->Add($arLoadProductArray))
{
	echo '
		{
			"message": "Ваше письмо отправлено успешно."
		}
	';
}
		
		$arMailFields = array(
		"USERMAIL"          => $_REQUEST['email'],
		"TEXT" => $_REQUEST['text'],
		);
		CEvent::Send(
			"NEW_ORDER_ADMIN", SITE_ID, $arMailFields, "N", #NEW_ORDER_ADMIN#
		);
		CEvent::Send(
			"NEW_ORDER_USER", SITE_ID, $arMailFields, "N", #NEW_ORDER_USER#
		);
//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>
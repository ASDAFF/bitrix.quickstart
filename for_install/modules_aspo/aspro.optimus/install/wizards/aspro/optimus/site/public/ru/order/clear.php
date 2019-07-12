<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
if (CModule::IncludeModule("sale"))
{
	$dbBasketItems = CSaleBasket::GetList(
            array(
               "NAME" => "ASC",
               "ID" => "ASC"
            ),
            array(
              "LID" => SITE_ID,
              "ORDER_ID" => NULL,
            )
         );
         while ($arItems = $dbBasketItems->Fetch()){           
			CSaleBasket::Delete($arItems['ID']);
         }
}
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>